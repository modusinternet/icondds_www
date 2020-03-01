<?php
header("Content-Type: text/html; charset=UTF-8");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if($_SERVER["SCRIPT_NAME"] != "/ccmsusr/index.php") {
	echo "This script can NOT be called directly.";
	die();
}

$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_user` WHERE `id` = :id LIMIT 1;");
$qry->execute(array(':id' => $CLEAN["SESSION"]["user_id"]));
$ccms_user = $qry->fetch(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html id="no-fouc" lang="en" style="opacity: 0;">
	<head>
		<meta charset="utf-8">
		<title>User Profile</title>
		<meta name="description" content="" />
		{CCMS_TPL:header-head.html}
		<script>
			var navActiveArray = ["user_profile"];
		</script>
	</head>
	<body>
		<div id="wrapper">
			{CCMS_TPL:header-body.php}
			<div id="page-wrapper">
				<div class="row">
					<div class="col-md-12">
						<h1 class="page-header">User Profile</h1>
						<div class="img-circle" style="background-color: #337AB7; height: 100px; width: 100px;">
							<i class="fa fa-user" style="color: #ffffff; font-size: 6em;"></i>
						</div>
						<h2><?php echo $ccms_user["firstname"] . " " . $ccms_user["lastname"] . " (" . $ccms_user["alias"] . ")"; ?></h2>
						<?php echo $ccms_user["position"]; ?><br />
						<br />
						<ul class="nav nav-tabs">
							<li class="active">
								<a href="#info_tab" data-toggle="tab" aria-expanded="false">Info</a>
							</li>
							<li>
								<a href="#password_tab" data-toggle="tab">Password</a>
							</li>
							<li>
								<a href="#privilege_tab" data-toggle="tab" aria-expanded="true">Privileges</a>
							</li>
							<li>
								<a href="#session_tab" data-toggle="tab">Sessions</a>
							</li>
						</ul>
					</div>
				</div>

				<!-- Tab panes -->
				<div class="tab-content" style="margin: 10px;">
					<div class="tab-pane fade active in" id="info_tab">
						<form class="form-horizontal" id="info_tab_form" role="form">
							<input name="ajax_flag" type="hidden" value="1">
							<div class="row">
								<div class="col-md-4">
									<h3>General</h3>
									<div id="info_tab_form_success" class="alert alert-success" role="alert" style="display: none;"></div>
									<div id="info_tab_form_fail" class="alert alert-danger" role="alert" style="display: none;"></div>
									<div class="form-group">
										<label for="firstname" class="control-label">Firstname</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-user"></span></div>
											<input class="form-control" id="firstname" name="firstname" placeholder="Type your Firstname here." type="text" value="<?php echo $ccms_user["firstname"]; ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="lastname" class="control-label">Lastname</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-user"></span></div>
											<input class="form-control" id="lastname" name="lastname" placeholder="Type your Lastname here." type="text" value="<?php echo $ccms_user["lastname"]; ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="alias" class="control-label">Alias *</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-user"></span></div>
											<input class="form-control" id="alias" name="alias" placeholder="Type your Alias here." type="text" value="<?php echo $ccms_user["alias"]; ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="position" class="control-label">Position</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-cog"></span></div>
											<input class="form-control" id="position" name="position" placeholder="Type your work Position or Title here." type="text" value="<?php echo $ccms_user["position"]; ?>">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<h3>Address</h3>
									<div class="form-group">
										<label for="address1" class="control-label">Address Line 1</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-map-marker"></span></div>
											<input class="form-control" id="address1" name="address1" placeholder="Type your Address here." type="text" value="<?php echo $ccms_user["address1"]; ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="address2" class="control-label">Address Line 2</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-map-marker"></span></div>
											<input class="form-control" id="address2" name="address2" placeholder="Type your Address here." type="text" value="<?php echo $ccms_user["address2"]; ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="prov_state" class="control-label">Prov/State</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-map-marker"></span></div>
											<input class="form-control" id="prov_state" name="prov_state" placeholder="Type your Province or State here." type="text" value="<?php echo $ccms_user["prov_state"]; ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="country" class="control-label">Country</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-map-marker"></span></div>
											<input class="form-control" id="country" name="country" placeholder="Type your Country Name here." type="text" value="<?php echo $ccms_user["country"]; ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="post_zip" class="control-label">Post/Zip Code</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-map-marker"></span></div>
											<input class="form-control" id="post_zip" name="post_zip" placeholder="Type your Postal or Zip Code here." type="text" value="<?php echo $ccms_user["post_zip"]; ?>">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<h3>Contact</h3>
									<div class="form-group">
										<label for="email" class="control-label">Email *</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-envelope"></span></div>
											<input class="form-control" id="email" name="email" placeholder="Type your Email Address here." type="text" value="<?php echo $ccms_user["email"]; ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="phone1" class="control-label">Phone #1</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-phone"></span></div>
											<input class="form-control" id="phone1" name="phone1" placeholder="Type your main Phone Number here." type="text" value="<?php echo $ccms_user["phone1"]; ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="phone2" class="control-label">Phone #2</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-phone"></span></div>
											<input class="form-control" id="phone2" name="phone2" placeholder="Type your secondary Phone Number here." type="text" value="<?php echo $ccms_user["phone2"]; ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="facebook" class="control-label">Facebook</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-facebook-official"></span></div>
											<input class="form-control" id="facebook" name="facebook" placeholder="Type your Facebook URI here." type="text" value="<?php echo $ccms_user["facebook"]; ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="skype" class="control-label">Skype</label>
										<div class="input-group">
											<div class="input-group-addon"><span class="fa fa-skype"></span></div>
											<input class="form-control" id="skype" name="skype" placeholder="Type your Skype Account Name here." type="text" value="<?php echo $ccms_user["skype"]; ?>">
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<h3>Other</h3>

									<div class="form-group">
										<label for="note" class="control-label">Notes</label>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-file-text-o"></i></div>
											<textarea name="note" id="note" cols="30" rows="4" class="form-control" placeholder="Type any other notes you wish to attach to your account here."><?php echo $ccms_user["note"]; ?></textarea>
										</div>
									</div>
								</div>
							</div>
							<hr>
							<div class="row">
								<div class="col-md-12">
									<button class="btn-primary btn">Update</button>
									<button class="btn-default btn">Cancel</button>
								</div>
							</div>
						</form>
					</div>
					<div class="tab-pane fade" id="password_tab">
						<form class="form-horizontal" id="password_tab_form" role="form">
							<input name="ajax_flag" type="hidden" value="1">
							<div class="row">
								<div class="col-md-12">
									<h3>Manage Your Password Here</h3>
									<div id="password_tab_form_success" class="alert alert-success" role="alert" style="display: none;"></div>
									<div id="password_tab_form_fail" class="alert alert-danger" role="alert" style="display: none;"></div>
									<div class="form-group">
										<label for="password" class="control-label">Password *</label>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-key"></i></div>
											<input class="form-control" id="password" name="password" placeholder="Type your current password here." type="password" value="" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
										</div>
									</div>
									<div class="form-group">
										<label for="password1" class="control-label">New Password *</label>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-key"></i></div>
											<input class="form-control" id="password1" name="password1" placeholder="Type your new password here." type="password" value="" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
										</div>
									</div>
									<div class="form-group">
										<label for="password2" class="control-label">Repeat New Password *</label>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-key"></i></div>
											<input class="form-control" id="password2" name="password2" placeholder="Type your new password here again." type="password" value="" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
										</div>
									</div>
								</div>
							</div>
							<hr>
							<div class="row">
								<div class="col-md-12">
									<button class="btn-primary btn">Update</button>
									<button class="btn-default btn">Cancel</button>
								</div>
							</div>
						</form>
					</div>
					<div class="tab-pane fade" id="privilege_tab">
						<div class="row">
							<div class="col-md-12">
								<h3>Your User Privileges</h3>
								The string below is an exact copy of what the server reads in order to determine your personal read/write privileges for functions found throughout this site.  The data below that is the same content structured to help make it easier to read.  These setting <span style="text-decoration: underline;">can not</span> be modified here.  Changes can only be made by users with read/write access to the '<span class="oj">Admin / User Privileges</span>' area.<br />
								<div class="alert alert-success" style="word-wrap: break-word;">
<?php
	$json = json_decode($ccms_user["priv"]);
	echo json_encode($json, JSON_UNESCAPED_SLASHES);
?>
									<pre><?=json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);?></pre>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="session_tab">
						<div class="row">
							<div class="col-md-12" style="padding-bottom: 20px;">
								<form class="form-horizontal" role="form" action="/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/login.html">
									<input type="hidden" name="logout" value="1" />
									<h3>Your Session</h3>
									Below is your current session information.<br />
									<div class="alert alert-success">
										<strong>Token:</strong><br />
										<span style="word-wrap: break-word;"><?php echo $CLEAN["SESSION"]["code"]; ?></span><br />
										<strong>Created:</strong><br />
										<?php echo date('Y-m-d H:i:s', $CLEAN["SESSION"]["first"]); ?><br />
										<strong>Last Updated:</strong><br />
										<?php echo date('Y-m-d H:i:s', $CLEAN["SESSION"]["last"]); ?><br />
										<strong>Exprires:</strong><br />
										<?php echo date('Y-m-d H:i:s', $CLEAN["SESSION"]["exp"]); ?><br />
										<strong>User Agent (Browser):</strong><br />
										<span style="word-wrap: break-word;"><?php echo $CLEAN["SESSION"]["user_agent"]; ?></span><br />
										<strong>IP Address:</strong><br />
										<?php echo $CLEAN["SESSION"]["ip"] . "\n"; ?>
									</div>
									<button class="btn-primary btn">Logout</button>
								</form>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								Other active sessions found on the server related to your account:
							</div>
						</div>
<?php
$qry = $CFG["DBH"]->prepare("SELECT * FROM `ccms_session` WHERE `user_id` = :user_id;");
$qry->execute(array(':user_id' => $CLEAN["SESSION"]["user_id"]));
if ($qry->rowCount() > 1) :
?>
						<div id="session_tab_logout_all_div">
							<div class="row">
<?php while ($row = $qry->fetch(PDO::FETCH_ASSOC)) : ?>
<?php if ($row["code"] != $CLEAN["SESSION"]["code"]) : ?>
								<div class="col-md-4 cust-grid-01">
									<div class="alert alert-warning">
										<strong>Token:</strong><br />
										<span style="word-wrap: break-word;"><?php echo $row["code"]; ?></span><br />
										<strong>Created:</strong><br />
										<?php echo date('Y-m-d H:i:s', $row["first"]); ?><br />
										<strong>Last Updated:</strong><br />
										<?php echo date('Y-m-d H:i:s', $row["last"]); ?><br />
										<strong>Exprires:</strong><br />
										<?php echo date('Y-m-d H:i:s', $row["exp"]); ?><br />
										<strong>User Agent (Browser):</strong><br />
										<span style="word-wrap: break-word;"><?php echo $row["user_agent"]; ?></span><br />
										<strong>IP Address:</strong><br />
										<?php echo $row["ip"] . "\n"; ?>
									</div>
								</div>
<?php endif; ?>
<?php endwhile; ?>
							</div>
						</div>
							<div class="row">
								<div class="col-md-12" style="padding-bottom: 20px;">
									The sessions found above may or may not be expired, regardless they are sessions that were started and validated against your credentials using an alternate browsers or devices which where not manually logged out.  If you would like to remove them all now simply click the 'Logout All' button below.  Otherwise they will be automatically removed by the system on the next valid login.  (This will not include the session you are currently using here now.)
									<form class="form-horizontal" id="session_tab_logout_all_form" role="form">
										<input name="ajax_flag" type="hidden" value="1">
										<div id="session_tab_logout_all_success" class="alert alert-success" role="alert" style="display: none;"></div>
										<div id="session_tab_logout_all_fail" class="alert alert-danger" role="alert" style="display: none;"></div>
										<button class="btn-primary btn">Logout All</button>
									</form>
								</div>
							</div>
						</div>
<?php else : ?>
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-warning">
									None found.
								</div>
							</div>
						</div>
<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<script>
			function loadFirst(e,t){var a=document.createElement("script");a.async = true;a.readyState?a.onreadystatechange=function(){("loaded"==a.readyState||"complete"==a.readyState)&&(a.onreadystatechange=null,t())}:a.onload=function(){t()},a.src=e,document.body.appendChild(a)}

			var cb = function() {
				var l = document.createElement('link'); l.rel = 'stylesheet';
				l.href = "/ccmsusr/_css/bootstrap-3.3.7.min.css";
				var h = document.getElementsByTagName('head')[0]; h.parentNode.insertBefore(l, h);

				var l = document.createElement('link'); l.rel = 'stylesheet';
				l.href = "/ccmsusr/_css/metisMenu-2.4.0.min.css";
				var h = document.getElementsByTagName('head')[0]; h.parentNode.insertBefore(l, h);

				var l = document.createElement('link'); l.rel = 'stylesheet';
				l.href = "/ccmsusr/_css/custodiancms.css";
				/*l.href = "/ccmsusr/_css/custodiancms.min.css";*/
				var h = document.getElementsByTagName('head')[0]; h.parentNode.insertBefore(l, h);

				var l = document.createElement('link'); l.rel = 'stylesheet';
				l.href = "/ccmsusr/_css/font-awesome-4.7.0.min.css";
				var h = document.getElementsByTagName('head')[0]; h.parentNode.insertBefore(l, h);
			};

			var raf = requestAnimationFrame || mozRequestAnimationFrame || webkitRequestAnimationFrame || msRequestAnimationFrame;
			if (raf) raf(cb);
			else window.addEventListener('load', cb);

			function loadJSResources() {
				loadFirst("/ccmsusr/_js/jquery-2.2.0.min.js", function() { /* JQuery is loaded */
					loadFirst("/ccmsusr/_js/bootstrap-3.3.7.min.js", function() { /* Bootstrap is loaded */
						loadFirst("/ccmsusr/_js/metisMenu-2.4.0.min.js", function() { /* MetisMenu JavaScript */
							/*loadFirst("/ccmsusr/_js/custodiancms.js", function() { /* CustodianCMS JavaScript */
							loadFirst("/ccmsusr/_js/custodiancms.min.js", function() { /* CustodianCMS JavaScript */

								navActiveArray.forEach(function(s) {$("#"+s).addClass("active");});

								// Load MetisMenu
								$('#side-menu').metisMenu();

								// Fade in web page.
								$("#no-fouc").delay(200).animate({"opacity": "1"}, 500);

								$("#menu-toggle").click(function(e) {
									e.preventDefault();
									$("#wrapper").toggleClass("toggled");
									$("#wrapper.toggled").find("#sidebar-wrapper").find(".collapse").collapse("hide");
									$("#sidebar-wrapper").toggle();
								});

								$(function(){$(window).bind("load resize",function(){showHideNav();})});

								/*loadFirst("//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js", function() { /* jquery.validate.js */
								loadFirst("/ccmsusr/_js/jquery.validate-1.17.0.min.js", function() { /* JQuery Validate */
									/*loadFirst("//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/additional-methods.min.js", function(){ /* additional-methods.js */
									loadFirst("/ccmsusr/_js/additional-methods-1.17.0.min.js", function() { /* JQuery Validate Additional Methods */

										$.validator.addMethod(
											"badCharRegex",
											function(value, element, regexp) {
												var re = new RegExp(regexp);
												return this.optional(element) || re.test(value);
											}, "Please check your input."
										);

										$('#info_tab_form').each(function() {
											$(this).validate({
												rules: {
													firstname: {
														maxlength: 64,
														badCharRegex: /^[^\<\>&#]+$/i
													},
													lastname: {
														maxlength: 64,
														badCharRegex: /^[^\<\>&#]+$/i
													},
													alias: {
														required: true,
														minlength: 4,
														maxlength: 32,
														badCharRegex: /^[^\<\>&#]+$/i
													},
													position: {
														maxlength: 128,
														badCharRegex: /^[^\<\>&#]+$/i
													},
													address1: {
														maxlength: 128,
														badCharRegex: /^[^\<\>&#]+$/i
													},
													address2: {
														maxlength: 128,
														badCharRegex: /^[^\<\>&#]+$/i
													},
													prov_state: {
														maxlength: 32,
														badCharRegex: /^[^\<\>&#]+$/i
													},
													country: {
														maxlength: 64,
														badCharRegex: /^[^\<\>&#]+$/i
													},
													post_zip: {
														maxlength: 32,
														badCharRegex: /^[^\<\>&#]+$/i
													},
													email: {
														required: true,
														email: true,
														maxlength: 255
													},
													phone1: {
														maxlength: 64,
														badCharRegex: /^[^\<\>&#]+$/i
													},
													phone2: {
														maxlength: 64,
														badCharRegex: /^[^\<\>&#]+$/i
													},
													skype: {
														maxlength: 32,
														badCharRegex: /^[^\<\>&#]+$/i
													},
													facebook: {
														maxlength: 128,
														badCharRegex: /^[^\<\>&#]+$/i
													},
													note: {
														maxlength: 1024,
														badCharRegex: /^[^\<\>&#]+$/i
													}
												},
												messages: {
													firstname: {
														maxlength: "This field has a maximum length of 64 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													},
													lastname: {
														maxlength: "This field has a maximum length of 64 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													},
													alias: {
														required: "This field is required.",
														minlength: "This field has a minimum length of 4 characters or more.",
														maxlength: "This field has a maximum length of 32 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													},
													position: {
														maxlength: "This field has a maximum length of 32 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													},
													address1: {
														maxlength: "This field has a maximum length of 128 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													},
													address2: {
														maxlength: "This field has a maximum length of 128 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													},
													prov_state: {
														maxlength: "This field has a maximum length of 32 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													},
													country: {
														maxlength: "This field has a maximum length of 64 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													},
													post_zip: {
														maxlength: "This field has a maximum length of 32 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													},
													email: {
														required: "Please enter a valid email address.",
														maxlength: "This field has a maximum length of 255 characters or less."
													},
													phone1: {
														maxlength: "This field has a maximum length of 64 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													},
													phone2: {
														maxlength: "This field has a maximum length of 64 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													},
													skype: {
														maxlength: "This field has a maximum length of 32 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													},
													facebook: {
														maxlength: "This field has a maximum length of 128 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													},
													note: {
														maxlength: "This field has a maximum length of 1024 characters or less.",
														badCharRegex: "The following characters are not permitted in this field.  ( > < & # )"
													}
												},
												submitHandler: function(form) {
													var request;
													// Abort any pending request.
													if (request) request.abort();
													var $inputs = $(form).find("input, select, textarea, button");
													var serializedData = $(form).serialize();
													// Disable the inputs for the duration of the ajax request.
													$inputs.prop("disabled", true);
													request = $.ajax({
														url: "/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/user_profile/info-ajax.html",
														cache: false,
														type: "post",
														data: serializedData
													});
													// Called on success.
													request.done(function(msg) {
														//console.log(msg);
														if(msg == "1") {
															//$(form).find('[name="form-status"]').html("Info form updated.");
															$("#info_tab_form_fail").css("display", "none");
															$("#info_tab_form_success").html('<span class="fa fa-check" aria-hidden="true" style="margin-right: 10px;"></span>'+"Success: Updates saved.");
															$("#info_tab_form_success").css("display", "block");
															$("#info_tab_form_success").scrollView();
															setTimeout(function() {
																//$(form).find('[name="form-status"]').html("");
																//$(form).find('[name="FromEmail"]').val("");
																//$(form).find('[name="ToEmail"]').val("");
																//$(form).find('[name="Message"]').val("");
																$("#info_tab_form_success").css("display", "none");
															}, 10000);
														} else {
															//$(form).find('[name="form-status"]').html(msg);
															$("#info_tab_form_success").css("display", "none");
															$("#info_tab_form_fail").html(msg);
															$("#info_tab_form_fail").css("display", "block");
															$("#info_tab_form_fail").scrollView();
														}
													});
													// Called on failure.
													request.fail(function (jqXHR, textStatus, errorThrown){
														// log the error to the console
														//console.error( "The following error occured: " + textStatus, errorThrown );
														//$(form).find('[name="form-status"]').html("The following error occured: " + textStatus, errorThrown);
														$("#info_tab_form_success").css("display", "none");
														$("#info_tab_form_fail").css("display", "block");
														$("#info_tab_form_fail").html("The following error occured: " + textStatus, errorThrown);
													});
													// Called if the request failed or succeeded.
													request.always(function () {
														// reenable the inputs
														setTimeout(function() {
															$inputs.prop("disabled", false);
														}, 5000);
													});
													// Prevent default posting of form.
													return false;
												}
											});
										});


										$('#password_tab_form').each(function() {
											$(this).validate({
												rules: {
													password: {
														required: true,
														minlength: 8
													},
													password1: {
														required: true,
														minlength: 8,
														equalTo: "#password2"
													},
													password2: {
														required: true,
														minlength: 8,
														equalTo: "#password1"
													}
												},
												messages: {
													password: {
														required: "This field is required.",
														maxlength: "This field has a minimum length of 8 characters or more."
													},
													password1: {
														required: "This field is required.",
														maxlength: "This field has a minimum length of 8 characters or more.",
														equalTo: "'New Password' and 'Repeat New Password' are not the same."
													},
													password2: {
														required: "This field is required.",
														maxlength: "This field has a minimum length of 8 characters or more.",
														equalTo: "'New Password' and 'Repeat New Password' are not the same."
													}
												},
												submitHandler: function(form) {
													var request;
													// Abort any pending request.
													if (request) request.abort();
													var $inputs = $(form).find("input, select, textarea, button");
													var serializedData = $(form).serialize();
													// Disable the inputs for the duration of the ajax request.
													$inputs.prop("disabled", true);
													request = $.ajax({
														url: "/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/user_profile/password-ajax.html",
														cache: false,
														type: "post",
														data: serializedData
													});
													// Called on success.
													request.done(function(msg) {
														//console.log(msg);
														if(msg == "1") {
															//$(form).find('[name="form-status"]').html("Password form updated.");
															$("#password_tab_form_fail").css("display", "none");
															$("#password_tab_form_success").html('<span class="fa fa-check" aria-hidden="true" style="margin-right: 10px;"></span>'+"Success: Updates saved.");
															$("#password_tab_form_success").css("display", "block");
															$("#password_tab_form_success").scrollView();
															setTimeout(function() {
																//$(form).find('[name="form-status"]').html("");
																//$(form).find('[name="FromEmail"]').val("");
																//$(form).find('[name="ToEmail"]').val("");
																//$(form).find('[name="Message"]').val("");
																$("#password_tab_form_success").css("display", "none");
															}, 10000);
														} else {
															//$(form).find('[name="form-status"]').html(msg);
															$("#password_tab_form_success").css("display", "none");
															$("#password_tab_form_fail").html(msg);
															$("#password_tab_form_fail").css("display", "block");
															$("#password_tab_form_fail").scrollView();
														}
													});
													// Called on failure.
													request.fail(function (jqXHR, textStatus, errorThrown){
														// log the error to the console
														//console.error( "The following error occured: " + textStatus, errorThrown );
														//$(form).find('[name="form-status"]').html("The following error occured: " + textStatus, errorThrown);
														$("#password_tab_form_success").css("display", "none");
														$("#password_tab_form_fail").css("display", "block");
														$("#password_tab_form_fail").html("The following error occured: " + textStatus, errorThrown);
													});
													// Called if the request failed or succeeded.
													request.always(function () {
														// reenable the inputs
														setTimeout(function() {
															$inputs.prop("disabled", false);
															$("#password").val("");
															$("#password1").val("");
															$("#password2").val("");
														}, 5000);
													});
													// Prevent default posting of form.
													return false;
												}
											});
										});


										$('#session_tab_logout_all_form').each(function() {
											$(this).validate({
												submitHandler: function(form) {
													var request;
													// Abort any pending request.
													if (request) request.abort();
													var $inputs = $(form).find("input, select, textarea, button");
													var serializedData = $(form).serialize();
													// Disable the inputs for the duration of the ajax request.
													$inputs.prop("disabled", true);
													request = $.ajax({
														url: "/{CCMS_LIB:_default.php;FUNC:ccms_lng}/user/user_profile/session_logout_all-ajax.html",
														cache: false,
														type: "post",
														data: serializedData
													});
													// Called on success.
													request.done(function(msg) {
														//console.log(msg);
														if(msg == "1") {
															//$(form).find('[name="form-status"]').html("Password form updated.");
															$("#session_tab_logout_all_fail").css("display", "none");
															$("#session_tab_logout_all_success").html('<span class="fa fa-check" aria-hidden="true" style="margin-right: 10px;"></span>'+"Success: All other active sessions for this account have been disabled.");
															$("#session_tab_logout_all_success").css("display", "block");
															$("#session_tab_logout_all_success").scrollView();
															setTimeout(function() {
																//$(form).find('[name="form-status"]').html("");
																//$(form).find('[name="FromEmail"]').val("");
																//$(form).find('[name="ToEmail"]').val("");
																//$(form).find('[name="Message"]').val("");
																$("#session_tab_logout_all_success").css("display", "none");
																$("#session_tab_logout_all_div").html("None found.");
															}, 7000);
														} else {
															//$(form).find('[name="form-status"]').html(msg);
															$("#session_tab_logout_all_success").css("display", "none");
															$("#session_tab_logout_all_fail").html(msg);
															$("#session_tab_logout_all_fail").css("display", "block");
															$("#session_tab_logout_all_fail").scrollView();
														}
													});
													// Called on failure.
													request.fail(function (jqXHR, textStatus, errorThrown){
														// log the error to the console
														//console.error( "The following error occured: " + textStatus, errorThrown );
														//$(form).find('[name="form-status"]').html("The following error occured: " + textStatus, errorThrown);
														$("#session_tab_logout_all_success").css("display", "none");
														$("#session_tab_logout_all_fail").css("display", "block");
														$("#session_tab_logout_all_fail").html("The following error occured: " + textStatus, errorThrown);
													});
													// Called if the request failed or succeeded.
													request.always(function () {
														// reenable the inputs
														//setTimeout(function() {
														//	$inputs.prop("disabled", false);
														//}, 2000);
													});
													// Prevent default posting of form.
													return false;
												}
											});
										});
									});
								});
							});
						});
					});
				});
			}

			if (window.addEventListener)
				window.addEventListener("load", loadJSResources, false);
			else if (window.attachEvent)
				window.attachEvent("onload", loadJSResources);
			else window.onload = loadJSResources;
		</script>
	</body>
</html>
