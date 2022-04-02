<?php
/*************************************************************
References:
http://www.regular-expressions.info/unicode.html
NOTE: To match a letter including any diacritics, use \p{L}\p{M}*+.
An extensive list of regular expression examples:
http://www.roscripts.com/PHP_regular_expressions_examples-136.html

A list of predefined PHP constants for use with the filter_var() function can be found here: http://ca2.php.net/manual/en/filter.constants.php
**************************************************************/


/*************************************************************
The following is a list of types already pre defined that you can use and the regular expressions they represent.
These types are found at the top of the /ccmspre/index.php template and should not be altered there.

CRYPT							=> /^[a-z\-_\/#=&:\pN\?\.\";\'\`\*\s]*\z/i
HTTP_ACCEPT_LANGUAGE			=> /^[a-z0-9\-,;=\.]{2,}\z/i
HTTP_COOKIE						=> /^[a-z\-_=\.\pN]{1,}\z/i
LNG								=> /^[a-z]{2}(-[a-z]{2})?\z/i
PARMS							=> /^[a-z\-_\pN\/]+\z/i
QUERY_STRING					=> /^[a-z\-_=&\.\pN]{1,}\z/i
SESSION_ID						=> /^[a-z\pN]{1,}\z/i
TPL								=> /^[a-z\-\pN\/]{1,}\z/i
UTF8_STRING_WHITE				=> /^[\pL\pM*+\s]*\z/u
UTF8_STRING_DIGIT_WHITE			=> /^[\pL\pM*+\pN\s]*\z/u
UTF8_STRING_DIGIT_PUNC_WHITE	=> /^[\pL\pM*+\pN\pP\s]*\z/u
WHOLE_NUMBER					=> /^[\pN]*\z/

If you would like to add your own DEFINE's please add them here.  Remember to add a new switch statement to the USER_filter() below.
**************************************************************/


define('EXAMPLE_EXPRESSION_1', '/^[\pL\pM*+\s]{2,15}\z/u');
// ^		Start of line
// [		Start of the character class.
// \pL		Any kind of letter from any language, upper or lower case.
// \pM		Mark.  (*** A character intended to be combined with another character (e.g. accents, umlauts, enclosing boxes, etc.). ***)
// \s		Whitespaces.
// ]		End of the character class.
// {2,15}	Minimum of 2 characters, maximum of 15 characters.
// \z		End of subject or newline at end. (Better then $ because $ does not include /n characters at the end of a line.)
// /i		Pattern is treated as case-insensitive.

define('EXAMPLE_EXPRESSION_2', '/^[\pN]+\z/');
// ^ Start of line
// [ Starts the character class.
// \pN Any number.
// ] Ends the character class.
// + One or more.
// \z End of subject or newline at end. (Better then $ because $ does not include /n characters at the end of a line.)
// / End of the Pattern.


$whitelist = array(
	"example_given_name"	 => array("type" => "EXAMPLE_EXPRESSION_1",	"minlength" => 1,	"maxlength" => 15),
	"example_age"			  	=> array("type" => "EXAMPLE_EXPRESSION_2",	"maxlength" => 3),
	"example_dropdown"		=> array("type" => "EXAMPLE_EXPRESSION_3",	"options"		=> array("apple", "ball", "car", "tooth")),
);


function CCMS_Public_Filter($input, $whitelist) {
	global $CLEAN;

	foreach ($input as $key => $value) {
		if (array_key_exists($key, $whitelist)) {
			$buf = null;
			$value = @trim($value);
			// utf8_decode() converts unknown ISO-8859-1 chars to '?' for the purpose of counting.
			$length = strlen(utf8_decode($value));
			if (isset($whitelist[$key]['minlength']) && ($length < $whitelist[$key]['minlength'])) {
				$buf = "MINLEN";
			}
			if (isset($whitelist[$key]['maxlength']) && ($length > $whitelist[$key]['maxlength'])) {
				$buf = "MAXLEN";
			}
			if ($buf != "MINLEN" && $buf != "MAXLEN") {
				switch ($whitelist[$key]['type']) {


					case "EXAMPLE_EXPRESSION_1":
						$buf = (preg_match(EXAMPLE_EXPRESSION_1, $value)) ? $value : "INVAL";
						break;
					case "EXAMPLE_EXPRESSION_2":
						$buf = (preg_match(EXAMPLE_EXPRESSION_2, $value)) ? $value : "INVAL";
						break;
					case "EXAMPLE_EXPRESSION_3":
						if(is_array($value)) {
							if($whitelist[$key]['multiselect']) {
								$buf = array();
								foreach($value as $option) {
									if(in_array($option, $whitelist[$key]['options'])) {
										$buf[] = $option;
									}
								}
							}
						} else {
							$buf = in_array($value, $whitelist[$key]['options']) ? $value : "invalid";
						}
						break;


// Add your own case statements here, just copy the patter above, make the neccessary changes, save and upload.


				}
			}
			$CLEAN[$key] = $buf;
		}
	}
}
