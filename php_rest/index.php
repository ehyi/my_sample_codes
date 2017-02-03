<?php

require "config.php";
require "inc/classes/autoloader.php";
require "inc/slim.inc.php"; 
require "inc/dbin.inc.php"; 

SessionHandler::start();
$debug = FALSE; // turn off display of Slim errors

$slim = new Slim( array (
                        'debug' => $debug,
                ) );
$slim->response()->header('Content-type', 'application/json');
            	    
# cors file sets up rules to allow cross domain script access
require "inc/cors.php";

$slim->error( 'log_slim_error' );

$slim->map     ( '/users',                 'create_user'  )->via ('POST' );
$slim->map     ( '/users/login',           'login'        )->via ('POST' );
$slim->map     ( '/users/logout',          'logout'       )->via ('POST', 'PUT', 'GET');
$slim->map     ( '/users/register',        'register'     )->via ('POST' );
$slim->map     ( '/users/status',          'user_status'  )->via ('GET'  );
$slim->map     ( '/users/status/wrapped',  'user_status_wrapped'  )->via ('GET'  );

$slim->map     ( '/users/password',        'change_pw'    )->via ('POST' );
$slim->map     ( '/users/password/token',  'change_pw_token' )->via ('POST' );
$slim->map     ( '/users/email',           'change_email' )->via ('POST' );
$slim->map     ( '/users/password/reset',  'reset_pw'     )->via ('POST' );

$slim->map     ( '/courses/token/:action', 'mod_token'    )->via ('POST' );
$slim->map     ( '/courses/token/:code',   'token_info'   )->via ('GET'  );

//$slim->map     ( '/message/:msg',          'message'      )->via ('GET');
//$slim->map     ( '/debuginfo',             'debuginfo'    )->via ('GET');

//declare 404 page for non ajax requests
$slim->notFound('custom_not_found_callback');
function custom_not_found_callback() {
    $slim = Slim::getInstance();
    if ( ! $slim->request()->isAjax() ){
        $slim->redirect('/forms/404.php');
    }
}
$slim->run();
exit;

/*|***************||*\
|*| END LIVE CODE ||*|
\*|***************||*/
//TODO: remove before live / restrict to test
function debuginfo(){
    echo "<pre>";
    print_r ( $_REQUEST );
    print_r ( $_SESSION );
}

/**
 * a call to this error handler would be unexpected,
 * dump a bunch of stuff to the error log to help
 * track down any issues that may arise,
 */
function log_slim_error( Exception $e ){
    $error = array(
        '_SERVER'  => $_SERVER,
        '_REQUEST' => $_REQUEST,
        'message'  => $e->getMessage(),
        'line'     => $e->getLine(),
        'file'     => $e->getFile(),
        'stack'    => $e->getTraceAsString(),
    ) ;

    if( isset( $_SESSION) )
        $error['_SESSION'] = $_SESSION;
    error_log ( "SLIM Error:" );
    error_log( print_r ( $error, TRUE ) );
}

function user_status(){
    require_logged_in();
    $user = User::get_logged_in_user();
    echo json_encode( $user->status() );
}

function user_status_wrapped(){
    require_logged_in();
    $user = User::get_logged_in_user();
    wrap_output( $user->status() );
}

/**
 * give info about token with $code
 */
function token_info( $code ){
    $token = new RegistrationToken( $code );

    if ( ! $token->is_valid() ){
        deny('invalid token', 401 );
    }

    echo json_encode( $token->get_info() );
}
/**
 * POST /users/password
 *
 * attempt to change a users password
 *
 * parameters:
 *  new_pw:      users new password
 *
 * addition parameters if user not logged in
 *  pw   : users current password
 *  email: users current email
 *
 * response codes: see change_user_item
 */
function change_pw(){
    $requires   = array( 'new_pw' );
    $input_name = 'new_pw';

    change_user_item( 'password', $input_name, $requires );
}

