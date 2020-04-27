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
define('HTTP_ACCEPT_LANGUAGE', '/^[a-z0-9\-,;=\.]{2,}\z/i');
define('HTTP_COOKIE', '/^[a-z\pN\-_=\.; \/]{2,}\z/i');
define('HTTP_USER_AGENT', '/^[a-z\pN\-_;:,.()#\/\+ ]{2,}\z/i');
define('LNG', '/^[a-z]{2}(-[a-z]{2})?\z/i');
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

$ccms_whitelist = array(
	"ccms_lngSelect"			=> array("type" => "LNG",								"maxlength"     => 5),
	"ccms_parms"				=> array("type" => "PARMS",							"maxlength"     => 128),
	"ccms_tpl"					=> array("type" => "TPL",								"maxlength"     => 256),
	"ccms_session"				=> array("type" => "SESSION_ID",						"maxlength"     => 64),
	"ccms_cid"					=> array("type" => "SESSION_ID",						"maxlength"     => 64),
	"ccms_lng"					=> array("type" => "LNG",								"maxlength"     => 5),
	"ccms_token"				=> array("type" => "UTF8_STRING_DIGIT_WHITE",	"maxlength"     => 64),
	"HTTP_ACCEPT_LANGUAGE"	=> array("type" => "HTTP_ACCEPT_LANGUAGE",		"maxlength"     => 256),
	"HTTP_COOKIE"				=> array("type" => "HTTP_COOKIE",					"maxlength"     => 512),
	"HTTP_USER_AGENT"			=> array("type" => "HTTP_USER_AGENT",				"maxlength"     => 512),
	"QUERY_STRING"				=> array("type" => "QUERY_STRING",					"maxlength"     => 1024)
);

