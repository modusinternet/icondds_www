<?php
// benchmark start
//$time_start = microtime(true);

// Use the ini_set function to set value of the include_path option on your server if necessary.
// e.g.: ini_set('include_path', 'ccmslib:ccmspre:ccmstpl:ccmsusr' . ini_get('include_path'));
//ini_set('include_path', $CFG["LIBDIR"] . ':' . $CFG["PREDIR"] . ':' . $CFG["TPLDIR"] . ':' . $CFG["USRDIR"] . ':' . ini_get('include_path'));

$CFG = array();
$CLEAN = array();

$CFG["VERSION"] = "0.7.7";
$CFG["RELEASE_DATE"] = "Mar 31, 2022";

// Necessary to solve a problem on GoDaddy servers when running sites found in sub folders of existing sites.
if(isset($_SERVER["REAL_DOCUMENT_ROOT"])) {
	$_SERVER["DOCUMENT_ROOT"] = $_SERVER["REAL_DOCUMENT_ROOT"];
}

if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/ccms-setup.php")) {
	require_once($_SERVER["DOCUMENT_ROOT"] . "/ccms-setup.php");
	exit;
}

require_once $_SERVER["DOCUMENT_ROOT"] . "/ccmspre/config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/ccmspre/index.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/ccmspre/whitelist_user.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/ccmslib/_default.php";

ob_start("ob_gzhandler");

$CFG["TPLDIR"] = $CFG["USRDIR"];
$CFG["INDEX"] = $CFG["USRINDEX"];

// This creats a persistent connection to MySQL.
CCMS_DB_First_Connect();

CCMS_Filter($_SERVER + $_REQUEST, $ccms_whitelist);

CCMS_User_Filter($_SERVER + $_REQUEST, $whitelist);


// Security check, is the user on the blacklist?
if(ccms_badIPCheck($_SERVER["REMOTE_ADDR"])) {
	// log out
	$_SESSION = array();
	$_SESSION['EXPIRED'] = "1";
	//header("Location: /" . $CFG["DEFAULT_SITE_CHAR_SET"] . "/user/");

	if($CLEAN["ajax_flag"] == 1) {
		// If this call contains an Ajax flag set to '1' we don't actually want to send them to the login page, we'll just send a session expired message instead.

		header("Content-Type: application/javascript; charset=UTF-8");
		// NOTE: If the template is later called using a serviceWorker be aware that will not respect the settings of the 'cache-control' header as noted in here: https://web.dev/service-workers-cache-storage/#api-nuts-and-bolts

		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		//echo "/* Session Error */";
		echo '{"error":"Session Error"}';
		exit;
	} else {
		// Show login template because they are NOT logged in.
		$CLEAN["ccms_tpl"] = "/login.php";
	}
}


CCMS_Set_SESSION();

//if(isset($_SESSION["FAIL"]) >= 5) {
if(($_SESSION["FAIL"] ?? null) >= 5) {
	// If the users session record indicates that they have attempted to login 5 or more times and failed; do not show this page at all.  Simply redirect them base to the homepage for this site immediatly.

	header("Location: /");
	exit;
}

if(!isset($_SESSION["USER_ID"]) || isset($_POST["ccms_login"]) || isset($_REQUEST["ccms_logout"]) || isset($_POST["ccms_pass_reset_part_1"]) || isset($_POST["ccms_pass_reset_part_2"])) {
	if($CLEAN["ajax_flag"] == 1) {
		// if this call contains an Ajax flag set to 1 we don't actually want to send them to the login page, we'll just send a session expired message instead.
			header("Content-Type: application/javascript; charset=UTF-8");
			header("Expires: on, 01 Jan 1970 00:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");

			echo '{"error":"Session Error"}';
			exit;
	} else {
			// Show login template because they are NOT logged in.
			$CLEAN["ccms_tpl"] = "/login.php";
	}
}

// If there is no template requested, show $CFG["INDEX"].
// This code is used when accessing the /user/ templates, before login credentials have between
// verified and when dealing with URL's that resemble:
// $CLEAN["INDEX"] === BLANK
// /
// Make into:
// /index.html
// /index.html
if(!isset($CLEAN["ccms_tpl"]) || $CLEAN["ccms_tpl"] === "" || $CLEAN["ccms_tpl"] === "/") {
	$CLEAN["ccms_tpl"] = "/dashboard/";
}

// If the template being requested is inside a dir and no specific template name is
// part of that request, add index to the end.
// /fruit/
// /fruit/orange/
// /fruit/orange/vitamin/
// Make into:
// /fruit/index
// /fruit/orange/index
// /fruit/orange/vitamin/index
if(preg_match("/[\/]\z/", $CLEAN["ccms_tpl"])) {
	$CLEAN["ccms_tpl"] .= "index.php";
}

CCMS_Main();

// benchmark end
//echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);