/**
 * POST /users/password/token
 *
 * change users forgotten password using a token
 * rather than existing credentials
 *
 * requires:
 * 	token : password_reset_tokens.token
 * 	pw: 	new password
 *
 * response codes:
 * 	400 : password doensn't meet requirements
 * 	403 : invalid token, missing input
 */
function change_pw_token(){
	$input    = get_input_array();
	$requires = array( 'pw', 'token' );

	$wrap_output = isset($input['wrap_output'])? $input['wrap_output'] : false;
	if( ! Validator::required_inputs( $requires, $input ) ){
		if($wrap_output){
			not_allowed("Missing parameter.","Warning","MISSING_INPUT_FIELD");
		}else
			deny ( 'missing parameter' , 403 );
	}

	if( ! Validator::is_password( $input['pw'] ) ){
		if($wrap_output){
			bad_request("Password doesn't meet requirements.","Warning","INVALID_INPUT_VALUES");
		}else
			deny( "password doensn't meet requirements", 400 );
	}

	$token = new PasswordResetToken( $input['token'] );
	if( ! $token->is_valid() ){
		if($wrap_output){
			not_allowed("Invalid token.","Warning","INVALID_INPUT_VALUES");
		}else
			deny( 'invalid token', 403 );
	}

	$user = new User( $token->get_user_id() );
	$user->password = $input['pw'];

	$user->save();
	$token->consume();
	if($wrap_output) wrap_output('');
}

/**
 * POST /users/email
 *
 * attempt to change a users email
 *
 * parameters:
 *  new_email:      users new email
 *
 * addition parameters if user not logged in
 *  pw   : users current password
 *  email: users current email
 *
 * response codes:
 *  409: user w/ email already exists
 *  see change_user_item for more
 */
function change_email(){
    $requires   = array( 'new_email' );
    $input_name = 'new_email';

    $input = get_input_array();
	$wrap_output = isset($input['wrap_output'])? $input['wrap_output'] : false;
    if ( isset( $input[ $input_name ] ) && User::email_in_use( $input[ $input_name ] ) ) {
		if($wrap_output){
			custom_error("Email address already exists.","Warning","USERNAME_TAKEN",409);
		}else 
			deny( 'email address already exists', 409 );
    }

    change_user_item( 'email', $input_name, $requires );
}

/**
 * change a publicly accessible user property
 * @param {string} $field       name of publicly accessible user property
 * @param {string} $input_name  name of input key containing the new value for field
 * @param {array}  $requires    a list of required input fields
 *
 * response codes:
 *  200: success
 *  400: input doesnt validate  ( password )
 *  401: input doesnt validate  ( email )
 *  403: missing parametr / invalid credentials if not logged in
 */
