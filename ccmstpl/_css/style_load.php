<?php
include $_SERVER["DOCUMENT_ROOT"] . "/" . $CFG["LIBDIR"] . "/site.php"; ?>
var l=document.createElement("link");
l.rel="stylesheet";
l.href="<?php
$a = "/" . ccms_lng_ret() . "/_css/style-" . ccms_lng_dir_ret() . ".css";
echo $a;
?>";

/*<?php echo "a=[".$a."]";?>*/


l.integrity="<?php

$b = sri_ret("",$a);
echo $b;
?>";
l.crossOrigin="anonymous";
var h=document.getElementsByTagName("head")[0];
h.parentNode.insertBefore(l,h);
