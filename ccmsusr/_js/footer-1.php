function loadFirst(e,t,i,c){var a=document.createElement("script");a.async="true";a.setAttribute("nonce", "{CCMS_LIB:_default.php;FUNC:ccms_csp_nounce}");if(i){a.integrity=i;a.crossOrigin=c;}a.readyState?a.onreadystatechange=function(){("loaded"==a.readyState||"complete"==a.readyState)&&(a.onreadystatechange=null,t())}:a.onload=function(){t()},a.src=e,document.body.appendChild(a)}

/* Reload Screen START
window.setTimeout(function(){
	location.reload(true);
},({CCMS_LIB:_default.php;FUNC:ccms_cfgCookieSessionExpire}*1000)+10000);
Reload Screen END */

if(window.addEventListener){
	window.addEventListener("load",loadJSResources,false);
}else if(window.attachEvent){
	window.attachEvent("onload",loadJSResources);
}else{window.onload=loadJSResources;}
