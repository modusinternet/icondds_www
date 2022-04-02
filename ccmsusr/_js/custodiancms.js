/* Loading Screen START */
window.setTimeout(function(){
	document.getElementById("loading_svg").style.opacity="0";
	window.setTimeout(function(){
		document.getElementById("loading_svg").style.display="none";
	},500);
},500);
window.setTimeout(function(){
	document.getElementsByTagName("main")[0].style.opacity="1";
},250);
/* Loading Screen END */


// Fade in navigation.
$("header").delay(250).animate({"opacity": "1"}, 250);


/* metisMenu START */
$(() => {
	const menu = $('#menu-ctn'),
	bars = $('.menu-bars'),
	content = $('#menu-cnt');
	let firstClick = true,
	menuClosed = true;
	let handleMenu = event => {
		if(!firstClick) {
			bars.toggleClass('crossed hamburger');
		} else {
			bars.addClass('crossed');
			firstClick = false;
		}
		menuClosed = !menuClosed;
		content.toggleClass('dropped');
		event.stopPropagation();
	};
	menu.on('click', event => {
		handleMenu(event);
	});
	$('body').not('#menu-cnt, #menu-ctn').on('click', event => {
		if(!menuClosed) handleMenu(event);
	});
	$('#menu-cnt, #menu-ctn').on('click', event => event.stopPropagation());
});

$("#menu1").metisMenu();
navActiveSub.forEach(function(nl){$("#"+nl).addClass("mm-active");});
navActiveSub.forEach(function(nl){$("#"+nl+">a").attr("aria-expanded","true");});
navActiveSub.forEach(function(nl){$("#"+nl+">a").addClass("active");});
navActiveSub.forEach(function(nl){$("#"+nl+">ul").addClass("mm-show");});
navActiveItem.forEach(function(nl){$("#"+nl+">a").addClass("active");});
/* metisMenu END */


/* w3schoolMenu START */
navActiveW3schoolsItem.forEach(function(nl){$("#"+nl).addClass("active");});
/* w3schoolMenu END */


/* Fetch Cache BEGIN */
const cachedFetch = (url, options) => {
	let expiry = 5 * 60; // 5 min default
	if(typeof options === 'number') {
		expiry = options;
		options = undefined;
	} else if(typeof options === 'object') {
		// Don't set it to 0 seconds
		expiry = options.seconds || expiry;
	}
	let cached = localStorage.getItem(url);
	let whenCached = localStorage.getItem(url + ':ts');
	if(cached !== null && whenCached !== null) {
		let age = (Date.now() - whenCached) / 1000;
		if(cached[0].errorMsg !== null || age > expiry) {
			// Clean up the old key
			localStorage.removeItem(url);
			localStorage.removeItem(url + ':ts');
		} else {
			let response = new Response(new Blob([cached]));
			return Promise.resolve(response);
		}
	}

	return fetch(url + "?token=" + Math.random() + "&ajax_flag=1", options).then(response => {
		if(response.status === 200) {
			response.clone().text().then(content => {
				localStorage.setItem(url, content);
				localStorage.setItem(url+':ts', Date.now());
			});
		}
		return response;
	});
}
/*
Combined with fetch's options object but called with a custom name
let init = {
	mode: 'same-origin',
	seconds: 3 * 60 // 3 minutes
}
cachedFetch('https://httpbin.org/get', init)
	.then(r => r.json())
	.then(info => {
		console.log('3) ********** Your origin is ' + info.origin)
	}
)

cachedFetch('https://httpbin.org/image/png')
	.then(r => r.blob())
	.then(image => {
		console.log('Image is ' + image.size + ' bytes')
	}
)
*/
/* Fetch Cache END */





/* ===== metisMenu load ===== */
/* Loads the correct sidebar on window load, collapses the sidebar on window resize. Sets the min-height of #page-wrapper to window size. */
/*
function showHideNav() {
	topOffset = 50;
	width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
	if (width < 768) {
		$('div.navbar-collapse').addClass('collapse');
		topOffset = 100; // 2-row-menu
	} else {
		$('div.navbar-collapse').removeClass('collapse');
	}

	height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
	height = height - topOffset;
	if (height < 1) height = 1;
	if (height > topOffset) {
		$("#page-wrapper").css("min-height", (height) + "px");
	}
}

$(function(){$(window).bind("load resize",function(){showHideNav();})});
showHideNav();
*/
/* ===== metisMenu load Close ===== */










/* =========== open: scroll to ID value on page ============== */
$.fn.scrollView = function () {
	return this.each(function () {
		$('html, body').animate({
			scrollTop: $(this).offset().top
		}, 1000);
	});
}
// use it like:  $('#your-div').scrollView();
/* =========== close: scroll to ID value on page ============== */
