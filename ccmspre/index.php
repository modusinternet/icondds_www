<?php
/*************************************************************
References:
http://www.regular-expressions.info/unicode.html
NOTE: To match a letter including any diacritics, use \p{L}\p{M}*+.
An extensive list of regular expression examples:
http://www.roscripts.com/PHP_regular_expressions_examples-136.html
Another great reference is:
https://www.autohotkey.com/docs/misc/RegEx-QuickRef.htm
The online tester that I use most often is:
https://regex101.com/

A list of predefined PHP constants for use with the filter_var() function can be found here: http://ca2.php.net/manual/en/filter.constants.php
**************************************************************/

define('CRYPT', '/^[a-z\-_\/#=&:\pN\?\.\";\'\`\*\s]*\z/i');
define('HTTP_COOKIE', '/^[a-z\pN\-_=\.; \/]{2,}\z/i');
define('HTTP_USER_AGENT', '/^[a-z\pN\-_;:,.()#\/\+ ]{2,}\z/i');
define('LNG', '/^[a-z]{2,3}(-[a-z0-9]{2,3})?\z/i');
define('PARMS', '/^[a-z\-_\pN\/]+\z/i');
define('QUERY_STRING', '/^[a-z\pN\-_=&\?\.\/]{1,}\z/i');
define('SESSION_ID', '/^[a-z\pN]{1,}\z/i');
define('TPL', '/^[a-z\-_\pN\.\/]*\z/i');

define('UTF8_STRING_WHITE', '/^[\pL\pM*+\s]*\z/u');
// ^ Start of line
// [ Starts the character class.
// \pL Any kind of letter from any language.
// \pM*+	Matches zero or more code points that are combining marks.  A character intended to be combined with another character (e.g. accents, umlauts, enclosing boxes, etc.).
// \s Whitespaces.
// ] Ends the character class.
// * zero or more
// \z End of subject or newline at end. (Better then $ because $ does not include /n characters at the end of a line.)
// /u Pattern strings are treated as UTF-8

define('UTF8_STRING_DIGIT_WHITE', '/^[\pL\pM*+\pN\s]*\z/u');
// ^ Start of line
// [ Starts the character class.
// \pL Any kind of letter from any language.
// \pM*+	Matches zero or more code points that are combining marks.  A character intended to be combined with another character (e.g. accents, umlauts, enclosing boxes, etc.).
// \pN Any number.
// \s Whitespaces.
// ] Ends the character class.
// * zero or more
// \z End of subject or newline at end. (Better then $ because $ does not include /n characters at the end of a line.)
// /u Pattern strings are treated as UTF-8

define('UTF8_STRING_DIGIT_PUNC_WHITE', '/^[\pL\pM*+\pN\pP\s]*\z/u');
// ^ Start of line
// [ Starts the character class.
// \pL Any kind of letter from any language.
// \pM*+	Matches zero or more code points that are combining marks.  A character intended to be combined with another character (e.g. accents, umlauts, enclosing boxes, etc.).
// \pN Any number.
// \pP Punctuation. (*** Does not include ~$^+=|<> symbols. ***)
// \s Whitespaces.
// ] Ends the character class.
// * zero or more
// \z End of subject or newline at end. (Better then $ because $ does not include /n characters at the end of a line.)
// /u Pattern strings are treated as UTF-8

define('WHOLE_NUMBER', '/^[\pN]*\z/');
// ^		Start of line
// [		Starts the character class.
// \pN	Any number.
// ]		Ends the character class.
// *		Zero or more
// \z		End of subject or newline at end. (Better then $ because $ does not include /n characters at the end of a line.)
// /		End of the Pattern.

$ccms_whitelist = array(
	"ccms_lngSelect"	=> array("type" => "LNG",						"maxlength" => 5),
	"ccms_parms"		=> array("type" => "PARMS",						"maxlength" => 128),
	"ccms_tpl"			=> array("type" => "TPL",						"maxlength" => 256),
	"ccms_session"		=> array("type" => "SESSION_ID",				"maxlength" => 64),
	"ccms_cid"			=> array("type" => "SESSION_ID",				"maxlength" => 64),
	"ccms_lng"			=> array("type" => "LNG",						"maxlength" => 5),
	"ccms_token"		=> array("type" => "UTF8_STRING_DIGIT_WHITE",	"maxlength" => 64),
	"HTTP_COOKIE"		=> array("type" => "HTTP_COOKIE",				"maxlength" => 512),
	"HTTP_USER_AGENT"	=> array("type" => "HTTP_USER_AGENT",			"maxlength" => 512),
	"QUERY_STRING"		=> array("type" => "QUERY_STRING",				"maxlength" => 1024)
);


