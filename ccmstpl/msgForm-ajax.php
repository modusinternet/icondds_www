<?
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["LIBDIR"] . "/_default.php";
include $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["LIBDIR"] . "/site.php";

$json = array();

if(!strstr($_SERVER["HTTP_REFERER"], $CFG["DOMAIN"])) {
	$json['error']['invalid_referer'] = "Invalid submission, your POST does not appeared to have been submitted from the " . $CFG["DOMAIN"] . " website.";
}

if(!ccms_badIPCheck($_SERVER["REMOTE_ADDR"])) {
	$json['error']['invalid_badIPCheck'] = "There is a problem with your message, it can not be posted using this form from your current IP Address.  Please contact the website administrators directly by either phone or email if you feel this message is in error for more information.";
}

if($CLEAN["msgName"] == "") {
	$json['error']['msgName'] = "Please enter your full name.";
} elseif($CLEAN["msgName"] == "MINLEN") {
	$json['error']['msgName'] = "This field must be between 2 to 32 characters";
} elseif($CLEAN["msgName"] == "MAXLEN") {
	$json['error']['msgName'] = "This field must be between 2 to 32 characters";
} elseif($CLEAN["msgName"] == "INVAL") {
	$json['error']['msgName'] = "'Name' field contains invalid characters.  ( > < & # )  You have used characters in this field which are either not supported by this field or we do not permitted on this system.";
}

if($CLEAN["msgEmail"] == "") {
	$json['error']['msgEmail'] = "Please include your email address.";
} elseif($CLEAN["msgEmail"] == "MAXLEN") {
	$json['error']['msgEmail'] = "'Email' field exceeded its maximum number of 256 character.";
} elseif($CLEAN["msgEmail"] == "INVAL") {
	$json['error']['msgEmail'] = "'Email' field contains invalid characters.  ( > < & # )  You have used characters in this field which are either not supported by this field or we do not permitted on this system.";
}

if($CLEAN["msgTextarea"] == "") {
	$json['error']['msgTextarea'] = "Please enter your message.";
} elseif($CLEAN["msgTextarea"] == "MAXLEN") {
	$json['error']['msgTextarea'] = "'Message' field exceeded its maximum number of 512 character.";
} elseif($CLEAN["msgTextarea"] == "INVAL") {
	$json['error']['msgTextarea'] = "'Message' field contains invalid characters.  ( > < & # )  You have used characters in this field which are either not supported by this field or we do not permitted on this system.";
}

if(!bad_word_check($CLEAN["msgName"])) {
	$json['error']['msgName'] = "There is a problem with your message, it contains words or phrase which can not be posted using this form.  Please contact the website administrators directly by either phone or email if you feel this message is in error for more information.";
}

if(!bad_word_check($CLEAN["msgEmail"])) {
	$json['error']['msgEmail'] = "There is a problem with your message, it contains words or phrase which can not be posted using this form.  Please contact the website administrators directly by either phone or email if you feel this message is in error for more information.";
}

if(!bad_word_check($CLEAN["msgTextarea"])) {
	$json['error']['msgTextarea'] = "There is a problem with your message, it contains words or phrase which can not be posted using this form.  Please contact the website administrators directly by either phone or email if you feel this message is in error for more information.";
}

/* Checking reCaptcha
if($_POST["g-recaptcha-response"]) {
	$resp = file_get_contents( "https://www.google.com/recaptcha/api/siteverify?secret={$CFG['GOOGLE_RECAPTCHA_PRIVATEKEY']}&response={$_POST['g-recaptcha-response']}&remoteip={$_SERVER['REMOTE_ADDR']}" );
	$resp = json_decode($resp);
	if($resp->success == false) {
		$json['error']['grecaptcha'] = 'Incorrect code. Please try again.';
	}
} else {
	$json['error']['grecaptcha'] = 'Please prove that you are not a robot.';
}
*/

/*
// Checking reCaptcha
if($_POST["g-recaptcha-response"]) {
	$resp = file_get_contents( "https://www.google.com/recaptcha/api/siteverify?secret=" . $CFG['GOOGLE_RECAPTCHA_PRIVATEKEY'] . "&response=" . $_POST['g-recaptcha-response'] . "&remoteip=" . $_SERVER['REMOTE_ADDR'] );
	if($resp.success == false) {
		$error = $resp.error-codes;
		$json['error']['grecaptcha'] = "Incorrect code. Please try again.<br />error code: " . $error;
	}
} else {
	$json['error']['grecaptcha'] = 'Please prove that you are not a robot.';
}
*/

// If no errors
if(!isset( $json['error'])) {
	$search = array(Chr(10), Chr(13), "\\n");
	$replace = array("<br />", "", "<br />");

	// Email text
	$mail_message = "This email message was sent by someone using the Message Form Popup on <a href=\"" . $_SERVER["HTTP_REFERER"] . "\">" . $_SERVER["HTTP_REFERER"] . "</a>.<br /><br />IP Address: " . $_SERVER["REMOTE_ADDR"] . "<br />";
	$mail_message .= "From: " . $CLEAN["msgName"] . "<br />";
	$mail_message .= "Email: " . $CLEAN["msgEmail"] . "<br />";
	$mail_message .= "Message: " . str_replace($search, $replace, $CLEAN["msgTextarea"]) . "<br />
<br /><div style='color: gray; font-size: .8em;'>This e-mail may be privileged and/or confidential, and the sender does not waive any related rights and obligations. Any distribution, use or copying of this e-mail or the information it contains by other than an intended recipient is unauthorized. If you received this e-mail in error, please advise me (by return e-mail or otherwise) immediately.</div>";

	$mail_headers = "MIME-Version: 1.0\n";
	$mail_headers .= "Content-type: text/html; charset=UTF-8\n";
	$mail_headers .= "From: " . $CFG["EMAIL_FROM"] . "\r\n";
	// Sending email
	mail( $CFG["EMAIL_FROM"], "Email from " . $CFG["DOMAIN"] . ", Message Form Popup", $mail_message, $mail_headers, "-f" . $CFG["EMAIL_BOUNCES_RETURNED_TO"] );
	//mail( "vince@modusinternet.com", "Email from " . $CFG["DOMAIN"] . ", Popup Message form", $mail_message, $mail_headers, "-f" . $CFG["EMAIL_BOUNCES_RETURNED_TO"] );
	$json['success'] = 'Message Sent';
}
echo json_encode($json);
