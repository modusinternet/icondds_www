<?php
$CFG = array();
$CLEAN = array();

require_once "./config.php";

$CLEAN["CCMS_DB_Preload_Content"] = array();
$host       = $CFG["DB_HOST"];
$dbname         = $CFG["DB_NAME"];
$user       = $CFG["DB_USERNAME"];
$pass       = $CFG["DB_PASSWORD"];

try {
    // MSSQL
    // $CFG["DBH"] = new PDO("mssql:host=$host;dbname=$dbname, $user, $pass");
    // MySQL
    $CFG["DBH"] = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    // SQLite
    // $CFG["DBH"] = new PDO("sqlite:my/database/path/database.db");
    // Sybase
    // $CFG["DBH"] = new PDO("sybase:host=$host;dbname=$dbname, $user, $pass");
    // Sets encoding UTF-8
    /*
	Great sites talking about how to handle the utf-8 character sets properly:
	https://www.toptal.com/php/a-utf-8-primer-for-php-and-mysql
	https://mathiasbynens.be/notes/mysql-utf8mb4
	*/
    $CFG["DBH"]->exec("set names utf8mb4");
    $CFG["DBH"]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    if ($CFG["DEBUG_SQL"] == 1) {
        echo "Error!: " . $e->getCode() . '<br />\n'. $e->getMessage();
    }
    die();
}

$qry = $CFG["DBH"]->prepare("DELETE FROM `ccms_session` WHERE `exp` < :time;");
$qry->execute(array(':time' => time()));
echo "Crontask to clean up " . $CFG["DOMAIN"] . " session table done...\n\n";
