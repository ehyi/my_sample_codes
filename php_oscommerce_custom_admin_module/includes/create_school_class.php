<?php
include_once "create_school_config.php";

///////////////////////////////////////////////////////////////////////////////
class createSchool
{
    public $error_msg = ""; 
    public $success_msg = ""; 

    public function search_oscommerce($env, $config, $search_data_array) 
    {
        
        $search_results = array();      
        
        $sql_info = $config[$env];
        
        $con = mysql_connect($sql_info["host"], $sql_info["username"], $sql_info["password"]);
        if (!$con) 
        {
            $this->error_msg .= print_r(mysql_error(), true) . "<br />";
            return false;
        }
        mysql_select_db($sql_info["dbname"]);    
                
        $name  = mysql_real_escape_string(trim($search_data_array["name"]));
        $city  = mysql_real_escape_string(trim($search_data_array["city"]));
        $state = strtoupper(mysql_real_escape_string(trim($search_data_array["state"])));
        $zip   = strtoupper(mysql_real_escape_string(trim($search_data_array["zip"])));

        if ($name . $city . $state . $zip > "") {
            $sql_where = "s_name like '%" . str_replace(" ", "%", $name)  . "%' ";
            /*
            $name_array = explode(" ", $name);
            for ($i = 0; $i < count($name_array); $i++) {
                $sql_where .= "or s_name like '%" . $name_array[$i]  . "%' ";
                if ($i > 0) {
                    $subname = "";
                    for ($j = 0; $j <= $i; $j++) {
                        $subname .= $name_array[$j];
                        if ($j < $i) {
                            $subname .= " ";
                        }
                    }
                    if ($subname > "") {
                        $sql_where .= "or s_name like '%" . $subname . "%' ";
                    }
                }
            }
            */
        
            $query =  "SELECT ".
                    "s_pid, ".
                    "s_name, ".
                    "s_address, ".
                    "s_city, ".
                    "s_state, ".
                    "s_zip, ".
                    "s_country ".
                    "FROM schools ".
                    "WHERE s_city  like '" . $city  . "%' ".
                    "AND   s_state like '" . $state . "%' ".
                    "AND   s_zip   like '" . $zip   . "%' ".
                    "and (" . $sql_where . ") ".
                    "ORDER BY s_state, s_city, s_name";
            // echo $query . "<br />";
            $result = mysql_query($query);
            $result_count = mysql_num_rows($result);
            $i = 0;
            while ($row = mysql_fetch_array($result)) {
                $search_results[$i]["name"] = $row['s_name'];
                $search_results[$i]["address"] = $row['s_address'];
                $search_results[$i]["city"] = $row['s_city'];
                $search_results[$i]["state"] = $row['s_state'];
                $search_results[$i]["zip"] = $row['s_zip'];
                $search_results[$i]["country"] = $row['s_country'];
                $search_results[$i]["pid"] = $row['s_pid'];
                $i++;
            }
        }
        
        mysql_close($con);        
        
        return $search_results;
    }

    public function get_pid($env, $config) 
    {
        
        $pid = 0;     
        
        $sql_info = $config[$env];
        
        $con = mysql_connect($sql_info["host"], $sql_info["username"], $sql_info["password"]);
        if (!$con) 
        {
            $this->error_msg .= print_r(mysql_error(), true) . "<br />";
            return false;
        }
        mysql_select_db($sql_info["dbname"]);    
            
        $query =  "
            SELECT 
            max(s_pid) as s_pid 
            FROM schools 
        ";
        // echo $query . "<br />";
        $result = mysql_query($query);
        if ($row = mysql_fetch_array($result)) {
            $pid = $row['s_pid'];
        }
        
        mysql_close($con);        
        
        return $pid;
    }

    public function check_oscommerce($env, $config, $add_array) 
    {
        
        $function_result = true;        
        $sql_info = $config[$env];
        $myname = "[" . $env . "] ";
        
        if (!($con = mysql_connect($sql_info["host"], $sql_info["username"], $sql_info["password"])))
        {
            $this->error_msg .= $myname . TEXT_ERROR_CONNECT_DB . print_r(mysql_error(), true) . "<br />";	
            return false;               
        }
        mysql_select_db($sql_info["dbname"]);    
        
        $add_pid     = mysql_real_escape_string($add_array["pid"]);
        $add_name    = mysql_real_escape_string($add_array["name"]);
        $add_address = mysql_real_escape_string($add_array["address"]);
        $add_city    = mysql_real_escape_string($add_array["city"]);
        $add_state   = mysql_real_escape_string($add_array["state"]);
        $add_zip     = mysql_real_escape_string($add_array["zip"]);
        $add_country = mysql_real_escape_string($add_array["country"]);

        if ($add_name > "" && $add_address > "" && $add_city > "" && $add_state > "" && $add_zip > "") 
        {
            $query =  "select s_pid from schools where s_pid = " . $add_pid;
            $result = mysql_query($query);
            if (mysql_num_rows($result) > 0) 
            {
                $this->error_msg .= $myname . TEXT_ERROR_PID_EXIST . $add_pid . "<br />";
                $function_result = false;
            }
            
            $query =  "SELECT ".
                    "s_pid ".
                    "FROM schools ".
                    "WHERE s_name = '" . $add_name  . "' ".
                    "AND   s_address = '" . $add_address . "' ".
                    "AND   s_city = '" . $add_city . "' ".
                    "AND   s_state = '" . $add_state . "' ".
                    "AND   s_zip = '" . $add_zip   . "' ";
            $result = mysql_query($query);
            if ($row = mysql_fetch_array($result)) 
            {
                $this->error_msg .= $myname . TEXT_ERROR_SCHOOL_EXIST .  print_r($add_array, true) . "<br />";	
                $function_result = false;            
            }
        }
      
        mysql_close($con);        
      
        return $function_result;
    }

