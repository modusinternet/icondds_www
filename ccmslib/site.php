<?php
function navLngdir() {
	global $CFG;
	/* Used to help move the language dropdow box around when switching from ltr to rtl languages. */
	if($CFG["CCMS_LNG_DIR"] == "ltr") {
		echo " right:0; left:auto;";
	} else {
		echo " text-align:right;";
	}
}

function navLngList() {
	global $CFG, $CLEAN;
	// this line of code produces the wrong output on GoDaddy servers.
	//$tpl = htmlspecialchars(preg_replace('/^\/([\pL\pN\-]*)\/?(.*)\z/i', '${2}', $_SERVER['REDIRECT_URL']));
	$tpl = htmlspecialchars(preg_replace('/^\/([\pL\pN\-]*)\/?(.*)\z/i', '${2}', $_SERVER['REQUEST_URI']));
	$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_lng_charset` WHERE `status` = 1 ORDER BY lngDesc ASC;");
	if($qry->execute()) {
		while($row = $qry->fetch()) {
			if($row["ptrLng"]) {
				echo "<li><a id=\"lng-" . $row["lng"] . "\" class=\"dropdown-item\" dir=\"" . $row["dir"] . "\" href=\"/" . $row["ptrLng"] . "/" . $tpl . "\" onclick=\"ccms_lcu('" . $row["ptrLng"] . "');\" title=\"" . $row["lngDesc"] . "\">" . $row["lngDesc"] . "</a></li>";
			} else {
				echo "<li><a id=\"lng-" . $row["lng"] . "\" class=\"dropdown-item\" dir=\"" . $row["dir"] . "\" href=\"/" . $row["lng"] . "/" . $tpl . "\" onclick=\"ccms_lcu('" . $row["lng"] . "');\" title=\"" . $row["lngDesc"] . "\">" . $row["lngDesc"] . "</a></li>";
			}
		}
	}
}

function lngList() {
	global $CFG, $CLEAN;
	// this line of code produces the wrong output on GoDaddy servers.
	//$tpl = htmlspecialchars(preg_replace('/^\/([\pL\pN\-]*)\/?(.*)\z/i', '${2}', $_SERVER['REDIRECT_URL']));
	$tpl = htmlspecialchars(preg_replace('/^\/([\pL\pN\-]*)\/?(.*)\z/i', '${2}', $_SERVER['REQUEST_URI']));
	$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_lng_charset` WHERE `status` = 1 ORDER BY lngDesc ASC;");
	if($qry->execute()) {
		while($row = $qry->fetch()) {
			if($row["ptrLng"]) {
				echo "<li><a id=\"lng-" . $row["lng"] . "\" dir=\"" . $row["dir"] . "\" href=\"/" . $row["ptrLng"] . "/" . $tpl . "\" onclick=\"ccms_lcu('" . $row["ptrLng"] . "');\" title=\"" . $row["lngDesc"] . "\">" . $row["lngDesc"] . "</a></li>";
			} else {
				echo "<li><a id=\"lng-" . $row["lng"] . "\" dir=\"" . $row["dir"] . "\" href=\"/" . $row["lng"] . "/" . $tpl . "\" onclick=\"ccms_lcu('" . $row["lng"] . "');\" title=\"" . $row["lngDesc"] . "\">" . $row["lngDesc"] . "</a></li>";
			}
		}
	}
}

function lng_dir_left_go_right_right_go_left() {
	global $CFG;
	if($CFG["CCMS_LNG_DIR"] == "ltr") {
		echo "right";
	} else {
		echo "left";
	}
}

function lng_dir_right_go_left_left_go_right() {
	global $CFG;
	if($CFG["CCMS_LNG_DIR"] == "ltr") {
		echo "left";
	} else {
		echo "right";
	}
}

function shadow_direction() {
	global $CFG;
	/* Used to help direct the horizontal (x) direction of shadows generated in CSS when changing languages. */
	if(!($CFG["CCMS_LNG_DIR"] == "ltr")){
		echo "-";
	}
}

