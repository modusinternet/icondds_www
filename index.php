<?php
// benchmark start
//$time_start = microtime(true);

// Use the ini_set function to set value of the include_path option on your server if necessary.
// e.g.: ini_set('include_path', 'ccmslib:ccmspre:ccmstpl:ccmsusr' . ini_get('include_path'));
//ini_set('include_path', $CFG["LIBDIR"] . ':' . $CFG["PREDIR"] . ':' . $CFG["TPLDIR"] . ':' . $CFG["USRDIR"] . ':' . ini_get('include_path'));

$CFG = array();
$CLEAN = array();

$CFG["VERSION"] = "0.6.8";
$CFG["RELEASE_DATE"] = "Nov 11, 2019";

// Necessary to solve a problem on GoDaddy servers when running sites found in sub folders of existing sites.
if($_SERVER["REAL_DOCUMENT_ROOT"]) {
	$_SERVER["DOCUMENT_ROOT"] = $_SERVER["REAL_DOCUMENT_ROOT"];
}

if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/ccms-setup.php")) {
	require_once($_SERVER["DOCUMENT_ROOT"] . "/ccms-setup.php");
	die();
}

require_once "ccmspre/config.php";

ob_start("ob_gzhandler");

require_once $CFG["PREDIR"] . '/whitelist_public.php';
require_once $CFG["PREDIR"] . '/index.php';

// This creats a persistent connection to MySQL.
CCMS_DB_First_Connect();

CCMS_Filter($_SERVER + $_REQUEST, $ccms_whitelist);

CCMS_Public_Filter($_SERVER + $_REQUEST, $whitelist);

CCMS_Main();

// benchmark end
//echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);
