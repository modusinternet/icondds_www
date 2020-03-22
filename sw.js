const cacheName='v2020.03.21-03';

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
