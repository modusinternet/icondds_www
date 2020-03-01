const cacheName='v1';

var cacheFiles=[
	'/ccmsusr/_js/jquery-3.3.1.min.js',
	'/ccmsusr/_js/jquery-validate-1.19.0.min.js',
	'/ccmsusr/_js/jquery-validate-additional-methods-1.19.0.min.js'
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
