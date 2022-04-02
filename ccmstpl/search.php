<?
header("Content-Type: text/html; charset=utf-8");
header("Cache-Control: public, must-revalidate, proxy-revalidate");
?>{CCMS_DB_PRELOAD:all,search}<!DOCTYPE html>
<html dir="{CCMS_LIB:_default.php;FUNC:ccms_lng_dir}" lang="{CCMS_LIB:_default.php;FUNC:ccms_lng}" prefix="og: http://ogp.me/ns#">
	<head>
		<!-- Repeated content and can be placed once in your header head template. -->
		{CCMS_TPL:header-head.html}

		<!-- Should be representative of the page in reference. -->
		<title dir="{CCMS_DB_DIR:all,company-name}">{CCMS_DB:all,company-name} / {CCMS_DB:all,search}</title>
		<meta name="description" content="{CCMS_DB:search,description}" />

		<!-- Google / Search Engine Tags -->
		<meta itemprop="name" content="{CCMS_DB:all,company-name} / {CCMS_DB:all,search}" />
		<meta itemprop="description" content="{CCMS_DB:search,description}" />

		<!-- Facebook Meta Tags -->
		<meta property="og:url" content="https://{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}/{CCMS_LIB:_default.php;FUNC:ccms_lng}/" />
		<meta property="og:title" content="{CCMS_DB:all,company-name} / {CCMS_DB:all,search}">
		<meta property="og:description" content="{CCMS_DB:search,description}" />

		<!-- Twitter Meta Tags -->
		<meta name="twitter:title" content="{CCMS_DB:all,company-name} / {CCMS_DB:all,search}" />
		<meta name="twitter:description" content="{CCMS_DB:search,description}" />

		<script type="application/ld+json">
			[{
				"@context":"https://schema.org",
				"@type":"WebSite",
				"name":"{CCMS_DB:all,company-name}",
				"url":"https://{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}/{CCMS_LIB:_default.php;FUNC:ccms_lng}/search.html",
				"alternateName":"{CCMS_DB:all,company-name} / {CCMS_DB:all,search}"
			},{
				"@context":"https://schema.org",
				"@type":"Dentist",
				"name":"{CCMS_DB:all,company-name}",
				"description":"{CCMS_DB:search,description}",
				"url":"https://{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}/{CCMS_LIB:_default.php;FUNC:ccms_lng}/search.html",
				"logo":{
					"@type":"ImageObject",
					"url":"{CCMS_LIB:site.php;FUNC:load_resource("AWS")}/ccmstpl/_img/logo1.3.png"
				},
				"telephone":"(714) 835-4441",
				"email":"info@{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}",
				"priceRange":"Call for pricing options",
				"address":{
					"@type":"PostalAddress",
					"addressLocality":"Tustin",
					"addressRegion":"CA",
					"postalCode":"92780",
					"streetAddress":"17501 Irvine Blvd Ste 101 Tustin",
					"addressCountry":"US"
				},
				"sameAs":[
					/*"https://twitter.com/mywebsite",*/
					"https://www.instagram.com/iconic.dds/?hl=en",
					/*"https://plus.google.com/+mywebsite",*/
					"https://www.facebook.com/tustindentistry/"
					/*"https://www.pinterest.com/mywebsite/",*/
					/*"https://www.youtube.com/user/mywebsite"*/
				],
				"geo":{
					"@type":"GeoCoordinates",
					"latitude":33.748438,
					"longitude":-117.82662
				},
				"openingHours":["Mon-Fri: 9am - 5pm", "Sat-Sun: Closed"],
				"image":"{CCMS_LIB:site.php;FUNC:load_resource("AWS")}/ccmstpl/_img/business-card.png"
			}]
		</script>

		<style nonce="{CCMS_LIB:site.php;FUNC:csp_nounce}">
			{CCMS_TPL:/_css/header.html}

			.webp .search_01{background-image:url("{CCMS_LIB:site.php;FUNC:load_resource("AWS")}/ccmstpl/_img/search_01.webp")}

			.no-webp .search_01{background-image:url("{CCMS_LIB:site.php;FUNC:load_resource("AWS")}/ccmstpl/_img/search_01-min.jpg")}

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
		<script nonce="{CCMS_LIB:site.php;FUNC:csp_nounce}">
			var navActiveArray = ["nl-search","nl-search-1","nl-lng","lng-{CCMS_LIB:_default.php;FUNC:ccms_lng}"];
			var navActiveFooterArray = ["fl-search"];
		</script>
	</head>
	<body>
		<div id="loading_svg"></div>
		<main class="cd-main-content">
			<div class="parallax search_01"></div>
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
					<script nonce="{CCMS_LIB:site.php;FUNC:csp_nounce}">
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
							gcse.setAttribute("nonce", "{CCMS_LIB:site.php;FUNC:csp_nounce}");
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
<!-- {  CCMS_TPL:msg-popup.html} -->
{CCMS_TPL:a2hs-box.html}
		<script nonce="{CCMS_LIB:site.php;FUNC:csp_nounce}">
			/*window.performance.mark("mark_beginning_javascript");*/
{CCMS_TPL:footer-js.html}
			function loadFirst(e,t,i,c){var a=document.createElement("script");a.async="true";a.setAttribute("nonce", "{CCMS_LIB:site.php;FUNC:csp_nounce}");if(i){a.integrity=i;a.crossOrigin=c;}a.readyState?a.onreadystatechange=function(){("loaded"==a.readyState||"complete"==a.readyState)&&(a.onreadystatechange=null,t())}:a.onload=function(){t()},a.src=e,document.body.appendChild(a)}

			/*
			Argument details for build_css_link2() and build_js_link() function calls:
			arg1 = (1 = append AWS link), (empty = do not append AWS link)
			arg2 = (1 = append language code to link), (empty = do not append language code to link)	In other words, send it through the parser first like a normal template.
			arg3 = a variable found in the config file that represents a partial pathway to the style sheet, not including and details about AWS, language code, or language direction)
			arg4 = (1 = append language direction to link), (empty = do not append language direction to link)

			Argument details for build_js_sri() function calls:
			arg1 = 1 = build sri code based on version stored on AWS.  empty = build sri code based on version stored on our own server.
			arg2 = a variable found in the config file that represents a partial pathway to the style sheet. (Not including details about AWS, language code, or language direction)
			*/

			{CCMS_LIB:site.php;FUNC:build_css_link("1","","CSS-01","1")}

			function loadJSResources(){
				loadFirst("{CCMS_LIB:site.php;FUNC:build_js_link("1","","MODERNIZER")}",function(){

					loadFirst("{CCMS_LIB:site.php;FUNC:build_js_link("1","","JQUERY")}",function(){

						loadFirst("{CCMS_LIB:site.php;FUNC:build_js_link("1","","JQUERY-MOBILE-CUST")}",function(){

							loadFirst("{CCMS_LIB:site.php;FUNC:build_js_link("1","","JQUERY-VALIDATE")}",function(){

								loadFirst("{CCMS_LIB:site.php;FUNC:build_js_link("1","","JQUERY-VALIDATE-ADDITIONAL-METHODS")}",function(){

									loadFirst("{CCMS_LIB:site.php;FUNC:build_js_link("1","","JS-01")}",function(){

										/*window.performance.mark("mark_end_javascript");
										console.log(window.performance.getEntriesByName("mark_beginning_javascript"));
										console.log(window.performance.getEntriesByName("mark_end_javascript"));*/
									}{CCMS_LIB:site.php;FUNC:build_js_sri("1","JS-01")});
								}{CCMS_LIB:site.php;FUNC:build_js_sri("1","JQUERY-VALIDATE-ADDITIONAL-METHODS")});
							}{CCMS_LIB:site.php;FUNC:build_js_sri("1","JQUERY-VALIDATE")});
						}{CCMS_LIB:site.php;FUNC:build_js_sri("1","JQUERY-MOBILE-CUST")});
					}{CCMS_LIB:site.php;FUNC:build_js_sri("1","JQUERY")});
				}{CCMS_LIB:site.php;FUNC:build_js_sri("1","MODERNIZER")});
			}

			if(window.addEventListener){window.addEventListener("load",loadJSResources,false)}
			else if(window.attachEvent){window.attachEvent("onload",loadJSResources)}
			else{window.onload=loadJSResources}
		</script>
	</body>
</html>
