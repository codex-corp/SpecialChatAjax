<?php

  	 function load_internal_letters_emoticons($text)
  	 {
  	   //Load emoticons from file link (*g* *b*)
  	   if(file_exists(LTChatTemplateSystemPath."tpl_emoticons.txt"))
  	   {
		  $tpl_emoticons = file(LTChatTemplateSystemPath."tpl_emoticons.txt");
		  foreach ($tpl_emoticons as $line)
		  {
		  	$lines = explode("\t",$line);
		  	if(count($lines) <2) continue;

		  	$from = htmlspecialchars($lines[0]);
		  	$letter[$from] = str_replace("#path#","./emoticons/smiles/".$lines[count($lines)-1],LTChat_emotStyle);
		  }
  	   }
  	   return $out = strtr($text, $letter);
  	 }

	function load_internal_emoticons($MSG) {	
		$i = 9;
		while ( $i != 1086 ) {
		$MSG = preg_replace ("/\*($i)\*/", "<img src=\"./emoticons/smiles/icon$1.gif\" title='$1' border='0' align='absmiddle'>", $MSG);
			$i++;
		}
		return $MSG;
	}

	function ConfigVars($category, $var_name, $var_desc, $type)
	{
		global $ConfigVarsInfo;
		
		$ConfigVarsInfo[$var_name]['category'] = $category;
		$ConfigVarsInfo[$var_name]['description'] = $var_desc;
		$ConfigVarsInfo[$var_name]['type'] = $type;
	}

	function debug($var, $class = null, $line = null)
	{
		$v = var_export($var, true);
		$f = fopen("_debug.txt","a");
		
		fwrite($f,"{$class} ($line)\n".stripslashes($v)."\n\n");
		fclose($f);
	}

	function get_ConfVar($var_name)
	{
		if(isset($GLOBALS['LTChatConfig'][$var_name]))
		  return $GLOBALS['LTChatConfig'][$var_name];
		elseif(defined($var_name)) 
		  return constant($var_name);
		else 
		  return NULL;
	}
	
	/**
	* checkEmailAddress
	*
	* @param	string	Email address
	* @return	mixed
	* @since	2.0
	*/
	function checkEmailAddress($email = "")
	{
		$email = trim($email);
		
		$email = str_replace( " ", "", $email );
		
		//-----------------------------------------
		// Check for more than 1 @ symbol
		//-----------------------------------------
		
		if ( substr_count( $email, '@' ) > 1 )
		{
			return FALSE;
		}
		
    	$email = preg_replace( "#[\;\#\n\r\*\'\"<>&\%\!\(\)\{\}\[\]\?\\/\s]#", "", $email );
    	
    	if ( preg_match( "/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/", $email) )
    	{
    		return $email;
    	}
    	else
    	{
    		return FALSE;
    	}
	}
	
	function email_send($name, $password, $email)
	{
	
	# -=-=-=- MIME BOUNDARY
	$mime_boundary = "----SpecialChat.NeT----".md5(time());
	# -=-=-=- MAIL HEADERS
	$to = "$email";
	$subject = "Your account active now in SpecialChat.net";
	
	$headers = "From: SpecialChat <mailer@specialchat.net>\n";
	$headers .= "Reply-To: $name <$email>\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
	
	# -=-=-=- HTML EMAIL PART
	 
	$message .= "--$mime_boundary\n";
	$message .= "Content-Type: text/html; charset=UTF-8\n";
	$message .= "Content-Transfer-Encoding: 8bit\n\n";
	
	$message .= "<html>\n";
	$message .= "<body style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n"; 
	$message .= "<br>\n";
	$message .= "This is a notice for new account in SpecialChat.net .<br>\n";
	$message .= "Please Mr/Miss $name save this information in you computer and delete this email.<br>\n\n";
	$message .= "Your nick name : $name  <br>\n\n";
	$message .= "Your Password: $password <br>\n\n";
	$message .= "Your account active now !<br>\n\n";
	$message .= "Best regards from Specialchat.net admin";
	$message .= "</body>\n";
	$message .= "</html>\n";
		
	# -=-=-=- FINAL BOUNDARY
	
	$message .= "--$mime_boundary--\n\n";
	
	# -=-=-=- SEND MAIL
	
	$mail_sent = @mail( $to, $subject, $message, $headers );
	return $mail_sent ? "Mail sent" : "Mail failed";
	}
	
	
	function new_password($name, $new_password, $email)
	{
	# -=-=-=- MIME BOUNDARY
	$mime_boundary = "----SpecialChat.NeT----".md5(time());
	# -=-=-=- MAIL HEADERS
	$to = "$email";
	$subject = "New Issued Password!";
	
	$headers = "From: SpecialChat <mailer@specialchat.net>\n";
	$headers .= "Reply-To: $name <$email>\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
	
	# -=-=-=- HTML EMAIL PART
	 
	$message .= "--$mime_boundary\n";
	$message .= "Content-Type: text/html; charset=UTF-8\n";
	$message .= "Content-Transfer-Encoding: 8bit\n\n";
		
	$message .= "<html>\n";
	$message .= "<body style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n"; 
	$message .= "<br>\n";
	$message .= "This is a notice for new password to your account in SpecialChat.net .<br>\n";
	$message .= "Please Mr/Miss $name save this information in you computer and delete this email.<br>\n\n";
	$message .= "Your nick name : $name  <br>\n\n";
	$message .= "Your new Password: $password <br>\n\n";
	$message .= "Best regards from Specialchat.net admin";
	$message .= "</body>\n";
	$message .= "</html>\n";
	
	# -=-=-=- FINAL BOUNDARY
	
	$message .= "--$mime_boundary--\n\n";
	
	# -=-=-=- SEND MAIL
	
	$mail_sent = @mail( $to, $subject, $message, $headers );
	return $mail_sent ? "Mail sent" : "Mail failed";
	}
	
	function new_upgrade_downgrade($name, $new_level, $email, $status)
	{
	# -=-=-=- MIME BOUNDARY
	$mime_boundary = "----SpecialChat.NeT----".md5(time());
	# -=-=-=- MAIL HEADERS
	$to = "$email";
	$subject = "You Have New $status!";
	
	$headers = "From: SpecialChat <mailer@specialchat.net>\n";
	$headers .= "Reply-To: $name <$email>\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
	
	# -=-=-=- HTML EMAIL PART
	 
	$message .= "--$mime_boundary\n";
	$message .= "Content-Type: text/html; charset=UTF-8\n";
	$message .= "Content-Transfer-Encoding: 8bit\n\n";
		
	$message .= "<html>\n";
	$message .= "<body style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n"; 
	$message .= "<br>\n";
	$message .= "This is a notice for new <b>$status</b> to your account in SpecialChat.net .<br>\n";
	$message .= "Please Mr/Miss $name save this information in you computer and delete this email.<br>\n\n";
	$message .= "Your nick name : $name  <br>\n\n";
	$message .= "Your new level: $new_level <br>\n\n";
	$message .= "Best regards from Specialchat.net admin";
	$message .= "</body>\n";
	$message .= "</html>\n";
	
	# -=-=-=- FINAL BOUNDARY
	
	$message .= "--$mime_boundary--\n\n";
	
	# -=-=-=- SEND MAIL
	
	$mail_sent = @mail( $to, $subject, $message, $headers );
	return $mail_sent ? "Mail sent" : "Mail failed";
	}


	function download_file($file_name) {
		
	//Select ext..
	$live_playing_type= '.html';
	
	// We'll be outputting a video
	header('Content-type: application/' . $live_playing_type);
	
	// It will be called videoname.extention
	header('Content-Disposition: attachment; filename="'. $file_name . $live_playing_type . '"');
	
	$go = './loggers/saved/$file_name.html'	;
	// The song source
	readfile($go);
	
	} //end function download video

	// Obviously, add to a log
	function add($file,$user,$text){
		
			// Prepare the message (remove \')
			$text = eregi_replace("\\\\'","'",$text);
			$time = time();
			$log_file = ROOT_LOGS_PATH."saved/$file.html";
			$fh = fopen($log_file,"a");
			flock($fh,2);
			fwrite($fh,"$user$text\n");
			flock($fh,3);
			fclose($fh);
		
	}

	// add forward to a log
	function addforward($file,$user,$text){
		
			// Prepare the message (remove \')
			$text = eregi_replace("\\\\'","'",$text);
			$time = time();
			$log_file = ROOT_LOGS_PATH."$file.html";
			$fh = fopen($log_file,"a");
			flock($fh,2);
			fwrite($fh,"$user$text\n");
			flock($fh,3);
			fclose($fh);
		
	}
	
	// add forward to a log
	function addmsg($file,$user,$text){
		
			// Prepare the message (remove \')
			$text = eregi_replace("\\\\'","'",$text);
			$time = time();
			$log_file = ROOT_LOGS_PATH."$file.html";
			$fh = fopen($log_file,"a");
			flock($fh,2);
			fwrite($fh,"$user$text\n");
			flock($fh,3);
			fclose($fh);
		
	}

	function check_banned($BannedIP) {
	
		if ( preg_match("/$BannedIP/",$_SERVER['REMOTE_ADDR']) or preg_match("/$BannedIP/",$_SERVER['HTTP_X_FORWARDED_FOR']) ) {
			return true;
		}else{
			return false;
		}
	}

	function is_ip($ip) 
	 {  
	if (preg_match("/^(\d+\.?)+$/", $ip)) {
		$valid = TRUE; 
			foreach(explode(".", $ip) as $block) 
			 { 
			  if( $block<0 || $block>255 ) 
			   {            
				$valid = FALSE; 
			   } 
			 } 
		}
	  else 
	   { 
		$valid = FALSE; 
	   } 
	  return $valid; 
	 }

	function discoverPCIP()
	{
		if (isset($_SERVER))
		{
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			elseif(isset($_SERVER["HTTP_CLIENT_IP"]))
				$ip = $_SERVER["HTTP_CLIENT_IP"];
			else
				$ip = 'NONE';
		}
		else
		{
			if (getenv('HTTP_X_FORWARDED_FOR'))
				$ip = getenv('HTTP_X_FORWARDED_FOR');
			elseif (getenv('HTTP_CLIENT_IP'))
				$ip = getenv('HTTP_CLIENT_IP');
			else
				$ip = 'NONE';
		}
		
		return $ip;
	}
	
	function discoverIP()
	{
		if (isset($_SERVER))
		{
			if (isset($_SERVER["REMOTE_ADDR"]))
				$ip = $_SERVER["REMOTE_ADDR"];
			else
				$ip = 'NONE';
		}
		else
		{
			if (getenv('REMOTE_ADDR'))
				$ip = getenv('REMOTE_ADDR');
			else
				$ip = 'NONE';
		}
		
		return $ip;
	}

	function close_tags($chat)
	{
	
	  $to_close = array("b","i","a","font","MARQUEE","u");
	
	  foreach ($to_close as $tag){
	
	   $o_count = count(spliti("<$tag", $chat));
	   $c_count = count(spliti("</$tag",$chat));
	
	   while ($c_count < $o_count){ $chat .= "</$tag>"; $c_count++; }
	  }
	
	  return $chat;
	}
	
	function html2txt($document){
	$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
				   '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
				   '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
				   '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
	);
	$text = preg_replace($search, '', $document);
	return $text;
	}

	function advfinder($text)
	{
			if ($text) {
				$MESSAGE = $text;
				$Sites = array(
							   'syriantalk',
							   'arabtalk'
							   );
				$length = strlen($MESSAGE);	
				foreach ($Sites as $Site_Value) {
					$Array	 = str_split($Site_Value);		
					$Site_Name = implode('.*',$Array);
					$MESSAGE = eregi_replace("$Site_Name",'ADV',$MESSAGE);
				}
			
				if ( strlen($MESSAGE) != $length ) {
					$text = "ADV Found";
				}
				return $text;
			}
	}

	function rain($msg){
	
	$colors = array("000000", "121111", "3C3B3B", "656464", "7A7878", "8E8C8C", "ABA9A9", "C8C5C5", "D0B9B9", "CFA2A2", "D19090", "D17A7A", "D06565", "D04E4E", "D13838", "D11E1E", "D20C0C", "E90404", "FE0303", "D95B06", "D97406", "D87E1C", "DA8932", "DB954A", "DA9F5F", "D8A670", "D9B084", "D8BC9D", "F0C89D", "F2BE86", "F4C97C", "F6C165", "F5B74B", "F7B238", "FAAC23", "FBA40A", "E7DF06", "EAE42B", "E7DF06", "FBA40A", "FAAC23", "F7B238", "F5B74B", "F6C165", "F4C97C", "F2BE86", "F0C89D", "D8BC9D", "D9B084", "D8A670", "DA9F5F", "DB954A", "DA8932", "D87E1C", "D97406", "D95B06", "FE0303", "E90404", "D20C0C", "D11E1E", "D13838", "D04E4E", "D06565", "D17A7A", "D19090", "CFA2A2", "D0B9B9", "C8C5C5", "ABA9A9", "8E8C8C", "7A7878", "656464", "3C3B3B", "121111",'FF0000','ABCDE4');
	
	$i = 0;
	
	$msg_split = str_split($msg);
	
	foreach ( $msg_split as $X ) {
		if ( $i > count($colors) ) {
			$i = 0;
		}
		$go .= "<font color=$colors[$i]>$X</font>";
		$i++;
	}
	
	return $go;
	
	}

	function last_login($offset)
	{
	$second = 1;
	$minute = $second*60;
	$hour = $minute*60;
	$day = $hour*24;
	$week = $day*7;
	
	$time = time();
	
	$difference = $time-$offset;
	
	$wcount = 0;
	for($wcount = 0; $difference>$week; $wcount++) {
	$difference = $difference - $week;
	}
	$dcount = 0;
	for($dcount = 0; $difference>$day; $dcount++) {
	$difference = $difference - $day;
	}
	$hcount = 0;
	for($hcount = 0; $difference>$hour; $hcount++) {
	$difference = $difference - $hour;
	}
	$mcount = 0;
	for($mcount = 0; $difference>$minute; 
	$mcount++) {
	$difference = $difference - $minute;
	}
	 
	$output = "Hours $hcount Minutes $mcount Seconds $difference";
	
	return $output;
	}
	
