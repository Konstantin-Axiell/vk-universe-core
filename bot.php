<?php
/*
Made By UnFox
ДОКУМЕНТАЦИЯ - https://vkue.foxy-link.ru/doc/
*/
require("VK-UNIVERSE/vkue.php");
$options = [
'token' => "access_token",
];
$vkue = new VKUE($options);

$cmds = [
//пример таймера
'everyPool'=>[
[300, function() use ($vkue){//каждые 300 секунд выполнять фукнцию
    $vkue->api->account_setOnline();//сделать аккаунт online (получается вечный онлайн)
}, true],
],
'user_invite' => function($vkue, $msg){//Если добавили человека в беседу
    $vkue->send("Привет, ".$msg->from_id);//Отвечаем Привет, {id пользователя}
},
'messages' => //Обработка сообщений
[
    [
        'r'=>"/^Test/i",    // Если текст сообщения равен Test 
        'f'=>function($params, $vkue, $msg){
            $vkue->send("OK");  // Отвечаем OK
        }
    ], 
    [
        'r'=>"/^say (.*)/i",    // Если текст сообщения равен  say [что-либо]
        'f'=>function($params, $vkue, $msg){
            $vkue->send($params[1]);  // Отвечаем [что-либо]
        }
    ], 
], 
];
$vkue->long_poll->init($cmds);
?>