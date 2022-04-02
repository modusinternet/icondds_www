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

if(ccms_badIPCheck($_SERVER["REMOTE_ADDR"])) {
	$msg["error"] = "There is a problem with your login, your IP Address is currently being blocked.  Please contact the website administrators directly if you feel this message is in error.";

} elseif($CLEAN["ccms_login_password"] == "") {
	$msg["error"] = "'Password' field missing content.";
} elseif($CLEAN["ccms_login_password"] == "MINLEN") {
	$msg["error"] = "'Password' field is too short, must be 8 or more characters in length.";
} elseif($CLEAN["ccms_login_password"] == "INVAL") {
	$msg["error"] = "'Password' field error, indeterminate.";

} elseif($_REQUEST["ccms_pass_reset_part_2_pass_1"] !== "" || $_REQUEST["ccms_pass_reset_part_2_pass_2"] !== "") {
	if($CLEAN["ccms_pass_reset_part_2_pass_1"] == "MINLEN") {
		$msg["error"] = "'New Password' field is too short, must be 8 or more characters in length.";
	} elseif($CLEAN["ccms_pass_reset_part_2_pass_1"] == "INVAL") {
		$msg["error"] = "Something is wrong with your 'New Password', it came up as INVALID when testing is with with an open (.+) expression.";
	} elseif($CLEAN["ccms_pass_reset_part_2_pass_2"] == "MINLEN") {
		$msg["error"] = "The 'Retype' Password field is too short, must be 8 or more characters in length.";
	} elseif($CLEAN["ccms_pass_reset_part_2_pass_2"] == "INVAL") {
		$msg["error"] = "Something is wrong with the 'Retype' Password, it came up as INVALID when testing is with with an open (.+) expression.";
	}	elseif($CLEAN["ccms_pass_reset_part_2_pass_1"] !== $CLEAN["ccms_pass_reset_part_2_pass_2"]) {
		$msg["error"] = "'New Password' and the 'Retype' Password fields are not the same.";
	}

} elseif($CLEAN["2fa_radio"] == "") {
	$msg["error"] = "No 2FA option selected.";
} elseif($CLEAN["2fa_radio"] == "MINLEN") {
	$msg["error"] = "'2FA' variable too short, must be 1 or more characters in length.";
} elseif($CLEAN["2fa_radio"] == "INVAL") {
	$msg["error"] = "'2FA' variable contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif($CLEAN["2fa_radio"] === "2" && $CLEAN["2fa_secret"] === "") {
	$msg["error"] = "Problem reading '2FA secret', not found.";
} elseif($CLEAN["2fa_radio"] === "2" && $CLEAN["2fa_secret"] === "MINLEN") {
	$msg["error"] = "'2FA secret' too short, must be 16 or more characters in length.";
	} elseif($CLEAN["2fa_radio"] === "2" && $CLEAN["2fa_secret"] === "INVAL") {
	$msg["error"] = "'2FA secret' variable contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";
}

if(!isset($msg["error"])) {
	$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_user` WHERE `id` = :user_id LIMIT 1;");
	$qry->execute(array(':user_id' => $_SESSION["USER_ID"]));
	$row = $qry->fetch(PDO::FETCH_ASSOC);

	if($row) {
		if(password_verify($CLEAN["ccms_login_password"], $row["hash"])) {

			if($_REQUEST["ccms_pass_reset_part_2_pass_1"] !== "" || $_REQUEST["ccms_pass_reset_part_2_pass_2"] !== "") {
				if($CLEAN["ccms_pass_reset_part_2_pass_1"] !== ""){
					// The submitted password matches the hashed password stored on the server.
					// Rehash the password and replace original password hash on the server to make even more secure.
					// See https://alias.io/2010/01/store-passwords-safely-with-php-and-mysql/ for more details.
					$options = ['cost' => 10];
					$hash = password_hash($CLEAN["ccms_pass_reset_part_2_pass_1"], PASSWORD_BCRYPT, $options);
				}
			}

			if(isset($hash)){
				// Password is being changed
				if($CLEAN["2fa_radio"] === "0") {
					// 2fa is set to 'enabled' on the form so don't change what's already in the database.
					$qry = $CFG["DBH"]->prepare("UPDATE `ccms_user` SET `hash` = :hash WHERE `id` = :id;");
					$qry->execute(array(':hash' => $hash, ':id' => $_SESSION["USER_ID"]));
				} elseif($CLEAN["2fa_radio"] === "1") {
					// 2fa is set to 'disabled' on the form so remove it from the database.
					$qry = $CFG["DBH"]->prepare("UPDATE `ccms_user` SET `hash` = :hash, `2fa_secret` = '' WHERE `id` = :id;");
					$qry->execute(array(':hash' => $hash, ':id' => $_SESSION["USER_ID"]));
				} elseif($CLEAN["2fa_radio"] === "2"){
					// 2fa is set to 'Generate new' on the form so update it in the database.
					$qry = $CFG["DBH"]->prepare("UPDATE `ccms_user` SET `hash` = :hash, `2fa_secret` = :2fa_secret WHERE `id` = :id;");
					$qry->execute(array(':hash' => $hash, ':2fa_secret' => $CLEAN["2fa_secret"], ':id' => $_SESSION["USER_ID"]));
				}
			} else {
				// Password is NOT being changed
				if($CLEAN["2fa_radio"] === "1") {
					// 2fa is set to 'disabled' on the form so remove it from the database.
					$qry = $CFG["DBH"]->prepare("UPDATE `ccms_user` SET `2fa_secret` = '' WHERE `id` = :id;");
					$qry->execute(array(':id' => $_SESSION["USER_ID"]));
				} elseif($CLEAN["2fa_radio"] === "2"){
					// 2fa is set to 'Generate new' on the form so update it in the database.
					$qry = $CFG["DBH"]->prepare("UPDATE `ccms_user` SET `2fa_secret` = :2fa_secret WHERE `id` = :id;");
					$qry->execute(array(':2fa_secret' => $CLEAN["2fa_secret"], ':id' => $_SESSION["USER_ID"]));
				}
			}

			$msg["success"] = "Updates Saved"; // update successful
		} else {
			$msg["error"] = "Password failed, please try again.";
		}
	} else {
		$msg["error"] = "Password update failed, account not found on the server.";
	}
}

echo json_encode($msg);