function change_user_item( $field, $input_name, $requires ){

    // check if Validator defines an "is_$field" function
    // which will be called if it exists
    $validate_func = "is_" . $field;
    $validate      = method_exists( "Validator", $validate_func ) ;

    //if users isn't logged in also require login creds
    if ( ! $_SESSION['is_logged_in'] ){
        $requires[] = 'pw';
        $requires[] = 'email';
    }

    $input = get_input_array();

	$wrap_output = isset($input['wrap_output'])? $input['wrap_output'] : false;
	
	if ( ! Validator::required_inputs( $requires, $input ) ){
		if($wrap_output){
			not_allowed("Missing parameter", "Warning","MISSING_INPUT_FIELD");
		}else 
			deny ( 'missing parameter' , 403 );
    }

    //confirm with Validator func
    if ( $validate && ! Validator::$validate_func( $input[ $input_name ] ) ){
        //vary error response based on field to help w/ front end error msg
        switch ( $field ){
        case 'email':
            $code = 401;
            break;
        case 'password':
        default:
            $code = 400;
        }
		if($wrap_output){
			custom_error("$field doesn't meet requirements", "Warning","INVALID_INPUT_VALUES", $code);
		}else 
        	deny ( "$field doesn't meet requirements" , $code );
    }

    // if user is not logged in they must authenticate before
    // changing their data,
    //
    // if we receive a email password combo validate it
    // even if it looks like the user is already logged in
    if( ! $_SESSION['is_logged_in'] || isset( $input['pw'] ) ){
        $auth  = new Authenticator( $input['email'], $input['pw'] );
        //auth user name and password
        if ( ! $auth->authenticate() ){
			if($wrap_output){
				not_allowed("Invalid email or password", "Warning","INVALID_INPUT_VALUES");
			}else 
            	deny( 'Invalid email or password.', 403 );
            //deny stops execution
        }
        $user = User::fetch_by_email( $input['email'] );

    }else{
        //logged in user, look up by session user id
        $user = new User ( $_SESSION['user_id'] );
    }

    if ( is_null ( $user ) ){
		if($wrap_output){
			not_found("Couldn't find user.", "Warning","USER_NOT_FOUND");
		}else 
        	deny( 'uhoh? couldnt find user', 500  );
    }

    $user->$field = $input[ $input_name ];
    $user->save();
	
	if($wrap_output){
		wrap_output('{}');
	}else 
		echo '{}';	// Must return valid JSON
}

/**
 * POST /users/password/reset
 *
 * Attempt to reset a users password
 * sends user an email w/ a link to change pw
 *
 *
 * requires:
 *   email: users email address
 *
 * response codes:
 *  200:  success
 *        no user account for address
 *  403:  missing parameter,
 *        invalid email address ( formatting )
 */
function reset_pw(){
    $input    = get_input_array();
    $requires = array( 'email' );

	$wrap_output = isset($input['wrap_output'])? $input['wrap_output'] : false;
	
	if ( ! Validator::required_inputs( $requires, $input ) ){
		if($wrap_output){
			not_allowed("Missing parameter","Warning","MISSING_INPUT_FIELD");
		}else 	
			deny ( 'missing parameter' , 403 );
    }

    if ( ! Validator::is_email( $input['email'] ) ) {
		if($wrap_output){
			not_allowed("Invalid email address.","Warning","INVALID_INPUT_VALUES");
		}else 	
        	deny ('invalid email address', 403 );
    }

    $user = User::fetch_by_email( $input['email'] );

    if ( is_null( $user ) ){
        //deny but send 200 response to prevent phishing
        //for user emails
        //deny( 'no user account exists for this email', 200 );
        return;
    }

	$token = PasswordResetToken::generate( $user->user_id );

    send_password_reset_mail( $user, $token->get_url() );
	if($wrap_output){wrap_output('');}
}

/**
 * send an email to $user letting them know that their
 * new password is $new_pass
 * $User user to end mail to
 * $url url user needs to visit to change their password
 */
function send_password_reset_mail( User $user, $url ){

    require_once WEBROOT . "mail_svr/mail_api.php";

    $email     = $user->email;
    $recipient = $user->first_name . " " . $user->last_name . " <$email>";

    $from      =  "Online Products <no-reply@mydomain.com>" ;
    $feedback  = "https://www.mydomain.com/technical-support";

    $data = array( array(
        'to'     => $recipient,
        'fields' => array(
            'email'        => $email,
            'feedback_url' => $feedback,
            'reset_url'    => $url,
        ),
    ) );
    send_template( 'password_reset', $data, $from );
}

/**
 * POST  /users
 *
 * create a new student account
 * with the supplied paramters
 * and establish a logged in session for new user
 *
 * requires:
 *  first: user first name
 *  last:  user last name
 *  email: user email OR username
 *  pw:    users password,
 *  token: course code or book book reg code
 *
 *
 * response codes:
 * 200: success!
 * 401: invalid token
 * 402: from login: user doesn't have access to book
 *        ( potentially, uses a1 book creates acct w/ a2 course code etc. )
 * 403: missing parameter
 *      invalid email / password
 * 409: account exists
 * 410: no remaining seats
 * 412: course code can't create account
 */
