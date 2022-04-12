<?php
function ccms_cfgDomain() {
	global $CFG;
	echo $CFG["DOMAIN"];
}


function ccms_cfgLibDir() {
	global $CFG;
	echo $CFG["LIBDIR"];
}


function ccms_cfgPreDir() {
	global $CFG;
	echo $CFG["PREDIR"];
}


function ccms_cfgTplDir() {
	global $CFG;
	echo $CFG["TPLDIR"];
}


function ccms_cfgUsrDir() {
	global $CFG;
	echo $CFG["USRDIR"];
}


function ccms_cfgCookieSessionExpire() {
	global $CFG;
	echo $CFG["COOKIE_SESSION_EXPIRE"];
}


function ccms_csp_nounce() {
	global $CFG;
	echo $CFG["nonce"];
}


function ccms_csp_nounce_ret() {
	global $CFG;
	return $CFG["nonce"];
}


function ccms_googleRecapPubKey() {
	global $CFG;
	echo $CFG["GOOGLE_RECAPTCHA_PUBLICKEY"];
}


function ccms_googleCredKey() {
	global $CFG;
	echo $CFG["GOOGLE_CREDENTIALS_KEY"];
}


function ccms_hrefLang_list() {
	// International targeting by listing alternate language pages.
	// https://support.google.com/webmasters/answer/189077
	// DONT FORGET to add <link rel="alternate" hreflang="x-default" href="//{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}/">
	// on your homepage below the area where this content is being generated.  It only needs to be on the home page.
	global $CFG, $CLEAN;

	$tpl1 = htmlspecialchars(preg_replace('/^\/([\pL\pN\-]*)\/?(.*)\z/i', '${2}', $_SERVER['REQUEST_URI']));
	echo "<link rel=\"alternate\" hreflang=\"x-default\" href=\"" . $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"] . "/" . $CFG["DEFAULT_SITE_CHAR_SET"] . "/" . $tpl1 . "\">";
	$qry1 = $CFG["DBH"]->prepare("SELECT * FROM `ccms_lng_charset` WHERE `status` = 1 ORDER BY lngDesc ASC;");
	if($qry1->execute()) {
		while($row = $qry1->fetch()) {
			if($row["ptrLng"]) {
				if($row["ptrLng"] != $CLEAN["ccms_lng"]) {
					// Make sure to show pointers to languages that we are currently not looking at.
					echo "<link rel=\"alternate\" hreflang=\"" . $row["ptrLng"] . "\" href=\"" . $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"] . "/" . $row["ptrLng"] . "/" . $tpl1 . "\">";
				}
			} else {
				if($row["lng"] != $CLEAN["ccms_lng"]) {
					// Make sure to show pointers to languages that we are currently not looking at.
					echo "<link rel=\"alternate\" hreflang=\"" . $row["lng"] . "\" href=\"" . $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"] . "/" . $row["lng"] . "/" . $tpl1 . "\">";
				}
			}
		}
	}
}

function ccms_canonical() {
	global $CFG, $CLEAN;

	/*
	Only use this feature on the homepage to help prevent dupicate indexing attempts by search engines when dealing with language dir.
	ie:
	https://yourdomain.com
	vs
	https://yourdomain.com/en/

	Both would contain the same content so we want Google to not index the normal domain, an index the one containing the /en/ dir instead.  The reason for this is, depending on what language page your currently viewing a site in, (eg: /en/, /fr/, /sp/). the root page content will look exactly the same, when using CustodianCMS, as the one found in the language specific sub dir.
	ie:
	https://somedomain.com
	and
	https://somedomain.com/cx/

	We need to tell search engines not to index the content on the https://somedomain.com but go ahead and index the content on the https://somedomain.com/cx/ page.
	*/

	if($_SERVER['REQUEST_URI'] === "/"){
		// The visitor is looking at the root of the website WITHOUT the language dir.
		// ie: https://yourdomain.com
		echo '<meta name="robots" content="noindex" />';
		echo '<link rel="canonical" href="' . $_SERVER['REQUEST_SCHEME'] . "://" . $CFG["DOMAIN"] . "/" . $CLEAN["ccms_lng"] . '/" />';
	} else {
		// The visitor is looking at the root of the website WITH the language dir.
		// ie: https://yourdomain.com/en/
		echo '<link rel="canonical" href="' . $_SERVER['REQUEST_SCHEME'] . "://" . $CFG["DOMAIN"] . $_SERVER['REQUEST_URI'] . '" />';
	}
}


