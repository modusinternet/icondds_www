<?php
http_response_code(400);
header("Content-Type: text/html; charset=UTF-8");
header("Cache-Control: public, must-revalidate, proxy-revalidate");
?><!DOCTYPE html>
<html lang="<?=$CLEAN["ccms_lng"];?>">
	<head>
		<meta charset="utf-8">
		<title>400 ERROR | <?=$CFG["DOMAIN"];?></title>
		<meta name="description" content="" />
		<meta name="author" content="Developed by Vincent Hallberg of modusinternet.com." />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<!-- Favicons -->
		<link rel="icon" href="/ccmsusr/_img/favicon.ico" type="image/x-icon">

		<!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		
		<style type="text/css">
			.gsc-adBlock { opacity: .3 !important;}
			
			input.gsc-input {
				outline: none !important;
				display: block !important;
				width: 99% !important;
				height: 34px !important;
				margin: 0px 2px 0px 0px !important;
				font-size: 14px !important;
				line-height: 1.42857143 !important;
				color: #555 !important;
				background-color: #fff !important;
				background-image: none !important;
				border: 1px solid #ccc !important;
				border-radius: 4px !important;
			}
			
			input.gsc-search-button {
				color: #fff !important;
				background-color: #337ab7 !important;
				border-color: #2e6da4 !important;
				display: inline-block !important;
				padding: 6px 12px !important;
				/* margin-bottom: 10px !important; */
				font-size: 14px !important;
				font-weight: 400 !important;
				line-height: 1.42857143 !important;
				text-align: center !important;
				white-space: nowrap !important;
				vertical-align: middle !important;
				-ms-touch-action: manipulation !important;
				touch-action: manipulation !important;
				cursor: pointer !important;
				-webkit-user-select: none !important;
				-moz-user-select: none !important;
				-ms-user-select: none !important;
				user-select: none !important;
				background-image: none !important;
				border: 1px solid transparent !important;
				border-radius: 4px !important;
				height: auto !important;
			}
			
			input.gsc-search-button:hover {
				color: #fff !important;
				background-color: #286090 !important;
				border-color: #204d74 !important;
			}
		</style>
	</head>
	<body>
		{CCMS_TPL:examples/header-body.html}
		<p>
			Current Location:
			/ <a href="http://<?=$CFG["DOMAIN"];?>">Homepage</a>
			/ <span style="border-bottom:1px dotted #ec7f27;">404 ERROR</span>
		</p>
		<div style="float:left; width:300px;">
			<h3 style="margin-top:0px;">400 ERROR:</h3>
			<p style="color:red; word-break: break-all;"><?=htmlspecialchars($_SERVER["REQUEST_URI"]);?> not found.  The request cannot be fulfilled due to bad syntax.</p>
			<p style="border:1px dotted red; font:12px/21px HelveticaNeue, 'Helvetica Neue', Helvetica, Arial, sans-serif !important; padding:7px;">
				<span style="font-weight:bold">NOTE: <abbr style="border-bottom:1px dotted black; cursor:help;" title="In computing, a Uniform Resource Identifier (URI) is a string of characters used to identify a name or a resource on the Internet.  You can generally see the content of a URI in the navigation bar at the top of your browser after you clicked on any link.">URI</abbr>'s on this site are parsed based on a 2-5 character language code first.<br />
				For example:</span><br />
				<br />
				abc.com/en<br />
				abc.com/en-us/<br />
				abc.com/fr/<br />
				abc.com/zh-cn<br />
				<br />
				<span style="font-weight:bold;">Followed by the 1-255 character long name of the dir and or page desired.<br />
				For example:</span><br />
				<br />
				abc.com/en/page1.html<br />
				abc.com/en-us/apples<br />
				abc.com/fr/fruit/oranges/<br />
				abc.com/zh-cn/fruit/water-melons.html
			</p>
		</div>
		<div style="float:left; margin-left:20px; width:600px;">
			<h3 style="margin-top:0px;">Search Options</h3>
				<p><?=$CFG["DOMAIN"];?> Alternate Language Search Results<br />
				<br />
<?
/* Trim off anything before the first / and save in $lng. */
$lng = htmlspecialchars(preg_replace('/^\/([a-z]{2}(-[a-z]{2})?)\/(.*)\z/i', '${1}', $_SERVER['REQUEST_URI']));

