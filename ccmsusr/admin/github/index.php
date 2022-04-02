<?php
header("Content-Type: text/html; charset=UTF-8");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if($_SERVER["SCRIPT_NAME"] != "/ccmsusr/index.php") {
	echo "This script can NOT be called directly.";
	die();
}

$msg = array();

// Test to see if shell_exce() is disabled.
if(!is_callable('shell_exec') && true === stripos(ini_get('disable_functions'), 'shell_exec')) {
	// shell_exce() is disabled.
	$msg["shell_exce"]["error"] = TRUE;
} else {
	// shell_exce() is enabled.
	// Test to see if git is installed.
	$output = trim(shell_exec("git --version"));

	// test to confirm git is installed.
	if(preg_match("/^git version .*/i", $output)) {
		// git is installed.
		$msg["git"]["version"] = $output;

		$output = trim(shell_exec("git status"));
		if($output == "") {
			 $output = "not a git repository";
		}
		if(preg_match("/not a git repository/i", $output)) {
			// git has not been setup to work with a repository under this directory yet.
			$msg["git"]["status"]["error"] = $output;
		} elseif(!preg_match("/nothing to commit/i", $output)) {
			// There is something wrong with this repository, you might need to access it from the commandline and add/commit/push unresolved files first.
			$msg["git"]["status"]["warning"] = $output;

			// build and easier list of problem files to read from.
			$output = trim(shell_exec("git status --porcelain | cut -c4-"));
			$msg["git"]["status2"]["output"] = $output;
		} else {
			// All is well, looks like there is nothing to commit here.
			$msg["git"]["status"] = $output;
		}

		$output = trim(shell_exec("git config --list"));
		$msg["git"]["config"] = $output;

		if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/.gitignore")) {
			$msg["gitignore"] = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/.gitignore");
		}
	} else {
		// git is NOT installed.
		$msg["git"]["error"] = $output;
	}
}
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>GitHub</title>
		<meta name="description" content="" />
		{CCMS_TPL:/head-meta.html}
		<script nonce="{CCMS_LIB:_default.php;FUNC:ccms_csp_nounce}">
			var navActiveArray = ["github"];
		</script>
	</head>
	<body>
		<div id="wrapper">
			{CCMS_TPL:/header-body.php}
			<div id="page-wrapper">
				<h1 class="page-header">GitHub</h1>
				<p>GitHub is the premier tool used by website and software engineers to collaborate and synchronize more than 85 million repositories and projects around the world.  Basically, if your work involves distributing anything through the internet or collaborating with anyone other than yourself, you need to consider setting up an account on GitHub.</p>
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#status" aria-controls="status" role="tab" data-toggle="tab">Status</a></li>
<? if(isset($msg["git"]["version"])): ?>
					<li role="presentation"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">Details</a></li>
<? endif ?>
					<li role="presentation"><a href="#setup" aria-controls="setup" role="tab" data-toggle="tab">Setup</a></li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="status">
<? if(isset($msg["shell_exce"]["error"])): ?>
						<div class="panel panel-danger">
							<div class="panel-heading">Error</div>
							<div class="panel-body">
								<p>Unable to call shell_exce().  Confirm your account has access to this function with your administrator before continuing.</p>
							</div>
						</div>
<? elseif(isset($msg["git"]["error"])): ?>
						<div class="panel panel-danger">
							<div class="panel-heading">Error</div>
							<div class="panel-body">
								<p>.git is either NOT installed or you do not have access to git from this account.  Confirm with your administrator before continuing.</p>
								<pre style="padding: 15px; margin: 15px 0px 20px;"><?=$msg["git"]["error"];?></pre>
							</div>
						</div>
