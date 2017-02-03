<?php
include_once "includes/create_school_config.php";
include_once "includes/create_school_class.php";

///////////////////////////////////////////////////////////////////////////////
// init
$error_msg = "";
$success_msg = "";
$success = true;
$search_results = array();
$add_array = array();
///////////////////////////////////////////////////////////////////////////////

$createSchool = new createSchool();

if (isset($_REQUEST['school_search_name']) && $_REQUEST['school_search_name'] > "") 
{
    $search_data_array["name"]  = $_REQUEST['school_search_name'];
    $search_data_array["city"]  = $_REQUEST['school_search_city'];
    $search_data_array["state"] = $_REQUEST['school_search_state'];
    $search_data_array["zip"]   = $_REQUEST['school_search_zip'];
            
    $search_results = $createSchool->search_oscommerce(DB_OSCOMMERCE_LIVE, $config, $search_data_array);
}

if (isset($_REQUEST['add_name']) && $_REQUEST['add_name'] > "")
{
    $pid = $createSchool->get_pid(DB_OSCOMMERCE_LIVE, $config);
    $pid++;
    
    if ($pid) 
    {
        $success_msg .= TEXT_NEW_PID . $pid . "<br />";
        
        $add_array["pid"]     = $pid;
        $add_array["name"]    = trim($_REQUEST['add_name']);
        $add_array["address"] = trim($_REQUEST['add_address']);
        $add_array["city"]    = trim($_REQUEST['add_city']);
        $add_array["state"]   = strtoupper(trim($_REQUEST['add_state']));
        $add_array["zip"]     = strtoupper(trim($_REQUEST['add_zip']));
        $add_array["country"] = strtoupper(trim($_REQUEST['add_country']));
        
        if ($add_array["country"] == "")
        {
            $add_array["country"] = TEXT_US;
        }
        
        if (strlen($add_array["state"]) > STATEMAXLENGTH)
        {
            $error_msg .= TEXT_STATE_EXEEDING . STATEMAXLENGTH . ".<br />";
            $success = false;
            goto end;            
        }

        if (strlen($add_array["zip"]) > ZIPMAXLENGTH)
        {
            $error_msg .= TEXT_ZIP_EXEEDING . ZIPMAXLENGTH . ".<br />";
            $success = false;
            goto end;            
        }        
        
        if ($add_array["name"] > "" && $add_array["address"] > "" && $add_array["city"] > "" && $add_array["state"] > "" && $add_array["zip"] > "") 
        {       
            $success = $createSchool->check_oscommerce(DB_OSCOMMERCE_TEST, $config, $add_array);
            if ($success)
            {
                $success_msg .= DB_OSCOMMERCE_TEST . " checked.<br />";
            }
            else
            {
                $error_msg .= $createSchool->error_msg;
                goto end;
            }
            
            $success = $createSchool->check_oscommerce(DB_OSCOMMERCE_LIVE, $config, $add_array);
            if ($success)
            {
                $success_msg .= DB_OSCOMMERCE_LIVE . " checked.<br />";
            }
            else
            {
                $error_msg .= $createSchool->error_msg;
                goto end;
            }
            
            $success = $createSchool->check_magento(DB_MAGENTO_TEST, $config, $add_array);
            if ($success)
            {
                $success_msg .= DB_MAGENTO_TEST . " checked.<br />";
            }
            else
            {
                $error_msg .= $createSchool->error_msg;
                goto end;
            }

            $success = $createSchool->check_magento(DB_MAGENTO_LIVE, $config, $add_array);
            if ($success)
            {
                $success_msg .= DB_MAGENTO_LIVE . " checked.<br />";
            }
            else
            {
                $error_msg .= $createSchool->error_msg;
                goto end;
            }

            $success = $createSchool->check_kb(DB_KB_TEST, $config, $add_array);
            if ($success)
            {
                $success_msg .= DB_KB_TEST . " checked.<br />";
            }
            else
            {
                $error_msg .= $createSchool->error_msg;
                goto end;
            }

            $success = $createSchool->check_kb(DB_KB_LIVE, $config, $add_array);
            if ($success)
            {
                $success_msg .= DB_KB_LIVE . " checked.<br />";
            }
            else
            {
                $error_msg .= $createSchool->error_msg;
                goto end;
            }

            if ($error_msg == "")
            {
                $success = $createSchool->insert_oscommerce(DB_OSCOMMERCE_TEST, $config, $add_array);
                $success = $createSchool->insert_oscommerce(DB_OSCOMMERCE_LIVE, $config, $add_array);
                $success = $createSchool->insert_magento(DB_MAGENTO_TEST, $config, $add_array);
                $success = $createSchool->insert_magento(DB_MAGENTO_LIVE, $config, $add_array);
                $success = $createSchool->insert_kb(DB_KB_TEST, $config, $add_array);
                $success = $createSchool->insert_kb(DB_KB_LIVE, $config, $add_array);
            }        
        }
        else 
        {
            $error_msg .= TEXT_ERROR_MISSING_PARAM;
            $success = false;
            goto end;   
        }
    }
    else 
    {
        $error_msg .= TEXT_ERROR_GET_PID;
        $success = false;
        goto end;
    }    
}