function create_user(){
    $input = get_input_array();
    $requires = array( 'first', 'last', 'email', 'pw', 'token' );

	$wrap_output = isset($input['wrap_output'])? $input['wrap_output'] : false;
	
	if ( ! Validator::required_inputs( $requires, $input ) ){
		if($wrap_output){
			not_allowed("Missing parameter","Warning","MISSING_INPUT_FIELD");
		}else 
			deny ( 'missing parameter' , 403 );
    }

    if ( ! Validator::is_password( $input['pw'] ) ){
		if($wrap_output){
			not_allowed("Invalid password.","Warning","INVALID_INPUT_VALUES");
		}else 
        	deny ('invalid password', 403 );
    }

    $book  = Book::get_active();
    $fact  = new RegistrationCodeFactory();
    $token = $fact->make_registration_object( $input['token'], $book->book_id );

    if ( ! $token->is_valid() ){
		if($wrap_output){
			not_authorized("Invalid token.","Warning","INVALID_INPUT_VALUES");
		}else 
        	deny( 'invalid token', 401 );
    }

    if( ! $token->has_seats() ){
		if($wrap_output){
			custom_error("Token has no seats available.","Warning","NO_SEATS_AVAILABLE", 410);
		}else 
        	deny( 'token has no seats available', 410 );
    }

    if ( ! $token->can_create_accounts() ){
		if($wrap_output){
			custom_error("Token cannot create accounts.","Warning","WRONG_USER_LEVEL", 412);
		}else 
        	deny( 'token can not create accounts', 412 );
    }

    if ( ! is_null( User::fetch_by_email( $input['email'] ) ) ){
		if($wrap_output){
			custom_error("User account for this email already exists.","Warning","USERNAME_TAKEN", 409);
		}else 
        	deny( 'user account already exists for this email', 409 );
    }


    $user = new User() ;
    $user->first_name = $input['first'];
    $user->last_name  = $input['last'];
    $user->email      = $input['email'];
    $user->password   = $input['pw'] ;
    $user->usertype   = $token->get_create_user_usertype();

    $user->save();

    // Temporarily set session for course->create.
	SessionHandler::restart();
	$_SESSION['is_logged_in'] = TRUE;
	$user->populate_session();
	SessionHandler::update_last_activity();
	$token->register( $user );
	SessionHandler::destroy();
	SessionHandler::start();

    $output_status = FALSE;
    login( $output_status );
	
	$msg = json_encode( array_merge(
        array('msgs' => $token->get_response() ),
        $user->status() ) )  ;
	if($wrap_output){
		wrap_output($msg);
	}else 
    	echo $msg;
}
/**
 *  Modify properties of a registration token,
 *  supported $action
 *      regenerate - create a new token string, invalidates existing token
 *      approval   - set requires_approval field to provided value
 *
 *  logged in user must be course owner to use this
 */
function  mod_token( $action ){

    if ( ! $_SESSION['is_logged_in'] ){
        deny('Must be logged in' );
    }

    $user             = new User   ( $_SESSION['user_id'] );
    $course           = new course ( $_REQUEST['course_id'] );
    $require_approval = ( bool )     $_REQUEST['require_approval'];

    if ( ! $course->is_owner ( $user->user_id ) ){
        deny('must be course owner');
    }

    switch ( $action ){
    case 'regenerate':
        $token = RegistrationToken::generate( $course->course_id, $require_approval );
        $msg   = "Token is now " . $token ;
        break;

    case 'approval':
        $token = RegistrationToken::get_token_by_course_id( $course->course_id );
        $token->set_require_approval( $require_approval );
        $msg = "Updated application preference.";
        break;
    }

    if ( isset ( $_REQUEST['return'] )) return_to_sender( $msg );
}

/**
 * destroy current session
 */
