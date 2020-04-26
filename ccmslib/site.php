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
		if($aws_flag){
			$url .= "";
		}else{
			$url .= "/" . ccms_lng_ret();
		}
	}

	$url .= $CFG["RES"][$path];

	if($dir_flag){
		$url .= "-" . ccms_lng_dir_ret();
	}

	$buff .= $url . '.css';

	$buff .= '";';

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
		if($aws_flag){
			$url .= "";
		}else{
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
