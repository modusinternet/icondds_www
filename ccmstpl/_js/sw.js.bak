/* In order to get the listed resources below to load properly once moved to Amazon's CloudFront servers you need to add this to your S3 bucket, under Permissions/CORS configuration:

<?xml version="1.0" encoding="UTF-8"?>
<CORSConfiguration xmlns="http://s3.amazonaws.com/doc/2006-03-01/">
	<CORSRule>
		<AllowedOrigin>https://PUTYOURDOMAINNAMEHERE.com</AllowedOrigin>
		<AllowedMethod>GET</AllowedMethod>
		<MaxAgeSeconds>3000</MaxAgeSeconds>
		<AllowedHeader>Authorization</AllowedHeader>
	</CORSRule>
</CORSConfiguration>


Then you need to select the appropriate distribution under CloudFront and click the Behaviors tab.  Create or Edit an existing Behavior and select the following settings:

Allowed HTTP Methods: GET, HEAD, OPTIONS
Cache Based on Selected Request Headers: Whitelist
Add these to the right box under Whitelist Headers:
	Access-Control-Request-Headers
	Access-Control-Request-Method
	Origin


Then click the 'Yes, Edit' button at the bottom and give it about 10 minutes to propagate through the system and test using Chrome.
*/

const cacheName='{CCMS_LIB:_default.php;FUNC:ccms_lng}-2020.10.04-01';

/*
Argument details for build_css_link2() and build_js_link() function calls:
arg1 = (1 = append AWS link), (empty = do not append AWS link)
arg2 = (1 = append language code to link), (empty = do not append language code to link)	In other words, send it through the parser first like a normal template.
arg3 = a variable found in the config file that represents a partial pathway to the style sheet, not including and details about AWS, language code, or language direction)
arg4 = (1 = append language direction to link), (empty = do not append language direction to link)
*/
var cacheFiles=[
	'/{CCMS_LIB:_default.php;FUNC:ccms_lng}/offline.html',
	'/ccmstpl/_img/ico/apple-touch-icon.png',
	'/ccmstpl/_img/ico/safari-pinned-tab.svg',
	'/ccmstpl/_img/ico/favicon.ico',
	'/ccmstpl/_img/ico/favicon-32x32.png',
	'/ccmstpl/_img/ico/favicon-16x16.png',
	'/{CCMS_LIB:_default.php;FUNC:ccms_lng}/manifest.html',
	'/ccmstpl/_img/logo1.3.webp',
	'/ccmstpl/_img/logo1.3.png',
	'/ccmstpl/_img/logo2.png',
	'/ccmstpl/_img/offline_01.webp',
	'/ccmstpl/_img/offline_01-min.jpg',
	'{CCMS_LIB:site.php;FUNC:build_css_link2("","1","CSS-01","1")}',
	'{CCMS_LIB:site.php;FUNC:load_resource("MODERNIZER")}',
	'{CCMS_LIB:site.php;FUNC:load_resource("JQUERY")}',
	'{CCMS_LIB:site.php;FUNC:load_resource("JQUERY-MOBILE-CUST")}',
	'{CCMS_LIB:site.php;FUNC:load_resource("JQUERY-VALIDATE")}',
	'{CCMS_LIB:site.php;FUNC:load_resource("JQUERY-VALIDATE-ADDITIONAL-METHODS")}',
	'{CCMS_LIB:site.php;FUNC:build_js_link("","1","JS-01")}'
];

/* Analytics and Service Worker: https://developers.google.com/web/ilt/pwa/integrating-analytics#analytics_and_service_worker */
/*self.importScripts('/ccmstpl/_js/analytics-helper.js');*/

self.addEventListener('install',e=>{
	e.waitUntil(
		caches.open(cacheName).then(cache=>{
			return cache.addAll(cacheFiles);
		})
	);
});

/* This event fires after the worker is up and running.  It looks for
and removes old services workers and their cache based on version number. */
self.addEventListener('activate',e=>{
	e.waitUntil(
		caches.keys().then(keyList=>{
			return Promise.all(keyList.map(key=>{
				if(key!==cacheName) {
					return caches.delete(key);
				}
			}));
		})
	);
});

/* Fetchs cached resources first, otherwise gets from the network.  If no
network connection displays the offline page. */
self.addEventListener('fetch',e=>{
	const {request}=e;
	/* Always bypass for range requests, due to browser bugs. */
	if(request.headers.has('range')) return;
	e.respondWith(async function(){
		/* Try to get from the cache. */
		const cachedResponse=await caches.match(request);
		if(cachedResponse) return cachedResponse;
		/* Otherwise get from the network. */
		try{
			return await fetch(request);
		}catch(err){
			/* If this was a navigation, a page requested by the user via clicking on a link and not a .css or .js resource, show the offline page. */
			if(request.mode==='navigate'){
				return caches.match('/{CCMS_LIB:_default.php;FUNC:ccms_lng}/offline.html');
			}
			/* Otherwise throw. */
			throw err;
		}
	}());
});