function ccms_user_admin_slider() {
	global $CFG, $CLEAN;

	if(isset($_SESSION["USER_ID"])) {
		$json_a = json_decode($_SESSION["PRIV"], true);
	}

	if(($json_a["content_manager"]["rw"] ?? null) == 1): ?>
<style>
	#CCMSTab-slide{font:600 20px/32px "Open Sans",sans-serif;position:fixed;right:-35px;top:120px;z-index:999;-webkit-transition-duration:.3s;-moz-transition-duration:.3s;-o-transition-duration:.3s;transition-duration:.3s}#CCMSTab-slide-tab{background:#86B135;border-radius:5px 0 0 5px;box-shadow:10px 5px 10px #888;color:#fff;cursor:pointer;display:block;padding:8px 0 0 8px;position:relative;top:-62px;left:-35px;-moz-border-radius:5px 0 0 5px;-webkit-border-radius:5px 0 0 5px}#CCMSTab-slide-tab svg{width:30px}#CCMSTab-slide-tab svg>path{fill:var(--cl0)}#CCMSTab-slide-outer{position:absolute;top:-70px;left:3px;width:300px;background:#86B135;border-radius:5px 0 0 5px;box-shadow:10px 5px 10px #888;-webkit-border-radius:5px 0 0 5px;-moz-border-radius:5px 0 0 5px;}#CCMSTab-slide-inner{color:#fff;margin:15px;}#CCMSTab-slide-inner svg{float:right;margin-bottom:10px;width:30px}#CCMSTab-slide-inner svg>path{fill:var(--cl0)}#CCMSTab-slide-tab-checkbox:checked + #CCMSTab-slide{right:265px}#CCMSTab-slide-tab-checkbox{display:none}.CCMSTab-slide-header{font-size:24px;text-align:center;color:inherit;border-bottom:1px solid #fff;padding-bottom:10px;margin-bottom:10px}#CCMSEdit-edit-mode-switch{position:relative;top:-10px}#CCMSEdit-edit-mode-switch-label{position:relative;top:6px;display:inline-block;width:60px;height:34px}#CCMSEdit-edit-mode-switch-label input{display:none}.slider{position:absolute;cursor:pointer;top:4px;left:0;right:0;bottom:-3px;background-color:#ccc;-webkit-transition:.4s;transition:.4s}.slider:before{position:absolute;content:"";height:26px;width:26px;left:4px;bottom:4px;background-color:#fff;-webkit-transition:.4s;transition:.4s}#CCMSEdit-edit-mode-switch-label input{display:none}#CCMSEdit-edit-mode-switch-label input:checked + .slider{background-color:#A2D345}#CCMSEdit-edit-mode-switch-label input:focus + .slider{box-shadow:0 0 1px #A2D345}#CCMSEdit-edit-mode-switch-label input:checked + .slider:before{-webkit-transform:translateX(26px);-ms-transform:translateX(26px);transform:translateX(26px)}.slider.round{border-radius:34px}.slider.round:before{border-radius:50%}#CCMSlng-list{border:solid 1px var(--cl0);list-style:none;margin:0 0 15px 0;padding:0 10px;max-height:200px;overflow:auto;width:100%}#CCMSlng-list li{margin:5px 0;}#CCMSlng-list li > a:link,#CCMSlng-list li > a:visited{color:#fff;text-decoration:none}#CCMSlng-list li > a:hover,#CCMSlng-list li > a:active{color:#fff;text-decoration:none;border-bottom:1px solid #fff}@media (max-height:340px){#CCMSlng-list{max-height:90px;overflow-y:scroll;overflow-x:hidden}}.CCMS-wrap{position:relative}.CCMSEdit-edit-link-border{border:1px dashed #86B135;padding:30px 10px 0 5px;display:block;background-color:#E5F2CD}.CCMS-editor-but{position:absolute;top:-10px;right:20px;z-index:999;padding:4px 8px;font-size:16px;line-height:1.5;border-radius:3px;color:#fff;background-color:#86B135;border:1px solid transparent;cursor:pointer;vertical-align:middle;text-align:center;box-shadow:5px 5px 10px #888}.CCMS-editor-savebut{right:110px}.CCMS-editor-textarea{width:99%;overflow-y:auto;font-family:inherit;font-style:inherit;font-variant:inherit;font-weight:inherit;font-stretch:inherit;font-size:inherit;line-height:inherit;resize:both}.hidden{display:none}#CCMS-loadingSpinner{display:none;position:absolute;background:#fff}#CCMS-loadingSpinner-load{position:absolute}#CCMSEdit-edit-mode-lng{width:100%;height:34px;padding:6px 12px;margin-bottom:10px;font-size:14px;line-height:1.42857143;color:#555;background-color:#fff;background-image:none;border:1px solid #ccc;border-radius:4px;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);box-shadow:inset 0 1px 1px rgba(0,0,0,.075);-webkit-transition:border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;-o-transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s}.ccms_msg{border-radius:4px;box-shadow:rgba(0,0,0,0.2)0px 4px 10px 0px,rgba(0,0,0,0.19)0px 4px 20px 0px;color:rgb(255,255,255);display:block;left:0;padding:20px;position:fixed;margin:0 auto;right:0;top:-1000px;transition:all 1s;width:90%;z-index:1000}.ccms_msg.active{top:100px}.ccms_msg.active.error{background-color:rgb(255,0,0)}.ccms_msg.active.success{background-color:rgb(134,177,53)}@media only screen and (min-width:435px){.ccms_msg{max-width:400px}.ccms_msg.active.error{max-width:80%}.ccms_msg.active.success{max-width:400px}}@media only screen and (min-width:600px){.ccms_msg{max-width:400px}.ccms_msg.active.error{max-width:70%}.ccms_msg.active.success{max-width:400px}}@media only screen and (min-width:950px){.ccms_msg{max-width:400px}.ccms_msg.active.error{max-width:60%}.ccms_msg.active.success{max-width:400px}}@media only screen and (min-width:1400px){.ccms_msg{max-width:400px}.ccms_msg.active.error{max-width:50%}.ccms_msg.active.success{max-width:400px}}
</style>
<div id="ccms_msg" role="alert" class="ccms_msg"></div>
<input type="checkbox" id="CCMSTab-slide-tab-checkbox">
<div id="CCMSTab-slide">
	<label id="CCMSTab-slide-tab" for="CCMSTab-slide-tab-checkbox" title="User Admin Slider">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="#fff" d="M21,12a1,1,0,0,0-1,1v6a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V5A1,1,0,0,1,5,4h6a1,1,0,0,0,0-2H5A3,3,0,0,0,2,5V19a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V13A1,1,0,0,0,21,12ZM6,12.76V17a1,1,0,0,0,1,1h4.24a1,1,0,0,0,.71-.29l6.92-6.93h0L21.71,8a1,1,0,0,0,0-1.42L17.47,2.29a1,1,0,0,0-1.42,0L13.23,5.12h0L6.29,12.05A1,1,0,0,0,6,12.76ZM16.76,4.41l2.83,2.83L18.17,8.66,15.34,5.83ZM8,13.17l5.93-5.93,2.83,2.83L10.83,16H8Z"/></svg>
	</label>
	<div id="CCMSTab-slide-outer">
		<div id="CCMSTab-slide-inner">
			<div class="CCMSTab-slide-header">User Admin Slider</div>
			<div id="CCMSEdit-edit-mode-switch">
				<span>Edit Mode Off/On: </span>
				<label id="CCMSEdit-edit-mode-switch-label">
					<input id="CCMSEdit-edit-mode-switch-check" type="checkbox">
					<span class="slider round"></span>
				</label>
			</div>
			<ul id="CCMSlng-list">
		<?php
		$tpl = htmlspecialchars(preg_replace('/^\/([\pL\pN-]*)\/?(.*)\z/i', '${2}', $_SERVER['REQUEST_URI']));
		$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_lng_charset` ORDER BY lngDesc ASC;");
		if($qry->execute()) {
			while($row = $qry->fetch()) {
				if($json_a["content_manager"]["rw"] === 1 && $json_a["content_manager"]["sub"][$row["lng"]] !== 0) {
					// So long as you do have 'content_manager' read privileges (1), and the language your trying to access is not flagged on your account as 'No Access' (0) then it should show up in the list.
					if($row["ptrLng"]) {
						echo '<li id="ccms_lng-' . $row["lng"] . '"><a href="/' . $row["ptrLng"] . '/' . $tpl . '" title="Points to lng code: ' . $row["ptrLng"] . '">' . $row["lngDesc"] . '</a></li>';
					} else {
						echo '<li id="ccms_lng-' . $row["lng"] . '"';

						if($row["lng"] === $CLEAN["ccms_lng"]){
							echo ' style="text-decoration:underline dashed"';
						}

						echo '><a href="/' . $row["lng"] . '/' . $tpl . '" title="lng code: ' . $row["lng"] . '">' . $row["lngDesc"] . '</a></li>';
					}
				}
			}
		}
		?>
			</ul>
			<div>
				<a href="/<?php echo $CLEAN["ccms_lng"]; ?>/user/" style="float:left" title="Dashboard">
					<svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 24 24"><path fill="#fff" d="M10,13H4a1,1,0,0,0-1,1v6a1,1,0,0,0,1,1h6a1,1,0,0,0,1-1V14A1,1,0,0,0,10,13ZM9,19H5V15H9ZM20,3H14a1,1,0,0,0-1,1v6a1,1,0,0,0,1,1h6a1,1,0,0,0,1-1V4A1,1,0,0,0,20,3ZM19,9H15V5h4Zm1,7H18V14a1,1,0,0,0-2,0v2H14a1,1,0,0,0,0,2h2v2a1,1,0,0,0,2,0V18h2a1,1,0,0,0,0-2ZM10,3H4A1,1,0,0,0,3,4v6a1,1,0,0,0,1,1h6a1,1,0,0,0,1-1V4A1,1,0,0,0,10,3ZM9,9H5V5H9Z"/></svg>
					<!-- a href="https://iconscout.com/">Unicons by Iconscout</a -->
				</a>
				<a href="/<?php echo $CLEAN["ccms_lng"]; ?>/user/?ccms_logout=1" title="<?php echo $CLEAN["CCMS_DB_Preload_Content"]["all"]["login3"][$CLEAN["ccms_lng"]]["content"]; ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="#fff" d="M4,12a1,1,0,0,0,1,1h7.59l-2.3,2.29a1,1,0,0,0,0,1.42,1,1,0,0,0,1.42,0l4-4a1,1,0,0,0,.21-.33,1,1,0,0,0,0-.76,1,1,0,0,0-.21-.33l-4-4a1,1,0,1,0-1.42,1.42L12.59,11H5A1,1,0,0,0,4,12ZM17,2H7A3,3,0,0,0,4,5V8A1,1,0,0,0,6,8V5A1,1,0,0,1,7,4H17a1,1,0,0,1,1,1V19a1,1,0,0,1-1,1H7a1,1,0,0,1-1-1V16a1,1,0,0,0-2,0v3a3,3,0,0,0,3,3H17a3,3,0,0,0,3-3V5A3,3,0,0,0,17,2Z"/></svg>
					<!-- a href="https://iconscout.com/">Unicons by Iconscout</a -->
				</a>
			</div>
		</div>
	</div>
</div>
<div id="CCMS-loadingSpinner">
  <i class="fa fa-spinner fa-spin fa-5x" id="CCMS-loadingSpinner-load" aria-hidden="true"></i>
</div>

<script nonce="<?=$CFG["nonce"];?>">
	function ccms_tab_switch() {
		if($("#CCMSTab-slide-tab-checkbox").is(":checked")) {
			// Tab Open
			localStorage.setItem("CCMSTab-slide-tab-checkbox", true);
		} else {
			// Tab Closed
			localStorage.setItem("CCMSTab-slide-tab-checkbox", false);
		}
	}

	function ccms_edit_mode_switch() {
		if($("#CCMSEdit-edit-mode-switch-check").is(":checked")) {
			// Edit Switch On
			localStorage.setItem("CCMSEdit-edit-mode-switch-check", true);

			$.fn.setCursorPosition = function(pos) {
				this.each(function(index, elem) {
					if(elem.setSelectionRange) {
						elem.setSelectionRange(pos, pos);
					} else if(elem.createTextRange) {
						var range = elem.createTextRange();
						range.collapse(true);
						range.moveEnd('character', pos);
						range.moveStart('character', pos);
						range.select();
					}
				});
				return this;
			};

			NodeList.prototype.forEach = Array.prototype.forEach;

			var divs = document.querySelectorAll('[data-ccms]').forEach(function(el) {
				var textOrig = [], textNew = null, editor = null;
				// We add a new class to the node containing the data-ccms attribute.  This generates the box.
				el.className += " CCMSEdit-edit-link-border";
				$(el).wrap('<div class="CCMS-wrap">');

				//Find out if it's read and writable in privileges
				var rwPriv = el.getAttribute("data-ccms-rw");
				if(rwPriv == "2") {
					// read and writable
					rwPriv = '<button class="CCMS-editor-but CCMS-editor-editbut">Edit</button>';
				} else {
					// read only
					rwPriv = '<button class="CCMS-editor-but CCMS-editor-editbut" disabled style="text-decoration:line-through" title="Read Only">Edit</button>';
				}

				var editbtn = $(rwPriv);
				var savebtn = $('<button class="CCMS-editor-but CCMS-editor-savebut hidden">Save</button>');
				var cancelbtn = $('<button class="CCMS-editor-but hidden">Cancel</button>');
				$(editbtn).prependTo($(el).parent());
				$(savebtn).prependTo($(el).parent());
				$(cancelbtn).prependTo($(el).parent());


				$(editbtn).click(function() {
					$(editbtn).addClass("hidden");
					$(savebtn).removeClass("hidden");
					$(cancelbtn).removeClass("hidden");
					textOrig[0] = el.getAttribute("data-ccms");
					textOrig[2] = el.getAttribute("data-ccms-grp");
					textOrig[3] = el.getAttribute("data-ccms-name");
					$.ajax({
						url: "/<?= $CLEAN["ccms_lng"]; ?>/user/_js/ccms-user-admin-slider-01-ajax.php?ajax_flag=1",
						cache: false,
						type: "post",
						data: "ccms_ins_db_id=" + textOrig[0]
					}).done(function(msg) {
						const ccms_msg_div = document.getElementById('ccms_msg');
						var obj = JSON.parse(msg);
						if(obj.success) {
							textOrig[1] = $.trim($(el).html());
							$(el).html("");
							editor=$('<textarea class="CCMS-editor-textarea" rows="5">'+obj.success+'</textarea><div style="position:relative;color:#000;font:16px/1.2 normal;text-align:left;text-transform:none;"><span><strong>Warning</strong>: Only &lt;a&gt;, &lt;blockquote&gt;, &lt;br&gt;, &lt;i&gt;, &lt;img&gt;, &lt;p&gt;, &lt;pre&gt;, &lt;span&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt; and CCMS tags like <span style="word-break:break-all;">&#123;CCMS_LIB:_default.php;FUNC:ccms_lng}</span> or &#123;CCMS_DB:index,para1} are permitted here.  All else is automatically removed at the server.<br>Shift+[Enter] for Break</span><span style="position:absolute;bottom:0;right:0;">( ID:'+textOrig[0]+', GRP:'+textOrig[2]+', NAME:'+textOrig[3]+')</span></div>').appendTo($(el));
							$(el).find('textarea').keyup(function (e) {
								if(e.keyCode == 13 && e.shiftKey) {
									var content = this.value;
									var caret = getCaret(this);
									this.value = content.substring(0, caret - 1) + "<br>\n" + content.substring(caret, content.length);
									$(el).find('textarea').setCursorPosition(caret + 6);
									e.preventDefault();
								}
							});
						} else {
							$(editbtn).removeClass("hidden");
							$(savebtn).addClass("hidden");
							$(cancelbtn).addClass("hidden");
							const ccms_msg_div = document.getElementById('ccms_msg');
							ccms_msg_div.textContent = obj.error;
							ccms_msg_div.classList.add("active", "error");
							setTimeout(function() {
								ccms_msg_div.classList.remove("active", "error");
							},15000);
							window.onclick = function(event) {
								if(event.target != ccms_msg_div) {
									ccms_msg_div.classList.remove("active", "error");
								}
							}
						}
					}).fail(function (jqXHR, textStatus, errorThrown){
						// Called on failure.
						// log the error to the console
						console.error( "The following error occured: " + textStatus, errorThrown );
						alert("The following error occured: " + textStatus, errorThrown);
					}).always(function () {
						// Loading Spinner Stop
						$("#CCMS-loadingSpinner").fadeOut();
					});
				});


				$(savebtn).click(function() {
					if(textOrig[1] != $(el).find('textarea').val()) {
						textNew = $(el).find('textarea').val();
						textNew = textNew.replace("{ CCMS", "{CCMS");
						// Loading Spinner Start
						$t = $(el);
						$("#CCMS-loadingSpinner").css({
							opacity : 0.5,
							top     : $t.offset().top,
							width   : $t.outerWidth(),
							height  : $t.outerHeight()
						});
						$("#CCMS-loadingSpinner-load").css({
							top  : ($t.height() / 2),
							left : ($t.width() / 2)
						});
						$("#CCMS-loadingSpinner").fadeIn();
						$.ajax({
							url: "/<?= $CLEAN["ccms_lng"];?>/user/_js/ccms-user-admin-slider-02-ajax.php?ajax_flag=1",
							cache: false,
							type: "post",
							data: "ccms_ins_db_id=" + textOrig[0] + "&ccms_ins_db_text=" + encodeURIComponent(textNew)
						}).done(function(msg) {
							const ccms_msg_div = document.getElementById('ccms_msg');
							var obj = JSON.parse(msg);
							if(obj.success) {
								$(editbtn).removeClass("hidden");
								$(savebtn).addClass("hidden");
								$(cancelbtn).addClass("hidden");
								$(el).html(textNew);
								try {
									document.execCommand("styleWithCSS", 0, false);
								} catch (e) {
									try {
										document.execCommand("useCSS", 0, true);
									} catch (e) {
										try {
											document.execCommand('styleWithCSS', false, false);
										} catch (e) {}
									}
								}
								ccms_msg_div.classList.add("active", "success");
								ccms_msg_div.textContent = obj.success;
								setTimeout(function() {
									ccms_msg_div.classList.remove("active", "success");
								},5000);
								window.onclick = function(event) {
									if(event.target != ccms_msg_div) {
										ccms_msg_div.classList.remove("active", "success");
									}
								}
							} else {
								ccms_msg_div.textContent = obj.error;
								ccms_msg_div.classList.add("active", "error");
								setTimeout(function() {
									ccms_msg_div.classList.remove("active", "error");
								},15000);
								window.onclick = function(event) {
									if(event.target != ccms_msg_div) {
										ccms_msg_div.classList.remove("active", "error");
									}
								}
							}
						}).fail(function (jqXHR, textStatus, errorThrown){
							// Called on failure.
							// log the error to the console
							console.error( "The following error occured: " + textStatus, errorThrown );
							alert("The following error occured: " + textStatus, errorThrown);
						}).always(function () {
							// Loading Spinner Stop
							$("#CCMS-loadingSpinner").fadeOut();
						});
					} else {
						// There was no change to the content so simply close the textarea.
						$(editbtn).removeClass("hidden");
						$(savebtn).addClass("hidden");
						$(cancelbtn).addClass("hidden");
						$(el).html(textOrig[1]);
						try {
							document.execCommand("styleWithCSS", 0, false);
						} catch (e) {
							try {
								document.execCommand("useCSS", 0, true);
							} catch (e) {
								try {
									document.execCommand('styleWithCSS', false, false);
								} catch (e) {}
							}
						}
					}
				});


				$(cancelbtn).click(function(){
					$(editbtn).removeClass("hidden");
					$(savebtn).addClass("hidden");
					$(cancelbtn).addClass("hidden");
					try {
						document.execCommand("styleWithCSS", 0, false);
					} catch (e) {
						try {
							document.execCommand("useCSS", 0, true);
						} catch (e) {
							try {
								document.execCommand('styleWithCSS', false, false);
							} catch (e) {}
						}
					}
					$(el).html(textOrig[1]);
				});


			})
		} else {
			// Edit Switch Off
			const list = document.querySelectorAll('[data-ccms]');
			const ccms_msg_div = document.getElementById('ccms_msg');
			const ccms_edit_mode_switch = document.getElementById('CCMSEdit-edit-mode-switch-check');//var

			for(const value in list) {
				if(list.hasOwnProperty(value)) {
					if($(list).find('textarea').length) {
						ccms_msg_div.textContent = "Edit Mode can not be disabled while edit windows are still open.  Please save or cancel open edits before turning this feature off.";
						ccms_msg_div.classList.add("active", "error");
						$('#CCMSEdit-edit-mode-switch-check').prop('checked', true);
						localStorage.setItem("CCMSEdit-edit-mode-switch-check", true);
						window.addEventListener('click', function abc(event) {
							if(event.target != ccms_msg_div && event.target != ccms_edit_mode_switch) {
								ccms_msg_div.classList.remove("active", "error");
								window.removeEventListener('click', abc);
							}
						});
						setTimeout(function() {
							ccms_msg_div.classList.remove("active", "error");
						},15000);
						break;
					} else {
						console.log("not found");
						$(".CCMS-wrap button").remove();
						$(list[value]).unwrap();
						list[value].className = list[value].className.replace(/\bCCMSEdit-edit-link-border\b/, "");
					}
				}
			}
			localStorage.setItem("CCMSEdit-edit-mode-switch-check", false);
		}
	}

	function getCaret(el) {
		if(el.selectionStart) {
			return el.selectionStart;
		} else if(document.selection) {
			el.focus();
			var r = document.selection.createRange();
			if(r == null) {return 0;}
			var re = el.createTextRange(),
			rc = re.duplicate();
			re.moveToBookmark(r.getBookmark());
			rc.setEndPoint('EndToStart', re);
			return rc.text.length;
		}
		return 0;
	}

	function compVer(a_components, b_components) {
		// Return 1  if a > b
		// Return -1 if a < b
		// Return 0  if a == b
		if(a_components === b_components) {return 0;}
		var partsNumberA = a_components.split(".");
		var partsNumberB = b_components.split(".");
		for(var i = 0; i < partsNumberA.length; i++) {
			var valueA = parseInt(partsNumberA[i]);
			var valueB = parseInt(partsNumberB[i]);
			// A bigger than B
			if(valueA > valueB || isNaN(valueB)) {return 1;}
			// B bigger than A
			if(valueA < valueB) {return -1;}
		}
	}

	function ccms_edit_mode_switch_main() {
		if(localStorage.getItem("CCMSTab-slide-tab-checkbox") == "true") {
			$('#CCMSTab-slide-tab-checkbox').prop('checked', true);
		}
		if(localStorage.getItem("CCMSEdit-edit-mode-switch-check") == "true") {
			$('#CCMSEdit-edit-mode-switch-check').prop('checked', true);
			ccms_edit_mode_switch();
		}
	}

	function ccms_load_jquery() {
		if(typeof jQuery == 'undefined') {
			// jQuery is not loaded

console.log("jQuery not found, trying to load now.")

			var jq = document.createElement('script');
			jq.type = 'text/javascript';
			jq.src = '/ccmsusr/_js/jquery-3.6.0.min.js';
			//jq.integrity = 'sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=';
			jq.nonce = '<?=$CFG["nonce"];?>';
			//jq.crossOrigin = 'anonymous';
			document.getElementsByTagName('head')[0].appendChild(jq);
		} else if(-1 == compVer(jQuery.fn.jquery, "2.2.4")) {
			// jQuery is loaded but the version is too low, kill the process

console.log("jQuery is already loaded but not a high enough version.")

			localStorage.setItem("CCMSTab-slide-tab-checkbox", false);
			localStorage.setItem("CCMSEdit-edit-mode-switch-check", false);
			document.getElementById("CCMSTab-slide").innerHTML = "";

			const ccms_msg_div = document.getElementById('ccms_msg');
			ccms_msg_div.textContent = "jQuery is already loaded but not a high enough version. The User Admin Slider requires jQuery v2.2.4 or higher to run properly.";
			ccms_msg_div.classList.add("active", "error");
			setTimeout(function() {
				ccms_msg_div.classList.remove("active", "error");
			},15000);
			window.onclick = function(event) {
				if(event.target != ccms_msg_div) {
					ccms_msg_div.classList.remove("active", "error");
				}
			}
			return false;
		}
		document.getElementById("ccms_lng-<?= $CLEAN["ccms_lng"]; ?>").scrollIntoView();
		document.getElementById("ccms_lng-<?= $CLEAN["ccms_lng"]; ?>").children[0].style.textDecoration = "underline";
		setTimeout(function() {ccms_admin_slider_token();}, 1000);
		setTimeout(function() {ccms_edit_mode_switch_main();}, 1000);

	}

	function ccms_admin_slider_token() {
		// Needed because of page caching.  If you login as a user and need to edit templates using the User Admin Slider you will have trouble finding your controls if you don't refresh your page because the browser is set to cache the pages in visitors browsers to help imporve it's speed.
		var parser = document.createElement('a'),
			searchObject = {},
			queries,
			split,
			i,
			ccms_token = "ccms_token=<?php echo md5(time()); ?>";
		$("a").each(function() {
			parser.href = this.href;
			// Convert query string to object
			queries = parser.search.replace(/^\?/, '').split('&');
			for( i = 0; i < queries.length; i++ ) {
				split = queries[i].split('=');
				searchObject[split[0]] = split[1];
			}
			if(parser.hostname == "<?php global $CFG; echo $CFG["DOMAIN"]; ?>") {
				// First we test to make sure the href belongs to our site and is not a link to another site.  If so then we clean out any previous instances of the ccms_token variable to prevent accumulation.
				parser.search = parser.search.replace(/([&\?]ccms_token=[a-z0-9]*$|ccms_token=[a-z0-9]*&|[?&]ccms_token=[a-z0-9]*(?=#))/ig, '');
				// Now we rebuild the link to include a fresh ccms_token to help makesure we don't pull a cached version from the browser when we get there.
				this.href = parser.protocol+'//'+parser.hostname+(parser.port ? ':'+parser.port : '')+parser.pathname+(parser.search ? parser.search+'&'+ccms_token : '?'+ccms_token)+parser.hash
			}
		});
	}

	document.getElementById("CCMSEdit-edit-mode-switch-check").addEventListener("click", ccms_edit_mode_switch);

	if(window.addEventListener)
		window.addEventListener("load", ccms_load_jquery, false);
	else if(window.attachEvent)
		window.attachEvent("onload", ccms_load_jquery);
	else window.onload = ccms_load_jquery;
</script>
	<?php endif;
}


function ccms_dateYear() {
	echo date("Y");
}


function ccms_lng() {
	global $CLEAN;
	echo $CLEAN["ccms_lng"];
}


function ccms_lng_ret() {
	/* Used to return a value without submitting it to a template buffer prematurely. */
	global $CLEAN;
	return $CLEAN["ccms_lng"];
}


function ccms_lng_dir() {
	global $CFG;
	echo $CFG["CCMS_LNG_DIR"];
}


function ccms_lng_dir_ret() {
	/* Used to return a value without submitting it to a template buffer prematurely. */
	global $CFG;
	return $CFG["CCMS_LNG_DIR"];
}


function ccms_token() {
	echo md5(time());
}


function ccms_printrClean() {
	global $CLEAN;
	echo "<br>\$CLEAN=[<pre>";
	print_r($CLEAN);
	echo "</pre>]\n";
}


function ccms_version() {
	global $CFG;
	echo $CFG["VERSION"];
}


function ccms_release_date() {
	global $CFG;
	echo $CFG["RELEASE_DATE"];
}


function ccms_tpl() {
	global $CLEAN;
	echo $CLEAN["ccms_tpl"];
}


function _phpinfo() {
	return phpinfo();
}


function ccms_badIPCheck($ip) {
	global $CFG;

	$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_blacklist` WHERE `id` = 1;");
	$qry->execute();
	$row = $qry->fetch(PDO::FETCH_ASSOC);

	if(isset($row["data"])) {
		if(strstr($row["data"], $ip)) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}


function bad_word_check($sentence) {
	global $CFG;
	$qry = $CFG["DBH"]->prepare("SELECT * FROM ccms_blacklist;");
	$qry->execute();
	$qry->setFetchMode(PDO::FETCH_ASSOC);
	while($row = $qry->fetch()) {
		if($row["id"] == 1) {
			$badIPAddressData = $row["data"];
		} elseif($row["id"] == 2) {
			$badWordData = $row["data"];
		}
	}
	$found = 0;
	$pos = false;
	$word_array = explode("|", $badWordData);
	foreach($word_array as $the_word) {
		$pos = @strpos(strtoupper($sentence), strtoupper($the_word));
		if($pos !== false) {
			$found = 1;
			break;
		}
	}
	if($found == 1) {
		return false;
	} else {
		return true;
	}
}
