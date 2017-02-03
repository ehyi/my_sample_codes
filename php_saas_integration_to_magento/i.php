<?php
require_once "includes/class.php";

$pathinfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $_SERVER['REDIRECT_URL'];
$urlparams = preg_split('|/|', $pathinfo, -1, PREG_SPLIT_NO_EMPTY);
switch ($urlparams[0]) {
    case "school_by_zip":
        echo School::school_by_zip();
        break;
    case "school_selected":
        echo School::school_selected();
        break;
    default:
        break;
}
exit;
?>