    public function check_magento($env, $config, $add_array) 
    {
        
        $function_result = true;
        $sql_info = $config[$env];
        $myname = "[" . $env . "] ";
        
        if (!($con = mysql_connect($sql_info["host"], $sql_info["username"], $sql_info["password"])))
        {
            $this->error_msg .= $myname . TEXT_ERROR_CONNECT_DB . print_r(mysql_error(), true) . "<br />";	
            return false;               
        }
        mysql_select_db($sql_info["dbname"]);    
        
        $add_pid     = mysql_real_escape_string($add_array["pid"]);
        $add_name    = mysql_real_escape_string($add_array["name"]);
        $add_address = mysql_real_escape_string($add_array["address"]);
        $add_city    = mysql_real_escape_string($add_array["city"]);
        $add_state   = mysql_real_escape_string($add_array["state"]);
        $add_zip     = mysql_real_escape_string($add_array["zip"]);

        if ($add_name > "" && $add_address > "" && $add_city > "" && $add_state > "" && $add_zip > "") 
        {
            $query =  "select PID from mdr_schools where PID = " . $add_pid;
            $result = mysql_query($query);
            if (mysql_num_rows($result) > 0) 
            {
                $this->error_msg .= $myname . TEXT_ERROR_PID_EXIST . $add_pid . "<br />";
                $function_result = false;
            }
            
            $query =  "SELECT ".
                    "PID ".
                    "FROM mdr_schools ".
                    "WHERE school = '" . $add_name  . "' ".
                    "AND   address = '" . $add_address . "' ".
                    "AND   city = '" . $add_city . "' ".
                    "AND   state = '" . $add_state . "' ".
                    "AND   zip = '" . $add_zip   . "' ";
            $result = mysql_query($query);
            if ($row = mysql_fetch_array($result)) 
            {
                $this->error_msg .= $myname . TEXT_ERROR_SCHOOL_EXIST .  print_r($add_array, true) . "<br />";
                $function_result = false;            
            }
        }
      
        mysql_close($con);        
      
        return $function_result;
    }

    public function check_kb($env, $config, $add_array)
    {
        
        $function_result = true;
        
        $url = $config[$env]["url"];
        $env = $config[$env]["env"];
        
        $postdata = 'name=' . $add_array["name"] .		
                '&address1=' . $add_array["address"] .	
                '&city=' . $add_array["city"] . 			
                '&state=' . $add_array["state"] . 		
                '&zipcode=' . $add_array["zip"] . 		
                '&country=' . $add_array["country"] . 								
                '&pid=' . $add_array["pid"].
                '&env=' . $env.
                '&action=check_school';	
        $curl_out = createSchool::make_curl($url, $postdata);
        if (trim($curl_out) != TEXT_SUCCESS)
        {
            $this->error_msg .= $curl_out . "<br />";
            $function_result = false;
        }
        
        return $function_result;
    }
    
    public function make_curl($url, $postdata)
    {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $curl_output = trim(curl_exec($ch));
        $curl_info = curl_getinfo($ch);
        // echo "curl_output(".$curl_output.")<br />";
        // echo "http_code(".$curl_info['http_code'].")<br />";
        if ($curl_output != "" || $curl_info['http_code'] != 200) {
            // error
        } else {
            // success
        }
        curl_close($ch);
        unset($ch);   

        return $curl_output;
    }
    
