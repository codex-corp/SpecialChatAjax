<?
  include_once("./include/LTChatConfig.php");

  if($_GET['private_id'] == "")	  $private_id = -1;
  else 							  $private_id = (int)$_GET['private_id'];

  if($private_id < 0)
    echo "Error no private id.";
  else
  {
    $LTParser = new LTChatTplParser(null, $private_id);
    echo $LTParser->show_private_chat();
  }
?>