<?
header("Content-Type: text/html; charset=UTF-8");
header("Cache-Control: public, must-revalidate, proxy-revalidate");
?>{CCMS_DB_PRELOAD:all}<!DOCTYPE html>
<html dir="{CCMS_LIB:_default.php;FUNC:ccms_lng_dir}" lang="{CCMS_LIB:_default.php;FUNC:ccms_lng}" prefix="og: http://ogp.me/ns#">
	<head>
		<!-- Repeated content and can be placed once in your header head template. -->
		{CCMS_TPL:header-head.html}
		<!-- Should be representative of the page in reference. -->
		<title dir="{CCMS_LIB:_default.php;FUNC:ccms_lng_dir}">ICONIC Dentistry / {CCMS_DB:all,search}</title>
		<meta property="og:title" dir="{CCMS_DB_DIR:index,title}" content="{CCMS_DB:index,title}" />
		<meta name="twitter:title" dir="{CCMS_DB_DIR:index,title}" content="{CCMS_DB:index,title}" />
		<meta name="description" dir="{CCMS_DB_DIR:index,description}" content="{CCMS_DB:index,description}" />
		<meta property="og:description" dir="{CCMS_DB_DIR:index,description}" content="{CCMS_DB:index,description}" />
		<meta name="twitter:description" dir="{CCMS_DB_DIR:index,description}" content="{CCMS_DB:index,description}" />
		<meta property="og:image" content="/ccmstpl/_img/main-image-or-logo.jpg" />
		<meta property="og:image:secure_url" content="/ccmstpl/_img/main-image-or-logo.jpg" />
		<meta property="og:image:width" content="123" />
		<meta property="og:image:height" content="123" />
		<!-- Must be at least 60px by 60px.	Images greater than 120px by 120px will be resized and cropped in a square aspect ratio. -->
		<meta name="twitter:image" content="/ccmstpl/_img/main-image-or-logo.jpg" />
		<meta property="og:url" content="https://{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}/" />
		<script type="application/ld+json">
			[{
				"@context":"https://schema.org",
				"@type":"WebSite",
				"name":"mywebsite",
				"url":"https://{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}",
				"alternateName":"mywebsite description"
			},{
				"@context":"https://schema.org",
				"@type":"Organization",
				"name":"mywebsite name, LLC",
				"url":"https://{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}",
				"logo":{
					"@type":"ImageObject",
					"url":"/ccmstpl/_img/main-image-or-logo.jpg"
				},
				"sameAs":[
					"https://twitter.com/mywebsite",
					"https://instagram.com/mywebsite",
					"https://plus.google.com/+mywebsite",
					"https://www.facebook.com/mywebsite",
					"https://www.pinterest.com/mywebsite/",
					"https://www.youtube.com/user/mywebsite"
				]
			}]
		</script>
		<style>
			{CCMS_TPL:/_css/header.html}

			.gsc-adBlock{opacity:.3 !important}

			.gsc-input {
				outline:none !important;
				display:block !important;
				width:99% !important;
				height:34px !important;
				margin:0px 2px 0px 0px !important;
				color:#555 !important;
				background-color:#fff !important;
				background-image:none !important;
				border-radius:4px !important
			}

			.gsc-search-button{margin-left:unset !important}

			.gsc-search-button-v2{
				color:#fff !important;
				background-color:var(--cl2) !important;
				border-color:var(--cl2) !important;
				display:inline-block !important;
				padding:10px 20px !important;
				text-align:center !important;
				white-space:nowrap !important;
				vertical-align:middle !important;
				-ms-touch-action:manipulation !important;
				touch-action:manipulation !important;
				cursor:pointer !important;
				-webkit-user-select:none !important;
				-moz-user-select:none !important;
				-ms-user-select:none !important;
				user-select:none !important;
				background-image:none !important;
				border:1px solid transparent !important;
				border-radius:4px !important;
				height:auto !important
			}

			.gsc-search-button-v2>svg>path{fill:#fff}

			.gsc-search-button-v2:hover {
				color:#fff !important;
				background-color:var(--cl2-tran) !important;
				border-color:var(--cl2-tran) !important
			}

			.gsib_a{padding:0 0 0 10px !important}
		</style>
		<script>
			var navActiveArray = ["nl-search","nl-search-1","nl-lng","lng-{CCMS_LIB:_default.php;FUNC:ccms_lng}"];
			var navActiveFooterArray = ["fl-search"];
		</script>
	</head>
	<body>
		<div id="loading_svg"></div>
		<main class="cd-main-content">
			<div class="parallax" style="background-image:url('/ccmstpl/_img/search-01-min.jpg')"></div>
			<div style="padding-bottom:15px">
				<div class="bcGrid" dir="{CCMS_LIB:_default.php;FUNC:ccms_lng_dir}">
					<h1 class="c1" dir="{CCMS_DB_DIR:all,search}">{CCMS_DB:all,search}</h1>
					<i class="c2"></i>
					<h4 class="c3" style="margin:8px 0 0">
						<a dir="{CCMS_DB_DIR:all,homepage}" href="/{CCMS_LIB:_default.php;FUNC:ccms_lng}/">{CCMS_DB:all,homepage}</a> <span style="color:#888888">/</span> <span dir="{CCMS_DB_DIR:all,search}" style="color:var(--cl2)">{CCMS_DB:all,search}</span>
					</h4>
				</div>
			</div>
			<div class="contentType1">
				<div>
<? if($CFG["GOOGLE_CUSTOM_SEARCH_ENGINE_CODE"] != ""): ?>
					<script>
						var gcsForm = function() {
							var element = google.search.cse.element.getElement('gcsForm');
							element.execute('<?=htmlspecialchars($_REQUEST["search"]);?>');
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
<? else: ?>
					<p>Google Search Results / Custom Search Engine (CSE)</p>
					<p>To add Google Custom Search results to this page visit <a href="https://cse.google.com/cse/all" target="_blank">https://cse.google.com/cse/all</a> and create a new CSE code.  Copy the code (e.g.: 010508916976745981301:bdscggyxyle) into the $CFG["GOOGLE_CUSTOM_SEARCH_ENGINE_CODE"] variable in your config file and you are done.</p>
<? endif; ?>
				</div>
			</div>
{CCMS_TPL:footer.html}
		</main>
{CCMS_TPL:header-body.html}
{CCMS_TPL:msg-popup.html}
{CCMS_TPL:a2hs-box.html}
		<script>
			/*window.performance.mark("mark_beginning_javascript");*/
{CCMS_TPL:footer-js.html}
			function loadFirst(e,t){var a=document.createElement("script");a.async="true";a.readyState?a.onreadystatechange=function(){("loaded"==a.readyState||"complete"==a.readyState)&&(a.onreadystatechange=null,t())}:a.onload=function(){t()},a.src=e,document.body.appendChild(a)}

			var l=document.createElement("link");l.rel="stylesheet";
			l.href = "{CCMS_LIB:site.php;FUNC:css_01}";
			var h=document.getElementsByTagName("head")[0];h.parentNode.insertBefore(l,h);

			function loadJSResources(){
				loadFirst("{CCMS_LIB:site.php;FUNC:load_resource("JQUERY")}",function(){

					loadFirst("{CCMS_LIB:site.php;FUNC:load_resource("JQUERY-MOBILE-CUST")}",function(){

						loadFirst("{CCMS_LIB:site.php;FUNC:load_resource("JQUERY-VALIDATE")}",function(){

							loadFirst("{CCMS_LIB:site.php;FUNC:load_resource("JQUERY-VALIDATE-ADDITIONAL-METHODS")}",function(){

								loadFirst("{CCMS_LIB:site.php;FUNC:load_resource("MODERNIZER")}",function(){

									loadFirst("{CCMS_LIB:site.php;FUNC:js_01}",function(){

										/*window.performance.mark("mark_end_javascript");
										console.log(window.performance.getEntriesByName("mark_beginning_javascript"));
										console.log(window.performance.getEntriesByName("mark_end_javascript"));*/
									});
								});
							});
						});
					});
				});
			}

			if(window.addEventListener){window.addEventListener("load",loadJSResources,false)}
			else if(window.attachEvent){window.attachEvent("onload",loadJSResources)}
			else{window.onload=loadJSResources}
		</script>
	</body>
</html>
