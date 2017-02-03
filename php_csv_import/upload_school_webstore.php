<?php
// $trans_action = "commit";
$trans_action = "rollback";	// TESTING

$config["dbserver"] = "*****";
$config["dbuser"]   = "*****";
$config["dbpass"]   = "*****";

$config["header_row"]  = 1;
$config["colName"]     = "O";
$config["colAddress"]  = "P";
$config["colCity"]     = "Q";
$config["colState"]    = "R";
$config["colZip"]      = "S";
$config["colPlus4"]    = "T";
$config["colCounty"]   = "DQ";
$config["colPID"]      = "B";
$config["colInstType"] = "A"; // FILETYPE 05=Districts
$config["LogFile"] 		= "upload_school_webstore_log.txt";
$config["ErrorLogFile"]	= "upload_school_webstore_errorlog.txt";
/*------------------------------------------------------------------------------------------------------*/

$input_syntax = "Usage: <this>.php <input file> <DB - test/live>\n";

$filename = trim($argv[1]);
if ($filename == "") {
	echo "Error: Parameter missing #1\n\n";
	echo $input_syntax;
	die();
}
$whichdb = trim($argv[2]);
if ($whichdb == "") {
	echo "Error: Parameter missing #3\n\n";
	echo $input_syntax;
	die();
}
switch ($whichdb) {
    case "test":
		$config["dbname"] = "mytestdatabase";
        break;
    case "live":
		$config["dbname"] = "mylivedatabase";
        break;
}


// Main
ConnectDB();
ProcessFile($filename);
CheckDupPID();
DisconnectDB();
exit;



function letters_to_num($letters) {
	$num = 0;
	$arr = array_reverse(mystr_split($letters));
	for ($i = 0; $i < count($arr); $i++) {
		$num += (ord(strtolower($arr[$i])) - 96) * (pow(26,$i));
	}
	
	$num--; // THIS IS NECESSARY FOR READING EXCEL FILE!!!!
	
	return $num;
}

// str_split() is NOT available in PHP 4.
function mystr_split($string, $split_length = 1) {
	$array = explode("\r\n", chunk_split($string, $split_length));
	array_pop($array);
	return $array;
}

function ConnectDB() {
	global $mysqli;
	global $config;
	
	foreach ($config as $key => $value) {
		$$key = $value;
	}
	
	$mysqli = new mysqli($dbserver, $dbuser, $dbpass, $dbname);
	if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
	}
	return;
}