function clear_session(){
    SessionHandler::restart();
}

/**
 * log use out of system
 * by discarding the current session
 */
function logout(){
    clear_session();
}

function message( $msg ) {
    echo $msg;
}

/**
 * return array containing associative array of inputs
 * either $_REQUEST or decoded json
 */
function get_input_array(){
    $request = Slim::getInstance()->request();
    if ( $request->getMediaType() == 'application/json' ){
        $input = json_decode( $request->getBody(), TRUE );  // true => get array
    }else{
        $input = $_REQUEST;  //@todo change to _POST / _PUT ? read from slim?
    }
    return $input;
}

/**
 * POST /users/login
 *
 * attempt to log a student into the system,
 *
 * requires:
 *  email:  users email address / username
 *  pw:     users password
 *
 * response codes:
 * 200: success!
 * 402: user doesn't have access to book
 * 403: missing parameter OR invalid username / pass
 * 412: student trying to access book from outside of browser
 *
 */
function login( $output_status = TRUE ){

    $slim     = Slim::getInstance();
    $request  = $slim->request();

    //forget current session and run through
    //fresh login procedure
    SessionHandler::restart();

    //field names for email and password
    $email_field    = 'email';
    $password_field = 'pw';
    $requires       = array( $email_field, $password_field );
    $input          = get_input_array();
	
	$wrap_output = isset($input['wrap_output'])? $input['wrap_output'] : false;

    //check input existence
    if ( ! Validator::required_inputs( $requires, $input ) ){
		if($wrap_output){
			bad_request("Missing parameter", "Warning", "MISSING_INPUT_FIELD");	
		}else 
			deny ( 'missing parameter' , 403 );
		
    }

    $email   = $input[ $email_field    ];
    $pass    = $input[ $password_field ];

    $auth  = new Authenticator( $email, $pass );

    //auth user name and password
    if ( ! $auth->authenticate() ){
		if($wrap_output){
			not_allowed("Invalid email or password.", "Warning", "INVALID_INPUT_VALUES");
		}else 
        	deny( '02: Invalid email or password.' );
        //deny stops execution
    }

    /**
     * login complete
     * now set up session
     */
    $_SESSION['active_book'] = Book::get_active();

    if( ! in_x_browser() && isset( $input['book_id'] ) ){
        // user input based book detection
        $book = new Book( $input['book_id'] );
        $_SESSION['pid']          = $book->pid;
        $_SESSION['book_id']      = $book->book_id;
        $_SESSION['bver_id']      = $book->bver_id;
    }else{
        // user agent based book detection
        $_SESSION['pid']     = getPidFromUA();
        $_SESSION['book_id'] = getBookIDsFromPid();
        $_SESSION['bver_id'] = getBverFromUA();
    }
    if( isset( $_SERVER['HTTP_REFERER'] ) )
        $_SESSION['active_book']->set_content_url( $_SERVER['HTTP_REFERER'] );


    $user = User::fetch_by_email( $email );


    /**
     * don't allow student users access to protected content if not in one of
     * our browsers
     */
    //if( $user->is_student() && ! in_x_browser() ){
        //clear_session();
        //deny('attempting to access from outside of xooks browser', 412 );
    //}
    /**
     * Check if user should have access to this book if its an online version
     * of a book
     */
    if( $_SESSION['active_book']->manages_login() &&
        ! $user->has_book_access( $_SESSION['active_book']->book_id ) ){
        clear_session();
		if($wrap_output){
			forbidden("User does not have access to ".$_SESSION['active_book']->book_name);
		}else 
        	deny('no access to this book: ' . $_SESSION['active_book']->book_name, 402 );
    }

	// For XYZ - BEGIN
	if (!isset($_SESSION['active_book']) || $_SESSION['active_book']->book_id == -1 || !$_SESSION['active_book']->book_id)
	{
		if (strpos($_SERVER['HTTP_HOST'], 'mydomain.com') !== FALSE) 
		{	
			$param = array ("%string%x%", "%mybrand%");
			
			global $db;
			$query = "select book_id from books where (book_name ilike $1 or book_name ilike $2)";
			$result = pg_query_params($db, $query, $param);
			$mybrand_book_id_array = array();
			while ($row = pg_fetch_assoc($result)) {
				$mybrand_book_id_array[] = $row["book_id"];
			}
			$books = $user->books_user_has_access_to();
			$have_access = false;

			switch ($user->usertype) {
				case 6:
				case 5:
				case 4:
					$have_access = true;
					$book_id_temp = 47;	// Assign TE book to admins.
					break;
				case 3:
				case 2:
					foreach($books as $key => $onebook) {
						// Only TE book grants access to TEs and TAs.
						if (in_array($onebook["book_id"], $mybrand_book_id_array) && $onebook["is_te"]) { 
							$have_access = true;
							if ($onebook["student_book_id"] > 0) {
								$book_id_temp = $onebook["student_book_id"];
							} else {
								$book_id_temp = $onebook["book_id"];
							}
							$bver_id_temp = Book::get_max_bver_id($book_id_temp);
							$course_id_temp = $user->select_course($book_id_temp, $bver_id_temp);
							if ($course_id_temp != "") { // Found a course
								$_SESSION['book_id'] = $book_id_temp; 
								$_SESSION['bver_id'] = $bver_id_temp; 
								break;
							}
						}
					}	
					break;
				default:
					foreach($books as $key => $onebook) {
						if (in_array($onebook["book_id"], $mybrand_book_id_array)) {
							$have_access = true;
							if ($onebook["student_book_id"] > 0) {
								$book_id_temp = $onebook["student_book_id"];
							} else {
								$book_id_temp = $onebook["book_id"];
							}
							$bver_id_temp = Book::get_max_bver_id($book_id_temp);
							$course_id_temp = $user->select_course($book_id_temp, $bver_id_temp);			
							if ($course_id_temp != "") { // Found a course
								$_SESSION['book_id'] = $book_id_temp; 
								$_SESSION['bver_id'] = $bver_id_temp; 
								break;
							}
						}
					}
					break;
			}
			// You have a book access.
			if ($have_access) {
				if ($book_id_temp) {
					$_SESSION['active_book'] = new Book($book_id_temp); 	
				}
			} else {
				clear_session(); 
				if($wrap_output){
					forbidden("User does not have access to ".$_SESSION['active_book']->book_name);
				}else 
					deny('No access to this book: ' . $_SESSION['active_book']->book_name, 402 ); 
			}
		}
	}
	// For XYZ - END	
							
							
							
	$_SESSION['is_logged_in'] = TRUE;
    $user->populate_session();
    $user->select_course( $_SESSION['book_id'], $_SESSION['bver_id'] );
	
    
    SessionHandler::update_last_activity();

    if( is_single_login_book() ){
        SessionHandler::set_book_id( $_SESSION['book_id'] );
    }

    if ( use_dst( $_SESSION['user_id'], $_SESSION['timezone'] ) )
        $_SESSION['timezone'] += 1;

	 if( $output_status ){
		  $outstat = $user->status();
		if($wrap_output){
			wrap_output($outstat);
		}else 
        	echo json_encode( $outstat );
    }
}
/**
 * extracted from login.php
 * this needs to be replaced..
 */
