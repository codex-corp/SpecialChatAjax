<?
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `signup` (
	`userid` int(11) auto_increment,
	`username` VARCHAR(255),
	`password` VARCHAR(255),
	`firstname` VARCHAR(255),
	`lastname` VARCHAR(255),
	`emailaddress` VARCHAR(255), 
	PRIMARY KEY  (`userid`)
	)ENGINE=MyISAM DEFAULT CHARSET=latin1;
*/


class User
{
	var $userId;
	var $userName;
	var $password;
	var $firstName;
	var $emailAddress;
	var $auto;
	var $LTChatDataKeeper;
		
function createPassword($length) {
	$chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$i = 0;
	$password = "";
	while ($i <= $length) {
		$password .= $chars{mt_rand(0,strlen($chars))};
		$i++;
	}
	return $password;
}
	
	function User($userName='', $auto='', $password='', $firstName='', $emailAddress='')
	{
		
		$this->userName = $userName;
		if(!empty($this->password)){
		//password
		$this->password = $password;
		}else{
		//generate
		$this->password = $this->createPassword(8);
		}
		$this->firstName = $firstName;
		$this->emailAddress = $emailAddress;
	}

	/**
	* Saves the object to the database
	* @return integer $userId
	*/
	function Save()
	{
		$a = mysql_query("select userid from `signup` where `userid`='".$this->userId."' LIMIT 1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		if (mysql_num_rows($a) > 0)
		{
			$query = "update `signup` set 
			`username`='".$this->userName."', 
			`password`='".$this->password."', 
			`firstname`='".$this->firstName."', 
			`emailaddress`='".$this->emailAddress."' where `userid`='".$this->userId."'";
		}
		else
		{
			$query = "insert into `signup` (`username`, `password`, `firstname`, `emailaddress` ) values (
			'".$this->userName."', 
			'".$this->password."', 
			'".$this->firstName."', 
			'".$this->emailAddress."' )";
		}
		$result = mysql_query($query);
		if ($this->userId == "")
		{
			$this->userId = intval(mysql_insert_id());
		}
		return $this->userId;
	}
	
	
	/**
	* Clones the object and saves it to the database
	* @return integer $userId
	*/
	function SaveNew()
	{
		$this->userId='';
		return $this->Save();
	}
	
	
	/**
	* Deletes the object from the database
	* @return boolean
	*/
	function delete_request($id)
	{
		$query = "delete from `signup` where `userid`='".$id."'";
		return mysql_query($query);
	}
	
	function active_request($id){
	
	  $result = mysql_query("select * from `signup` where `userid`='".$id."' LIMIT 1");
	  
	  if($row = mysql_fetch_object($result)){
	     $get_username = $row->username;
		 $get_password = $row->password;
		 $get_emailaddress = $row->emailaddress;
		 $get_password = $row->password;
	  }
	  
	  if(mysql_num_rows($result) == 1){
		  $rights = "Trial op";
		  $level = "1";
		  
		  //if((boolean)get_ConfVar("LTChat_md5_passwords"))  $mypassword = md5($get_password);
		  //$mypassword = addslashes($get_password);
		  
		  $mypassword = md5($get_password);
		  
		  $rand_color = array("#9900FF","#FF0000","#CC3366","#0099CC","#6600FF","#0000FF","#009900","#660000","#FF9900","#FF00CC","#FF3399","#FF66FF", "FF99FF","#CC9900","#0033FF","#CC6666","#9966FF","#000000","#003366","#339999","#CC66FF","#330099","#990099","#3366FF","#000033","#CC9999","#663300","#996666","#FFCCCC");
		  
		  //Rand color array for give user 'color'
		  $result = array_rand($rand_color);
		  
		  //Rand color array for give user 'nickcolor'
		  $result2 = array_rand($rand_color);
	
		   mysql_query("INSERT INTO `".LTChat_Main_prefix."users` 
		   (nick, password, registered, rights, chat_id, color, nickcolor, font, nickfont, level, email) 
		   values 
		   ('{$get_username}', '{$mypassword}','".time()."','{$rights}','".LTChat_Main_CHAT_ID."','{$rand_color[$result]}','{$rand_color[$result2]}','Tahoma','Tahoma', '{$level}', '{$get_emailaddress}')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		   
		   email_send($get_username, $get_password, $get_emailaddress);
		   //deleted req after create
		   if(mysql_affected_rows() == 1){
		   $this->delete_request($id);
		   }
	   }
	}	
	
	function active_member(){
		
		$query = "delete from `signup` where `userid`='".$this->userId."'";
		$result = mysql_query($query);
		if (mysql_num_rows($result) > 0)
		{
			$query = "update `signup` set 
			`username`='".$this->userName."', 
			`password`='".$this->password."', 
			`firstname`='".$this->firstName."', 
			`emailaddress`='".$this->emailAddress."' where `userid`='".$this->userId."'";
		}
	
	}
	
	function show_signup(){
		
		$query = mysql_query("select * from `signup`");
		$output .= '<table border=1 style="border:solid 1px #999999; border-collapse:collapse" width="100%"><tr>
		<td width="8">#</td>
		<td width="175">username</td>
		<td width="199">First name</td>
		<td width="198">Email address</td>
		<td width="90">delete</td>
		<td width="89">active</td>
	</tr>
	<tr>';
	$i = 0;
    while($row = mysql_fetch_object($query)){
	
		$output .= "<td width=\"30\"> $row->userid </td>
		<td width=\"175\">$row->username</td>
		<td width=\"199\">$row->firstname</td>
		<td width=\"198\">$row->emailaddress</td>
		<td width=\"90\"><a href=\"./command_tpl.php?load_template=command_register.tpl&other_vars=N%3B&action=delete&id=$row->userid\">delete</a></td>
		<td width=\"89\"><a href=\"./command_tpl.php?load_template=command_register.tpl&other_vars=N%3B&action=active&id=$row->userid\">active</a></td>";
 
	if($i = 2){
	$output .= "<tr>";
	}

    $i++;
    }
    $output .= "</tr></table>";
	
	switch ($_GET['action']){
	
	case "delete":
	$this->delete_request($_GET['id']);
	break;
	
	case "active":
	$this->active_request($_GET['id']);
	break;
		
	}
	return $output;
	
	}
	
}
?>