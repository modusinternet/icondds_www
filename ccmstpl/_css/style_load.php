var l=document.createElement("link");
l.rel="stylesheet";
l.href="<?php
$a = "/" . ccms_lng() . "/_css/style-" . ccms_lng_dir() . ".css";
echo $a;
?>";
l.integrity="<?php
sri("",$a;
?>";
l.crossOrigin="anonymous";
var h=document.getElementsByTagName("head")[0];
h.parentNode.insertBefore(l,h);
