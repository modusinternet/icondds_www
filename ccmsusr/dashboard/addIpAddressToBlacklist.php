<?php
header("Content-Type:text/html; charset=UTF-8");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if($_SERVER["SCRIPT_NAME"] != "/ccmsusr/index.php") {
	echo "This script can NOT be called directly.";
	exit;
}

$msg = array();
$privArray = json_decode($_SESSION["PRIV"], true);
//echo $privArray["admin"]["rw"];
//echo $privArray["admin"]["sub"]["blacklist_settings"];
//exit;

if(ccms_badIPCheck($_SERVER["REMOTE_ADDR"])) {
	$msg["error"] = "There is a problem with your login, your IP Address is currently being blocked.  Please contact the website administrators directly if you feel this message is in error.";

} elseif($_SESSION["SUPER"] != 1) {
	if($privArray["admin"]["rw"] != 1 || $privArray["admin"]["sub"]["blacklist_settings"] != 2) {
		$msg["error"] = "Blacklist cancelled, you do not have 'Write' privlages.  Double check your privlages and or contact your website administrators directly if you feel this message is in error.(2)";
	}

} elseif($CLEAN["ip"] == "") {
	$msg["error"] = "No IP provided.";

} elseif($CLEAN["ip"] == "MINLEN") {
	$msg["error"] = "This field must be between 7 to 15 characters";

} elseif($CLEAN["ip"] == "MAXLEN") {
	$msg["error"] = "This field must be between 7 to 15 characters";

} elseif($CLEAN["ip"] == "INVAL") {
	$msg["error"] = "'Name' field contains invalid characters.  ( > < & # )  You have used characters in this field which are either not supported by this field or we do not permitted on this system.";
}

if(!isset($msg["error"])) {
	// no problems
	//$qry = $CFG["DBH"]->prepare("DELETE FROM `ccms_log` WHERE `id` = :id LIMIT 1;");
	$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_blacklist` WHERE `id` = 1;");
	$qry->execute();
	$row = $qry->fetch(PDO::FETCH_ASSOC);

	if(isset($row["data"])) {
		if(strstr($row["data"], $CLEAN["ip"])) {
			//$msg["success"] = "0";
			$msg["success"] = $CLEAN["ip"] . " Already Blocked";
		} else {
			$qry = $CFG["DBH"]->prepare("UPDATE `ccms_blacklist` SET `data` = :data WHERE `id` = 1;");
			$qry->execute(array(':data' => $row["data"] . "|" . $CLEAN["ip"]));
			//$msg["success"] = "1";
			$msg["success"] = $CLEAN["ip"] . " Blocked";
		}
	} else {
		$msg["error"] = "Record no. 1, of the ccms_blacklist table, does not appear to have a 'data' column.  Please contact your website administrator.";
	}
}

echo json_encode($msg);
