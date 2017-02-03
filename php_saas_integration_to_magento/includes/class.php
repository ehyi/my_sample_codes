<?php
class SurveyEngine {

	var $survey_title = 'Test Survey';
	var $survey_subtitle;
	var $survey_questions;
	var $form_core_values = array(
		"xnQsjsdp" => "cueSWeD3ql9thYo@kRl79w$$",
		"zc_gad" => "",
		"xmIwtLD" => "rZpp9RpJaKGlGxXS6OpL7xIPrfT1d3Zy",
		"actionType" => "TGVhZHM="
		);
	var $company_name_length = 30;
	var $company_pid_length = 8;
    
	function SurveyEngine($survey_key='', $domain, $form_post = null, $isthanks = null) {
        
		$this->question_map[] = 'null';

        $this->question_map[] = 'LEADCF49';
        $this->question_map[] = 'LEADCF47';
        $this->question_map[] = 'LEADCF29';
        $this->question_map[] = 'LEADCF28';
        $this->question_map[] = 'LEADCF24';
        $this->question_map[] = 'LEADCF22';
        $this->question_map[] = 'LEADCF123';
        $this->question_map[] = 'LEADCF158';
        $this->question_map[] = 'LEADCF122';
        $this->question_map[] = 'LEADCF23';

        $this->answer_map[] = 'null';
        $this->answer_map[] = 'LEADCF152';
        $this->answer_map[] = 'LEADCF125';
        $this->answer_map[] = 'LEADCF124';
        $this->answer_map[] = 'LEADCF50';
        $this->answer_map[] = 'LEADCF48';
        $this->answer_map[] = 'LEADCF163';
        $this->answer_map[] = 'LEADCF121';
        $this->answer_map[] = 'LEADCF162';
        $this->answer_map[] = 'LEADCF128';
        $this->answer_map[] = 'LEADCF127';

		$this->domain = $domain;
		$this->validated = false;
		$this->survey_key = $survey_key;
		$this->load_survey();

		if(isset($form_post['survey_key'])){
			$a = $this->process_survey($form_post);
			//var_dump($form_post);
			//var_dump($a[0]);
			//die();
			if($this->validated){
				$this->send_to_zoho($a[0]);

				header ("Location: ?thanks=y&survey_key=" . $form_post['survey_key']);
				exit;
			}

		}
		$this->survey_title = $this->surveyd['survey_title'];
		$this->survey_subtitle = $this->surveyd['survey_subtitle'];
		$this->intro_text = nl2br($this->surveyd['intro']);
		foreach($this->surveyd['questions'] as $question) {
			 $sq = new SurveyQuestion($question);
			 $this->survey_questions[] = $sq;
		}
        
        return;
	}

	function send_to_zoho($zohodata){

        if ($this->surveyd["form_action"] > "") {
            $url = $this->surveyd["form_action"];
        } else {
            $url = 'https://crm.zoho.com/crm/WebToLeadForm';
        }

		$final_zohodata .= "returnURL=" . urlencode($this->surveyd['returnURL']) . "&";
		$final_zohodata .= urlencode("Lead Source") . "=" . urlencode($this->surveyd['lead']) . "&";
		$final_zohodata .= urlencode("Lead Status") . "=Open&";
		$final_zohodata .= $zohodata;
		// error_log("final_zohodata: ".$final_zohodata); 
		//return;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $final_zohodata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$return = curl_exec($ch);
		$info = curl_getinfo($ch);
        
		// error_log("curl url: ".print_r($url,true));
		// error_log("curl final_zohodata: ".print_r($final_zohodata,true));
		// error_log("curl return: ".print_r($return,true));
		// error_log("curl info: ".print_r($info,true));      

        return;   
	}

	function load_survey(){
		include('forms/sf_config/_forms.php');
		if(in_array($this->survey_key, $survey_keys)){
			include('forms/sf_config/'.$this->survey_key.'.php');
		}else{
			$domain = $this->domain;
			include('forms/sf_config/default.php');
		}

		$this->surveyd = $surveyd;
		//var_dump($surveyd);
        
        return;
	}

