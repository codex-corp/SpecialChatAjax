<?
  include_once("./include/LTChatConfig.php");

  session_start();
  $_SESSION = array();

  if (isset($_COOKIE[session_name()]))
    setcookie(session_name(), null, time()-42000, '/'); 
	  
	  unset($_SESSION['LTChart_user_id']);
	  session_destroy();
  
  $location = urldecode($_GET['back']);
  if($location == null)
    $location = "./";

  header("Location: {$location}");
?>