$error_msg .= $createSchool->error_msg;
$success_msg .= $createSchool->success_msg;
unset($createSchool);

///////////////////////////////////////////////////////////////////////////////
end:
if (!$success)
{    
    $error_msg .= TEXT_ERROR_SOMETHING;
    $success_msg = "";
}
///////////////////////////////////////////////////////////////////////////////

require_once('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CREATE_ORDER_PROCESS);

?>	
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TEXT_HEADING_TITLE ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr>
		<td width="<?php echo BOX_WIDTH; ?>" valign="top">
			<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2" class="columnLeft">
				<!-- left_navigation //-->
				<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
				<!-- left_navigation_eof //-->
			</table>
		</td>
		<td valign="top">
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="pageHeading"><?php echo TEXT_HEADING_TITLE ?></td>
				</tr>
			</table>	
			<p class="main">Search thoroughly before attempting to add. I suggest to search by entering only the STATE and minimal keywords in the NAME.</p>
			<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post">	
			<table border="0" cellspacing="1" cellpadding="2" class="infoBox">
				<tr>
					<td class="main">Name:</td>
					<td class="main"><?php echo tep_draw_input_field('school_search_name', '', $address['school_search_name']); ?> (Examples) "ele sch" -> ABC Elementary School. "wa col" -> Walla Walla Community College.</td>
				</tr>
				<tr>
					<td class="main">City:</td>
					<td class="main"><?php echo tep_draw_input_field('school_search_city', '', $address['school_search_city']); ?></td>
				</tr>
				<tr>
					<td class="main">State:</td>
					<td class="main"><?php echo tep_draw_input_field('school_search_state', '', $address['school_search_state']); ?></td>
				</tr>
				<tr>
					<td class="main">Zip:</td>
					<td class="main"><?php echo tep_draw_input_field('school_search_zip', '', $address['school_search_zip']); ?></td>
				</tr>
				<tr>
					<td class="main">&nbsp;</td>
					<td class="main"><input type="submit" value="Search" /></td>
				</tr>
			</table>	
			</form>
<?php
if (isset($_REQUEST['school_search_name'])) {
?>
			<p class="main">Results: <?php echo count($search_results)?> found</p>
<?php
	if (count($search_results) > 0) {
?>
			<table border="0" cellspacing="0" cellpadding="3" class="infoBox">
				<tr>
					<td class="main dataTableHeadingRow">Name</td>
					<td class="main dataTableHeadingRow">Address</td>
					<td class="main dataTableHeadingRow">City</td>
					<td class="main dataTableHeadingRow">State</td>
					<td class="main dataTableHeadingRow">Zip</td>
					<td class="main dataTableHeadingRow">Country</td>
					<td class="main dataTableHeadingRow">PID</td>
				</tr>
<?php
		for ($i = 0; $i < count($search_results); $i++) {
?>
				<tr>
					<td class="main"><?php echo $search_results[$i]["name"]?></td>
					<td class="main"><?php echo $search_results[$i]["address"]?></td>
					<td class="main"><?php echo $search_results[$i]["city"]?></td>
					<td class="main"><?php echo $search_results[$i]["state"]?></td>
					<td class="main"><?php echo $search_results[$i]["zip"]?></td>
					<td class="main"><?php echo $search_results[$i]["country"]?></td>
					<td class="main"><?php echo $search_results[$i]["pid"]?></td>
				</tr>			
<?php
		}
?>			
			</table>	
<?php
	}
}
?>
			<br /><br />
			<p class="messageStackError"><?php echo $error_msg?></p>
			<p class="messageStackSuccess"><?php echo $success_msg?></p>
			<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post">	
			<table border="0" cellspacing="1" cellpadding="2" class="infoBox">
				<tr>
					<td class="main">Name:</td>
					<td class="main"><?php echo tep_draw_input_field('add_name', '', ''); ?></td>
				</tr>
				<tr>
					<td class="main">Address:</td>
					<td class="main"><?php echo tep_draw_input_field('add_address', '', ''); ?></td>
				</tr>
				<tr>
					<td class="main">City:</td>
					<td class="main"><?php echo tep_draw_input_field('add_city', '', ''); ?></td>
				</tr>
				<tr>
					<td class="main">State:</td>
					<td class="main"><?php echo tep_draw_input_field('add_state', '', 'maxlength="2"'); ?></td>
				</tr>
				<tr>
					<td class="main">Zip:</td>
					<td class="main"><?php echo tep_draw_input_field('add_zip', '', 'maxlength="' . ZIPMAXLENGTH . '"'); ?></td>
				</tr>
				<tr>
					<td class="main">Country:</td>
					<td class="main"><?php echo tep_draw_input_field('add_country', '', 'maxlength="' . STATEMAXLENGTH . '"'); ?></td>
				</tr>
				<tr>
					<td class="main">&nbsp;</td>
					<td class="main"><input type="submit" value="Add" />&nbsp;<input type="reset" /></td>
				</tr>
			</table>	
			</form>	
		</td>
	</tr>
</table>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
