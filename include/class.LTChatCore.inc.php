<?

/**
 * LTChatCore
 *
 * @package Special Chat
 * @author Hany alsamman
 * @copyright (c) 2008 Special Group
 * @version $Id chatcore.inc.php,v 1.2 08/02/2008 09:02 $
 * @access public
 */
 
  class LTChatCore
  {
  /**
  * @param use to get MySQL class functions 
  */  	
	 var $LTChatDataKeeper;
  
  /**
  * @param use to get core functions 
  */	 
	 var $LTChatCoreFunctions;

 /**
  * @param use to add with date() 
  */
  	 var $months;
	   	 
 /**
  * @param for load stop word section
  */	   
	 var $stop_words;
	 
 /**
  * @param for get stop_word function
  */
  	 var $stop_words_loaded = false;
	 
 /**
  * @param use for create GLOBALS string
  */
     var $LTChatConfig;
     
 /**
  * @param use for load chat config
  */
     var $LTChatConfig_loaded = false;  	 

  /**
   * LTChatCore::LTChatCore()
   *
   * @return
   */
  	 function LTChatCore()
  	 {
  	   //Create class LTChatDataKeeper
       $this->LTChatDataKeeper = new LTChatDataKeeper();
       //Get the initialize and connect to database
       $this->LTChatDataKeeper->initialize();
       //Get chat config variables
       $GLOBALS['LTChatConfig'] = $this->LTChatDataKeeper->get_chat_variables();
       //Check lang file exists
       if(!file_exists(LTChart_path."/lang/".get_ConfVar("LTChatLanguage")."/LTChatlangConfig.inc.php")){
       //Set english default lang if no lang file dir exists
	   define("LTChatLanguage", "english");
       }else{
       //Get the lang define from config file
	   define("LTChatLanguage", get_ConfVar("LTChatLanguage"));
	   }

       //Include lang config
 	   include_once(LTChart_path."/lang/".LTChatLanguage."/LTChatlangConfig.inc.php");
       //Include commands
 	   include_once(LTChart_path."/include/LTChatLangArray.inc.php"); 	   
       //Check template string
 	   if(get_ConfVar("LTChatTemplateName") == null){
 	   //Select standard if nothing
	   define("LTChatTemplateName", "standard");
       }else{
       //Select template name from config file
	   define("LTChatTemplateName", get_ConfVar("LTChatTemplateName"));
	   }         

	   //Template system path
       define("LTChatTemplateSystemPath",LTChart_path."/templates/".LTChatTemplateName."/");
	   //Template link path
	   define("LTChatTemplatePath","./templates/".LTChatTemplateName."/");

	   //Get casding style sheet file
 	   if(get_ConfVar("LTTpl_css_link") == null){
 	   //Select default if nothing
	   define("LTTpl_css_link", LTChatTemplatePath."css/default.css");
       }else{
       //Select ccs file name from config
	   define("LTTpl_css_link", get_ConfVar("LTTpl_css_link"));
	   }
       //Require config link file
       require_once(LTChatTemplateSystemPath."/LTChatTplConfig.inc.php"); 	   
       //Get monthes
       $this->months = $GLOBALS['language_config']['months'];
       //check if not array
  	   if(!is_array($this->months)){
  	     $this->months = array();
  	   }
  	   //Create class LTChatCoreFunctions
       $this->LTChatCoreFunctions = new LTChatCoreFunctions($this->LTChatDataKeeper);
       //Set language config
 	   $this->LTChatCoreFunctions->set_language_config($GLOBALS['language_config']);
  	 }

  /**
   * LTChatCore::set_chat_variable()
   *
   * @param mixed $variables
   * @return
   */
  	 function set_chat_variable($variables)
  	 {
  	   //Check session right
  	   if($_SESSION['LTChart_user_rights'] != "Admin"){
  	     return;
  	     }

	   foreach ($variables as $v_name => $value);
  	   {
  	     foreach ($GLOBALS['ConfigVarsInfo'] as $var_name => $info)
  	   	   if($var_name == $v_name)
  	   	     break;
  	   	 if($var_name != $v_name)
  	   	 {
  	   	 	unset($variables[$v_name]);
  	   	 }
  	   	 else 
  	   	 {
  	   	   if($info['type'] == "int" && $value == "".intval($value))
  	   	   {
  	         $this->LTChatDataKeeper->set_chat_variable($var_name, $value);
  	         $GLOBALS['LTChatConfig'][$var_name] = $value;
  	   	   }
  	   	   if($info['type'] == "boolean" && $value == "0" || $value == "1")
  	   	   {
  	         $this->LTChatDataKeeper->set_chat_variable($var_name, $value);
  	         $GLOBALS['LTChatConfig'][$var_name] = $value;
  	   	   }
  	   	 }
  	   }
  	 }
	 
  /**
   * LTChatCore::load_emoticons()
   *
   * @param mixed $MSG
   * @return $MSG with replace number icon (*10*) to img link
   */
	function load_emoticons($MSG) {	
		//The icon name start from 9
		$i = 9;
		//Loop icon name for replace with img link
		while ( $i != 1086 ) {
		$MSG = preg_replace ("/\*($i)\*/", "<img src=\"./emoticons/smiles/icon$1.gif\" title='$1' border='0' align='absmiddle'>", $MSG);
			$i++;
		}
		return $MSG;
	}
	 
  /**
   * LTChatCore::load_stop_words()
   *
   * @return
   */
  	 function load_stop_words()
  	 {
  	   //Check for load stop words
  	   if($this->stop_words_loaded){ 
		 	return;
		 }

  	   //Load emoticons from file link (*g* *b*)
  	   if(file_exists(LTChatTemplateSystemPath."tpl_emoticons.txt"))
  	   {
		  $tpl_emoticons = file(LTChatTemplateSystemPath."tpl_emoticons.txt");
		  foreach ($tpl_emoticons as $line)
		  {
		  	$lines = explode("\t",$line);
		  	if(count($lines) <2) continue;

		  	$from = htmlspecialchars($lines[0]);
		  	$this->stop_words[$from] = str_replace("#path#","./emoticons/smiles/".$lines[count($lines)-1],LTChat_emotStyle);
		  }
  	   }
  	   //Load stop word file if exists
  	   if(file_exists(LTChart_path."/lang/".LTChatLanguage."/stop_words.txt"))
  	   {
		  $f_stop_words = file(LTChart_path."/lang/".LTChatLanguage."/stop_words.txt");
		  foreach ($f_stop_words as $line)
		  {
		  	$lines = explode("\t",$line);
		  	if(count($lines) <2) continue;

		  	$from = htmlspecialchars($lines[0]);
		  	$this->stop_words[$from] = $lines[count($lines)-1];
		  }
  	   }
  	 }
  	 
  /**
   * LTChatCore::set_avatar()
   *
   * @param mixed $f_path
   * @return
   */
  	 function set_avatar($f_path)
  	 {
  	   //Get avatar list
  	   $list = $this->LTChatDataKeeper->get_avatars_list();
  	   if(is_array($list))
  	     foreach ($list as $file_path => $f_name)
  	   	   if($f_path == $file_path)
  	   	     return false;

  	   $ex = explode("/", str_replace("\\","/",$f_path));
  	   if($ex[0] == "." && $ex[1] == "img" && $ex[2] == "avatars" && !isset($ex[4]) && file_exists($f_path))
  	     $this->LTChatDataKeeper->set_avatar($f_path);
  	 }
	 
  /**
   * LTChatCore::get_user_id()
   *
   * @return id of user by seesion
   */
	 function get_user_id()
	 {
	 return $this->LTChatDataKeeper->get_user_id();
	 }
	 
  /**
   * LTChatCore::get_logs()
   *
   * @return operator actions logs
   */
	 function get_logs()
	 {
	 return $this->LTChatDataKeeper->get_logs();
	 }
	 
  /**
   * LTChatCore::get_updologs()
   *
   * @return operator actions logs
   */
	 function get_updologs()
	 {
	 return $this->LTChatDataKeeper->get_updologs();
	 }
	 
  /**
   * LTChatCore::get_stoped_logs()
   *
   * @return operator stoped logs
   */
	 function get_stoped_logs()
	 {
	 return $this->LTChatDataKeeper->get_stoped_logs();
	 }
	 
  /**
   * LTChatCore::get_apply_logs()
   *
   * @return apply new members logs
   */
	 function get_apply_logs()
	 {
	 return $this->LTChatDataKeeper->get_apply_logs();
	 }
	 
  /**
   * LTChatCore::get_users_online_list()
   *
   * @return users online list now
   */
	 function get_users_online_list()
	 {
	 return $this->LTChatDataKeeper->get_users_online_list();
	 }
	 
  /**
   * LTChatCore::post_logs()
   *
   * @param mixed $reason
   * @param mixed $room
   * @param mixed $type
   * @param mixed $action_on
   * @return post logs in database
   */
	 function post_logs($reason, $room, $type, $action_on)
	 {
	 return $this->LTChatDataKeeper->post_logs($reason, $room, $type, $action_on);
	 }
	 
  /**
   * LTChatCore::check_saved()
   *
   * @return logs saved to check
   */
	 function check_saved()
	 {
	 return $this->LTChatDataKeeper->check_saved();
	 }
	 
  /**
   * LTChatCore::showops()
   *
   * @return show members list
   */
	 function showops()
	 {
	 return $this->LTChatDataKeeper->showops();
	 }
	 
  /**
   * LTChatCore::showsus()
   *
   * @return show suspaned members
   */
	 function showsus()
	 {
	 return $this->LTChatDataKeeper->showsus();
	 }
	 
  /**
   * LTChatCore::showban()
   *
   * @return show banned members
   */
	 function showban()
	 {
	 return $this->LTChatDataKeeper->showban();
	 }
	 
  /**
   * LTChatCore::showbanpc()
   *
   * @return show banned members by pcip
   */
	 function showbanpc()
	 {
	 return $this->LTChatDataKeeper->showbanpc();
	 }
	 
  /**
   * LTChatCore::whois_have_commands()
   *
   * @param mixed $id
   * @return get who is have action in check table
   */
	 function whois_have_commands($id)
	 {
	 return $this->LTChatDataKeeper->whois_have_commands($id);
	 }
	 
  /**
   * LTChatCore::whois_have_action()
   *
   * @param mixed $id
   * @param mixed $row_name
   * @return get who is have action in check table
   */
	 function whois_have_action($id,$row_name)
	 {
	 return $this->LTChatDataKeeper->whois_have_action($id,$row_name);
	 }
	 
  /**
   * LTChatCore::showdisable()
   *
   * @return show memebrs disable of power
   */
	 function showdisable()
	 {
	 return $this->LTChatDataKeeper->showdisable();
	 }
	 
  /**
   * LTChatCore::delete_sus()
   *
   * @param mixed $id
   * @param mixed $users_id
   * @return queris for unsuspaned member
   */
	 function delete_sus($users_id)
	 {
	 return $this->LTChatDataKeeper->delete_sus($users_id);
	 }
	 
  /**
   * LTChatCore::delete_ban()
   *
   * @param mixed $id
   * @return queris for unbanned members by (ip|pc)
   */
	 function delete_ban($id)
	 {
	 return $this->LTChatDataKeeper->delete_ban($id);
	 }
	 
  /**
   * LTChatCore::delete_disable()
   *
   * @param mixed $id
   * @param mixed $users_id
   * @return queris for restore the power for an members
   */
	 function delete_disable($id, $users_id)
	 {
	 return $this->LTChatDataKeeper->delete_disable($id, $users_id);
	 }
	 
  /**
   * LTChatCore::get_new_cmail()
   *
   * @return get the new cmail in database
   */
	 function get_new_cmail()
	 {
	 return $this->LTChatDataKeeper->get_new_cmail();
	 }
	 
  /**
   * LTChatCore::get_all_rooms()
   *
   * @return get all rooms in chat
   */
	 function get_all_rooms()
	 {
  	   return array('rooms' => $this->LTChatDataKeeper->get_all_rooms());
	 }
	 
  /**
   * LTChatCore::get_total_time()
   *
   * @return get total time for total_time row in timer table
   */
	 function get_total_time()
	 {
  	   return $this->LTChatDataKeeper->get_total_time();
	 }
	 
  /**
   * LTChatCore::get_total_monthly()
   *
   * @return get monthly days for monthly row in timer table
   */
	 function get_total_monthly()
	 {
  	   return $this->LTChatDataKeeper->get_total_monthly();
	 }
	 
  /**
   * LTChatCore::get_user_color()
   *
   * @param mixed $get_level
   * @return get the color of user by the level
   */
	 function get_user_color($get_level)
	 {
  	   return $this->LTChatDataKeeper->get_user_color($get_level);
	 }
	 
  /**
   * LTChatCore::get_user_level()
   *
   * @return get level to an user by session
   */
	 function get_user_level()
	 {
  	   return $this->LTChatDataKeeper->get_user_level();
	 }
	 
  /**
   * LTChatCore::get_rights_by_level()
   *
   * @param mixed $get_level
   * @return get the right to an user by level
   */
	 function get_rights_by_level($get_level)
	 {
  	   return $this->LTChatDataKeeper->get_rights_by_level($get_level);
	 }

  /**
   * LTChatCore::login_user()
   *
   * @return error message or reason or -> do login queris
   */
  	 function login_user()
  	 {
	 	//check if post the info from guest from
		if($_POST['guest'] == 1){
			$guest = 1; //set guest true
			$login = trim($_POST['userlogin']); //get user name input value
		}else{
			$guest = 0; //set guest false (is member)
			$login = trim($_POST['login']); //get user name input value
			$password = trim($_POST['password']); //get password input value
		}
				
		//check this section when user submit
		if($guest == 1){
			//arabic letters
			$ArabicLetters = array('Ç','Ã','Å','Â','Æ','Á','Ä','ì','É','È','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ý','Þ','ß','á','ã','ä','å','æ','í');
			//replace whitespace with ( _ )
			$login = str_replace(' ','_',$login);
			//check user name login length
			$NickLength = strlen($login);
			//check user name for anything else arabic/english/number
			$result = ereg("^([" . implode('',$ArabicLetters) . "]|[A-Za-z0-9_])+$", $login, $arr);
			//when error return message
			if ( $result != $NickLength ) {
				return ChTPL_RegErrUserBadNick;
			}
		}//end
		
		//check repeated nick for user if online
		if($guest == 1 && $this->LTChatDataKeeper->online($this->LTChatDataKeeper->get_id_by_nick(stripslashes($login)))){
		return ChCore_guest_user_exists;
		}
		
		//check nickname for exists in database (member)
		if($guest == 1 && $this->LTChatDataKeeper->check_member_exists(stripslashes($login))){
		return ChCore_nick_exists_in_database;
		}
		
		//check if not set value the forms
		if(!empty($_POST['userlogin']) || !empty($_POST['login'])){
		$info = $this->LTChatDataKeeper->login_user(stripslashes($login), stripslashes($password), $guest);
		}
				   
				   if($info === true){ return;
				   //check forbbiden
				   }elseif(isset($info['error']) && $info['error'] === ChDK_log_err_guest_account){
					 return 'Guest account forbbiden';
				   //check password
				   }elseif(isset($info['error']) && $info['error'] === ChDK_log_err_bad_password){
					 return ChCore_login_err_bad_pass;
				   //check for bad login
				   }elseif(isset($info['error']) && $info['error'] === ChDK_log_err_bad_login){
					 return ChCore_login_err_bad_login;
				   //check for kick
				   }elseif(isset($info['error']) && $info['error'] === ChDK_log_err_kicked){
					 return $info['reason'];
				   //check for mkick
				   }elseif(isset($info['error']) && $info['error'] === ChDK_log_err_mkicked){
					 return $info['reason'];					 
				   //check for suspaned
				   }elseif(isset($info['error']) && $info['error'] === ChDK_log_err_suspended){
					  return $info['reason'];
				   //check for banned
				   }elseif(isset($info['error']) && $info['error'] === ChDK_log_err_banned){
					  return $info['reason'];
				   }
		}
  	 
  /**
   * LTChatCore::user_logged_in()
   *
   * @return check the seesion (id|name)
   */
  	 function user_logged_in()
  	 {
  	 	return $this->LTChatDataKeeper->user_logged_in();
  	 }
  	 
  /**
   * LTChatCore::get_registration_fields()
   *
   * @return ge registration fields
   */
  	 function get_registration_fields()
  	 {
		return $this->LTChatDataKeeper->get_registration_fields();
  	 }
  	 
  /**
   * LTChatCore::update_other_fields()
   *
   * @param mixed $other_options
   * @return update fields posted
   */
  	 function update_other_fields($other_options)
  	 {
  	   $fields = $this->get_registration_fields();
  	   $to_update = array();

  	   foreach ($fields as $field)
  	   {
  	     if($field->required == 1 && $other_options["form{$field->id}"] == null)
  	       return LTChatCore_user_error_fill_required;

  	     if($field->var_type == 'integer' && $other_options["form{$field->id}"] != "".intval($other_options["form{$field->id}"]))
  	        return LTChatCore_user_error_bad_type;
  	     
  	     $to_update[$field->id] = stripslashes($other_options["form{$field->id}"]);
  	   }

  	   $this->LTChatDataKeeper->update_other_fields($to_update);
  	   return true;
  	 }

  /**
   * LTChatCore::update_style_fields()
   *
   * @param mixed $other_options
   * @return change the style like (type,font ..) to an member
   */
  	 function update_style_fields($other_options, $id)
  	 { 	     
  	   $this->LTChatDataKeeper->update_style_fields($other_options, $id);
  	 }

  /**
   * LTChatCore::add_user()
   *
   * @param mixed $other_options
   * @return queris to add members or user to database
   */
  	 function add_user($other_options)
  	 {		

  	   if(!isset($_POST['create'])) return false;
  	   //stripslashes for nick name
  	   $cr_name = stripslashes($_POST['create']);
  	   //stripslashes for password
  	   $cr_pass = stripslashes($_POST['password']);
       //check length for nickname
  	   if(strlen($cr_name) < LTChatCore_user_min_login_charss){
	   return LTChatCore_user_error_too_short_login;
	   }
	   //check password length
  	   if(strlen($cr_pass) < LTChatCore_user_min_password_chars){
	   return LTChatCore_user_error_too_short_password;
	   }
	   //check email length
  	   if(checkEmailAddress($_POST['email']) == false){
	   return LTChatCore_user_error_email;
	   }
	   
			//arabic letters
			$ArabicLetters = array('Ç','Ã','Å','Â','Æ','Á','Ä','ì','É','È','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ý','Þ','ß','á','ã','ä','å','æ','í');
			//replace whitespace with ( _ )
			$cr_name = str_replace(' ','_', $cr_name);
			//check user name login length
			$NickLength = strlen($cr_name);
			//check user name for anything else arabic/english/number
			$result = ereg("^([" . implode('',$ArabicLetters) . "]|[A-Za-z0-9_])+$", $cr_name, $arr);
			//when error return message
			if ( $result != $NickLength ) {
				return ChTPL_RegErrUserBadNick;
			}
	   
	   //check if arrat exists
  	   if(is_array($other_options)){
	     foreach ($other_options as $ot_id => $ot_ar){
	       if($ot_ar['required'] == 1 && $ot_ar['value'] == null){
			return LTChatCore_user_error_fill_required;
			}
		 }
	   }
       //check if not user added return error message
  	   if($this->LTChatDataKeeper->add_user($cr_name, $cr_pass, $other_options) == false)
  	     return LTChatCore_user_errro_user_exists;
  	   else
  	     return true;
  	 }
	 
	 function register(){
	 
	$query = mysql_query("SELECT `U`.id AS delete_id  FROM `".LTChat_Main_prefix."who_is_online` W, `".LTChat_Main_prefix."users` U WHERE `W`.online = '0' and `U`.level <= '0' and `W`.users_id = `U`.id and `W`.`chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
	
	//check for ChDK_delete_guest_after && ChDK_delete_user_after
	while ($expired = mysql_fetch_object($query)){
	$this->LTChatDataKeeper->delete_user($expired->delete_id);
	}
	 
	 	$user = new User();
		$errors = array();
		
		// we are doing extra validation before saving.
		if (isset($_POST['username'])){
		
			stripslashes(trim($_POST['username']));
			$username = $_POST['username'];
			
			if(strlen($_POST['username']) > 1){
			
			//arabic letters
			$ArabicLetters = array('Ç','Ã','Å','Â','Æ','Á','Ä','ì','É','È','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ý','Þ','ß','á','ã','ä','å','æ','í');
			//replace whitespace with ( _ )
			$username = str_replace(' ','_', $username);
			//check user name login length
			$NickLength = strlen($username);
			//check user name for anything else arabic/english/number
			$result = ereg("^([" . implode('',$ArabicLetters) . "]|[A-Za-z0-9_])+$", $username, $arr);
			//when error return message
			if ( $result != $NickLength ) {
				$errors[] = ChTPL_RegErrUserBadNick;
			}
			
			$user->userName = $username;
			
			} else {
				$errors[] = '<font color="red">Please provide a valid username between 4 and 30 characters.</font>';
			}
		}
		
		if (isset($_POST['password1']) && isset($_POST['password2']) && !empty($_POST['password1'])){
				if ($_POST['password1'] == $_POST['password2']) {
					$user->password = $_POST['password1'];
				} else {
					$errors[] = '<font color="red">The 2 passwords you have entered do not match.</font>';
				}
		}
		
		if(empty($_POST['password1']) && $_POST['auto'] !== 'yes'){
					$errors[] = '<font color="red">passwords you have entered do not match.</font>';
		}
		
		if (isset($_POST['firstname'])){
			stripslashes(trim($_POST['firstname']));
			
			if(strlen($_POST['firstname']) > 1){
				$user->firstName = $_POST['firstname'];
			} else {
				$errors[] = '<font color="red">Please provide a valid username between 4 and 30 characters.</font>';
			}	
		}
		
		if (isset($_POST['emailaddress'])){
			$user->emailAddress = $_POST['emailaddress'];
			
			if (!eregi('^[a-zA-Z]+[a-zA-Z0-9_-]*@([a-zA-Z0-9]+){1}(\.[a-zA-Z0-9]+){1,2}', stripslashes(trim($_POST['emailaddress'])) )) {
				$errors[] = '<font color="red">Please provide a valid email address.</font>';
			} else {
				$email = $_POST['emailaddress'];
			}
		}
			if (empty($errors)) {
			   $output = '<h3>Thank You!</h3>
       You have been registered, you have been sent an e-mail to the address you specified before. Please wait the admin of site activate your account after 30 Min (max)';
			   $user->Save();
				} else {
				
				$output .= '<h3>Error!</h3>
				The following error(s) occured:<br />';
				
				foreach ($errors as $msg) {
					$output .= " - <font color='red' face='arial' size='2'>$msg</font><br /><hr>\n";
				}
				
		}
		return $output;
	 }
  	 
  /**
   * LTChatCore::del_reg_field()
   *
   * @param mixed $id
   * @return drop reg filed from database
   */
  	 function del_reg_field($id)
  	 {
       if($this->LTChatDataKeeper->get_user_level() != "50"){
	   return false;
	   }
       $id = (int)$id;
       //Delete queris
       $this->LTChatDataKeeper->del_reg_field($id);
  	 }

  /**
   * LTChatCore::add_reg_field()
   *
   * @param mixed $post
   * @return add reg filed to database
   */
  	 function add_reg_field($post)
  	 {
       if($this->LTChatDataKeeper->get_user_level() != "50"){
	   return false;
	   }

       foreach ($_POST as $k => $v){
	   $_POST[$k] = htmlspecialchars(stripslashes($v));
	   }
       
       $required = ($_POST['required'] == "on" ) ? '1' : '0';

       $this->LTChatDataKeeper->add_reg_field($_POST['f_name'], $_POST['item'], $required, (int)$_POST['lenght'], trim($_POST['options'],"|"));
  	 }

  /**
   * LTChatCore::user_action()
   *
   * @param mixed $room
   * @return
   */
  	 function user_action($room)
  	 {
  	   return $this->LTChatDataKeeper->user_action($room);
  	 }
  	 
  /**
   * LTChatCore::command_tpl_params()
   *
   * @param mixed $template_name
   * @return
   */
  	 function command_tpl_params($template_name)
     {
	   $help = $GLOBALS['language_config']['help'];
	   foreach ($help as $commands)
	   {
	     if($commands['load_template'] == $template_name && is_callable(array(get_class($this->LTChatCoreFunctions),$commands['execute_tpl_function']))){
		   return call_user_func(array($this->LTChatCoreFunctions,$commands['execute_tpl_function']));
		 }
	   }
	   return array();
     }

  /**
   * LTChatCore::post_shoutbox_msg()
   *
   * @param mixed $text
   * @param mixed $login
   * @param mixed $shoutbox_id
   * @return
   */
     function post_shoutbox_msg($text, $login, $shoutbox_id)
     {
	   $this->LTChatDataKeeper->post_shoutbox_msg($text, $login, (int)$shoutbox_id);
     }

  /**
   * LTChatCore::post_msg()
   *
   * @param mixed $text
   * @param mixed $room
   * @param mixed $private_id
   * @param mixed $private_id_checked
   * @return queris insert the message to public or private
   */
     function post_msg($text, $room, $private_id, $private_id_checked)
     {
       if($this->user_logged_in())
       {
	    $this->LTChatDataKeeper->total_time_update();
		$text = html2txt($text);
		// ADV Finder
		$text = advfinder($text);
		//Rain effects
		$text = preg_replace("/\-rain(.+?)\/rain/ie", "rain(\"$1\");", $text);
	    $check_flood = $this->LTChatDataKeeper->flood_control($room);
		$check_jail = $this->whois_have_action($this->get_user_id(), 'jail');
       	 
		if($private_id < 0){
			if($check_flood == FALSE and $check_jail == FALSE){ //check flood && jail
				$this->LTChatDataKeeper->post_msg($text, $room, $private_id_checked);
			}
	    }else{
			    $this->LTChatDataKeeper->post_private_msg($text, $private_id, false);
		}## private_id
       }## user_logged_in
     }
     
  /**
   * LTChatCore::xml_encode_characters()
   *
   * @param mixed $data
   * @return replace xml
   */
     function xml_encode_characters($data)
     {
       return str_replace(array("#", "&", "*" , "<", ">"),
	                      array("#hash#", "#and#", "#star#", "#pale_open#","#pale_close#"), $data);
     }
     
  /**
   * LTChatCore::get_user_name()
   *
   * @return get user name by session
   */
     function get_user_name()
     {
       return $this->LTChatDataKeeper->get_user_name();
     }
     
  /**
   * LTChatCore::get_user_by_nick()
   *
   * @param mixed $nick
   * @return get user info by nick
   */
     function get_user_by_nick($nick)
     {
       return $this->LTChatDataKeeper->get_user_by_nick($nick);
     }
	 
  /**
   * LTChatCore::get_user_by_id()
   *
   * @param mixed $id
   * @return get user info by id
   */
     function get_user_by_id($id)
     {
       return $this->LTChatDataKeeper->get_user_by_id($id);
     }
	 
  /**
   * LTChatCore::get_user_style()
   *
   * @param mixed $id
   * @return get style for an user like (font,color ...)
   */
     function get_user_style($id)
     {
       return $this->LTChatDataKeeper->get_user_style($id);
     }
     
  /**
   * LTChatCore::msg_to_xml()
   *
   * @param mixed $data_type
   * @param mixed $result
   * @param mixed $room
   * @param mixed $lastid
   * @param mixed $private_id
   * @return
   */
	 function msg_to_xml($data_type, $result, $room, $lastid, $private_id)
	 {
       //addslashes to room
       $room = addslashes($room);
       //get last id
       $lastid = (int)$lastid;
	   //buzz string
	   $buzz = 0;
	   //count of result
       $counter = count($result);
	   //get user level
	   $level = $this->LTChatDataKeeper->get_user_level();
	   
       //check result if array
       if(is_array($result))
         foreach ($result as $row)
         {
           $other_options = "";
           
           //if($data_type == 'shoutbox_msg')	   
		   //$row->user = htmlspecialchars($row->user);

		   $row->user = $this->xml_encode_characters($row->user);
		   //get user style
		   $user_style = $this->get_user_by_nick($row->user);
		   		   
		   $row->time = strtr(date("Y-F-d H:m:s",$row->time), $this->months);

           if($row->text[0] == '/' && $data_type != 'shoutbox_msg')
           {
           	 $is_command = "true";

           	 $command = $this->LTChatCoreFunctions->command($row, $room, $private_id);
           	 if(is_array($command['other_options']))
           	 foreach($command['other_options'] as $option_name => $option_value){
           	   $other_options .= str_replace(array("#value#","#option#"),array($option_value, $option_name),LTChart_xml_data_more_options);
           	 }
           	 if(isset($command['data_type']))
           	   $data_type = $command['data_type'];

           	 if($command['type'] == 'private' && $row->user == $this->LTChatDataKeeper->get_user_name())
           	   $row->text = $command['text'];
           	   
           	 elseif($command['type'] == 'public')
           	   $row->text = $command['text'];
           	   
           	 elseif($command['type'] == 'skip')
           	   continue;
           	   
           	 else
           	   continue;
             $row->text = $this->xml_encode_characters($row->text);
           	 if($command['type'] == 'public')
           	   $is_command = "false";
           }
           else
           {
           	 $is_command = "false";
       	     //$row->text = htmlspecialchars($row->text);
       	     $this->load_stop_words();
			 //$row->text = $this->load_emoticons($row->text);
             $row->text = strtr($row->text, $this->stop_words);
       	     $row->text = $this->xml_encode_characters($row->text);
           }
       	   $data_array[$counter] = str_replace(array("#other_options#","#user#","#user_id#","#time#","#date_now#","#time_now#","#text#","#id#","#is_command#","#LTChatTemplatePath#","#data_type#","#nickfont#","#nickcolor#","#font#","#color#","#rights#","#level#"),
         									   array($other_options, $row->user,$row->user_id, $row->time, $date, $time, $row->text, $row->id, $is_command, LTChatTemplatePath, $data_type, $user_style->nickfont, $user_style->nickcolor, $user_style->font, $user_style->color, $user_style->rights, $level), LTChart_message_xml_data);

           $counter--;
	    }

	   for($i = 1;$i <= count($result); $i++)
	     $data_out .= $data_array[$i];
	
	   return $data_out;
	 }


	 
  /**
   * LTChatCore::get_statistics()
   *
   * @return
   */
	 function get_statistics()
	 {
	   $info = $this->LTChatDataKeeper->get_all_rooms();
	   $stats = $this->LTChatDataKeeper->get_board_statistics();
	   $users_online = $this->LTChatDataKeeper->get_main_users_online();
	 	       
  	   return array('rooms' => $info, 'online' => $users_online, 'stats' => $stats);
	 }
	 
	 
  /**
   * LTChatCore::enter_room()
   *
   * @param mixed $room
   * @return
   */
     function enter_room($room)
     {
       $this->LTChatDataKeeper->enter_room($room);
     }

  /**
   * LTChatCore::get_users_list_xml_elements()
   *
   * @param mixed $room
   * @param mixed $who_id
   * @param bool $private
   * @param integer $user_id
   * @return
   */
     function get_users_list_xml_elements($room, $who_id, $private = false, $user_id = -1)
     {
       if($private){
	     $users_list = $this->LTChatDataKeeper->get_users_list_from_array(array($user_id,$_SESSION['LTChart_user_id']));
       }else{
	     $users_list = $this->LTChatDataKeeper->get_users_list($room, $who_id);
	   }

       if(is_array($users_list))
       {
       	 $friends_list = array();
	     if($who_id == 0)
	     {
	       $friends_list = $this->LTChatDataKeeper->get_friend_list();
	     }

	     if(is_array($users_list))
	       foreach ($users_list as $u_key => $user)
	       {
	         $users_list[$u_key]->action = true;
	         if(is_array($friends_list['from']))
	           foreach ($friends_list['from'] as $f_key => $friend)
	             if($friend->users_id == $user->users_id)
	             {
	               $users_list[$u_key]->friend = true;
	               unset($friends_list['from'][$f_key]);
	               break;
	             }
	       }

		 if(is_array($friends_list['from']))
	       foreach ($friends_list['from'] as $f_key => $friend)
	       {
	         $friend->action = false;
	         $friend->friend = true;
	         $friend->online = false;
	         $users_list[] = $friend;
	       }

	     foreach ($users_list as $user)
	     {
	       $options = "";
	       foreach ($user as $key => $item)
	       {
		     $item = htmlspecialchars($item);
	         $options .= str_replace(array("#value#","#option#"),array($item, $key),LTChart_xml_data_more_options);
	       }
		   $get_user_color = $this->LTChatDataKeeper->get_user_color($user->level);
		   $get_rights_by_level = $this->LTChatDataKeeper->get_rights_by_level($user->level);
		   $my_id = $this->get_user_id();
		   $options .= str_replace(array("#rights#","#option#"),
		                           array($get_rights_by_level,"rights"),LTChart_xml_data_more_rights);
		
		   $options .= str_replace(array("#my_id#","#option#"),
		                           array($my_id,"my_id"),LTChart_xml_data_more_my_id);
		                           
		   $options .= str_replace(array("#color#","#option#"),
		                           array($get_user_color,"users_color"),LTChart_xml_data_more_color);
		                           
	       $options .= str_replace(array("#value#","#option#"),
		                           array(time(),"time_stamp"),LTChart_xml_data_more_options);

	       $data_out .= str_replace("#options#", $options,LTChart_user_xml_data);
	     }
       }
	   return $data_out;
     }
	 
  /**
   * LTChatCore::get_xml_collection()
   *
   * @return
   */
     function get_xml_collection()
     {
	 	   $id = $this->get_user_id();
		   $options = '';
		   
		   $check_actions = $this->LTChatDataKeeper->check_actions($id);
		    
			if(is_array($check_actions)){
			 foreach ($check_actions as $key => $row)
			 {
			   //check for have jail
			   if($row['type'] == $key && $row['jail']){
			   $options .= str_replace(array("#jail#","#rep_col#","#reason#"),
			                           array($row['jail'],"jail"),LTChart_xml_data_jail);
			   }
               //check for have kick
			   if($row['type'] == $key && $row['kick']){
			   $options .= str_replace(array("#kick#","#rep_col#","#reason#"),
			                           array($row['kick'],"kick", strip_tags($row['reason'])),LTChart_xml_data_kick);
			   }
			   //check for have multi kick
			   if($row['type'] == $key && $row['mkick']){
			   $options .= str_replace(array("#mkick#","#rep_col#","#reason#"),
			                           array($row['mkick'],"mkick", strip_tags($row['reason'])),LTChart_xml_data_mkick);
			   }                        
			   //check for have ban for nickname
			   if($row['type'] == $key && $row['banuser']){
			   $options .= str_replace(array("#banuser#","#rep_col#","#reason#"),
			                           array($row['banuser'],"banuser", strip_tags($row['reason'])),LTChart_xml_data_ban);
			   }
			   //check for have ban for my true ip
			   if($row['type'] == $key && $row['banip']){
			   $options .= str_replace(array("#banip#","#rep_col#","#reason#"),
			                           array($row['banip'],"banip", strip_tags($row['reason'])),LTChart_xml_data_banip);
			   }
			   //check for have ban for my pc
			   if($row['type'] == $key && $row['xban']){
			   $options .= str_replace(array("#xban#","#rep_col#","#reason#"),
			                           array($row['xban'],"xban", strip_tags($row['reason'])),LTChart_xml_data_xban);
			   }
			   //check for sus my member
			   if($row['type'] == $key && $row['sus']){
			   $options .= str_replace(array("#sus#","#rep_col#","#reason#"),
			                           array($row['sus'],"sus", strip_tags($row['reason'])),LTChart_xml_data_sus);
			   }
		     }
			}
			   //check for flood on public
			   if($this->whois_have_action($id, 'flood')){
			     $options .= str_replace(array("#flood#","#rep_col#"),
									     array(1,"flood"),LTChart_xml_data_flood);
			   }
			   
			   //check for clear public by nickname
			   if($this->whois_have_action($id, 'clear')){
			   $options .= str_replace(array("#clear#","#rep_col#"),
			                           array(1,"clear"),LTChart_xml_data_clear);
			   }
			   //check for filter message on public by word
			   if($this->whois_have_action($id, 'filter')){
			   $options .= str_replace(array("#filter#","#rep_col#"),
			                           array(1,"filter"),LTChart_xml_data_filter);
			   }
							
				$data_out = str_replace("#rep_col#", $options, LTChart_xml_collection);
				return $data_out;			
     }

  /**
   * LTChatCore::get_my_new_private_messages_xml_elements()
   *
   * @param mixed $last_id
   * @return
   */
     function get_my_new_private_messages_xml_elements($last_id)
     {
       $refresh_counter = 10;
       while($refresh_counter-- != 0)
       {
	     if(($message_list = $this->LTChatDataKeeper->get_my_new_private_messages($last_id)) != null) break;
	     break;
         sleep(1);
       }

       return $this->msg_to_xml('private_msg',$message_list, null, 0, 1);
     }

  /**
   * LTChatCore::get_msg_xml_elements()
   *
   * @param mixed $room
   * @param mixed $lastid
   * @param integer $private_id
   * @return
   */
     function get_msg_xml_elements($room, $lastid, $private_id = -1)
     {
   	   if($private_id < 0){
   	     $result = $this->LTChatDataKeeper->get_msg_elements($room, $lastid);
   	   }else{
   	     $result = $this->LTChatDataKeeper->get_prv_msg_elements($lastid, $private_id);
   	   }
       return $this->msg_to_xml('msg',$result, $room, $lastid, $private_id);
     }

  /**
   * LTChatCore::get_all_private_xml_data()
   *
   * @param mixed $private_id
   * @param mixed $prv_msg_last_id
   * @param mixed $user_status_last_id
   * @return
   */
     function get_all_private_xml_data($private_id, $prv_msg_last_id,  $user_status_last_id)
     {
	   if(!$this->user_logged_in())  return false;

	   if($room != '')
		 $this->LTChatDataKeeper->user_action($room);

	   $data_out = $this->get_msg_xml_elements(null, $prv_msg_last_id, $private_id);
	   if($prv_msg_last_id == 0)
	   	 if($data_out != null)
	       $data_out = $this->get_users_list_xml_elements($room, $user_status_last_id, true, $private_id).$data_out;

	   return str_replace("#data#", $data_out, LTChart_message_xml_header);
     }
     
  /**
   * LTChatCore::get_all_xml_data()
   *
   * @param mixed $room
   * @param mixed $private_id
   * @param mixed $msg_last_id
   * @param mixed $prv_msg_last_id
   * @param mixed $user_status_last_id
   * @return
   */
     function get_all_xml_data($room, $private_id, $msg_last_id, $prv_msg_last_id, $user_status_last_id)
     {
	   if(!$this->user_logged_in())  return false;

	   if($room != '')
	   {
	     $this->LTChatDataKeeper->user_action($room);
	   }

	   $data_out  = $this->get_msg_xml_elements($room, $msg_last_id, $private_id);
	   $data_out .= $this->get_my_new_private_messages_xml_elements($prv_msg_last_id);
	   $data_out .= $this->get_users_list_xml_elements($room, $user_status_last_id);
	   $data_out .= $this->get_xml_collection();

	   return str_replace("#data#", $data_out, LTChart_message_xml_header);
     }

     // ------------------------ soutbox functions ------------------------------
  /**
   * LTChatCore::get_shoutbox_xml_elements()
   *
   * @param mixed $sbox_id
   * @param mixed $msg_last_id
   * @return
   */
     function get_shoutbox_xml_elements($sbox_id, $msg_last_id)
     {
       $result = $this->LTChatDataKeeper->get_shoutbox_elements($sbox_id, $msg_last_id);
       return $this->msg_to_xml('shoutbox_msg', $result, $room, $lastid, $private_id);     
     }
     
  /**
   * LTChatCore::get_shoutbox_xml_data()
   *
   * @param mixed $sbox_id
   * @param mixed $msg_last_id
   * @return
   */
     function get_shoutbox_xml_data($sbox_id, $msg_last_id)
     {
	   $data_out  = $this->get_shoutbox_xml_elements($sbox_id, $msg_last_id);
	   return str_replace("#data#", $data_out, LTChart_message_xml_header);
     }
     // ------------------------ soutbox functions ------------------------------
  /**
   * LTChatCore::set_default_room()
   *
   * @param mixed $room_id
   * @return
   */
     function set_default_room($room_id)
     {
       if($room_id == "")
         return ChFun_croom_ErrNoRoomSel;
       else 
         return $this->LTChatDataKeeper->set_default_room($room_id);
     }
  /**
   * LTChatCore::delete_room()
   *
   * @param mixed $room_id
   * @return
   */
     function delete_room($room_id)
     {
       if($room_id == "")
         return ChFun_croom_ErrNoRoomSel;
       else 
         return $this->LTChatDataKeeper->delete_room($room_id);
     }

  /**
   * LTChatCore::add_room()
   *
   * @param mixed $data
   * @return
   */
     function add_room($data)
     {
       if($this->LTChatDataKeeper->get_user_level() != "50") return false;
     	
       $room_cat = stripslashes($data['rooom_cat']);
       $room_name = stripslashes($data['rooom_name']);
	   if($room_cat == "")  return ChFun_croom_ErrNoCat;
	   elseif ($room_name == "")  return ChFun_croom_ErrNoRoom;
	   elseif (strlen($room_cat) > LTChat_MaxRoomCatName) return ChFun_croom_ErrLenCat;
	   elseif (strlen($room_name) > LTChat_MaxRoomName) return ChFun_croom_ErrLenRoom;
	   else return $this->LTChatDataKeeper->add_room($room_name, $room_cat);
     }
  }
?>