	function process_survey($data){

        $zohodata = "";
        $coredata = "";

        //var_dump($data); die();
		$this->answers = array();
		$description = $this->surveyd['lead_description']."\n";

		$zohodata = "Description=".urlencode($description)."&";
		foreach($data['data'] as $qid=>$qa){
			$que = $qa['q'];
			$ans = $qa['a'];
			//var_dump($qa['a']);
			if(is_array($ans)){
				$t=join("; ",$ans);
				foreach($ans as $v){
					$this->answers[$qid][$v] = $v;
					if(isset($qa['o'])){
						$this->answers[0][$qid]=$qa['o'];
					}
				}
				$ans= $t;
				
			}else{
				$this->answers[$qid] = $ans;
			}

			/*
			if($qid==1){
				$zohodata .= urlencode("LEADCF49")."=".urlencode($que." ".$ans)."&";
			}else{
				$zohodata .= urlencode("Question ".$qid)."=".urlencode($que." ".$ans)."&";
			}*/
			$zohodata .= urlencode($this->question_map[$qid])."=".urlencode($que)."&";
			$zohodata .= urlencode($this->answer_map[$qid])."=".urlencode($ans)."&";
			

			$description .= $que."\n".$ans."\n\n";
		}

		//$company = $data['LEADCF40'] . "-" . $data['LEADCF26'];
		$company = str_pad(substr($data['LEADCF40'], 0, $this->company_name_length), $this->company_name_length, " ", STR_PAD_RIGHT) . "-" . str_pad(substr($data['LEADCF26'], 0, $this->company_pid_length), $this->company_pid_length, "0", STR_PAD_LEFT);
		$invalid = false;
		if(trim($data['First_Name'])==''){
			$invalid = true;
			$errors[] = 'First Name';
		}
		if(trim($data['Last_Name'])==''){
			$invalid = true;
			$errors[] = 'Last Name';
		}
		if(trim($data['Designation'])==''){
			$invalid = true;
			$errors[] = 'Title';
		}
		if(trim($data['Email'])==''){
			$invalid = true;
			$errors[] = 'Email';
		}

		if(trim($data['LEADCF40'])==''){
			$invalid = true;
			$errors[] = 'Your School';
		}
		if(!$invalid){
			
			$this->validated = true;
		}else{
			$this->error_message = "Please be sure all of the following are completed. <br>".join("<br>",$errors);
		}

		$zohodata .=urlencode("Company")."=".$company."&";
		$this->answers['Company']=$company;
        
		$this->answers[99][2]=$data['LEADCF26'];

		$zohodata .=urlencode("Zip Code")."=".$data['Zip_Code']."&";
		$this->answers[99][1]=$data['Zip_Code'];
		$zohodata .=urlencode("LEADCF40")."=".$data['LEADCF40']."&";
		$zohodata .=urlencode("LEADCF26")."=".$data['LEADCF26']."&";
		
		$zohodata .=urlencode("LEADCF25")."=".$data['LEADCF25']."&";
		$zohodata .=urlencode("LEADCF44")."=".$data['LEADCF44']."&";
		$zohodata .=urlencode("LEADCF17")."=".$data['LEADCF17']."&";
		$zohodata .=urlencode("LEADCF27")."=".$data['LEADCF27']."&";
		$zohodata .=urlencode("LEADCF41")."=".$data['LEADCF41']."&";
		$zohodata .=urlencode("LEADCF42")."=".$data['LEADCF42']."&";
		$zohodata .=urlencode("LEADCF43")."=".$data['LEADCF43']."&";



		$zohodata .=urlencode("Street")."=".$data['Street']."&";
		//$this->answers['Street']=$data['Street'];
		$zohodata .=urlencode("City")."=".$data['City']."&";
		//$this->answers['City']=$data['City'];
		$zohodata .=urlencode("State")."=".$data['State']."&";
		//$this->answers['State']=$data['State'];
		$zohodata .=urlencode("Phone")."=".$data['Phone']."&";
		//$this->answers['Phone']=$data['Phone'];

		$zohodata .=urlencode("First Name")."=".$data['First_Name']."&";
		$this->answers[99][8]=$data['First_Name'];
		$zohodata .=urlencode("Last Name")."=".$data['Last_Name']."&";
		$this->answers[99][9]=$data['Last_Name'];
		$zohodata .=urlencode("Designation")."=".$data['Designation']."&";
		$this->answers[99][10]=$data['Designation'];
		$zohodata .=urlencode("Email")."=".$data['Email'];
		$this->answers[99][11]=$data['Email'];    
                
        if ($this->survey_key == "contacts") {
            $zohodata = urlencode("COBJ6CF19") . "=" . urlencode($data['data'][1]['a']) . "&";
            $zohodata .= urlencode("COBJ6CF21") . "=" . urlencode($data['data'][2]['a']) . "&";
            $zohodata .= urlencode("COBJ6CF13") . "=" . urlencode($data['First_Name']) . "&";
            $zohodata .= urlencode("COBJ6CF15") . "=" . urlencode($data['Last_Name']) . "&";
            $zohodata .= urlencode("COBJ6CF11") . "=" . urlencode($data['Designation']) . "&";
            $zohodata .= urlencode("Email") . "=" . urlencode($data['Email']) . "&";
            $zohodata .= urlencode("COBJ6CF25") . "=" . urlencode($company) . "&"; // Account Name
            $zohodata .= urlencode("COBJ6CF10") . "=" . urlencode($data['Phone']) . "&";
            $zohodata .= urlencode("COBJ6CF7") . "=" . urlencode($data['Street']) . "&";
            $zohodata .= urlencode("COBJ6CF6") . "=" . urlencode($data['City']) . "&";
            $zohodata .= urlencode("COBJ6CF4") . "=" . urlencode($data['State']) . "&";
            $zohodata .= urlencode("COBJ6CF3") . "=" . urlencode($data['Zip_Code']) . "&";
            // $zohodata .= urlencode("COBJ6CF1") . "=" . urlencode($data['']) . "&"; //County
            // $zohodata .= urlencode("COBJ6CF5") . "=" . urlencode($data['']) . "&"; //Country
            $zohodata .= urlencode("COBJ6CF2") . "=" . urlencode($data['LEADCF17']) . "&"; //FIPS
            $zohodata .= urlencode("COBJ6CF20") . "=" . urlencode($data['LEADCF26']) . "&"; //PID
            $zohodata .= urlencode("COBJ6CF17") . "=" . urlencode($data['LEADCF44']) . "&"; //PARENTID
            $zohodata .= urlencode("COBJ6CF18") . "=" . urlencode($data['LEADCF45']) . "&"; //UPID
            $zohodata .= urlencode("COBJ6CF14") . "=" . urlencode($data['LEADCF25']) . "&"; //FILETYPE
            $zohodata .= urlencode("COBJ6CF12") . "=" . urlencode($data['LEADCF42']) . "&"; //SCHTYPE
            $zohodata .= urlencode("COBJ6CF8") . "=" . urlencode($data['LEADCF43']) . "&"; //SCHCLASS
            $zohodata .= urlencode("COBJ6CF16") . "=" . urlencode($data['LEADCF27']) . "&"; //LOWGRADE
            $zohodata .= urlencode("COBJ6CF9") . "=" . urlencode($data['LEADCF41']) . "&"; //HIGHGRADE
        }
       
        // Decalred in sf_config
        foreach ($this->surveyd["form_core"] as $k => $v) {
            $coredata .= $k . "=" . urlencode($v) . "&"; 
        }

        // Otherwise, load the default values
        foreach ($this->form_core_values as $k => $v) {
            if (!array_key_exists($k, $this->surveyd["form_core"])) {
                $coredata .= $k . "=" . urlencode($v) . "&"; 
            }
        }   

        $zohodata = $coredata . $zohodata;
        
		//var_dump($this->answers);
		return array($zohodata,$description);
		//echo nl2br($zohodata);
	}
}

