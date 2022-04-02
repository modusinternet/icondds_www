<p align="center">
	<img src="./ccmstpl/examples/_img/business-card.png" />
</p>
<h1 align="center">Custodian CMS</h1>

The word 'Custodian' is defined by thefreedictionary.com as 'A person entrusted with guarding or maintaining a property; caretaker' and this suits the definition of the Custodian CMS very well.

Custodian CMS (CCMS) is a good caretaker to build your site upon because:
<ol>
	<li>It combines the best security practices against techniques like hot linking, cross site scripting, remote code execution and MySQL injection in multiple areas of the system.  The .htaccess file is full of deeply scrutinized code that is constantly being improved.  Other .htaccess files found throughout its structure provide additional layers of protection.</li>
	<li>It operates under a clearly defined URI structure that is optimal for Search Engines.</li>
	<li>It provides an easy process and structure for developers to separate HTML from programming code so that its easier for junior developers and maintainers to work with after development is complete.</li>
	<li>It provides the simplest method in existence to solve the issues of constructing multilingual websites using a single set of templates, on a single domain, containing an unlimited number of languages.</li>
	<li>It simplifies the user experience by determining language preference automatically.</li>
	<li>It gets out of the way of developers to build sites the way they want, with the tools/plugins/frameworks they want, using the themes they want.</li>
</ol>

Though CCMS does not come with a finished admin system (coming in v1.0) users do already have the ability to maintain some content in an easy, safe and secure way already via the public side templates.  CCMS's programming requirements are also very minimal, containing only 5 proprietary tags, it can be maintained with a simple text editor and a tool like phpMyAdmin to add, remove or update database inserts.


About
--

CCMS is a small, light weight, Content Management System, designed to help you build multilingual websites and is distributed for free under the GNU LGPL.

The primary purpose of CCMS is to maintain a database of custom content, written and maintianed by native speakers, and make it easy to display the correct one using a single set of templates.  One website, one set of templates, many languages.  The website developer sets the default language for the site, adds support for additional languages, fills the database with individually hand crafted blobs of content and inserts CCMS_DB tags throughout the HTML to automatically replace content in the language requested by visitors.  Here is an example of the database content insertion tag used in CCMS.

	{CCMS_DB:about_us_page,first_paragraph}
	{CCMS_DB:use_anywhere,form_button_submit}
	{CCMS_DB:trips_to_mexico_template,request_more_info_text1}

If your site contains content which may need to be displayed bidirectional (BIDI) you can use the CCMS_DB_DIR tag to help output left-to-right (lrt) or right-to-left (rtl) flags to use in your HTML.

	{CCMS_DB_DIR:about_us_page,first_paragraph}
	{CCMS_DB_DIR:use_anywhere,form_button_submit}
	{CCMS_DB_DIR:trips_to_mexico_template,request_more_info_text1}

CCMS also provides a framework to help website developers build Search Engine Optimized (SEO)/friendly URIs and insert one template into another (CCMS_TPL tags) or libraries of custom code with the template they are currently working on (CCMS_LIB tags).

	{CCMS_TPL:header.html}
	{CCMS_TPL:somedir/footer.html}
	{CCMS_TPL:products/list.php}

	{CCMS_LIB:_default.php;FUNC:ccms_cfgDomain}
	{CCMS_LIB:cms/_123.php;FUNC:XyZZy123_}
	{CCMS_LIB:test/dir/indeX_Asdf-123.php;FUNC:cfgindeX_Asdf123("arg1", "arg2")}


System requirements
--

LAMP
* Linux
* Apache
* MySQL v5.5.3+
* PHP v7.3+

(CCMS will probably run on IIS but ya never know.  If someone would like to test it and let me know I'd appreciate it.)

Installation
--

You can now use a single template to download CCMS and begin the config/setup process.  Download it here: [Custodian CMS Download](https://github.com/modusinternet/Custodian-CMS-Download)

Or use the original method below to install.

* Download a CCMS package from https://github.com/modusinternet/custodian-cms/releases.
* Unpack and place the archive on your server.
* Import ccms-db-setup.sql into your MySQL editor after setting up a new database.
* Update the settings found inside of /ccmspre/config_original.php and /ccmspre/user_whiteList_original.php as required.
* Copy and or Rename /ccmspre/config_original.php to /ccmspre/config.php and /ccmspre/user_whiteList_original.php to /ccmspre/user_whiteList.php.
* Open a browser and call your test environment, if the first template that comes up says 'Custodian CMS Configuration Instructions' follow the instructions and double check your installation.  Most likely you forgot to rename the /ccmspre/config_original.php and /ccmspre/user_whiteList_original.php files described above.

Visit the project website at https://custodiancms.org (Under development) or connect with us on Discord at: https://discord.gg/AA9vrxxyAJ
