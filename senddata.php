<?
  include_once("./include/LTChatConfig.php");
  //get convert class
  require_once('./convert/ConvertCharset.class.php');
  
  $chat = new LTChatCore(); 
  
  $FromCharset = 'UTF-8';
  $ToCharset = 'windows-1256';
  
  $NewEncoding = new ConvertCharset;
  $NewMsgOutput = $NewEncoding->Convert($_POST['msg'], $FromCharset, $ToCharset, $Entities);

  $chat->post_msg(stripslashes(str_replace("#and#","&", $NewMsgOutput)),
                  stripslashes($_POST['room']), 
				  $_POST['private_id'],
				  $_POST['private_id_checked']
				  );
?>