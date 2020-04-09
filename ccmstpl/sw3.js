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

const cacheName='{CCMS_LIB:_default.php;FUNC:ccms_lng}-2020.04.06-09';

var cacheFiles=[
	/*
	'{  CCMS_LIB:site.php;FUNC:load_resource("ANIMATE")}',
	'{  CCMS_LIB:site.php;FUNC:load_resource("JQUERY")}',
	'{  CCMS_LIB:site.php;FUNC:load_resource("JQUERY-VALIDATE")}',
	'{  CCMS_LIB:site.php;FUNC:load_resource("JQUERY-VALIDATE-ADDITIONAL-METHODS")}',
	'{  CCMS_LIB:site.php;FUNC:load_resource("OWL-CSS")}',
	'{  CCMS_LIB:site.php;FUNC:load_resource("OWL-JS")}',
	'{  CCMS_LIB:site.php;FUNC:load_resource("JQUERY-MOBILE-CUST")}',
	'{  CCMS_LIB:site.php;FUNC:load_resource("MODERNIZER")}',
	'{  CCMS_LIB:site.php;FUNC:css_01}',
	'{  CCMS_LIB:site.php;FUNC:js_01}',
	*/
	'{CCMS_LIB:site.php;FUNC:load_resource("AWS")}/ccmstpl/_img/offline_01.webp',
	'{CCMS_LIB:site.php;FUNC:load_resource("AWS")}/ccmstpl/_img/offline_01-min.jpg',
	'/{CCMS_LIB:_default.php;FUNC:ccms_lng}/offline.html'
];

/* Analytics and Service Worker: https://developers.google.com/web/ilt/pwa/integrating-analytics#analytics_and_service_worker */
self.importScripts('/ccmstpl/_js/analytics-helper.js');


self.addEventListener('install',e=>{
	e.waitUntil(
		caches.open(cacheName).then(cache=>{
			return cache.addAll(cacheFiles);
		})
	);
});

/* This event fires after the asdf worker is up and running.
We use this to delete old services workers. */
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

addEventListener('fetch',e=>{
	/*const {request}=e;*/
	/* Always bypass for range requests, due to browser bugs. */
	/*if(e.headers.has('range')) return;*/
	e.respondWith(async function(){
		/* Try to get from the cache. */
		const cachedResponse=await caches.match(e.request);

		const networkResponsePromise=fetch(e.request);
		e.waitUntil(async function(){
			const networkResponse=await networkResponsePromise;
			await cache.put(e.request,networkResponse.clone());
		}());

		if(cachedResponse){
			return cachedResponse;
		} else if (networkResponse){
			return networkResponse;
		} else {
			if(request.mode==='navigate'){
				return caches.match('/{CCMS_LIB:_default.php;FUNC:ccms_lng}/offline.html');
			}
		}
	}());
});

self.addEventListener('fetch', (event) => {
  event.respondWith(async function() {
    const cache = await caches.open('mysite-dynamic');
    const cachedResponse = await cache.match(event.request);
    const networkResponsePromise = fetch(event.request);

    event.waitUntil(async function() {
      const networkResponse = await networkResponsePromise;
      await cache.put(event.request, networkResponse.clone());
    }());

    // Returned the cached response if we have one, otherwise return the network response.
    return cachedResponse || networkResponsePromise;
  }());
});
