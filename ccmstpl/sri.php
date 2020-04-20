<?php
header("Content-Type: text/html; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
@include $_SERVER["DOCUMENT_ROOT"] . "/ccmspre/config.php";
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Used to temporarily store Subresource Integrity codes.</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	</head>
	<body>
<?php
$array = array(
	"https://d23cij6660kk94.cloudfront.net/ccmsusr/_css/animate.min.css",
	"https://d23cij6660kk94.cloudfront.net/ccmsusr/_js/jquery-3.4.1.min.js",
	"https://d23cij6660kk94.cloudfront.net/ccmsusr/_js/jquery-validate-1.19.0.min.js",
	"https://d23cij6660kk94.cloudfront.net/ccmsusr/_js/jquery-validate-additional-methods-1.19.0.min.js",
	"https://d23cij6660kk94.cloudfront.net/ccmstpl/_js/jquery.mobile.custom.min.js",
	"https://d23cij6660kk94.cloudfront.net/ccmstpl/_js/modernizr-3.6.0-custom-min.js",
	"https://d23cij6660kk94.cloudfront.net/ccmstpl/_css/owl.carousel-2.3.4.min.css",
	"https://d23cij6660kk94.cloudfront.net/ccmstpl/_js/owl.carousel.min.js"
);

if($_SERVER["REQUEST_URI"] == "/en/sri.html?flag=1") {
	try {
		$CFG["DBH"] = @new PDO("mysql:host=" . $CFG["DB_HOST"] . ";dbname=" . $CFG["DB_NAME"], $CFG["DB_USERNAME"], $CFG["DB_PASSWORD"], array(PDO::ATTR_PERSISTENT => true));
		$CFG["DBH"]->exec("set names utf8mb4");
		$CFG["DBH"]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		try {
			$CFG["DBH"]->query("TRUNCATE `sri`");
			echo "SRI table truncated.<br><br>\n";

			foreach ($array as $i => $value) {
				unset($data);
				unset($result);
				$data = file_get_contents($value);
				$result = base64_encode(hash("sha384", $data, true));



				$qry = $CFG["DBH"]->prepare("INSERT INTO `sri` (`id`, `url`, `sri-code`) VALUES (NULL, :data, :result);");
				$qry->execute(array(':data' => $data, ':result' => $result));





			}
		} catch(PDOException $e) {
			echo $e->getCode() . " " . $e->getMessage() . "<br><br>\n";
		}
	} catch(PDOException $e) {
		$CFG["pass"] = 0;
		echo $e->getCode() . " " . $e->getMessage() . "<br><br>\n";
	}
}
?>
	<form action="/en/sri.html?flag=1" method="post">
		Rebuild Subresource Integrity (SRI) codes: <button type="submit">Start</button>
	</form>

	</body>
</html>
