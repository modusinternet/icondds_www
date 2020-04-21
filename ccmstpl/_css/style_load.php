var l=document.createElement("link");
l.rel="stylesheet";
l.href="<?php
$a = "/" . ccms_lng_ret() . "/_css/style-" . ccms_lng_dir_ret() . ".css";
echo $a;
?>";
l.integrity="<?php
$a = sri("","CSS-01");
echo $a;
?>";
l.crossOrigin="anonymous";
var h=document.getElementsByTagName("head")[0];
h.parentNode.insertBefore(l,h);
