<?php
// Domain name
$CFG["DOMAIN"] = "";

// Primary index for /ccmstpl/.
$CFG["INDEX"] = "index.html";

// Document root folder globals.
$CFG["DBH"] = NULL;
$CFG["LIBDIR"] = "ccmslib";
$CFG["PREDIR"] = "ccmspre";
$CFG["TPLDIR"] = "ccmstpl";
$CFG["USRDIR"] = "ccmsusr";

// Database globals.
$CFG["DB_HOST"] = "";
$CFG["DB_USERNAME"] = "";
$CFG["DB_PASSWORD"] = "";
$CFG["DB_NAME"] = "";

// HTML Minify.
// This code will not break pre, code or textarea tagged content.
// WARNING: Make sure your actual HTML templates do not contain any commented // code because minification means all whitespaces will be removed and the carriage return at the end of your comment will also be removed, making everything that comes after that a commented comment aswell.
// e.g.:
// $CFG["HTML_MIN"] = 0; // off (Default)
// $CFG["HTML_MIN"] = 1; // on
$CFG["HTML_MIN"] = 0;

// Caching on .html templates and their threads.
// NOTE: Does not interfear with the process of updating cookies on it's own.  To make sure a cached site setting does not break dynamically generated content call .php template and set your own headers.
// e.g.:
// $CFG["CACHE"] = 0; // off (Default)
// $CFG["CACHE"] = 1; // on
$CFG["CACHE"] = 0;

// CACHE templates in your database for how many minutes?  If $CFG["CACHE"] = 1 all .html temps generated by your web server will be cached and
// stored in your database to be served up for future page requests.  This process means the entire page does not need to be rebuilt on the fly
// which can considerably decrease the amount of time a page request returns anything to an end user.
// e.g.:
// $CFG["CACHE_EXPIRE"] = 360; // 6 hours
// $CFG["CACHE_EXPIRE"] = 12000; // 8 days (Default)
$CFG["CACHE_EXPIRE"] = 12000;

// Display debug info for failed SQL calls.  (Requires $CFG["DEBUG"] to also be enabled.)
// e.g.:
// $CFG["DEBUG_SQL"] = 0; // off
// $CFG["DEBUG_SQL"] = 1; // on
$CFG["DEBUG_SQL"] = 0;

// Log events for later analisys in the 'ccms_log' table.
// e.g.:
// $CFG["LOG_EVENTS"] = 0; // off
// $CFG["LOG_EVENTS"] = 1; // on
$CFG["LOG_EVENTS"] = 0;

// This is for deep PHP debugging error messages.
// e.g.:
// $CFG["ERROR_REPORTING"] = 0; // off
// $CFG["ERROR_REPORTING"] = 1; // on
$CFG["ERROR_REPORTING"] = 0;

// COOKIE based SESSION expire time.  Set in number of seconds.
// e.g.:
// $CFG["COOKIE_SESSION_EXPIRE"] = 1800; // 1800 seconds = 30 minutes.
// $CFG["COOKIE_SESSION_EXPIRE"] = 10800; // 10800 seconds = 3 hours.
$CFG["COOKIE_SESSION_EXPIRE"] = 1800;

// When emails are sent by the server what email address do you want them to be sent from.
$CFG["EMAIL_FROM"] = "";

// When emails are sent by the server what email address do you want them to be sent from.
$CFG["EMAIL_BOUNCES_RETURNED_TO"] = "";

// To enable Google Custom Search Engine in your error pages enter your CustomSearchControl code here.
// To get one for your site visit http://www.google.com/cse/
$CFG["GOOGLE_CUSTOM_SEARCH_ENGINE_CODE"] = "";

// To add Google reCaptcha to your web forms enter your recaptcha keys here.
// https://www.google.com/recaptcha/admin/create
// https://codeforgeek.com/google-recaptcha-v3-tutorial/
$CFG["GOOGLE_RECAPTCHA_PUBLICKEY"] = ""; // Site key
$CFG["GOOGLE_RECAPTCHA_PRIVATEKEY"] = ""; // Secret key

// To add Google Credentials so that you can embed things like maps to your site add your key here.
// https://console.cloud.google.com
$CFG["GOOGLE_CREDENTIALS_KEY"] = "";

// List of resource names and versions used throughout the site.  We use this method to maintain
// our resource versions because of the problems pushing updates to existing resources already found
// on Amazon Cloudfront servers.
//$CFG["RES"]["AWS"]                = "";
//$CFG["RES"]["CSS-01"]             = "/examples/_css/style";
$CFG["RES"]["CSS-01"]             = "/ccmstpl/examples/_css/style";
$CFG["RES"]["JQUERY"]             = "/ccmsusr/_js/jquery-3.5.1.min.js";
$CFG["RES"]["JQUERY-VALIDATE"]    = "/ccmsusr/_js/jquery-validate-1.19.0.min.js";
//$CFG["RES"]["JS-01"]              = "/examples/_js/main";
$CFG["RES"]["JS-01"]              = "/ccmstpl/examples/_js/main";
$CFG["RES"]["MODERNIZER"]         = "/ccmstpl/_js/modernizr-3.6.0-custom-min.js";
