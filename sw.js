const cacheName='v2020.03.08-01';

var cacheFiles=[
	'/ccmsusr/_css/animate.min.css',
	'/en/_css/style-ltr.css',
	'/ccmsusr/_js/jquery-3.4.1.min.js',
	'/ccmstpl/_js/jquery.mobile.custom.min.js';
	'/ccmsusr/_js/jquery-validate-1.19.0.min.js',
	'/ccmsusr/_js/jquery-validate-additional-methods-1.19.0.min.js'
	'/en/_js/main.js',
	'/ccmstpl/_js/modernizr-3.6.0-custom-min.js',
	'/ccmstpl/_css/owl.carousel-2.3.4.min.css',
	'/ccmstpl/_js/owl.carousel.min.js',
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
			return fetch(e.request);
		})
	);
});