class SurveyQuestion {

	var $question = 'This is a question';

	var $question_type = 'text';

	function SurveyQuestion($q){
		$this->question = $q['question'];
		$this->question_type = $q['type'];
		$this->answers = $q['answers'];

        return;
	}
    
}

class School {

    static function school_by_zip() {

        $return = "";
    
        $zip = mysql_real_escape_string(trim($_REQUEST['zip']));

        $myarray = School::read_local_xml();
        mysql_connect("localhost:" . $myarray[0], $myarray[1], $myarray[2]) or die(mysql_error());
        mysql_select_db($myarray[3]) or die(mysql_error());
        $query = "SELECT * FROM mdr_schools WHERE zip = '" . $zip . "'";
        $result = mysql_query($query) or die(mysql_error());

        $return .= "<select name=\"schooltemp\" id=\"school\" class=\"input-text required\">";

        if ($count = mysql_num_rows($result)) {
            $return .= "<option value=\"\"> - Select your school - </option>";
            for ($i = 0; $i < $count; $i++) {
                $rows = mysql_fetch_array($result);
                $return .= "<option value=\"" . $rows['PID'] . "\">" . $rows['school'] . "</option>";
            }
        } else { 
            $return .= "<option value=\"\">No schools found for this zip code.</option>";
        }
        $return .= "</select>";
        
        return $return;
    }
    
