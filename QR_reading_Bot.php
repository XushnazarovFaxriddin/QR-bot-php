<?php
ob_start();
define('API_KEY','bot tokeni ....');
$botim = "QR_reading_Bot";
$admin = array("1092338349","1494841611");
   function del($nomi){
   array_map('unlink', glob("$nomi"));
   }
function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$mid = $message->message_id;
$cid = $message->chat->id;
$tx = $message->text;
$photo = $message->photo;

if($tx=="/start"){
bot('sendmessage',[
'chat_id'=>$cid,
    'text'=>"🧾 QR kod ni o'qish uchun rasmni yuboring!!!

📇 O'zingiz QR kod yasamoqchi bo'lsangiz  `/qr va text` shunday tartibda" ,
'parse_mode'=>'markdown',
]);
}

if($photo){
$file_id = $message->photo[0]->file_id;
$url = json_decode(file_get_contents('https://api.telegram.org/bot'.API_KEY.'/getFile?file_id='.$file_id),true);
$path=$url['result']['file_path'];
$file = 'https://api.telegram.org/file/bot'.API_KEY.'/'.$path;
$type = strtolower(strrchr($file,'.')); 
$type=str_replace('.','',$type);
if( ($type !== "png") and ($type !== "jpg")){
	bot('sendmessage',[
    'chat_id'=>$cid,
    'text'=>"Xatolik #1 -> Fayl kengaytmasi .jpg yoki .png bo'lgan rasm yuboring" ,
]);
}else{
$okey = file_put_contents("data/$cid.png",file_get_contents($file));
if($okey==false){
	bot('sendmessage',[
    'chat_id'=>$cid,
    'text'=>"Xatolik #2 -> Iltimos shu xabarni @coder_Xushnazarov ga yuboring" ,
]);
}else{
$url = "http://u2392.xvest1.ru/MyBots/QR/data/$cid.png";
$api = json_decode(file_get_contents("https://api.qrserver.com/v1/read-qr-code/?fileurl=$url"));
$text=$api[0]->symbol[0]->data;
$error=$api[0]->symbol[0]->error;
if($error==null){
bot('sendmessage',[
'chat_id'=>$cid,
    'text'=>"🔣QR KODdagi Matn:⤵️

 $text

🎯By: @$botim" ,
'parse_mode'=>'html',
]);
}else{
bot('sendmessage',[
    'chat_id'=>$cid,
    'text'=>"Xatolik #2 -> Uzur QR Kodni o'qib bo'lmadi" ,
]);
}
}
}
}
if($tx=='/qr'){
bot('sendmessage',[
'chat_id'=>$cid,
    'text'=>"QR Kod yaratish uchun 

`/qr URL` 

Shu ko'rinishda yuboring" ,
'parse_mode'=>'markdown',
]);
}
if(mb_stripos($tx,"/qr")!==false){ 
$ex=explode("/qr ",$tx); 
$text=$ex[1]; 
$api = array("http://qr-code.ir/api/qr-code?s=5&e=M&t=P&d=$text","http://api.qrserver.com/v1/create-qr-code/?data=$text"); 
bot('sendPhoto',[
'chat_id'=>$cid,
"photo"=>$api[0],
    'caption'=>"Siz kiritgan matn QR kod rasmi:  *$text*" ,
'parse_mode'=>'markdown',
]);
}
