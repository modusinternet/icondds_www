<?php
header("Content-Type: text/html; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once $_SERVER["DOCUMENT_ROOT"] . "/ccmspre/config.php";
if (!strstr($_SERVER["HTTP_REFERER"], $CFG["DOMAIN"])) {
    exit('No direct script access allowed');
}

if ($CLEAN["password"] == "") {
    $error_message = "'Password' field missing content.";
} elseif ($CLEAN["password"] == "MINLEN") {
    $error_message = "'Password' field is too short, must be 8 or more characters in length.";
} elseif ($CLEAN["password"] == "INVAL") {
    $error_message = "'Password' field error, indeterminate.";

} elseif ($CLEAN["password1"] == "") {
    $error_message = "'New Password' field missing content.";
} elseif ($CLEAN["password1"] == "MINLEN") {
    $error_message = "'New Password' field is too short, must be 8 or more characters in length.";
} elseif ($CLEAN["password1"] == "INVAL") {
    $error_message = "Something is wrong with your 'New Password', it came up as INVALID when testing is with with an open (.+) expression.";

} elseif ($CLEAN["password2"] == "") {
    $error_message = "'Repeat New Password' field missing content.";
} elseif ($CLEAN["password2"] == "MINLEN") {
    $error_message = "'Repeat New Password' field is too short, must be 8 or more characters in length.";
} elseif ($CLEAN["password2"] == "INVAL") {
    $error_message = "Something is wrong with your 'Repeat New Password', it came up as INVALID when testing is with with an open (.+) expression.";

} elseif ($CLEAN["password1"] != $CLEAN["password2"]) {
    $error_message = "'New Password' and 'Repeat New Password' fields are not the same.";
}

if (!$error_message) {
    $qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_user` WHERE `id` = :user_id LIMIT 1;");
    $qry->execute(array(':user_id' => $CLEAN["SESSION"]["user_id"]));
    $row = $qry->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        if ($row["hash"] == crypt($CLEAN["password"], $row["hash"])) {
            // The submitted password matches the hashed password stored on the server.

            // Rehash the password and replace original password hash on the server to make even more secure.
            // See https://alias.io/2010/01/store-passwords-safely-with-php-and-mysql/ for more details.
            $cost = 10;
            $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
            $salt = sprintf("$2a$%02d$", $cost) . $salt;
            $hash = crypt($CLEAN["password1"], $salt);
            $qry = $CFG["DBH"]->prepare("UPDATE `ccms_user` SET `hash` = :hash WHERE `id` = :user_id;");
            $qry->execute(array(':hash' => $hash, ':user_id' => $CLEAN["SESSION"]["user_id"]));
            echo "1";
        } else {
            echo '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true" style="margin-right: 10px;"></span>'."Password failed, please try again.";
        }
    } else {
        echo '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true" style="margin-right: 10px;"></span>'."Password update failed, your account is not found on the server anymore.";
    }
} else {
    echo '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true" style="margin-right: 10px;"></span>'.$error_message;
}