    public function insert_oscommerce($env, $config, $add_array) 
    {
        
        $function_result = true;
        $sql_info = $config[$env];
        $myname = "[" . $env . "] ";
        
        if (!($con = mysql_connect($sql_info["host"], $sql_info["username"], $sql_info["password"])))
        {
            $this->error_msg .= $myname . TEXT_ERROR_CONNECT_DB . print_r(mysql_error(), true) . "<br />";	
            return false;               
        }
        mysql_select_db($sql_info["dbname"]);    
        
        $add_name    = mysql_real_escape_string($add_array["name"]);
        $add_address = mysql_real_escape_string($add_array["address"]);
        $add_city    = mysql_real_escape_string($add_array["city"]);
        $add_state   = mysql_real_escape_string($add_array["state"]);
        $add_zip     = mysql_real_escape_string($add_array["zip"]);
        $add_pid     = mysql_real_escape_string($add_array["pid"]);

        if ($add_name > "" && $add_address > "" && $add_city > "" && $add_state > "" && $add_zip > "" && $add_pid > 0) 
        {
            $query =  "insert into schools (".
                    "s_name,".
                    "s_address,".
                    "s_city,".
                    "s_state,".
                    "s_zip,".
                    "s_country,".
                    "s_pid,".
                    "s_insttype".
                    ")values(".
                    "'" . $add_name . "',".
                    "'" . $add_address . "',".
                    "'" . $add_city . "',".
                    "'" . $add_state . "',".
                    "'" . $add_zip . "',".
                    "'US',".
                    "" . $add_pid . ",".
                    "0".
                    ") ";
            if ($result = mysql_query($query)) 
            {
                $this->success_msg .= $myname . TEXT_SUCCESS_SCHOOL_ADDED;
            }
            else
            {
                $this->error_msg .= $myname . TEXT_ERROR_QUERY . $query . ".<br />";	
                $function_result = false;            
            }
        }
        else 
        {
            $this->error_msg .= $myname . TEXT_ERROR_MISSING_PARAM . print_r($add_array, true) . "<br />";	
            $function_result = false;                        
        }
      
        mysql_close($con);        
      
        return $function_result;
    }

    public function insert_magento($env, $config, $add_array) 
    {
        
        $function_result = true;
        $sql_info = $config[$env];
        $myname = "[" . $env . "] ";
        
        if (!($con = mysql_connect($sql_info["host"], $sql_info["username"], $sql_info["password"])))
        {
            $this->error_msg .= $myname . TEXT_ERROR_CONNECT_DB . print_r(mysql_error(), true) . "<br />";	
            return false;               
        }
        mysql_select_db($sql_info["dbname"]);    
        
        $add_name    = mysql_real_escape_string($add_array["name"]);
        $add_address = mysql_real_escape_string($add_array["address"]);
        $add_city    = mysql_real_escape_string($add_array["city"]);
        $add_state   = mysql_real_escape_string($add_array["state"]);
        $add_zip     = mysql_real_escape_string($add_array["zip"]);
        $add_pid     = mysql_real_escape_string($add_array["pid"]);

        if ($add_name > "" && $add_address > "" && $add_city > "" && $add_state > "" && $add_zip > "" && $add_pid > 0) 
        {
            $query =  "insert into mdr_schools (".
                    "school,".
                    "address,".
                    "city,".
                    "state,".
                    "zip,".
                    "phone,".
                    "PID".
                    ")values(".
                    "'" . $add_name . "',".
                    "'" . $add_address . "',".
                    "'" . $add_city . "',".
                    "'" . $add_state . "',".
                    "'" . $add_zip . "',".
                    "'phone',".
                    "" . $add_pid . "".
                    ") ";
            if ($result = mysql_query($query)) 
            {
                $this->success_msg .= $myname . TEXT_SUCCESS_SCHOOL_ADDED;
            }
            else
            {
                $this->error_msg .= $myname . TEXT_ERROR_QUERY . $query . ".<br />";	
                $function_result = false;             
            }
        }
        else 
        {
            $this->error_msg .= $myname . TEXT_ERROR_MISSING_PARAM . print_r($add_array, true) . "<br />";	
            $function_result = false;                        
        }
        
        mysql_close($con);        
      
        return $function_result;
    }

    public function insert_kb($env, $config, $add_array)
    {
        
        $function_result = true;
        $myname = "[" . $env . "] ";
        $url = $config[$env]["url"];
        $env = $config[$env]["env"];

        $postdata = 'name=' . $add_array["name"] .		
                '&address1=' . $add_array["address"] .	
                '&city=' . $add_array["city"] . 			
                '&state=' . $add_array["state"] . 		
                '&zipcode=' . $add_array["zip"] . 		
                '&country=' . $add_array["country"] . 								
                '&pid=' . $add_array["pid"].
                '&env=' . $env.
                '&action=add_school';	
        $curl_out = createSchool::make_curl($url, $postdata);
        if (trim($curl_out) != TEXT_SUCCESS)
        {
            $this->error_msg .= $curl_out . "<br />";
            $function_result = false;
        }        
        else 
        {
            $this->success_msg .= $myname . TEXT_SUCCESS_SCHOOL_ADDED;                  
        }
        
        return $function_result;
    }    
    
}
///////////////////////////////////////////////////////////////////////////////
// End of class createSchool
///////////////////////////////////////////////////////////////////////////////
?>