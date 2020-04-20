<?php
header("Content-Type: text/html; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
@include $_SERVER["DOCUMENT_ROOT"] . "ccmspre/config.php";
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Used to temporarily store Subresource Integrity codes.</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	</head>
	<body>


<?php if($_SERVER["REQUEST_URI"] == "/en/sri.html?flag=1") {
	try {
		$CFG["DBH"] = @new PDO("mysql:host=$CFG["DB_HOST"];dbname=$CFG["DB_NAME"]", $CFG["DB_USERNAME"], $CFG["DB_PASSWORD"], array(PDO::ATTR_PERSISTENT => true));
		$CFG["DBH"]->exec("set names utf8mb4");
		$CFG["DBH"]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		try {
			$CFG["DBH"]->query("TRUNCATE `iconicde_www`.`sri`");
			echo "\nsri table truncated<br>\n";
		} catch(PDOException $e) {
			echo "\n" . $e->getCode() . " " . $e->getMessage() . "<br>\n";
		}





	} catch(PDOException $e) {
		$CFG["pass"] = 0;
		$msg = $e->getCode() . ' ' . $e->getMessage();
	}
?>

	<form action="/en/sri.html?flag=1" method="post">
		<button type="submit">Submit</button>
	</form>

	</body>
</html>