/* Trim off everything after the first / and save in $tmp. */
$tpl = htmlspecialchars(preg_replace('/^\/([a-z]{2}(-[a-z]{2})?)\/(.*)\z/i', '${3}', $_SERVER['REQUEST_URI']));

$tpl = ltrim($tpl, "/");

/* Replace any occurence of / in $tmp with a space instead and save for use with Google Search. */
$tpl2 = htmlspecialchars(preg_replace('/[\/*]/i', ' ', $tpl));

$tpl2 = htmlspecialchars(preg_replace('/(\.html|\.htm|\.php)/i', '', $tpl2));

if($tpl == ""){
	/* This fixes URI calls that look like 'mydomain.com/asdf' for the code below because there was only 1 variable after the domain name. */
	$tpl = $lng;
	$lng = "";
}
$qry = $CFG["DBH"]->prepare("SELECT lng FROM `ccms_lng_charset` WHERE `status` = 1 ORDER BY lng ASC;");
if($qry->execute()) {
	while($row = $qry->fetch()) {
		echo "\t\t\t<a href=\"/" . $row["lng"] . "/" . $tpl . "\" style=\"word-break: break-all;\">" . $CFG["DOMAIN"] . "/" . $row["lng"] . "/" . $tpl . "</a><br />\n";
	}
}
?></p>
<?if($CFG["GOOGLE_CUSTOM_SEARCH_ENGINE_CODE"] != ""):?>
				<p>
					Google Search Results<br />
					<br />
					
					
<script>
	var gcsForm = function() {
		var element = google.search.cse.element.getElement('gcsForm');
		element.execute('<?=$tpl2;?>');
	};
	var myCallback = function() {
		if (document.readyState == 'complete') {
			gcsForm();
		} else {
			google.setOnLoadCallback(gcsForm, true);
		}
	};
	window.__gcse = {
		callback: myCallback
	};
	(function() {
		var cx = '<?=$CFG["GOOGLE_CUSTOM_SEARCH_ENGINE_CODE"];?>';
		var gcse = document.createElement('script');
		gcse.type = 'text/javascript';
		gcse.async = true;
		gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//cse.google.com/cse.js?cx=' + cx + '&language=<?=$CLEAN["ccms_lng"];?>';
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(gcse, s);
	})();
</script>
<gcse:search enableAutoComplete="true" gname="gcsForm"></gcse:search>


				</p>
<?else:?>
				<p>
					Google Search Results / Custom Search Engine (CSE)<br />
					<br />
					To add automatically generated Google search results to this page visit <a href="http://www.google.com/cse/">google.com/cse/</a> and setup a new CSE code.  Copy the code (e.g.: 010508916976745981301:bdscggyxyle) into the $CFG["GOOGLE_CUSTOM_SEARCH_ENGINE_CODE"] variable in your config file and your done.
				</p>
<?endif;?>
		</div>
		<div style="clear:both;"></div>
		<p style="margin-bottom:10px;">
			Copyright &copy; {CCMS_LIB:_default.php;FUNC:ccms_dateYear} <a href="http://modusinternet.com" title="Modus Internet : Located in Vancouver and Burnaby British Columbia we do website design, database integration, custom programming, search engine optimization (SEO) or consultation.">Modus Internet</a>. All rights reserved.
		</p>
		<script>
			/*
			Load all CSS first and JS files for your site here.  This has nothing at all to do with CCMS and you can choose to not use if you wish but it can help emensly when optimising your sites with asynchronous downloads.  Test it with Google PageSpeed Insights, https://developers.google.com/speed/pagespeed/insights/.
			*/
			window.onload = function(){
				function loadjscssfile(filename, filetype) {
					if(filetype == "js") {
						var cssNode = document.createElement('script');
						cssNode.setAttribute("type", "text/javascript");
						cssNode.setAttribute("src", filename);
					} else if(filetype == "css") {
						var cssNode = document.createElement("link");
						cssNode.setAttribute("rel", "stylesheet");
						cssNode.setAttribute("type", "text/css");
						cssNode.setAttribute("href", filename);
					}
					if(typeof cssNode != "undefined")
						document.getElementsByTagName("head")[0].appendChild(cssNode);
				}

				loadjscssfile("/ccmstpl/examples/css/style.min.css", "css");
			};
		</script>
	</body>
</html>