function CCMS_Set_LNG() {
	global $CFG, $CLEAN;

	$CFG["lngCodeFoundFlag"] = false;
	$CFG["lngCodeActiveFlag"] = false;

	if($CLEAN["ccms_lng"] != "" && $CLEAN["ccms_lng"] != "MAXLEN" && $CLEAN["ccms_lng"] != "INVAL") {
		foreach ($CFG["CCMS_LNG_CHARSET"] as $key => $value) {
			if(strcasecmp($key, $CLEAN["ccms_lng"]) == 0) {
				// The language code provided was found in the database.
				$CFG["lngCodeFoundFlag"] = true;
				if($value["status"] == 1) {
					// The language code provided is active in the database.
					//$CLEAN["ccms_lng"] = $key;
					$CFG["CCMS_LNG_DIR"] = $value["dir"];
					$CFG["lngCodeActiveFlag"] = true;
					//break;
				} elseif($CLEAN["SESSION"]["user_id"]) {
					// If this is a verified user trying to make changes to content in a language which is currently
					// not set live, get the users privilages and verify their rights to make updates in the language.
					$qry = $CFG["DBH"]->prepare("SELECT super, priv FROM `ccms_user` WHERE id = :user_id LIMIT 1;");
					$qry->execute(array(':user_id' => $CLEAN["SESSION"]["user_id"]));
					$row = $qry->fetch(PDO::FETCH_ASSOC);
					$json_a = json_decode($row["priv"], true);
					if($row["super"] == 1 || $json_a[priv][content_manager][r] == 1) {
						if($row["super"] == 1 || $json_a[priv][content_manager][lng][$key] == 1 || $json_a[priv][content_manager][lng][$key] == 2) {
							//$CLEAN["ccms_lng"] = $key;
							$CFG["CCMS_LNG_DIR"] = $value["dir"];
							$CFG["lngCodeActiveFlag"] = true;
							//break;
						}
					}
				}
				break;
			}
		}
	} elseif($CLEAN["HTTP_COOKIE"] != "" && $CLEAN["HTTP_COOKIE"] != "MAXLEN" && $CLEAN["HTTP_COOKIE"] != "INVAL") {
		// A value was found in $CLEAN["HTTP_COOKIE"] variable.
		$cookieLng = explode("; ", $CLEAN["HTTP_COOKIE"]);
		foreach($cookieLng as $cookieLng2) {
			$cookieLng3 = explode("=", $cookieLng2);
			if($cookieLng3[0] == "ccms_lng") {
				if(preg_match('/^[a-z]{2}(\-[a-z]{2})?\z/i', $cookieLng3[1], $match)) {
					foreach($CFG["CCMS_LNG_CHARSET"] as $key => $value) {
						if(strcasecmp($key, $match[0]) == 0) {
							// The language code provided was found in the database.
							$CFG["lngCodeFoundFlag"] = true;
							if($value["status"] == 1) {
								// The language code provided is active in the database.
								$CLEAN["ccms_lng"] = $key;
								$CFG["CCMS_LNG_DIR"] = $value["dir"];
								$CFG["lngCodeActiveFlag"] = true;
								break 2;
							}
						}
					}
				}
			}
		}
	}

	if($CLEAN["ccms_lng"] == "" && $CLEAN["HTTP_ACCEPT_LANGUAGE"] != "" && $CLEAN["HTTP_ACCEPT_LANGUAGE"] != "MAXLEN" && $CLEAN["HTTP_ACCEPT_LANGUAGE"] != "INVAL") {
		// Nothing has been found or set in the CLEAN[ccms_lng] variable but there is something in the HTTP_ACCEPT_LANGUAGE variable we can check.
		preg_match_all('/([a-z]{2}((\-[a-z]{2,4})*)?)(;q=[0-9]\.[0-9])?/i', $CLEAN["HTTP_ACCEPT_LANGUAGE"], $match_all);
		foreach($match_all[1] as $match) {
			foreach($CFG["CCMS_LNG_CHARSET"] as $key => $value) {
				if(strcasecmp($key, $match) == 0) {
					// The language code provided was found in the database.
					$CFG["lngCodeFoundFlag"] = true;
					if($value["ptrLng"]) {
						// The language code found is a pointer to another language code in the database.
						if($CFG["CCMS_LNG_CHARSET"][$value["ptrLng"]]["status"] == 1) {
							// The other language code pointed to is active in the database.
							$CLEAN["ccms_lng"] = $value["ptrLng"];
							$CFG["CCMS_LNG_DIR"] = $CFG["CCMS_LNG_CHARSET"][$value["ptrLng"]]["dir"];
							$CFG["lngCodeActiveFlag"] = true;
							break 2;
						}
					} elseif($value["status"] == 1) {
						// The language code provided is active in the database.
						$CLEAN["ccms_lng"] = $key;
						$CFG["CCMS_LNG_DIR"] = $value["dir"];
						$CFG["lngCodeActiveFlag"] = true;
						break 2;
					}
				}
			}
		}
	}

	if($CLEAN["ccms_lng"] == "") {
		// There is still no value assigned to the $CLEAN["ccms_lng"] variable so we will first attempt to retrieve one set
		// in the database.  If not found in the database we will pull a default language setting from the config file.
		$CFG["lngCodeFoundFlag"] = true;
		$CLEAN["ccms_lng"] = $CFG["DEFAULT_SITE_CHAR_SET"];
		$CFG["CCMS_LNG_DIR"] = $CFG["DEFAULT_SITE_CHAR_SET_DIR"];
		$CFG["lngCodeActiveFlag"] = true;
	}

	//setcookie("ccms_lng", $CLEAN["ccms_lng"], time() + ($CFG["COOKIE_SESSION_EXPIRE"] * 60), "/", "", 0, 0);
	// 259200 = 3 days of secconds based on 60*60*24*3
	// setcookie("ccms_lng", $CLEAN["ccms_lng"], time() + 259200, "/", "", 0, 0);

	// This fix adds the 'samesite=strict' attribute to cookies to protect it from cross site scripting button
	// it does is in one of two different ways depending on your version of PHP.
	if(PHP_VERSION_ID < 70300) {
		/*setcookie("ccms_lng", $CLEAN["ccms_lng"], time() + ($CFG["COOKIE_SESSION_EXPIRE"] * 60), "/;samesite=strict", "", 0, 0);*/
		setcookie("ccms_lng", $CLEAN["ccms_lng"], time() + ($CFG["COOKIE_SESSION_EXPIRE"] * 60), "/;httponly;samesite=lax;secure", "", 0, 0);

		/* __Secure- */

	} else {
		setcookie("ccms_lng", $CLEAN["ccms_lng"], [
			'expires' => time() + ($CFG["COOKIE_SESSION_EXPIRE"] * 60),
			'path' => "/",
			'domain' => $CFG["DOMAIN"],
			/*'samesite' => "strict",
			'secure' => 0,
			'httponly' => 0*/
			'samesite' => "lax",
			'secure' => true,
			'httponly' => true
		]);
	}
}


