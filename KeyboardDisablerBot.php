#!/usr/bin/env php

<?php

/* HOW DOES THIS BOT WORK?
 * This bot simply replaces other bots' keyboard
 * and then removes it. Useful when cleaning keyboards
 * in groups.
 *
 * THIS BOT USES POLLING FUNCTION
 * DEPENDENCIES: php, php-curl
 * RUN AS: php KeyboardDisablerBot.php --token="Your bot token"
 * OR: ./KeyboardDisablerBot.php --token="Your bot token"
 * 
 * Made by AryToNeX -- 2017
 */

// Edit this part before starting

const COMMAND = "/panic"; // Define your command. If you define a command without the bot's username you MUST disable privacy settings in the BotFather.
const ADMIN_MODE = false; // ADMIN MODE: Delete your message after clearing the keyboard. Requires the bot to be admin of the group.


// Omg look at my code!


$token = getopt("", array("token:"));
$token = $token["token"];
$apiUrl = "https://api.telegram.org/bot" . $token . "/";
$offset = intval(file_get_contents("last_update"));

while(true){
	$updates = getUpdates($offset + 1);

	if($updates["ok"]) foreach($updates["result"] as $data){
		
		if(isset($data["message"]["text"]) && (strtolower($data["message"]["text"]) == COMMAND)){
			$message = sendMessage("Removing", $data["message"]["chat"]["id"], $data["message"]["message_id"], array("keyboard" => array(array(array("text" => "Removing...")))));
			editMessageText("Removed", $message["result"]["message_id"], $data["message"]["chat"]["id"], array("remove_keyboard" => true));
			deleteMessage($data["message"]["chat"]["id"], $message["result"]["message_id"]);
			if(ADMIN_MODE) deleteMessage($data["message"]["chat"]["id"], $data["message"]["message_id"]);
			echo "A keyboard was removed.\n";
		}

		$offset = $data["update_id"];
		file_put_contents("last_update", $offset);

	}
}


// Telegram Functions

function getUpdates($offset){
	global $apiUrl;
	$url = $apiUrl . "getUpdates?offset=$offset";

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	$content = curl_exec($ch);
	curl_close($ch);

	return json_decode($content, true);
}

function sendMessage($message, $chatId, $reply = null, $replyMarkup = null){
	global $apiUrl;
	$message = urlencode($message);
	$url = $apiUrl . "sendMessage?chat_id=$chatId&text=$message";
	if(isset($reply)) $url .= "&reply_to_message_id=$reply";
	if(isset($replyMarkup)) $url .= "&reply_markup=" . urlencode(json_encode($replyMarkup));

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	$content = curl_exec($ch);
	curl_close($ch);

	return json_decode($content, true);
}

function editMessageText($message, $msgId, $chatId, $replyMarkup = null){
	global $apiUrl;
	$message = urlencode($message);
	$url = $apiUrl . "editMessageText?message_id=$msgId&chat_id=$chatId&text=$message";
	if(isset($replyMarkup)) $url .= "&reply_markup=" . urlencode(json_encode($replyMarkup));

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	$content = curl_exec($ch);
	curl_close($ch);

	return json_decode($content, true);
}

function deleteMessage($chatId, $messageId){
	global $apiUrl;
	$url = $apiUrl . "deleteMessage?chat_id=$chatId&message_id=$messageId";

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	$content = curl_exec($ch);
	curl_close($ch);

	return json_decode($content, true);
}