function load_resource($argv){
	global $CFG;
	echo $CFG["RES"][$argv];
}

/*
$aws_flag = if not null append AWS link
$lng_flag = if not null append language code to link
$path = a variable found in the config file that represents a partial pathway to the style sheet, not including and details about AWS, language code, or language direction)
$dir_flag = if not null append language direction to link
*/
function build_css_link($aws_flag = null, $lng_flag = null, $path, $dir_flag = null){
	global $CFG;

	/* If $path is not found in the config.php file then do nothing. */
	if(!isset($CFG["RES"][$path])) return;

	$buff = 'var l=document.createElement("link");l.rel="stylesheet";l.href="';

	$url = "";

	if($aws_flag){
		$url .= $CFG["RES"]["AWS"];
	}

	/* We do this for safety to help just incase the script calling this function requests the AWS code and the language code by accident.  We never ask for language code ones things are located on AWS. */
	if($lng_flag){
		if(!$aws_flag){
			$url .= "/" . ccms_lng_ret();
		}
	}

	$url .= $CFG["RES"][$path];

	if($dir_flag){
		$url .= "-" . ccms_lng_dir_ret();
	}

	$url .= '.css';

	$buff .= $url . '";';

	if($aws_flag){
		$qry = $CFG["DBH"]->prepare("SELECT * FROM `sri` WHERE `url` = :url LIMIT 1;");
		$qry->execute(array(':url' => $url));

		$row = $qry->fetch(PDO::FETCH_ASSOC);
		if($row){
			$buff .= 'l.integrity="sha384-' . $row["sri-code"] . '";';
		}else{
			$tmp = file_get_contents($url);
			$result = base64_encode(hash("sha384", $tmp, true));
			$qry = $CFG["DBH"]->prepare("INSERT INTO `sri` (`id`, `url`, `sri-code`) VALUES (NULL, :url, :result);");
			$qry->execute(array(':url' => $url, ':result' => $result));
			$buff .= 'l.integrity="sha384-' . $result . '";';
		}
		$buff .= 'l.crossOrigin="anonymous";';
	}
	echo $buff .= 'var h=document.getElementsByTagName("head")[0];h.parentNode.insertBefore(l,h);';
}

/*
$aws_flag = if not null append AWS link
$lng_flag = if not null append language code to link
$path = a variable found in the config file that represents a partial pathway to the style sheet, not including and details about AWS, language code, or language direction)
*/
function build_js_link($aws_flag = null, $lng_flag = null, $path){
	global $CFG;

	/* If $path is not found in the config.php file then do nothing. */
	if(!isset($CFG["RES"][$path])) return;

	$url = "";

	if($aws_flag){
		$url .= $CFG["RES"]["AWS"];
	}

	/* We do this for safety to help just incase the script calling this function requests the AWS code and the language code by accident.  We never ask for language code ones things are located on AWS. */
	if($lng_flag){
		if(!$aws_flag){
			$url .= "/" . ccms_lng_ret();
		}
	}

	echo $url .= $CFG["RES"][$path];
}

/*
$path = a variable found in the config file that represents a partial pathway to the style sheet, not including and details about AWS, language code, or language direction)
*/
function build_js_sri($path){
	global $CFG;

	/* If $path is not found in the config.php file then do nothing. */
	if(!isset($CFG["RES"][$path])) return;

	$buff = ",'";

	$url = $CFG["RES"]["AWS"] . $CFG["RES"][$path];

	$qry = $CFG["DBH"]->prepare("SELECT * FROM `sri` WHERE `url` = :url LIMIT 1;");
	$qry->execute(array(':url' => $url));

	$row = $qry->fetch(PDO::FETCH_ASSOC);
	if($row) {
		echo $buff .= "sha384-" . $row["sri-code"] . "','anonymous'";
	}else{
		$tmp = file_get_contents($url);
		$result = base64_encode(hash("sha384", $tmp, true));
		$qry = $CFG["DBH"]->prepare("INSERT INTO `sri` (`id`, `url`, `sri-code`) VALUES (NULL, :url, :result);");
		$qry->execute(array(':url' => $url, ':result' => $result));
		echo $buff .= "sha384-" . $result . "','anonymous'";
	}
}

