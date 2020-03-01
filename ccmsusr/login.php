<?php
header("Content-Type:text/html; charset=UTF-8");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if($_SERVER["SCRIPT_NAME"] != "/ccmsusr/index.php") {
	echo "This script can NOT be called directly.";
	die();
}

$message = NULL;
$ccms_pass_reset_form_message = NULL;

// This line scrubs out all the sessions that are expired.
$qry = $CFG["DBH"]->prepare("DELETE FROM `ccms_session` WHERE `exp` < :first;");
$qry->execute(array(':first' => $CLEAN["SESSION"]["first"]));

if($CLEAN["SESSION"]["fail"] >= 5) {
	// If the users session record indicates that they have attempted to login 5 or more times and failed; do not
	// show this page at all.  Simply redirect them base to the homepage for this site immediatly.
	header("Location: /");
	die();
} elseif($CLEAN["logout"] == "1") {
	// log out
	$qry = $CFG["DBH"]->prepare("UPDATE `ccms_session` SET `user_id` = NULL WHERE `code` = :code LIMIT 1;");
	$qry->execute(array(':code' => $CLEAN["SESSION"]["code"]));
	$message = "Logout Successful";
} elseif($CLEAN["login"] == "1") {
	// Login credentials posted, test them.
	if(!ccms_badIPCheck($_SERVER["REMOTE_ADDR"])) {
		$message = "There is a problem with your login, your IP Address is currently being blocked.  Please contact the website administrators directly by either phone or email if you feel this message is in error for more information.";
	} elseif($CLEAN["loginEmail"] == "") {
		$message = "'Email' field missing content.";
	} elseif($CLEAN["loginEmail"] == "MAXLEN") {
		$message = "'Email' field exceeded its maximum number of 255 character.";
	} elseif($CLEAN["loginEmail"] == "INVAL") {
		$message = "'Email' field either contains invalid characters or an invalid email address!";

	} elseif($CLEAN["loginPassword"] == "") {
		$message = "'Password' field missing content.";
	} elseif($CLEAN["loginPassword"] == "MINLEN") {
		$message = "'Password' field is too short, must be a minimum of 8 characters.";
	} elseif($CLEAN["loginPassword"] == "INVAL") {
		$message = "Something is wrong with your password, it came up as INVALID when testing is with with an open (.+) expression.";

	} elseif($CLEAN["g-recaptcha-response"] == "") {
		$message = "'g-recaptcha-response' field missing content.";
	} elseif($CLEAN["g-recaptcha-response"] == "MAXLEN") {
		$message = "'g-recaptcha-response' field exceeded its maximum number of 2048 character.";
	} elseif($CLEAN["g-recaptcha-response"] == "INVAL") {
		$message = "'g-recaptcha-response' field contains invalid characters!";
	} elseif($CLEAN["g-recaptcha-response"] != "" && $CLEAN["g-recaptcha-response"] != "MAXLEN" && $CLEAN["g-recaptcha-response"] != "INVAL") {
		$resp = file_get_contents( "https://www.google.com/recaptcha/api/siteverify?secret={$CFG['GOOGLE_RECAPTCHA_PRIVATEKEY']}&response={$CLEAN['g-recaptcha-response']}&remoteip={$_SERVER['REMOTE_ADDR']}" );
		$resp = json_decode($resp);
		if($resp->success == false) {
			$message = 'Google reCAPTCHA failed.';
		}
	}

	if($message == "") {
		$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_user` WHERE `email` = :loginEmail && `status` = 1 LIMIT 1;");
		$qry->execute(array(':loginEmail' => $CLEAN["loginEmail"]));
		$row = $qry->fetch(PDO::FETCH_ASSOC);
		if($row) {
			// An active user with the same email address WAS found in the database.
			if(password_verify($CLEAN["loginPassword"], $row["hash"])) {
				// The submitted password matches the hashed password stored on the server.
				// Rehash the password and replace original password hash on the server to make even more secure.
				// See https://alias.io/2010/01/store-passwords-safely-with-php-and-mysql/ for more details.
				$options = ['cost' => 10];
				$hash = password_hash($CLEAN["loginPassword"], PASSWORD_BCRYPT, $options);

				$qry = $CFG["DBH"]->prepare("UPDATE `ccms_user` SET `hash` = :hash WHERE `id` = :id LIMIT 1;");
				$qry->execute(array(':hash' => $hash, ':id' => $row["id"]));

				$qry = $CFG["DBH"]->prepare("UPDATE `ccms_session` SET `user_id` = :id, `fail` = 0 WHERE `code` = :code LIMIT 1;");
				$qry->execute(array(':id' => $row["id"], ':code' => $CLEAN["SESSION"]["code"]));

				header("Location: /" . $CLEAN["ccms_lng"] . "/user/");
				die();
			} else {
				// Password failed so we increment the fail field by 1, once it reaches 5 the login page wont
				// even be available to the user anymore till their session expires.
				$CLEAN["SESSION"]["fail"] = $CLEAN["SESSION"]["fail"] + 1;
				$qry = $CFG["DBH"]->prepare("UPDATE `ccms_session` SET `fail` = :fail WHERE `code` = :code LIMIT 1;");
				$qry->execute(array(':fail' => $CLEAN["SESSION"]["fail"], ':code' => $CLEAN["SESSION"]["code"]));
				if($CLEAN["SESSION"]["fail"] >= 5) {
					// Maximum number of fails for this session have been reached.  Do not accept anymore tries till this session record expires.
					header("Location: /");
					die();
				} else {
					$message = "Password failed, please try again.";
				}
			}
		} else {
			// An active user with the same email address WAS NOT found in the database.
			// Login failed so we increment the fail field by 1, once it reaches 5 the login page wont
			// even be available to the user anymore till their session expires.
			$CLEAN["SESSION"]["fail"] = $CLEAN["SESSION"]["fail"] + 1;
			$qry = $CFG["DBH"]->prepare("UPDATE `ccms_session` SET `fail` = :fail WHERE `code` = :code LIMIT 1;");
			$qry->execute(array(':fail' => $CLEAN["SESSION"]["fail"], ':code' => $CLEAN["SESSION"]["code"]));
			if($CLEAN["SESSION"]["fail"] >= 5) {
				// Maximum number of fails for this session have been reached.  Do not accept anymore tries till this session record expires.
				header("Location: /");
				die();
			} else {
				$message = "Login failed, please try again.";
			}
		}
	} else {
		// Login failed so we increment the fail field by 1, once it reaches 5 the login page wont
		// even be available to the user anymore till their session expires.
		$CLEAN["SESSION"]["fail"] = $CLEAN["SESSION"]["fail"] + 1;
		$qry = $CFG["DBH"]->prepare("UPDATE `ccms_session` SET `fail` = :fail WHERE `code` = :code LIMIT 1;");
		$qry->execute(array(':fail' => $CLEAN["SESSION"]["fail"], ':code' => $CLEAN["SESSION"]["code"]));
		if($CLEAN["SESSION"]["fail"] >= 5) {
			// Maximum number of fails for this session have been reached.  Do not accept anymore tries till this session record expires.
			header("Location: /");
			die();
		} else {
			$message .= " Login failed, please try again.";
		}
	}
} elseif($CLEAN["ccms_pass_reset_form"] == "1") {
	if(!ccms_badIPCheck($_SERVER["REMOTE_ADDR"])) {
		$ccms_pass_reset_form_message["fail"] = "There is a problem with your login, your IP Address is currently being blocked.  Please contact the website administrators directly by either phone or email if you feel this message is in error for more information.";
	} elseif($CLEAN["ccms_pass_reset_form_email"] == "") {
		$ccms_pass_reset_form_message["fail"] = "'ccms_pass_reset_form_email' field missing content.";
	} elseif($CLEAN["ccms_pass_reset_form_email"] == "MAXLEN") {
		$ccms_pass_reset_form_message["fail"] = "'ccms_pass_reset_form_email' field exceeded its maximum number of 255 character.";
	} elseif($CLEAN["ccms_pass_reset_form_email"] == "INVAL") {
		$ccms_pass_reset_form_message["fail"] = "'ccms_pass_reset_form_email' field contains invalid characters!";
	}

	if($ccms_pass_reset_form_message["fail"] == "") {
		$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_user` WHERE `email` = :ccms_pass_reset_form_email && `status` = 1 LIMIT 1;");
		$qry->execute(array(':ccms_pass_reset_form_email' => $CLEAN["ccms_pass_reset_form_email"]));
		$row = $qry->fetch(PDO::FETCH_ASSOC);
		if($row) {
			// An active user with the same email address WAS found in the database.
			// So create a new session record which can be linked to in email and used to help recover a lost password.
			$a = time() + 10;
			$b = $a;
			$a = md5($a);
			$c = $b + ($CFG["COOKIE_SESSION_EXPIRE"] * 60);

			$qry = $CFG["DBH"]->prepare("INSERT INTO `ccms_session` (code, first, last, exp, ip, user_agent, user_id, prf) VALUES (:code, :first, :last, :exp, :ip, :user_agent, :id, 1);");
			$qry->execute(array(':code' => $a, ':first' => $b, ':last' => $b, ':exp' => $c, ':ip' => $_SERVER["REMOTE_ADDR"], ':id' => $row["id"], ':user_agent' => $CLEAN["SESSION"]["user_agent"]));

			$boundary = uniqid('np');

			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "From: " . $CFG["EMAIL_FROM"] . "\r\n";
			$headers .= "Reply-To: " . $CFG["EMAIL_FROM"] . "\r\n";
			//$headers .= "Content-type: text/html; charset=UTF-8\r\n";
			$headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";

			$email_message = "This is a MIME encoded message.\r\n\r\n--" . $boundary . "\r\nContent-type: text/plain;charset=utf-8\r\n\r\n";

			//Plain text body
			$email_message .= 'To whom it may concern,

We received a password reset request for an account at ' . $CFG["DOMAIN"] . ' using this email address for verification. If you did not request this email please ignore this message.

Click or Copy and Paste the link bellow into your browser to proceed.

' . $_SERVER["REQUEST_SCHEME"] . '://' . $CFG["DOMAIN"] . '/en/user/?ccms_pass_reset_form_prf=1&ccms_pass_reset_form_code='.$a.'

Note: This recovery link will only work once, and must be used as soon as possible.  If you have any questions please contact us directly at ' . $CFG["EMAIL_FROM"] . '.

Regards,

' . $CFG["DOMAIN"] . '

--------------------
This e-mail may be privileged and/or confidential, and the sender does not waive any related rights and obligations. Any distribution, use or copying of this e-mail or the information it contains by other than an intended recipient is unauthorized. If you received this e-mail in error, please advise us (by return e-mail or otherwise) immediately.';

$email_message .= "\r\n\r\n--" . $boundary . "\r\nContent-type: text/html;charset=utf-8\r\n\r\n";

//Html body
$email_message .= '<html><body>
To whom it may concern,<br />
<br />
We received a password reset request for an account at ' . $CFG["DOMAIN"] . ' using this email address for verification. If you did not request this email please ignore this message.<br />
<br />
Click or Copy and Paste the link bellow into your browser to proceed.<br />
<br />
<a href="' . $_SERVER["REQUEST_SCHEME"] . '://' . $CFG["DOMAIN"] . '/en/user/?ccms_pass_reset_form_prf=1&ccms_pass_reset_form_code='.$a.'">' . $CFG["DOMAIN"] . '/en/user/?ccms_pass_reset_form_prf=1&ccms_pass_reset_form_code='.$a.'</a><br />
<br />
Note: This recovery link will only work once, and must be used as soon as possible.  If you have any questions please contact us directly at ' . $CFG["EMAIL_FROM"] . '.<br />
<br />
Regards,<br />
<br />
' . $CFG["DOMAIN"] . '<br />
<br />
<hr style="height: 1px; width:100%;" />
<span style="font-size: .8em;">This e-mail may be privileged and/or confidential, and the sender does not waive any related rights and obligations. Any distribution, use or copying of this e-mail or the information it contains by other than an intended recipient is unauthorized. If you received this e-mail in error, please advise us (by return e-mail or otherwise) immediately.</span>
</body></html>';
$email_message .= "\r\n\r\n--" . $boundary . "--";

			mail( $CLEAN["ccms_pass_reset_form_email"], "Temporary password reset link from " . $CFG["DOMAIN"], $email_message, $headers, "-f" . $CFG["EMAIL_BOUNCES_RETURNED_TO"] );

			$ccms_pass_reset_form_message["success"] = "A temporary password reset link has been emailed to " . $CLEAN["ccms_pass_reset_form_email"] . ". Follow the instructions in the email to reset your password. If you do not receive an email soon, please contact support at: " . $CFG["EMAIL_FROM"] . ".";
		} else {
			// An active user with the same email address WAS NOT found in the database.
			// Password reset failed so we increment the fail field by 1, once it reaches 5 the login page wont
			// even be available to the user anymore till their session expires.
			$CLEAN["SESSION"]["fail"] = $CLEAN["SESSION"]["fail"] + 1;
			$qry = $CFG["DBH"]->prepare("UPDATE `ccms_session` SET `fail` = :fail WHERE `code` = :code LIMIT 1;");
			$qry->execute(array(':fail' => $CLEAN["SESSION"]["fail"], ':code' => $CLEAN["SESSION"]["code"]));
			if($CLEAN["SESSION"]["fail"] >= 5) {
				// Maximum number of fails for this session have been reached.  Do not accept anymore tries till this session record expires.
				header("Location: /");
				die();
			} else {
				$ccms_pass_reset_form_message["fail"] = "Password reset failed.  An active user with the email address you submitted was not found.";
			}
		}
	} else {
		// Password reset failed so we increment the fail field by 1, once it reaches 5 the login page wont
		// even be available to the user anymore till their session expires.
		$CLEAN["SESSION"]["fail"] = $CLEAN["SESSION"]["fail"] + 1;
		$qry = $CFG["DBH"]->prepare("UPDATE `ccms_session` SET `fail` = :fail WHERE `code` = :code LIMIT 1;");
		$qry->execute(array(':fail' => $CLEAN["SESSION"]["fail"], ':code' => $CLEAN["SESSION"]["code"]));
		if($CLEAN["SESSION"]["fail"] >= 5) {
			// Maximum number of fails for this session have been reached.  Do not accept anymore tries till this session record expires.
			header("Location: /");
			die();
		} else {
			$ccms_pass_reset_form_message["fail"] .= "Password reset failed, please try again.";
		}
	}
} elseif($CLEAN["ccms_pass_reset_form_prf"] == "1") {
	// prf = pass reset flag
	if(!ccms_badIPCheck($_SERVER["REMOTE_ADDR"])) {
		$ccms_pass_reset_form_message["fail"] = "There is a problem with your login, your IP Address is currently being blocked.  Please contact the website administrators directly by either phone or email if you feel this message is in error for more information.";
	} elseif($CLEAN["ccms_pass_reset_form_code"] == "") {
		$ccms_pass_reset_form_message["fail"] = "'ccms_pass_reset_form_code' field missing content.";
	} elseif($CLEAN["ccms_pass_reset_form_code"] == "MAXLEN") {
		$ccms_pass_reset_form_message["fail"] = "'ccms_pass_reset_form_code' field exceeded its maximum number of 64 character.";
	} elseif($CLEAN["ccms_pass_reset_form_code"] == "INVAL") {
		$ccms_pass_reset_form_message["fail"] = "'ccms_pass_reset_form_code' field contains invalid characters!";
	}

	if($ccms_pass_reset_form_message["fail"] == "") {
		// This is an incoming password reset hyperlink, so first we need to make sure the prf session is still available.
		// Check the 'ccms_session' table for matches.
		$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_session` WHERE `code` = :ccms_session AND `ip` = :ip AND `user_agent` = :user_agent AND `prf` = 1 LIMIT 1;");
		$qry->execute(array(':ccms_session' => $CLEAN["ccms_pass_reset_form_code"], ':ip' => $_SERVER["REMOTE_ADDR"], ':user_agent' => $CLEAN["SESSION"]["user_agent"]));
		$row = $qry->fetch(PDO::FETCH_ASSOC);

		if(!$row) {
			// The prf session in the URL is either expired, invalid from this device or location.
			$CLEAN["ccms_pass_reset_form_prf"] = "";
			// Password reset failed so we increment the fail field by 1, once it reaches 5 the login page wont
			// even be available to the user anymore till their session expires.
			$CLEAN["SESSION"]["fail"] = $CLEAN["SESSION"]["fail"] + 1;
			$qry = $CFG["DBH"]->prepare("UPDATE `ccms_session` SET `fail` = :fail WHERE `code` = :code LIMIT 1;");
			$qry->execute(array(':fail' => $CLEAN["SESSION"]["fail"], ':code' => $CLEAN["SESSION"]["code"]));
			if($CLEAN["SESSION"]["fail"] >= 5) {
				// Maximum number of fails for this session have been reached.  Do not accept anymore tries till this session record expires.
				header("Location: /");
				die();
			} else {
				$ccms_pass_reset_form_message["fail"] = "Password reset failed.  This link is expired or invalid from this browser/device/location.  Please request a new password reset Email for this device from this location.";
			}
		}
	} else {
		// Something was wrong with the ccms_pass_reset_form_code variable.
		$CLEAN["ccms_pass_reset_form_prf"] = "";
		// Password reset failed so we increment the fail field by 1, once it reaches 5 the login page wont
		// even be available to the user anymore till their session expires.
		$CLEAN["SESSION"]["fail"] = $CLEAN["SESSION"]["fail"] + 1;
		$qry = $CFG["DBH"]->prepare("UPDATE `ccms_session` SET `fail` = :fail WHERE `code` = :code LIMIT 1;");
		$qry->execute(array(':fail' => $CLEAN["SESSION"]["fail"], ':code' => $CLEAN["SESSION"]["code"]));
		if($CLEAN["SESSION"]["fail"] >= 5) {
			// Maximum number of fails for this session have been reached.  Do not accept anymore tries till this session record expires.
			header("Location: /");
			die();
		} else {
			$ccms_pass_reset_form_message["fail"] .= " Password reset failed, please try again.";
		}
	}
} elseif($CLEAN["ccms_pass_reset_form_prf"] == "2") {
	// prf = pass reset flag
	// This is an incoming password reset hyperlink
	if(!ccms_badIPCheck($_SERVER["REMOTE_ADDR"])) {
		$ccms_pass_reset_form_message["fail"] = "There is a problem with your login, your IP Address is currently being blocked.  Please contact the website administrators directly by either phone or email if you feel this message is in error for more information.";
	} elseif($CLEAN["ccms_pass_reset_form_code"] == "") {
		$ccms_pass_reset_form_message["fail"] = "'ccms_pass_reset_form_code' field missing content.";
	} elseif($CLEAN["ccms_pass_reset_form_code"] == "MAXLEN") {
		$ccms_pass_reset_form_message["fail"] = "'ccms_pass_reset_form_code' field exceeded its maximum number of 64 character.";
	} elseif($CLEAN["ccms_pass_reset_form_code"] == "INVAL") {
		$ccms_pass_reset_form_message["fail"] = "'ccms_pass_reset_form_code' field contains invalid characters!";

	} elseif($CLEAN["password1"] == "") {
		$ccms_pass_reset_form_message["fail"] = "'password1' field missing content.";
	} elseif($CLEAN["password1"] == "MINLEN") {
		$ccms_pass_reset_form_message["fail"] = "'password1' field is too short, must be a minimum of 8 characters.";
	} elseif($CLEAN["password1"] == "INVAL") {
		$ccms_pass_reset_form_message["fail"] = "Something is wrong with your password1, it came up as INVALID when testing is with with an open (.+) expression.";

	} elseif($CLEAN["password2"] == "") {
		$ccms_pass_reset_form_message["fail"] = "'password2' field missing content.";
	} elseif($CLEAN["password2"] == "MINLEN") {
		$ccms_pass_reset_form_message["fail"] = "'password2' field is too short, must be a minimum of 8 characters.";
	} elseif($CLEAN["password2"] == "INVAL") {
		$ccms_pass_reset_form_message["fail"] = "Something is wrong with your password2, it came up as INVALID when testing is with with an open (.+) expression.";

	} elseif($CLEAN["password1"] != $CLEAN["password2"]) {
		$ccms_pass_reset_form_message["fail"] = "Password1 and password2 do not match.";

	} elseif($CLEAN["g-recaptcha-response"] == "") {
		$ccms_pass_reset_form_message["fail"] = "'g-recaptcha-response' field missing content.";
	} elseif($CLEAN["g-recaptcha-response"] == "MAXLEN") {
		$ccms_pass_reset_form_message["fail"] = "'g-recaptcha-response' field exceeded its maximum number of 2048 character.";
	} elseif($CLEAN["g-recaptcha-response"] == "INVAL") {
		$ccms_pass_reset_form_message["fail"] = "'g-recaptcha-response' field contains invalid characters!";
	} elseif($CLEAN["g-recaptcha-response"] != "" && $CLEAN["g-recaptcha-response"] != "MAXLEN" && $CLEAN["g-recaptcha-response"] != "INVAL") {
		$resp = file_get_contents( "https://www.google.com/recaptcha/api/siteverify?secret={$CFG['GOOGLE_RECAPTCHA_PRIVATEKEY']}&response={$CLEAN['g-recaptcha-response']}&remoteip={$_SERVER['REMOTE_ADDR']}" );
		$resp = json_decode($resp);
		if($resp->success == false) {
			$ccms_pass_reset_form_message["fail"] = 'Google reCAPTCHA failed.';
		}
	}

	if($ccms_pass_reset_form_message["fail"] == "") {
		// This is an password reset submittion, so first we need to make sure the prf session is still available.
		// Check the 'ccms_session' table for matches.
		$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_session` WHERE `code` = :ccms_session AND `ip` = :ip AND `user_agent` = :user_agent AND `prf` = 1 LIMIT 1;");
		$qry->execute(array(':ccms_session' => $CLEAN["ccms_pass_reset_form_code"], ':ip' => $_SERVER["REMOTE_ADDR"], ':user_agent' => $CLEAN["SESSION"]["user_agent"]));
		$row = $qry->fetch(PDO::FETCH_ASSOC);

		if(!$row) {
			// The prf session in the URL is either expired, invalid from this device or location.
			$CLEAN["ccms_pass_reset_form_prf"] = "";
			$ccms_pass_reset_form_message["fail"] = "Password reset failed.  This link is expired or invalid from this browser/device/location.  Please request a new password reset Email for this device from this location.";
		} else {
			// The prf session is valid.
			// Remove the Password Reset session from the database because they are one time use only.
			$id = $row["user_id"];
			$qry = $CFG["DBH"]->prepare("DELETE FROM `ccms_session` WHERE `id` = :id LIMIT 1;");
			$qry->execute(array(':id' => $row["id"]));
			// Confirm there is a live, active, user account under the specified user id.
			$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_user` WHERE `id` = :id && `status` = 1 LIMIT 1;");
			$qry->execute(array(':id' => $id));
			$row = $qry->fetch(PDO::FETCH_ASSOC);
			if(!$row) {
				// Failed, an active user of the provided ID was not found.
				$qry = $CFG["DBH"]->prepare("DELETE FROM `ccms_session` WHERE `code` = :ccms_session LIMIT 1;");
				$qry->execute(array(':ccms_session' => $CLEAN["ccms_pass_reset_form_code"]));
				$CLEAN["ccms_pass_reset_form_prf"] = "";
				$ccms_pass_reset_form_message["fail"] = "Password reset failed.  An active user of the provided ID was not found.  Please request a new password reset Email for this browser/device/location because they are one-time use only.";
			} else {
				// Success, an active user of the provided ID WAS found.
				// Encrypt the new password and overwrite the old one.
				// Rehash the password and replace original password hash on the server to make even more secure.
				// See https://alias.io/2010/01/store-passwords-safely-with-php-and-mysql/ for more details.
				$options = ['cost' => 10];
				$hash = password_hash($CLEAN["password1"], PASSWORD_BCRYPT, $options);

				$qry = $CFG["DBH"]->prepare("UPDATE `ccms_user` SET `hash` = :hash WHERE `id` = :id LIMIT 1;");
				$qry->execute(array(':hash' => $hash, ':id' => $id));

				$qry = $CFG["DBH"]->prepare("DELETE FROM `ccms_session` WHERE `code` = :ccms_session LIMIT 1;");
				$qry->execute(array(':ccms_session' => $CLEAN["ccms_pass_reset_form_code"]));
				$CLEAN["ccms_pass_reset_form_prf"] = "";

				$ccms_pass_reset_form_message["success"] = "Success!  Your password has been updated, you can now log in with it using the form above.";
			}
		}
	} else {
		// Something is wrong with one or more of the required fields.  The password reset attempt failed and must be done again.
		// Remove the Password Reset session from the database because they are one time use only.
		$qry = $CFG["DBH"]->prepare("DELETE FROM `ccms_session` WHERE `code` = :ccms_session LIMIT 1;");
		$qry->execute(array(':ccms_session' => $CLEAN["ccms_pass_reset_form_code"]));
		$CLEAN["ccms_pass_reset_form_prf"] = "";

		// Password reset failed so we increment the fail field by 1, once it reaches 5 the login page wont
		// even be available to the user anymore till their session expires.
		$CLEAN["SESSION"]["fail"] = $CLEAN["SESSION"]["fail"] + 1;
		$qry = $CFG["DBH"]->prepare("UPDATE `ccms_session` SET `fail` = :fail WHERE `code` = :code LIMIT 1;");
		$qry->execute(array(':fail' => $CLEAN["SESSION"]["fail"], ':code' => $CLEAN["SESSION"]["code"]));
		if($CLEAN["SESSION"]["fail"] >= 5) {
			// Maximum number of fails for this session have been reached.  Do not accept anymore tries till this session record expires.
			header("Location: /");
			die();
		} else {
			$ccms_pass_reset_form_message["fail"] .= " Please request a new password reset Email for this browser/device/location because they are one-time use only.";
		}
	}
}

if($CLEAN["logout"] == "" && $CLEAN["login"] == "" && $CLEAN["ccms_pass_reset_form"] == "" && $CLEAN["ccms_pass_reset_form_prf"] == "") {
	$CLEAN["login"] = "1";
}
?><!DOCTYPE html>
<html id="no-fouc" lang="en" style="opacity: 0;">
	<head>
		<meta charset="utf-8">
		<title>Login</title>
		<meta name="description" content="" />
		{CCMS_TPL:header-head.html}
	</head>
	<body>
<?php if($CLEAN["logout"] == "1" || $CLEAN["login"] == "1" || $CLEAN["ccms_pass_reset_form"] == "1" || $CLEAN["ccms_pass_reset_form_prf"] == "2"): ?>

		<div class="container">
			<div class="row">
				<div class="col-md-5 col-md-offset-3">

					<div>
						<img alt="Custodian CMS Banner." src="/ccmsusr/_img/ccms-535x107.png" style="height: 56px; margin-top: 20px;" title="Custodian CMS Banner.  Easy gears no spilled beers.">
					</div>
<?php if(isset($message) && $message != ""): ?>
					<div class="alert alert-danger" style="margin-bottom: 0; margin-top: 20px;">
						<?php echo $message; ?>
					</div>
<?php endif ?>
					<div class="login-panel panel panel-default" style="margin-top: 20px;">
						<div class="panel-heading">
							<h3 class="panel-title">Login - Session Expired</h3>
						</div>
						<div class="panel-body">

							<form action="/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/" id="loginForm" method="post">
								<input type="hidden" name="login" value="1">
								<div id="login-status" style="color:#ec7f27; font-weight:bold;"></div>
								<div class="form-group">
									<label for="loginEmail">Email Address</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-at"></i></div>
										<input class="form-control" id="loginEmail" name="loginEmail" placeholder="Email" type="email" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
									</div>
								</div>
								<div class="form-group">
									<label for="loginPassword">Password</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-key"></i></div>
										<input class="form-control" id="loginPassword" name="loginPassword" placeholder="Password" type="password" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
									</div>
								</div>
								<div class="form-group">
									<div class="input-group" id="form-captcha">
										<div class="g-recaptcha" data-sitekey="{CCMS_LIB:_default.php;FUNC:ccms_googleRecapPubKey}"></div>
										<label id="error-grecaptcha" class="error" for="g-recaptcha" style="display: none;"></label>
									</div>
								</div>
								<button type="submit" class="btn btn-lg btn-success btn-block">Login</button>
							</form>

						</div>
					</div>
				</div>
			</div>
			<div class="row" style="margin-bottom: 20px;">
				<div class="col-md-5 col-md-offset-3">
					<a href="/{CCMS_LIB:_default.php;FUNC:ccms_lng}/">
						<i class="fa fa-caret-square-o-left"></i> Return
					</a>
					<a href="/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/" id="loginHelpLink" style="float: right;">
						Login Help <i class="fa fa-caret-square-o-right"></i>
					</a>
				</div>
			</div>
			<div class="row" id="ccms_reset_div" style="display: none;">
				<div class="col-md-5 col-md-offset-3">
<?php if(isset($ccms_pass_reset_form_message["fail"]) && $ccms_pass_reset_form_message["fail"] != ""): ?>
					<div class="alert alert-danger" style="margin-top: 20px; margin-bottom: 20px;">
						<?php echo $ccms_pass_reset_form_message["fail"]; ?>
					</div>
<?php elseif(isset($ccms_pass_reset_form_message["success"]) && $ccms_pass_reset_form_message["success"] != ""): ?>
					<div class="alert alert-success" style="margin-top: 20px; margin-bottom: 20px;">
						<?php echo $ccms_pass_reset_form_message["success"]; ?>
					</div>
<?php endif ?>
					<div class="panel panel-yellow">
						<div class="panel-heading">Password Reset</div>
						<div class="panel-body">
							<p>Please enter your account email address. We will send you a link to reset your password.</p>
							<form action="/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/" id="ccms_pass_reset_form" method="post">
								<input type="hidden" name="ccms_pass_reset_form" value="1">
								<div id="reset-status" style="color:#ec7f27; font-weight:bold;"></div>
								<div class="form-group">
									<label for="ccms_pass_reset_form_email">Email Address</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-at"></i></div>
										<input class="form-control" id="ccms_pass_reset_form_email" name="ccms_pass_reset_form_email" placeholder="Email" type="email">
										<label id="error-ccms_pass_reset_form_email" class="error" for="ccms_pass_reset_form_email" style="display: none;"></label>
									</div>
								</div>
								<button type="submit" class="btn btn-lg btn-success btn-block">Request Link</button>
							</form>
							<p>NOTE: The link contained in the email will only work once and only within one hour of its request.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php else: ?>
		<div class="container">
			<div class="row">
				<div class="col-md-5 col-md-offset-3">
					<div class="alert alert-success" style="margin-bottom: 0; margin-top: 20px;">
						Use the form below to reset your password.  Remember this link/form will only work one time.  Once you submit this form it will not work again unless you request a new Password Reset link.
					</div>
					<div class="login-panel panel panel-default" style="margin-top: 20px;">
						<div class="panel-heading">
							<h3 class="panel-title">New Password</h3>
						</div>
						<div class="panel-body">

							<form action="/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/" id="ccms_pass_reset_form_prf" method="post">
								<input type="hidden" name="ccms_pass_reset_form_prf" value="2">
								<input type="hidden" name="ccms_pass_reset_form_code" value="<?php echo $CLEAN["ccms_pass_reset_form_code"]; ?>">

								<div id="update-status" style="color:#ec7f27; font-weight:bold;"></div>

								<div class="form-group">
									<label for="password1">Password</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-key"></i></div>
										<input class="form-control" id="password1" name="password1" placeholder="Password" type="password">
									</div>
								</div>
								<div class="form-group">
									<label for="password2">Re-Type Password</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-key"></i></div>
										<input class="form-control" id="password2" name="password2" placeholder="Re-Type Password" type="password">
									</div>
								</div>
								<div class="form-group">
									<div class="input-group" id="form-captcha">
										<div class="g-recaptcha" data-sitekey="{CCMS_LIB:_default.php;FUNC:ccms_googleRecapPubKey}"></div>
										<label id="error-grecaptcha" class="error" for="g-recaptcha" style="display: none;"></label>
									</div>
								</div>
								<button type="submit" class="btn btn-lg btn-success btn-block">Update</button>
							</form>

						</div>
					</div>

					<?php echo $ccms_pass_reset_form_prf_message["success"]; ?>

				</div>
			</div>
<?php endif ?>
		<script>
			function loadFirst(e,t){var a=document.createElement("script");a.async = true;a.readyState?a.onreadystatechange=function(){("loaded"==a.readyState||"complete"==a.readyState)&&(a.onreadystatechange=null,t())}:a.onload=function(){t()},a.src=e,document.body.appendChild(a)}

			var cb = function() {
				var l = document.createElement('link'); l.rel = 'stylesheet';
				l.href = "/ccmsusr/_css/bootstrap-3.3.7.min.css";
				var h = document.getElementsByTagName('head')[0]; h.parentNode.insertBefore(l, h);

				var l = document.createElement('link'); l.rel = 'stylesheet';
				l.href = "/ccmsusr/_css/custodiancms.css";
				/*l.href = "/ccmsusr/_css/custodiancms.min.css";*/
				var h = document.getElementsByTagName('head')[0]; h.parentNode.insertBefore(l, h);

				var l = document.createElement('link'); l.rel = 'stylesheet';
				l.href = "/ccmsusr/_css/font-awesome-4.7.0.min.css";
				var h = document.getElementsByTagName('head')[0]; h.parentNode.insertBefore(l, h);
			};

			var raf = requestAnimationFrame || mozRequestAnimationFrame || webkitRequestAnimationFrame || msRequestAnimationFrame;
			if (raf) raf(cb);
			else window.addEventListener('load', cb);

			function loadJSResources() {
				loadFirst("/ccmsusr/_js/jquery-2.2.0.min.js", function() { /* JQuery */
					loadFirst("/ccmsusr/_js/bootstrap-3.3.7.min.js", function() { /* Bootstrap */
						loadFirst("https://www.google.com/recaptcha/api.js?hl={CCMS_LIB:_default.php;FUNC:ccms_lng}", function() { /* Google reCaptcha */
							loadFirst("/ccmsusr/_js/jquery.validate-1.17.0.min.js", function() { /* JQuery Validate */
								/*loadFirst("/ccmsusr/_js/custodiancms.js", function() { /* CustodianCMS JavaScript */
								loadFirst("/ccmsusr/_js/custodiancms.min.js", function() { /* CustodianCMS JavaScript */

									/* Fade in web page. */
									$("#no-fouc").delay(200).animate({"opacity": "1"}, 500);

									$("#loginHelpLink").click(function(event){
										event.preventDefault();

										if($("#ccms_reset_div").is(":visible")) {
											$("#ccms_reset_div").css("display", "none");
										} else {
											$("#ccms_reset_div").css("display", "block");
											$("#ccms_reset_div").scrollView();
										}

									});

									$("#loginForm").validate({
										rules: {
											loginEmail: {
												required: true,
												email: true,
												maxlength: 255
											},
											loginPassword: {
												required: true,
												minlength: 8
											}
										},
										messages: {
											loginEmail: {
												required: "Please enter a valid email address.",
												maxlength: "Please try to keep your email address to 255 characters or less."
											},
											loginPassword: {
												required: "Please enter your password.",
												minlength: "Passwords must be at least 8 characters in length."
											}
										}
									});

									$("#ccms_pass_reset_form").validate({
										rules: {
											ccms_pass_reset_form_email: {
												required: true,
												email: true,
												maxlength: 255
											}
										},
										messages: {
											ccms_pass_reset_form_email: {
												required: "Please enter a valid email address.",
												maxlength: "Please try to keep your email address to 255 characters or less."
											}
										}
									});

									$("#ccms_pass_reset_form_prf").validate({
										rules: {
											password1: {
												required: true,
												minlength: 8
											},
											password2: {
												required: true,
												minlength: 8,
												equalTo: "#password1"
											}
										},
										messages: {
											password1: {
												required: "Please enter your password.",
												minlength: "Passwords must be at least 8 characters in length."
											},
											password2: {
												required: "Please enter your password.",
												minlength: "Passwords must be at least 8 characters in length.",
												equalTo: "Please enter the same password as above."
											}
										}
									});
<?php if(isset($ccms_pass_reset_form_message) && ($ccms_pass_reset_form_message != "")): ?>
									$('#ccms_reset_div').css('display', 'block');
									$('#ccms_reset_div').scrollView();
<?php endif ?>
								});
							});
						});
					});
				});
			}

			if(window.addEventListener)
				window.addEventListener("load", loadJSResources, false);
			else if(window.attachEvent)
				window.attachEvent("onload", loadJSResources);
			else window.onload = loadJSResources;
		</script>
	</body>
</html>
