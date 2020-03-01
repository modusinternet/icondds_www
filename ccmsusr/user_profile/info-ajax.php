<?php
header("Content-Type: text/html; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once $_SERVER["DOCUMENT_ROOT"] . "/ccmspre/config.php";
if (!strstr($_SERVER["HTTP_REFERER"], $CFG["DOMAIN"])) {
    exit('No direct script access allowed');
}

if ($CLEAN["firstname"] == "MAXLEN") {
    $error_message = "'Firstname' field exceeded its maximum number of 64 character.";
} elseif ($CLEAN["firstname"] == "INVAL") {
    $error_message = "'Firstname' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif ($CLEAN["lastname"] == "MAXLEN") {
    $error_message = "'Lastname' field exceeded its maximum number of 64 character.";
} elseif ($CLEAN["lastname"] == "INVAL") {
    $error_message = "'Lastname' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif ($CLEAN["alias"] == "") {
    $error_message = "'Alias' field missing content.";
} elseif ($CLEAN["alias"] == "MAXLEN") {
    $error_message = "'Alias' field exceeded its maximum number of 32 character.";
} elseif ($CLEAN["alias"] == "INVAL") {
    $error_message = "'Alias' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif ($CLEAN["position"] == "MAXLEN") {
    $error_message = "'Position' field exceeded its maximum number of 128 character.";
} elseif ($CLEAN["position"] == "INVAL") {
    $error_message = "'Position' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif ($CLEAN["address1"] == "MAXLEN") {
    $error_message = "'Address Line 1' field exceeded its maximum number of 128 character.";
} elseif ($CLEAN["address1"] == "INVAL") {
    $error_message = "'Address Line 1' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif ($CLEAN["address2"] == "MAXLEN") {
    $error_message = "'Address Line 2' field exceeded its maximum number of 128 character.";
} elseif ($CLEAN["address2"] == "INVAL") {
    $error_message = "'Address Line 2' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif ($CLEAN["prov_state"] == "MAXLEN") {
    $error_message = "'Prov/State' field exceeded its maximum number of 32 character.";
} elseif ($CLEAN["prov_state"] == "INVAL") {
    $error_message = "'Prov/State' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif ($CLEAN["country"] == "MAXLEN") {
    $error_message = "'Country' field exceeded its maximum number of 64 character.";
} elseif ($CLEAN["country"] == "INVAL") {
    $error_message = "'Country' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif ($CLEAN["post_zip"] == "MAXLEN") {
    $error_message = "'Postal/Zip Code' field exceeded its maximum number of 32 character.";
} elseif ($CLEAN["post_zip"] == "INVAL") {
    $error_message = "'Postal/Zip Code' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif ($CLEAN["email"] == "") {
    $error_message = "'Email' field missing content.";
} elseif ($CLEAN["email"] == "MAXLEN") {
    $error_message = "Please try to keep your 'Email' address to 255 characters or less.";
} elseif ($CLEAN["email"] == "INVAL") {
    $error_message = "Please enter a valid 'Email' address.";

} elseif ($CLEAN["phone1"] == "MAXLEN") {
    $error_message = "'Phone #1' field exceeded its maximum number of 64 character.";
} elseif ($CLEAN["phone1"] == "INVAL") {
    $error_message = "'Phone #1' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif ($CLEAN["phone2"] == "MAXLEN") {
    $error_message = "'Phone #2' field exceeded its maximum number of 64 character.";
} elseif ($CLEAN["phone2"] == "INVAL") {
    $error_message = "'Phone #2' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif ($CLEAN["skype"] == "MAXLEN") {
    $error_message = "'Skype' field exceeded its maximum number of 32 character.";
} elseif ($CLEAN["skype"] == "INVAL") {
    $error_message = "'Skype' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif ($CLEAN["facebook"] == "MAXLEN") {
    $error_message = "'Facebook' field exceeded its maximum number of 128 character.";
} elseif ($CLEAN["facebook"] == "INVAL") {
    $error_message = "'Facebook' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";

} elseif ($CLEAN["note"] == "MAXLEN") {
    $error_message = "'Note' field exceeded its maximum number of 1024 character.";
} elseif ($CLEAN["note"] == "INVAL") {
    $error_message = "'Note' field contains invalid characters.  The following characters are not permitted in this field. ( > < & # )";
}

if (!$error_message) {
    $qry = $CFG["DBH"]->prepare("UPDATE `ccms_user` SET `firstname` = :firstname, `lastname` = :lastname, `alias` = :alias, `position` = :position, `address1` = :address1, `address2` = :address2, `prov_state` = :prov_state, `country` = :country, `post_zip` = :post_zip, `email` = :email, `phone1` = :phone1, `phone2` = :phone2, `skype` = :skype, `facebook` = :facebook, `note` = :note WHERE `id` = :id LIMIT 1;");
    $qry->execute(array(':firstname' => $CLEAN["firstname"], ':lastname' => $CLEAN["lastname"], ':alias' => $CLEAN["alias"], ':position' => $CLEAN["position"], ':address1' => $CLEAN["address1"], ':address2' => $CLEAN["address2"], ':prov_state' => $CLEAN["prov_state"], ':country' => $CLEAN["country"], ':post_zip' => $CLEAN["post_zip"], ':email' => $CLEAN["email"], ':phone1' => $CLEAN["phone1"], ':phone2' => $CLEAN["phone2"], ':skype' => $CLEAN["skype"], ':facebook' => $CLEAN["facebook"], ':note' => $CLEAN["note"], ':id' => $CLEAN["SESSION"]["user_id"] ));
    echo "1";
} else {
    echo '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true" style="margin-right: 10px;"></span>'.$error_message;
}
