<?

function connect()
{
 
  $db_host = "localhost";
  $db_user = "root";
  $db_password = "";
  $db_name = "chat";

  $link = mysql_connect($db_host,$db_user,$db_password);// or die("fuck! polaczenie z baza lezy!!!");
  mysql_select_db($db_name) or die(mysql_error());

  return $link;
}
?>