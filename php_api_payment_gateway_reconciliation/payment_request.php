<?php
//////////////////////////////////////////////////////////////////////////////
$config["debugging"] = false;
//////////////////////////////////////////////////////////////////////////////
// $config["debugging"] = true;
//////////////////////////////////////////////////////////////////////////////

$input_syntax = "Usage: <this>.php < SANDBOX / PRODUCTION >\n";

$config["environment"] = trim($argv[1]);
if ($config["environment"] == "") {
	echo "Error: Parameter missing #1\n\n";
	echo $input_syntax;
	die();
}

/*
FLOWS:

	Authorized -> Submitted For Settlement
    Authorized -> Submitted For Settlement (for partial payment)
    Authorized -> Expired -> Cloned -> Submitted For Settlement / Submitted For Settlement (for partial payment)
    Authorized -> Expired -> Cannot Clone because payment method in vault 
        -> Create new transaction (Need customer ID and payment method token) -> Submitted For Settlement
*/

//////////////////////////////////////////////////////////////////////////////
// Begin of config
//////////////////////////////////////////////////////////////////////////////
$config["ftp_address"] = "ftp.mydomain.com";
$config["ftp_username"] = "username";
$config["ftp_password"] = "*****";
$config["ftp_file"] = "trans.csv";
$config["ftp_file_datetime"] = "";

$config["ftp_command_fileexists"] = "FILEEXISTS";
$config["ftp_command_get"] = "GET";
$config["ftp_command_put"] = "PUT";
$config["ftp_command_rename"] = "RENAME";

$config["mypath"] = "/home/eyi/cron/gateway/";
$config["mypath_processed"] = "/home/eyi/cron/gateway/processed/";
$config["file_processed_name"] = "trans_processed.csv";
$config["file_log_name"] = "payment_request.log";

$config["email_subject"] = "gateway Payment Settlement";
$config["admin_email"] = "eyi@mydomain.com";
$config["accounting_email"] = "AccountsReceivable@mydomain.com";

$config["csv_line_struct"] = array(
    "some Order Number",
    "some AB Number",
    "Web Confirmation Number",
    "Token Number",
    "Amount",
    "Authorization Number"
);

if ($config["environment"] == "SANDBOX") {
    $config["gateway_environment"] = "sandbox";
    $config["gateway_merchant_id"] = "blablablabla";
    $config["gateway_public_key"] = "blablablabla";
    $config["gateway_private_key"] = "blablablabla";
} elseif ($config["environment"] == "PRODUCTION") {
    $config["gateway_environment"] = "production";
    $config["gateway_merchant_id"] = "blablablabla";
    $config["gateway_public_key"] = "blablablabla";
    $config["gateway_private_key"] = "blablablabla";
}

$config["gateway_transaction_expired"] = "authorization_expired";
$config["transaction_id_min_length"] = 3;
//////////////////////////////////////////////////////////////////////////////
// End of config
//////////////////////////////////////////////////////////////////////////////

$errored_out = false;
$file_found = false;

if (!$config["debugging"]) {
    $file_found = payment_request::ftp_file($config["ftp_command_fileexists"], $config["ftp_file"]);
    if (!$file_found) {
        exit;
    }
} else {
    echo "***** D E B U G G I N G   M O D E *****\n\n";
}

date_default_timezone_set("UTC"); 
$datetime = date("Ymd_His") . "UTC_";

$file_log_datetime = $datetime . $config["file_log_name"];
$logfile = $config["mypath"] . $file_log_datetime;
$fh_log = fopen($logfile, 'w');	
if (!$fh_log) {
    payment_request::alert_error("Error: Cannot open " . $logfile . " lof file");    
    exit;
}

$file_processed_datetime = $datetime . $config["file_processed_name"];
$processedfile = $config["mypath"] . $file_processed_datetime;
$fh_processedfile = fopen($processedfile, 'w');	
if (!$fh_processedfile) {
    payment_request::alert_error("Error: Cannot open " . $processedfile . " file");    
    exit;
}

if (!$config["debugging"]) {
    if (!payment_request::ftp_file($config["ftp_command_get"], $config["ftp_file"])) {
        $errored_out = true;
    }
    // Rename the remote file as soon as downloaded to prevent processing the same file again in case it craps out.
    if (!payment_request::ftp_file($config["ftp_command_rename"], $config["ftp_file"])) {
        $errored_out = true;
    }    
}

