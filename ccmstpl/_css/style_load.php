var l=document.createElement("link");
l.rel="stylesheet";


/* Use this one for loading it locally, through the parser. */
l.href="<?php $a = "/" . ccms_lng_ret() . "/_css/style-" . ccms_lng_dir_ret() . ".css"; echo $a; ?>";


/* Use this one for loading it locally, without passing it through the parser. */
/*l.href="<?php $a = "/ccmstpl/_css/style-" . ccms_lng_dir_ret() . ".css"; echo $a; ?>";*/


/* Use this one for loading it off AWS, this one includes the Subresource Integrity code. */
/*l.href="<?php $a = $CFG["AWS"] . "/ccmstpl/_css/style-" . ccms_lng_dir_ret() . ".css"; echo $a; ?>";
l.integrity="<?php $a = sri("", "CSS-01"); echo $a; ?>";
l.crossOrigin="anonymous";*/


var h=document.getElementsByTagName("head")[0];
h.parentNode.insertBefore(l,h);
