<?php 
//automatically load classes from this directory *as needed*
spl_autoload_register('inc_class_autoloader');
function inc_class_autoloader ( $class ){
    # the "inc" folder, which is the parent of
    # the directory containing this file
    $inc = dirname( dirname( __FILE__ ) ) ; 

    $class_dir = "$inc/classes/"; 
	$pest_dir = "$inc/pest/";

    $file = $class_dir . $class . ".php";
   	$pest_file = $pest_dir . $class . ".php";
   	$inc_file = "$inc/$class" . ".class.php";
	// Check first in inc/classes directory.
    if ( file_exists( $file ) ){
        require $file;
    }
	// Next, in inc/pest directory.
	elseif (file_exists($pest_file)) {
        require $pest_file; 
    }
	// Last, in inc directory. Here, files are expected to end in .class.php.
	elseif (file_exists($inc_file)) {
		require $inc_file;
	}
}
