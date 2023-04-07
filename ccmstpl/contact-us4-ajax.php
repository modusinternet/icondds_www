<?
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["LIBDIR"] . "/_default.php";

$json = array();

/*
if(!strstr($_SERVER["HTTP_REFERER"], $CFG["DOMAIN"])) {
	$json['error']['invalid_referer'] = "Invalid submission, your POST does not appeared to have been submitted from the " . $CFG["DOMAIN"] . " website. (HTTP_REFERER = ". $_SERVER["HTTP_REFERER"] . ")";
}
*/

if(ccms_badIPCheck($_SERVER["REMOTE_ADDR"])) {
	$json['error']['invalid_badIPCheck'] = "There is a problem with your message, it can not be posted using this form from your current IP Address.  Please contact the website administrators directly by either phone or email if you feel this message is in error for more information.";
}

if($CLEAN["cuName"] == "") {
	$json['error']['cuName'] = "Please enter your full name.";
} elseif($CLEAN["cuName"] == "MINLEN") {
	$json['error']['cuName'] = "This field must be between 2 to 32 characters";
} elseif($CLEAN["cuName"] == "MAXLEN") {
	$json['error']['cuName'] = "This field must be between 2 to 32 characters";
} elseif($CLEAN["cuName"] == "INVAL") {
	$json['error']['cuName'] = "'Name' field contains invalid characters.  ( > < & # )  You have used characters in this field which are either not supported by this field or we do not permitted on this system.";
}

if($CLEAN["cuEmail"] == "") {
	$json['error']['cuEmail'] = "Please include your email address.";
} elseif($CLEAN["cuEmail"] == "MAXLEN") {
	$json['error']['cuEmail'] = "'Email' field exceeded its maximum number of 256 character.";
} elseif($CLEAN["cuEmail"] == "INVAL") {
	$json['error']['cuEmail'] = "'Email' field contains invalid characters.  ( > < & # )  You have used characters in this field which are either not supported by this field or we do not permitted on this system.";
}

if($CLEAN["cuMessage"] == "") {
	$json['error']['cuMessage'] = "Please enter your message.";
} elseif($CLEAN["cuMessage"] == "MAXLEN") {
	$json['error']['cuMessage'] = "'Message' field exceeded its maximum number of 512 character.";
} elseif($CLEAN["cuMessage"] == "INVAL") {
	$json['error']['cuMessage'] = "'Message' field contains invalid characters.  ( > < & # )  You have used characters in this field which are either not supported by this field or we do not permitted on this system.";
}

if(!bad_word_check($CLEAN["cuName"])) {
	$json['error']['cuName'] = "There is a problem with your message, it contains words or phrase which can not be posted using this form.  Please contact the website administrators directly by either phone or email if you feel this message is in error for more information.";
}

if(!bad_word_check($CLEAN["cuEmail"])) {
	$json['error']['cuEmail'] = "There is a problem with your message, it contains words or phrase which can not be posted using this form.  Please contact the website administrators directly by either phone or email if you feel this message is in error for more information.";
}

if(!bad_word_check($CLEAN["cuMessage"])) {
	$json['error']['cuMessage'] = "There is a problem with your message, it contains words or phrase which can not be posted using this form.  Please contact the website administrators directly by either phone or email if you feel this message is in error for more information.";
}

