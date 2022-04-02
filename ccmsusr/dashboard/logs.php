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

$query = "SELECT * FROM `ccms_log` ORDER BY `date`  DESC;";
$statement = $CFG["DBH"]->prepare($query);
$statement->execute($data);
$result = $statement->fetchAll();
foreach($result as $row){
	$output[] = array(
		'id' => $row['id'],
		'date' => $row['date'],
		'ip' => $row['ip'],
		'url' => $row['url'],
		'log' => $row['log']
	);
}

echo json_encode($output);