<? else: ?>
						<h2>git status</h2>
	<? if(isset($msg["git"]["status"]["error"])): ?>
						<div class="panel panel-danger">
							<div class="panel-heading">Error</div>
							<div class="panel-body">
								<p>No .git repository setup in this directory or any of it's parent directories yet.  <a class="href-to-setup" href="#setup">Click here</a> to learn more about how to set up and connect this website to your own GitHub repository.</p>
								<pre style="padding:15px;margin:15px 0px 20px">fatal: not a git repository (or any of the parent directories): .git</pre>
							</div>
						</div>
	<? elseif(isset($msg["git"]["status"]["warning"])): ?>
						<div class="panel panel-warning">
							<div class="panel-heading">Warning</div>
							<div class="panel-body">
								<p>There is something wrong with this repository, you might need to access it from the command-line and run add/commit/push manunally to fix it.</p>
								<pre style="padding: 15px; margin: 15px 0px 20px;"><?=$msg["git"]["status"]["warning"];?></pre>
								<p>(Easier to read file list, remember all files listed are located relative to the document root of your website.)</p>
								<pre style="padding: 15px; margin: 15px 0px 20px;"><?=$msg["git"]["status2"]["output"];?></pre>
								<p>Note: Pushing from your server to a GitHub repository is not recommended for security reasons which is why it is not an automated feature in Custodian CMS.  Use the two commands below if needed.</p>
								<p class="boxed">
									git commit -am "from server"<br>
									git push
								</p>
								<p>
									Note: Or, if all you want to do is overwrite a single file on your server with what's currently on the GitHub repo you can try the following command. (NOTE: You may need to navigate into the dir that contains the file you want to overwrite first.)
								</p>
								<p class="boxed">
									git checkout origin/master -- {filename}<br>
									git checkout -- .htaccess<br>
									git checkout origin/main -- ccmstpl/examples/index.html
								</p>
							</div>
						</div>
	<? else: ?>
						<div class="panel panel-success">
							<div class="panel-heading">Success</div>
							<div class="panel-body">
								<pre style="padding: 15px; margin: 15px 0px 20px;"><?=$msg["git"]["status"];?></pre>
							</div>
						</div>
	<? endif ?>
<? endif ?>
					</div>
<? if(isset($msg["git"]["version"])): ?>
					<div role="tabpanel" class="tab-pane" id="details">
						<h2>git --version</h2>
						<pre style="padding: 15px; margin: 15px 0px 20px;"><?=$msg["git"]["version"];?></pre>
						<h2>git config --list</h2>
						<pre style="padding: 15px; margin: 15px 0px 20px;"><?=$msg["git"]["config"];?></pre>
						<h2>.gitignore</h2>
	<? if(isset($msg["gitignore"])): ?>
						<pre style="padding: 15px; margin: 15px 0px 20px;"><?=$msg["gitignore"];?></pre>
	<? else: ?>
						<pre style="padding: 15px; margin: 15px 0px 20px;">.gitignore not found.</pre>
	<? endif ?>
					</div>
