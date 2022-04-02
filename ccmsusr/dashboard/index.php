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
?><!DOCTYPE html>
<html lang="{CCMS_LIB:_default.php;FUNC:ccms_lng}">
	<head>
		<title><?= $_SERVER["SERVER_NAME"];?> | User | Dashboard</title>
		{CCMS_TPL:head-meta.html}
	</head>
	<style>
		{CCMS_TPL:/_css/head-css.html}

		button:hover{background-color:transparent}

		.blacklistIpAddress{
			font-size:.8em;
			color:var(--cl11);
			cursor:pointer
		}

		.cssGrid-Dashboard-01{
			display:grid;
			grid-gap:1em;
		}

		#ccms_compress_button{
			float:left;
			left:10px;
			position:relative;
			top:5px
		}

		#ccms_compress_button:hover{
			background-color:unset;
			color:var(--cl0)
		}

		#ccms_news_items{padding-left:30px}

		#ccms_news_items li{margin-bottom:10px}

		#ccms_news_reload_button,#ccms_security_logs_reload_button{
			float:right;
			position:relative;
			right:0;
			top:5px
		}

		#ccms_security_logs{display:none}

		#ccms_security_logs_hidden{display:block}

		/* 875px or larger. Pixel Xl Landscape resolution is 411 x 823. */
		@media only screen and (min-width: 875px){
			.cssGrid-Dashboard-01{
				grid-template-areas:
					"c1 c2"
			}
		}
	</style>
	<script nonce="{CCMS_LIB:_default.php;FUNC:ccms_csp_nounce}">
		let navActiveItem = ["nav-dashboard"];
		let navActiveSub = [];
		let navActiveW3schoolsItem = [];
	</script>
	<body>
		<main style="padding:20px 20px 20px 0">
			<h1 style="border-bottom:1px dashed var(--cl3)">Dashboard</h1>
			<p>This section is still under development, but if you come across any unresolved issues please let us know at: <a class="ccms_a" href="mailto:info@custodiancms.org?subject=unresolved+issue+report">info@custodiancms.org</a></p>


				<div id="msg_div" role="alert" class="ccms_msg"></div>

				<div class="modal">
				<div>
					<span style="float:left">Security Logs</span>
					<button class="svg_icon svg_compress_button" id="ccms_compress_button" title="Compress Show/Hide"></button>
					<button class="svg_icon svg_reload_button" id="ccms_security_logs_reload_button" title="Reload"></button>
				</div>
				<div>
					<p>List of sessions and or form calls, found in the 'ccms_log' table, that failed.<?php if($CFG["LOG_EVENTS"] === 0){echo '<br><span class="blacklistIpAddress">Currently disabled in config. Only old logs displayed below for now, if any.</span>';}?></p>
					<div id="ccms_security_logs"></div>
					<div id="ccms_security_logs_hidden">Click the <svg class="svg_icon" style="bottom:-5px;cursor:text;fill:var(--cl4);position:relative" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17,20H13V16.41l.79.8a1,1,0,0,0,1.42,0,1,1,0,0,0,0-1.42l-2.5-2.5a1,1,0,0,0-.33-.21,1,1,0,0,0-.76,0,1,1,0,0,0-.33.21l-2.5,2.5a1,1,0,0,0,1.42,1.42l.79-.8V20H7a1,1,0,0,0,0,2H17a1,1,0,0,0,0-2ZM7,4h4V7.59l-.79-.8A1,1,0,1,0,8.79,8.21l2.5,2.5a1,1,0,0,0,.33.21.94.94,0,0,0,.76,0,1,1,0,0,0,.33-.21l2.5-2.5a1,1,0,1,0-1.42-1.42l-.79.8V4h4a1,1,0,0,0,0-2H7A1,1,0,0,0,7,4Z"/></svg> icon above to show/hide the Security Logs table.</div>
				</div>
			</div>

			<div class="cssGrid-Dashboard-01">
				<div class="modal">
					<div>System Info</div>
					<div>
						<p style="word-break:break-all">Server Name: <span class="oj"><?= $_SERVER["SERVER_NAME"];?></span></p>
						<p style="word-break:break-all">Document Root: <span class="oj"><?=$_SERVER["DOCUMENT_ROOT"];?></span></p>
						<p>System Address: <span class="oj"><?= $_SERVER["SERVER_ADDR"];?></p>
						<p>Web Server: <span class="oj"><?php $a = explode(" ",$_SERVER["SERVER_SOFTWARE"]);echo $a[0];?></span></p>
						<p>PHP Version: <span class="oj"><?= phpversion();?></span></p>
						<p>PHP Memory Limit: <span class="oj"><?= ini_get("memory_limit");?></span></p>
						<p>MySQL Version: <span class="oj"><?= $CFG["DBH"]->getAttribute(PDO::ATTR_SERVER_VERSION);?></span></p>
						<p>COOKIE_SESSION_EXPIRE: <span class="oj"><?= $CFG["COOKIE_SESSION_EXPIRE"];?></span></p>
						<p>HTML_MIN: <span class="oj"><?= $CFG["HTML_MIN"];?></span></p>
						<p>CACHE: <span class="oj"><?= $CFG["CACHE"];?></span></p>
						<p>CACHE_EXPIRE: <span class="oj"><?= $CFG["CACHE_EXPIRE"];?></span></p>
						<p>LOG_EVENTS: <span class="oj"><?= $CFG["LOG_EVENTS"];?></span></p>
						<p>EMAIL_FROM: <span class="oj"><?= $CFG["EMAIL_FROM"];?></span></p>
						<p style="word-break:break-all">EMAIL_BOUNCES_RETURNED_TO: <span class="oj"><?= $CFG["EMAIL_BOUNCES_RETURNED_TO"];?></span></p>
					</div>
				</div>

				<div class="modal">
					<div>CustodianCMS.org News
						<button class="svg_icon svg_reload_button" id="ccms_news_reload_button" title="Reload"></button>
					</div>
					<div id="ccms_news_items">
						<p>Nothing to see at the moment.</p>
					</div>
				</div>
			</div>

			<div class="modal">
				<div>License Info</div>
				<div>
					@Version
					<p style="margin-left:20px;">
						{CCMS_LIB:_default.php;FUNC:ccms_version} (Release Date: {CCMS_LIB:_default.php;FUNC:ccms_release_date})
					</p>
					@Copyright
					<p style="margin-left:20px;">
						&copy; {CCMS_LIB:_default.php;FUNC:ccms_dateYear} assigned by Vincent Hallberg of <a class='ccms_a' href="https://custodiancms.org" rel="noopener" target="_blank">custodiancms.org</a> and <a class='ccms_a' href="https://modusinternet.com" rel="noopener" target="_blank">modusinternet.com</a>
					</p>
					<span style="margin:0 20px">License (MIT)</span>
					<p>Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:</p>
					<p>The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.</p>
					<p>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.</p>
				</div>
			</div>





			<ul>
				<li>HTML Minify</li>
				<li>Templates in Database Cache</li>
				<li>Clear Cache</li>
				<li>Backup/Restore</li>
				<li>Password Recovery attempts currently in the ccms_password_recovery table</li>
			</ul>




			{CCMS_TPL:/footer.html}
		</main>

		{CCMS_TPL:/body-head.php}

		<script nonce="{CCMS_LIB:_default.php;FUNC:ccms_csp_nounce}">
			{CCMS_TPL:/_js/footer-1.php}

			var l=document.createElement("link");l.rel="stylesheet";
			l.href = "/ccmsusr/_css/custodiancms.css";
			var h=document.getElementsByTagName("head")[0];h.parentNode.insertBefore(l,h);

			var l=document.createElement("link");l.rel="stylesheet";
			l.href = "/ccmsusr/_css/metisMenu-3.0.6.min.css";
			var h=document.getElementsByTagName("head")[0];h.parentNode.insertBefore(l,h);

			function loadJSResources() {
				loadFirst("/ccmsusr/_js/jquery-3.6.0.min.js", function() {
					loadFirst("/ccmsusr/_js/metisMenu-3.0.7.min.js", function() {
						loadFirst("/ccmsusr/_js/custodiancms.js", function() {
							loadFirst("/ccmsusr/_js/jquery-validate-1.19.3.min.js", function() {


								/* user_dropdown START */
								/* When the user clicks on the svg button add the 'show' class to the dropdown box below it. */
								$("#user_dropdown_btn").click(function() {
									$("#user_dropdown_list").addClass("show");
								});

								/* Hide dropdown menu on click outside */
								$(document).on("click", function(e){
									if(!$(e.target).closest("#user_dropdown_btn").length){
										$("#user_dropdown_list").removeClass("show");
									}
								});
								/* user_dropdown END */


								/* Fetch Cache BEGIN */
								// (URL to call, Max expire time after saved in localhost) 3600 = seconds is equivalent to 1 hour
								cachedFetch('https://custodiancms.org/cross-origin-resources/news.php', 3600)
									.then(r => r.text())
									.then(content => {
										document.getElementById("ccms_news_items").innerHTML = content;
								});

								document.getElementById("ccms_news_reload_button").addEventListener("click", () => {
									const url = "https://custodiancms.org/cross-origin-resources/news.php";
									localStorage.removeItem(url);
									localStorage.removeItem(url + ":ts");
									// 3600 = seconds is equivalent to 1 hour
									cachedFetch(url, 3600)
										.then(r => r.text())
										.then(content => {
											document.getElementById("ccms_news_items").innerHTML = content;
											const msg_div = document.getElementById('msg_div');
											msg_div.classList.add("active", "success");
											msg_div.textContent = "CustodianCMS.org News Reloaded";
											setTimeout(function() {
												msg_div.classList.remove("active", "success");
											},15000);
											window.onclick = function(event) {
												if(event.target != msg_div) {
													msg_div.classList.remove("active", "success");
												}
											}
									});
								});


								function securityLogTable(data) {
									document.getElementById("ccms_security_logs").innerHTML = "";

									if(data === null) {
										document.getElementById("ccms_security_logs").innerHTML = '<p class="blacklistIpAddress">Nothing to see at the moment.</p>';
										return;
									}

									if(data[0].errorMsg) {
										document.getElementById("ccms_security_logs").innerHTML = "<p>" + data[0].errorMsg + "</p>";
										return;
									}

									var mainContainer = document.getElementById("ccms_security_logs");

									// Get values for the table headers.
									// ie: {'ID', 'Date', 'IP' , 'URL','Log'}
									var tablecolumns = [];
									for(var i = 0; i < data.length; i++) {
										for(var key in data[i]) {
											if(tablecolumns.indexOf(key) === -1) {
												tablecolumns.push(key);
											}
										}
									}

									var divTable = document.createElement("div");
									divTable.className = 'table';

									var divTableHeaderRow = document.createElement("div");
									divTableHeaderRow.className = 'tableRow';
									divTable.appendChild(divTableHeaderRow);

									for(var i = 0; i < tablecolumns.length; i++) {
										//console.log(tablecolumns[i]);
										var div = document.createElement("div");
										div.className = 'tableCell tableHead';

										if(tablecolumns[i] == "id"){
											tablecolumns[i] = tablecolumns[i].toUpperCase();
										}
										if(tablecolumns[i] == "date"){
											div.setAttribute("title", "YYYY-MM-DD HH-MM-SS");
										}
										if(tablecolumns[i] == "ip"){
											tablecolumns[i] = tablecolumns[i].toUpperCase();
										}
										if(tablecolumns[i] == "url"){
											tablecolumns[i] = tablecolumns[i].toUpperCase();
										}

										div.innerHTML = tablecolumns[i];
										divTableHeaderRow.appendChild(div);
									}

									// Add one more empty div at the end of the header to contain stuff like a delete or edit button.
									var div = document.createElement("div");
									div.className = 'tableCell tableHead';
									div.innerHTML = "";
									divTableHeaderRow.appendChild(div);

									for(let i = 0; i < data.length; i++) {
										let divTableRow = document.createElement("div");
										divTableRow.setAttribute("class", "tableRow");
										divTableRow.setAttribute("id", "sec-log-row-id-" + data[i].id);

										const date = new Date(data[i].date*1000);

										// Year
										let year = date.getFullYear();
										// Month
										let month = ("0" + (date.getMonth() + 1)).slice(-2);
										// Day
										let day = ("0" + date.getDate()).slice(-2);
										// Hours
										let hours = date.getHours();
										// Minutes
										let minutes = "0" + date.getMinutes();
										// Seconds
										let seconds = "0" + date.getSeconds();

										const convdataTime = '<span style="white-space:nowrap">'+year+'-'+month+'-'+day+'</span><br>'+hours+':'+minutes.substr(-2)+':'+seconds.substr(-2);

										divTableRow.innerHTML = '<div class="tableCell">'+ data[i].id
										+ '</div><div class="tableCell">' + convdataTime
										+ '</div><div class="tableCell">' + data[i].ip
										+ '<br><span class="blacklistIpAddress" data-ip="' + data[i].ip
										+ '">(Blacklist)</span></div><div class="tableCell" style="line-break:anywhere;min-width:200px">' + data[i].url
										+ '</div><div class="tableCell" style="min-width:390px;width:100%">' + data[i].log
										+ '</div><div class="tableCell" style="text-align:center"><button class="svg_icon svg_delete_button" data-id="' + data[i].id
										+ '" title="Delete"></button></div>';

										divTable.appendChild(divTableRow);
									}

									mainContainer.appendChild(divTable);

									var delBut = document.getElementsByClassName('svg_delete_button');
									for(var i = 0; i < delBut.length; i++){
										const id = delBut[i].getAttribute('data-id');
										delBut[i].onclick = function(){
											let url = "/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/dashboard/logs_delete.php";
											fetch(url + "?token=" + Math.random() + "&ajax_flag=1&id=" + id)
												.then(response => response.json())
												.then(obj => {
													/*
													if(data.success === "0") { // success
														console.log(id + " deleted");
														document.getElementById("sec-log-row-id-" + id).outerHTML = "";
													} else if(data.success === "1") { // already deleted
														console.log(id + " already deleted");
														document.getElementById("sec-log-row-id-" + id).outerHTML = "";
													} else if(data.error === "Session Error") {
														document.getElementById("ccms_security_logs").innerHTML = "<p>Session Error</p>";
													} else {
														document.getElementById("ccms_security_logs").innerHTML = "<p>Error: See console for more detail.</p>";
														console.log(data);
													}
													*/

													const msg_div = document.getElementById('msg_div');
													if(obj.success) {
														document.getElementById("sec-log-row-id-" + id).outerHTML = "";
														msg_div.classList.add("active", "success");
														msg_div.textContent = obj.success;
														setTimeout(function() {
															msg_div.classList.remove("active", "success");
														},15000);
														window.onclick = function(event) {
															if(event.target != msg_div) {
																msg_div.classList.remove("active", "success");
															}
														}
													} else {
														msg_div.classList.add("active", "error");
														msg_div.textContent = obj.error;
														setTimeout(function() {
															msg_div.classList.remove("active", "error");
														},15000);
														window.onclick = function(event) {
															if(event.target != msg_div) {
																msg_div.classList.remove("active", "error");
															}
														}
													}

												}
											).catch(console.error);
										}
									}

									var blacklistBut = document.getElementsByClassName('blacklistIpAddress');
									for(var i = 0; i < blacklistBut.length; i++){
										const ip = blacklistBut[i].getAttribute('data-ip');
										blacklistBut[i].onclick = function(){
											let url = "/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/dashboard/addIpAddressToBlacklist.php";
											fetch(url + "?token=" + Math.random() + "&ajax_flag=1&ip=" + ip)
											.then(response => response.json())
											.then(obj => {
												const msg_div = document.getElementById('msg_div');
												if(obj.success) {
													msg_div.classList.add("active", "success");
													msg_div.textContent = obj.success;
													setTimeout(function() {
														msg_div.classList.remove("active", "success");
													},15000);
													window.onclick = function(event) {
														if(event.target != msg_div) {
															msg_div.classList.remove("active", "success");
														}
													}
												} else {
													msg_div.classList.add("active", "error");
													msg_div.textContent = obj.error;
													setTimeout(function() {
														msg_div.classList.remove("active", "error");
													},15000);
													window.onclick = function(event) {
														if(event.target != msg_div) {
															msg_div.classList.remove("active", "error");
														}
													}
												}
											}).catch(console.error);
										}
									}
								}

								// (URL to call, Max expire time after saved in localhost) 3600 = seconds is equivalent to 1 hour
								//cachedFetch('/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/dashboard/logs.php', 3600)
								cachedFetch('/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/dashboard/logs.php')
									.then(r => r.json())
									.then(content => {
										securityLogTable(content);
									}
								);

								document.getElementById("ccms_security_logs_reload_button").addEventListener("click", () => {
									const url = "/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/dashboard/logs.php";
									localStorage.removeItem(url);
									localStorage.removeItem(url + ":ts");
									//document.getElementById("ccms_security_logs").innerHTML = "";
									// 3600 = seconds is equivalent to 1 hour
									//cachedFetch(url, 3600)
									cachedFetch(url)
										.then(r => r.json())
										.then(content => {
											securityLogTable(content);
											const msg_div = document.getElementById('msg_div');
											msg_div.classList.add("active", "success");
											msg_div.textContent = "Security Log Table Reloaded";
											setTimeout(function() {
												msg_div.classList.remove("active", "success");
											},15000);
											window.onclick = function(event) {
												if(event.target != msg_div) {
													msg_div.classList.remove("active", "success");
												}
											}
										}
									);
								});

								function ccms_security_logs() {
									let compressed = localStorage.getItem("ccms_security_logs_compress");
									let a = document.querySelector('#ccms_security_logs');
									let b = document.querySelector('#ccms_security_logs_hidden');
									if(compressed == null || compressed == 0) {
										a.style.display = 'block';
										b.style.display = 'none';
										localStorage.setItem("ccms_security_logs_compress", 0);
									} else {
										a.style.display = 'none';
										b.style.display = 'block';
										localStorage.setItem("ccms_security_logs_compress", 1);
									}
								}
								/* Fetch Cache BEGIN */


								document.getElementById("ccms_compress_button").addEventListener("click", () => {
									let compressed = localStorage.getItem("ccms_security_logs_compress");
									let a = document.querySelector('#ccms_security_logs');
									let b = document.querySelector('#ccms_security_logs_hidden');
									if(compressed == null || compressed == 1) {
										a.style.display = 'block';
										b.style.display = 'none';
										localStorage.setItem("ccms_security_logs_compress", 0);
									} else {
										a.style.display = 'none';
										b.style.display = 'block';
										localStorage.setItem("ccms_security_logs_compress", 1);
									}
								});

								setTimeout(function() {ccms_security_logs();}, 1000);
							});
						});
					});
				});
			}
		</script>
	</body>
</html>
