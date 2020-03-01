<?php
header("Content-Type: text/html; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if (!strstr($_SERVER["HTTP_REFERER"], $CFG["DOMAIN"])) {
    exit("Invalid submission, your POST does not appeared to have been submitted from the " . $CFG["DOMAIN"] . " website.");
}
if (!ccms_badIPCheck($_SERVER["REMOTE_ADDR"])) {
    exit("There is a problem with your message, it can not be posted using this form from your current IP Address.  Please contact the website administrators directly by either phone or email if you feel this message is in error for more information.");
}

$json_a = json_decode($CLEAN["SESSION"]["priv"], true);
if (!($json_a[priv][content_manager][r] == 1 && $json_a[priv][content_manager][lng][$CLEAN["ccms_lng"]] == 2)) {
    $error = "You are not permitted to make edits to content in this language, at this time.  You can double check your privileges in the user/admin area.";
}

if ($CLEAN["ccms_ins_db_id"] == "") {
    $error = "Database record missing.";
} elseif ($CLEAN["ccms_ins_db_id"] == "MINLEN") {
    $error = "Database record must be between 1-2147483647.";
} elseif ($CLEAN["ccms_ins_db_id"] == "MAXLEN") {
    $error = "Database record must be between 1-2147483647.";
} elseif ($CLEAN["ccms_ins_db_id"] == "INVAL") {
    $error = "Database record contains invalid characters.  ( > < & # )  You have used characters in this field which are either not supported by this field or we do not permitted on this system.";
}

if ($CLEAN["ccms_ins_db_text"] == "MAXLEN") {
    $error = "Text fields can not be larger then 16000 characters in order to accomadate UTF-8 characters.";
} elseif ($CLEAN["ccms_ins_db_text"] == "INVAL") {
    $error = "The text field contains invalid characters.  ( > < & # )  You have used characters in this field which are either not supported by this field or we do not permitted on this system.";
}

if (!$error) {
    //echo "CLEAN['ccms_ins_db_text'] 4=[ " . $CLEAN["ccms_ins_db_text"] . " ]\n\n";
    $search =   array("&lt;","&gt;");
    $replace =  array("<",">");
    $CLEAN["ccms_ins_db_text"] = str_replace($search, $replace, $CLEAN["ccms_ins_db_text"]);

    //<a>, <br>, <img>, <p>, <pre> and <span>
    $search =   array(
    "{ CCMS",
    "<blockquote>","<blockquote ","</blockquote>",
    "<br>","<br/>","<br />",
    "<a ","</a>",
    "<i ","</i>",
    "<img ",
    "<p>","<p ","</p>",
    "<span ","</span>",
    "<ul>","<ul ","</ul>",
    "<ol>","<ol ","</ol>",
    "<li>","<li ","</li>",
    );
    $replace =  array(
    "{CCMS",
    "{BLOCKQUOTE1-BEGIN}","{BLOCKQUOTE2-BEGIN}","{BLOCKQUOTE-END}",
    "{BREAK}","{BREAK}","{BREAK}",
    "{A-BEGIN}","{A-END}",
    "{I-BEGIN}","{I-END}",
    "{IMG-BEGIN}",
    "{P1-BEGIN}","{P2-BEGIN}","{P-END}",
    "{SPAN-BEGIN}","{SPAN-END}",
    "{UL1-BEGIN}","{UL2-BEGIN}","{UL-END}",
    "{OL1-BEGIN}","{OL2-BEGIN}","{OL-END}",
    "{LI1-BEGIN}","{LI2-BEGIN}","{LI-END}",
    );
    $CLEAN["ccms_ins_db_text"] = str_replace($search, $replace, $CLEAN["ccms_ins_db_text"]);

    // Allow <a>, <br>, <img>, <p>, <pre> and <span> tags
    //$CLEAN["ccms_ins_db_text"] = strip_tags($CLEAN["ccms_ins_db_text"], "<a><img><p><pre><span>");
    $CLEAN["ccms_ins_db_text"] = strip_tags($CLEAN["ccms_ins_db_text"]);

    $search =   array(
    "{BLOCKQUOTE1-BEGIN}","{BLOCKQUOTE2-BEGIN}","{BLOCKQUOTE-END}",
    "{BREAK}",
    "{A-BEGIN}","{A-END}",
    "{I-BEGIN}","{I-END}",
    "{IMG-BEGIN}",
    "{P1-BEGIN}","{P2-BEGIN}","{P-END}",
    "{SPAN-BEGIN}","{SPAN-END}",
    "{UL1-BEGIN}","{UL2-BEGIN}","{UL-END}",
    "{OL1-BEGIN}","{OL2-BEGIN}","{OL-END}",
    "{LI1-BEGIN}","{LI2-BEGIN}","{LI-END}",
    );
    $replace =  array(
    "<blockquote>","<blockquote ","</blockquote>",
    "<br />",
    "<a ","</a>",
    "<i ","</i>",
    "<img ",
    "<p>","<p ","</p>",
    "<span ","</span>",
    "<ul>","<ul ","</ul>",
    "<ol>","<ol ","</ol>",
    "<li>","<li ","</li>",
    );

    $CLEAN["ccms_ins_db_text"] = str_replace($search, $replace, $CLEAN["ccms_ins_db_text"]);

    $qry = $CFG["DBH"]->prepare("UPDATE `ccms_ins_db` SET `" . $CLEAN["ccms_lng"] . "` = :ccms_ins_db_text WHERE `ccms_ins_db`.`id` = :ccms_ins_db_id;");
    $qry->execute(array(':ccms_ins_db_text' => $CLEAN["ccms_ins_db_text"], ':ccms_ins_db_id' => $CLEAN["ccms_ins_db_id"]));
    echo "1";
} else {
    echo $error;
}
