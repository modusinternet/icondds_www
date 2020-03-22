const cacheName='v2020.03.22-02';

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

//https://d23cij6660kk94.cloudfront.net
//https://s3-us-west-1.amazonaws.com/icondds.com-www-01

var cacheFiles=[
	'https://s3-us-west-1.amazonaws.com/icondds.com-www-01/ccmsusr/_css/animate.min.css',
	'https://s3-us-west-1.amazonaws.com/icondds.com-www-01/ccmsusr/_js/jquery-3.4.1.min.js',
	'https://s3-us-west-1.amazonaws.com/icondds.com-www-01/ccmsusr/_js/jquery-validate-1.19.0.min.js',
	'https://s3-us-west-1.amazonaws.com/icondds.com-www-01/ccmsusr/_js/jquery-validate-additional-methods-1.19.0.min.js',
	'https://s3-us-west-1.amazonaws.com/icondds.com-www-01/ccmstpl/_css/owl.carousel-2.3.4.min.css',
	'https://s3-us-west-1.amazonaws.com/icondds.com-www-01/ccmstpl/_js/owl.carousel.min.js',
	'https://s3-us-west-1.amazonaws.com/icondds.com-www-01/ccmstpl/_js/jquery.mobile.custom.min.js',
	'https://s3-us-west-1.amazonaws.com/icondds.com-www-01/ccmstpl/_js/modernizr-3.6.0-custom-min.js',
	'/en/_css/style-ltr.css',
	'/en/_js/main.js'
]

self.addEventListener('install',e=>{
	e.waitUntil(
		caches.open(cacheName).then(cache=>{
			return cache.addAll(cacheFiles);
		})
	);
});

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

// Check the cache first, if that fails look on the network. (Best for mostly static websites.)
self.addEventListener('fetch',e=>{
	e.respondWith(
		caches.match(e.request).then(response=>{
			if(response) {
				return response;
			}

			/*
			const successfulRequest = new Request('https://cors-test.appspot.com/test', {
				mode: 'cors'
			});
			*/

			return fetch(e.request);
		})
	);
});