function ProcessFile($filename) {
	global $mysqli;
	global $config;
	
	foreach ($config as $key => $value) {
		$$key = $value;
	}
	
	$CountInserted = 0;
	$CountInsertFailed = 0;
	$CountUpdated = 0;
	$CountUpdateFailed = 0;	
	$CountSkipped = 0;
	
	$fh_log = fopen($LogFile, 'w') or die ("Can't open to write: " . $LogFile . "\n");	
	$fh_errorlog = fopen($ErrorLogFile, 'w') or die ("Can't open to write: " . $ErrorLogFile . "\n");	
		
	$handle = @fopen($filename, "r") or die ("Error: " . $filename . " doesn't exist!\n\n");
	if ($handle) {
		$i = 1;
		while (!feof($handle))
		{
			$line = "";
			$parts = array();
			$school_array = array();

			echo $i . " ";
			$line = fgets($handle);
			// echo $line . "\n\n";
			
			if ($i == $header_row) {
				$CountSkipped++;
				$i++;
				continue;
			}
			
			$parts = explode(",", $line);

			// Excel puts annoying double quotes in values containing commas.  I don't know how to get rid of them easily so
			for ($j = 0; $j < count($parts); $j++) {
				$mystring = trim($parts[$j]);
				if (substr($mystring, 0, 1) == '"') {
					$mystring = substr($mystring, 1);
				}
				if (substr($mystring, strlen($mystring) - 1, 1) == '"') {
					$mystring = substr($mystring, 0, strlen($mystring) - 1);
				}
				$parts[$j] = $mystring;
			}			
			
			$school_array["s_name"]     = trim($parts[letters_to_num($colName)]);
			$school_array["s_address"]  = trim($parts[letters_to_num($colAddress)]);
			$school_array["s_city"]     = trim($parts[letters_to_num($colCity)]);
			$school_array["s_state"]    = trim($parts[letters_to_num($colState)]);
			$school_array["s_zip"]      = trim($parts[letters_to_num($colZip)]);
			$school_array["s_plus4"]    = trim($parts[letters_to_num($colPlus4)]);
			$school_array["s_county"]   = trim($parts[letters_to_num($colCounty)]);
			$school_array["s_pid"]      = trim($parts[letters_to_num($colPID)]);
			$school_array["s_insttype"] = trim($parts[letters_to_num($colInstType)]);
			$school_array["s_country"]  = "US";
						
			$line_print = $i . ": ".
				"s_name(" . $school_array["s_name"] . ") ".
				"s_address(" . $school_array["s_address"] . ") ".
				"s_city(" . $school_array["s_city"] . ") ".
				"s_state(" . $school_array["s_state"] . ") ".
				"s_zip(" . $school_array["s_zip"] . ") ".
				"s_plus4(" . $school_array["s_plus4"] . ") ".
				"s_county(" . $school_array["s_county"] . ") ".
				"s_pid(" . $school_array["s_pid"] . ") ".
				"s_insttype(" . $school_array["s_insttype"] . ") ".
				"s_country(" . "(US)\n";
	
			if ($school_array["s_pid"] == "" || $school_array["s_pid"] == "PID") {
				fwrite($fh_errorlog, $line_print); 
				fwrite($fh_errorlog, " * PID is null\n");
				$CountSkipped++;
				$i++;
				continue;
			} elseif (!is_numeric($school_array["s_pid"])) {
				fwrite($fh_errorlog, $line_print); 
				fwrite($fh_errorlog, " * PID is not numeric\n");
				$CountSkipped++;
				$i++;
				continue;
			}

			$school_array["s_name"]    = $mysqli->real_escape_string($school_array["s_name"]);
			$school_array["s_address"] = $mysqli->real_escape_string($school_array["s_address"]);
			$school_array["s_city"]    = $mysqli->real_escape_string($school_array["s_city"]);
			$school_array["s_state"]   = $mysqli->real_escape_string($school_array["s_state"]);
			$school_array["s_zip"]     = $mysqli->real_escape_string($school_array["s_zip"]);
			$school_array["s_plus4"]   = $mysqli->real_escape_string($school_array["s_plus4"]);
			$school_array["s_county"]  = $mysqli->real_escape_string($school_array["s_county"]);

			$query = "select ".
					"s_name, ".
					"s_address, ".
					"s_city, ".
					"s_state, ".
					"s_zip, ".
					"s_plus4, ".
					"s_county, ".
					"s_country, ".
					"s_pid, ".
					"s_insttype ".	
					"from schools where s_pid = '" . $school_array["s_pid"] . "' ";
			// echo $query . "\n";
			if ($result = $mysqli->query($query)) {
				// echo "num_rows( " . $result->num_rows . ")\n";
				if ($result->num_rows > 0) {
					$error = 1;
					while ($row = $result->fetch_assoc()) {
						$s_name = $row["s_name"];
						$s_address = $row["s_address"];
						$s_city = $row["s_city"];
						$s_state = $row["s_state"];
						$s_zip = $row["s_zip"];
						$s_plus4 = $row["s_plus4"];
						$s_county = $row["s_county"];
						$s_country = $row["s_country"];
						$s_pid = $row["s_pid"];
						$s_insttype = $row["s_insttype"];

						$update = "update schools set ".
									"s_name = '" . $school_array["s_name"] . "', ".
									"s_address = '" . $school_array["s_address"] . "', ".
									"s_city = '" . $school_array["s_city"] . "', ".
									"s_state = '" . $school_array["s_state"] . "', ".
									"s_zip = '" . $school_array["s_zip"] . "', ".
									"s_plus4 = '" . $school_array["s_plus4"] . "', ".
									"s_county = '" . $school_array["s_county"] . "', ".
									"s_country = '" . $school_array["s_country"] . "', ".
									"s_insttype = '" . $school_array["s_insttype"] . "' ".
									"where s_pid = " . $school_array["s_pid"];
						// echo $update . "\n";
						fwrite($fh_log, $line_print); 
						fwrite($fh_log, " * OLD:" . $s_name . "\t" . $s_address . "\t" . $s_city . "\t" . $s_state . "\t" . $s_zip . "\t" . $s_plus4 . "\t" . $s_county . "\t" . $s_country . "\t" . $s_pid . "\t" . $s_insttype . "\n"); 
						fwrite($fh_log, " * NEW:" . $school_array["s_name"] . "\t" . $school_array["s_address"] . "\t" . $school_array["s_city"] . "\t" . $school_array["s_state"] . "\t" . $school_array["s_zip"] . "\t" . $school_array["s_plus4"] . "\t" . $school_array["s_county"] . "\t" . $school_array["s_country"] . "\t" . $school_array["s_pid"] . "\t" . $school_array["s_insttype"] . "\n\n"); 
						
						// echo "UPDATE: " . $update . "\n";
						if ($mysqli->query($update) === FALSE) {
							fwrite($fh_errorlog, $line_print); 
							fwrite($fh_errorlog, " * Error updating: " . $mysqli->error . "\n");
							$CountUpdateFailed++;
						} else {
							$CountUpdated++;
						}
					}
				} else {
					$insert = "insert into schools (".
								"s_name, ".
								"s_address, ".
								"s_city, ".
								"s_state, ".
								"s_zip, ".
								"s_plus4, ".
								"s_county, ".
								"s_country, ".
								"s_pid, ".
								"s_insttype) ".
								"values (".
								"'" . $school_array["s_name"] . "', ".
								"'" . $school_array["s_address"] . "', ".
								"'" . $school_array["s_city"] . "', ".
								"'" . $school_array["s_state"] . "', ".
								"'" . $school_array["s_zip"] . "', ".
								"'" . $school_array["s_plus4"] . "', ".
								"'" . $school_array["s_county"] . "', ".
								"'" . $school_array["s_country"] . "', ".
								"'" . $school_array["s_pid"] . "', ".
								"'" . $school_array["s_insttype"] . "') ";
					// echo $insert . "\n";
					if ($mysqli->query($insert) === FALSE) {
						fwrite($fh_errorlog, $line_print); 
						fwrite($fh_errorlog, " * Error updating: " . $mysqli->error . "\n");
						$CountInsertFailed++;
					} else {
						fwrite($fh_log, $line_print); 
						fwrite($fh_log, " * INSERTED" . "\n\n");
						$CountInserted++;
					}					
				}
			}
			$result->free();

			// Necessary to initialize for handling a big file
			unset($line);
			unset($parts);
			unset($school_array);
			
			$i++;
		}
	}	
	fclose($handle);
	fclose($fh_log);
	fclose($fh_errorlog);

	$i--;
	echo "\n\n";
	echo "Lines read in: " . $i . "\n";
	echo "Total Inserted: " . $CountInserted . "\n";
	echo "Total Insert Failed: " . $CountInsertFailed . "\n";
	echo "Total Updated: " . $CountUpdated . "\n";
	echo "Total Update Failed: " . $CountUpdateFailed . "\n";
	echo "Total Skipped: " . $CountSkipped . "\n";
	return;
}

function CheckDupPID() {
	global $mysqli;
	
	$error = 0;

	$query = "select s_pid, count(*) as dup_count from schools group by s_pid having count(*) > 1 ";
	// echo $query . "\n";
	if ($result = $mysqli->query($query)) {
		echo "Duplicate PIDs: " . $result->num_rows . "\n\n";
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$s_pid = $row["s_pid"];
				$dup_count = $row["dup_count"];

				echo "PID( " . $s_pid . ") Count(" . $dup_count . ")\n";
			}
		}
	}
	$result->free();

	return;
}

function DisconnectDB() {
	global $mysqli;
	
	$mysqli->close();
	
	return;
}
?>