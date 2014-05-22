<?
  include_once("./include/LTChatConfig.php");
  
  if($_POST['room'] != "")		$room = stripslashes($_POST['room']);
  else							$room = 'Arabia';

  $LTParser = new LTChatTplParser($room);
  
  echo $LTParser->get_chat();
  
?>