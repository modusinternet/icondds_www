const cacheName='v2020.03.21-02';

//https://d23cij6660kk94.cloudfront.net

var cacheFiles=[
	'https://d23cij6660kk94.cloudfront.net/ccmsusr/_css/animate.min.css',
	'https://d23cij6660kk94.cloudfront.net/ccmsusr/_js/jquery-3.4.1.min.js',
	'https://d23cij6660kk94.cloudfront.net/ccmsusr/_js/jquery-validate-1.19.0.min.js',
	'https://d23cij6660kk94.cloudfront.net/ccmsusr/_js/jquery-validate-additional-methods-1.19.0.min.js',
	'https://d23cij6660kk94.cloudfront.net/ccmstpl/_css/owl.carousel-2.3.4.min.css',
	'https://d23cij6660kk94.cloudfront.net/ccmstpl/_js/owl.carousel.min.js',
	'https://d23cij6660kk94.cloudfront.net/ccmstpl/_js/jquery.mobile.custom.min.js',
	'https://d23cij6660kk94.cloudfront.net/ccmstpl/_js/modernizr-3.6.0-custom-min.js',
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