function use_dst( $user_id, $timezone ){
    # 2013-11-03 dst is offforawhile
    return FALSE; 

    global $db;
    $sql  = "
        SELECT COUNT(*) AS cnt
          FROM schools s, user_schools us
         WHERE s.school_id = us.school_id
           and us.user_id  = $1
           AND s.state     in  ('AZ', 'HI')
        ";
    $resaz = pg_query_params($db, $sql, array( $user_id ));
    $no_dst = (pg_fetch_result($resaz,0,0)>0);

    # HI Doesn't observe DST
    # Neither do our AZ users.  We really need better TZ/DST handling.
    if ( $timezone != '' && ! $no_dst ) {
        return TRUE;
    }

    return FALSE;
}

/**
 * set http status to $code, default 403 ( access denied )
 * stop execution of script and send what ever is
 * in slim's buffer ( others headers etc )
 *
 */
function deny( $msg ='', $code = 403 ){

    $slim = Slim::getInstance();
    $slim->response()->status( $code );

    if ( $slim->request()->isAjax() ){
        // do nothing just send response code
    }else{
        message( $msg );
    }
    error_log ("denied! $msg $code");
    $slim->stop();
}

/**
 * redirect request back to referer
 */
function return_to_sender( $msg ='' ){
    $slim  = Slim::getInstance();
    $refer = $slim->request()->getReferer();
    $refer = preg_replace( '/\?.*$/', '', $refer );
    $url   = $refer . '?msg=' . urlencode( $msg );
    $slim->redirect( $url );
    $slim->stop();
}
/**
 * POST /users/register
 * attempt to register $token to student
 * logs in user and establishes session
 *
 * requires:
 * email:  usersname / email
 * pw:     pw
 * token:  book registration code or course code
 *
 * responses:
 * 200: success!
 * 401: invalid token
 * 402: from login: user doesn't have access to book
 *        ( potentially, uses a1 book creates acct w/ a2 course code etc. )
 * 403: invalid username / pass
 * 410: no remaining seats
 * 412: course code, can not be used by teacher+
 */
