<?
 class MessagesTplParser
 {
   var $LTChartCore;
   var $language_config;
   
   var $title;

   function MessagesTplParser()
   {
   	 session_start();
     $this->LTChartCore = new LTChatCore();
   	 $this->language_config = $GLOBALS['language_config'];
   }

   function get_tpl($tpl_name, $replace = array(), $recurrent = false)
   {
   	 $replace['#title#'] = $this->title;

     $tpl_ar = file(LTChatTemplateSystemPath.'cpanel/messages/'.$tpl_name);
     if(is_array($tpl_ar))
       $tpl = implode(null, $tpl_ar);
     
     if(is_array($replace))
       $tpl = strtr($tpl, $replace);

     if(is_array($this->language_config[$tpl_name]))
	   $tpl = strtr($tpl, $this->language_config[$tpl_name]);

     return $tpl;
   }
      
   function pass_params_via_tpl($template, $params)
   {
	  $other_vars = urlencode(serialize($params));   
      return "./command_tpl.php?load_template={$template}&other_vars={$other_vars}";
   }

   function get_pm_tpl($template, $other_vars, $msgid)
   {
   
   if(!$this->LTChartCore->user_logged_in())  return LTTpl_fullhelp_desc;
   
   	 switch ($template)
   	 {

   	 	case "inbox.tpl":
   	 	{
		
		$user_name = $this->LTChartCore->get_user_name();
		$replace['#path#'] = LTChatTemplateSystemPath;
		
        $get = mysql_query("SELECT * from `pmessages` where `touser` = '{$user_name}' order by id desc") or debug(mysql_error(), "MessagesTplParser", __LINE__); 
		
		    $replace['#pm_header#'] = '';
		    $replace['#messages_zero#'] = ''; 
			
			if (mysql_num_rows($get) == 0) 
			{ 
			$replace['#messages_zero#'] = '<TR><TD colspan="5" align="center">You have 0 messages</TR>'; 
			} 
			else 
			{
				while ($messages = mysql_fetch_array($get)) 
				{ 
				$read = $messages['unread'] == 'read' ? "read" : "unread";
		//the above lines gets all the messages sent to you, and displays them with the newest ones on top 
				if ($messages[reply] == yes) 
				{ 
				$replace['#pm_header#'] .= "Reply to: "; 
				} 
		$replace['#pm_header#'] .= "<TR onmouseover=\"this.bgColor='#E6E8E3'\" onmouseout=\"this.bgColor=''\">
<TD align=right bgColor=white></TD>
<TD width=\"4%\">$read<INPUT type='checkbox' value='$messages[id]' name='del[]'></TD>
<TD>
<P align=left>&nbsp;<a href=\"messages.php?load_template=view.tpl&msgid=$messages[id]\">$messages[title]</A></P></TD>
</A></P></TD>
<TD><SPAN><FONT color=\"$levelcolor\">$messages[from]</FONT></SPAN></TD>
<TD><SPAN class=gensmall>$messages[date]</SPAN></TD></TR>"; 
				}
		
			}
	   	 	break;
   	 	}
		
	case "write.tpl": 
	{
	    $user_name = $this->LTChartCore->get_user_name();
		
		$getusers = mysql_query("SELECT * FROM `users` ORDER BY 'nick' ASC") or debug(mysql_error(), "MessagesTplParser", __LINE__); 
			while ($users = mysql_fetch_array($getusers)) { 
			  $replace['#member_list_name#'] .= "<option value=\"$users[nick]\">$users[nick]</option>"; 
			}
	   break;
	   }
	   
	case "send.tpl": 
	{
	    $user_name = $this->LTChartCore->get_user_name();
		
		if (isset($_POST['send']) && $_POST['ops']) 
		{ 
			//the form has been submitted.  Now we have to make it secure and insert it into the database 
			$ip = discoverIP();
			$subject = htmlspecialchars(addslashes("$_POST[subject]")); 
			
			$getmessage = addslashes("$_POST[message]"); 
			strip_tags($getmessage, '<p><hr><font><strong><em><u>');
			$message = advfinder($getmessage);
			
			$to = htmlspecialchars(addslashes("$_POST[ops]")); 
			
			$send = mysql_query("INSERT INTO `pmessages` 
			( `title` , `message` ,  `touser` , `from` , `unread` ,  `date`, `ip` ) 
			VALUES 
			('{$subject}', '{$message}', '{$to}',  '{$user_name}', 'unread', NOW(), '{$ip}')") or debug(mysql_error(), "MessagesTplParser", __LINE__); 
		} 
		unset($_POST['send'], $_POST['ops']);
	   break;
	   }
	   
	 case 'delete_selected.tpl' :
	 {
	 
	 $user_name = $this->LTChartCore->get_user_name();
		
		if (isset($_POST['delete']) && $_POST['del'] ) {
		        
				$sql = "DELETE FROM `pmessages` WHERE id IN ('" . implode("','", $_POST['del']) . "') AND `touser` = '$user_name'";
			  if (!mysql_query($sql)) { 
				print 'Could not delete selected messages';
			  } else { 
				$replace['#count#'] .= mysql_affected_rows();
			  }
		}
	 break;
	 }
	   
	case 'delete.tpl': 
	{
	    $user_name = $this->LTChartCore->get_user_name();
		if (!$msgid) 
		{ 
		echo ("
		<a href='messages.php?load_template=inbox.tpl'>Go Back</a><br><br>
		Sorry, but this is an invalid message.
		"); 
		} 
		else 
		{ 
		$getmsg = mysql_query("SELECT * from pmessages where id = '$msgid'") or debug(mysql_error(), "MessagesTplParser", __LINE__); 
		$msg = mysql_fetch_array($getmsg); 
		//hmm..someones trying to delete someone elses messages!  This keeps them from doing it 
		if ($msg[touser] != $user_name) 
		{ 
		echo ("
		<a href='messages.php?load_template=inbox.tpl'>Go Back</a><br><br>
		This message was not sent to you!
		"); 
		
		} 
		else 
		{ 
		$delete  = mysql_query("delete from `pmessages` where id = '$msgid'") or debug(mysql_error(), "MessagesTplParser", __LINE__); 
		echo ("
		<a href='messages.php?load_template=inbox.tpl'>Go Back</a><br><br>
		Message Deleted!
		"); 
		} 
		} 
	break; 
    }
	   	   	
		case "view.tpl":
	   	{
        $user_name = $this->LTChartCore->get_user_name();
		$replace['#path#'] = LTChatTemplateSystemPath;
		
		//the url now should look like ?page=view&msgid=# 
		if (!$msgid) 
		{ 
		//there isnt a &msgid=# in the url 
		echo ("
		<a href='messages.php?load_template=inbox.tpl'>Go Back</a><br><br>
		Invalid message!");
		} 
		else 
		{ 
		//the url is fine..so we continue... 
		$getmsg= mysql_query("SELECT * from pmessages where id = '{$msgid}'") or debug(mysql_error(), "MessagesTplParser", __LINE__); 
		$msg = mysql_fetch_array($getmsg); 
		//the above lines get the message, and put the details into an array. 
		if ($msg[touser] == $user_name) 
		{ 
		//makes sure that this message was sent to the logged in member 
		if (!$_POST[message]) 
		{ 
		//the form has not been submitted, so we display the message and the form 
		$markread = mysql_query("Update pmessages set unread = 'read' where id = '{$msgid}'") or debug(mysql_error(), "MessagesTplParser", __LINE__); 
		//this line marks the message as read. 
		$message = nl2br(stripslashes("$msg[message]")); 
		//removes slashes and converts new lines into line breaks. 
		$replace['#title#'] = $msg['title'];
		$replace['#message#'] = $message;
		$replace['#from#'] = $msg['from'];
		$replace['#date#'] = $msg['date'];
		$replace['#id#'] = $msg['id'];
		
		} 
		if ($_POST[message]) 
		{ 
		//This will send the Message to the database
		$message = htmlspecialchars(addslashes("$_POST[message]")); 
		$do = mysql_query("INSERT INTO `pmessages` ( `title` , `message` , `touser` , `from` , `unread` ,  
		`date`, `reply`) VALUES 
		('$msg[title]', '$message', '$msg[from]', '{$user_name}', 
		'unread', NOW(), 'yes')") or debug(mysql_error(), "MessagesTplParser", __LINE__); 
		echo ("
		<a href='messages.php?load_template=inbox.tpl'>Go Back</a><br><br>
		Your message has been sent"); 
		} 
		} 
		else 
		{ 
		//This keeps users from veiwing other users comments
		echo("
		<a href='messages.php?load_template=inbox.tpl'>Go Back</a><br><br>
		<b>Error</b><br />"); 
		echo ("This message was not sent to you!"); 
		}} 
		echo"
		</td>
					</tr>
		</table>
		";

			break;
	   	}	   		
	   		default:
	   		{
			    $user_name = $this->LTChartCore->get_user_name();
				$user_info = $this->LTChartCore->get_user_by_nick($user_name);
				
				if($user_info->level > 0){
			    $replace['#path#'] = LTChatTemplateSystemPath;
	   			$template = 'start.tpl';
				}else{
				$template = 'dont_access.tpl';
				}
	   		}
   	 }
   	 return $this->get_tpl($template, $replace);
   }
 }
?>