    static function school_selected() {

        $return = "";

        $myarray = School::read_local_xml();
        mysql_connect("localhost:" . $myarray[0], $myarray[1], $myarray[2]) or die(mysql_error());
        mysql_select_db($myarray[3]) or die(mysql_error());

        $pid = mysql_real_escape_string(trim($_REQUEST['pid']));

        $query = "SELECT * FROM mdr_schools WHERE PID = '" . $pid . "'";
        $result = mysql_query($query) or die(mysql_error());
        if ($count = mysql_num_rows($result)) {
            $rows = mysql_fetch_array($result);
        
            $return .= "<input type=\"hidden\" name=\"LEADCF40\" value=\"" . $rows["school"] . "\" />";
            $return .= "<label class=\"label\">School Address:</label>";
            $return .= "<input type=\"text\" name=\"Street\" value=\"" . $rows["address"] . "\" class=\"input-text required\" /><br />";
            $return .= "<div class=\"clear-both\" style=\"padding-bottom:5px;\"></div>";
            $return .= "<label class=\"label\">School City:</label>";
            $return .= "<input type=\"text\" name=\"City\" value=\"" . $rows["city"] . "\" class=\"input-text required\" /><br />";
            $return .= "<div class=\"clear-both\" style=\"padding-bottom:5px;\"></div>";
            $return .= "<label class=\"label\">School State: </label>";
            $return .= "<input type=\"text\" name=\"State\" value=\"" . $rows["state"] . "\" class=\"input-text required\" /><br />";
            $return .= "<div class=\"clear-both\" style=\"padding-bottom:5px;\"></div>";
            $return .= "<label class=\"label\">Phone: </label>";
            $return .= "<input type=\"text\" name=\"Phone\" id=\"phone\" class=\"input-text required\" value=\"" . $rows["phone"] . "\" /><br />";
            $return .= "<div class=\"clear-both\" style=\"padding-bottom:5px;\"></div>";
            $return .= "<input type=\"hidden\" name=\"LEADCF25\" value=\"" . $rows["filetype"] . "\" />"; //FILETYPE
            $return .= "<input type=\"hidden\" name=\"LEADCF26\" value=\"" . $rows["PID"] . "\" />"; //PID
            $return .= "<input type=\"hidden\" name=\"LEADCF44\" value=\"" . $rows["parentpid"] . "\" />"; //PARENTPID
            $return .= "<input type=\"hidden\" name=\"LEADCF45\" value=\"" . $rows["upid"] . "\" />"; //UPID
            $return .= "<input type=\"hidden\" name=\"LEADCF17\" value=\"" . $rows["fips"] . "\" />"; //?FIPS
            $return .= "<input type=\"hidden\" name=\"LEADCF27\" value=\"" . $rows["lowgrade"] . "\" />"; //LOWGRADE
            $return .= "<input type=\"hidden\" name=\"LEADCF41\" value=\"" . $rows["highgrade"] . "\" />"; //HIGHGRADE
            $return .= "<input type=\"hidden\" name=\"LEADCF42\" value=\"" . $rows["schtype"] . "\" />"; //SCHTYPE
            $return .= "<input type=\"hidden\" name=\"LEADCF43\" value=\"" . $rows["schclass"] . "\" />"; //SCHCLASS
        }

        return $return;
    }
    
    static function read_local_xml() {
        
        $return = array();
        
        $file_path = "../../../../../../etc/local.xml";
        $xml = simplexml_load_file($file_path) or die ("Error: Cannot load file");
        $return[] = (string)$xml->global->resources->default_setup->connection->host;
        $return[] = (string)$xml->global->resources->default_setup->connection->username;
        $return[] = (string)$xml->global->resources->default_setup->connection->password;
        $return[] = (string)$xml->global->resources->default_setup->connection->dbname;
        
        return $return;
    }
    
}