function CCMS_cookie_SESSION() {
	global $CFG, $CLEAN;

	$CLEAN["SESSION"]["user_agent"] = $CLEAN["HTTP_USER_AGENT"];
	if(isset($CLEAN["ccms_session"]) && $CLEAN["ccms_session"] != "MAXLEN" && $CLEAN["ccms_session"] != "INVAL") {
		// A value was found, so we'll try testing it against the database.
		//break;
	} elseif($CLEAN["HTTP_COOKIE"] != "" && $CLEAN["HTTP_COOKIE"] != "MAXLEN" && $CLEAN["HTTP_COOKIE"] != "INVAL") {
		// A value was found in $CLEAN["HTTP_COOKIE"] variable.  We'll try extracting the session value and validate it
		// here first.  If it passes then we'll try testing it against the database.
		$cookieSess = explode("; ", $CLEAN["HTTP_COOKIE"]);
		foreach($cookieSess as $cookieSess2) {
			$cookieSess3 = explode("=", $cookieSess2);
			if($cookieSess3[0] == "ccms_session") {
				$cookieSess3[1] = @trim($cookieSess3[1]);
				// utf8_decode() converts unknown ISO-8859-1 chars to '?' for the purpose of counting.
				$length = strlen(utf8_decode($cookieSess3[1]));
				$buf = NULL;
				if($length > 64) {
					$CLEAN["ccms_session"] = "MAXLEN";
				} else {
					$CLEAN["ccms_session"] = (preg_match('/^[a-z\pN]{1,}\z/i', $cookieSess3[1])) ? $cookieSess3[1] : "INVAL";
				}
				//$CLEAN["ccms_session"] = $buf;
				break;
			}
		}
	}
	if(isset($CLEAN["ccms_session"]) && $CLEAN["ccms_session"] != "MAXLEN" && $CLEAN["ccms_session"] != "INVAL") {
		// The user appears to already have a session code so now we test it.  Check the 'ccms_session' table for matches.
		$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_session` WHERE `code` = :ccms_session AND `ip` = :ip AND `user_agent` = :user_agent AND `prf` IS NULL LIMIT 1;");
		$qry->execute(array(':ccms_session' => $CLEAN["ccms_session"], ':ip' => $_SERVER["REMOTE_ADDR"], ':user_agent' => $CLEAN["SESSION"]["user_agent"]));
		$row = $qry->fetch(PDO::FETCH_ASSOC);

		if($row) {
			// Session match found
			$a = time();

			if($a > $row["exp"]) {
				// Session expired

				if(isset($CLEAN["ccms_token"])) {
					// If the session belonged to an administrator or translator we should redirect them to the login page instead.
					header("Location: /" . $CFG["DEFAULT_SITE_CHAR_SET"] . "/user/");
					die();
				}

				$qry = $CFG["DBH"]->prepare("DELETE FROM `ccms_session` WHERE `id` = :id LIMIT 1;");
				$qry->execute(array(':id' => $row["id"]));

				$a = time();
				$b = $a;
				$a = md5($a);
				$c = $b + ($CFG["COOKIE_SESSION_EXPIRE"] * 60);
				//setcookie("ccms_session", $a, $c, "/", "", 0, 0);

				// This fix adds the 'samesite=strict' attribute to cookies to protect it from cross site scripting button
				// it does is in one of two different ways depending on your version of PHP.
				if(PHP_VERSION_ID < 70300) {
					/*setcookie("ccms_session", $a, $c, "/; samesite=strict", "", 0, 0);*/
					setcookie("ccms_session", $a, $c, "/;httponly;samesite=lax;secure", "", 0, 0);
				} else {
					setcookie("ccms_session", $a, [
						'expires' => $c,
						'path' => "/",
						'domain' => $CFG["DOMAIN"],
						/*'samesite' => "strict",
						'secure' => 0,
						'httponly' => 0*/
						'samesite' => "lax",
						'secure' => true,
						'httponly' => true
					]);
				}

				$CLEAN["SESSION"]["code"] = $a;
				$CLEAN["SESSION"]["first"] = $b;
				$CLEAN["SESSION"]["last"] = $b;
				$CLEAN["SESSION"]["exp"] = $c;
				$CLEAN["SESSION"]["ip"] = $_SERVER["REMOTE_ADDR"];
				$CLEAN["SESSION"]["user_id"] = null;
				$CLEAN["SESSION"]["fail"] = "0";

				$qry = $CFG["DBH"]->prepare("INSERT INTO `ccms_session` (code, first, last, exp, ip, user_agent) VALUES (:code, :first, :last, :exp, :ip, :user_agent);");
				$qry->execute(array(':code' => $a, ':first' => $b, ':last' => $b, ':exp' => $c, ':ip' => $_SERVER["REMOTE_ADDR"], ':user_agent' => $CLEAN["SESSION"]["user_agent"]));
			} else {
				// Session not expired
				$a = time();
				$b = $a;
				$a = md5($a);
				$c = $b + ($CFG["COOKIE_SESSION_EXPIRE"] * 60);
				//setcookie("ccms_session", $a, $c, "/", "", 0, 0);

				// This fix adds the 'samesite=strict' attribute to cookies to protect it from cross site scripting button
				// it does is in one of two different ways depending on your version of PHP.
				if(PHP_VERSION_ID < 70300) {
					/*setcookie("ccms_session", $a, $c, "/; samesite=strict", "", 0, 0);*/
					setcookie("ccms_session", $a, $c, "/;httponly;samesite=lax;secure", "", 0, 0);
				} else {
					setcookie("ccms_session", $a, [
						'expires' => $c,
						'path' => "/",
						'domain' => $CFG["DOMAIN"],
						/*'samesite' => "strict",
						'secure' => 0,
						'httponly' => 0*/
						'samesite' => "lax",
						'secure' => true,
						'httponly' => true
					]);
				}

				$CLEAN["SESSION"]["code"] = $a;
				$CLEAN["SESSION"]["first"] = $row["first"];
				$CLEAN["SESSION"]["last"] = $b;
				$CLEAN["SESSION"]["exp"] = $c;
				$CLEAN["SESSION"]["ip"] = $row["ip"];
				$CLEAN["SESSION"]["user_id"] = $row["user_id"];
				$CLEAN["SESSION"]["fail"] = $row["fail"];

				$qry = $CFG["DBH"]->prepare("UPDATE `ccms_session` SET `code` = :code, `last` = :last, `exp` = :exp WHERE `id` = :id LIMIT 1;");
				$qry->execute(array(':code' => $a, ':last' => $b, ':exp' => $c, ':id' => $row["id"]));
			}
		} else {
			// Session not found
			if(isset($CLEAN["ccms_token"])) {
				// If the URI contains a ccms_token administrator or translator token we should redirect them to the login page instead.
				header("Location: /" . $CFG["DEFAULT_SITE_CHAR_SET"] . "/user/");
				die();
			}

			$a = time();
			$b = $a;
			$a = md5($a);
			$c = $b + ($CFG["COOKIE_SESSION_EXPIRE"] * 60);
			//setcookie("ccms_session", $a, $c, "/", "", 0, 0);

			// This fix adds the 'samesite=strict' attribute to cookies to protect it from cross site scripting button
			// it does is in one of two different ways depending on your version of PHP.
			if(PHP_VERSION_ID < 70300) {
				/*setcookie("ccms_session", $a, $c, "/; samesite=strict", "", 0, 0);*/
				setcookie("ccms_session", $a, $c, "/;httponly;samesite=lax;secure", "", 0, 0);
			} else {
				setcookie("ccms_session", $a, [
					'expires' => $c,
					'path' => "/",
					'domain' => $CFG["DOMAIN"],
					/*'samesite' => "strict",
					'secure' => 0,
					'httponly' => 0*/
					'samesite' => "lax",
					'secure' => true,
					'httponly' => true
				]);
			}

			$CLEAN["SESSION"]["code"] = $a;
			$CLEAN["SESSION"]["first"] = $b;
			$CLEAN["SESSION"]["last"] = $b;
			$CLEAN["SESSION"]["exp"] = $c;
			$CLEAN["SESSION"]["ip"] = $_SERVER["REMOTE_ADDR"];
			$CLEAN["SESSION"]["user_id"] = null;
			$CLEAN["SESSION"]["fail"] = "0";

			$qry = $CFG["DBH"]->prepare("INSERT INTO `ccms_session` (code, first, last, exp, ip, user_agent) VALUES (:code, :first, :last, :exp, :ip, :user_agent);");
			$qry->execute(array(':code' => $a, ':first' => $b, ':last' => $b, ':exp' => $c, ':ip' => $_SERVER["REMOTE_ADDR"], ':user_agent' => $CLEAN["SESSION"]["user_agent"]));
		}
	} else {
		// Session not found
		if(isset($CLEAN["ccms_token"])) {
			// If the URI contins a ccms_token administrator or translator token we should redirect them to the login page instead.
			header("Location: /" . $CFG["DEFAULT_SITE_CHAR_SET"] . "/user/");
			die();
		}

		$a = time();
		$b = $a;
		$a = md5($a);
		$c = $b + ($CFG["COOKIE_SESSION_EXPIRE"] * 60);
		//setcookie("ccms_session", $a, $c, "/", "", 0, 0);

		// This fix adds the 'samesite=strict' attribute to cookies to protect it from cross site scripting button
		// it does is in one of two different ways depending on your version of PHP.
		if(PHP_VERSION_ID < 70300) {
			/*setcookie("ccms_session", $a, $c, "/; samesite=strict", "", 0, 0);*/
			setcookie("ccms_session", $a, $c, "/;httponly;samesite=lax;secure", "", 0, 0);
		} else {
			setcookie("ccms_session", $a, [
				'expires' => $c,
				'path' => "/",
				'domain' => $CFG["DOMAIN"],
				/*'samesite' => "strict",
				'secure' => 0,
				'httponly' => 0*/
				'samesite' => "lax",
				'secure' => true,
				'httponly' => true
			]);
		}

		$CLEAN["SESSION"]["code"] = $a;
		$CLEAN["SESSION"]["first"] = $b;
		$CLEAN["SESSION"]["last"] = $b;
		$CLEAN["SESSION"]["exp"] = $c;
		$CLEAN["SESSION"]["ip"] = $_SERVER["REMOTE_ADDR"];
		$CLEAN["SESSION"]["user_id"] = null;
		$CLEAN["SESSION"]["fail"] = "0";

		$qry = $CFG["DBH"]->prepare("INSERT INTO `ccms_session` (code, first, last, exp, ip, user_agent) VALUES (:code, :first, :last, :exp, :ip, :user_agent);");
		$qry->execute(array(':code' => $a, ':first' => $b, ':last' => $b, ':exp' => $c, ':ip' => $_SERVER["REMOTE_ADDR"], ':user_agent' => $CLEAN["SESSION"]["user_agent"]));
	}
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
			echo "Error!: " . $e->getCode() . '<br />\n'. $e->getMessage();
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
					case "HTTP_ACCEPT_LANGUAGE":
						$buf = (preg_match(HTTP_ACCEPT_LANGUAGE, $value)) ? $value : "INVAL";
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
				}
			}
			$CLEAN[$key] = $buf;
		}
	}
}


function CCMS_DB($a) {
	global $CFG, $CLEAN;

	if(isset($CLEAN["CCMS_DB_Preload_Content"])) {
		if($CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["content"] != "") {
			echo CCMS_TPL_Parser($CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["content"]);
		} else {
			echo CCMS_TPL_Parser($CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CFG["DEFAULT_SITE_CHAR_SET"]]["content"]);
		}
	} else {
		echo $a[0] . " ERROR: Either CCMS_DB_Preload function was not called or the CCMS_DB_PRELOAD tag was not found on your template prior to calling this CCMS_DB tag. ";
	}
}


function CCMS_DB_Dir($a) {
	global $CFG, $CLEAN;

	if(isset($CLEAN["CCMS_DB_Preload_Content"])) {
		if($a[5] == 1) {
			// Make editable on the public side.
			if($CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["content"] != "") {
				echo $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["dir"] . "\" data-ccms=\"" . $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["id"] . "\" data-ccms-grp=\"" . $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["grp"] . "\" data-ccms-name=\"" . $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["name"];
			} else {
				echo $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CFG["DEFAULT_SITE_CHAR_SET"]]["dir"] . "\" data-ccms=\"" . $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["id"] . "\" data-ccms-grp=\"" . $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["grp"] . "\" data-ccms-name=\"" . $CLEAN["CCMS_DB_Preload_Content"][$a[2]][$a[3]][$CLEAN["ccms_lng"]]["name"];
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

	// This function can be called in two different ways:
	// $content = CCMS_DB_Preload("about_us_filter,footer_filter,header_filter,twiter_feed_filter");
	// or
	// {CCMS_DB_PRELOAD:about_us_filter,footer_filter,header_filter,twiter_feed_filter}
	if($a[2]) {
		$grpArray = explode(",", $a[2]);
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
	// Enable and Disable this feature in config.php.
	// eg:
	// $CFG["HTML_MIN"] = 0; // off
	// $CFG["HTML_MIN"] = 1; // on (Default)
	//
	// This code will not break pre, code or textarea tagged content.
	// WARNING: Make sure your actual HTML templates do not contain any commented // code because minification means all whitespaces will be removed and the carriage return at the end of your comment will also be removed, making everything that comes after that a commented comment aswell.
	global $CFG, $CLEAN;

	if($CFG["HTML_MIN"] == 1 && $CLEAN["SESSION"]["user_id"] == null) {
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

	// Test to see if CLEAN["ccms_tpl"] file being requested is stored on the server with a .htm, .html, .php,
	// .tpl, .txt, .xml or .xsl extension.  .php is tested for first, if found it is pre-parsed by php, stored in a buffer
	// and then submitted to the CMS system for further parsing.  If any other extension found it is sent
	// immediately for parsing.
	//
	// NOTE: The filenames are returned in the order in which they are stored by the file system.
	//
	// NOTE ABOUT file_get_contents(): On Windows servers the case of a filename is not important, however on
	// UNIX/LINUX systems case is very important.  If you have a file on your system you are looking for is not
	// typed if with the proper case it will reselt in an error.  Just make sure you always lowercase all your
	// URL's and template names for safety.
	//
	// WARNING: It is recommended that you do NOT store two files of the same name with different extensions
	// in the same directory at the same time.  You'll save yourself from pulling out all your hair trying
	// to figure out why the newer file simply isn't being called.  In these cases it's best to remove the
	// original and replace with the new file extension all together.
	if(preg_match('/\.php\z/i', $a[2])) {
		ob_start();
		include $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/" . $a[2];
		$html = ob_get_contents();
		ob_end_clean();
		echo CCMS_TPL_Parser($html);
	} elseif(preg_match('/\.html\z/i', $a[2])) {
		if(($html = @file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/" . $a[2])) !== false) {
			echo CCMS_TPL_Parser($html);
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
				// Note: There is a potential bug/problem with the use of the function_exists() function below.
				// If someone places two CCMS_LIB tags in their code like this:
				// {CCMS_LIB:_default.php;FUNC:test1}
				// {CCMS_LIB:user_lib.php;FUNC:test1}
				// The function test1 inside the _default.php template gets loaded first by PHP with the require_once().
				// When PHP attempts to load the the user_lib.php template it will produce an error complaining that the
				// test1 function is already in use because it was previously loaded on the _default.php template.
				// Rule of thumb, make sure all your functions have different names.
				if (function_exists($c[4])) {
					if ($c["5"] == "") {
						call_user_func($c[4]);
					} else {
						call_user_func_array($c[4], $tmp);
					}
				} else {
					require_once $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["LIBDIR"] . "/" . $c[2];
					if (function_exists($c[4])) {
						if ($c["5"] == "") {
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

	if(!preg_match('/^\/(([a-z]{2})(-[a-z]{2})?)\/user\/(.*)\z/ui', $_SERVER["REQUEST_URI"])) {
		CCMS_cookie_SESSION();
	}

	CCMS_Set_LNG();

	// If there is no template requested, show $CFG["INDEX"].
	// This code helps when dealing with URL's that resemble:
	// $CLEAN["INDEX"] == BLANK
	// /
	// Make into:
	// index
	// index
	if (!isset($CLEAN["ccms_tpl"]) || $CLEAN["ccms_tpl"] == "" || $CLEAN["ccms_tpl"] == "/") {
		$CLEAN["ccms_tpl"] = $CFG["INDEX"];
	}

	// If the template being requested is inside a dir and no specific template name is
	// part of that request, add index to the end.
	// /fruit/
	// /fruit/orange/
	// /fruit/orange/vitamin/
	// Make into:
	// /fruit/index
	// /fruit/orange/index
	// /fruit/orange/vitamin/index
	if (preg_match('/[\/]\z/', $CLEAN["ccms_tpl"])) {
		$CLEAN["ccms_tpl"] .= "index";
	}

	// Trims the forward slash (/) from the beginning and .html from the end.  Resave back to CLEAN["ccms_tpl"]:
	// /index
	// /fruit/orange.html
	// /fruit/orange/vitamin
	// /fruit/orange/vitamin/c.html
	// Make into:
	// index
	// fruit/orange
	// fruit/orange/vitamin
	// fruit/orange/vitamin/c
	/*$CLEAN["ccms_tpl"] = preg_replace('/^(\/)(.*?)(\.html?)?\z/i', '$2', $CLEAN["ccms_tpl"]);*/
	$CLEAN["ccms_tpl"] = preg_replace('/^(\/)(.*?)(\.css?)?(\.html?)?(\.js?)?\z/i', '$2', $CLEAN["ccms_tpl"]);

	// Copys the end of the string found inside $CLEAN["ccms_tpl"] after the last /.
	preg_match('/([^\/]*)\z/', $CLEAN["ccms_tpl"], $ccms_file);

	// Copys the first part of the string inside $CLEAN["ccms_tpl"] before the last /.
	$ccms_dir = @strstr($CLEAN["ccms_tpl"], $ccms_file[0], true);

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

	if ($CFG["lngCodeFoundFlag"] && $CFG["lngCodeActiveFlag"]) {
		// Test to make sure the visitor is not requesting a language which is either non existant or status not live.  If so they should be sent to the error.php template regardless.
		if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/" . $ccms_dir)) {
			$odhandle = @opendir($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/" . $ccms_dir);
			while (($file = @readdir($odhandle)) !== false) {
				if ($file != "." && $file != ".." && is_file($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/" . $ccms_dir . $file)) {
					if ($file == $ccms_file[0] . ".php") {
						// .php template.  Do not check or save cached version.
						ob_start();
						include $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/" . $ccms_dir . $file;
						$html = ob_get_contents();
						ob_end_clean();
						CCMS_TPL_Parser($html);
						$found = true;
						break;
					} elseif ($file == $ccms_file[0] . ".css" || $file == $ccms_file[0] . ".html" || $file == $ccms_file[0] . ".js") {
						if ($CLEAN["SESSION"]["user_id"] == null) {
							// If this is a normal session and the user is not logged in then cache this page in the visitors browers.
							if($file == $ccms_file[0] . ".css"){
								header("Content-Type: text/css; charset=utf-8");
							} elseif ($file == $ccms_file[0] . ".html") {
								header("Content-Type: text/html; charset=utf-8");
							} elseif ($file == $ccms_file[0] . ".js") {
								header("Content-Type: application/javascript");
							} else {
								header("Content-Type: text/html; charset=utf-8");
							}
							// Expires in
							header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + ($CFG["CACHE_EXPIRE"] * 60)));

							// Check for a cache version, that's not expired and if necessary, cache a new copy.
							$url = "/" . $CLEAN["ccms_lng"] . "/" . $ccms_dir . $file;
							$url_md5 = md5($url);

							if($CFG["CACHE"] == 1) {
								// Cache setting in /ccmspre/config.php is enabled, $CFG["CACHE"] = 1;.
								$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_cache` WHERE `url_md5` = :url_md5 LIMIT 1;");
								$qry->execute(array(':url_md5' => $url_md5));
								$row = $qry->fetch(PDO::FETCH_ASSOC);

								if($row) {
									// A cached version of the page was found, we need to check if it is expired.

									if(time() >= $row["exp"]) {
										// The cached template is expried.  It should be removed, rebuild and recached.

										$qry = $CFG["DBH"]->prepare("DELETE FROM `ccms_cache` WHERE `id` = :id LIMIT 1;");
										$qry->execute(array(':id' => $row["id"]));

										ob_start();
										$html = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/" . $ccms_dir . $file);
										CCMS_TPL_Parser($html);
										$buf = ob_get_contents();
										ob_end_clean();
										$date = time();
										$qry = $CFG["DBH"]->prepare("INSERT INTO `ccms_cache` (url_md5, url, date, exp, content) VALUES (:url_md5, :url, :date, :exp, :content);");
										$qry->execute(array(':url_md5' => $url_md5, ':url' => $url, ':date' => $date, ':exp' => $date + ($CFG["CACHE_EXPIRE"] * 60), ':content' => $buf));

										echo $buf;
										/*
										echo "<!-- cache id: " . $CFG["DBH"]->lastInsertId() . " -->";
										*/
									} else {
										// The cached template is NOT expried.  It should be used.

										echo $row["content"];
										/*
										echo "<!-- cache id: " . $row["id"] . " -->";
										*/
									}
								} else {
									// A cached version of the page requested was NOT found.
									// It should be built and cached.
									ob_start();
									$html = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/" . $ccms_dir . $file);
									CCMS_TPL_Parser($html);
									$buf = ob_get_contents();
									ob_end_clean();
									/*
									$qry = $CFG["DBH"]->prepare("INSERT INTO `ccms_cache` (url_md5, url, exp, content) VALUES (:url_md5, :url, :exp, :content);");
									$qry->execute(array(':url_md5' => $url_md5, ':url' => $url, ':exp' => time() + ($CFG["CACHE_EXPIRE"] * 60), ':content' => $buf));
									*/
									$date = time();
									$qry = $CFG["DBH"]->prepare("INSERT INTO `ccms_cache` (url_md5, url, date, exp, content) VALUES (:url_md5, :url, :date, :exp, :content);");
									$qry->execute(array(':url_md5' => $url_md5, ':url' => $url, ':date' => $date, ':exp' => $date + ($CFG["CACHE_EXPIRE"] * 60), ':content' => $buf));

									echo $buf;
									/*
									echo "<!-- cache id: " . $CFG["DBH"]->lastInsertId() . " -->";
									*/
								}
							} else {
								// Cache setting in /ccmspre/config.php is NOT enabled, $CFG["CACHE"] = 0;.
								// Just do a normal template pars.
								$html = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/" . $ccms_dir . $file);
								CCMS_TPL_Parser($html);
							}

							$found = true;
							break;
						} else {
							// If this is a verified session asigned of an active user then disable cache.
							// .html template, admin/translator template request, logged in.  Do not check or save cached version.
							if($file == $ccms_file[0] . ".css"){
								header("Content-Type: text/css; charset=utf-8");
							} elseif ($file == $ccms_file[0] . ".html") {
								header("Content-Type: text/html; charset=utf-8");
							} elseif ($file == $ccms_file[0] . ".js") {
								header("Content-Type: application/javascript");
							} else {
								header("Content-Type: text/html; charset=utf-8");
							}
							header("Cache-Control: no-cache, must-revalidate");
							header("Pragma: no-cache");
							$html = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/" . $ccms_dir . $file);
							CCMS_TPL_Parser($html);
							$found = true;
							break;
						}
					}
				}
			}
			@closedir($odhandle);
		}
	}

	if (!$found) {
		// Store a copy of the original tpl requested for use later on in the error page.
		$CLEAN["ccms_tpl_org"] = $CLEAN["ccms_tpl"];

		// Rest the tpl variable to the error page.
		$CLEAN["ccms_tpl"] = "error";
		if ($CLEAN["ccms_tpl"] == "error") {
			header("HTTP/1.0 404 not found");
		}

		ob_start();
		include $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["TPLDIR"] . "/" . $CLEAN["ccms_tpl"] . ".php";
		$html = ob_get_contents();
		ob_end_clean();
		CCMS_TPL_Parser($html);
	}
}

// benchmark end
//echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);