function get_date_ex($date, $min, $hours, $days, $week){

			$diff = time() - $date;
			
			if ($min && $diff < 3600 )
			{
				if ( $diff < 120 )
				{
					return 'time_less_minute'; //time_less_minute
				}
				else
				{
					return intval($diff / 60); //time_minutes_ago
				}
			}
			else if ($min && $diff < 7200 )
			{
				return 'time_less_hour'; //time_less_hour
			}
			else if ($hours && $diff < 86400 )
			{
				return intval($diff / 3600); //time_hours_ago
			}
			else if ($days && $diff < 172800 )
			{
				return 'time_less_day'; //time_less_day
			}
			else if ($days && $diff < 604800 )
			{
				return intval($diff / 86400); //time_days_ago
			}
			else if ($week && $diff < 1209600 )
			{
				return 'time_less_week'; //time_less_week
			}
			else if ($week && $diff < 3024000 )
			{
				return intval($diff / 604900); //time_weeks_ago
			}
}
	
	function get_date ($t) {
		$s = time() - $t;
		if ($s<60) return 'moments ago';
		$m = $s/60;
		if ($m<60) return floor($m) . ' minutes ago';
		$h = $m/60;
		if ($h<24) return floor($h) . ' hours ago';
		$d = $h/24;
		if ($d<2) return 'Yesterday, ' . date('h:iA',$t);
		if ($d<=7) return floor($d) . ' days ago';
		return date("M jS, Y",$t);
	}
?>