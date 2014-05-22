<? 

include_once("./include/LTChatConfig.php");

	 $MessagesTplParser = new MessagesTplParser();
	   
echo $MessagesTplParser->get_pm_tpl($_GET['load_template'], 
                                    unserialize(stripslashes(urldecode($_GET['other_vars']))),
									$_GET['msgid']);

?>