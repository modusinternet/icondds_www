<?
header("Content-Type: application/json; charset=UTF-8");
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + ($CFG["CACHE_EXPIRE"] * 60)));
?>{
	"short_name": "ICONIC",
	"name": "ICONIC Dentistry",
	"description": "At ICONIC Dentistry we serve patients with the goal of achieving better oral and systemic health.",
	"icons": [
		{
			"src": "https://d23cij6660kk94.cloudfront.net/ccmstpl/_img/ico/android-chrome-192x192.png",
			"sizes": "192x192",
			"type": "image/png"
		},
		{
			"src": "https://d23cij6660kk94.cloudfront.net/ccmstpl/_img/ico/android-chrome-256x256.png",
			"sizes": "256x256",
			"type": "image/png"
		},
		{
			"src": "https://d23cij6660kk94.cloudfront.net/ccmstpl/_img/ico/android-chrome-512x512.png",
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
