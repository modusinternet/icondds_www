<?
header("Content-Type: application/json; charset=UTF-8");
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + ($CFG["CACHE_EXPIRE"] * 60)));
?>{
	"short_name": "Modus",
	"name": "ICONIC Dentistry",
	"description": "Description goes here.",
	"icons": [
		{
			"src": "/ccmstpl/_img/ico/android-chrome-192x192.png",
			"sizes": "192x192",
			"type": "image/png"
		},
		{
			"src": "/ccmstpl/_img/ico/android-chrome-256x256.png",
			"sizes": "256x256",
			"type": "image/png"
		},
		{
			"src": "/ccmstpl/_img/ico/android-chrome-512x512.png",
			"sizes": "512x512",
			"type": "image/png"
		}
	],
	"start_url": "/",
	"theme_color": "#ffffff",
	"background_color": "#ffffff",
	"display": "standalone",
	"scope": "/",
	"lang": "{CCMS_LIB:_default.php;FUNC:ccms_lng}",
	"dir": "{CCMS_LIB:_default.php;FUNC:ccms_lng_dir}"
}