// Checking reCaptcha
if($_POST["g_recaptcha_response"]) {
	//$resp = file_get_contents( "https://www.google.com/recaptcha/api/siteverify?secret={$CFG['GOOGLE_RECAPTCHA_PRIVATEKEY']}&response={$_POST['g-recaptcha-response']}&remoteip={$_SERVER['REMOTE_ADDR']}" );



	/*
	$resp = file_get_contents( "https://www.google.com/recaptcha/api/siteverify?secret=" . $CFG['GOOGLE_RECAPTCHA_PRIVATEKEY'] . "&response=" . $_POST['g-recaptcha-response'] . "&remoteip=" . $_SERVER['REMOTE_ADDR'] );

//echo "\/*" . $resp . "*\/";
//print_r($resp);
//die();

	//$resp = json_decode($resp);
	//$resp = json_decode($response, true);
	//if($resp->success == false) {
	if($resp.success == false) {
	//if (!$resp->is_valid) {
	//if($resp["success"] == false) {
	//if(!$resp->is_valid) {
		$error = $resp.error-codes;
		$json['error']['grecaptcha'] = "Incorrect code. Please try again.<br />error code: " . $error;
	}
	*/




	$resp = '';
	// query use fsockopen
	$fp = fsockopen('ssl://www.google.com', 443, $errno, $errstr, 10);
	if($fp !== false) {
		$out = "GET /recaptcha/api/siteverify?secret={$CFG['GOOGLE_RECAPTCHA_PRIVATEKEY']}&response={$CLEAN['g_recaptcha_response']}&remoteip={$_SERVER['REMOTE_ADDR']} HTTP/1.1\r\n";
		$out .= "Host: www.google.com\r\n";
		$out .= "Connection: Close\r\n\r\n";
		@fwrite($fp, $out);
		while(!feof($fp)) {
			//$resp .= fgets($fp, 4096);
			$resp .= fread($fp, 4096);
		}
		@fclose($fp);

		$position = strpos($resp, "\r\n\r\n");
		$resp = substr($resp, $position);
		$position = strpos($resp, "{");
		$resp = substr($resp, $position);
		$resp = trim($resp, "\r\n0");
		$resp = json_decode($resp, true);

		//if($resp["success"] == false || $resp["action"] !== $CLEAN["g_recaptcha_action"] || $resp["score"] <= 0.4) {
			/*$ccms_login_message["FAIL"] = 'Google reCAPTCHA failed or expired. Try again. (success=['.$resp["success"].'], score=['.$resp["score"].'], action=['.$resp["action"].'], error-codes=['.$resp["error-codes"].'])';*/
			//$json['error']['grecaptcha'] = 'Google reCAPTCHA failed or expired. Try again. (1)';
		//}




		if($resp["success"] == false) {
			$json['error']['grecaptcha'] = 'Google reCAPTCHA failed or expired. Try again. (1)  (success=['.$resp["success"].'], score=['.$resp["score"].'], action=['.$resp["action"].'], error-codes=['.$resp["error-codes"].'])';
			//$json['error']['grecaptcha'] = 'Google reCAPTCHA failed or expired. Try again. (1)';
		} elseif($resp["action"] !== $CLEAN["g_recaptcha_action"]) {
			$json['error']['grecaptcha'] = 'Google reCAPTCHA failed or expired. Try again. (2 (' . $CLEAN["g_recaptcha_action"] . '))';
		} elseif($resp["score"] <= 0.4) {
			$json['error']['grecaptcha'] = 'Google reCAPTCHA failed or expired. Try again. (3)';
		}


	} else {
		//$ccms_login_message["FAIL"] = 'Unable to connect to Google reCAPTCHA.)';
		$json['error']['grecaptcha'] = 'Unable to connect to Google reCAPTCHA.)';
	}



} else {
	$json['error']['grecaptcha'] = 'Please prove that you are not a robot.';
}





// If no errors
if(!isset( $json['error'])) {
	$search = array(Chr(10), Chr(13), "\\n");
	$replace = array("<br />", "", "<br />");

	// Email text
	$mail_message = "<div style=\"padding:10px;\">This email message was sent by someone using the Contact Us Form on <a href=\"" . $_SERVER["HTTP_REFERER"] . "\">" . $_SERVER["HTTP_REFERER"] . "</a>.<br /><br />IP Address: " . $_SERVER["REMOTE_ADDR"] . "<br />";
	$mail_message .= "From: " . $CLEAN["cuName"] . "<br />";
	$mail_message .= "E-mail: " . $CLEAN["cuEmail"] . "<br />";
	$mail_message .= "Message:<br />
	" . str_replace($search, $replace, $CLEAN["cuMessage"]) . "<br />
	<br />
	<div style='color: gray; font-size: .8em;'>
	This e-mail may be privileged and/or confidential, and the sender does not waive any related rights and obligations. Any distribution, use or copying of this e-mail or the information it contains by other than an intended recipient is unauthorized. If you received this e-mail in error, please advise me (by return e-mail or otherwise) immediately.</div></div>";

	$mail_headers = "MIME-Version: 1.0" . "\r\n";
	$mail_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$mail_headers .= "From: " . $CFG["EMAIL_FROM"] . "\r\n";
	// Sending email
	mail( $CFG["EMAIL_FROM"], "Email from " . $CFG["DOMAIN"] . ", Contact Us Form", $mail_message, $mail_headers, "-f" . $CFG["EMAIL_BOUNCES_RETURNED_TO"] );
	$json['success'] = 'Your message was sent successfully!';
}
echo json_encode($json);
