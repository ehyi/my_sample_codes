<?php
require "inc/slim/Slim/Slim.php";

//wrapper to help decouple calls somewhat for front end
function wrap_output($output, $encode=TRUE){
	$app = Slim::getInstance();
	if(isset($_SESSION['enable_cache']) && $_SESSION['enable_cache']==TRUE){
		header("Cache-Control: public, max-age=60");
	}
	if(isset($_SESSION['wrap_output']) && $_SESSION['wrap_output'] == TRUE){
		$out = array("data" => $output, "status" => "success");	
		echo json_encode($out);
	}else {
		if ($encode){
			echo json_encode($output);
		}else{
			echo $output;
		}
	}
}	



function require_logged_in(){
    // let people work on dev enviroments w/o logging in
    if ( isset( $_SERVER['HTTP_X_FORWARDED_SERVER' ] ) &&
                $_SERVER['HTTP_X_FORWARDED_SERVER' ]  == 'localhost'
        ){
        $_SESSION['user_id'] = 10200;	// FOR TESTING
        return; 
    }

    if ( ! $_SESSION['is_logged_in']) {
        not_allowed(); 
    }
}

function only_in_office(){
    if( ENVIRONMENT !== 'office-staging' )
        not_allowed(); 
}
/**
 * set http status 404 and stop execution of script
 */
function not_found($msg=NULL, $level="Warning", $code="NOT_FOUND"){
	 $out = "";
	 if(is_null($msg)) $msg = "The specified input data or some required internal data was not found.";
	 $output = array("Code" => $code, "Error" => $out.$msg, "Level" => $level);
	 
	 $app = Slim::getInstance();
	 if(isset($_SESSION['wrap_output']) && $_SESSION['wrap_output'] == TRUE){
		$out = array("data" => $output, "status" => $code);	
		echo json_encode($out);
	 }else {
    	$app->response()->status( 404 );
	 	$app->halt(404, json_encode($output));
	 } 
	 $app->stop();
	 
}

function not_authorized($msg=NULL, $level="Warning", $code="NOT_AUTHORIZED"){
	 $out = "";
	 if(is_null($msg)) $msg = "The current user does not have the necessary permissions for this operation.";
	 $output = array("Code" => $code, "Error" => $out.$msg, "Level" => $level);

	 $app = Slim::getInstance();
	 if(isset($_SESSION['wrap_output']) && $_SESSION['wrap_output'] == TRUE){
		$out = array("data" => $output, "status" => $code);	
		echo json_encode($out);
	 }else {
    	$app->response()->status( 401 );
	 	$app->halt(401, json_encode($output));
	 }	
	 $app->stop(); 
}

function bad_request($msg=NULL, $level="Warning", $code="BAD_REQUEST"){
	 $out = "";
	 if(is_null($msg)) $msg = "The request body is not correctly formatted or is missing required fields.";
	 $output = array("Code" => $code, "Error" => $out.$msg, "Level" => $level);

	 $app = Slim::getInstance();
	 if(isset($_SESSION['wrap_output']) && $_SESSION['wrap_output'] == TRUE){
		$out = array("data" => $output, "status" => $code);	
		echo json_encode($out);
	 }else {
    	$app->response()->status(400);
	 	$app->halt(400, json_encode($output));
	 }	
	 $app->stop();
}

function not_allowed($msg=NULL, $level="Warning", $code="NOT_LOGGED_IN"){
	 $out = "";
	 if(is_null($msg)) $msg = "Client is not currently logged in.";
	 $output = array("Code" => $code, "Error" => $out.$msg, "Level" => $level);

	 $app = Slim::getInstance();
	 if(isset($_SESSION['wrap_output']) && $_SESSION['wrap_output'] == TRUE){
		$out = array("data" => $output, "status" => $code);	
		echo json_encode($out);
	 }else {
	 	//echo json_encode($output);
    	$app->response()->status( 403 ); 
	 	$app->halt(403, json_encode($output));
	 }
	 $app->stop(); 
}


function forbidden($msg=NULL, $level="Warning", $code="BOOK_ACCESS_DENIED"){
	 $out = "";
	 if(is_null($msg)) $msg = "This user does not have access to this product.";
	 $output = array("Code" => $code, "Error" => $out.$msg, "Level" => $level);

	 $app = Slim::getInstance();
	 if(isset($_SESSION['wrap_output']) && $_SESSION['wrap_output'] == TRUE){
		$out = array("data" => $output, "status" => $code);	
		echo json_encode($out);
	 }else {
	 	//echo json_encode($output);
    	$app->response()->status( 402 ); 
	 	$app->halt(402, json_encode($output));
	 }
	 $app->stop(); 
}


function custom_error($msg=NULL, $level="Warning", $code="GENERIC_ERROR", $error_num=410){
	 $out = "";
	 if(is_null($msg)) $msg = "No message given for this error.";
	 $output = array("Code" => $code, "Error" => $out.$msg, "Level" => $level);

	 $app = Slim::getInstance();
	 if(isset($_SESSION['wrap_output']) && $_SESSION['wrap_output'] == TRUE){
		$out = array("data" => $output, "status" => $code);	
		echo json_encode($out);
	 }else {
	 	//echo json_encode($output);
    	$app->response()->status( $error_num ); 
	 	$app->halt($error_num, json_encode($output));
	 }
	 $app->stop(); 
}


