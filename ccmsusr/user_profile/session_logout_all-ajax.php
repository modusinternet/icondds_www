<?php
header("Content-Type: text/html; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once $_SERVER["DOCUMENT_ROOT"] . "/ccmspre/config.php";
if (!strstr($_SERVER["HTTP_REFERER"], $CFG["DOMAIN"])) {
    exit('No direct script access allowed');
}

$qry = $CFG["DBH"]->prepare("DELETE FROM `ccms_session` WHERE `user_id` = :user_id AND `code` != :code;");
$qry->execute(array(':user_id' => $CLEAN["SESSION"]["user_id"], ':code' => $CLEAN["SESSION"]["code"]));

echo "1";
