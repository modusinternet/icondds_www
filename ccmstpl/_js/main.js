{CCMS_DB_PRELOAD:all,contact-us}/* Compress using https://jscompress.com/ */

/* nav bar active selector */
navActiveArray.forEach(function(s){$("#"+s).addClass("active");});
navActiveFooterArray.forEach(function(s){$("#"+s).addClass("active");});


/* Shrink the Nav bar once we've scrolled 50px or more down the screen. */
$(window).scroll(function() {
	var scroll = $(window).scrollTop();
	if(scroll >= 155) {
		$("#logo1").attr("style","opacity:0");
		$("#logo2").attr("style","opacity:1");
		$("#headerTop").removeClass("active");
		$(".cd-nav").addClass("scrolled");
		$(".cd-header-buttons").addClass("scrolled");
		$(".cd-search").addClass("scrolled");
		/*$(".scrollToTopButton").addClass("scrollToTopButton-active");*/
	} else {
		$("#logo1").attr("style","opacity:1");
		$("#logo2").attr("style","opacity:0");
		$("#headerTop").addClass("active");
		$(".cd-nav").removeClass("scrolled");
		$(".cd-header-buttons").removeClass("scrolled");
		$(".cd-search").removeClass("scrolled");
		$(".scrollToTopButton").removeClass("scrollToTopButton-active");
	}
});


/* ---------- */
/* Navigation Code Begin */
/* ---------- */
jQuery(document).ready(function($){
	/* If you change this breakpoint in the css file, don't forget to update this value as well. */
	var MqL = 1024;
	/* Move nav element position according to window width. */
	moveNavigation();
	$(window).on('resize',function(){
		(!window.requestAnimationFrame)?setTimeout(moveNavigation,300):window.requestAnimationFrame(moveNavigation);
	});

	/* Mobile - open lateral menu clicking on the menu icon. */
	$('.cd-nav-trigger').on('click',function(event){
		event.preventDefault();
		if($('.cd-main-content').hasClass('nav-is-visible')){
			closeNav();
			$('.cd-overlay').removeClass('is-visible');
		} else {
			$(this).addClass('nav-is-visible');
			$('.cd-primary-nav').addClass('nav-is-visible');
			$('.cd-main-header').addClass('nav-is-visible');
			$('.cd-main-content').addClass('nav-is-visible').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend',function(){
				$('body').addClass('overflow-hidden');
			});
			toggleSearch('close');
			$('.cd-overlay').addClass('is-visible');
		}
	});

	/* Open search form. */
	$('.cd-search-trigger').on('click',function(event){
		event.preventDefault();
		toggleSearch();
		closeNav();
	});

	/* Close lateral menu on mobile. */
	$('.cd-overlay').on('swiperight',function(){
		if($('.cd-primary-nav').hasClass('nav-is-visible')){
			closeNav();
			$('.cd-overlay').removeClass('is-visible');
		}
	});
	$('.nav-on-left .cd-overlay').on('swipeleft',function(){
		if($('.cd-primary-nav').hasClass('nav-is-visible')){
			closeNav();
			$('.cd-overlay').removeClass('is-visible');
		}
	});
	$('.cd-overlay').on('click',function(){
		closeNav();
		toggleSearch('close');
		$('.cd-overlay').removeClass('is-visible');
	});

	/* Prevent default clicking on direct children of .cd-primary-nav. */
	$('.cd-primary-nav').children('.has-children').children('a').on('click',function(event){
		event.preventDefault();
	});
	/* Open submenu. */
	$('.has-children').children('a').on('click',function(event){
		if(!checkWindowWidth()) event.preventDefault();
		var selected = $(this);
		if(selected.next('ul').hasClass('is-hidden')){
			/* Desktop version only. */
			selected.addClass('selected').next('ul').removeClass('is-hidden').end().parent('.has-children').parent('ul').addClass('moves-out');
			selected.parent('.has-children').siblings('.has-children').children('ul').addClass('is-hidden').end().children('a').removeClass('selected');
			$('.cd-overlay').addClass('is-visible');
		} else {
			selected.removeClass('selected').next('ul').addClass('is-hidden').end().parent('.has-children').parent('ul').removeClass('moves-out');
			$('.cd-overlay').removeClass('is-visible');
		}
		toggleSearch('close');
	});

	/* Submenu items - go back link. */
	$('.go-back').on('click',function(){
		$(this).parent('ul').addClass('is-hidden').parent('.has-children').parent('ul').removeClass('moves-out');
	});

	function closeNav(){
		$('.cd-nav-trigger').removeClass('nav-is-visible');
		$('.cd-main-header').removeClass('nav-is-visible');
		$('.cd-primary-nav').removeClass('nav-is-visible');
		$('.has-children ul').addClass('is-hidden');
		$('.has-children a').removeClass('selected');
		$('.moves-out').removeClass('moves-out');
		$('.cd-main-content').removeClass('nav-is-visible').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend',function(){
			$('body').removeClass('overflow-hidden');
		});
	}

	function toggleSearch(type){
		if(type=="close"){
			/* Close serach. */
			$('.cd-search').removeClass('is-visible');
			$('.cd-search-trigger').removeClass('search-is-visible');
			$('.cd-overlay').removeClass('search-is-visible');
		} else {
			/* Toggle search visibility. */
			$('.cd-search').toggleClass('is-visible');
			$('.cd-search-trigger').toggleClass('search-is-visible');
			$('.cd-overlay').toggleClass('search-is-visible');
			if($('.cd-search').hasClass('is-visible')) $('.cd-search').find('input[type="search"]').focus();
			($('.cd-search').hasClass('is-visible'))?$('.cd-overlay').addClass('is-visible'):$('.cd-overlay').removeClass('is-visible');
		}
	}

	function checkWindowWidth(){
		/* Check window width (scrollbar included). */
		var e = window,a = 'inner';
		if(!('innerWidth' in window)){
			a = 'client';
			e = document.documentElement || document.body;
		}
		if(e[a+'Width']>=MqL){
			return true;
		} else {
			return false;
		}
	}

	function moveNavigation(){
		var navigation = $('.cd-nav');
		var desktop = checkWindowWidth();
		if(desktop){
			navigation.detach();
			navigation.insertBefore('.cd-header-buttons');
		} else {
			navigation.detach();
			navigation.insertAfter('.cd-main-content');
		}
	}
});
/* ---------- */
/* Navigation Code End */
/* ---------- */