function CCMS_Set_Headers(){
	global $CFG, $CLEAN;

	$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_headers` WHERE `status` = 1;");
	$qry->execute();
	$qry->setFetchMode(PDO::FETCH_ASSOC);

	while($row = $qry->fetch()) {
		if($row["name"] === "Content-Security-Policy"){
			$CFG["nonce"] = hash("sha256", rand());
			//if($CLEAN["ccms_tpl"] === "search"){
			if($CLEAN["ccms_tpl"] === "/search.php"){
				// This helps make sure Google's Custom Search Engine (CSE) will work properly on the search template.
				// NOTE: this presumes that your search template is located in the ccmstpl folder and is named 'search.php'.  'search.html' will not fail to add 'unsafe-eval' to your headers properly for this page to function.
				$search = array(
					"{UNSAFE-EVAL}",
					"{NONCE}"
				);
				$replace = array(
					" 'unsafe-eval'",
					$CFG["nonce"]
				);
				$row["value"] = str_replace($search, $replace, $row["value"]);
			} else {
				$search = array(
					"{UNSAFE-EVAL}",
					"{NONCE}"
				);
				$replace = array(
					"",
					$CFG["nonce"]
				);
				$row["value"] = str_replace($search, $replace, $row["value"]);
			}
			header($row["name"] . ": " . $row["value"]);
		} else {
			header($row["name"] . ": " . $row["value"]);
		}
	}
}


function CCMS_Set_LNG() {
	global $CFG, $CLEAN;

	$CFG["lngCodeFoundFlag"] = false;
	$CFG["lngCodeActiveFlag"] = false;

	if(isset($_SESSION["LNG"]) && !isset($CLEAN["ccms_lng"])) {

		// This might happen if the visitor has been to the site before and a language was correctly set in the SESSION but the website designer made links that return to the homepage without a language variable/dir.  In this case we need to grab the known language preference of the visitor from the session variable and copy it to the ccms_lng argument because it will be needed later on.  ie: https://abc.org as apposed to https://abc.org/en/
		$CLEAN["ccms_lng"] = $_SESSION["LNG"];
	}

	if(isset($CLEAN["ccms_lng"]) && $CLEAN["ccms_lng"] !== "MAXLEN" && $CLEAN["ccms_lng"] !== "INVAL") {

		// Make sure whatever language value currenlty inside the ccms_lng arg is also found in the SESSION["LNG"].
		$_SESSION["LNG"] = $CLEAN["ccms_lng"];

		// A language variable was found in the $_SESSION["LNG"], URI or POST arguments.
		foreach($CFG["CCMS_LNG_CHARSET"] as $key => $value) {
			//if($key === $CLEAN["ccms_lng"]) {
			if(strcasecmp($key, $CLEAN["ccms_lng"]) == 0) {
				// The $CLEAN["ccms_lng"] language code was found in the database.
				$CFG["lngCodeFoundFlag"] = true;
				if($value["status"] == "1") {
					// The language code provided is active in the database.
					$CFG["CCMS_LNG_DIR"] = $value["dir"];
					$CFG["lngCodeActiveFlag"] = true;
				} elseif(isset($_SESSION["USER_ID"])) {
					// If this is a verified user trying to make changes to content in a language which is currently not set live, get the users privilages and verify their rights to make updates in the language.
					/*
					$qry = $CFG["DBH"]->prepare("SELECT super, priv FROM `ccms_user` WHERE id = :user_id LIMIT 1;");
					$qry->execute(array(':user_id' => $_SESSION["USER_ID"]));
					$row = $qry->fetch(PDO::FETCH_ASSOC);
					$json_a = json_decode($row["priv"], true);
					$_SESSION["SUPER"] = $row["super"];

					if($row["super"] == "1" || $json_a["priv"]["content_manager"]["r"] == 1) {
						if($row["super"] == "1" || $json_a["priv"]["content_manager"]["lng"][$key] == 1 || $json_a["priv"]["content_manager"]["lng"][$key] == 2) {
							$CFG["CCMS_LNG_DIR"] = $value["dir"];
							$CFG["lngCodeActiveFlag"] = true;
						}
					}
					*/

					$privArray = json_decode($_SESSION["PRIV"], true);
					if($_SESSION["SUPER"] == "1") {
						// Super users can do anything.
						$CFG["CCMS_LNG_DIR"] = $value["dir"];
						$CFG["lngCodeActiveFlag"] = true;
					} elseif($privArray["content_manager"]["sub"][$key] != 0) {
						// So long as you have a valid USER_ID and you are permitted to at least read and or write content in the language reguested it's cool to display.
						$CFG["CCMS_LNG_DIR"] = $value["dir"];
						$CFG["lngCodeActiveFlag"] = true;
					} else {
						// Because they have no access to the language they want to work in and they are not super users, we change all their language output to the default language instead.
						$CFG["lngCodeFoundFlag"] = true;
						$CLEAN["ccms_lng"] = $CFG["DEFAULT_SITE_CHAR_SET"];
						$_SESSION["LNG"] = $CFG["DEFAULT_SITE_CHAR_SET"];
						$CFG["CCMS_LNG_DIR"] = $CFG["DEFAULT_SITE_CHAR_SET_DIR"];
						$CFG["lngCodeActiveFlag"] = true;
					}
				}
				break;
			}
		}
	} elseif(($_SERVER["HTTP_ACCEPT_LANGUAGE"] ?? null) !== "") {
		// Something was found in the HTTP_ACCEPT_LANGUAGE variable.

		preg_match_all("/[a-z]{2,3}(-[a-z0-9]{2,3})?/i", $_SERVER["HTTP_ACCEPT_LANGUAGE"], $matches);

		foreach($matches[0] as $match) {
			foreach($CFG["CCMS_LNG_CHARSET"] as $key => $value) {
				if(strcasecmp($key, $match) == 0) {
					// The language code provided was found in the database.
					$CFG["lngCodeFoundFlag"] = true;
					if($value["ptrLng"]) {
						// The language code found is a pointer to another language code in the database.
						if($CFG["CCMS_LNG_CHARSET"][$value["ptrLng"]]["status"] === "1") {
							// The other language code pointed to is active in the database.
							$CLEAN["ccms_lng"] = $value["ptrLng"];
							$_SESSION["LNG"] = $value["ptrLng"];
							$CFG["CCMS_LNG_DIR"] = $CFG["CCMS_LNG_CHARSET"][$value["ptrLng"]]["dir"];
							$CFG["lngCodeActiveFlag"] = true;
							break 2;
						}
					} elseif($value["status"] === "1") {
						// The language code provided is active in the database.
						$CLEAN["ccms_lng"] = $key;
						$_SESSION["LNG"] = $key;
						$CFG["CCMS_LNG_DIR"] = $value["dir"];
						$CFG["lngCodeActiveFlag"] = true;
						break 2;
					}
				}
			}
		}
	}

	if(!isset($CLEAN["ccms_lng"])) {
		// There was nothing in $_SESSION["LNG"], the $CLEAN["ccms_lng"] variable was empty, MAXLEN or INVAL, and $_SERVER["HTTP_ACCEPT_LANGUAGE"] variable was empty or invalid.  So we will pull the default language set from the database.
		$CFG["lngCodeFoundFlag"] = true;
		$CLEAN["ccms_lng"] = $CFG["DEFAULT_SITE_CHAR_SET"];
		$_SESSION["LNG"] = $CFG["DEFAULT_SITE_CHAR_SET"];
		$CFG["CCMS_LNG_DIR"] = $CFG["DEFAULT_SITE_CHAR_SET_DIR"];
		$CFG["lngCodeActiveFlag"] = true;
	}
}


function CCMS_Set_SESSION() {
	global $CFG, $CLEAN;

	//ini_set('session.use_only_cookies', 1);
	// By setting this directive cookies are used as the mandatory storage to preserve session id. It prevents session hijacking.

	//ini_set('session.cookie_lifetime', $CFG["COOKIE_SESSION_EXPIRE"]);
	// This is used to set cookie lifetime. If it is set as 0, then cookie remains until browser restart.

	ini_set('session.cookie_httponly', 1);
	// This directive stops client side scripts from accessing session id preserved in cookie.

	ini_set('session.cookie_secure', 1);
	// Controls whether cookies are sent via secure connections or not. Set it with 1 | 0 value. The default (off) is 0.

	ini_set('session.cookie_samesite', "Strict");

	session_name("__Host-ccms_session");

	// Check to see if a session has already been started.
	if(PHP_VERSION_ID >= 50400) {
		// PHP version 5.4.0 or higher

		if(session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	} else {
		// PHP version 5.3.9 or lower

		if(session_id() == '') {
			session_start();
		}
	}

	if(isset($_SESSION['START_TIME'])) {
		// The time of the visitors last page load is known.

		$duration = time() - (int)$_SESSION['START_TIME'];
		// How long ago was it?

		if($duration >= $CFG["COOKIE_SESSION_EXPIRE"]) {
			// It was too long ago.

			if(isset($_SESSION["USER_ID"])) {
				// The user was logged in but their session is now expired so send them back to the login page.

				///*
				if($CFG["LOG_EVENTS"] === 1) {
					// Save a log of this event.

					$qry = $CFG["DBH"]->prepare("INSERT INTO `ccms_log` (`id`, `date`, `ip`, `url`, `log`) VALUES (NULL, :date, :ip, :url, :log);");
					$qry->execute(array(':date' => time(), ':ip' => $_SERVER["REMOTE_ADDR"], ':url' => $_SERVER["REQUEST_URI"], ':log' => "User ID (".$_SESSION["USER_ID"].") session expired, redirected to login page.\n".$_SERVER["HTTP_USER_AGENT"]));
				}
				//*/

				// log out
				$_SESSION = array();
				$_SESSION['EXPIRED'] = "1";
				//header("Location: /" . $CFG["DEFAULT_SITE_CHAR_SET"] . "/user/");

				if($CLEAN["ajax_flag"] == 1) {
					// If this call contains an Ajax flag set to '1' we don't actually want to send them to the login page, we'll just send a session expired message instead.

					header("Content-Type: application/javascript; charset=UTF-8");
					// NOTE: If the template is later called using a serviceWorker be aware that will not respect the settings of the 'cache-control' header as noted in here: https://web.dev/service-workers-cache-storage/#api-nuts-and-bolts

					header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
					header("Cache-Control: post-check=0, pre-check=0", false);
					header("Pragma: no-cache");
					//echo "/* Session Error */";
					echo '{"error":"Session Error"}';
				} else {
					header("Location: /" . $CFG["DEFAULT_SITE_CHAR_SET"] . "/user/");
				}

				exit;
			} else {
				// This visitor is not logged in.

				$_SESSION['EXPIRED'] = "1";
				$_SESSION['START_TIME'] = time();
			}
		} else {
			// The visitors session is not too old, update start time.

			$_SESSION['START_TIME'] = time();
		}
	} else {
		// The time of the visitors last page load is unknown. Possibly their first visit.

		$_SESSION['START_TIME'] = time();
	}

	if(isset($_SESSION["HTTP_USER_AGENT"])) {
		// The $_SESSION['HTTP_USER_AGENT'] variable is only set when a user is fully logged in to help strengthen session security. It is not necessary to double check at any other time.

		if($_SESSION["HTTP_USER_AGENT"] != md5($_SERVER["HTTP_USER_AGENT"])) {
			// Possible session highjacking attempt, destroy the session and restart it but direct logged in users to relogin.

			if($CFG["LOG_EVENTS"] === 1) {
				// Save a log of this event.

				$qry = $CFG["DBH"]->prepare("INSERT INTO `ccms_log` (`id`, `date`, `ip`, `url`, `log`) VALUES (NULL, :date, :ip, :url, :log);");
				$qry->execute(array(':date' => time(), ':ip' => $_SERVER["REMOTE_ADDR"], ':url' => $_SERVER["REQUEST_URI"], ':log' => "User ID (".$_SESSION["USER_ID"].") under possible session highjacking attempt.  The Session and Server HTTP_USER_AGENT's do not match.  Therefor, the session has been deleted and the user redirected to the login page.\n".$_SERVER["HTTP_USER_AGENT"]."\nNOTE: This type of error can also be accidentilly generated by users testing pages. (Making media souce and resolution changes in developer mode.)"));
			}

			// log out
			$_SESSION = array();
			$_SESSION['EXPIRED'] = "1";
			//header("Location: /" . $CFG["DEFAULT_SITE_CHAR_SET"] . "/user/");

			if($CLEAN["ajax_flag"] == 1) {
				// If this call contains an Ajax flag set to '1' we don't actually want to send them to the login page, we'll just send a session expired message instead.

				header("Content-Type: application/javascript; charset=UTF-8");
				// NOTE: If the template is later called using a serviceWorker be aware that will not respect the settings of the 'cache-control' header as noted in here: https://web.dev/service-workers-cache-storage/#api-nuts-and-bolts

				header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");
				//echo "/* Session Error */";
				echo '{"error":"Session Error"}';
			} else {
				header("Location: /" . $CFG["DEFAULT_SITE_CHAR_SET"] . "/user/");
			}

			exit;
		}
	}

	if(isset($_SESSION["USER_ID"])) {
		$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_user` WHERE `id` = :id && `status` = 1 LIMIT 1;");
		$qry->execute(array(':id' => $_SESSION["USER_ID"]));
		$row = $qry->fetch(PDO::FETCH_ASSOC);

		if($row) {
			// User 'status' is still valid

			if(!empty($row["2fa_secret"])) {
				// The user is valid but has enabled 2 factor authentication.

				if(!isset($_SESSION["2FA_VALID"])) {
					// The user is logged in successfully but they have 2FA enabled and haven't verified it yet.

					$CLEAN["ccms_tpl"] = "/authenticator.php";
				}
			} else {
				// The user is valid and nothing is outstanding so just update the most current privilages.

				$_SESSION["2FA_VALID"] = null;
				$_SESSION["ALIAS"] = $row["alias"];
				$_SESSION["PRIV"] = $row["priv"];
				$_SESSION["SUPER"] = $row["super"];
			}
		} else {
			// Looks like they were properly logged in at one point but their account has either been removed or 'status' is set to '0' now.

			if($CFG["LOG_EVENTS"] === 1) {
				$qry = $CFG["DBH"]->prepare("INSERT INTO `ccms_log` (`id`, `date`, `ip`, `url`, `log`) VALUES (NULL, :date, :ip, :url, :log);");
				$qry->execute(array(':date' => time(), ':ip' => $_SERVER["REMOTE_ADDR"], ':url' => $_SERVER["REQUEST_URI"], ':log' => "User ID (".$_SESSION["USER_ID"].") was properly logged in at one point but their 'status' is set to '0' now.  Session deleted and user redirected to login page.\n\n".$_SERVER["HTTP_USER_AGENT"]));
			}

			// log out
			$_SESSION = array();
			$_SESSION['EXPIRED'] = "1";

			if($CLEAN["ajax_flag"] == 1) {
				// If this call contains an Ajax flag set to '1' we don't actually want to send them to the login page, we'll just send a session expired message instead.

				header("Content-Type: application/javascript; charset=UTF-8");
				// NOTE: If the template is later called using a serviceWorker be aware that will not respect the settings of the 'cache-control' header as noted in here: https://web.dev/service-workers-cache-storage/#api-nuts-and-bolts

				header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");
				//echo "/* Session Error */";
				echo '{"error":"Session Error"}';
			} else {
				header("Location: /" . $CFG["DEFAULT_SITE_CHAR_SET"] . "/user/");
			}
			exit;
		}
	}
	session_regenerate_id();
}


