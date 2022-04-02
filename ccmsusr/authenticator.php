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

/*
PHP Class for handling Google Authenticator 2-factor authentication.
@author Michael Kliewe
@copyright 2012 Michael Kliewe
@license http://www.opensource.org/licenses/bsd-license.php BSD License
@link http://www.phpgangsta.de/
*/

class PHPGangsta_GoogleAuthenticator{
	protected $_codeLength = 6;

	/*
	Create new secret.
	16 characters, randomly chosen from the allowed base32 characters.
	@param int $secretLength
	@return string
	*/
	public function createSecret($secretLength = 16){
		$validChars = $this->_getBase32LookupTable();

		// Valid secret lengths are 80 to 640 bits
		if($secretLength < 16 || $secretLength > 128){
			throw new Exception('Bad secret length');
		}
		$secret = '';
		$rnd = false;
		if(function_exists('random_bytes')){
			$rnd = random_bytes($secretLength);
		} elseif (function_exists('mcrypt_create_iv')){
			$rnd = mcrypt_create_iv($secretLength, MCRYPT_DEV_URANDOM);
		} elseif (function_exists('openssl_random_pseudo_bytes')){
			$rnd = openssl_random_pseudo_bytes($secretLength, $cryptoStrong);
			if(!$cryptoStrong){
				$rnd = false;
			}
		}
		if($rnd !== false){
			for($i = 0;$i < $secretLength;++$i){
				$secret .= $validChars[ord($rnd[$i]) & 31];
			}
		} else {
			throw new Exception('No source of secure random');
		}
		return $secret;
	}

	/*
  Calculate the code, with given secret and point in time.
  @param string $secret
  @param int|null $timeSlice
  @return string
  */

	public function getCode($secret, $timeSlice = null){
		if ($timeSlice === null) {
			$timeSlice = floor(time() / 30);
		}

		$secretkey = $this->_base32Decode($secret);

		// Pack time into binary string
		$time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);
		// Hash it with users secret key
		$hm = hash_hmac('SHA1', $time, $secretkey, true);
		// Use last nipple of result as index/offset
		$offset = ord(substr($hm, -1)) & 0x0F;
		// grab 4 bytes of the result
		$hashpart = substr($hm, $offset, 4);

		// Unpak binary value
		$value = unpack('N', $hashpart);
		$value = $value[1];
		// Only 32 bits
		$value = $value & 0x7FFFFFFF;

		$modulo = pow(10, $this->_codeLength);

