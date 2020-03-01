<?php
// benchmark start
//$time_start = microtime(true);

// Use the ini_set function to set value of the include_path option on your server if necessary.
// e.g.: ini_set('include_path', 'ccmslib:ccmspre:ccmstpl:ccmsusr' . ini_get('include_path'));
//ini_set('include_path', $CFG["LIBDIR"] . ':' . $CFG["PREDIR"] . ':' . $CFG["TPLDIR"] . ':' . $CFG["USRDIR"] . ':' . ini_get('include_path'));

$CFG = array();
$CLEAN = array();

if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/ccms-setup.php")) {
    require_once($_SERVER["DOCUMENT_ROOT"] . "/index.php");
    die();
}

require_once "../ccmspre/config.php";

require_once "../" . $CFG["PREDIR"] . '/index.php';
require_once "../" . $CFG["PREDIR"] . '/whitelist_user.php';
require_once "../" . $CFG["LIBDIR"] . '/_default.php';

$CFG["TPLDIR"] = $CFG["USRDIR"];
$CFG["INDEX"] = $CFG["USRINDEX"];

// This creats a persistent connection to MySQL.
CCMS_DB_First_Connect();

CCMS_Filter($_SERVER + $_REQUEST, $ccms_whitelist);

CCMS_User_Filter($_SERVER + $_REQUEST, $whitelist);

CCMS_cookie_SESSION();

// Necessary to solve a problem on GoDaddy servers when running sites found in sub folders of existing sites.
if($_SERVER["REAL_DOCUMENT_ROOT"]) {
    $_SERVER["DOCUMENT_ROOT"] = $_SERVER["REAL_DOCUMENT_ROOT"];
}

ob_start("ob_gzhandler");

if($CLEAN["logout"] == "1" || $CLEAN["login"] == "1") {
    $CLEAN["ccms_tpl"] = "login";
} else {
    // Double check that the user is even allowed to be logged in still.
    // Admin might have cleared all sessions or even marked the user status to 0.
    $qry = $CFG["DBH"]->prepare("SELECT b.id, b.priv FROM `ccms_session` AS a INNER JOIN `ccms_user` AS b ON b.id = a.user_id WHERE a.code = :code AND a.ip = :ip AND b.status = '1' LIMIT 1;");
    $qry->execute($data = array(':code' => $CLEAN["SESSION"]["code"], ':ip' => $_SERVER["REMOTE_ADDR"]));
    $row = $qry->fetch(PDO::FETCH_ASSOC);
    if(!$row) {
        if ($CLEAN["ajax_flag"] == 1) { // if this call contains an Ajax flag set to 1 we don't actually want to send them to the login page, we'll just send a session expired message instead.
            header("Content-Type: application/javascript; charset=UTF-8");
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            echo "/* Session Error */";
            die();
        } else {
            // Show login template because they are NOT logged in.
            $CLEAN["ccms_tpl"] = "login";
        }
    } else {
        $CLEAN["SESSION"]["priv"] = $row["priv"];
    }
}

CCMS_Main();

// benchmark end
//echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);
