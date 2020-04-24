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

function css_01(){
	global $CFG;
	echo "/" . ccms_lng_ret() . "/_css/" . $CFG["CSS-01"]  . "-" . ccms_lng_dir_ret() . ".css";
}

function js_01(){
	global $CFG;
	echo "/" . ccms_lng_ret() . "/_js/" . $CFG["JS-01"] . ".js";
}

function load_resource($argv){
	global $CFG;
	echo $CFG[$argv];
}

function sri($aws_flag = null, $url){
	global $CFG;

	if($CFG["SRI"][$url]){
		if($aws_flag){
			$tmp = $CFG["SRI"]["AWS"] . $CFG["SRI"][$url];
		}else{
			$tmp = $CFG["SRI"][$url];
		}

		$qry = $CFG["DBH"]->prepare("SELECT * FROM `sri` WHERE `url` = :url LIMIT 1;");
		$qry->execute(array(':url' => $tmp));

		$row = $qry->fetch(PDO::FETCH_ASSOC);
		if($row) {
			echo "sha384-" . $row["sri-code"];
		}
	}
}




/*
$aws_flag = if not null append AWS link
$lng_flag = if not null append language code to link
$path = partial pathway information to the style sheet, not including and details about AWS, language code, or language direction)
$dir_flag = if not null append language direction to link
*/
function build_css_link($aws_flag = null, $lng_flag = null, $path, $dir_flag = null){
	global $CFG;

	$buff = 'var l=document.createElement("link");l.rel="stylesheet";l.href="';

	$url = ""

	if($aws_flag){
		$url .= $CFG["AWS"];
	}

	if($lng_flag){
		$url .= "/" . ccms_lng_ret();
	}

	$url .= $path;

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