if (file_exists($config["mypath"] . $config["ftp_file"])) {
    $config["ftp_file_datetime"] = $config["ftp_file"];
    if (!$config["debugging"]) {
        rename($config["mypath"] . $config["ftp_file"], $config["mypath"] . $datetime . $config["ftp_file"]);
        $config["ftp_file_datetime"] = $datetime . $config["ftp_file_datetime"];
        fwrite($fh_log, "Renamed the source file from " . $config["ftp_file"] . " to " . $datetime . $config["ftp_file"] . "\n");
    }
    
    if (!payment_request::process()) {
        $errored_out = true;
    }
    
    if (!$config["debugging"]) {
        if ($errored_out != true) {
            if (!payment_request::ftp_file($config["ftp_command_put"], $file_processed_datetime)) {
                $errored_out = true;
            }
            // if (!payment_request::ftp_file($config["ftp_command_rename"], $config["ftp_file"])) {
                // $errored_out = true;
            // }
            $email_body = "Mode: " . $config["environment"] . "\n\nTransactions processed for payment settlement. See " . $file_processed_datetime;
            payment_request::send_mail($config["email_subject"], $email_body, $config["admin_email"], $config["admin_email"], $processedfile);   
            payment_request::send_mail($config["email_subject"], $email_body, $config["accounting_email"], $config["admin_email"], $processedfile);   
            unset($email_body);
        }
    }
} else {
    // fwrite($fh_log, "Error: Not processing because file does not exist: " . $config["mypath"] . $config["ftp_file"] . "\n");
    // $errored_out = true;
    echo "No file to process: " . $config["mypath"] . $config["ftp_file"] . "\n";
}

if ($errored_out == true) {
    payment_request::alert_error("Error: There was an error.  See " . $config["mypath_processed"] . $file_log_datetime . " for details.");  
} else {
    if (!$config["debugging"]) {
        rename($config["mypath"] . $config["ftp_file_datetime"], $config["mypath_processed"] . $config["ftp_file_datetime"]);
        rename($config["mypath"] . $file_log_datetime, $config["mypath_processed"] . $file_log_datetime);
        rename($config["mypath"] . $file_processed_datetime, $config["mypath_processed"] . $file_processed_datetime);
    }
    fwrite($fh_log, "Moved the source file, " . $config["ftp_file_datetime"] . " to: " . $config["mypath_processed"] . $config["ftp_file_datetime"] . "\n");
    fwrite($fh_log, "Moved the log file, " . $file_log_datetime . " to: " . $config["mypath_processed"] . $file_log_datetime . "\n");
    fwrite($fh_log, "Moved the processed file, " . $file_processed_datetime . " to: " . $config["mypath_processed"] . $file_processed_datetime . "\n");
}

fclose($fh_processedfile);
fclose($fh_log);

exit;


//////////////////////////////////////////////////////////////////////////////
class payment_request {
//////////////////////////////////////////////////////////////////////////////
	
	public $log;
	
    static function ftp_file($mode = "GET", $file) {
        global $config;
        global $fh_log;
        
        try {
            $conn_id = ftp_connect($config["ftp_address"]); 
            $login_result = ftp_login($conn_id, $config["ftp_username"], $config["ftp_password"]);
            ftp_pasv($conn_id, true);

            if ($mode == $config["ftp_command_fileexists"]) {
                $contents_on_server = ftp_nlist($conn_id, ".");
                if (in_array($file, $contents_on_server)) {
                    return true;
                } else {
                    return false;
                };
            }            
            
            if ($mode == $config["ftp_command_get"]) {
                $local_file = $config["mypath"] . $file;
                fwrite($fh_log, "Downloading " . $file . " to " . $local_file . "\n");
                if (ftp_get($conn_id, $local_file, $file, FTP_ASCII)) {
                    fwrite($fh_log, "Success: FTP downloaded " . $file . "\n");
                    return true;
                } else {
                    fwrite($fh_log, "Error: FTP downloading " . $file . "\n");
                    return false;
                }
            }

            if ($mode == $config["ftp_command_put"]) {
                $local_file = $config["mypath"] . $file;
                fwrite($fh_log, "Uploading " . $local_file . " to " . $file . "\n");
                if (ftp_put($conn_id, $file, $local_file, FTP_ASCII)) {
                    fwrite($fh_log, "Success: FTP uploaded " . $file . "\n");
                    return true;
                } else {
                    fwrite($fh_log, "Error: FTP uploading " . $file . "\n");
                    return false;
                }
            }    

            if ($mode == $config["ftp_command_rename"]) {
                $rename_to_file = $file . "_processed";
                fwrite($fh_log, "Renaming the remote file,  " . $file . " to " . $rename_to_file . "\n");
                if (ftp_rename($conn_id, $file, $rename_to_file)) {
                    fwrite($fh_log, "Success: FTP renamed " . $file . "\n");
                    return true;
                } else {
                    fwrite($fh_log, "Error: FTP rename " . $file . "\n");
                    return false;
                }
            } 
            
        } catch (Exception $e) {
            fwrite($fh_log, "Error in ftp_file(): " . $e->getMessage() . "\n");
            return false;
        }
        
        return true;
    }
    