function register(){

    $slim = Slim::getInstance();
    $input = get_input_array();

	$wrap_output = isset($input['wrap_output'])? $input['wrap_output'] : false;
    // if email is set or session is not logged in
    // very user name and password before proceeding
    if ( isset( $input['email'] ) || ! $_SESSION['is_logged_in'] ){

        $auth  = new Authenticator( $input['email'], $input['pw'] );

        //auth user name and password
        if ( ! $auth->authenticate() ){
			if($wrap_output){
				not_allowed("Invalid email or password", "Warning", "INVALID_INPUT_VALUES");
			}else 
				deny( '02: Invalid email or password.' );
            //deny stops execution
        }
    }

    $book  = Book::get_active();
    $fact  = new RegistrationCodeFactory();
    $token = $fact->make_registration_object( $input['token'], $book->book_id );

    if ( ! $token->is_valid() ){
		if($wrap_output){
			not_authorized("Invalid token", "Warning", "INVALID_INPUT_VALUES");		
		}else 
        	deny('invalid token', 401 );
    }

    if( ! $token->has_seats() ){
		if($wrap_output){
			custom_error("Token has no seats available.", "Warning", "NO_SEATS_AVAILABLE", 410);		
		}else 
        	deny( 'token has no seats available', 410 );
    }

    $user = User::fetch_by_email( $input['email'] );

    if ( is_null ( $user ) ){
		if($wrap_output){
			not_found("Unable to find the user.", "Warning", "USER_NOT_FOUND");		
		}else 
        	deny( 'unable to find user' );
    }

    if ( $token->is_student_only() && ! $user->is_student() ){
		if($wrap_output){
			custom_error("This code can only be used by students.", "Warning", "WRONG_USER_LEVEL", 412);		
		}else 
        	deny( 'this code can only be used by students', 412 );
    }

	SessionHandler::restart();
	$_SESSION['is_logged_in'] = TRUE;
	$user->populate_session();
	SessionHandler::update_last_activity();
	$token->register( $user );
	SessionHandler::destroy();
	SessionHandler::start();

    $output_status = FALSE;
    login( $output_status );

    $msg = json_encode( array_merge(
        array('msgs' => $token->get_response() ),
        $user->status() ) )  ;
	
	if($wrap_output){
		wrap_output($msg);
	}else 
    	echo $msg;
}