function CCMS_DB_First_Connect() {
	global $CFG, $CLEAN;

	$CLEAN["CCMS_DB_Preload_Content"] = array();
	$host = $CFG["DB_HOST"];
	$dbname = $CFG["DB_NAME"];
	$user = $CFG["DB_USERNAME"];
	$pass = $CFG["DB_PASSWORD"];

	try {
		// MSSQL
		// $CFG["DBH"] = new PDO("mssql:host=$host;dbname=$dbname, $user, $pass");
		// MySQL
		$CFG["DBH"] = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, array(PDO::ATTR_PERSISTENT => true));
		// SQLite
		// $CFG["DBH"] = new PDO("sqlite:my/database/path/database.db");
		// Sybase
		// $CFG["DBH"] = new PDO("sybase:host=$host;dbname=$dbname, $user, $pass");
		// Sets encoding UTF-8
		/*
			Great sites talking about how to handle the utf-8 character sets properly:
			https://www.toptal.com/php/a-utf-8-primer-for-php-and-mysql
			https://mathiasbynens.be/notes/mysql-utf8mb4
		*/
		$CFG["DBH"]->exec("set names utf8mb4");
		$CFG["DBH"]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		if($CFG["DEBUG_SQL"] == 1) {
			echo "Error!: " . $e->getCode() . '<br>\n'. $e->getMessage();
		}
		die();
	}

	$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_lng_charset`;");
	$qry->execute();
	$qry->setFetchMode(PDO::FETCH_ASSOC);

	while($row = $qry->fetch()) {
		$CFG["CCMS_LNG_CHARSET"][$row["lng"]]["lngDesc"] = $row["lngDesc"];
		$CFG["CCMS_LNG_CHARSET"][$row["lng"]]["status"] = $row["status"];
		$CFG["CCMS_LNG_CHARSET"][$row["lng"]]["default"] = $row["default"];
		$CFG["CCMS_LNG_CHARSET"][$row["lng"]]["dir"] = $row["dir"];
		$CFG["CCMS_LNG_CHARSET"][$row["lng"]]["ptrLng"] = $row["ptrLng"];
	}

	// Pars the $CFG["CCMS_LNG_CHARSET"] table for the default character set and it's direction.
	foreach($CFG["CCMS_LNG_CHARSET"] as $key => $value) {
		if($value["default"] == 1) {
			$CFG["DEFAULT_SITE_CHAR_SET"] = $key;
			$CFG["DEFAULT_SITE_CHAR_SET_DIR"] = $value["dir"];
			break;
		}
	}
}


function CCMS_Filter($input, $whitelist) {
	global $CLEAN;

	foreach($input as $key => $value) {
		if(array_key_exists($key, $whitelist)) {
			$buf = null;
			$value = @trim($value);
			// utf8_decode() converts unknown ISO-8859-1 chars to '?' for the purpose of counting.
			$length = strlen(utf8_decode($value));
			if(isset($whitelist[$key]['minlength']) && ($length < $whitelist[$key]['minlength'])) {
				$buf = "MINLEN";
			}
			if(isset($whitelist[$key]['maxlength']) && ($length > $whitelist[$key]['maxlength'])) {
				$buf = "MAXLEN";
			}
			if($buf != "MINLEN" && $buf != "MAXLEN") {
				switch($whitelist[$key]['type']) {
					case "CRYPT":
						$value = stripslashes(rawurldecode($value));
						$buf = (preg_match(CRYPT, $value)) ? $value : "INVAL";
						break;
					case "HTTP_COOKIE":
						$buf = (preg_match(HTTP_COOKIE, $value)) ? $value : "INVAL";
						break;
					case "HTTP_USER_AGENT":
						$buf = (preg_match(HTTP_USER_AGENT, $value)) ? $value : "INVAL";
						break;
					case "LNG":
						$buf = (preg_match(LNG, $value)) ? $value : "INVAL";
						break;
					case 'OPTION':
						if(is_array($value)) {
							if($whitelist[$key]['multiselect']) {
								$buf = array();
								foreach($value as $option) {
									if(in_array($option, $whitelist[$key]['options'])) {
										$buf[] = $option;
									}
								}
							}
						} else {
							$buf = in_array($value, $whitelist[$key]['options']) ? $value : "INVAL";
						}
						break;
					case "QUERY_STRING":
						$buf = (preg_match(QUERY_STRING, $value)) ? $value : "INVAL";
						break;
					case "SESSION_ID":
						$buf = (preg_match(SESSION_ID, $value)) ? $value : "INVAL";
						break;
					case "TPL":
						$buf = (preg_match(TPL, $value)) ? $value : "INVAL";
						break;
					case "UTF8_STRING_WHITE":
						$buf = (preg_match(UTF8_STRING_WHITE, $value)) ? $value : "INVAL";
						break;
					case "UTF8_STRING_DIGIT_WHITE":
						$buf = (preg_match(UTF8_STRING_DIGIT_WHITE, $value)) ? $value : "INVAL";
						break;
					case "UTF8_STRING_DIGIT_PUNC_WHITE":
						$buf = (preg_match(UTF8_STRING_DIGIT_PUNC_WHITE, $value)) ? $value : "INVAL";
						break;
					case "WHOLE_NUMBER":
						$buf = (preg_match(WHOLE_NUMBER, $value)) ? $value : "INVAL";
						break;
				}
			}
			$CLEAN[$key] = $buf;
		}
	}
}


function CCMS_DB($a) {
	global $CFG, $CLEAN;

	if(isset($CLEAN["CCMS_DB_Preload_Content"])) {

		if(isset($CLEAN["CCMS_DB_Preload_Content"][$a[2]]) === true && isset($CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]]) === true) {
			// Confirm "grp" ($a[2]) and "name" ($a[3]) were found in the database together and thier content have been stored here in this large array for use.  This only fails when a website developer has been working on an html template, adding entires that will eventually refer to content that needs to be pulled from the database, but has not actuall been added yet. (To the ccms_ins_db table.)

			if(!empty($CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["content"])) {
				echo CCMS_TPL_Parser($CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["content"]);
			} else {
				echo CCMS_TPL_Parser($CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CFG["DEFAULT_SITE_CHAR_SET"]]["content"]);
			}
		} else {
			echo "MISSING:";
			$tmp = (isset($CLEAN["CCMS_DB_Preload_Content"][$a[2]]) === false) ? " grp '$a[2]'" : "";
			$tmp = (isset($CLEAN["CCMS_DB_Preload_Content"][$a[3]]) === false) ? " grp '$a[2]' with name '$a[3]'" : "";
			echo (empty($tmp)) ? " Not found in 'ccms_ins_db' table." : $tmp . " not found in 'ccms_ins_db' table.";
		}
	} else {
		echo $a[0] . " ERROR: Either CCMS_DB_Preload function was not called or the CCMS_DB_PRELOAD tag was not found on your template prior to calling this CCMS_DB tag. ";
	}
}


function CCMS_DB_Dir($a) {
	global $CFG, $CLEAN;

	if(isset($CLEAN["CCMS_DB_Preload_Content"])) {
		if(($a[5] ?? null) === "1") {
			// Make editable on the public side.



			//if($CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["content"] ?? null) {
			if($CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["content"] !== "") {
				echo $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["dir"] . "\" data-ccms=\"" . $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["id"] . "\" data-ccms-grp=\"" . $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["grp"] . "\" data-ccms-name=\"" . $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["name"];
			} else {
				echo $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CFG["DEFAULT_SITE_CHAR_SET"]]["dir"] . "\" data-ccms=\"" . $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["id"] . "\" data-ccms-grp=\"" . $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["grp"] . "\" data-ccms-name=\"" . $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["name"];
			}

			if(isset($_SESSION["USER_ID"])) {
				$json_a = json_decode($_SESSION["PRIV"], true);
				echo '" data-ccms-rw="' . $json_a["content_manager"]["sub"][$CLEAN["ccms_lng"]];
			}












		} else {
			// Not editable on the public side.
			if($CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["content"] != "") {
				echo $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["dir"];
			} else {
				echo $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CFG["DEFAULT_SITE_CHAR_SET"]]["dir"];
			}
		}
	} else {
		echo $a[0] . " ERROR: Either CCMS_DB_Preload function was not called or the CCMS_DB_PRELOAD tag was not found on your template prior to calling this CCMS_DB_DIR tag. ";
	}
}


function CCMS_DB_Preload($a = null) {
	global $CFG, $CLEAN;

	/*
	This function can be called in two different ways:
	$content = CCMS_DB_Preload("about_us_filter,footer_filter,header_filter,twiter_feed_filter");
	or
	{CCMS_DB_PRELOAD:about_us_filter,footer_filter,header_filter,twiter_feed_filter}
	*/

	if($a[2]) {
		$grpArray = explode(",", $a[2]);
		$grp = "";
		foreach($grpArray as $key) {
			if($grp != "") {
				$grp .= " OR ";
			}
			$grp .= "`grp` = '" . $key . "'";
		}
		// `access` = '0'; www side
		// `access` = '1'; admin side
		$query = "SELECT * FROM `ccms_ins_db` WHERE `status` = '1' AND `access` = '0' AND (" . $grp . ");";
	} else {
		$query = "SELECT * FROM `ccms_ins_db` WHERE `status` = '1' AND `access` = '0';";
	}
	$qry = $CFG["DBH"]->prepare($query);
	$qry->execute();
	$qry->setFetchMode(PDO::FETCH_ASSOC);

	while($row = $qry->fetch()) {
		$CLEAN["CCMS_DB_Preload_Content"][$row["grp"]][$row["name"]][$CFG["DEFAULT_SITE_CHAR_SET"]]["id"] = $row["id"];
		$CLEAN["CCMS_DB_Preload_Content"][$row["grp"]][$row["name"]][$CFG["DEFAULT_SITE_CHAR_SET"]]["content"] = $row[$CFG["DEFAULT_SITE_CHAR_SET"]];
		$CLEAN["CCMS_DB_Preload_Content"][$row["grp"]][$row["name"]][$CFG["DEFAULT_SITE_CHAR_SET"]]["dir"] = $CFG["DEFAULT_SITE_CHAR_SET_DIR"];
		$CLEAN["CCMS_DB_Preload_Content"][$row["grp"]][$row["name"]][$CFG["DEFAULT_SITE_CHAR_SET"]]["grp"] = $row["grp"];
		$CLEAN["CCMS_DB_Preload_Content"][$row["grp"]][$row["name"]][$CFG["DEFAULT_SITE_CHAR_SET"]]["name"] = $row["name"];
		$CLEAN["CCMS_DB_Preload_Content"][$row["grp"]][$row["name"]][$CLEAN["ccms_lng"]]["id"] = $row["id"];
		$CLEAN["CCMS_DB_Preload_Content"][$row["grp"]][$row["name"]][$CLEAN["ccms_lng"]]["content"] = $row[$CLEAN["ccms_lng"]];
		$CLEAN["CCMS_DB_Preload_Content"][$row["grp"]][$row["name"]][$CLEAN["ccms_lng"]]["dir"] = $CFG["CCMS_LNG_DIR"];
		$CLEAN["CCMS_DB_Preload_Content"][$row["grp"]][$row["name"]][$CLEAN["ccms_lng"]]["grp"] = $row["grp"];
		$CLEAN["CCMS_DB_Preload_Content"][$row["grp"]][$row["name"]][$CLEAN["ccms_lng"]]["name"] = $row["name"];
	}
}


function CCMS_html_min($buffer) {
	global $CFG, $CLEAN;

	/*
	Enable and Disable this feature in config.php.
		$CFG["HTML_MIN"] = 0; // off
		$CFG["HTML_MIN"] = 1; // on (Default)

	This code will not break pre, code or textarea tagged content.

	WARNING: Make sure your actual HTML templates do not contain any commented // code because minification means all whitespaces will be removed and the carriage return at the end of your comment will also be removed, making everything that comes after that a commented comment aswell.
	*/

	if($CFG["HTML_MIN"] === 1 && (!isset($_SESSION["USER_ID"]))) {
		// If HTML_MIN is set to 1 in the config file and this is a normal session and the user is not logged in.

		$search = array("\r\n", "\n", "\t");
		$replace = array("{CHAR_RET}", "{CHAR_RET}", "{CHAR_TAB}");
		$buffer = preg_replace_callback(
			"/<(pre|code|textarea)\s?.*?>(.*?)<\/(pre|code|textarea)>/msi",
			function ($matches) use (&$search, &$replace) {
				return str_replace($search, $replace, $matches[0]);
			},
			$buffer
		);

		$buffer = preg_replace(["/<!--.*?-->/s","/\/\*.*?\*\//s","/[[:cntrl:]]+/s","/ {2,}/s"],["","",""," "],$buffer);
		$buffer = preg_replace(["/\{CHAR_RET\}/","/\{CHAR_TAB\}/"],["\n","\t"],$buffer);
	}
	return $buffer;
}


function CCMS_TPL_Insert($a) {
	global $CFG;

	/*
	Test to see if CLEAN["ccms_tpl"] file being requested is stored on the server with a .htm, .html, .php, .tpl, .txt, .xml or .xsl extension.  .php is tested for first, if found it is pre-parsed by php, stored in a buffer and then submitted to the CMS system for further parsing.  If any other extension found it is sent immediately for parsing.

	NOTE: The filenames are returned in the order in which they are stored by the file system.
	NOTE  About file_get_contents() on Windows servers, the case of a filename is not important, however on UNIX/LINUX systems case is very important.  If you have a file on your system you are looking for is not typed if with the proper case it will reselt in an error.  Just make sure you always lowercase all your URL's and template names for safety.

	WARNING: It is recommended that you do NOT store two files of the same name with different extensions in the same directory at the same time.  You'll save yourself from pulling out all your hair trying to figure out why the newer file simply isn't being called.  In these cases it's best to remove the original and replace with the new file extension all together.
	*/

	//if(preg_match('/\.php\z/i', $a[2])) {
	if(preg_match("/\.php\z/i", $a[3])) {
		ob_start();
		//include $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/" . $a[2];
		include $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . $a[2];
		$buf = ob_get_contents();
		ob_end_clean();
		echo CCMS_TPL_Parser($buf);
	//} elseif(preg_match('/\.html\z/i', $a[2])) {
	} elseif(preg_match("/\.html\z/i", $a[3])) {
		if(($buf = @file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/" . $a[2])) !== false) {
			echo CCMS_TPL_Parser($buf);
		} else {
			echo $a[0] . " ERROR: CCMS_TPL '" . $a[2] . "' not performed.  Be sure the file exists and ends in a .html extention.";
		}
	} else {
		echo $a[0] . " ERROR: CCMS_TPL '" . $a[2] . "' not performed.  Be sure the file exists and has either a .php or .html extention.";
	}
}


function CCMS_TPL_Parser($a = null) {
	global $CFG;

	if($a) {
		$from = 0;
		$a = CCMS_html_min($a);
		while(($to = strpos($a, "{CCMS_", $from)) !== false) {
			echo substr($a, $from, $to - $from);
			$from = $to;
			$to = strpos($a, "}", $from);
			$to++;
			$b = substr($a, $from, $to-$from);
			$from = $to;
			if(preg_match('/^\{(CCMS_LIB):(_?[a-z]+[a-z\-_\pN\/]+[a-z\-_\pN]+\.php);(FUNC):([a-z_\pN]+)\(?(.*?)\)?}\z/i', $b, $c)) {
				// {CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}
				// {CCMS_LIB:cms/_123.php;FUNC:XyZZy123_}
				// {CCMS_LIB:test/dir/indeX_Asdf-123.php;FUNC:cfgindeX_Asdf123("arg1", "arg2")}
				if($c["5"] != "") {
					$tmp = explode('",', $c["5"]);
					foreach($tmp as $key => $val) {
						$val = ltrim($val, ' ');
						$val = ltrim($val, '"');
						$tmp[$key] = rtrim($val, '"');
					}
				}
				/*
				Note: There is a potential bug/problem with the use of the function_exists() function below. If someone places two CCMS_LIB tags in their code like this:
					{CCMS_LIB:_default.php;FUNC:test1}
					{CCMS_LIB:user_lib.php;FUNC:test1}
				The function test1 inside the _default.php template gets loaded first by PHP with the require_once(). When PHP attempts to load the the user_lib.php template it will produce an error complaining that the test1 function is already in use because it was previously loaded on the _default.php template. Rule of thumb, make sure all your functions have different names.
				*/
				if(function_exists($c[4])) {
					if($c["5"] == "") {
						call_user_func($c[4]);
					} else {
						call_user_func_array($c[4], $tmp);
					}
				} else {
					require_once $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["LIBDIR"] . "/" . $c[2];
					if(function_exists($c[4])) {
						if($c["5"] == "") {
							call_user_func($c[4]);
						} else {
							call_user_func_array($c[4], $tmp);
						}
					} else {
						echo $c[0] . " ERROR: FUNC '" . $c[4] . "' not found. ";
					}
				}
			} elseif (preg_match('/^\{(CCMS_DB):([a-z]+[a-z\-_\pN]+),([a-z]+[a-z\-_\pN]+)}\z/i', $b, $c)) {
				// {CCMS_DB:about_us_filter,meta_description}
				// {CCMS_DB:about_us_filter,meta_keywords}
				// {CCMS_DB:about_us_filter,title}
				// {CCMS_DB:about_us_filter,first_paragraph}
				// {CCMS_DB:about_us_filter,second_paragraph}
				// {CCMS_DB:footer_filter,copywrite}
				// {CCMS_DB:header_filter,title}
				// {CCMS_DB:twiter_feed_filter,title}
				// {CCMS_DB:twiter_feed_filter,tag_top}
				// {CCMS_DB:twiter_feed_filter,tag_bottm}
				CCMS_DB($c);
			} elseif (preg_match('/^\{(CCMS_DB_DIR):([a-z]+[a-z\-_\pN]+),([a-z]+[a-z\-_\pN]+)(:(1))?}\z/i', $b, $c)) {
				// {CCMS_DB_DIR:about_us_filter,meta_description}
				// {CCMS_DB_DIR:about_us_filter,meta_description:1}
				// {CCMS_DB_DIR:about_us_filter,meta_keywords:1}
				// {CCMS_DB_DIR:about_us_filter,title}
				// {CCMS_DB_DIR:about_us_filter,first_paragraph}
				// {CCMS_DB_DIR:about_us_filter,second_paragraph}
				// {CCMS_DB_DIR:footer_filter,copywrite}
				// {CCMS_DB_DIR:footer_filter,copywrite:1}
				// {CCMS_DB_DIR:header_filter,title}
				// {CCMS_DB_DIR:twiter_feed_filter,title}
				// {CCMS_DB_DIR:twiter_feed_filter,tag_top:1}
				// {CCMS_DB_DIR:twiter_feed_filter,tag_bottm}
				CCMS_DB_Dir($c);
			} elseif (preg_match('/^\{(CCMS_DB_PRELOAD):([a-z]+[a-z\-_,\pN]*)}\z/i', $b, $c)) {
				// This goes in the header of any template you will be placing database calls for multilingual content in.
				// Its purpose is to help speed up reads from the database by pulling everything you might need for a given
				// template in one call and storing it in memory where it can be accessed much faster.
				// These calls typically go at the top of your template and look like this:
				// {CCMS_DB_PRELOAD:all,index}<!DOCTYPE html>
				// {CCMS_DB_PRELOAD:about_us_filter,footer_filter,header_filter,twiter_feed_filter}<!DOCTYPE html>
				CCMS_DB_Preload($c);
			} elseif (preg_match('/^\{(CCMS_TPL):([a-z\-_\pN\/]+(\.php|\.html)?)}\z/i', $b, $c)) {
				// This preg_match helps prevent CCMS_TPL calls like this; {CCMS_TPL:css/../../../../../../../etc/passwd}
				// {CCMS_TPL:test_01}
				// {CCMS_TPL:test_02.html}
				// {CCMS_TPL:test_03.php}
				// {CCMS_TPL:temp/test_04}
				// {CCMS_TPL:temp/test_05.html}
				// {CCMS_TPL:temp/test_06.php}
				CCMS_TPL_Insert($c);
			} else {
				echo $b;
			}
		}
		echo substr($a, $from, strlen($a)-$from);
	}
}


function CCMS_Main() {
	global $CFG, $CLEAN;

	// If there is no template requested, show $CFG["INDEX"].
	// This code is used when accessing the /user/ templates, before login credentials have between
	// verified and when dealing with URL's that resemble:
	// $CLEAN["INDEX"] === BLANK
	// /
	// Make into:
	// /index.html
	// /index.html
	if(!isset($CLEAN["ccms_tpl"]) || $CLEAN["ccms_tpl"] === "" || $CLEAN["ccms_tpl"] === "/") {
		$CLEAN["ccms_tpl"] = "/" . $CFG["INDEX"];
	}

	CCMS_Set_Headers();

	CCMS_Set_LNG();

	// If the template being requested is inside a dir and no specific template name is
	// part of that request, add index to the end.
	// /fruit/
	// /fruit/orange/
	// /fruit/orange/vitamin/
	// Make into:
	// /fruit/index
	// /fruit/orange/index
	// /fruit/orange/vitamin/index
	if(preg_match("/[\/]\z/", $CLEAN["ccms_tpl"])) {
		$CLEAN["ccms_tpl"] .= "index.html";
	}

	// Copys the end of the string found inside $CLEAN["ccms_tpl"] after the last /.
	// fruit/orange
	// becomes:
	// orange
	preg_match("/[^\.]*\z/", $CLEAN["ccms_tpl"], $ccms_extention);

	// Copys the first part of the string inside $CLEAN["ccms_tpl"] before the last /.
	// /fruit/orange/vitamin/js/c.js
	// Make into:
	// /fruit/orange/vitamin/js/
	//$ccms_dir = @strstr($CLEAN["ccms_tpl"], $ccms_file[0], true);

	// Test to see if CLEAN["ccms_tpl"] file being requested is stored on server with a .php or
	// .html extension.  .php is tested for first, if found it is pre-parsed by php, stored in
	// a buffer and then submitted to the CCMS system for further parsing.  Files with .html
	// extentions are sent immediately to the CCMS system for parsing.
	//
	// NOTE: The filenames are returned in the order in which they are stored by the file system.
	//
	// WARNING: Because of the note above its recommended you do not try to store two files of the
	// same name with different extensions in the same directory at the same time.  You'll save
	// yourself from pulling out all your hair trying to figure out why the newer file simply
	// isn't being called.  In these cases it's best to remove the original and replace with
	// the new file extension all together.

	$found = false;

	if($CFG["lngCodeFoundFlag"] && $CFG["lngCodeActiveFlag"]) {
		// Test to make sure the visitor is not requesting a language which is either non existant or status not live.  If so they should be sent to the error.php template regardless.

		if(isset($_SESSION["USER_ID"])) {
			// The user is logged in, do NOT pull content from the cache for this visit.

			if(is_file($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . $CLEAN["ccms_tpl"])) {

				$found = true;

				header("cache: NOT cached because this is a logged in user.");
				header("Cache-Control: no-cache, must-revalidate");
				header("Pragma: no-cache");

				if($ccms_extention[0] === "php") {
					ob_start();
					include $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . $CLEAN["ccms_tpl"];
					$buf = ob_get_contents();
					ob_end_clean();
					CCMS_TPL_Parser($buf);
				} else {
					if($ccms_extention[0] === "css"){
						header("Content-Type: text/css; charset=utf-8");
					} elseif($ccms_extention[0] === "html") {
						header("Content-Type: text/html; charset=utf-8");
					} elseif($ccms_extention[0] === "js") {
						header("Content-Type: application/javascript; charset=utf-8");
					} else {
						header("Content-Type: text/plain; charset=utf-8");
					}

					$buf = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . $CLEAN["ccms_tpl"]);

					CCMS_TPL_Parser($buf);
				}
			}
		} elseif($ccms_extention[0] === "php") {
			// Looking for a PHP template.  Do not check or save cached version.
			// Headers in this type of template call are set in the template, not here.

			if(is_file($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . $CLEAN["ccms_tpl"])) {

				$found = true;

				header("cache: NOT tested because this is a PHP template request which are always generated in real-time.");

				ob_start();
				include $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . $CLEAN["ccms_tpl"];
				$buf = ob_get_contents();
				ob_end_clean();
				CCMS_TPL_Parser($buf);
			}
		} else {
			// The user is NOT logged in and this is NOT a PHP template request.

			if($CFG["CACHE"] === 1) {
				// Cache setting in config IS turned on.

				$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_cache` WHERE `url` = :url LIMIT 1;");
				/*$qry->execute(array(':url' => "/" . $CFG["TPLDIR"] . $CLEAN["ccms_tpl"]));*/
				$qry->execute(array(':url' => "/" . $CLEAN["ccms_lng"] . $CLEAN["ccms_tpl"]));
				$row = $qry->fetch(PDO::FETCH_ASSOC);

				if($row) {
					// Cache template IS found in the database.

					if(time() <= $row["exp"]) {
						// Cached template is NOT expried.  It should be used.

						$found = true;

						if($ccms_extention[0] === "css"){
							header("Content-Type: text/css; charset=utf-8");
						} elseif($ccms_extention[0] === "html") {
							header("Content-Type: text/html; charset=utf-8");
						} elseif($ccms_extention[0] === "js") {
							header("Content-Type: application/javascript; charset=utf-8");
						} else {
							header("Content-Type: text/plain; charset=utf-8");
						}

						header("cache: ENABLED but NOT expired so returned.");
						header("Expires: " . gmdate('D, d M Y H:i:s T', $row["exp"]));
						header("Last-Modified: " . gmdate('D, d M Y H:i:s T', $row["date"]));

						$tmp = $CFG["CACHE_EXPIRE"] * 60;
						// NOTE: If the template is later called using a serviceWorker be aware that will not respect the settings of the 'cache-control' header as noted in here: https://web.dev/service-workers-cache-storage/#api-nuts-and-bolts

						header("Cache-Control: max-age=" . $tmp);
						$etag = md5($CLEAN["ccms_tpl"]) . "." . $row["date"];
						header("ETag: " . $etag);

						/*
						The nonce search and replace is neccesary in order to help makesure Content-Security-Policy's (nonce validation in templates specificaly) remain valid and secure regardless of where it comes from, dynamicly generated or simply pulled from the cache.

						The canonical update is neccesary because we don't ever want the index page to be crawled by spiders if the URI doesn't contain a language code.  The problem we have to deal with in this case is that the index page of a site may be either dynacically generated or pulled from the cache.  In both cases we must be sure that the <meta name="robots" content="noindex" /> tag IS SENT to the visitor if the language declaration IS NOT found in the URI. (Even if it's sent twice.)
						ie: https://yourdomain.com
						And that the <meta name="robots" content="noindex" /> tag IS NOT sent to the visitor if the language declaration IS found in the URI.
						ie: https://yourdomain.com/en/
						*/
						if($_SERVER['REQUEST_URI'] === "/"){
							$search = array('{NONCE}','<link rel="canonical" href="');
							$replace = array($CFG["nonce"],'<meta name="robots" content="noindex" /><link rel="canonical" href="');
						} else {
							$search = array('{NONCE}','<meta name="robots" content="noindex" />');
							$replace = array($CFG["nonce"]);
						}

						echo str_replace($search, $replace, $row["content"]);
					} else {
						// Cached template IS expried.  It should be removed, rebuilt and recached.

						$qry = $CFG["DBH"]->prepare("DELETE FROM `ccms_cache` WHERE `id` = :id LIMIT 1;");
						$qry->execute(array(':id' => $row["id"]));

						if(is_file($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . $CLEAN["ccms_tpl"])) {

							$found = true;

							if($ccms_extention[0] === "css"){
								header("Content-Type: text/css; charset=utf-8");
							} elseif($ccms_extention[0] === "html") {
								header("Content-Type: text/html; charset=utf-8");
							} elseif($ccms_extention[0] === "js") {
								header("Content-Type: application/javascript; charset=utf-8");
							} else {
								header("Content-Type: text/plain; charset=utf-8");
							}

							$date = time();

							header("cache: ENABLED but EXPIRED so rebuilt, returned and recached.");
							header("Expires: " . gmdate('D, d M Y H:i:s T', $date + ($CFG["CACHE_EXPIRE"] * 60)));
							header("Last-Modified: " . gmdate('D, d M Y H:i:s T', $date));

							$tmp = $CFG["CACHE_EXPIRE"] * 60;
							// NOTE: If the template is later called using a serviceWorker be aware that will not respect the settings of the 'cache-control' header as noted in here: https://web.dev/service-workers-cache-storage/#api-nuts-and-bolts

							header("Cache-Control: max-age=" . $tmp);
							$etag = md5($CLEAN["ccms_tpl"]) . "." . $date;
							header("ETag: " . $etag);

							$buf = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . $CLEAN["ccms_tpl"]);

							ob_start();
							CCMS_TPL_Parser($buf);
							$buf = ob_get_contents();
							ob_end_clean();

							echo $buf;

							$search = $CFG["nonce"];
							$replace = "{NONCE}";
							$buf = str_replace($search, $replace, $buf);

							$qry = $CFG["DBH"]->prepare("INSERT INTO `ccms_cache` (`url`, `date`, `exp`, `content`) VALUES (:url, :date, :exp, :content)");
							$qry->execute(array(':url' => "/" . $CLEAN["ccms_lng"] . $CLEAN["ccms_tpl"], ':date' => $date, ':exp' => $date + ($CFG["CACHE_EXPIRE"] * 60), ':content' => $buf));
						}
					}
				} else {
					// Cache template is NOT found in the database.

					if(is_file($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . $CLEAN["ccms_tpl"])) {

						$found = true;

						if($ccms_extention[0] === "css"){
							header("Content-Type: text/css; charset=utf-8");
						} elseif($ccms_extention[0] === "html") {
							header("Content-Type: text/html; charset=utf-8");
						} elseif($ccms_extention[0] === "js") {
							header("Content-Type: application/javascript; charset=utf-8");
						} else {
							header("Content-Type: text/plain; charset=utf-8");
						}

						$date = time();

						header("cache: ENABLED but NOT found in the database so built, returned and cached.");
						header("Expires: " . gmdate('D, d M Y H:i:s T', $date + ($CFG["CACHE_EXPIRE"] * 60)));
						header("Last-Modified: " . gmdate('D, d M Y H:i:s T', $date));

						$tmp = $CFG["CACHE_EXPIRE"] * 60;
						// NOTE: If the template is later called using a serviceWorker be aware that will not respect the settings of the 'cache-control' header as noted in here: https://web.dev/service-workers-cache-storage/#api-nuts-and-bolts

						header("Cache-Control: max-age=" . $tmp);
						$etag = md5($CLEAN["ccms_tpl"]) . "." . $date;
						header("ETag: " . $etag);

						$buf = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . $CLEAN["ccms_tpl"]);

						ob_start();
						CCMS_TPL_Parser($buf);
						$buf = ob_get_contents();
						ob_end_clean();

						echo $buf;

						$search = $CFG["nonce"];
						$replace = "{NONCE}";
						$buf = str_replace($search, $replace, $buf);

						$qry = $CFG["DBH"]->prepare("INSERT INTO `ccms_cache` (`url`, `date`, `exp`, `content`) VALUES (:url, :date, :exp, :content)");
						$qry->execute(array(':url' => "/" . $CLEAN["ccms_lng"] . $CLEAN["ccms_tpl"], ':date' => $date, ':exp' => $date + ($CFG["CACHE_EXPIRE"] * 60), ':content' => $buf));
					}
				}
			} else {
				// Cache setting in config NOT ENABLED.

				if(is_file($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . $CLEAN["ccms_tpl"])) {

					$found = true;

					if($ccms_extention[0] === "css"){
						header("Content-Type: text/css; charset=utf-8");
					} elseif($ccms_extention[0] === "html") {
						header("Content-Type: text/html; charset=utf-8");
					} elseif($ccms_extention[0] === "js") {
						header("Content-Type: application/javascript; charset=utf-8");
					} else {
						header("Content-Type: text/plain; charset=utf-8");
					}

					header("cache: NOT ENABLED so just returned.");

					$buf = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . $CLEAN["ccms_tpl"]);

					CCMS_TPL_Parser($buf);
				}
			}
		}
	}

	if(!$found) {
		// Store a copy of the original tpl requested for use later on in the error page.
		$CLEAN["ccms_tpl_org"] = $CLEAN["ccms_tpl"];

		header("HTTP/1.0 404 not found");

		if(is_file($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/error.php")) {
			ob_start();
			include $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/error.php";
			$buf = ob_get_contents();
			ob_end_clean();
			//echo CCMS_TPL_Parser($buf);
			CCMS_TPL_Parser($buf);
		} else {
			//echo "ccms_tpl=[" . $CLEAN["ccms_tpl"] . "]";
			echo '<div style="margin:100px auto;text-align:center"><img src="https://custodiancms.org/cross-origin-resources/404-animated-ascii.gif" style="display:block;margin:0 auto" /><br>Under Construction</div>';
		}
	}
}

// benchmark end
//echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);
