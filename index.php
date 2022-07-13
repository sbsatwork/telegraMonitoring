<?php

include "./config.php";

############### Program code #######################

foreach ($urlsOfTelegramChannels as $urlToCheck) {

    //Reset variables
    $emailBody = "";
    $html = "";

    // Open channel and get HTML
    $html = openURLandReturnHTML($urlToCheck);
    
    // Check and translate content (if activated)
    buildDomAndCheckContent($html);

    // Send out info via email
    sendEmailToRecipient($urlToCheck);
}

############### Functions #######################

function openURLandReturnHTML($urlOfTelegramChannelFunc){

	global $status;

	$c = curl_init($urlOfTelegramChannelFunc);

	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

	$htmlOfCURL = curl_exec($c);

	if (curl_error($c))
	    die(curl_error($c));

	// Check if the CURL gets a 200 OK Status. If not, stop the script.
	$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
	curl_close($c);

	if ($status != "200") {
		echo "Error. HTTP Status Code: ".$status;
	}else{
		return $htmlOfCURL;
	}

}

function buildDomAndCheckContent($htmlFunc){

	global $makeTranslation, $deepLAuthKey, $deepLTargetLang, $emailBody;

	$dom = new DomDocument();
	$dom->loadHTML($htmlFunc);
	$classname = 'tgme_widget_message_wrap js-widget_message_wrap';
	$finder = new DomXPath($dom);
	$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]"); // returns an DOMNodeList

    foreach ($nodes as $node) {

        $nodeSender = $finder->evaluate('string(.//*[@class="tgme_widget_message_owner_name"][1])', $node);
        echo $nodeSender, '<br/>';
        
        $nodeContent = $finder->evaluate('string(.//*[@class="tgme_widget_message_text js-message_text"][1])', $node);
        
        if ($makeTranslation){
            $nodeContent = myTranslationFunc($nodeContent);
        }
        $nodeContent = nl2br($nodeContent);
        echo $nodeContent, '<br/>';
        
        $photoContent = $finder->evaluate('string(.//*[@class="tgme_widget_message_photo_wrap"][1])', $node);
        echo $photoContent, '<br/>'; // not yet working

        $nodeViews = $finder->evaluate('string(.//*[@class="tgme_widget_message_views"][1])', $node);
        echo $nodeViews, '<br/>';
        
        $nodeDate = $finder->evaluate('string(.//*[@class="tgme_widget_message_date"][1])', $node);
        echo $nodeDate, '<br/>';

        //Set infos for email body
        $emailBody .= "Sender: ".$nodeSender."\n";
        $emailBody .= "Text: ".$nodeContent."\n";
        $emailBody .= "Views: ".$nodeViews."\n";
        $emailBody .= "Date: ".$nodeDate."\n\n";
        $emailBody .= "############################### \n\n";
	}
}

function myTranslationFunc($textToTranslate) {
	
	global $deepLAuthKey, $deepLTargetLang;

    $translatedText = "";
	
	$ch = curl_init();

	$paramsForURL = "auth_key=".$deepLAuthKey."&text=".$textToTranslate."&target_lang=".$deepLTargetLang."";

	curl_setopt($ch, CURLOPT_URL, 'https://api-free.deepl.com/v2/translate');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsForURL);

	$headers = array();
	$headers[] = 'Content-Type: application/x-www-form-urlencoded';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	    echo 'Error:' . curl_error($ch);
	}
	curl_close($ch);

	$translatedWords = json_decode($result, true); // Decode the words
	$result = $translatedWords['translations'][0]['text']; // Search the words

	return $result;
}

function sendEmailToRecipient($checkedURL) {
	global $emailRecipient, $emailBody;
	$emailBody .= "\n URL of Channel: ".$checkedURL;
	$emailSubject = "Telegram Update on Channel ".$checkedURL;
	mail($emailRecipient, $emailSubject, $emailBody);
	echo "Email sent to: ".$emailRecipient." with subject".$emailSubject."<br/><br/><br/><hr/><br/><br/><br/>";
}
?>