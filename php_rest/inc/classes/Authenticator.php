<?php 
/**
* a class to authenticate a user by checking user name and email 
* against email and hashed password
*
* usage : 
*  $email    : user supplied email
*  $password : user supplided password
*
*  Use: 
*  $auth = new Authenticator( $email, $password ); 
*  $auth->authenticate(); # returns bool 
*
**/
class Authenticator{
    protected $db, 
              $email, 
              $password, 
              $authenticated
              ; 
    function __construct( $email, $password ){
        global $db; 

        $this->authenticated = FALSE; 

        $this->email    = $this->_clean_email( $email ) ; 
        $this->password = $password; 
        $this->db       = $db; 
    }
    function _clean_email( $e ){
        return strtolower( trim ( $e ) );
    }
    /**
     *
     */
    function authenticate(){

        $sql  = "SELECT user_id, password, hwpw 
                   FROM users u
                  WHERE email = $1 ";
        
        $res = pg_query_params( $this->db, $sql, array( $this->email) );

        if ( pg_num_rows( $res ) !== 1 ){
            return $this->_deny( 'email not found' ); 
        }

        $userinfo = pg_fetch_assoc( $res );

        # still two types of password, 
        # password, and hwpw left over from 
        # before merging online homework and webaccess 
        # dbs. we want to allow a user to login with either of these
        foreach ( array( 'password', 'hwpw' ) as $pw_field  ){
            if( $this->_verify_pass( $this->password, $userinfo[ $pw_field ] ) ){
                return $this->_allow("$pw_field match");                                
            }
        }
        
        return $this->_deny( 'invalid password' );
    }
    /** 
     * check user supplied password against salted? hash
     */
    function _verify_pass( $user_pass , $hash ){
        $salt = ''; # still potentially unsalted passwords in db   
        
        # password salting was implemented after the sytem had been 
        # in use for a few years, old passwrds are NOT salted, just 
        # hashed w/ md5 all new passwords are salted and stored as
        # "HASH:SALT" 
        if ( preg_match("/(\w+):(\w+)/",$hash,$m ) ) { 
            $hash = $m[1]; 
            $salt = $m[2]; 
        }
        # old login login trims whitespace from users password
        # and allows access if either the password or trim'd pw 
        # matches the hash
        # keeping for compatibility,
        # if i user password is 'password', 'password', or '  password  ' 
        # would allow them in if its ' password' only ' password' would work
        $trim_pass  = trim ( $user_pass );

        return ( $hash == md5( $salt.$user_pass ) || 
                 $hash == md5( $salt.$trim_pass ) ) ;
    }
    # set this->authenticate to FALSE and return it
    function _deny( $msg = '' ){
        #error_log( "DENY: $msg" );
        $this->authenticated = FALSE; 
        return $this->authenticated; 
    }
    # set this->authenticate to TRUE and return it
    function _allow( $msg = '' ){
        #error_log( "ALLOW: $msg" );
        $this->authenticated = TRUE; 
        return $this->authenticated; 
    }
    /**
     * returns a salted hash of $password using current hashing scheme 
     * md5 + 2 char salt
     */
    static function hash_password( $password ){
        $salt = Authenticator::get_salt();
        $hashed = md5( $salt . $password ) . ":" . $salt;
        return $hashed; 
    }
    /**
     * return a random string of $len aLphAnumeric characters
     */
    static function get_salt( $len = 2 ){
        $salt =''; 
        // construct an alphabet of [a-zA-Z0-9]
        $alph = array_merge( range( 'a', 'z' ), 
                             range( 'A', 'Z' ),
                             range(  0 ,  9 ) );
        $alph_len = count( $alph ) - 1;

        for( $i=0; $i < $len; $i++ ){
            $salt .= $alph[ mt_rand( 0, $alph_len ) ] ;
        }
        return $salt; 
    }
}