/* ---------- */
/* Disable Loading Screen Begin */
/* ---------- */
window.setTimeout(function(){
	document.getElementById("loading_svg").style.opacity="0";
	window.setTimeout(function(){
		document.getElementById("loading_svg").style.display="none";
	},500);
},500);
window.setTimeout(function(){document.getElementsByClassName("cd-primary-nav")[0].style.opacity="1";},500);
/* ---------- */
/* Disable Loading Screen End */
/* ---------- */


/* ---------- */
/* MSG Popup Email Form Begin */
/* ---------- */
var msgContainer = document.getElementById('msg');
var svgButton = document.getElementById('msg-svg-fill');
function msg_show(){
	/*
	document.getElementById("msg-svg-fill").classList.add("hide");
	document.getElementById("msg").classList.remove("hide");
	*/
	svgButton.classList.add("hide");
	msgContainer.classList.remove("hide");
}
function msg_hide(){
	/*
	document.getElementById("msg-svg-fill").classList.remove("hide");
	document.getElementById("msg").classList.add("hide");
	*/
	svgButton.classList.remove("hide");
	msgContainer.classList.add("hide");
}
/*
msgContainer.addEventListener('click',function(e){
	if(msgContainer!==e.target&&svgButton!==e.target&&!msgContainer.contains(e.target)){
		svgButton.classList.remove("hide");
		msgContainer.classList.add("hide");
	}
});
msgContainer.addEventListener('click',function(e){
	svgButton.classList.remove("hide");
	msgContainer.classList.add("hide");
});
*/

/*document.addEventListener("click", function(a) {
	msgContainer === a.target || svgButton === a.target || msgContainer.contains(a.target) || (svgButton.classList.remove("hide"), msgContainer.classList.add("hide"))
});*/