function csp_header() {
	// Content Security Policy (CSP) only work in modern browsers Chrome 25+, Firefox 23+, Safari 7+.
	global $CFG;

	//$report_uri = "https://".$CFG["DOMAIN"]."/".ccms_lng_ret()."/violationReportForCSP.php";
	//$lang = ccms_lng_ret();

	$CFG["nonce"] = hash("sha256", rand());

	$buffer = "Content-Security-Policy: ".

		// Defines a set of allowed URLs which can be used in the src attribute of a HTML base tag.
		//"base-uri 'none'; ".

		// Applies to XMLHttpRequest (AJAX), WebSocket, fetch(), <a ping> or EventSource. If not allowed the browser emulates a 400 HTTP status code.
		//"connect-src 'self' *.cloudfront.net *.google.com *.googleapis.com *.googletagmanager.com *.google-analytics.com *.gstatic.com; ".

		// The default-src directive defines the default policy for fetching resources such as JavaScript, Images, CSS, Fonts, AJAX requests, Frames, HTML5 Media. Not all directives fallback to default-src. See the Source List Reference for possible values. (https://content-security-policy.com/#source_list)
		//"default-src 'none'; ".

		// Defines valid sources of font resources (loaded via @font-face).
		//"font-src 'self' data: *.gstatic.com *.googleapis.com; ".

		// Defines valid sources that can be used as an HTML <form> action.
		//"form-action 'self'; ".

		// Defines valid sources for embedding the resource using <frame> <iframe> <object> <embed> <applet>. Setting this directive to 'none' should be roughly equivalent to X-Frame-Options: DENY
		//"frame-ancestors 'self'; ".

		// Defines valid sources for loading frames. In CSP Level 2 frame-src was deprecated in favor of the child-src directive. CSP Level 3, has undeprecated frame-src and it will continue to defer to child-src if not present.
		//"frame-src 'self' *.google.com *.youtube.com;".

		// Defines valid sources of images.
		//"img-src 'self' data: *.cloudfront.net *.gstatic.com *.google-analytics.com *.googleapis.com *.googleusercontent.com *.googletagmanager.com *.google.com *.gravatar.com; ".

		// Restricts the URLs that application manifests can be loaded.
		//"manifest-src 'self'; ".

		// Defines valid sources of audio and video, eg HTML5 <audio>, <video> elements.
		//"media-src 'self' *.cloudfront.net; ".

		// Defines valid sources of plugins, eg <object>, <embed> or <applet>.
		"object-src 'none'; ".

		// Instructs the browser to POST a reports of policy failures to this URI. You can also use Content-Security-Policy-Report-Only as the HTTP header name to instruct the browser to only send reports (does not block anything). This directive is deprecated in CSP Level 3 in favor of the report-to directive.
		//"report-uri https://".$CFG["DOMAIN"]."/".$lang."/cspViolationReport.php; ".
		//"report-uri https://".$CFG["DOMAIN"]."/en/cspViolationReport.php; ".

		// Defines valid sources of JavaScript.
		//"script-src 'nonce-SraTe14t6sjq2m4' 'strict-dynamic' 'unsafe-inline' https:; ".
		"script-src 'nonce-" . $CFG["nonce"] . "' 'strict-dynamic' 'unsafe-eval' www.googletagmanager.com; ".

		// Defines valid sources of stylesheets or CSS.
		//"style-src 'self' 'unsafe-inline' *.cloudfront.net *.google.com *.googletagmanager.com *.google-analytics.com *.googleapis.com; ".
		"style-src 'unsafe-inline'; ".

		// Restricts the URLs which may be loaded as a Worker, SharedWorker or ServiceWorker.
		//"worker-src 'self';";

	header($buffer);
}

function csp_nounce() {
	global $CFG;

	echo $CFG["nonce"];
}
