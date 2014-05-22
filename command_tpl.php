<?
  include_once("./include/LTChatConfig.php");


  $LTParser = new LTChatTplParser();

  echo $LTParser->get_command_tpl($_GET['load_template'], 
                                   unserialize(stripslashes(urldecode($_GET['other_vars']))));
?>