$.validator.addMethod(
	"badCharRegex",
	function(value,element,regexp){
		var re=new RegExp(regexp);
		return this.optional(element)||re.test(value);
	},
	"Please check your input."
);
$("#msgForm").validate({
	rules:{
		msgName:{
			required:true,
			minlength:2,
			maxlength:32,
			badCharRegex:/^[^\<\>&#]+$/i
		},
		msgEmail:{
			required:true,
			email:true,
			maxlength:256
		},
		msgTextarea:{
			required:true,
			maxlength:512,
			badCharRegex:/^[^\<\>&#]+$/i
		}
	},
	messages:{
		msgName:{
			required: "{CCMS_DB:all,name-error-req}",
			minlength: "{CCMS_DB:all,name-error-minMax}",
			maxlength: "{CCMS_DB:all,name-error-minMax}",
			badCharRegex: "{CCMS_DB:all,name-error-badChar}"
		},
		msgEmail:{
			required: "{CCMS_DB:all,email-error-req}",
			maxlength: "{CCMS_DB:all,email-error-max}"
		},
		msgTextarea:{
			required: "{CCMS_DB:all,message-error-req}",
			maxlength: "{CCMS_DB:all,message-error-max}",
			badCharRegex: "{CCMS_DB:all,name-error-badChar}"
		}
	},
	errorPlacement:function($error,$element){
		var name=$element.attr("name");
		$("#error-"+name).append($error);
	},
	submitHandler:function(form){
		var request;
		/* Abort any pending request. */
		if(request) request.abort();
		var $inputs=$(form).find("input,select,textarea,button");
		var serializedData=$(form).serialize();
		/* Disable the inputs for the duration of the ajax request. */
		$inputs.prop("disabled",true);
		request=$.ajax({
			beforeSend:function(XMLHttpRequest){
				$('#msgForm .has-error').removeClass('has-error');
				$('#msgForm .help-block').html('').hide();
				$('#msgFormMessage').removeClass('alert-success').html('');
			},
			cache:false,
			data:serializedData,
			dataType:'json',
			type:"post",
			url:"/{CCMS_LIB:_default.php;FUNC:ccms_lng}/msgForm-ajax.html"
		});
		/* Called on success. */
		request.done(function(json,textStatus) {
			if(json.error){
				/* Error messages */
				if(json.error.msgName){
					$('#msgName').parent().addClass('has-error');
					$('#error-msgName').html(json.error.msgName).slideDown();
				}
				if(json.error.msgEmail){
					$('#msgEmail').parent().addClass('has-error');
					$('#error-msgEmail').html(json.error.msgEmail).slideDown();
				}
				if(json.error.msgTextarea){
					$('#msgTextarea').parent().addClass('has-error');
					$('#error-msgTextarea').html(json.error.msgTextarea).slideDown();
				}
				$inputs.prop("disabled",false);
			}
			if(json.success){
				$('#msgFormMessage').addClass('alert-success').html(json.success).slideDown();
				setTimeout(function(){
					$('#msgFormMessage').slideUp("fast",function(){
						$(this).removeClass('alert-success').html('');
					});
				},5000);
				$('#msgForm').find('.form-control').val('');
				/* reenable the inputs */
				setTimeout(function(){
					$inputs.prop("disabled",false);
				},2000);
			}
		});
		/* Called on failure. */
		request.fail(function(jqXHR,textStatus,errorThrown){
			/* log the error to the console */
			$("#msgForm").html("The following error occurred: "+textStatus,errorThrown);
		});
		/* Called if the request failed or succeeded. */
		request.always(function(){
			/* reenable the inputs */
			setTimeout(function(){
				$inputs.prop("disabled",false);
			},5000);
		});
		/* Prevent default posting of form. */
		return false;
	}
});
/* ---------- */
/* MSG Popup Email Form End */
/* ---------- */


/* ----------
Add to Home screen (A2HS) and ServiceWorker Code Begin.
https://web.dev/customize-install/#criteria
https://web.dev/codelab-make-installable/
https://developer.mozilla.org/en-US/docs/Web/Apps/Progressive/Add_to_home_screen#How_do_you_make_an_app_A2HS-ready
---------- */
let deferredPrompt;

const divInstall = document.getElementById('installContainer');
const butInstall = document.getElementById('butInstall');

window.addEventListener('beforeinstallprompt', (e) => {
	//console.log('beforeinstallprompt', 'beforeinstallprompt', e);
	// Prevent Chrome 76 and later from showing the mini-infobar
	e.preventDefault();
	// Stash the event so it can be triggered later.
	//window.deferredPrompt = e;
	deferredPrompt = e;
	// Remove the 'hidden' class from the install button container
	divInstall.classList.toggle('hidden', false);

	butInstall.addEventListener('click', (e) => {
		// hide our user interface that shows our A2HS button
		//btnAdd.style.display = 'none';
		divInstall.classList.toggle('hidden', true);
		// Show the prompt
		deferredPrompt.prompt();
		// Wait for the user to respond to the prompt
		deferredPrompt.userChoice.then((choiceResult) => {
			if (choiceResult.outcome === 'accepted') {
				console.log('User accepted the A2HS prompt');
			} else {
				console.log('User dismissed the A2HS prompt');
			}
			deferredPrompt = null;
		});
	});
});

window.addEventListener('appinstalled', (event) => {
	console.log('appinstalled', 'appinstalled', event);
});
/* ----------
Add to Home screen (A2HS) and ServiceWorker Code End.
https://web.dev/customize-install/#criteria
https://web.dev/codelab-make-installable/
https://developer.mozilla.org/en-US/docs/Web/Apps/Progressive/Add_to_home_screen#How_do_you_make_an_app_A2HS-ready
---------- */
