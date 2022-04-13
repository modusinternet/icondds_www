<?php
header("Content-Type: application/manifest+json; charset=utf-8");
header("Expires: " . gmdate("D, d M Y H:i:s T", time() + ($CFG["CACHE_EXPIRE"] * 60)));
?>{CCMS_DB_PRELOAD:all,index}{
	"short_name": "ICONIC",
	"name": "{CCMS_DB:all,company-name}",
	"description": "{CCMS_DB:index,description}",
	"icons": [
		{
			"src": "{CCMS_LIB:site.php;FUNC:load_resource("AWS")}/ccmstpl/_img/ico/android-chrome-192x192.png",
			"sizes": "192x192",
			"type": "image/png"
		},{
			"src": "{CCMS_LIB:site.php;FUNC:load_resource("AWS")}/ccmstpl/_img/ico/android-chrome-192x192.png",
			"sizes": "192x192",
			"type": "image/png",
			"purpose": "maskable"
		},{
			"src": "{CCMS_LIB:site.php;FUNC:load_resource("AWS")}/ccmstpl/_img/ico/android-chrome-256x256.png",
			"sizes": "256x256",
			"type": "image/png",
			"purpose": "any"
		},{
			"src": "{CCMS_LIB:site.php;FUNC:load_resource("AWS")}/ccmstpl/_img/ico/android-chrome-512x512.png",
			"sizes": "512x512",
			"type": "image/png",
			"purpose": "any"
		}
	],
	"start_url": "/",
	"theme_color": "#006058",
	"background_color": "#006058",
	"display": "standalone",
	"scope": "/",
	"lang": "{CCMS_LIB:_default.php;FUNC:ccms_lng}",
	"dir": "{CCMS_LIB:_default.php;FUNC:ccms_lng_dir}"
}
