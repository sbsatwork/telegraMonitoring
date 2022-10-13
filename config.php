<?php
############### Configuration area #######################

// Array of all channels that should be checked
$urlsOfTelegramChannels[] = "https://t.me/s/heise_de/";
$urlsOfTelegramChannels[] = "https://t.me/s/netzpolitik_org/";

$makeTranslation = false; // Boolean true or false
$deepLAuthKey = ""; // your personal deepLAuthKey
$deepLTargetLang = "de"; // Target language (e.g. "de" for German)

// Email settings
$emailRecipient = ""; // The email address, where the report will be sent to
$emailPriority = "Normal";
$emailHeader = 'From: MONITORING@YOURDOMAIN.COM' . "\r\n" .
'Reply-To: YOURREPLY@YOURDOMAIN.COM' . "\r\n" .
'X-Mailer: PHP/' . phpversion();

// A list of keywords you are looking for in the telegram channels, e.g. your company name
$keywords[] = "Cyber";

$emailBody = "";
$html = "";

############### Configuration area #######################
?>