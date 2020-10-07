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

const cacheName='{CCMS_LIB:_default.php;FUNC:ccms_lng}-2020.10.06-01';

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

addEventListener('install',e=>{
	e.waitUntil(
		caches.open(cacheName).then(cache=>{
			return cache.addAll(cacheFiles);
		})
	);
});

/* This event fires after the worker is up and running.  It looks for
and removes old services workers and their cache based on version number. */
addEventListener('activate',e=>{
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
/*
addEventListener('fetch',e=>{
	const {request}=e;

	if(request.headers.has('range')) return;

	e.respondWith(async function(){

		const cachedResponse=await caches.match(request);
		if(cachedResponse) return cachedResponse;

		try{
			return await fetch(request);
		}catch(err){
			if(request.mode==='navigate'){
				return caches.match('/{CCMS_LIB:_default.php;FUNC:ccms_lng}/offline.html');
			}

			throw err;
		}
	}());
});
*/

/*
self.addEventListener('fetch', e => {
	//const {request}=e;
	console.log("1");

	// Always bypass for range requests, due to browser bugs.
	if(e.request.headers.has('range')) return;
	console.log("2");

	e.respondWith(async function() {
		console.log("3");

		// Try to get from the cache.

		const cachedResponse = await caches.match(e.request);
		console.log("4");

		if(cachedResponse) return cachedResponse;
		console.log("5");

		// Otherwise get from the network.
		try {
			console.log("6");

			return await fetch(e.request);
		} catch(err) {
			console.log("7");

			// If this was a navigation, a page requested by the user via clicking on a link and not a .css or .js resource, show the offline page.

			if(e.request.mode === 'navigate') {
				console.log("8");

				return caches.match('/{CCMS_LIB:_default.php;FUNC:ccms_lng}/offline.html');
			}
			console.log("9");

			// Otherwise throw.
			throw err;
		}
	}());
});
*/




/*
self.addEventListener('fetch', (event) => {
  event.respondWith(async function() {

    const cache = await caches.open(cacheName);
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
*/


/*
addEventListener('fetch', (event) => {
  event.respondWith(async function() {

    const cache = await caches.open(cacheName);
    const cachedResponse = await cache.match(event.request);
    const networkResponsePromise = fetch(event.request);

    event.waitUntil(async function() {
      const networkResponse = await networkResponsePromise;
      await cache.put(event.request, networkResponse.clone());
    }());

		try {
			return cachedResponse || networkResponsePromise;
		} catch(err) {
			console.log("7");

			if(e.request.mode === 'navigate') {
				// If this was a navigation, a page requested by the user via clicking on a link and not a .css or .js resource, show the offline page.
				return cache.match('/{CCMS_LIB:_default.php;FUNC:ccms_lng}/offline.html');
			}

			// Otherwise throw.
			throw err;
		}
  }());
});
*/

/*
self.addEventListener('fetch', (event) => {
  // We only want to call event.respondWith() if this is a navigation request
  // for an HTML page.
  if (event.request.mode === 'navigate') {
    event.respondWith((async () => {
      try {
        // First, try to use the navigation preload response if it's supported.
        const preloadResponse = await event.preloadResponse;
        if (preloadResponse) {
          return preloadResponse;
        }

        const networkResponse = await fetch(event.request);
        return networkResponse;
      } catch (error) {
        // catch is only triggered if an exception is thrown, which is likely
        // due to a network error.
        // If fetch() returns a valid HTTP response with a response code in
        // the 4xx or 5xx range, the catch() will NOT be called.
        console.log('Fetch failed; returning offline page instead.', error);

        const cache = await caches.open(cacheName);
        const cachedResponse = await cache.match('/{CCMS_LIB:_default.php;FUNC:ccms_lng}/offline.html');
        return cachedResponse;
      }
    })());
  }
	// If our if() condition is false, then this fetch handler won't intercept the
  // request. If there are any other fetch handlers registered, they will get a
  // chance to call event.respondWith(). If no fetch handlers call
  // event.respondWith(), the request will be handled by the browser as if there
  // were no service worker involvement.
});
*/


/*
//self.addEventListener('fetch', (event) => {
addEventListener('fetch', (event) => {
  event.respondWith(async function() {

    try {
			const cache = await caches.open(cacheName);
	    const cachedResponse = await cache.match(event.request);
	    const networkResponsePromise = fetch(event.request);

	    event.waitUntil(async function() {
	      const networkResponse = await networkResponsePromise;
	      await cache.put(event.request, networkResponse.clone());
	    }());

	    // Returned the cached response if we have one, otherwise return the network response.
	    return cachedResponse || networkResponsePromise;
		} catch (error) {
			// catch is only triggered if an exception is thrown, which is likely
			// due to a network error.
			// If fetch() returns a valid HTTP response with a response code in
			// the 4xx or 5xx range, the catch() will NOT be called.
			console.log('Fetch failed; returning offline page instead.', error);

			const cache = await caches.open(cacheName);
			const cachedResponse = await cache.match('/{CCMS_LIB:_default.php;FUNC:ccms_lng}/offline.html');
			return cachedResponse;
		}
  }());
});
*/

addEventListener('fetch', e => {
	//console.log('Fetching: ' + e.request);

  e.respondWith(
		console.log('Fetching: ', e.request);

		// If there is no internet
		fetch(e.request).catch((error) =>
			caches.match('/{CCMS_LIB:_default.php;FUNC:ccms_lng}/offline.html')
		)
	);
});