<? endif ?>
					<div role="tabpanel" class="tab-pane" id="setup">
						<p style="margin: 15px 0px;">Listed below are the basic setup details to connect your website to a GitHub repository.  For more information about how to setup and maintain Git on your server visit <a href="https://git-scm.com/docs" target="_blank">https://git-scm.com/docs</a>.</p>
						<h2>Repository and Webserver Setup</h2>
						<ol class="boxed">
							<li>Create a new repository at GitHub. (<a href="https://github.com" target="_blank">https://github.com</a>)</li>
							<li>Add your web servers public ssh-key (id_rsa.pub) to your GitHub account under "Settings/SSH and GPG keys". (Follow instructions here to generate a new ssh-key if needed: <a href="https://help.github.com/articles/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent/" target="_blank">https://help.github.com/articles/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent/</a>)</li>
							<li>Add a webhook on GitHub under "Settings/Webhooks": https://<?=$CFG["DOMAIN"];?>/ccmsusr/github/webhook.php</li>
							<li>Create a new website folder on your server. (You must have access to shell, ssh and git services.)</li>
						</ol>
						<h2>Copy Custondian CMS Templates to Webserver</h2>
						<p style="margin: 15px 0px;">You can download the latest master version of the Custodian CMS templates from <a href="https://github.com/modusinternet/Custodian-CMS/archive/master.zip" target="_blank">GitHub</a> directly or use the <a href="https://github.com/modusinternet/Custodian-CMS-Download" target="_blank">Custodian CMS Download</a>.  If you prefer SSH, log into your server and type the following on the command-line.</p>
						<ol class="boxed">
							<li>git clone --depth=1 https://github.com/modusinternet/Custodian-CMS.git /tmp/Custodian-CMS</li>
							<li>rm -rf /tmp/Custodian-CMS/.git</li>
							<li>shopt -s dotglob</li>
							<li>cp -r /tmp/Custodian-CMS/* /THE_PATH_TO_YOUR_WEBSITES_DOCUMMENT_ROOT</li>
							<li>rm -rf /tmp/Custodian-CMS</li>
						</ol>
						<h2>Initialize git on the Webserver</h2>
						<p style="margin: 15px 0px;">Once you've finished moving a copy of the Custodian CMS templates into place initialize git at the document root of the website and connect it to your GitHub repository.</p>
						<ol class="boxed">
							<li>Test your connection to the GitHub servers via ssh:<br>
								ssh -T git@github.com<br>
								If successful, type the following commands:</li>
							<li>git init</li>
							<li>git add .</li>
							<li>git config --global user.email "noreply@<?=$CFG["DOMAIN"];?>"</li>
							<li>git config --global user.name "YOUR_NAME"</li>
							<li>git commit -m "first commit"</li>
							<li>git remote add origin git@github.com:YOUR_ACCOUNT_ON_GITHUB/YOUR_REPO_ON_GITHUB.git</li>
							<li>git push -u origin master</li>
						</ol>
						<h2>Install Local Software</h2>
						<ol class="boxed">
							<li>Check GitHub to see if all the files on your web server have been copied over.</li>
							<li>Install GitHub Desktop (<a href="https://desktop.github.com" target="_blank">https://desktop.github.com</a>) on your PC and File/Clone Repository to somewhere on your computer.</li>
							<li>Install the Atom editor (<a href="https://atom.io" target="_blank">https://atom.io</a>) and go to "File/Add Project Folder" and select the document root folder containing the local copy of your repositories.  You should now be able to make changes using Atom, commit your changes to GitHub which will automaticaly submit them to your live website using the webhook.</li>
						</ol>
					</div>
				</div>
			</div>
		</div>

		<script nonce="{CCMS_LIB:_default.php;FUNC:ccms_csp_nounce}">
			{CCMS_TPL:/_js/footer-1.php}

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

			function loadJSResources() {
				/*loadFirst("/ccmsusr/_js/jquery-2.2.0.min.js", function() {*/
				loadFirst("/ccmsusr/_js/jquery-3.6.0.min.js", function() {
					loadFirst("/ccmsusr/_js/bootstrap-3.3.7.min.js", function() {
						loadFirst("/ccmsusr/_js/metisMenu-3.0.7.min.js", function() {
							loadFirst("/ccmsusr/_js/custodiancms.js", function() {

								navActiveArray.forEach(function(s) {$("#"+s).addClass("active");});

								// Load MetisMenu
								$('#side-menu').metisMenu();

								$("#menu-toggle").click(function(e) {
									e.preventDefault();
									$("#wrapper").toggleClass("toggled");
									$("#wrapper.toggled").find("#sidebar-wrapper").find(".collapse").collapse("hide");
									$("#sidebar-wrapper").toggle();
								});

								$('.href-to-setup').click(function(e) {
									e.preventDefault();
									var a = $('a[href="' + $(this).attr('href') + '"]');
									a.tab('show');
								});
							});
						});
					});
				});
			}
		</script>
	</body>
</html>