		return str_pad($value % $modulo, $this->_codeLength, '0', STR_PAD_LEFT);
	}

	/**
	 * Get QR-Code URL for image, from google charts.
	 *
	 * @param string $name
	 * @param string $secret
	 * @param string $title
	 * @param array  $params
	 *
	 * @return string
	 */
	public function getQRCodeGoogleUrl($name, $secret, $title = null, $params = array())
	{
		$width = !empty($params['width']) && (int) $params['width'] > 0 ? (int) $params['width'] : 200;
		$height = !empty($params['height']) && (int) $params['height'] > 0 ? (int) $params['height'] : 200;
		$level = !empty($params['level']) && array_search($params['level'], array('L', 'M', 'Q', 'H')) !== false ? $params['level'] : 'M';

		$urlencoded = urlencode('otpauth://totp/'.$name.'?secret='.$secret.'');
		if (isset($title)) {
			$urlencoded .= urlencode('&issuer='.urlencode($title));
		}

		return "https://api.qrserver.com/v1/create-qr-code/?data=$urlencoded&size=${width}x${height}&ecc=$level";
	}

	/**
	 * Check if the code is correct. This will accept codes starting from $discrepancy*30sec ago to $discrepancy*30sec from now.
	 *
	 * @param string   $secret
	 * @param string   $code
	 * @param int	  $discrepancy	  This is the allowed time drift in 30 second units (8 means 4 minutes before or after)
	 * @param int|null $currentTimeSlice time slice if we want use other that time()
	 *
	 * @return bool
	 */
	public function verifyCode($secret, $code, $discrepancy = 1, $currentTimeSlice = null)
	{
		if ($currentTimeSlice === null) {
			$currentTimeSlice = floor(time() / 30);
		}

		if (strlen($code) != 6) {
			return false;
		}

		for ($i = -$discrepancy; $i <= $discrepancy; ++$i) {
			$calculatedCode = $this->getCode($secret, $currentTimeSlice + $i);
			if ($this->timingSafeEquals($calculatedCode, $code)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Set the code length, should be >=6.
	 *
	 * @param int $length
	 *
	 * @return PHPGangsta_GoogleAuthenticator
	 */
	public function setCodeLength($length)
	{
		$this->_codeLength = $length;

		return $this;
	}

	/**
	 * Helper class to decode base32.
	 *
	 * @param $secret
	 *
	 * @return bool|string
	 */
	protected function _base32Decode($secret)
	{
		if (empty($secret)) {
			return '';
		}

		$base32chars = $this->_getBase32LookupTable();
		$base32charsFlipped = array_flip($base32chars);

		$paddingCharCount = substr_count($secret, $base32chars[32]);
		$allowedValues = array(6, 4, 3, 1, 0);
		if (!in_array($paddingCharCount, $allowedValues)) {
			return false;
		}
		for ($i = 0; $i < 4; ++$i) {
			if ($paddingCharCount == $allowedValues[$i] &&
				substr($secret, -($allowedValues[$i])) != str_repeat($base32chars[32], $allowedValues[$i])) {
				return false;
			}
		}
		$secret = str_replace('=', '', $secret);
		$secret = str_split($secret);
		$binaryString = '';
		for ($i = 0; $i < count($secret); $i = $i + 8) {
			$x = '';
			if (!in_array($secret[$i], $base32chars)) {
				return false;
			}
			for ($j = 0; $j < 8; ++$j) {
				$x .= str_pad(base_convert(@$base32charsFlipped[@$secret[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
			}
			$eightBits = str_split($x, 8);
			for ($z = 0; $z < count($eightBits); ++$z) {
				$binaryString .= (($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48) ? $y : '';
			}
		}

		return $binaryString;
	}

	/**
	 * Get array with all 32 characters for decoding from/encoding to base32.
	 *
	 * @return array
	 */
	protected function _getBase32LookupTable()
	{
		return array(
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
			'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
			'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
			'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
			'=',  // padding char
		);
	}

	/**
	 * A timing safe equals comparison
	 * more info here: http://blog.ircmaxell.com/2014/11/its-all-about-time.html.
	 *
	 * @param string $safeString The internal (safe) value to be checked
	 * @param string $userString The user submitted (unsafe) value
	 *
	 * @return bool True if the two strings are identical
	 */
	private function timingSafeEquals($safeString, $userString)
	{
		if (function_exists('hash_equals')) {
			return hash_equals($safeString, $userString);
		}
		$safeLen = strlen($safeString);
		$userLen = strlen($userString);

		if ($userLen != $safeLen) {
			return false;
		}

		$result = 0;

		for ($i = 0; $i < $userLen; ++$i) {
			$result |= (ord($safeString[$i]) ^ ord($userString[$i]));
		}

		// They are only identical strings if $result is exactly 0...
		return $result === 0;
	}
}


//if($_POST["ccms_auth_token_login"] == "1") {
if(($_POST["ccms_auth_token_login"] ?? null) === "1") {
	if(ccms_badIPCheck($_SERVER["REMOTE_ADDR"])) {
		$ccms_auth_token_error["FAIL"] = "There is a problem with your login, your IP Address is currently being blocked.  Please contact the website administrators directly if you feel this message is in error.";
	} elseif(empty($CLEAN["ccms_auth_token"])) {
		$ccms_auth_token_error["FAIL"] = "'Token' field missing content.";
	} elseif($CLEAN["ccms_auth_token"] == "MAXLEN") {
		$ccms_auth_token_error["FAIL"] = "'Token' field exceeded its maximum number of 6 character.";
	} elseif($CLEAN["ccms_auth_token"] == "MINLEN") {
		$ccms_auth_token_error["FAIL"] = "'Token' field is too short, its minimum number of characters is 6.";
	} elseif($CLEAN["ccms_auth_token"] == "INVAL") {
		$ccms_auth_token_error["FAIL"] = "The 'Token' field contains invalid characters! Try again.";
	}

	if(($ccms_auth_token_error["FAIL"] ?? null) !== "") {
		// If $ccms_auth_token_error["FAIL"] is null and not equal to "".

		$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_user` WHERE `id` = :id && `status` = 1 LIMIT 1;");
		$qry->execute(array(':id' => $_SESSION["USER_ID"]));
		$row = $qry->fetch(PDO::FETCH_ASSOC);

		$ga = new PHPGangsta_GoogleAuthenticator();
		$result = $ga->verifyCode($row["2fa_secret"], $CLEAN['ccms_auth_token'], 3);
		if($result == "3") {

			$_SESSION["2FA_VALID"] = true;
			$_SESSION["FAIL"] = 0;
			$_SESSION["HTTP_USER_AGENT"] = md5($_SERVER["HTTP_USER_AGENT"]);
			header("Location: /" . $CLEAN["ccms_lng"] . "/user/" . $CFG["INDEX"]);
			exit;
		} else {

			$_SESSION["FAIL"] = $_SESSION["FAIL"] + 1;
			$ccms_auth_token_error["FAIL"] = "Login failed, try again.";
		}
	}
}
?><!DOCTYPE html>
<html lang="{CCMS_LIB:_default.php;FUNC:ccms_lng}">
	<head>
		<title><?= $CFG["DOMAIN"];?> | User | Login | 2-Factor Authentication</title>
		{CCMS_TPL:head-meta.html}
	</head>
	<style nonce="{CCMS_LIB:_default.php;FUNC:ccms_csp_nounce}">
		{CCMS_TPL:/_css/head-css.html}

		button{
			border:unset;
			border-radius:5px;
			border-width:1px;
			font:unset;
			padding:3px 10px
		}

		.aGrid{display:grid}

		.aGrid>button{
			background:var(--cl3);
			border:0;
			color:var(--cl0);
			padding:0.5em;
			width:100%
		}

		.aGrid>button:hover{background:var(--cl3-tran)}

		.aGrid>input{
			background:var(--cl0);
			border:1px solid var(--cl10);
			border-radius:4px;
			padding:0.7em;
			margin-bottom:0.5rem
		}

		.aGrid>input:focus{outline:3px solid gold}

		.aGrid>label{white-space:nowrap}

		.aGrid>label.error{
			color:var(--cl11);
			margin-bottom:1rem;
			text-align:unset;
			white-space:unset
		}

		.formDiv{
			background-color:var(--cl0);
			border:1px solid var(--cl2-tran);
			border-radius:4px;
			box-shadow:5px 5px 5px var(--cl12)
		}

		.formDiv>div:first-child{
			background-color:var(--cl4);
			border-radius:4px 4px 0 0;
			color:var(--cl0);
			padding:5px 10px
		}

		.logo{
			filter:drop-shadow(5px 5px 5px rgba(10,37,64,.5));
			-webkit-transition:all 1.0s ease-in-out;
			-moz-transition:all 1.0s ease-in-out;
			-o-transition:all 1.0s ease-in-out;
			transition:all 1.0s ease-in-out;
			width:200px
		}

		.logo_a{
			border:0 none;
			display:block;
			text-align:left;
			text-decoration:none;
			/*margin:10px 10px 10px 60px;*/
			margin:unset
		}

		/* 500px or wider. */
		@media only screen and (min-width:500px){
			.aGrid{
				grid-template-columns:1fr 1fr;
				grid-gap:15px
			}

			.aGrid>button{grid-column:1 / span 2}

			.aGrid>label.error{grid-column:1 / span 2}

			.aGrid>input{grid-column:2 / 3}

			.aGrid>label{
				text-align:right;
				grid-column:1 / 2
			}

			.g-recaptcha>div{margin-bottom:unset}
		}
	</style>
	<body>
		<div id="loading_svg"></div>
		<a href="/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/" class="logo_a" style="position:unset;text-align:center;width:100%" title="Custodian CMS, for real website developers, not WYSIWYG developers.">
			<svg class="logo" style="margin:0px 10% 30px;max-width:1024px;width:unset" version="1.1" viewBox="0 0 210 63.26" xmlns="http://www.w3.org/2000/svg"><g transform="translate(-3.591e-7 -.02177)"><g transform="matrix(.4872 0 0 .4872 125.8 -147.1)" style="stroke-width:2.053"><g transform="matrix(1.108 0 0 1.108 5.208 -39.89)" style="stroke-width:1.852"><g transform="matrix(.9136 0 0 .9136 -455.2 203.5)" style="fill:#d7680f"><path transform="scale(.2646)" d="m1114 434.6c-6.721-0.0385-12.5 4.758-13.7 11.37l-5.848 32.23c-20.11 4.774-39.36 12.6-57.1 23.21l-26.65-18.92c-5.48-3.889-12.96-3.279-17.74 1.445l-42.06 41.58c-4.778 4.724-5.473 12.2-1.646 17.72l18.62 26.87c-10.81 17.61-18.85 36.78-23.86 56.83l-32.29 5.48c-6.627 1.123-11.49 6.845-11.53 13.57l-0.3359 59.14c-0.0378 6.719 4.757 12.49 11.37 13.69l32.23 5.85c4.774 20.11 12.6 39.36 23.21 57.1l-18.92 26.65c-3.889 5.48-3.279 12.96 1.445 17.74l41.58 42.06c4.724 4.778 12.2 5.473 17.72 1.646l26.87-18.62c17.61 10.81 36.78 18.85 56.83 23.86l5.482 32.29c1.123 6.626 6.844 11.49 13.56 11.53l59.14 0.3359c6.721 0.0385 12.5-4.758 13.7-11.37l5.848-32.23c20.11-4.774 39.36-12.6 57.1-23.21l26.66 18.92c5.48 3.889 12.96 3.279 17.74-1.445l42.06-41.58-88.3-62.29c-2.142 2.537-4.495 5.078-7.201 7.754-39.18 38.74-97.85 50.06-148.6 28.69-50.78-21.38-83.69-71.24-83.38-126.3 0.3131-55.1 33.78-104.6 84.81-125.4 51.02-20.8 109.6-8.808 148.3 30.38 2.676 2.706 5 5.273 7.113 7.834l89.01-61.28-41.58-42.06c-4.724-4.778-12.2-5.473-17.72-1.646l-26.87 18.62c-17.61-10.81-36.78-18.85-56.83-23.86l-5.482-32.29c-1.123-6.626-6.844-11.49-13.56-11.53zm64.77 47.94c-0.081 0.3182-0.1416 0.641-0.1817 0.9668-12.29 746.7-432.6 51.93 0.1817-0.9668z" style="fill:#d7680f"/></g><g transform="matrix(.3344 0 0 -.3344 559.1 249.8)" style="stroke-width:1.465"><g style="fill:#d7680f;stroke-width:1.465"><path d="m-1770-357.9c1.382 1.688 2.867 3.289 4.447 4.794h0.01c9.829 9.439 22.94 14.69 36.57 14.65 0.8652 0 1.737-0.0199 2.609-0.063 28-1.665 49.99-24.61 50.47-52.65 0.136-16.13-7.118-31.44-19.69-41.55-2.276-1.927-3.567-4.773-3.518-7.755v-7.39c-0.018-4.2-2.671-7.936-6.631-9.336v-3.925c-0.01-2.455-0.9303-4.819-2.586-6.631 3.448-3.748 3.448-9.514 0-13.26 3.676-4.01 3.404-10.24-0.6054-13.92-1.064-0.9747-2.328-1.704-3.705-2.136-1.514-9-10.04-15.07-19.04-13.56-6.945 1.168-12.39 6.61-13.56 13.56-5.194 1.616-8.096 7.136-6.48 12.33 0.4302 1.383 1.16 2.654 2.137 3.722-3.448 3.748-3.448 9.514 0 13.26-1.656 1.812-2.577 4.176-2.586 6.631v3.925c-3.96 1.4-6.613 5.136-6.631 9.336v7.775c-0.058 2.968-1.449 5.752-3.786 7.582-22.65 18.55-25.98 51.95-7.431 74.6zm41.06-146.3c4.214 6e-3 7.969 2.66 9.379 6.631h-18.76c1.41-3.971 5.166-6.626 9.379-6.631zm-13.26 13.26h26.52c1.831 0 3.315 1.484 3.315 3.315s-1.484 3.315-3.315 3.315h-26.52c-1.831 0-3.315-1.484-3.315-3.315s1.484-3.315 3.315-3.315zm0 13.26h26.52c1.831 0 3.315 1.484 3.315 3.315 0 1.831-1.484 3.315-3.315 3.315h-26.52c-1.831 0-3.315-1.484-3.315-3.315 0-1.831 1.484-3.315 3.315-3.315zm-3.315 16.58c0-1.831 1.484-3.315 3.315-3.315h26.52c1.831 0 3.315 1.484 3.315 3.315v3.315h-33.15z" style="fill:#d7680f;stroke-width:1.465"/><path d="m-1729-331.8c-1.831 0-3.316 1.484-3.316 3.315v13.26c0 1.831 1.485 3.315 3.316 3.315s3.316-1.484 3.316-3.315v-13.26c0-1.831-1.484-3.315-3.316-3.315z" style="fill:#d7680f;stroke-width:1.465"/><path d="m-1758-339.8c0-7.4e-4 -0.01-2e-3 -0.01-2e-3 -1.586-0.9146-3.614-0.3705-4.528 1.216 0 4.2e-4 -4e-4 7.5e-4 -4e-4 7.5e-4l-6.631 11.48c-0.9258 1.58-0.3962 3.611 1.184 4.537s3.611 0.3962 4.537-1.184c0.01-0.0124 0.015-0.0252 0.023-0.0381l6.631-11.48c0.9171-1.585 0.3754-3.613-1.21-4.53z" style="fill:#d7680f;stroke-width:1.465"/><path d="m-1785-362.9c-0.013 8e-3 -0.026 0.015-0.039 0.0223l-11.48 6.631c-1.592 0.9047-2.149 2.929-1.244 4.521 0.9046 1.592 2.929 2.149 4.52 1.244 0.013-8e-3 0.026-0.015 0.039-0.0224l11.48-6.631c1.592-0.9047 2.149-2.929 1.244-4.521-0.9047-1.592-2.929-2.149-4.52-1.244z" style="fill:#d7680f;stroke-width:1.465"/><path d="m-1788-391.5c0-1.831-1.484-3.315-3.316-3.315h-13.26c-1.831 0-3.316 1.484-3.316 3.315s1.485 3.315 3.316 3.315h13.26c1.831 0 3.316-1.484 3.316-3.315z" style="fill:#d7680f;stroke-width:1.465"/><path d="m-1796-426.7c0.012 8e-3 0.025 0.0145 0.038 0.0219l11.48 6.631c1.58 0.9258 3.611 0.3962 4.537-1.184 0.9258-1.58 0.3961-3.611-1.184-4.537-0.013-8e-3 -0.025-0.0145-0.038-0.0219l-11.48-6.631c-1.58-0.9258-3.611-0.3962-4.537 1.184-0.9258 1.579-0.3962 3.61 1.184 4.537z" style="fill:#d7680f;stroke-width:1.465"/><path d="m-1674-419.7c0.5819 0 1.154-0.1533 1.658-0.4443l11.48-6.631c1.592-0.9051 2.148-2.929 1.243-4.521s-2.929-2.148-4.521-1.243c-0.012 7e-3 -0.025 0.0145-0.038 0.0219l-11.48 6.631c-1.586 0.9155-2.129 2.943-1.213 4.529 0.5929 1.026 1.687 1.658 2.872 1.658z" style="fill:#d7680f;stroke-width:1.465"/><path d="m-1652-394.8h-13.26c-1.831 0-3.315 1.484-3.315 3.315s1.484 3.315 3.315 3.315h13.26c1.831 0 3.315-1.484 3.315-3.315s-1.484-3.315-3.315-3.315z" style="fill:#d7680f;stroke-width:1.465"/><path d="m-1661-356.2c-0.013-8e-3 -0.026-0.015-0.039-0.0223l-11.48-6.631c-1.58-0.9262-3.611-0.3966-4.537 1.183-0.9263 1.579-0.3966 3.611 1.183 4.537 0.012 8e-3 0.026 0.015 0.039 0.0224l11.48 6.631c1.58 0.9262 3.611 0.3966 4.537-1.183 0.9263-1.579 0.3966-3.611-1.183-4.537z" style="fill:#d7680f;stroke-width:1.465"/><path d="m-1688-327.1-6.631-11.48c-0.9047-1.592-2.929-2.149-4.52-1.244-1.592 0.9047-2.149 2.929-1.244 4.521 0.01 0.0128 0.015 0.0257 0.023 0.0385l6.631 11.48c0.9048 1.592 2.929 2.149 4.521 1.244 1.592-0.9047 2.149-2.929 1.244-4.521-0.01-0.0128-0.015-0.0257-0.023-0.0385z" style="fill:#d7680f;stroke-width:1.465"/></g></g><g transform="matrix(1.76 0 0 1.76 -186.7 -2091)" style="fill:#86b135;font-feature-settings:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-variant-numeric:normal" aria-label="Custodian"><path d="m49.47 1391v25.92h-8.027l0.1367-2.148q-0.8203 1.309-2.031 1.973-1.191 0.6445-2.754 0.6445-1.777 0-2.949-0.6249-1.172-0.625-1.738-1.66-0.5469-1.035-0.6836-2.148-0.1367-1.133-0.1367-4.473v-17.48h7.891v17.64q0 3.027 0.1758 3.594 0.1953 0.5664 1.016 0.5664 0.8789 0 1.035-0.5859 0.1758-0.5859 0.1758-3.77v-17.44z" style="fill:#86b135;font-feature-settings:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-variant-numeric:normal;stroke-width:.2783"/><path d="m68.46 1399h-6.992v-1.406q0-1.797-0.2148-2.266-0.1953-0.4883-1.016-0.4883-0.6641 0-0.9961 0.4493-0.332 0.4296-0.332 1.308 0 1.191 0.1562 1.758 0.1758 0.5468 0.9961 1.211 0.8398 0.664 3.418 1.934 3.437 1.68 4.512 3.164 1.074 1.484 1.074 4.316 0 3.164-0.8203 4.785-0.8203 1.602-2.754 2.48-1.914 0.8593-4.629 0.8593-3.008 0-5.156-0.9374-2.129-0.9375-2.93-2.539-0.8008-1.602-0.8008-4.844v-1.25h6.992v1.641q0 2.09 0.2539 2.715 0.2734 0.625 1.055 0.625 0.8398 0 1.172-0.4101 0.332-0.4297 0.332-1.777 0-1.855-0.4297-2.324-0.4492-0.4687-4.59-2.773-3.477-1.953-4.238-3.535-0.7617-1.602-0.7617-3.789 0-3.105 0.8203-4.57 0.8203-1.484 2.773-2.285 1.973-0.8008 4.57-0.8008 2.578 0 4.375 0.6641 1.816 0.6445 2.773 1.719 0.9766 1.074 1.172 1.992 0.2148 0.918 0.2148 2.871z" style="fill:#86b135;font-feature-settings:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-variant-numeric:normal;stroke-width:.2783"/><path d="m79.49 1387v4.082h2.129v4.102h-2.129v13.87q0 2.559 0.2539 2.852 0.2734 0.2929 2.207 0.2929v4.18h-3.184q-2.695 0-3.848-0.2148-1.152-0.2344-2.031-1.035-0.8789-0.8203-1.094-1.856-0.2148-1.055-0.2148-4.922v-13.16h-1.699v-4.102h1.699v-4.082z" style="fill:#86b135;font-feature-settings:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-variant-numeric:normal;stroke-width:.2783"/><path d="m129.4 1385v31.62h-7.891v-1.875q-1.133 1.172-2.383 1.758-1.25 0.5859-2.617 0.5859-1.836 0-3.184-0.957-1.348-0.9765-1.738-2.246-0.3711-1.27-0.3711-4.141v-12.13q0-2.988 0.3711-4.238 0.3906-1.25 1.758-2.187 1.367-0.9571 3.262-0.9571 1.465 0 2.676 0.5274 1.23 0.5273 2.227 1.582v-7.344zm-7.891 12.23q0-1.426-0.2539-1.914-0.2344-0.4883-0.957-0.4883-0.7031 0-0.957 0.4493-0.2344 0.4296-0.2344 1.953v12.7q0 1.582 0.2344 2.09 0.2344 0.4882 0.8984 0.4882 0.7617 0 1.016-0.5468 0.2539-0.5664 0.2539-2.734z" style="fill:#86b135;font-feature-settings:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-variant-numeric:normal;stroke-width:.2783"/><path d="m140.4 1385v4.121h-8.125v-4.121zm0 5.703v25.92h-8.125v-25.92z" style="fill:#86b135;font-feature-settings:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-variant-numeric:normal;stroke-width:.2783"/><path d="m150.3 1401h-7.402v-1.738q0-3.008 0.6836-4.629 0.7031-1.641 2.793-2.891t5.43-1.25q4.004 0 6.035 1.426 2.031 1.406 2.441 3.476 0.4102 2.051 0.4102 8.477v13.01h-7.676v-2.305q-0.7226 1.387-1.875 2.09-1.133 0.6835-2.715 0.6835-2.07 0-3.809-1.152-1.719-1.172-1.719-5.098v-2.129q0-2.91 0.918-3.965 0.918-1.055 4.551-2.461 3.887-1.523 4.16-2.051 0.2734-0.5274 0.2734-2.148 0-2.031-0.3125-2.637-0.293-0.625-0.9961-0.625-0.8008 0-0.9961 0.5274-0.1953 0.5078-0.1953 2.676zm2.5 3.555q-1.895 1.387-2.207 2.324-0.293 0.9374-0.293 2.695 0 2.012 0.2539 2.598 0.2734 0.5859 1.055 0.5859 0.7422 0 0.957-0.4492 0.2344-0.4687 0.2344-2.422z" style="fill:#86b135;font-feature-settings:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-variant-numeric:normal;stroke-width:.2783"/><path d="m171.4 1391-0.1367 2.383q0.8594-1.426 2.09-2.129 1.25-0.7227 2.871-0.7227 2.031 0 3.32 0.9571 1.289 0.957 1.66 2.422 0.3711 1.445 0.3711 4.844v18.16h-7.891v-17.95q0-2.676-0.1758-3.262-0.1758-0.586-0.9766-0.586-0.8398 0-1.055 0.6836-0.2148 0.6641-0.2148 3.594v17.52h-7.891v-25.92z" style="fill:#86b135;font-feature-settings:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-variant-numeric:normal;stroke-width:.2783"/></g><g transform="matrix(-2.597 0 0 -3.699 2340 5059)" style="fill:#d7680f;font-feature-settings:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-variant-numeric:normal" aria-label="CMS"><path d="m846 1262v-1.464h0.9803q0.4276 0 0.5319-0.049 0.1078-0.045 0.1078-0.2051 0-0.1807-0.1286-0.2294-0.1286-0.049-0.5562-0.049h-2.614q-0.4102 0-0.5354 0.049-0.1251 0.049-0.1251 0.219 0 0.1634 0.1251 0.2121 0.1252 0.052 0.5875 0.052h0.7057v1.464h-0.219q-0.8725 0-1.238-0.1251-0.365-0.1217-0.6396-0.5458-0.2746-0.4206-0.2746-1.039 0-0.6431 0.2329-1.06 0.2329-0.4171 0.6431-0.5527 0.4137-0.1356 1.241-0.1356h1.644q0.6084 0 0.9108 0.042 0.3059 0.042 0.5875 0.2469 0.2816 0.2085 0.4415 0.5735 0.1634 0.3685 0.1634 0.8448 0 0.6466-0.2503 1.067t-0.6257 0.5527q-0.372 0.1321-1.161 0.1321z" style="fill:#d7680f;font-feature-settings:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-variant-numeric:normal;stroke-width:.1581"/><path d="m848.5 1267h-5.628v-1.279h3.8l-3.8-0.511v-0.9073l3.713-0.5388h-3.713v-1.279h5.628v1.895q-0.5075 0.083-1.196 0.1773l-1.432 0.2016 2.628 0.3337z" style="fill:#d7680f;font-feature-settings:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-variant-numeric:normal;stroke-width:.1581"/><path d="m846.8 1271v-1.359h0.4172q0.292 0 0.372-0.052 0.0799-0.052 0.0799-0.1738 0-0.1321-0.1078-0.2016-0.1078-0.066-0.3268-0.066-0.2816 0-0.4241 0.076-0.1425 0.073-0.3442 0.4137-0.5805 0.9768-0.9525 1.231t-1.199 0.2538q-0.6014 0-0.8864-0.1426-0.285-0.139-0.4797-0.5422-0.1912-0.4033-0.1912-0.9386 0-0.5875 0.2225-1.005 0.2225-0.4137 0.5666-0.5423t0.9768-0.1286h0.3685v1.359h-0.6848q-0.3163 0-0.4067 0.056-0.0904 0.059-0.0904 0.2051 0 0.146 0.1147 0.2156 0.1147 0.073 0.3407 0.073 0.4971 0 0.6501-0.1356 0.153-0.1391 0.511-0.6848 0.3615-0.5458 0.5249-0.7231 0.1634-0.1773 0.4519-0.2955 0.2885-0.1147 0.737-0.1147 0.6466 0 0.9455 0.1634 0.299 0.1669 0.4658 0.5353 0.1703 0.3685 0.1703 0.89 0 0.5701-0.1842 0.9698-0.1842 0.4033-0.4658 0.5319-0.2781 0.1321-0.949 0.1321z" style="fill:#d7680f;font-feature-settings:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-variant-numeric:normal;stroke-width:.1581"/></g></g></g></g></svg>
		</a>
		<main style="margin:0 auto;max-width:500px;position:unset;top:unset">
			<?php if(!empty($ccms_auth_token_error["FAIL"])): ?>
					<div class="alertDiv fail">
						<?php echo $ccms_auth_token_error["FAIL"]; ?>
					</div>
			<?php endif ?>
			<div class="formDiv">
				<div>2-Factor Authentication (2FA)</div>
				<div style="padding:10px">
					<p style="margin-bottom:10px">Please enter your 2FA token.</p>
					<form action="/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/" class="aGrid" id="ccms_auth_form" method="post" novalidate="novalidate">
						<input type="hidden" name="ccms_auth_token_login" value="1">
						<label for="ccms_auth_token">Token <span class="rd">*</span></label>
						<input id="ccms_auth_token" name="ccms_auth_token" placeholder="6 Diget Code" type="text">
						<label id="ccms_auth_token_error" class="error" for="ccms_auth_token" style="display:none"></label>
						<button id="submit" type="submit">Submit</button>
					</form>
					<p>Unable to access your 2FA app or cell phone?  For now, contact your website adminstrator or developer and request it be reset or removed temporarly from your account.</p>
				</div>
			</div>
		</main>
		<script nonce="{CCMS_LIB:_default.php;FUNC:ccms_csp_nounce}">
			{CCMS_TPL:/_js/footer-1.php}

			var l=document.createElement("link");l.rel="stylesheet";
			l.href = "/ccmsusr/_css/custodiancms.css";
			var h=document.getElementsByTagName("head")[0];h.parentNode.insertBefore(l,h);

			function loadJSResources() {
				loadFirst("/ccmsusr/_js/jquery-3.6.0.min.js", function() {
					loadFirst("/ccmsusr/_js/custodiancms.js", function() {
						loadFirst("/ccmsusr/_js/jquery-validate-1.19.3.min.js", function() {
							$("#ccms_auth_form").validate({
								rules:{
									ccms_auth_token:{
										minlength:6,
										maxlength:6,
										number:true,
										required:true
									}
								},
								messages:{
									ccms_auth_token:{
										minlength:"Too short",
										maxlength:"Too long",
										number:"Numbers only, no letters or spaces",
										required:"2AF Code required"
									}
								},
								errorPlacement:function($error, $element){
									var name = $element.attr("name");
									$("#"+name+"_error").append($error);
								}
							});
						});
					});
				});
			}
		</script>
	</body>
</html>