    static function process() {
        global $config;
        global $fh_log;
        global $fh_processedfile;
        
        $line_array = array();
        $skip_line = false;
        $skip_line_error_msg = "";
       
        $input_file = $config["mypath"] . $config["ftp_file_datetime"];
        if (!file_exists($input_file)) {
            fwrite($fh_log, "Error in process(): " . $input_file . " not found\n");
            return false;
        }
        
        if (!$file_contents = file($input_file, FILE_SKIP_EMPTY_LINES)) {
            fwrite($fh_log, "Error in process(): Unable to get the contents of " . $input_file . "\n");
            return false;            
        }
        $csv = array_map('str_getcsv', $file_contents);
        if (!$csv) {
            fwrite($fh_log, "Error in process(): " . $input_file . " cannot be opened\n");
            return false;
        }
        
        for ($i = 0; $i < count($csv); $i++) {
            $line_number = $i + 1;
            $skip_line = false;
            $skip_line_error_msg = "";
            
            $line_array = $csv[$i];
            fwrite($fh_log, "(LINE " . $line_number . ") " . print_r($line_array, true) . "\n");

            if (empty($line_array) || count($line_array) <= 1) {
                fwrite($fh_log, "Skipping an empty line.\n");
                continue;
            }
            
            for ($j = 0; $j < count($config["csv_line_struct"]); $j++) {
                if (trim($line_array[$j]) == "") {
                    fwrite($fh_log, "(LINE " . $line_number . ") " . "Warning: `" . $config["csv_line_struct"][$j] . "` has no value.\n");
                }
            }

            $some_order_number = trim($line_array[0]);
            $web_order_number = trim($line_array[2]);
            $transaction_id = trim($line_array[3]);
            $amount = trim($line_array[4]);
            
            // Per Jeff, to handle non-webstore orders
            if ($web_order_number == "") {
                $web_order_number = $some_order_number;
            }
            
            // Input Error Checking
            if (strlen($transaction_id) < $config["transaction_id_min_length"]) {
                $skip_line_error_msg = "transaction_id length less than " . $config["transaction_id_min_length"] . ".";
                $skip_line = true;
            }
            if ($amount == "" || $amount == 0) {
                $skip_line_error_msg = "amount has no value.";
                $skip_line = true;
            }
            if (!is_numeric($amount)) {
                $skip_line_error_msg = "amount is not numeric.";
                $skip_line = true;
            }

            if ($skip_line) {
                $line_array[] = "error, " . $skip_line_error_msg;
                fwrite($fh_log, "(LINE " . $line_number . ") " . "Error: Skipping the line. " . $skip_line_error_msg . "\n");                
            } else {
                $payment_params = array(
                    "web_order_number" => $web_order_number,
                    "transaction_id" => $transaction_id,
                    "amount" => $amount
                );
                $api_results = payment_request::gateway_api($payment_params); 
                $line_array[] = implode(",", $api_results);       
            }
            
            fputcsv($fh_processedfile, $line_array);
        }
        
        return true;
    }
    
