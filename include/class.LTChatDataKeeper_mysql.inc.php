<?
/**
 * LTChatDataKeeper
 *
 * @package SpecialChat
 * @author 
 * @copyright 2008
 * @version $Id$
 * @access public
 */
 class LTChatDataKeeper
 {   
 
 var $pcip;
 var $realip;
 
  /**
   * LTChatDataKeeper::initialize()
   *
   * @return
   */
   function initialize()
   {
   	 session_start();
	 
	//geen cache
	header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
	header("Cache-Control: no-store, no-cache, must-revalidate"); 
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

   	 $db_host = LTChat_Main_dbhost;
     $db_user = LTChat_Main_dbuser;
     $db_password = LTChat_Main_dbpassword;
     $db_name = LTChat_Main_dbname;

	 $link = @mysql_connect($db_host,$db_user,$db_password) or die(mysql_error());
	 @mysql_select_db($db_name) or die(mysql_error());
   }
   //---------------------------------------------------

  /**
   * LTChatDataKeeper::set_chat_variable()
   *
   * @param mixed $var_name
   * @param mixed $var_value
   * @return
   */
   function set_chat_variable($var_name, $var_value)
   {
     $var_name = addslashes($var_name);
     $var_value = addslashes($var_value);
     mysql_query("delete from `".LTChat_Main_prefix."chat_config` where `var_name` = '{$var_name}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
     
     mysql_query("insert into `".LTChat_Main_prefix."chat_config` 
	 (var_name, var_value, chat_id) 
	 values 
	 ('{$var_name}', '{$var_value}','".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }
   
  /**
   * LTChatDataKeeper::get_chat_variables()
   *
   * @return
   */
   function get_chat_variables()
   {
   	  $out = array();
      $result = mysql_query("select * from `".LTChat_Main_prefix."chat_config` where `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

      while ($row = mysql_fetch_object($result)){
      	$out[$row->var_name] = $row->var_value;
      }
      mysql_free_result($result);
	  return $out;
   }
   //---------------------------------------------------

  /**
   * LTChatDataKeeper::user_logged_in()
   *
   * @return
   */
   function user_logged_in()
   {
       if($_SESSION['LTChart_user_id'] != NULL && $_SESSION['LTChart_user_nick'] != NULL)
         return true;
  	   else
  	     return false;
   }
   
  /**
   * LTChatDataKeeper::get_main_users_online()
   *
   * @return
   */
   function get_main_users_online()
   {
     $query_select = "SELECT `O`.`users_id`, `O`.who_id, `O`.`online`, `O`.`room`, U.`nick`, U.`id`, U.`level`, U.`posted_msg` FROM `".LTChat_Main_prefix."who_is_online` O, `".LTChat_Main_prefix."users` U WHERE `O`.online = '1' and `O`.users_id = `U`.id order by `O`.who_id asc";

		$result = mysql_query($query_select) or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	    while ($row = mysql_fetch_array($result))
	      $out[] = $row;
	  return $out;
   }
   
  /**
   * LTChatDataKeeper::get_board_statistics()
   *
   * @return
   */
   function get_board_statistics()
   {
   	 
	 $a = mysql_query("select id,nick,level,chat_id FROM `".LTChat_Main_prefix."users` where `level` > '0' and `nick` != 'Chat System' and `chat_id` = '".LTChat_Main_CHAT_ID."' order by id desc LIMIT 0,5") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 
	 if(mysql_num_rows($a) >  0){
		 while ($row = mysql_fetch_array($a)){
		   $last_members .= $row['nick'] . '<br>';
		   }
	 }
	 mysql_free_result($a);

   	 $b = mysql_query("select users_id,total_time,chat_id from `".LTChat_Main_prefix."timer` where `total_time` != '0' and `chat_id` = '".LTChat_Main_CHAT_ID."' ORDER BY `total_time` DESC LIMIT 0,5") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   	 
	 if(mysql_num_rows($b) >  0){
		 while ($row = mysql_fetch_array($b)){
		   $id = $row['users_id'];
		   $getinfo = $this->get_user_by_id($id);
			   if($getinfo != NULL){
			   $users .= $getinfo->nick . '<br>';
			   }
		   }
		   $top_members = $users;
	 }
	 mysql_free_result($b);

   	 $c = mysql_query("select nick,level,posted_msg,chat_id from `".LTChat_Main_prefix."users` where `level` > '0' and `posted_msg` != '0' and chat_id = '".LTChat_Main_CHAT_ID."' ORDER BY `posted_msg` DESC LIMIT 0,5") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   	 
	 if(mysql_num_rows($c) >  0){
		 while ($row = mysql_fetch_array($c)){
		   $nick .= $row['nick'] . '<br>';
		   }
		   $active_members = $nick;
	 }
	  mysql_free_result($c);

	    return array('last_members' => $last_members , 
		             'top_members' => $top_members,
					 'active_members' => $active_members
					 );
   }

  /**
   * LTChatDataKeeper::delete_user()
   *
   * @param mixed $id
   * @return
   */
   function delete_user($id)
   {
        mysql_query("delete from `".LTChat_Main_prefix."who_is_online` where `users_id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   
        mysql_query("delete from `".LTChat_Main_prefix."ignore` where (`from_users_id` = '{$id}' or `to_users_id` = '{$id}') and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		
	  	mysql_query("delete from `".LTChat_Main_prefix."friends` where (`from_users_id` = '{$id}' or `to_users_id` = '{$id}') and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	  	
	  	mysql_query("delete from `".LTChat_Main_prefix."users_var` where `users_id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	  	
	  	mysql_query("delete from `".LTChat_Main_prefix."users` where `id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }
   
   
  /**
   * LTChatDataKeeper::delete_user()
   *
   * @param mixed $id
   * @return
   */
   function delete_member($id)
   {
        mysql_query("delete from `".LTChat_Main_prefix."who_is_online` where `users_id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   
        mysql_query("delete from `".LTChat_Main_prefix."actions` where `users_id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		
        mysql_query("delete from `".LTChat_Main_prefix."timer` where `users_id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		
        mysql_query("delete from `".LTChat_Main_prefix."wait` where `users_id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   
        mysql_query("delete from `".LTChat_Main_prefix."ignore` where (`from_users_id` = '{$id}' or `to_users_id` = '{$id}') and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		
	  	mysql_query("delete from `".LTChat_Main_prefix."friends` where (`from_users_id` = '{$id}' or `to_users_id` = '{$id}') and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	  	
	  	mysql_query("delete from `".LTChat_Main_prefix."users_var` where `users_id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	  	
	  	mysql_query("delete from `".LTChat_Main_prefix."users` where `id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }

  /**
   * LTChatDataKeeper::login_user()
   *
   * @param mixed $login [ opreator nickname entering ] 
   * @param mixed $password [ password entering ]
   * @param mixed $guest [ guest nickname entering ]
   * @return
   */
   function login_user($login, $password, $guest)
   {
   $room = $_POST['room']; //get name of room
   $range = range(from_level, to_level); //get the range for generate the timer bar
   $hostname = addslashes(gethostbyaddr($_SERVER['REMOTE_ADDR']));
   $login = addslashes(trim($login));
   $time = time();
   //discover the pcip
   $this->pcip = discoverPCIP();
   //discover the realip
   $this->realip = discoverIP();
   //set cookie for pcip
   $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
   setcookie('pcip', "$this->pcip", time()+60*60*24*365, '/', $domain, false);
   //setcookie('session_life', session_id(), time()+60*60*24*365, '/', $domain, false);
      
	//insert new user to users table and insert an option
	if($guest == 1 && get_ConfVar("LTChatCore_guest_account") == true){
	 $password = rand(0,12345678);
	 $this->add_user($login, $password, array(), $guest);
	}
	//check for guest account if not enable
	if($guest == 1 && get_ConfVar("LTChatCore_guest_account") == false){
	  return array('error' => ChDK_log_err_guest_account);
	}
   
   //select an user from database by nick
   $result = mysql_query("select * from `".LTChat_Main_prefix."users` where `nick` = '{$login}' and `chat_id` = '".LTChat_Main_CHAT_ID."' LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   
	//fetch with out while
	if($row = mysql_fetch_object($result))
	{
	
	   if($guest != 1){
		//check password for member only on login
		if($password != $row->password && md5($password) != $row->password)
		return array('error' => ChDK_log_err_bad_password);
	   }

	//select the user on actions table
	$actions = mysql_query("select * from `".LTChat_Main_prefix."actions` where `action_on` = '{$login}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__); 
		
		//select the type and reason and time
		if($actionsrow = mysql_fetch_object($actions))
		{
			$type = $actionsrow->type;
			$reason = $actionsrow->reason;
			$action_time = $actionsrow->action_time;
		}##
		
	/**
	* check for is member
	*/
	if($guest != 1){
	
   //check time bar if exsist dont insert row do (update) else insert row
   $check_timer = mysql_query("select * from `".LTChat_Main_prefix."timer` where `users_id` = '{$row->id}' and `chat_id` = '".LTChat_Main_CHAT_ID."' LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   
   if($check = mysql_fetch_object($check_timer))
   {
	  //check if have time rows
	  if($check->users_id == $row->id)
	  {
		mysql_query("update `".LTChat_Main_prefix."timer` set time_log = '".time()."', login_time = '".time()."' where `users_id` = '{$row->id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	  }##check if have time rows
	   
	   //check monthly bar and update to date
	   if($row->level >= from_level && $row->level <= to_level)
	   {
		  if(is_array($range))
			if(in_array($row->level, $range))
			  if($check->date != date('m-d'))
		mysql_query("update `".LTChat_Main_prefix."timer` set date = '" . date('m-d') . "' , monthly = monthly + 4 where `users_id` = '{$check->users_id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		}
	   
	//create new row if not have time rows		  
	}else{
		mysql_query("insert into `".LTChat_Main_prefix."timer` 
		(users_id, time_log, login_time, chat_id, date, monthly) 
		values 
		('{$check->users_id}', '".time()."', '".time()."', '".LTChat_Main_CHAT_ID."', '".date('m-d')."', '4')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	  }##check if have time rows
	mysql_free_result($check_timer);
 ## end time bar
	
	//check sus and flash users suspaneded more 7days
		if ($type == 'sus'){
			if(get_date_ex($action_time,false,false,true,false) >= 7){
				mysql_query("delete from `".LTChat_Main_prefix."actions` where `type` = 'sus' and `users_id` = '{$row->id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
				mysql_query("delete from `".LTChat_Main_prefix."check` where `sus` = '1' and `users_id` = '{$row->id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
			}else{ 
				return array('error' => ChDK_log_err_suspended, 'reason' => $reason);
			}
		}##end check sus
	
	//check kick and flash kick for users kicked more 3 min
	if ($type == 'kick'){
			if(get_date_ex($action_time,true,false,false,false) >= 3){
				mysql_query("delete from `".LTChat_Main_prefix."actions` where `type` = 'kick' and `action_on` = '{$login}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
				mysql_query("delete from `".LTChat_Main_prefix."check` where `kick` = '1' and `users_id` = '{}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
			}else{
			return array('error' => ChDK_log_err_kicked, 'reason' => $reason);
			}
	}##end check kick
		
	//check for howis waiting me and send wait msg!
	$d = mysql_query("select * from `".LTChat_Main_prefix."wait` where `users_id` = '{$row->id}' and `chat_id` = '".LTChat_Main_CHAT_ID."' LIMIT 1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
			if(mysql_num_rows($d) == 1){
				while ($wait = mysql_fetch_object($d))
				{
					foreach(explode(",", $wait->nick) as $idwait) 
					{
					$waituser = $this->get_user_by_id($idwait);
					$text = str_replace(array("#waitname#","#waitright#","#joinedroom#"),
										array($waituser->nick, $waituser->rights, $room), waitmsg);
						if ($this->online($idwait))
						$this->post_private_reason($text, $row->id, 1);
					}##end foreach
				 }##end while
			}##end number rows
	
	/**
	* check actions for user
	*/
	}else{
	
	//check kick and flash kick for users kicked more 3 min
	if ($type == 'kick'){
			if(get_date_ex($action_time,true,false,false,false) >= 3){
			mysql_query("delete from `".LTChat_Main_prefix."actions` where `type` = 'kick' and `action_on` = '{$login}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
				mysql_query("delete from `".LTChat_Main_prefix."check` where `kick` = '1' and `users_id` = '{$row->id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
			}else{
			return array('error' => ChDK_log_err_kicked, 'reason' => $reason);
			}
	}##end check kick
	
	//check kick and flash kick for users kicked more 3 min
	if ($type == 'mkick'){
			if(get_date_ex($action_time,true,false,false,false) >= 3){
			mysql_query("delete from `".LTChat_Main_prefix."actions` where `type` = 'mkick' and `action_on` = '{$login}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
			mysql_query("delete from `".LTChat_Main_prefix."check` where `mkick` = '1' and `users_id` = '{$id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
			}else{
			return array('error' => ChDK_log_err_mkicked, 'reason' => $reason);
			}
	}##end check kick
	
	//check actions for ban user || ip
	if ($type == 'banip' or $type = 'banuser'){ 
		$c = mysql_query("SELECT * FROM `".LTChat_Main_prefix."actions` WHERE `users_id` = '0' and `type` = 'banip' OR `type` = 'banuser' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		 
		 if(mysql_num_rows($c) > 0){
			 while ($banned = mysql_fetch_object($c))
			 {
				//check for global ip banned more 3 days and delete
				if(get_date_ex($action_time,false,false,true,false) >= 3):
				mysql_query("delete from `".LTChat_Main_prefix."actions` where `users_id` = '0' and `type` = 'banip' OR `type` = 'banuser' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
				// check banned ip by nick
				elseif( $banned->action_on == $login ):
				return array('error' => ChDK_log_err_banned, 'reason' => $banned->reason);
				// check banned ip
				elseif( check_banned($banned->banip) ):
				return array('error' => ChDK_log_err_banned, 'reason' => "Sorry, your Ip address ($banned->banip) has been banned by $banned->action_by");
				endif;
			 }##end while
		 }##end number rows
	 }##end check ban
	 
	//check actions for ban pc user || pc ip
	if ($type == 'banpcip' or $type = 'banpcuser'){ 
		$c = mysql_query("SELECT * FROM `".LTChat_Main_prefix."actions` WHERE `users_id` = '0' and `type` = 'banpcip' OR `type` = 'banpcuser' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		 
		 if(mysql_num_rows($c) > 0){
			 while ($banned = mysql_fetch_object($c))
			 {
				//check for pc ip banned more 3days and delete
				if(get_date_ex($action_time,false,false,true,false) >= 3):
				mysql_query("delete from `".LTChat_Main_prefix."actions` where `users_id` = '0' and `type` = 'banpcip' OR `type` = 'banpcuser' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
				// check banned ip by nick
				elseif( $banned->action_on == $login ):
				return array('error' => ChDK_log_err_banned, 'reason' => $banned->reason);
				// check banned ip
				elseif( check_banned($banned->banip) ):
				return array('error' => ChDK_log_err_banned, 'reason' => "Sorry, your PC Ip address ($banned->banip) has been banned by $banned->action_by");
				elseif($banned->banip == 'NONE' ):
				return array('error' => ChDK_log_err_banned, 'reason' => "Sorry, The System Not Found Ip address For Your Connection");
				endif;
			 }##end while
		 }##end number rows
	 }##end check ban pc
	 
	 }##if

	$_SESSION['LTChart_user_id'] = $row->id;
	$_SESSION['LTChart_user_nick'] = $row->nick;
	$_SESSION['LTChart_user_rights'] = $row->rights;
	$_SESSION['LTChart_user_level'] = $row->level;	
	
	//generate the rights for user by level
	$maskip = $this->get_rights_by_level($row->level);
	//generate user color by level
	$user_color = $this->get_user_color($row->level);
	
	if($row->mygroup == 1){
	$login_rights = 'Team Members';
	
	}else if($row->mygroup == 2){
	$login_rights = 'ADV Team';
	
	}else if($row->mygroup == 0){
	$login_rights = 'New Login';
	}
	
	//create the login message
	$msg = str_replace(array("#login_right#", "#user#", "#user_color#", "#rights#", "#ip#", "#room#"),
					   array($login_rights,$row->nick, $user_color, $maskip, $row->rights, $room), 
					   ChFun_new_login);
	//post the login message in public   
	$this->post_reason($msg, $room);
	
	//update and create new session life for who is online
	$this->enter_room($room);
	
	mysql_query("update `".LTChat_Main_prefix."users` set last_seen = '".time()."', last_host = '{$hostname}', last_ip = '{$this->realip}', last_pcip = '{$this->pcip}' where `id` = '{$row->id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	  
	//Post the trace logs (Mask ip, REMOTE_ADDR, HTTP_X_FORWARDED_FOR)
	$this->post_trace_logs($row->nick, $room);
	
	if($row->level > 0)
	$this->get_team_list_online($row->id);
				
	return true;			
   }

   // if nothink find to do post (bad login not found) message
   if($guest != 1)
	   return array('error' => ChDK_log_err_bad_login);

   }
	   

  /**
   * LTChatDataKeeper::add_user()
   *
   * @param mixed $login
   * @param mixed $password
   * @param mixed $other_options
   * @param integer $guest
   * @return
   */
   function add_user($login, $password, $other_options = array(), $guest = 0)
   {
	//$hany = $time - get_ConfVar("ChDK_delete_guest_after");
	
	$query = mysql_query("SELECT `U`.id AS delete_id  FROM `".LTChat_Main_prefix."who_is_online` W, `".LTChat_Main_prefix."users` U WHERE `W`.online = '0' and `U`.level <= '0' and `W`.users_id = `U`.id and `W`.`chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	
	//check for ChDK_delete_guest_after && ChDK_delete_user_after
	while ($expired = mysql_fetch_object($query)){
	$this->delete_user($expired->delete_id);
	}
	
	   $login = addslashes($login);

  	   if((boolean)get_ConfVar("LTChat_md5_passwords"))  $password = md5($password);
  	     
  	   $password = addslashes($password);

	   $level = $_POST['thelevel'] ? $_POST['thelevel'] : '0';

	   $rights = $this->get_rights_by_level($level); //get right by level
	   
  	   if($guest == 1)
  	     $rights = "Guest";
		 
      $rand_color = array("#9900FF","#FF0000","#CC3366","#0099CC","#6600FF","#0000FF","#009900","#660000","#FF9900","#FF00CC","#FF3399","#FF66FF", "FF99FF","#CC9900","#0033FF","#CC6666","#9966FF","#000000","#003366","#339999","#CC66FF","#330099","#990099","#3366FF","#000033","#CC9999","#663300","#996666","#FFCCCC");
	  
	  //Rand color array for give user 'color'
	  $result = array_rand($rand_color);
	  
	  //Rand color array for give user 'nickcolor'
	  $result2 = array_rand($rand_color);

	   mysql_query("INSERT INTO `".LTChat_Main_prefix."users` 
	   (nick, password, registered, rights, chat_id, color, nickcolor, font, nickfont, level, email) 
	   values 
	   ('{$login}', '{$password}','".time()."','{$rights}','".LTChat_Main_CHAT_ID."','{$rand_color[$result]}','{$rand_color[$result2]}','Tahoma','Tahoma', '{$level}', '{$antispam}')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

	   if(mysql_affected_rows() == -1)
	     return false;
	   else
	   {
	     $result = mysql_query("select * from `".LTChat_Main_prefix."users` where `nick` = '{$login}' and `password` = '{$password}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		 if($user = mysql_fetch_object($result))
		 {
		   if(is_array($other_options))
		     foreach ($other_options as $ot_id => $ot_ar)
		     {
		       $value = addslashes($ot_ar['value']);
		       mysql_query("INSERT INTO `".LTChat_Main_prefix."users_var` 
			   (`".LTChat_Main_prefix."users_var_names_id` , `".LTChat_Main_prefix."users_id` , `value`, chat_id) 
			   VALUES 
			   ('{$ot_id}', '{$user->id}', '{$value}','".LTChat_Main_CHAT_ID."');") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		     }
		 }
		 else 
		   return false;
	   	 
	     return true;
	   }
   }   

  /**
   * LTChatDataKeeper::update_other_fields()
   *
   * @param mixed $to_update
   * @return
   */
   function update_other_fields($to_update)
   {
   	 foreach ($to_update as $id => $value)
   	 {
   	   $value = addslashes($value);
	   mysql_query("delete from `".LTChat_Main_prefix."users_var` where ".LTChat_Main_prefix."users_var_names_id = '{$id}' and ".LTChat_Main_prefix."users_id = '{$_SESSION['LTChart_user_id']}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	   mysql_query("INSERT INTO `".LTChat_Main_prefix."users_var` (`".LTChat_Main_prefix."users_var_names_id` , `".LTChat_Main_prefix."users_id` , `value`, chat_id ) VALUES ('{$id}', '{$_SESSION['LTChart_user_id']}', '{$value}','".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   	 }
   }

  /**
   * LTChatDataKeeper::enter_room()
   *
   * @param mixed $room
   * @return
   */
   function enter_room($room)
   {
     $time = time();
  	 mysql_query("delete from `".LTChat_Main_prefix."who_is_online` where `users_id` = '{$_SESSION['LTChart_user_id']}' and room = '{$room}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
  	 mysql_query("insert into ".LTChat_Main_prefix."who_is_online
	 (action_time, users_id, room, online,chat_id,session_life) 
	 values  
	 ('{$time}', '{$_SESSION['LTChart_user_id']}','{$room}','1','".LTChat_Main_CHAT_ID."','".session_id()."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }

  /**
   * LTChatDataKeeper::delete_offline_users()
   *
   * @return
   */
   function delete_offline_users()
   {
  	 $time = time();
  	 // usuniecie uzytkownikow ktorzy wyszli z chata

  	 $result = mysql_query("select * from ".LTChat_Main_prefix."who_is_online where online = '1' and action_time < {$time}-".get_ConfVar("LTChart_offline_user_after")." and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
  	 while ($row = mysql_fetch_object($result))
  	 {
	   mysql_query("delete from ".LTChat_Main_prefix."who_is_online where who_id = '{$row->who_id}' and room = '{$row->room}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
  	   mysql_query("insert into ".LTChat_Main_prefix."who_is_online
	   (action_time, users_id, room, online, chat_id, session_life) 
	   values  
	   ('{$time}', '{$row->users_id}','{$row->room}','0','".LTChat_Main_CHAT_ID."','".session_id()."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
  	 }
   }
   
  /**
   * LTChatDataKeeper::delete_over_public_messages()
   *
   * @return
   */
   function delete_over_public_messages(){
   
   $q = mysql_query("SELECT COUNT(id) FROM `".LTChat_Main_prefix."talk`") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

    if($line = mysql_fetch_row($q)) {
		foreach ($line as $value) {
		   //sum the messages numbers for delete
		   $count = $value - LTChart_max_lines;
		   if($value > LTChart_max_lines){
		   mysql_query("delete FROM `".LTChat_Main_prefix."talk` WHERE `chat_id` = '0' ORDER BY id LIMIT {$count}") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		   }
		}
	}
	mysql_free_result($q);
   
   }
   
  /**
   * LTChatDataKeeper::delete_jail_finish()
   *
   * @return
   */
   function delete_jail_finish()
   {
  	 $time = time();

  	 $result = mysql_query("select * from `".LTChat_Main_prefix."check` where `jail` = '1' and `users_id` = '{$_SESSION['LTChart_user_id']}' and `chat_id` = '".LTChat_Main_CHAT_ID."' LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
  	   if($row = mysql_fetch_object($result))
  	   {
		  if(get_date_ex($row->action_time,true,false,false,false) >= 3){ // after 1 min
   	   mysql_query("update `".LTChat_Main_prefix."check` set `jail` = '0' where `users_id` = '{$_SESSION['LTChart_user_id']}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	      }
  	   }
   }
   
  /**
   * LTChatDataKeeper::delete_jail_finish()
   *
   * @return
   */
   function delete_user_screen_filterd()
   {
     $time = time();
     
	 $qurey = mysql_query("SELECT `W`.`users_id`, `W`.`online`, `C`.users_id, `C`.filter, `C`.clear FROM `".LTChat_Main_prefix."who_is_online` W, `".LTChat_Main_prefix."check` C WHERE `W`.users_id = `C`.users_id and `W`.chat_id = '".LTChat_Main_CHAT_ID."' and `W`.online = '1'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
        
		if(mysql_num_rows($qurey) > 0){
			while ($back = mysql_fetch_array($qurey)){
mysql_query("update `".LTChat_Main_prefix."check` set filter = '0' where action_time <= {$time}-".get_ConfVar("ChDK_delete_screen_filterd_after")." and `filter` = '1' and `users_id` = '{$back['users_id']}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
			}
		}
   }
   
  /**
   * LTChatDataKeeper::user_action()
   *
   * @param mixed $room
   * @return
   */
   function user_action($room)
   {
   	 $room = addslashes($room);
  	 $time = time();
  	 if($this->user_logged_in())
  	 {
//   	   mysql_query("update ".LTChat_Main_prefix."users set last_seen = '".time()."' where id = '{$_SESSION['LTChart_user_id']}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	   
       mysql_query("delete from `".LTChat_Main_prefix."private_talk` WHERE `users_id_to` = '{$_SESSION['LTChart_user_id']}' and time < {$time}-".get_ConfVar("LTChart_message_expired_after")." and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	   //check user's by the session for flood checked
	   if( $this->whois_have_action($_SESSION['LTChart_user_id'], 'flood') ){
		mysql_query("update `".LTChat_Main_prefix."check` set `flood` = '0' where `users_id` = '{$_SESSION['LTChart_user_id']}' and `flood` = '1' and `action_time` < ".time()."- 2 and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	   }
	   
	   $this->delete_over_public_messages();   	   
   	   $this->delete_offline_users();
	   $this->delete_jail_finish();
  
  	   $result = mysql_query("select * from `".LTChat_Main_prefix."who_is_online` where `users_id` = '{$_SESSION['LTChart_user_id']}' and `room` = '{$room}' and chat_id = '".LTChat_Main_CHAT_ID."' order by who_id asc") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
  	   if($row = mysql_fetch_object($result))
  	   {
  	   	  if($row->online == '0')
  	   	  {
  	   	  	mysql_query("delete from ".LTChat_Main_prefix."who_is_online where users_id = '{$_SESSION['LTChart_user_id']}' and room = '{$row->room}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
  	        mysql_query("insert into ".LTChat_Main_prefix."who_is_online
			(action_time, users_id, room, online, chat_id, session_life) 
			values  
			('{$time}', '{$_SESSION['LTChart_user_id']}','{$room}','1','".LTChat_Main_CHAT_ID."','".session_id()."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
  	   	  }
  	   	  else
  	   	  {
		    if(session_id() == $row->session_life){
  	   	    mysql_query("update ".LTChat_Main_prefix."who_is_online set action_time = '{$time}', online = '1' where who_id = '{$row->who_id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
			}else{
			session_destroy();
			exit;
			}
  	   	  }
  	   }
  	   else
  	   {
	     mysql_query("delete from ".LTChat_Main_prefix."who_is_online where who_id = '{$row->who_id}' and room = '{$row->room}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
  	     mysql_query("insert into ".LTChat_Main_prefix."who_is_online
		 (action_time, users_id, room, online, chat_id, session_life) 
		 values  
		 ('{$time}', '{$_SESSION['LTChart_user_id']}','{$room}','1','".LTChat_Main_CHAT_ID."', '".session_id()."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
  	   }
  	   
  	   mysql_query("delete from ".LTChat_Main_prefix."who_is_online where action_time < {$time}-".get_ConfVar("LTChart_delete_offline_data")." and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
  	   
	   return true;
  	 }
  	 else 
  	   return false;
   }
   
  /**
   * LTChatDataKeeper::get_my_new_private_messages()
   *
   * @param mixed $last_id
   * @return
   */
   function get_my_new_private_messages($last_id)
   {
   
   	 $result = mysql_query("SELECT U.nick as user, U.id as user_id, PT.id, PT.users_id_from, PT.users_id_to, PT.text, PT.time, PT.delivered_from, PT.delivered_to FROM `".LTChat_Main_prefix."users` U , `".LTChat_Main_prefix."private_talk` PT WHERE PT.users_id_from = U.id and PT.users_id_to = '{$_SESSION['LTChart_user_id']}' and PT.id > '{$last_id}'  and delivered_from = '1' and delivered_to = '0' and time + 10 < ".time()." and PT.chat_id = '".LTChat_Main_CHAT_ID."' and U.chat_id = '".LTChat_Main_CHAT_ID."' order by id desc") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     while ($row = mysql_fetch_object($result)){
       $out[] = $row;
     }
     mysql_free_result($result);
	   
     return $out;
   }

  /**
   * LTChatDataKeeper::set_avatar()
   *
   * @param mixed $file_path
   * @return
   */
   function set_avatar($file_path)
   {
     $file_path = addslashes($file_path);
     mysql_query("update ".LTChat_Main_prefix."users set picture_url = '{$file_path}' where id = '{$_SESSION['LTChart_user_id']}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 mysql_query("delete from ".LTChat_Main_prefix."who_is_online where users_id = '{$_SESSION['LTChart_user_id']}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }
   
  /**
   * LTChatDataKeeper::get_avatars_list()
   *
   * @return
   */
   function get_avatars_list()
   {
     $result = mysql_query("select nick, picture_url from ".LTChat_Main_prefix."users where picture_url <> '' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
     while ($row = mysql_fetch_array($result,MYSQL_ASSOC))
       $out[$row['picture_url']] = $row['nick'];

     return $out;
   }
   
  /**
   * LTChatDataKeeper::check_actions()
   *
   * @param mixed $id
   * @return
   */
   function check_actions($id)
   {
     $result = mysql_query("SELECT `C`.users_id,`C`.jail,`C`.kick,`C`.mkick,`C`.banuser,`C`.banip,`C`.xban,`C`.sus,`A`.users_id,`A`.type,`A`.action_by,`A`.reason from `".LTChat_Main_prefix."check` C, `".LTChat_Main_prefix."actions` A WHERE  `A`.users_id = '{$id}' and `C`.users_id = '{$id}' LIMIT 1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
     
      if($row = mysql_fetch_array($result)){
	  $out[] = $row;
	  }
      return $out;
   }
   
  /**
   * LTChatDataKeeper::check_member_exists()
   *
   * @param mixed $nick
   * @return
   */
   function check_member_exists($nick)
   {
   
   $q = mysql_query("SELECT nick FROM `".LTChat_Main_prefix."users` WHERE `nick` != 'Chat System' and `nick` = '{$nick}' and `level` > '0' and `chat_id` = '".LTChat_Main_CHAT_ID."' LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   
      if(mysql_num_rows($q) > 0){
	  	return true;
		  }else{
		return false;
	  }
	mysql_free_result($q);
	  
	}
   
  /**
   * LTChatDataKeeper::insert_check4action()
   *
   * @param mixed $id
   * @param mixed $row_name
   * @return
   */
   function insert_check4action($id,$row_name)
   {
	   $insert_check = mysql_query("select users_id,$row_name from `".LTChat_Main_prefix."check` where `users_id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."' LIMIT 1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	   
  	 if($how = mysql_fetch_object($insert_check))
  	    {
  	   	  if($how->users_id == $id)
mysql_query("update `".LTChat_Main_prefix."check` set `$row_name` = '1' ,action_time = '".time()."'  where `users_id` = '{$id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		  }else{
mysql_query("insert into `".LTChat_Main_prefix."check` (users_id, action_time, $row_name, chat_id) values ('{$id}', '".time()."', '1','".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		}
		mysql_free_result($insert_check);
   }
   
  /**
   * LTChatDataKeeper::get_id_by_nick()
   *
   * @param mixed $nick
   * @return
   */
   function get_id_by_nick($nick)
   {
   	 $nick = addslashes($nick);
     $result = mysql_query("select id,nick,chat_id from ".LTChat_Main_prefix."users where nick = '{$nick}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     if($row = mysql_fetch_object($result))
     {
	    $id = $row->id;
	 }
	 mysql_free_result($result);
	 
	 return $id;
}
   
  /**
   * LTChatDataKeeper::get_user_by_nick()
   *
   * @param mixed $nick
   * @param bool $simple
   * @return
   */
   function get_user_by_nick($nick, $simple = false)
   {
   	 $nick = addslashes($nick);
     $result = mysql_query("select * from ".LTChat_Main_prefix."users where nick = '{$nick}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     if($row = mysql_fetch_object($result))
     {
       if(!$simple)
       {
         $result = mysql_query("SELECT * FROM `".LTChat_Main_prefix."users_var` where ".LTChat_Main_prefix."users_id = '{$row->id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
         while ($res_vars = mysql_fetch_object($result))
           $row->other_fields[] = $res_vars;
       }
       return $row;
     }
     else
       return null;
   }
   
  /**
   * LTChatDataKeeper::get_user_by_id()
   *
   * @param mixed $id
   * @param bool $simple
   * @return
   */
   function get_user_by_id($id, $simple = false)
   {

     $result = mysql_query("select * from ".LTChat_Main_prefix."users where id = '{$id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     if($row = mysql_fetch_object($result))
     {
       if(!$simple)
       {
         $result = mysql_query("SELECT * FROM `".LTChat_Main_prefix."users_var` where ".LTChat_Main_prefix."users_id = '{$row->id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
         while ($res_vars = mysql_fetch_object($result))
           $row->other_fields[] = $res_vars;
       }
       return $row;
     }
     else
       return null;
    mysql_free_result($result);
   }
   
   // pobranie listy pol ktore uzytkownik wypelnia przy rejestracji
  /**
   * LTChatDataKeeper::get_registration_fields()
   *
   * @return
   */
   function get_registration_fields()
   {
     $result = mysql_query("select * from `".LTChat_Main_prefix."users_var_names` where `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
     
     while ($row = mysql_fetch_object($result))
		$out[] = $row;

     return $out;
   }
   
  /**
   * LTChatDataKeeper::del_reg_field()
   *
   * @param mixed $id
   * @return
   */
   function del_reg_field($id)
   {
   	 $result = mysql_query("select * from `".LTChat_Main_prefix."users_var_names` where id = '{$id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

   	 if($row = mysql_fetch_object($result))
   	 {
      mysql_query("delete from `".LTChat_Main_prefix."users_var_names` where id = '{$id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
      mysql_query("delete from `".LTChat_Main_prefix."users_var` where ".LTChat_Main_prefix."users_var_names_id  = '{$id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   	 }

   }

  /**
   * LTChatDataKeeper::add_reg_field()
   *
   * @param mixed $f_name
   * @param mixed $item
   * @param mixed $required
   * @param mixed $lenght
   * @param mixed $options
   * @return
   */
   function add_reg_field($f_name, $item, $required, $lenght, $options)
   {
   	 $f_name = addslashes($f_name);
   	 $item = addslashes($item);
   	 $lenght = addslashes($lenght);
   	 $options = addslashes($options);
   	 
   	 mysql_query("INSERT INTO `".LTChat_Main_prefix."users_var_names` (`var_name` , `var_type` , `var_length` , `options` , `required`, chat_id ) VALUES ('{$f_name}', '{$item}', '{$lenght}', '{$options}' , '{$required}', '".LTChat_Main_CHAT_ID."');") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }
   
  /**
   * LTChatDataKeeper::get_new_cmail()
   *
   * @return
   */
   function get_new_cmail()
   {
	 $get = mysql_query("SELECT count(id) from `pmessages` where `touser` = '{$_SESSION['LTChart_user_nick']}' and `unread` = 'unread'") or debug(mysql_error(), "MessagesTplParser", __LINE__);
     $rows = mysql_fetch_row($get);
     return $rows["0"];
   }
   
  /**
   * LTChatDataKeeper::get_time()
   *
   * @return
   */
   function get_time()
   {
     $result = mysql_query("select * from `".LTChat_Main_prefix."timer` where `users_id` = '{$_SESSION['LTChart_user_id']}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     if($row = mysql_fetch_object($result))
     {
	    $time_log = $row->time_log;
	 }
	 
	 return $time_log;
   }
   
  /**
   * LTChatDataKeeper::get_time_log()
   *
   * @param mixed $id
   * @return
   */
   function get_time_log($id)
   {
     $result = mysql_query("select time_log from `".LTChat_Main_prefix."timer` where `users_id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     if($row = mysql_fetch_object($result))
     {
	    $time_log = $row->time_log;
	 }
	 return $time_log;
   }
   
  /**
   * LTChatDataKeeper::get_total_time()
   *
   * @return
   */
   function get_total_time()
   {
     $result = mysql_query("select total_time from ".LTChat_Main_prefix."timer where users_id = '{$_SESSION['LTChart_user_id']}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     if($row = mysql_fetch_row($result))
     {
	    $total_time = $row[0];
	 }
	 return $total_time;
   }
   
  /**
   * LTChatDataKeeper::get_total_monthly()
   *
   * @return monthly
   */
   function get_total_monthly()
   {
     $result = mysql_query("select monthly from `".LTChat_Main_prefix."timer` where users_id = '{$_SESSION['LTChart_user_id']}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     if($row = mysql_fetch_row($result))
     {
	    $monthly = $row[0];
	 }
	 return $monthly;
   }
   
  /**
   * LTChatDataKeeper::insert_total_time()
   *
   * @param mixed $do_total_time
   * @param mixed $upgrade
   * @return
   */
   function insert_total_time($do_total_time, $upgrade)
   {
   
      if($do_total_time){
	 //update total time and reset the time log for refresh total time 
     mysql_query("update ".LTChat_Main_prefix."timer set total_time = total_time + $do_total_time, `time_log` = '".time()."' where `users_id` = '{$_SESSION['LTChart_user_id']}'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 }
	 
	 if($upgrade){
	 
	 mysql_query("update ".LTChat_Main_prefix."users set level = level + 1 where id='{$_SESSION['LTChart_user_id']}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);	 
	 
     mysql_query("update ".LTChat_Main_prefix."timer set total_time = '0', `time_log` = '".time()."' where `users_id` = '{$_SESSION['LTChart_user_id']}'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 }
	 
   }
   
  /**
   * LTChatDataKeeper::total_time_update()
   *
   * @return
   */
   function total_time_update()
   {
     global $autoup;
   
   	    $user_info = $this->get_user_by_nick($this->get_user_name()); //get sender info
		
	    $level = $this->get_user_level(); //get sender level
		
		$time_log = $this->get_time(); //get time login time
		
		$time_now = time(); //time now :)
	    
		$total = $time_now - $time_log;
		
		// update the total time and time log without upgrade level
		$this->insert_total_time($total, FALSE);
		
		$howmytotaltime = $this->get_total_time(); //get total time sender
		$sumtotaltime = $howmytotaltime / 3600; //sum total time on hours
		$mytotaltime = ceil($sumtotaltime); // ceil total time
		$range = range(from_level, to_level);

		if($level >= from_level && $level <= to_level){
		  if(is_array($range))
			if(in_array($level, $range))
				$limitaz = $autoup[$level];

			 if($mytotaltime >= $limitaz){
			     //upgrade message
				 $upgrade_msg = str_replace(array("#user#"), array($row->user), UPGRADE);
				 //upgrade the level and set total time zero(0)
				 $this->insert_total_time(FALSE, TRUE);
				 //post upgrade msg on public room
				 $this->post_reason($upgrade_msg, 'Arabia');
			 }
		}
   }
   
//------------ rooms ---------------------------------------------------
  /**
   * LTChatDataKeeper::add_room()
   *
   * @param mixed $room_name
   * @param mixed $room_cat
   * @return
   */
   function add_room($room_name, $room_cat)
   {
   	 $room_name = addslashes($room_name);
   	 $room_cat = addslashes($room_cat);
   	 $result = mysql_query("select * from ".LTChat_Main_prefix."rooms where room_name = '{$room_name}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

   	 if(mysql_num_rows($result) > 0)
   	   return ChFun_croom_ErrExists;
   	   
	 mysql_query("insert into ".LTChat_Main_prefix."rooms(room_name, room_cat, chat_id) values ('{$room_name}', '{$room_cat}', '".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 return true;
   }

  /**
   * LTChatDataKeeper::set_default_room()
   *
   * @param mixed $room_id
   * @return
   */
   function set_default_room($room_id)
   {
     mysql_query("update ".LTChat_Main_prefix."rooms set `default` = '0' where chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   	 $room_id = addslashes($room_id);
     mysql_query("update ".LTChat_Main_prefix."rooms set `default` = '1' where id = '{$room_id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }

  /**
   * LTChatDataKeeper::delete_room()
   *
   * @param mixed $room_id
   * @return
   */
   function delete_room($room_id)
   {
   	 $room_id = addslashes($room_id);
     mysql_query("delete from ".LTChat_Main_prefix."rooms where id = '{$room_id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }
 
  /**
   * LTChatDataKeeper::showops()
   *
   * @return
   */
   function showops()
   {
     $result = mysql_query("select id,nick,registered,last_seen,level,font,nickfont from `".LTChat_Main_prefix."users` where `nick` != 'Chat System' and `level` > '0' and `chat_id` = '".LTChat_Main_CHAT_ID."' order by level DESC") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
     while ($row = mysql_fetch_object($result))
       $out[] = $row;
	   return $out;
   }
   
  /**
   * LTChatDataKeeper::showsus()
   *
   * @return
   */
   function showsus()
   {
     $result = mysql_query("select * from `".LTChat_Main_prefix."actions` where `type` = 'sus'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 if ( mysql_num_rows($result) > 0 ) {
     while ($row = mysql_fetch_object($result)){
       $out[] = $row;
     }
	   return $out;
	   }else{
	   return false;
	   }
   }
   
  /**
   * LTChatDataKeeper::showban()
   *
   * @return
   */
   function showban()
   {
     $result = mysql_query("select * from `".LTChat_Main_prefix."actions` where `type` = 'banuser' or `type` = 'banip'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 if ( mysql_num_rows($result) > 0 ) {
     while ($row = mysql_fetch_object($result)){
       $out[] = $row;
     }
	   return $out;
	   }else{
	   return false;
	   }
   }
   
  /**
   * LTChatDataKeeper::showbanpc()
   *
   * @return
   */
   function showbanpc()
   {
     $result = mysql_query("select * from `".LTChat_Main_prefix."actions` where `type` = 'banpcuser' or `type` = 'banpcip'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 if ( mysql_num_rows($result) > 0 ) {
     while ($row = mysql_fetch_object($result)){
       $out[] = $row;
     }
	   return $out;
	   }else{
	   return false;
	   }
   }
   
  /**
   * LTChatDataKeeper::delete_sus()
   *
   * @param mixed $id
   * @param mixed $users_id
   * @return
   */
   function delete_sus($users_id)
   {
     //the user id well be stop the sus
     $users_id = (int)$users_id;
	 //get user info
     $users_data = $this->get_user_by_id($users_id);
	 //query for get the originally action by
	 $q = mysql_query("SELECT type,id,chat_id,action_by from `".LTChat_Main_prefix."actions` where `type` = 'sus' and `users_id` = '{$users_id}' and `chat_id` = '".LTChat_Main_CHAT_ID."' LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 
	 if($row = mysql_fetch_object($q)){
	 $action_by = $row->action_by;	 
	 }
	 //delete the sus from action's table
	 mysql_query("delete from `".LTChat_Main_prefix."actions` where `type` = 'sus' and `users_id` = '{$users_id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 //delete the sus for this member from check table
     mysql_query("update `".LTChat_Main_prefix."check` set `sus` = '0' where `users_id` = '{$users_id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);	 
	 //un-suspanded reason
	 $msg = str_replace(array("#user#","#sender#","#sender_original#"), 
		                array($users_data->nick, $action_by, $action_by), ChFun_un_sused);
	 //post the logs on logs table
	 $this->post_logs($msg, $room, 'unsus', FALSE);	
	 //post private logs
	 $this->post_private_logs(str_replace(array("#text#"), 
	                                            array($msg), ChFun_private_logs_syntex));
		 //check affected rows (update, delete)
		 if(mysql_affected_rows() == -1){
	     return false;
		 }else{
		 return true;
		 }
   }
   
  /**
   * LTChatDataKeeper::delete_ban()
   *
   * @param mixed $id
   * @return
   */
  function delete_ban($id)
  {
	//query for get the info the (action) by id
	$q = mysql_query("SELECT * from `".LTChat_Main_prefix."actions` where `id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."' LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	
	if($row = mysql_fetch_object($q)){
	  $type = $row->type;	 
	  $action_by = $row->action_by;
	  $action_on = $row->action_on;
	  $banip = $row->banip;
	}
	
	switch ($type){
	
		case "banip":
		$msg = str_replace(array("#ip#","#sender#","#sender_original#"), 
						   array($banip, $this->get_user_name(), $action_by), ChFun_un_banned_ip);
		$logs_type = 'unbanip';
		break;
		
		case "banuser":
		$msg = str_replace(array("#user#","#sender#","#sender_original#"), 
						   array($action_on, $this->get_user_name(), $action_by), ChFun_un_banned_nick);
		$logs_type = 'unbanuser';
		break;
		
		case "banpcuser":
		$msg = str_replace(array("#user#","#sender#","#sender_original#"), 
						   array($action_on, $this->get_user_name(), $action_by), ChFun_un_banned_nick);
		$logs_type = 'unbanpcip';
		break;
		
		case "banpcip":
		$msg = str_replace(array("#ip#","#sender#","#sender_original#"), 
						   array($banip, $this->get_user_name(), $action_by), ChFun_un_banned_ip);
		$logs_type = 'unbanpcip';
		break;
		
	}
	//post the logs on logs table
	$this->post_logs($msg, $room, $logs_type, FALSE);  
	
	mysql_query("delete from `".LTChat_Main_prefix."actions` where `id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	
	if(mysql_affected_rows() == -1){
	return false;
	}else{
	return true;
	}
  
  } 
   
  /**
   * LTChatDataKeeper::delete_disable()
   *
   * @param mixed $users_id -> user id
   * @return
   */
  function delete_disable($users_id)
   {
	 mysql_query("delete from `".LTChat_Main_prefix."actions` where `type` = 'disable' and `users_id` = '{$users_id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	
     mysql_query("update `".LTChat_Main_prefix."check` set `disable` = '0' where `users_id` = '{$users_id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		 
		 if(mysql_affected_rows() > 0){
	     return true;
		 }else{
		 return false;
		 }
   }
      
  /**
   * LTChatDataKeeper::showdisable()
   *
   * @return
   */
   function showdisable()
   {
     $result = mysql_query("select * from `".LTChat_Main_prefix."actions` where `type` = 'disable'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 if ( mysql_num_rows($result) > 0 ) {
     while ($row = mysql_fetch_object($result)){
       $out[] = $row;
     }
	   return $out;
	   }else{
	   return false;
	   }
   }
   
  /**
   * LTChatDataKeeper::show_whois()
   *
   * @param mixed $id
   * @param mixed $room
   * @param mixed $nick
   * @return
   */
   function show_whois($id, $room, $nick)
   {
     $a = mysql_query("SELECT `W`.`users_id`, `W`.`online`,`U`.`last_seen`,`U`.`id` FROM `".LTChat_Main_prefix."who_is_online` W, `".LTChat_Main_prefix."users` U WHERE W.online = '1' and W.users_id = '{$id}' and `W`.users_id = `U`.id and `W`.`chat_id` = '".LTChat_Main_CHAT_ID."' LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	    if ( mysql_num_rows($a) == 1 ) {
	        if($row = mysql_fetch_object($a)) {
		    $online = $row->online;
		    $login_time = $row->last_seen;
		    }
		}
		
	$b = mysql_query("select * from `".LTChat_Main_prefix."talk` where `room` = '{$room}' and `chat_id` = '".LTChat_Main_CHAT_ID."' and `user` = '{$nick}' order by id desc LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		if ( mysql_num_rows($b) == 1 ) { 
			if($talk = mysql_fetch_object($b)) {
			$last_public_msg_time = $talk->time;
			}
		}
		
	$c = mysql_query("select * from `".LTChat_Main_prefix."private_talk` where `users_id_from` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."' order by id desc LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		if ( mysql_num_rows($c) == 1 ) { 
			if($talk = mysql_fetch_object($c)) {
			$last_private_msg_time = $talk->time;
			}
		}
		
		return array('online' => $online, 
		             'login_time' => $login_time,
					 'last_public_msg_time' => $last_public_msg_time,
					 'last_private_msg_time' => $last_private_msg_time
					 );
   }
   
  /**
   * LTChatDataKeeper::upgrade_downgrade()
   *
   * @param mixed $id
   * @param mixed $new_level
   * @return
   */
   function upgrade_downgrade($id, $new_level)
   {
   		$id = addslashes($id);
		$new_level = addslashes($new_level);
		
		mysql_query("UPDATE `".LTChat_Main_prefix."users` SET `level` = '{$new_level}' WHERE `id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		
		$users_data = $this->get_user_by_id($id);
        $maskip = $this->get_rights_by_level($users_data->level); //get right by level
		
		mysql_query("UPDATE `".LTChat_Main_prefix."users` SET `rights` = '{$maskip}' WHERE `id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		
		if(mysql_affected_rows() == -1){
	     return false;
	    }else{
	     return true;
	    }
   }


  /**
   * LTChatDataKeeper::change_ip()
   *
   * @param mixed $setip
   * @return
   */
   function change_ip($setip)
   {
 
   mysql_query("UPDATE `".LTChat_Main_prefix."users` SET `rights` = '{$setip}' WHERE `id` = '{$_SESSION['LTChart_user_id']}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		
		if(mysql_affected_rows() == -1){
	     return false;
	    }else{
	     return true;
	    }
   }
 

  /**
   * LTChatDataKeeper::change_comment()
   *
   * @param mixed $comment
   * @return
   */
   function change_comment($comment)
   {
   
   $comment = addslashes($comment);
   
   mysql_query("UPDATE `".LTChat_Main_prefix."users` SET `comment` = '{$comment}' WHERE `id` = '{$_SESSION['LTChart_user_id']}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		
		if(mysql_affected_rows() == -1){
	     return false;
	    }else{
	     return true;
	    }
   }
   
  /**
   * LTChatDataKeeper::change_password()
   *
   * @param mixed $id
   * @param mixed $password
   * @return
   */
   function change_password($id, $password)
   {	
		//check hash
		if((boolean)get_ConfVar("LTChat_md5_passwords"))  $password = md5($password);
		
		$id = addslashes($id);
		$password = addslashes($password);
		
		mysql_query("UPDATE `".LTChat_Main_prefix."users` SET `password` = '{$password}' WHERE `id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		
		if(mysql_affected_rows() == -1){
	     return false;
	    }else{
	     return true;
	    }
   }
   
  /**
   * LTChatDataKeeper::change_op()
   *
   * @param mixed $id
   * @param mixed $new_nick_name
   * @return
   */
   function change_op($id, $new_nick_name)
   {
		$id = (int)$id;
		$new_nick_name = addslashes($new_nick_name);
		
		mysql_query("UPDATE `".LTChat_Main_prefix."users` SET `nick` = '{$new_nick_name}' WHERE `id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."' ") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		
		if(mysql_affected_rows() == -1){
		 return false;
		}else{
		 return true;
		}
   }

  /**
   * LTChatDataKeeper::kick_user()
   *
   * @param mixed $user_id
   * @param mixed $reason
   * @param mixed $type
   * @param mixed $nick
   * @return
   */
   function kick_user($user_id, $reason, $type, $nick)
   {
     $reason = addslashes($reason);
	 $type = addslashes($type);
	 $action_time = time();
   	 $user_id = (int)$user_id;
	 $nick = addslashes($nick);
	 $action_by = addslashes($_SESSION['LTChart_user_nick']);
	 
	 mysql_query("INSERT INTO `".LTChat_Main_prefix."actions` 
	 (`reason` , `type` , `action_time`, `chat_id`, `users_id`, `action_by`, `action_on`) 
	 VALUES 
	 ('{$reason}','{$type}', '{$action_time}', '".LTChat_Main_CHAT_ID."', '{$user_id}', '{$action_by}', '{$nick}')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }
   
  /**
   * LTChatDataKeeper::disable_member()
   *
   * @param mixed $user_id
   * @param mixed $reason
   * @param mixed $type
   * @param mixed $nick
   * @return
   */
   function disable_member($user_id, $reason, $type, $nick)
   {
     $reason = addslashes($reason);
	 $type = addslashes($type);
	 $action_time = time();
   	 $user_id = (int)$user_id;
	 $nick = addslashes($nick);
     $action_by = addslashes($_SESSION['LTChart_user_nick']);
	 
	 mysql_query("INSERT INTO `".LTChat_Main_prefix."actions` 
	 (`reason` , `type` , `action_time`, `chat_id`, `users_id`, `action_by`, `action_on`) 
	 VALUES 
	 ('{$reason}','{$type}', '{$action_time}', '".LTChat_Main_CHAT_ID."', '{$user_id}', '{$action_by}', '{$nick}')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }
   
  /**
   * LTChatDataKeeper::multikick_user()
   *
   * @param mixed $user_id
   * @param mixed $nick
   * @param mixed $last_ip
   * @return
   */
   function multikick_user($user_id, $nick, $last_ip)
   {
	 
     $g = mysql_query("SELECT `W`.`users_id`,`W`.action_time,`W`.`online`,U.`nick`,U.`last_ip`,U.`level`,U.`id` FROM `".LTChat_Main_prefix."who_is_online` W, `".LTChat_Main_prefix."users` U WHERE W.users_id = U.id and `W`.`chat_id` = '".LTChat_Main_CHAT_ID."' and W.online = '1' and U.chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	    $i = 0;
	    while ($row = mysql_fetch_object($g)){
			if(($row->level == 0) and ($row->last_ip == $last_ip)){
			$name .= $row->nick . ',';
	        mysql_query("INSERT INTO `".LTChat_Main_prefix."actions` 
	        (`reason` , `type` , `action_time`, `chat_id`, `users_id`, `action_by`, `action_on`) 
	        VALUES 
	        ('{$reason}','mkick', '{$row->action_time}', '".LTChat_Main_CHAT_ID."', '{$row->id}', '{$_SESSION['LTChart_user_nick']}', '{$row->nick}')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
			mysql_query("delete from `".LTChat_Main_prefix."who_is_online` where `online` = '1' and `users_id` = '{$row->id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
			$this->insert_check4action($row->id, 'mkick');
			$i++;
			}
		}##end while loop
		mysql_free_result($g);
		return array('number' => $i, 'name' => $name);  
   }
   
  /**
   * LTChatDataKeeper::jail_user()
   *
   * @param mixed $user_id
   * @param mixed $reason
   * @param mixed $type
   * @param mixed $nick
   * @return
   */
   function jail_user($user_id, $reason, $type, $nick)
   {
     $reason = addslashes($reason);
	 $type = addslashes($type);
	 $action_time = time();
   	 $user_id = (int)$user_id;
     $action_by = addslashes($_SESSION['LTChart_user_nick']);
	 
	 mysql_query("INSERT INTO `".LTChat_Main_prefix."actions` 
	 (`reason` , `type` , `action_time`, `chat_id`, `users_id`, `action_by`, `action_on`) 
	 VALUES 
	 ('{$reason}','{$type}', '{$action_time}', '".LTChat_Main_CHAT_ID."', '{$user_id}', '{$action_by}', '{$nick}')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }
   
  /**
   * LTChatDataKeeper::suspended_user()
   *
   * @param mixed $user_id
   * @param mixed $reason
   * @param mixed $type
   * @param mixed $nick
   * @return
   */
   function suspended_user($user_id, $reason, $type, $nick)
   {
     $reason = addslashes($reason);
	 $type = addslashes($type);
	 $action_time = time();
   	 $user_id = (int)$user_id;
     $action_by = addslashes($_SESSION['LTChart_user_nick']);
	 
	 mysql_query("INSERT INTO `".LTChat_Main_prefix."actions` 
	 (`reason` , `type` , `action_time`, `chat_id`, `users_id`, `action_by`, `action_on`) 
	 VALUES 
	 ('{$reason}','{$type}', '{$action_time}', '".LTChat_Main_CHAT_ID."', '{$user_id}', '{$action_by}', '{$nick}')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }
   
  /**
   * LTChatDataKeeper::banip_address()
   *
   * @param mixed $reason
   * @param mixed $type
   * @param mixed $banip
   * @param mixed $action_on
   * @return
   */
	function banip_address($reason, $type, $banip, $action_on)
	{
     $reason = addslashes($reason);
	 $type = addslashes($type);
	 $action_time = time();
	 $banip = addslashes($banip);
	 $action_by = addslashes($_SESSION['LTChart_user_nick']);
	 $action_on = addslashes($action_on);
	 	 
	 mysql_query("INSERT INTO `".LTChat_Main_prefix."actions` (`reason` , `type` , `action_time`, `chat_id`, `banip`, `action_by`, `action_on`) VALUES ('{$reason}','{$type}', '{$action_time}', '".LTChat_Main_CHAT_ID."', '{$banip}', '{$action_by}', '{$action_on}')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	}
	
  /**
   * LTChatDataKeeper::ban_nick()
   *
   * @param mixed $reason
   * @param mixed $type
   * @param mixed $banip
   * @param mixed $action_on
   * @return
   */
	function ban_nick($reason, $type, $banip, $action_on)
	{
     $reason = addslashes($reason);
	 $type = addslashes($type);
	 $action_time = time();
	 $banip = addslashes($banip);
	 $action_by = addslashes($_SESSION['LTChart_user_nick']);
	 $action_on = addslashes($action_on);
	 	 
	 mysql_query("INSERT INTO `".LTChat_Main_prefix."actions` 
	 (`reason` , `type` , `action_time`, `chat_id`, `banip`, `action_by`, `action_on`) 
	 VALUES 
	 ('{$reason}','{$type}', '{$action_time}', '".LTChat_Main_CHAT_ID."', '{$banip}', '{$action_by}', '{$action_on}')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	}

  /**
   * LTChatDataKeeper::get_all_rooms()
   *
   * @return
   */
   function get_all_rooms()
   {
   	 $this->delete_offline_users();
   	 $undefined = $out = array();

     $result = mysql_query("select * from ".LTChat_Main_prefix."rooms where chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
     while ($row = mysql_fetch_object($result))
       $out[] = $row;

     $result = mysql_query("select ".LTChat_Main_prefix."users.id, nick, room from `".LTChat_Main_prefix."who_is_online`,`".LTChat_Main_prefix."users` where users_id = ".LTChat_Main_prefix."users.id and online = '1' and `".LTChat_Main_prefix."who_is_online`.`chat_id` = '".LTChat_Main_CHAT_ID."' and `".LTChat_Main_prefix."users`.`chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
     while ($row = mysql_fetch_object($result))
     {
     	for($i = 0; $i < count($out); $i++)
     	{
     	  if($out[$i]->room_name == $row->room)
     	  {
     	    $out[$i]->users_online[] = array('nick' => $row->nick, 'id' => $row->id);
     	    break;
     	  }
     	}
     	if($out[$i]->room_name != $row->room)
     	  $undefined[$row->room][] = $row->nick;
     }


     return array('defined' => $out, 'undefined' => $undefined);
   }
############## rooms ###################################################

//----------------- friend ---------------------------------------------
  /**
   * LTChatDataKeeper::friend_user_del()
   *
   * @param mixed $user_id
   * @return
   */
   function friend_user_del($user_id)
   {
     mysql_query("delete from ".LTChat_Main_prefix."friends where `from_users_id` = '{$_SESSION['LTChart_user_id']}' and `to_users_id` = '{$user_id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }

  /**
   * LTChatDataKeeper::friend_user_add()
   *
   * @param mixed $user_id
   * @return
   */
   function friend_user_add($user_id)
   {
     mysql_query("insert into ".LTChat_Main_prefix."friends
	 (from_users_id, to_users_id, chat_id) 
	 values 
	 ('{$_SESSION['LTChart_user_id']}','{$user_id}','".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }
 
################### friend #############################################

  /**
   * LTChatDataKeeper::get_friend_list()
   *
   * @return
   */
   function get_friend_list()
   {
   	 $result = mysql_query("select U.id as users_id, U.nick, U.`picture_url` from ".LTChat_Main_prefix."users as U, ".LTChat_Main_prefix."friends as F where F.from_users_id = '{$_SESSION['LTChart_user_id']}' and F.to_users_id = U.id and F.chat_id = '".LTChat_Main_CHAT_ID."' and U.chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 while($row = mysql_fetch_object($result))
	   $out['from'][] = $row;

	 $result = mysql_query("select U.nick, F.from_users_id from ".LTChat_Main_prefix."users as U, ".LTChat_Main_prefix."friends as F where F.to_users_id = '{$_SESSION['LTChart_user_id']}' and F.from_users_id = U.id and F.chat_id = '".LTChat_Main_CHAT_ID."' and U.chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 while($row = mysql_fetch_object($result))
	   $out['to'][] = $row;

	 return $out;
   }
################### friend #############################################

//----------------- Team ---------------------------------------------

  /**
   * LTChatDataKeeper::team_user_add()
   *
   * @param mixed $g_id
   * @param mixed $user_id
   * @return
   */
   function team_user($g_id, $user_id)
   {
     mysql_query("UPDATE `".LTChat_Main_prefix."users` set `mygroup` = '{$g_id}' WHERE `id` = '{$user_id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }

  /**
   * LTChatDataKeeper::team_user_exists()
   *
   * @param mixed $user_id
   * @return
   */
   function team_user_exists($user_id)
   {
     $result = mysql_query("SELECT id FROM `".LTChat_Main_prefix."users` WHERE `id` = '{$user_id}' and `mygroup` != '0' and `chat_id` = '".LTChat_Main_CHAT_ID."' LIMIT 1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 
		 if(mysql_num_rows($result) > 0){
		 return FALSE;
		 }else{
		 return TRUE;
		 }
   }
      
  /**
   * LTChatDataKeeper::get_team_list()
   *
   * @return
   */
   function get_team_list()
   {
   	 $result = mysql_query("SELECT `U`.id, `U`.nick, `U`.level, `U`.mygroup, `G`.g_id, `G`.g_title, `G`.g_name FROM `users` U, `groups` G WHERE `U`.level > '0' and `U`.mygroup = `G`.g_id ORDER BY `U`.level") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 while($row = mysql_fetch_object($result))
	   $out['list'][] = $row;
	   
	 return $out;
   }
   
  /**
   * LTChatDataKeeper::get_team_list_online()
   *
   * @return
   */
   function get_team_list_online($id)
   {
   	 $result = mysql_query("SELECT `U`.nick, `U`.level, `U`.nickfont, `W`.online FROM `users` U, `groups` G, `who_is_online` W WHERE `U`.mygroup != '0' and `U`.mygroup = `G`.g_id and `U`.id = `W`.users_id ORDER BY `U`.level") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 
	if( mysql_num_rows($result) > 0 ) {
		while($row = mysql_fetch_object($result)){
		 
			 $levelcolor = $this->get_user_color($row->level); //get right by level
	
			 $text = str_replace(array("#levelcolor#", "#nickfont#", "#nick#"), 
								 array($levelcolor, $row->nickfont, $row->nick), ChFun_team_members_online);
									
		$this->post_private_reason($text, $id, 1);
		}
	}
	 
   }
################### Team #############################################

//----------------- ignore ---------------------------------------------
  /**
   * LTChatDataKeeper::ignore_user_del()
   *
   * @param mixed $user_id
   * @return
   */
   function ignore_user_del($user_id)
   {
     mysql_query("delete from `".LTChat_Main_prefix."ignore` where `from_users_id` = '{$_SESSION['LTChart_user_id']}' and `to_users_id` = '{$user_id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }

  /**
   * LTChatDataKeeper::ignore_user_add()
   *
   * @param mixed $user_id
   * @return
   */
   function ignore_user_add($user_id)
   {
     mysql_query("INSERT INTO `".LTChat_Main_prefix."ignore` (from_users_id, to_users_id, chat_id) values ('{$_SESSION['LTChart_user_id']}','{$user_id}','".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }
################## ignore #############################################

//----------------- wait ---------------------------------------------

  /**
   * LTChatDataKeeper::check_wait_list()
   *
   * @param mixed $id
   * @return
   */
   function check_wait_list($id)
   {
   $result = mysql_query("select * from `".LTChat_Main_prefix."wait` where `users_id` = '{$_SESSION['LTChart_user_id']}' AND chat_id = '".LTChat_Main_CHAT_ID."' LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   
	   if($check = mysql_fetch_object($result)){
	   $nick_ip = $check->nick;
	   }
       $Array = explode(",", $check->nick);
	   if(in_array($id, $Array)){
	   return FALSE;
	   }else{
	   return TRUE;
	   } 
   }

  /**
   * LTChatDataKeeper::wait_user_add()
   *
   * @param mixed $newmember
   * @return
   */
   function wait_user_add($newmember)
   {
     $result = mysql_query("select * from `".LTChat_Main_prefix."wait` where `users_id` = '{$_SESSION['LTChart_user_id']}' AND chat_id = '".LTChat_Main_CHAT_ID."' LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		 
	if ( mysql_num_rows($result) == 1 ) { 
	  if($WAIT = mysql_fetch_object($result)) 
		$Array = explode(",", $WAIT->nick);
		array_push($Array,"$newmember");
		$NewNicks = implode(",", $Array);
	
    mysql_query("update `".LTChat_Main_prefix."wait` set `nick` = '{$NewNicks}' where `users_id` = '{$_SESSION['LTChart_user_id']}' AND chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	
	}else{

    mysql_query("INSERT INTO `".LTChat_Main_prefix."wait` (nick , ip, users_id, chat_id) values ('{$newmember}', '', '{$_SESSION['LTChart_user_id']}', '".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	}

   }

  /**
   * LTChatDataKeeper::wait_user_del()
   *
   * @param mixed $removemember
   * @return
   */
   function wait_user_del($removemember)
   {

     $result = mysql_query("select * from `".LTChat_Main_prefix."wait` where `users_id` = '{$_SESSION['LTChart_user_id']}' AND chat_id = '".LTChat_Main_CHAT_ID."' LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 
	if ( mysql_num_rows($result) == 1 ) { 
		if($WAIT = mysql_fetch_object($result)) 
		$Array = explode(",", $WAIT->nick);
		$NewArray = array();
		
		if ( in_array($removemember, $Array) ) {
	        foreach ( $Array as $Value ) {
				if ( $Value != $removemember ) {
					array_push($NewArray, "$Value");
					$NewNicks = implode(",", $NewArray);
				}
			}
			
    mysql_query("update `".LTChat_Main_prefix."wait` set `nick` = '{$NewNicks}' where `users_id` = '{$_SESSION['LTChart_user_id']}' AND `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

		}##in_array
     }##num rows
   }
################## wait #############################################


//----------------- all msg --------------------------------------------

  /**
   * LTChatDataKeeper::send_allmsg()
   *
   * @param mixed $sender_id
   * @param mixed $text
   * @param mixed $room
   * @return
   */
function send_allmsg($sender_id, $text, $room)
{		
        $users_data = $this->get_user_by_id($sender_id);
        $maskip = $this->get_rights_by_level($users_data->level); //get right by level

		$result = mysql_query("SELECT `W`.`users_id`, `W`.`online`,U.`nick`,U.`level`,U.`id`,U.`rights` FROM `".LTChat_Main_prefix."who_is_online` W, `".LTChat_Main_prefix."users` U WHERE W.online = '1' and W.users_id = U.id and `W`.`chat_id` = '".LTChat_Main_CHAT_ID."' and U.chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	    $i = 0;
	    while ($row = mysql_fetch_object($result)){
		  if($row->level >= 0){
		  $msg = str_replace(array("#nickcolor#","#nickfont#","#sender_name#","#rights#","#color#","#font#","#text#"), 
		                    array($users_data->nickcolor ,$users_data->nickfont , $users_data->nick, $maskip, $users_data->color, $users_data->font, $text), ChFun_allmsg);
		  
		  $final = addslashes($msg);
		  mysql_query("INSERT INTO `".LTChat_Main_prefix."private_talk` 
		  (`users_id_from` , `users_id_to` , `text` , `time`, `delivered_from`, chat_id) 
		  VALUES 
          ('50','{$row->id}', '{$final}', '".time()."', '1','".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		  $i++;
		  }	
		}
		$this->post_logs($msg, $room, 'allmsg', FALSE);	
		return array('info' => $i);
}

################## all msg #############################################

//----------------- user msg --------------------------------------------

  /**
   * LTChatDataKeeper::send_usermsg()
   *
   * @param mixed $sender_id
   * @param mixed $text
   * @param mixed $room
   * @return
   */
function send_usermsg($sender_id, $text, $room)
{		
		$users_data = $this->get_user_by_id($sender_id);
        $maskip = $this->get_rights_by_level($users_data->level); //get right by level
		
		$result = mysql_query("SELECT `W`.`users_id`, `W`.`online`,U.`nick`,U.`level`,U.`id`,U.`rights` FROM `".LTChat_Main_prefix."who_is_online` W, `".LTChat_Main_prefix."users` U WHERE W.online = '1' and W.users_id = U.id and `W`.`chat_id` = '".LTChat_Main_CHAT_ID."' and U.chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	    $i = 0;
	    while ($row = mysql_fetch_object($result)){
		  if($row->level == 0){
		  $msg = str_replace(array("#nickcolor#","#nickfont#","#sender_name#","#rights#","#color#","#font#","#text#"), 
		                    array($users_data->nickcolor ,$users_data->nickfont , $users_data->nick, $maskip, $users_data->color, $users_data->font, $text), ChFun_guestmsg);
		  
		  $final = addslashes($msg);
		  mysql_query("INSERT INTO `".LTChat_Main_prefix."private_talk` 
		  (`users_id_from` , `users_id_to` , `text` , `time`, `delivered_from`, chat_id) 
		  VALUES 
          ('50','{$row->id}', '{$final}', '".time()."', '1','".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		    $i++;
		    }
			
		}
		$this->post_logs($msg, $room, 'usermsg', FALSE);
		return array('info' => $i); 		  
}

################## user msg #############################################

//----------------- operator msg --------------------------------------------

  /**
   * LTChatDataKeeper::send_opmsg()
   *
   * @param mixed $sender_id
   * @param mixed $text
   * @param mixed $room
   * @return
   */
function send_opmsg($sender_id, $text, $room)
{		
		$users_data = $this->get_user_by_id($sender_id);
        $maskip = $this->get_rights_by_level($users_data->level); //get right by level
		
		$result = mysql_query("SELECT `W`.`users_id`, `W`.`online`,U.`level`,U.`id`,U.`rights` FROM `".LTChat_Main_prefix."who_is_online` W, `".LTChat_Main_prefix."users` U WHERE W.online = '1' and W.users_id = U.id and `W`.`chat_id` = '".LTChat_Main_CHAT_ID."' and U.chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	    $i = 0;
	    while ($row = mysql_fetch_object($result)){
		  if($row->level > 0){
		  $msg = str_replace(array("#nickcolor#","#nickfont#","#sender_name#","#rights#","#color#","#font#","#text#"), 
		                    array($users_data->nickcolor ,$users_data->nickfont , $users_data->nick, $maskip, $users_data->color, $users_data->font, $text), ChFun_opmsg);
		  
		  $final = addslashes($msg);
		  mysql_query("INSERT INTO `".LTChat_Main_prefix."private_talk` 
		  (`users_id_from` , `users_id_to` , `text` , `time`, `delivered_from`, chat_id) 
		  VALUES 
          ('50','{$row->id}', '{$final}', '".time()."', '1','".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		    $i++;
			}
		}
		$this->post_logs($msg, $room, 'opmsg', FALSE);	
		return array('info' => $i); 	  
}

################## operator msg #############################################

  /**
   * LTChatDataKeeper::post_private_msg()
   *
   * @param mixed $text
   * @param mixed $private_id
   * @param mixed $delivered
   * @return
   */
   function post_private_msg($text, $private_id, $delivered)
   {
   	 mysql_query("update ".LTChat_Main_prefix."users set posted_msg = posted_msg + 1 where id = '{$_SESSION['LTChart_user_id']}'and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

   	 if($delivered)		$delivered = 1;
   	 else				$delivered = 0;

     $user = $_SESSION['LTChart_user_nick'];

     $room = addslashes($room);
     $user = addslashes($user);
     $text = addslashes($text);
	 $time = time();


	 if($private_id >= 0 && trim($text) != "")
	 {
	   $result = mysql_query("select * from `".LTChat_Main_prefix."ignore` where 
(from_users_id = '{$_SESSION['LTChart_user_id']}' and to_users_id = '{$private_id}') or 
(from_users_id = '{$private_id}' and to_users_id = '{$_SESSION['LTChart_user_id']}')
and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	   
	   if($row = mysql_fetch_object($result))
	   {
	   	 if($row->to_users_id == $_SESSION['LTChart_user_id'])	$text = "/ERROR ignore ".ERROR_ignore_from;
	   	 if($row->to_users_id == $private_id)					$text = "/ERROR ignore ".ERROR_ignore_to;
	   }
	   
//	   if($this->whois_have_action($_SESSION['LTChart_user_id'], 'jail'))
//	   {
//		$text = "/ERROR jail";
//	   }

	   $insert_msg = "INSERT INTO `".LTChat_Main_prefix."private_talk` (`users_id_from` , `users_id_to` , `text` , `time`, `delivered_from`, chat_id) VALUES 
       											    ('{$_SESSION['LTChart_user_id']}','{$private_id}', '{$text}', '{$time}', '{$delivered}','".LTChat_Main_CHAT_ID."')";
       mysql_query($insert_msg) or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
       return $text;
	 }
	 else
     {
       return $text;
     }
   }
   
	// check user founded in who_is_online table by id
  /**
   * LTChatDataKeeper::online()
   *
   * @param mixed $id
   * @return
   */
	function online($id){
	
		   $result = mysql_query("select * from `".LTChat_Main_prefix."who_is_online` where `users_id` = '{$id}' and `online` = '1' and `chat_id` = '".LTChat_Main_CHAT_ID."' LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		    if(mysql_num_rows($result) > 0){
			   return true;
			  }else{
			   return false;
			 }
	}
	
  /**
   * LTChatDataKeeper::get_user_style()
   *
   * @param mixed $nick
   * @return
   */
	function get_user_style($nick){
		 
		$result = mysql_query("SELECT u.nickfont, u.nickcolor, u.font, u.color, u.id, u.nick, w.users_id FROM `users` u , `who_is_online` w WHERE u.id = w.users_id AND u.nick = '{$nick}' LIMIT 1;") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	
		 while ($row = mysql_fetch_object($result))
		   $out[] = $row;
	
		 return $out;
	}
	
  /**
   * LTChatDataKeeper::update_style_fields()
   *
   * @param mixed $other_options
   * @param mixed $id
   * @return
   */
   function update_style_fields($other_options, $id)
   {
   	   mysql_query("update `".LTChat_Main_prefix."users` set color = '{$_POST['color']}', font = '{$_POST['font']}', nickcolor = '{$_POST['nickcolor']}', nickfont = '{$_POST['nickfont']}' where id = '{$id}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

   }
	
  /**
   * LTChatDataKeeper::get_user_status()
   *
   * @param mixed $id
   * @return
   */
	function get_user_status($id){
		 		
	 $result = mysql_query("SELECT * from `".LTChat_Main_prefix."check` WHERE `away` = '1' AND `users_id` = '{$id}' and chat_id = '".LTChat_Main_CHAT_ID."' LIMIT 1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	
		 if($row = mysql_fetch_object($result)){
		   $away = $row->away;
		   }
		  return $away;
	}
   
  /**
   * LTChatDataKeeper::check_saved()
   *
   * @return
   */
   function check_saved()
   {
   	$result = mysql_query("select * from `".LTChat_Main_prefix."logs` where `type` = 'abuse' OR `type` = 'kick' OR `type` = 'banuser' OR `type` = 'mkick' OR `type` = 'banpcuser' OR `type` = 'banpcip' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     while ($row = mysql_fetch_array($result))
		$out[] = $row;

     return $out;
   }
   
   /*-------------------------------------------------------------------------*/
   // get operator action's logs (kick, banuser, mkick, unbanip, unbanuser, banpcuser, banpcip, unbanpcuser, unbanpcip)
   /*-------------------------------------------------------------------------*/
  /**
   * LTChatDataKeeper::get_logs()
   *
   * @return
   */
   function get_logs()
   {
   	$result = mysql_query("select * from `".LTChat_Main_prefix."logs` where `type` = 'kick' OR `type` = 'banuser' OR `type` = 'mkick' OR `type` = 'unbanip'  OR `type` = 'unbanuser' OR `type` = 'banpcuser' OR `type` = 'banpcip' OR `type` = 'unbanpcuser' OR `type` = 'unbanpcip' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     while ($row = mysql_fetch_array($result))
		$out[] = $row;

     return $out;
   }
   
   /*-------------------------------------------------------------------------*/
   // get operator action's logs (upgrade, downgrade)
   /*-------------------------------------------------------------------------*/
  /**
   * LTChatDataKeeper::updologs()
   *
   * @return
   */
   function get_updologs()
   {
   	$result = mysql_query("select * from `".LTChat_Main_prefix."logs` where (`type` = 'upgrade') OR (`type` = 'downgrade') and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     while ($row = mysql_fetch_array($result))
		$out[] = $row;

     return $out;
   }
   
  /*-------------------------------------------------------------------------*/
  // get operator stoped logs (jail, sus, unsus, kill)
  /*-------------------------------------------------------------------------*/
   
  /**
   * LTChatDataKeeper::get_stoped_logs()
   *
   * @return
   */
   function get_stoped_logs()
   {
   	$result = mysql_query("SELECT * from `".LTChat_Main_prefix."logs` where `type` = 'jail' OR `type` = 'sus' OR `type` = 'unsus' OR `type` = 'kill' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     while ($row = mysql_fetch_array($result))
		$out[] = $row;

     return $out;
   }
   
   /*-------------------------------------------------------------------------*/
   // get the apply logs
   /*-------------------------------------------------------------------------*/
	
  /**
   * LTChatDataKeeper::get_apply_logs()
   *
   * @return
   */
   function get_apply_logs()
   {
   	$result = mysql_query("SELECT * from `".LTChat_Main_prefix."logs` where `type` = 'apply' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     while ($row = mysql_fetch_array($result))
		$out[] = $row;

     return $out;
   }
   
  /**
   * LTChatDataKeeper::get_forward_logs()
   *
   * @return
   */
   function get_forward_logs()
   {
   	$result = mysql_query("select * from `".LTChat_Main_prefix."logs` WHERE `type` = 'forward' AND `action_time` = action_time AND `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     while ($row = mysql_fetch_array($result))
		$out[] = $row;

     return $out;
   }
   
  /**
   * LTChatDataKeeper::flood_control()
   *
   * @param mixed $room
   * @return
   */
   function flood_control($room)
   {
     $flood = 0;
   	 //check the level for flood control
	 if($this->get_user_level() >= 0 && $this->get_user_level() <= 6)
	 {
	 $result = mysql_query("SELECT time FROM `".LTChat_Main_prefix."talk` WHERE `user` = '{$_SESSION['LTChart_user_nick']}' and `room` = '{$room}' order by id DESC LIMIT 0,1 ");
	 //check the mysql fetch
	 if($row = mysql_fetch_row($result))
		 if ($row['0'] > (time() - 4)){
		 $this->insert_check4action($_SESSION['LTChart_user_id'], 'flood'); //insert check for flood
		 $flood = 1;
		 }
	 }##end check the level for flood control
	 return $flood;
   }
   
  /**
   * LTChatDataKeeper::post_msg()
   *
   * @param mixed $text
   * @param mixed $room
   * @param mixed $private_id_checked
   * @return
   */
   function post_msg($text, $room, $private_id_checked)
   {	 
   	 mysql_query("update ".LTChat_Main_prefix."users set posted_msg = posted_msg + 1 where id = '{$_SESSION['LTChart_user_id']}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

     $user = $_SESSION['LTChart_user_nick'];
     $user = addslashes($user);
     $room = addslashes($room);
     $text = addslashes($text);
	 $time = time();

	 $this->check_private_id($private_id_checked);
	 
	 if(trim($text) != "")
     {
		mysql_query("INSERT INTO `".LTChat_Main_prefix."talk` 
		(`user` , `room` , `text` , `time`, `chat_id`) 
		VALUES 
		('{$user}','{$room}', '{$text}', '{$time}','".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		return true;
     }
     else 
       return false;
   }
   
  /**
   * LTChatDataKeeper::post_logs()
   *
   * @param mixed $reason
   * @param mixed $room
   * @param mixed $type
   * @param mixed $action_on
   * @return
   */
   function post_logs($reason, $room, $type, $action_on)
   {
     $reason = addslashes($reason);
	 $type = addslashes($type);
	 $room = addslashes($room);
	 $action_time = time();
     $action_by = addslashes($_SESSION['LTChart_user_nick']);
	 $action_on = addslashes($action_on);

	 if(trim($reason) != "")
     {
		mysql_query("INSERT INTO `".LTChat_Main_prefix."logs` 
		(`reason` , `type` , `room` , `action_time`, `action_by`, `action_on`, `chat_id`) 
		VALUES 
		('{$reason}','{$type}', '{$room}', '{$action_time}', '{$action_by}', '{$action_on}', '".LTChat_Main_CHAT_ID."')
		
		") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		return true;
     }
     else 
       return false;
   }
   
  /**
   * LTChatDataKeeper::post_private_logs()
   *
   * @param mixed $text
   * @return
   */
   function post_private_logs($text)
   {
        $users_data = $this->get_user_by_id($sender_id);
        $maskip = $this->get_rights_by_level($users_data->level); //get right by level

		$result = mysql_query("SELECT `W`.`users_id`, `W`.`online`,U.`nick`,U.`level`,U.`id` FROM `".LTChat_Main_prefix."who_is_online` W, `".LTChat_Main_prefix."users` U WHERE W.users_id = U.id and `W`.`chat_id` = '".LTChat_Main_CHAT_ID."' and W.online = '1' and U.chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

	    while ($row = mysql_fetch_object($result)){
		  if($row->level > 0){
		  
		  $final = addslashes($text);
		  mysql_query("INSERT INTO `".LTChat_Main_prefix."private_talk` 
		  (`users_id_from` , `users_id_to` , `text` , `time`, `delivered_from`, chat_id) 
		  VALUES 
          ('50','{$row->id}', '{$final}', '".time()."', '1','".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		  }	
		}
   }
   
  /**
   * LTChatDataKeeper::post_reason()
   *
   * @param mixed $text
   * @param mixed $room
   * @return
   */
   function post_reason($text, $room)
{
     $user = 'Chat System';
     $user = addslashes($user);
     $room = addslashes($room);
     $text = addslashes($text);
	 $time = time();

	 if(trim($text) != "")
     {
		mysql_query("INSERT INTO `".LTChat_Main_prefix."talk` (`user` , `room` , `text` , `time`, `chat_id`) VALUES ('{$user}','{$room}', '{$text}', '{$time}','".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		return true;
     }
     else 
       return false;
   }
   
  /**
   * LTChatDataKeeper::post_private_reason()
   *
   * @param mixed $text
   * @param mixed $private_id
   * @param mixed $delivered
   * @return
   */
   function post_private_reason($text, $private_id, $delivered)
   {

   	 if($delivered)		$delivered = 1;
   	 else				$delivered = 0;

     $text = addslashes($text);
	 $time = time();

	 if($private_id >= 0 && trim($text) != "")
	 {

	   $insert_msg = "INSERT INTO `".LTChat_Main_prefix."private_talk` 
	   (`users_id_from` , `users_id_to` , `text` , `time`, `delivered_from`, chat_id) 
	   VALUES 
	   ('50','{$private_id}', '{$text}', '{$time}', '{$delivered}','".LTChat_Main_CHAT_ID."')";
       mysql_query($insert_msg) or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
       return $text;
	 }
	 else
     {
       return $text;
     }
   }
   
/*-------------------------------------------------------------------------*/
// Post the trace logs (Mask ip, REMOTE_ADDR, HTTP_X_FORWARDED_FOR)
/*-------------------------------------------------------------------------*/
  /**
   * LTChatDataKeeper::post_trace_logs()
   *
   * @param mixed $nick
   * @param mixed $room
   * @return
   */
   function post_trace_logs($nick, $room)
   {
    $time = time();
	$date = date('l F Y h:i:s A', $time);
	
	$info = $this->get_user_by_nick($nick);
	$rights = $this->get_rights_by_level($info->level);
		   
    $trace = str_replace(array("#rights#","#user#","#maskip#","#room#","#time#"),
	                     array($rights, $info->nick, $info->rights, $room, $date), ChFun_trace_logs);   
	$trace2 = str_replace(array("#rights#","#user#","#remote_addr#","#room#","#time#"),
	                      array($rights, $info->nick, $info->last_ip, $room, $date), ChFun_trace2_logs);
	$trace4 = str_replace(array("#rights#","#user#","#pcip#","#room#","#time#"),
	                      array($rights, $info->nick, $info->last_pcip, $room, $date), ChFun_trace4_logs);
						  
	addmsg('trace',FALSE,$trace);
	addmsg('trace2',FALSE,$trace2);
	addmsg('trace4',FALSE,$trace4);
   }
   
  /**
   * LTChatDataKeeper::get_users_online_list()
   *
   * @return
   */
   function get_users_online_list()
   {
     $query_select = "SELECT `O`.`users_id`, `O`.`online`, `O`.`room`, `O`.`action_time`, U.`nick`, U.`id`, U.`last_ip`, U.`level`, U.`comment` FROM `".LTChat_Main_prefix."who_is_online` O, `".LTChat_Main_prefix."users` U WHERE O.users_id = U.id and `O`.`chat_id` = '0' and O.online = '1' and U.chat_id = '".LTChat_Main_CHAT_ID."' order by who_id asc";

		$result = mysql_query($query_select) or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	    while ($row = mysql_fetch_array($result)){
	      $out[] = $row;
   	    }
	  return $out;
   }

  /**
   * LTChatDataKeeper::get_users_list_from_array()
   *
   * @param mixed $users
   * @return
   */
   function get_users_list_from_array($users)
   {
   	
	  $time = time();
   	  if(is_array($users))
   	  {
        foreach ($users as $id)
          $where .= " or O.users_id = '{$id}' ";
		
        $query_select = "SELECT `O`.`who_id` as id, `O`.`users_id`, `O`.`online`, `O`.`room`, `O`.`action_time`, U.`nick`,U.`picture_url`,U.`id` as user_id FROM `".LTChat_Main_prefix."who_is_online` O, `".LTChat_Main_prefix."users` U WHERE O.users_id = U.id and (1=2 {$where}) and `O`.`chat_id` = '".LTChat_Main_CHAT_ID."' and O.online = '1' and U.chat_id = '".LTChat_Main_CHAT_ID."' order by who_id asc";

		$result = mysql_query($query_select) or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	    while ($row = mysql_fetch_object($result))
	      $out[] = $row;
   	  }
	  return $out;
   }
   
   
  /**
   * LTChatDataKeeper::get_users_list()
   *
   * @param mixed $room
   * @param integer $who_id
   * @return
   */
   function get_users_list($room = NULL, $who_id = 0)
   {
   	 $time = time();

	 if($who_id == 0)	   $query_select = "SELECT `O`.`who_id` as id, `O`.`users_id`, `O`.`online`, `O`.`room`, `O`.`action_time`, U.`nick`,U.`picture_url`,U.`level`,U.`rights`,U.`id` as user_id FROM `".LTChat_Main_prefix."who_is_online` O, `".LTChat_Main_prefix."users` U WHERE O.users_id = U.id and O.online = '1' and `O`.`room` = '{$room}' and `O`.`chat_id` = '".LTChat_Main_CHAT_ID."' and U.chat_id = '".LTChat_Main_CHAT_ID."' order by who_id asc";
	 else				   $query_select = "SELECT `O`.`who_id` as id, `O`.`users_id`, `O`.`online`, `O`.`room`, `O`.`action_time`, U.`nick`,U.`picture_url`,U.`level`,U.`rights`,U.`id` as user_id FROM `".LTChat_Main_prefix."who_is_online` O, `".LTChat_Main_prefix."users` U WHERE O.users_id = U.id and O.who_id > '{$who_id}' and `O`.`room` = '{$room}' and `U`.`chat_id` = '".LTChat_Main_CHAT_ID."' and `O`.`chat_id` = '".LTChat_Main_CHAT_ID."' group by online order by who_id asc";

	 $result = mysql_query($query_select) or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
     while ($row = mysql_fetch_object($result))
       $out[] = $row;

     return $out;
   }
     
  /**
   * LTChatDataKeeper::get_shoutbox_elements()
   *
   * @param mixed $sbox_id
   * @param mixed $lastid
   * @return
   */
   function get_shoutbox_elements($sbox_id,  $lastid)
   {
   	 if($lastid == 0)  $limit = get_ConfVar("ChDK_max_SB_msg_on_enter");
   	 else 			   $limit = get_ConfVar("ChDK_max_SB_msg_get");

	 $result = mysql_query("select *,".LTChat_Main_prefix."shoutbox.nick as user from ".LTChat_Main_prefix."shoutbox where shout_id = '{$sbox_id}' and id > '{$lastid}' and chat_id = '".LTChat_Main_CHAT_ID."' order by id desc limit 0, {$limit}") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 while ($row = mysql_fetch_object($result))
	   $out[] = $row;

	 return $out;
   }
   
  /**
   * LTChatDataKeeper::get_msg_elements()
   *
   * @param mixed $room
   * @param mixed $lastid
   * @return
   */
   function get_msg_elements($room, $lastid)
   {
   	 if($lastid == 0)
   	 {
   	 	//$time_back = " and time > ".time()."-".get_ConfVar("ChDK_max_msg_time_back");
   	 	$limit = get_ConfVar("ChDK_max_msg_on_enter");
   	 }
   	 else
   	   $limit = get_ConfVar("ChDK_max_msg_get");
	 
	 $result = mysql_query("select * from ".LTChat_Main_prefix."talk where room = '{$room}' and chat_id = '".LTChat_Main_CHAT_ID."' and id > '{$lastid}' order by id desc limit 0, {$limit}") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 while ($row = mysql_fetch_object($result))
	   $out[] = $row;

	 return $out;
   }
   
  /**
   * LTChatDataKeeper::get_msg_reason()
   *
   * @return
   */
   function get_msg_reason()
   {	 
	 $result = mysql_query("select * from ".LTChat_Main_prefix."talk where user = 'Chat_System'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 while ($row = mysql_fetch_object($result))
	   $out[] = $row;

	 return $out;
   }
   
  /**
   * LTChatDataKeeper::get_prv_msg_elements()
   *
   * @param mixed $lastid
   * @param integer $private_id
   * @return
   */
   function get_prv_msg_elements($lastid, $private_id = -1)
   {
   	   if($lastid == 0)		$limit = get_ConfVar("ChDK_max_msg_on_enter");
       else					$limit = get_ConfVar("ChDK_max_msg_get");

   	   $result = mysql_query("SELECT U.nick as user, PT.id, PT.users_id_from, PT.users_id_to, PT.text, PT.time, PT.delivered_from, PT.delivered_to FROM `".LTChat_Main_prefix."users` U , `".LTChat_Main_prefix."private_talk` PT WHERE  PT.chat_id = '".LTChat_Main_CHAT_ID."' and U.chat_id = '".LTChat_Main_CHAT_ID."' and PT.users_id_from = U.id  and PT.id > '{$lastid}' and ( ( PT.users_id_from = '{$_SESSION['LTChart_user_id']}' and delivered_from = '0' ) or ( PT.users_id_to = '{$_SESSION['LTChart_user_id']}' and delivered_to = '0' ) ) and (PT.users_id_from = '{$private_id}' or PT.users_id_to = '{$private_id}') order by id desc") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

	   while ($row = mysql_fetch_object($result))
	   {
	   	 if($row->users_id_from == $_SESSION['LTChart_user_id'])
	   	   mysql_query("UPDATE `".LTChat_Main_prefix."private_talk` set delivered_from = '1' where id = {$row->id} and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	   	 
//		 if($row->users_id_to == $_SESSION['LTChart_user_id'])
//	   	   mysql_query("UPDATE `".LTChat_Main_prefix."private_talk` set delivered_to = '1' where id = {$row->id} and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

	   	 $out[] = $row;
	   }
	   return $out;
   }
   
   
  /**
   * LTChatDataKeeper::get_rights_by_level()
   *
   * @param mixed $get_rights
   * @return
   */
	function get_rights_by_level($get_rights)
	{
	$i = "$get_rights";
	do {
		if ($i == 1) {
		  $rights = "Trial op";
		  break;
		}elseif($i>=2 && $i <= 25){
		  $rights = "Operator";
		  break;
		}elseif($i>=26 && $i <= 35){
		  $rights = "Super op";
		  break;
		}elseif($i>=36  && $i <= 40){
		  $rights = "Cop";
		  break;
		}elseif($i>=41  && $i <= 44){
		  $rights = "Super cop";
		  break;
		}elseif($i>=45  && $i <= 47){
		  $rights = "Agent";
		  break;
		}elseif($i == 48){
		  $rights = "Wizard";
		  break;
		}elseif($i == 49){
		  $rights = "Monitor";
		  break;
		}elseif($i == 50){
		  $rights = "Admin";
		  break;
		}else{
		  $rights = "Guest";
		  break;
		}//end if
	} while (0); //end while
	return $rights;
	}//end function

  // Colors for chat users
  /**
   * LTChatDataKeeper::get_user_color()
   *
   * @param mixed $get_level
   * @return
   */
   function get_user_color($get_level) {
   
   	$i = "$get_level";
	do {
		if ($i == 1) {
			$color = "#99A3A7";
			break;
		}else if($i >= 2 && $i <= 4) {
			$color = "#7B8589";
			break;
		}else if($i >= 5 && $i <= 15) {
			$color = "#4E5558";
			break;
		}else if($i >= 16 && $i <= 25) {
			$color = "#333632";
			break;
		}else if($i >= 26 && $i <= 34) {
			$color = "#5681AF";
			break;
		}else if($i >= 35 && $i <= 40) {
			$color = "#002B5F";
			break;
		}else if($i >= 41 && $i <= 45) {
			$color = "#7B6595";
			break;
		}else if($i == 46) {
			$color = "#FA964A";
			break;
		}else if($i == 47) {
			$color = "#DE6100";
			break;
		}else if($i == 48) {
			$color = "#2D6CC0";
			break;
		}else if($i == 49) {
			$color = "#003696";
			break;
		}else if($i == 50) {
			$color = "red";
			break;
		}else{
			$color = "#000000";
			break;
		}//end if
	 } while (0); //end while
	 return $color;
   }

  /**
   * LTChatDataKeeper::get_user_name()
   *
   * @return
   */
   function get_user_name()
   {
     return $_SESSION['LTChart_user_nick'];
   }

  /**
   * LTChatDataKeeper::get_user_id()
   *
   * @return
   */
   function get_user_id()
   {
     return $_SESSION['LTChart_user_id'];
   }
   
  /**
   * LTChatDataKeeper::get_user_rights()
   *
   * @return
   */
   function get_user_rights()
   {
     return $_SESSION['LTChart_user_rights'];
   }
   
  /**
   * LTChatDataKeeper::get_user_level()
   *
   * @return
   */
   function get_user_level()
   {
     return $_SESSION['LTChart_user_level'];
   }
   
  /**
   * LTChatDataKeeper::get_user_disable()
   *
   * @return
   */
   function get_user_disable()
   {
	$result = mysql_query("select * from `".LTChat_Main_prefix."users` where `nick` = '{$_SESSION['LTChart_user_nick']}' and `disable` = '1' and `chat_id` = '".LTChat_Main_CHAT_ID."' LIMIT 1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);

   	 if($row = mysql_fetch_object($result))
   	   return $row->disable;
   }
   
  /**
   * LTChatDataKeeper::delete_message_id()
   *
   * @param mixed $id
   * @param mixed $private_id
   * @return
   */
   function delete_message_id($id, $private_id)
   {
     if($private_id < 0)	mysql_query("delete from ".LTChat_Main_prefix."talk where id = '{$id}' and user = '{$_SESSION['LTChart_user_nick']}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
     else					mysql_query("delete from `".LTChat_Main_prefix."private_talk` where chat_id = '".LTChat_Main_CHAT_ID."' and id = '{$id}' and `users_id_from` = '{$_SESSION['LTChart_user_id']}' or `users_id_to` = '{$_SESSION['LTChart_user_id']}'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }
   
  /**
   * LTChatDataKeeper::delete_private()
   *
   * @return
   */
   function delete_private()
   {
 mysql_query("delete from `".LTChat_Main_prefix."private_talk` where chat_id = '".LTChat_Main_CHAT_ID."' and `users_id_to` = '{$_SESSION['LTChart_user_id']}'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   }
   
  /**
   * LTChatDataKeeper::delete_private_by_id_rec()
   *
   * @return
   */
   function delete_private_by_id_rec($id)
   {
 mysql_query("delete from `".LTChat_Main_prefix."private_talk` where chat_id = '".LTChat_Main_CHAT_ID."' and `id` = '{$id}' LIMIT 1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		 //check affected rows (update, delete)
		 if(mysql_affected_rows() == 1){
	     return true;
		 }else{
		 return false;
		 }
   }
   
  /**
   * LTChatDataKeeper::delete_private_by_id()
   *
   * @return
   */
   function delete_private_by_id()
   {
mysql_query("delete from `".LTChat_Main_prefix."private_talk` where `users_id_to` = '{$_SESSION['LTChart_user_id']}' and `changed` = '1' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		 //check affected rows (update, delete)
		 if(mysql_affected_rows() > 0){
	     return true;
		 }else{
		 return false;
		 }

   } 
   
  /**
   * LTChatDataKeeper::check_private_id()
   *
   * @param mixed $id
   * @return
   */
   function check_private_id($id)
   {
	 if(!empty($id))
	 {
	   $ids = explode(",", $id);
	   while (list($key, $value) = each($ids))
		 {
		  if(!empty($value)){
mysql_query("UPDATE `".LTChat_Main_prefix."private_talk` set changed = '1' where users_id_to = '{$_SESSION['LTChart_user_id']}' and id = '{$value}' and chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
          }
		 }
	 }
   } 
   
  /**
   * LTChatDataKeeper::forward_message_id()
   *
   * @param mixed $f_send_to_id
   * @param mixed $type
   * @param mixed $room
   * @param mixed $f_send_to
   * @return
   */
   function forward_message_id($f_send_to_id, $type, $room, $f_send_to)
   {
   	$name =  $_SESSION['LTChart_user_nick']; //forward sender name
   	$f_send_to_info = $this->get_user_by_id($f_send_to_id); //get info for op forward send
   	
   	//select the user info on private table for send msg
	$result = mysql_query("SELECT * from `".LTChat_Main_prefix."private_talk` WHERE `users_id_to` = '{$_SESSION['LTChart_user_id']}' and `changed` = '1' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	
	$i = 0;
	
	while($row = mysql_fetch_object($result)){ //fetch the select
	
	$users_info = $this->get_user_by_id($row->users_id_from); //get sender forward info
	$maskip = $this->get_rights_by_level($users_info->level); //get right by level
	
	//replace the message forward for insert syntex
	$text = str_replace(array("#f_sender#","#text#","#nickcolor#","#nickfont#","#user_name#","#rights#","#font#","#color#"), array($name ,$row->text, $users_info->nickcolor, $users_info->nickfont, $users_info->nick, $maskip, $users_info->font, $users_info->color), ChFun_msg_forward);
	
	if($i < 1){ //replace the message with start syntex
		 $start = str_replace(array("#f_sender#", "#f_send_to#"), array($name, $f_send_to_info->nick), ChFun_msg_forward_logs_start);
		 
		 addforward($type,FALSE,$start);
	}
	
    //replace the message for insert in forward.html logs
	$msg .= str_replace(array("#text#","#nickcolor#","#nickfont#","#user_name#","#rights#","#font#","#color#"), 
						array($row->text, $users_info->nickcolor, $users_info->nickfont, $users_info->nick, $maskip, $users_info->font, $users_info->color), ChFun_msg_forward_logs);
							
	$text = addslashes($text);
	//insert the message's in private table
	mysql_query("INSERT INTO `".LTChat_Main_prefix."private_talk` 
		   (`id`, `users_id_from` , `users_id_to` , `text` , `time`, `delivered_from`, `chat_id`) 
		   VALUES 
		   (NULL, '50', '{$f_send_to_id}', '{$text}', '".time()."', '1','".LTChat_Main_CHAT_ID."')") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		   $i++;
	}##end while
	
	addforward($type,FALSE,$msg);	
   }
   
  /**
   * LTChatDataKeeper::abuse_public()
   *
   * @param mixed $id
   * @param mixed $room
   * @return
   */
   function abuse_public($id,$room)
   {
	 $result = mysql_query("SELECT * from ".LTChat_Main_prefix."talk WHERE text NOT REGEXP '^[[.slash.]]' AND room = '{$room}' and chat_id = '".LTChat_Main_CHAT_ID."' order by id desc limit 0,30") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	
			 while ($row = mysql_fetch_object($result))
			 {			 
			 $msg = str_replace(array("#mynick#","#text#"),array($row->user,$row->text), abuse);			
				if ($msg) {
		        $filename = $id.time();
				// 1.file name 2.user name 3.message
		        add($filename,FALSE,$msg);
				}				
		     }
   }
   
  /**
   * LTChatDataKeeper::whois_have_action()
   *
   * @param mixed $id
   * @param mixed $row_name
   * @return
   */
   function whois_have_action($id,$row_name)
   { 	 
	 $result = mysql_query("SELECT * from `".LTChat_Main_prefix."check` WHERE `$row_name` = '1' AND `users_id` = '{$id}' and `chat_id` = '".LTChat_Main_CHAT_ID."' LIMIT 0,1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 
	 if(mysql_num_rows($result) > 0){
	 
	 	 while ($row = mysql_fetch_object($result))
	     $get = $row->$row_name;

	 return $get;
	 }else{
	 
	 return false;
	 
	 }
   }
   
  /**
   * LTChatDataKeeper::whois_have_action()
   *
   * @param mixed $id
   * @param mixed $row_name
   * @return
   */
   function whois_have_commands($id)
   { 	 
	 $result = mysql_query("SELECT * FROM `".LTChat_Main_prefix."groups` WHERE `g_id` = (SELECT `mygroup` FROM `".LTChat_Main_prefix."users` WHERE `id` = '{$id}') LIMIT 1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 
	 if(mysql_num_rows($result) == 1){	 
	 	 if($row = mysql_fetch_object($result))

		 return array('crlogs' => $row->crlogs,        'actionstop' => $row->actionstop,   'upgrade' => $upgrade ,
					  'downgrade' => $row->downgrade,  'changepass' => $row->changepass,   'create' => $row->create,                      'showclear' => $row->showclear,  'showfilter' => $row->showfilter,   'check' => $row->check,                      'actionlogs' => $row->actionlogs,'showforward' => $row->showforward, 'usermsg' => $row->usermsg,
					  'opmsg' => $row->opmsg
					  );
	 }else{
	 
	     return false;
	 
	 }
   }
   
  /**
   * LTChatDataKeeper::truncatef()
   *
   * @return
   */
   
   function truncate(){
   
	 mysql_query("TRUNCATE TABLE `".LTChat_Main_prefix."talk`") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
   
   }
   
  /**
   * LTChatDataKeeper::clear_all_public()
   *
   * @return
   */
   function clear_all_public()
   {
    	
    $a = mysql_query("SELECT `W`.`users_id`, `W`.`online`,U.`nick`,U.`id` FROM `".LTChat_Main_prefix."who_is_online` W, `".LTChat_Main_prefix."users` U WHERE W.users_id = U.id and `W`.`chat_id` = '".LTChat_Main_CHAT_ID."' and W.online = '1' and U.chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	
		while ($row = mysql_fetch_array($a)){
			$this->insert_check4action($row['id'], 'clear');
		}
   
   }
   
  /**
   * LTChatDataKeeper::back_from_clear()
   *
   * @return
   */
   function back_from_clear($id)
   {
	 
	 $qurey = mysql_query("SELECT id FROM `users` WHERE `id` in (SELECT users_id FROM `who_is_online` WHERE `online` = '1' and `users_id` = '{$id}' ) LIMIT 1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
        
		while ($back = mysql_fetch_array($qurey)){
mysql_query("update `".LTChat_Main_prefix."check` set clear = '0' where `clear` = '1' and `users_id` = '{$back['id']}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
        }
		        
		if(mysql_affected_rows() > 0){
		return true;
		}else{
		return false;
		}
		
   }
   
  /**
   * LTChatDataKeeper::back_from_filter()
   *
   * @return
   */
   function back_from_filter($id)
   {
	 
	 $qurey = mysql_query("SELECT id FROM `users` WHERE `id` in (SELECT users_id FROM `who_is_online` WHERE `online` = '1' and `users_id` = '{$id}' ) LIMIT 1") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
        
		while ($back = mysql_fetch_array($qurey)){
mysql_query("update `".LTChat_Main_prefix."check` set filter = '0' where `filter` = '1' and `users_id` = '{$back['id']}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
        }
		        
		if(mysql_affected_rows() > 0){
		return true;
		}else{
		return false;
		}
   }
   
  /**
   * LTChatDataKeeper::clear_user_message()
   *
   * @param mixed $nick
   * @param mixed $sender
   * @param mixed $type
   * @return
   */
   function clear_user_message($nick,$sender,$type)
   {
   
    $q = mysql_query("SELECT * from `".LTChat_Main_prefix."talk` WHERE `user` = '{$nick}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	
	if(mysql_num_rows($q) > 0){
	$i = 0;
	 while($clear = mysql_fetch_object($q)){
	    $users_info = $this->get_user_by_nick($clear->user); //get user
		$maskip = $this->get_rights_by_level($users_info->level);
		
		if($i == 0){ //replace the message with start syntex
			 $start = str_replace(array("#user#","#sender#"), array($nick,$sender), ChFun_msg_clear_logs_start);
		}
	     //replace the message for insert in clear.html logs
		 $msg .= str_replace(array("#text#","#nickcolor#","#nickfont#","#user_name#","#rights#","#font#","#color#"), 
							array($clear->text, $users_info->nickcolor, $users_info->nickfont, $users_info->nick, $maskip, $users_info->font, $users_info->color), ChFun_msg_clear_logs);
	 }
	  //add the message with start syntex
	  addmsg($type,FALSE,$start);
	  //add the message in in clear.html logs
	  addmsg($type,FALSE,$msg);
    }
   
	 mysql_query("delete from `".LTChat_Main_prefix."talk` where `user` = '{$nick}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 
     $g = mysql_query("SELECT `W`.`users_id`, `W`.`online`,U.`nick`,U.`id` FROM `".LTChat_Main_prefix."who_is_online` W, `".LTChat_Main_prefix."users` U WHERE W.users_id = U.id and `W`.`chat_id` = '".LTChat_Main_CHAT_ID."' and W.online = '1' and U.chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
        
		if(mysql_num_rows($g) > 1){
			while ($row = mysql_fetch_array($g)){
				$this->insert_check4action($row['id'], $type);
				}
		}
   }
   
  /**
   * LTChatDataKeeper::filter_user_message()
   *
   * @param mixed $word
   * @param mixed $sender
   * @param mixed $type
   * @return
   */
   function filter_user_message($word,$sender,$type)
   {
   
    $q = mysql_query("SELECT * from `".LTChat_Main_prefix."talk` WHERE `user` != 'Chat System' and `text` REGEXP '{$word}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	
	if(mysql_num_rows($q) > 0):
	$i = 0;
	 while($filter = mysql_fetch_object($q)):
	    $users_info = $this->get_user_by_nick($filter->user); //get user
		$maskip = $this->get_rights_by_level($users_info->level);
		
		if($i == 0){ //replace the message with start syntex
			 $start = str_replace(array("#word#","#sender#"), array($word,$sender), ChFun_msg_filter_logs_start);
		}
	     //replace the message for insert in filter.html logs
		 $msg .= str_replace(array("#text#","#nickcolor#","#nickfont#","#user_name#","#rights#","#font#","#color#"), 
							 array($filter->text, $users_info->nickcolor, $users_info->nickfont, $users_info->nick, $maskip, $users_info->font, $users_info->color), ChFun_msg_filter_logs);
	 endwhile;
	  //add the message with start syntex
	  addmsg($type,FALSE,$start);
	  //add the message in in clear.html logs
	  addmsg($type,FALSE,$msg);
     endif;
   
	 mysql_query("delete from `".LTChat_Main_prefix."talk` WHERE `text` REGEXP '{$word}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	 
     $g = mysql_query("SELECT `W`.`users_id`, `W`.`online`,U.`nick`,U.`id` FROM `".LTChat_Main_prefix."who_is_online` W, `".LTChat_Main_prefix."users` U WHERE W.users_id = U.id and `W`.`chat_id` = '".LTChat_Main_CHAT_ID."' and W.online = '1' and U.chat_id = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
        
		if(mysql_num_rows($g) > 1):
			while ($row = mysql_fetch_array($g)):
				$this->insert_check4action($row['id'], $type);
			endwhile;
		endif;
   }   
   
 }
?>