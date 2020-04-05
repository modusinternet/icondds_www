// Set this to your tracking ID
var trackingId = 'UA-162341556-1';

function sendAnalyticsEvent(eventAction, eventCategory) {
	'use strict';

	console.log('Sending analytics event: ' + eventCategory + '/' + eventAction);

	if (!trackingId) {
		console.error('You need your tracking ID in analytics-helper.js');
		console.error('Add this code:\nvar trackingId = \'UA-162341556-1\';');
		// We want this to be a safe method, so avoid throwing unless absolutely necessary.
		return Promise.resolve();
	}

	if (!eventAction && !eventCategory) {
		console.warn('sendAnalyticsEvent() called with no eventAction or ' +
		'eventCategory.');
		// We want this to be a safe method, so avoid throwing unless absolutely necessary.
		return Promise.resolve();
	}

	return self.registration.pushManager.getSubscription()
	.then(function(subscription) {
		if (subscription === null) {
			throw new Error('No subscription currently available.');
		}

		// Create hit data
		var payloadData = {
			// Version Number
			v: 1,
			// Client ID
			cid: subscription.endpoint,
			// Tracking ID
			tid: trackingId,
			// Hit Type
			t: 'event',
			// Event Category
			ec: eventCategory,
			// Event Action
			ea: eventAction,
			// Event Label
			el: 'serviceworker'
		};

		// Format hit data into URI
		var payloadString = Object.keys(payloadData)
		.filter(function(analyticsKey) {
			return payloadData[analyticsKey];
		})
		.map(function(analyticsKey) {
			return analyticsKey + '=' + encodeURIComponent(payloadData[analyticsKey]);
		})
		.join('&');

		// Post to Google Analytics endpoint
		return fetch('https://www.google-analytics.com/collect', {
			method: 'post',
			body: payloadString
		});
	})
	.then(function(response) {
		if (!response.ok) {
			return response.text()
			.then(function(responseText) {
				throw new Error(
					'Bad response from Google Analytics:\n' + response.status
				);
			});
		} else {
			console.log(eventCategory + '/' + eventAction +
				'hit sent, check the Analytics dashboard');
		}
	})
	.catch(function(err) {
		console.warn('Unable to send the analytics event', err);
	});
}