    static function gateway_api($payment_params) {
        global $config;
        global $fh_log;

        $output = array();
        
        $web_order_number = trim($payment_params["web_order_number"]);
        $transaction_id = strtolower(trim($payment_params["transaction_id"])); // gateway find() craps out if any uppercase letter.
        $amount = trim($payment_params["amount"]);
        
        if ($transaction_id == "") {
            fwrite($fh_log, "Error: missing transaction_id\n");
            $output = array(
                "result" => "error",
                "" => "missing transaction_id"
            );
            return $output;
        }
        if ($amount == "") {
            fwrite($fh_log, "Error: missing amount\n");
            $output = array(
                "result" => "error",
                "" => "missing amount"
            );
            return $output;
        }
        
        require_once 'sdk/gateway-php-3.15.0/lib/gateway.php';  
        try {
            gateway_Configuration::environment($config["gateway_environment"]);
            gateway_Configuration::merchantId($config["gateway_merchant_id"]);
            gateway_Configuration::publicKey($config["gateway_public_key"]);
            gateway_Configuration::privateKey($config["gateway_private_key"]);
        } catch (Exception $e) {
            fwrite($fh_log, "Fatal Error: " . $e->getMessage() . "\n");
            $output = array(
                "result" => "error",
                "" => print_r($e->getMessage(), true)
            );
            return $output;            
        }
        
        // See if transaction_id exists first.
        try {
            $result = gateway_Transaction::find($transaction_id);
        } catch (gateway_Exception_NotFound $e) {
            fwrite($fh_log, "Error: " . $e->getMessage() . "\n");
            $output = array(
                "result" => "error",
                "" => print_r($e->getMessage(), true)
            );
            return $output;            
        }
        
        if ($result) {
            $status = $result->status;
            // Authorization Expired
            if ($status == $config["gateway_transaction_expired"]) { 
                fwrite($fh_log, "Status: " . $result->status . ". Making a clone...\n");

                try {
                    $result = gateway_Transaction::cloneTransaction($transaction_id, [
                        'amount' => $amount,
                        'options' => [
                            'submitForSettlement' => true
                        ]
                    ]);
                } catch (Exception $e) {
                    fwrite($fh_log, "Error: " . $e->getMessage() . "\n");
                    $output = array(
                        "result" => "error",
                        "" => print_r($e->getMessage(), true)
                    );
                    return $output;            
                }
            } else {
                try {
                    $result = gateway_Transaction::submitForSettlement($transaction_id, $amount);
                } catch (Exception $e) {
                    fwrite($fh_log, "Error: " . $e->getMessage() . "\n");
                    $output = array(
                        "result" => "error",
                        "" => print_r($e->getMessage(), true)
                    );
                    return $output;            
                }
            }
        }

        // fwrite($fh_log, "gateway_Transaction: " . print_r($result->transaction, true) . "\n");
        fwrite($fh_log, "result->success: " . print_r($result->success, true) . "\n");
        
        if ($result->success) {
            $output = array(
                "result" => "success",
                "transaction_id" => $result->transaction->id,
                "amount" => $amount,
            );
            fwrite($fh_log, print_r($output, true) . "\n");
        } else {
            if ($result->transaction) {
                $result_transaction_id = $result->transaction->id;
            } else {
                $result_transaction_id = "";
            }
            
            $output = array(
                "result" => "error",
                "transaction_id" => $result_transaction_id,
                "amount" => $amount,
                "status" => $status,
                "message" => $result->message,
                // "code" => $result->transaction->processorResponseCode,
                // "text" => $result->transaction->processorResponseText,
            );
            fwrite($fh_log, "result->transaction: " . print_r($result->transaction, true) . "\n");
            fwrite($fh_log, "result->errors: " . print_r($result->errors, true) . "\n");
        } 
        
        // print_r($output);
        return $output;
    }
    
	static function alert_error($msg = "") {
		global $config;
		
		if ($msg == "") {
			$msg = $config["error"];
		}
		
		payment_request::send_mail($config["email_subject"] . " Error", $msg, $config["admin_email"], $config["admin_email"]);
		
		return true;
	}
	
	static function send_mail($subject = null, $body = null, $to = null, $from = null, $attachment = "") {
		global $config;

		if ($to == null) {
			$param["to"] = $config["admin_email"];
		} else {
			$param["to"] = $to;
		}
		if ($from == null) {
			$param["from"] = "root@instance-web";
		} else {
			$param["from"] = $from;
		}
        
		if ($subject == null || $subject == "") {
			$param["subject"] = $config["email_subject"];
		} else {
			$param["subject"] = $subject;
		}
		if ($body == null) {
			return false;
		} else {
			$param["body"] = $body;
		}        
        
        $uid = md5(uniqid(time()));

        $header = "";
        $nmessage = "";
        
        // header
        $header = "From: " . $param["from"] . "\r\n";
        $header .= "Reply-To: " . $param["from"] . "\r\n";
        $header .= "MIME-Version: 1.0\r\n";

        if ($attachment != "") {
            // message & attachment        
            $header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"\r\n\r\n";

            $file_content = file_get_contents($attachment);
            $file_content = chunk_split(base64_encode($file_content));
            $filename = basename($attachment);
            
            $nmessage = "--" . $uid . "\r\n";
            $nmessage .= "Content-type:text/plain; charset=utf-8\r\n";
            $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $nmessage .= $param["body"] . "\r\n\r\n";
            $nmessage .= "--" . $uid."\r\n";
            $nmessage .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n";
            $nmessage .= "Content-Transfer-Encoding: base64\r\n";
            $nmessage .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
            $nmessage .= $file_content."\r\n\r\n";
            $nmessage .= "--".$uid."--";
        } else {
            $header .= "Content-type: text/html; charset=utf-8\r\n";            
            $nmessage .= $param["body"] . "\r\n\r\n";
        }
        
		mail($param["to"], $param["subject"], $nmessage, $header) or die("Error in send_mail()" . print_r($param, true));

		// $email = new PHPMailer();
		// $email->From = $param["from"];
		// $email->FromName = 'Your Name';
		// $email->Subject = $param["subject"];
		// $email->Body = $body;
		// $email->AddAddress($param["to"]);
		// $file_to_attach = $processedfile;
		// $email->AddAttachment( $file_to_attach , $file_processed_datetime );
		// $email->Send();

		
		return true;
	}	    
    
} // End of class

?>
