<?
  class LTChatCoreFunctions
  {
  	var $LTChatDataKeeper;
  	var $language_config;

  	function LTChatCoreFunctions(&$LTChatDataKeeper)
  	{
  	  $this->LTChatDataKeeper = &$LTChatDataKeeper;
  	}
  	//---------------------------------------------------------------------
  	
  	function set_language_config($language_config)
  	{
  	  $this->language_config = $language_config;
  	}

  	function command_show_help_info_parse($command, $type)
  	{
  	   if($type == 'list')	$style = ChFun_help_ListStyle;
  	   else 				$style = ChFun_help_InfoStyle;
  	   
  	   if(is_array($command['commands']))
  	     foreach ($command['commands'] as $_commands)
  	       $commands .= "{$_commands} ";

  	   if(is_array($command['params']))
  	     foreach ($command['params'] as $_params)
  	       $params .= "$_params ";

  	   if(is_array($command['except_params_static']))
  	   {
  	     foreach ($command['except_params_static'] as $_except_params_static_key => $_except_params_static_value)
  	     {
  	       if(isset($except_params_static))
  	         $except_params_static .= " | ";

  	       $except_params_static .= "{$_except_params_static_value} ";
  	     }
  	     $except_params_static = "{{$except_params_static}}";
  	   }
  	   
  	   if(is_array($command['Description']))
  	   {
  	     $Description = "";
  	     foreach ($command['Description'] as $key => $desc)
  	     {
  	       $param = $command['except_params_static'][$key];
  	       $Description .= str_replace(array("#param#","#Description#"), array($param, $desc), ChFun_help_DescArStyle);
  	     }
  	   }
  	   else 
  	     $Description = $command['Description'];

  	   return str_replace(array("#commands#","#except_params_static#","#params#","#Description#"), 
	                      array($commands, $except_params_static, $params, $Description), 
						  $style);
  	}

  	function command_show_help($command_info)
  	{
  	  $params = $command_info['params'];

      $help_info = $this->language_config['help'];
      if(!is_array($help_info)) return;

      if(is_array($params))
        foreach ($params as $key => $param)
        {
          $params[$key] = trim($param);
          if($params[$key] == '')
          {  unset($params[$key]);  continue;  }

          if($params[$key][0] != '/')
             $params[$key] = '/'.$params[$key];
        }

      if(!is_array($params) || count($params) == 0)
      {
        foreach ($help_info as $command_help)
        {
          if($command_help[$this->LTChatDataKeeper->get_user_level()] == true && (is_callable(array(get_class($this),$command_help['execute_function'])) || is_callable(array(get_class($this),$command_help['execute_tpl_function']))) )
            $out .= $this->command_show_help_info_parse($command_help,'list');//str_replace(array("#command#","#args#","#description#"), array($com, $args, $desc), ChFun_help_ListStyle);
        }
      }
      else
      {
        foreach ($help_info as $command_help)
        {
          if(!is_callable(array(get_class($this),$command_help['execute_function'])) && !is_callable(array(get_class($this),$command_help['execute_tpl_function']))) continue;
          if($command_help[$this->LTChatDataKeeper->get_user_level()] != true) continue;

          $next = true;
      	  foreach ($command_help['commands'] as $command)
      	  {
      	  	foreach ($params as $key => $param)
      	  	  if($param == $command)
      	  	  {  $next = false;  break;  }
      	  	if($param == $command)  break;
      	  }

      	  if($next) continue;
      	  $out .= $this->command_show_help_info_parse($command_help,'info');//str_replace(array("#command#","#args#","#description#"), array($com, $args, $desc), ChFun_help_InfoStyle);
        }

        if($out == '')
	      $out = str_replace("#command#",implode(" ",$params), ChFun_help_UnknownCommand);
      }
      return array('text' => $out, 'type' => 'private');
  	}
  	//---------------------------------------------------------------------
  	function command_tpl_configreg()
  	{
  	  return array('reg_fields' =>  $this->LTChatDataKeeper->get_registration_fields());
  	}
	//---------------------------------------------------------------------
	function command_upgrade($command_info)
	{
		$row = $command_info['row']	;
		$room = $command_info['room'];
		$type = 'upgrade';
		
		if($row->user == $this->LTChatDataKeeper->get_user_name())
		{
		//delete the command from public
		$this->LTChatDataKeeper->delete_message_id($row->id, -1);
		
		$nick_name_have_upgrade = trim($command_info['params'][1]);
		unset($command_info['params'][1]);
		
		$upgrade_to_level = trim(implode(" ",$command_info['params']));
		
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($nick_name_have_upgrade); //get user
		
		//check for op exists on database
        if($user_info == NULL)
  	  	  return array('text' => str_replace("#user#", $user_info->nick, LTChatCore_user_doesnt_exists), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  	else
  	  	{
		
		if($user_info->level == 0)
		return array('text' => str_replace(array("#user#"), array($user_info->nick), LTChatCOM_stop_upgrade_level_zero), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		
		if($upgrade_to_level > 50)
		return array('text' => LTChatCOM_stop_upgrade_maximum_level, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		
		if($upgrade_to_level <= $user_info->level)
		return array('text' => str_replace(array("#from_level#", "#to_level#"), 
		                                   array($user_info->level, $upgrade_to_level), LTChatCOM_stop_upgrade_error_level), 
										   'type' => 'private', 
										   'other_options' => array('type_handle' => 'error'));
										   
		$sender_color = $this->LTChatDataKeeper->get_user_color($this->LTChatDataKeeper->get_user_level());
		$up_sender = $this->LTChatDataKeeper->get_user_name();
		
		$text = str_replace(array("#up_user#","#up_level#","#up_to_level#","#up_sender_color#","#up_sender#"), 
		                    array($user_info->nick, $user_info->level, $upgrade_to_level, $sender_color, $up_sender),LTChatCOM_upgrade_msg);
		
		//check for null reason
	    if($upgrade_to_level == NULL)
		return array('text' => LTChatCOM_upgrade_without_new_level, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else
		$changed = $this->LTChatDataKeeper->upgrade_downgrade($user_info->id, $upgrade_to_level);
		$mailer_upgrade = new_upgrade_downgrade($user_info->nick, $upgrade_to_level, $user_info->email, 'upgrade');
			
		$this->LTChatDataKeeper->post_logs($text, $room, $type, $user_info->nick);	
		$this->LTChatDataKeeper->post_private_reason($text, $user_info->id, 1);
		
		if($changed)
		return array('text' => "the user $user_info->nick upgrade from level $user_info->level to $upgrade_to_level and $mailer_upgrade", 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  	}
	
		}
		else
		return array('text' => '', 'type' => 'skip');
	}
	//---------------------------------------------------------------------
	function command_downgrade($command_info)
	{
		$row = $command_info['row']	;
		$room = $command_info['room'];
		$type = 'downgrade';
		
		if($row->user == $this->LTChatDataKeeper->get_user_name())
		{
		//delete the command from public
		$this->LTChatDataKeeper->delete_message_id($row->id, -1);
		
		$nick_name_have_downgrade = trim($command_info['params'][1]);
		unset($command_info['params'][1]);
		
		$downgrade_to_level = trim(implode(" ",$command_info['params']));
		
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($nick_name_have_downgrade); //get user
		
		//check for op exists on database
        if($user_info == NULL)
  	  	  return array('text' => str_replace("#user#", $user_info->nick, LTChatCore_user_doesnt_exists), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  	else
  	  	{
		
		if($user_info->level == 0)
		return array('text' => str_replace(array("#user#"), array($user_info->nick), LTChatCOM_stop_downgrade_level_zero), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		
		if($downgrade_to_level < 1)
		return array('text' => LTChatCOM_stop_downgrade_maximum_level, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		
		if($downgrade_to_level >= $user_info->level)
		return array('text' =>  str_replace(array("#from_level#", "#to_level#"), 
		                                    array($user_info->level, $downgrade_to_level), LTChatCOM_stop_downgrade_error_level),
										    'type' => 'private', 
										    'other_options' => array('type_handle' => 'error'));
		
		if($downgrade_to_level <= 50 && $downgrade_to_level >= 48 && $this->LTChatDataKeeper->get_user_level() != 50)
		return array('text' => LTChatCOM_stop_downgrade_only_admin, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		
		$sender_color = $this->LTChatDataKeeper->get_user_color($this->LTChatDataKeeper->get_user_level());
		$do_sender = $this->LTChatDataKeeper->get_user_name();
		
		$text = str_replace(array("#do_user#","#do_level#","#do_to_level#","#do_sender_color#","#do_sender#"), 
		                    array($user_info->nick, $user_info->level, $downgrade_to_level, $sender_color, $do_sender),
							LTChatCOM_downgrade_msg);
									
		//check for null reason
	    if($downgrade_to_level == NULL)
		return array('text' => LTChatCOM_downgrade_without_new_level, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else
		$changed = $this->LTChatDataKeeper->upgrade_downgrade($user_info->id, $downgrade_to_level);
		$mailer_downgrade = new_upgrade_downgrade($user_info->nick, $upgrade_to_level, $user_info->email, 'downgrade');
		
		$this->LTChatDataKeeper->post_logs($text, $room, $type, $user_info->nick);	
		$this->LTChatDataKeeper->post_private_reason($text, $user_info->id, 1);
			
		if($changed)
		return array('text' => "the user $user_info->nick downgrade from level $user_info->level to $downgrade_to_level and $mailer_downgrade", 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  	}

		}
		else
		return array('text' => '', 'type' => 'skip');
	}
	//---------------------------------------------------------------------
	function command_changeop($command_info)
	{
		$row = $command_info['row']	;
		$room = $command_info['room'];
		$type = 'change_op';
		
		if($row->user == $this->LTChatDataKeeper->get_user_name())
		{
		//delete the command from public
		$this->LTChatDataKeeper->delete_message_id($row->id, -1);	
	  	
		$nick_name_have_change = trim($command_info['params'][1]);
		unset($command_info['params'][1]);
		
		$new_nick_name = trim(implode(" ",$command_info['params']));
		
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($nick_name_have_change); //get user
		
		//check for op exists on database
        if($user_info == NULL)
  	  	return array('text' => str_replace("#user#", $user_info->nick, LTChatCore_user_doesnt_exists), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  	else
  	  	{
		
		if($user_info->level == 0)
		return array('text' => str_replace(array("#user#"), array($user_info->nick), LTChatCOM_changeop_level_zero), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));		
				
		//check for null reason
	    if($new_nick_name == NULL)
		return array('text' => LTChatCOM_changeop_without_new_nick, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else
		
		$text = str_replace(array("#user#","#new_nick#"), array($nick_name_have_change, $new_nick_name), LTChatCOM_changeop_msg);
		
		$changed = $this->LTChatDataKeeper->change_op($user_info->id, $new_nick_name);
		//$mailer_new_pass = new_password($user_info->nick, $new_pass, $user_info->email);
		
		$this->LTChatDataKeeper->post_logs($text, $room, $type, $user_info->nick);	
		if($changed)
		return array('text' => "the user $user_info->nick changed nick name to $new_nick_name and $mailer_new_pass", 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  	}
  	  }
  	  else
  	    return array('text' => '', 'type' => 'skip');
	}
  	//---------------------------------------------------------------------
  	function command_changepass($command_info)
  	{
  	  $row = $command_info['row'];
	  $room = $command_info['room'];
	  $type = 'change_pass';
	  
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {	  
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	  	
		$user_need_change = trim($command_info['params'][1]);
		unset($command_info['params'][1]);
		
		$new_pass = trim(implode(" ",$command_info['params']));
		
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_need_change); //get user

		//check for op exists on database
        if($user_info == NULL)
  	  	  return array('text' => str_replace("#user#", $user_need_change, LTChatCore_user_doesnt_exists), 
		               'type' => 'private', 
					   'other_options' => array('type_handle' => 'error'));
  	  	else
  	  	{

		if($user_info->level == 0)
		return array('text' => str_replace(array("#user#"), array($user_need_change), LTChatCOM_changepass_level_zero), 
		             'type' => 'private', 
					 'other_options' => array('type_handle' => 'error'));		
				
		//check for null reason
	    if($new_pass == NULL)
		return array('text' => LTChatCOM_changepass_without_new_level, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else
		
		$text = str_replace(array("#user#","#new_pass#"), array($user_need_change, $new_pass), LTChatCOM_changepass_msg);
		$changed = $this->LTChatDataKeeper->change_password($user_info->id, $new_pass);
		$mailer_new_pass = new_password($user_info->nick, $new_pass, $user_info->email);
		$this->LTChatDataKeeper->post_logs($text, $room, $type, $user_info->nick);	
		
		if($changed)
		return array('text' => "the user $user_info->nick changed password and $mailer_new_pass", 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  	}
  	  }
  	  else
  	    return array('text' => '', 'type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------
  	function command_pass($command_info)
  	{
  	  $row = $command_info['row'];
	  $room = $command_info['room'];
	  $type = 'change_pass';
	  
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {	  
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
		
		$user_name = $this->LTChatDataKeeper->get_user_name();
		
		$new_pass = trim(implode(" ",$command_info['params']));
		
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user

		//check for op exists on database
        if($user_info == NULL)
  	  	  return array('text' => str_replace("#user#", $user_name, LTChatCore_user_doesnt_exists), 
		               'type' => 'private', 
					   'other_options' => array('type_handle' => 'error'));
  	  	else
  	  	{

		if($user_info->level == 0)
		return array('text' => str_replace(array("#user#"), array($user_name), LTChatCOM_changepass_level_zero), 
		             'type' => 'private', 
					 'other_options' => array('type_handle' => 'error'));		
				
		//check for null reason
	    if($new_pass == NULL)
		return array('text' => LTChatCOM_changepass_without_new_level, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else
		
		$text = str_replace(array("#user#","#new_pass#"), array($user_need_change, $new_pass), LTChatCOM_changepass_msg);
		$changed = $this->LTChatDataKeeper->change_password($user_info->id, $new_pass);
		$mailer_new_pass = new_password($user_info->nick, $new_pass, $user_info->email);
		$this->LTChatDataKeeper->post_logs($text, $room, $type, $user_info->nick);	
		
		if($changed)
		return array('text' => "Hi $user_info->nick your password has been changed and $mailer_new_pass", 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  	}
  	  }
  	  else
  	    return array('text' => '', 'type' => 'skip');
  	}
  	//---------------------------------------------------------------------

    function command_skin_get_css()
    {
    	$out = array();
		if ($handle = opendir(LTChatTemplateSystemPath."css"))
		{
		    while (false !== ($file = readdir($handle)))
		      if ($file != "." && $file != "..")
		      	$out[] = $file;

		    closedir($handle); 
		}
		return $out;
    }

    function command_skin_get_skins()
    {
    	$out = array();
		if ($handle = opendir(LTChart_path."/templates/"))
		{
		    while (false !== ($file = readdir($handle)))
		      if ($file != "." && $file != "..")
		      	$out[] = $file;

		    closedir($handle); 
		}
		return $out;
    }

  	function command_skin($command_info)
  	{
      $row = $command_info['row'];
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {

	      $params = $command_info['params'];
	  	  $data_css = $this->command_skin_get_css();
	  	  $data_skins = $this->command_skin_get_skins();
	
	  	  if($command_info['command_help']['except_params_static']['showcss'] == trim($params[1]))
	  	    $data = $data_css;
	  	  elseif($command_info['command_help']['except_params_static']['showskins'] == trim($params[1]))
	  	    $data = $data_skins;
	  	  elseif($command_info['command_help']['except_params_static']['setskin'] == trim($params[1]))
	  	  {
	  	  	unset($params[1]);
			$name = trim(implode(" ",$params));
			if(is_array($data_skins))
			  foreach ($data_skins as $skin)
			    if($skin == $name)
			    {
			      $out = str_replace("#skin_name#",$name, ChFun_skin_SkinChanged);
			      $this->LTChatDataKeeper->set_chat_variable("LTChatTemplateName", $name);
			      $this->LTChatDataKeeper->set_chat_variable("LTTpl_css_link", LTChatTemplatePath."css/default.css");
			    }
	
			if(!$out)	$out = ChFun_skin_BadSkin;
	  	  }
	  	  elseif($command_info['command_help']['except_params_static']['setcss'] == trim($params[1]))
	  	  {
	  	  	unset($params[1]);
			$name = trim(implode(" ",$params));
			if(is_array($data_css))
			  foreach ($data_css as $css)
			  {
				if($css == $name)
			    {
			      $out = str_replace("#css_name#",$name, ChFun_skin_CssChanged);
			      $this->LTChatDataKeeper->set_chat_variable("LTTpl_css_link", LTChatTemplatePath."css/{$name}");
			    }
			  }
	
			if(!$out)	$out = ChFun_skin_BadCss;
	  	  }
	
	  	  if(is_array($data))
	  	  {
	  	    foreach ($data as $file)
	  	    {
	  	      if($out)	$out = str_replace("#name#", $file, ChFun_skin_List.ChFun_skin_ListSep).$out;
	  	  	  else		$out = str_replace("#name#", $file, ChFun_skin_List);
	  	    }
	  	    return array('text' => $out, 'type' => 'private');
	  	  }
	  	  elseif($out)
	  	    return array('text' => $out, 'type' => 'private');
	  	  else
	  	    return array('text' => str_replace("#param#", $params[1], ChFun_skin_UnParam), 'type' => 'private');
  	  }
  	  return array('type' => 'skip');
  	  
  	}
  	//---------------------------------------------------------------------

  	function command_showclear($command_info)
  	{
    return array();
  	}
  	//---------------------------------------------------------------------

  	function command_showops($command_info)
  	{
    return array();
  	}
	
  	//---------------------------------------------------------------------

  	function command_showforward($command_info)
  	{
    return array();
  	}
	
  	//---------------------------------------------------------------------

  	function command_showsus($command_info)
  	{
    return array();
  	}
	
  	//---------------------------------------------------------------------

  	function command_showban($command_info)
  	{
    return array();
  	}
	
  	//---------------------------------------------------------------------

  	function command_showbanpc($command_info)
  	{
    return array();
  	}	
	
  	//---------------------------------------------------------------------

  	function command_showdisable($command_info)
  	{
    return array();
  	}	
	
  	//---------------------------------------------------------------------

  	function command_apply($command_info)
  	{
    return array();
  	}
	
  	//---------------------------------------------------------------------

  	function command_applylogs($command_info)
  	{
    return array();
  	}	
  	//---------------------------------------------------------------------

  	function command_list($command_info)
  	{
    return array();
  	}
	
  	//---------------------------------------------------------------------

  	function command_register($command_info)
  	{
    return array();
  	}
	
  	//---------------------------------------------------------------------

  	function command_updologs($command_info)
  	{
    return array();
  	}
	
  	//---------------------------------------------------------------------

  	function command_trace($command_info)
  	{
    return array();
  	}
	
  	//---------------------------------------------------------------------

  	function command_trace2($command_info)
  	{
    return array();
  	}
	
  	//---------------------------------------------------------------------

  	function command_trace4($command_info)
  	{
    return array();
  	}
  	//---------------------------------------------------------------------

  	function command_d($command_info)
  	{
	  $row = $command_info['row'];
	  	
	  if($row->user == $this->LTChatDataKeeper->get_user_name())
	  {
		
		$message = trim(implode(" ",$command_info['params'])); //implode the song
		
		if($message == ''){
			return array('text' => ChFun_comment_msg_error, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}else{

			return array('text' => $message, 'type' => 'public', 'other_options' => array('type_handle' => 'd_message'));
		}

	  }
	  else
	  return array('type' => 'skip');
  	}
  	//---------------------------------------------------------------------

  	function command_comment($command_info)
  	{
	  $row = $command_info['row'];
	  	
	  if($row->user == $this->LTChatDataKeeper->get_user_name())
	  {
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);

		$user_name = $this->LTChatDataKeeper->get_user_name();
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user
		
		$comment = trim(implode(" ",$command_info['params'])); //implode the song
		
		if($comment == ''){
		return array('text' => ChFun_comment_msg_error, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}else{
		
		$replaced = load_internal_emoticons($comment);
		
		$msg = str_replace(
		       array("#myfont#","#mycolor#","#comment#"), 
		       array($user_info->font, $user_info->color, $replaced), ChFun_comment_msg);
					   
		//change the comment
		$change = $this->LTChatDataKeeper->change_comment($msg);
		
		if($change == TRUE){
		return array('text' => 'your comment message has been changed', 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}
		}

	  }
	  else
	  return array('type' => 'skip');
  	}
    //---------------------------------------------------------------------
  	function command_mk3($command_info)
  	{
	  $row = $command_info['row'];
	  $room = $command_info['room'];
	
	  if($row->user == $this->LTChatDataKeeper->get_user_name())
	  {
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
		
		$user_name = $this->LTChatDataKeeper->get_user_name();
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user
		
		$mk3_msg = trim(implode(" ",$command_info['params'])); //implode
		
		if($mk3_msg == ''){
		return array('text' => ChFun_mk3_msg_error, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}else{
		
		$msg = str_replace(
		       array("#user#","#myfont#","#mycolor#","#nickfont#","#nickcolor#","#text#"), 
		       array($user_info->nick, $user_info->font, $user_info->color, $user_info->nickfont, $user_info->nickcolor, $mk3_msg), ChFun_mk3_msg);
										   
		//post the singing message on public
		$this->LTChatDataKeeper->post_reason($msg, $room);
		
		return array('text' => 'Your marquee message has been sent', 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}
		
	  }
	  else
	  return array('type' => 'skip');	
	}
    //---------------------------------------------------------------------
  	function command_mk2($command_info)
  	{
	  $row = $command_info['row'];
	  $room = $command_info['room'];
	
	  if($row->user == $this->LTChatDataKeeper->get_user_name())
	  {
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
		
		$user_name = $this->LTChatDataKeeper->get_user_name();
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user
		
		$mk2_msg = trim(implode(" ",$command_info['params'])); //implode the song
		
		if($mk2_msg == ''){
		return array('text' => ChFun_mk2_msg_error, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}else{
		
		$msg = str_replace(
		       array("#user#","#myfont#","#mycolor#","#nickfont#","#nickcolor#","#text#"), 
		       array($user_info->nick, $user_info->font, $user_info->color, $user_info->nickfont, $user_info->nickcolor, $mk2_msg), ChFun_mk2_msg);
										   
		//post the singing message on public
		$this->LTChatDataKeeper->post_reason($msg, $room);
		
		return array('text' => 'Your marquee message has been sent', 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}
		
	  }
	  else
	  return array('type' => 'skip');	
	}
    //---------------------------------------------------------------------
  	function command_mk1($command_info)
  	{
	  $row = $command_info['row'];
	  $room = $command_info['room'];
	
	  if($row->user == $this->LTChatDataKeeper->get_user_name())
	  {
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
		
		$user_name = $this->LTChatDataKeeper->get_user_name();
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user
		
		$mk1_msg = trim(implode(" ",$command_info['params'])); //implode the song
		
		if($mk1_msg == ''){
		return array('text' => ChFun_mk1_msg_error, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}else{
		
		$msg = str_replace(
		       array("#user#","#myfont#","#mycolor#","#nickfont#","#nickcolor#","#text#"), 
		       array($user_info->nick, $user_info->font, $user_info->color, $user_info->nickfont, $user_info->nickcolor, $mk1_msg), ChFun_mk1_msg);
						   
		//post the singing message on public
		$this->LTChatDataKeeper->post_reason($msg, $room);
		
		return array('text' => 'Your marquee message has been sent', 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}
		
	  }
	  else
	  return array('type' => 'skip');	
	}
    //---------------------------------------------------------------------
  	function command_id($command_info)
  	{
	  $row = $command_info['row'];
	  $room = $command_info['room'];
	
	  if($row->user == $this->LTChatDataKeeper->get_user_name())
	  {
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
				
		$user_name = trim(implode(" ",$command_info['params'])); //implode the song
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user
		
		//check for op exists on database
        if($user_info == NULL){
  	  	  return array('text' => str_replace("#user#", $user_name, ChFun_sus_BadNick), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  	}else{
		
		if($user_info->level == 0)
		return array('text' => str_replace(array("#user#"), array($user_name), 'Not Found Inforamtion for Guest'), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
				
		//get level rights name by level
	    $maskip = $this->LTChatDataKeeper->get_rights_by_level($user_info->level);
		//get user color by level
		$level_color = $this->LTChatDataKeeper->get_user_color($user_info->level);
		
		$msg = str_replace(
		       array("#user#","#maskip#","#level#","#rights#","#levelcolor#"), 
		       array($user_info->nick, $maskip, $user_info->level, $user_info->rights, $level_color), ChFun_msg_id);
		
		return array('text' => $msg, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		
		}
		
	  }
	  else
	  return array('type' => 'skip');	
	}
    //---------------------------------------------------------------------
  	function command_sing($command_info)
  	{
	  $row = $command_info['row'];
	  $room = $command_info['room'];
	
	  if($row->user == $this->LTChatDataKeeper->get_user_name())
	  {
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
		
		$user_name = $this->LTChatDataKeeper->get_user_name();
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user
		
		$singing_msg = trim(implode(" ",$command_info['params'])); //implode the song
		
		if($singing_msg == ''){
		return array('text' => ChFun_sing_msg_error, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}else{
		
		$msg = str_replace(
		       array("#user#","#my_nick_font#","#my_nick_color#","#LTChatTemplatePath#","#my_color#","#my_font#","#text#"), 
		       array($user_info->nick, $user_info->nickfont, $user_info->nickcolor, LTChatTemplatePath, $user_info->color, $user_info->font, $singing_msg), ChFun_sing_msg);
										   
		//post the singing message on public
		$this->LTChatDataKeeper->post_reason($msg, $room);
		
		return array('text' => 'your song message has been sent', 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}
		
	  }
	  else
	  return array('type' => 'skip');	
	}
    //---------------------------------------------------------------------
  	function command_away($command_info)
  	{
  	  $row = $command_info['row'];
	  $type = 'away';
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
	    $user_info = $this->LTChatDataKeeper->get_user_by_nick($this->LTChatDataKeeper->get_user_name()); //get user
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
		
  	    $check = $this->LTChatDataKeeper->whois_have_action($user_info->id, $type);
		
		if($check == 1){
		$text = LTChatCOM_away_disable;
		mysql_query("delete from `".LTChat_Main_prefix."check` where `away` = '1' and `users_id` = '{$user_info->id}' and `chat_id` = '".LTChat_Main_CHAT_ID."'") or debug(mysql_error(), "LTChatDataKeeper", __LINE__);
		}else{
		$text = LTChatCOM_away_enable;
		$this->LTChatDataKeeper->insert_check4action($user_info->id, $type);
		}
		
  	    return array('text' => $text, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
      }
	  else
  	  return array('type' => 'skip');
  	}
  	//---------------------------------------------------------------------

  	function command_fl($command_info)
  	{
	  $row = $command_info['row'];
	  
	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
  	    //delete the command from talk for duplicate
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
		//delete the private
  	    $this->LTChatDataKeeper->delete_private();
  	    
		$other_options = array('function' => 'clear_private');  	    
		return array('data_type' => 'functions',
		             'text' => '',
					 'type' => 'private',
					 'other_options' => $other_options);
	 }	
	 else 
  	    return array('text' => '', 'type' => 'skip');
  	}
  	//---------------------------------------------------------------------

  	function command_changeip($command_info)
  	{
  	  $row = $command_info['row'];
	  
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
	      $this->LTChatDataKeeper->delete_message_id($row->id, -1);

		  $user_name = $this->LTChatDataKeeper->get_user_name();

		  $user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user
		   
		  $get_ip = trim(implode(" ",$command_info['params'])); //implode the ip to change
		  
		  //get level rights name by level
	      $maskip = $this->LTChatDataKeeper->get_rights_by_level($user_info->level);
		  //get connection ip
		  $connection_ip = $_SERVER['REMOTE_ADDR'];
		  //check ip before change
		  $check_ip = ($get_ip == 'BLOCKED' || $get_ip == $maskip || $get_ip == $connection_ip) ? $get_ip : 'BLOCKED';

		  //check for op exists on database
		  if($user_info == NULL){
		  return array('text' => str_replace("#user#", $user_name, LTChatCore_user_doesnt_exists), 
		               'type' => 'private', 
					   'other_options' => array('type_handle' => 'error'));
		  }else{
		  
		  if($get_ip == NULL){
		    
		   $other_options = array('function' => 'changeip', 
		                          'showchangeip' => '1',
								  'nickname' => $user_info->nick,
								  'status_1' => 'BLOCKED',
								  'status_2' => $maskip,
								  'status_3' => $user_info->last_ip
								  );
	
		  return array('data_type' => 'functions', 'text' => '', 'type' => 'private', 'other_options' => $other_options);
		  
		  }else{
		  
		  $how_change = $this->LTChatDataKeeper->change_ip($check_ip);
		  
		  $change = ($how_change == 1) ? $how_change : 0;
		  if($change)		  
		  return array('text' => str_replace("#change#", $check_ip, LTChatCore_change_ip), 
		               'type' => 'private', 
					   'other_options' => array('type_handle' => 'error'));		  
		  }
		 }
	  }
  	  else 
  	  return array('text' => '', 'type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------

  	function command_setip($command_info)
  	{
  	  $row = $command_info['row'];
	  
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
	      $this->LTChatDataKeeper->delete_message_id($row->id, -1);

		  $user_name = $this->LTChatDataKeeper->get_user_name();

		  $user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user
		   
		  $get_ip = trim(implode(" ",$command_info['params'])); //implode the ip to change

		  //check for op exists on database
		  if($user_info == NULL){
		  return array('text' => str_replace("#user#", $user_name, LTChatCore_user_doesnt_exists), 
		               'type' => 'private', 
					   'other_options' => array('type_handle' => 'error'));
		  }else{
		  
		  $how_change = $this->LTChatDataKeeper->change_ip($get_ip);
		  
		  $change = ($how_change == 1) ? $how_change : 0;
		  if($change)		  
		  return array('text' => str_replace("#change#", $get_ip, LTChatCore_change_ip), 
		               'type' => 'private', 
					   'other_options' => array('type_handle' => 'error'));
		 }
	  }
  	  else 
  	  return array('text' => '', 'type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------

  	function command_timebar($command_info)
  	{
      global $autoup;

  	  $row = $command_info['row'];
	  $room = $command_info['room'];
	  
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
	       $this->LTChatDataKeeper->delete_message_id($row->id, -1);

		   $user_name = $this->LTChatDataKeeper->get_user_name();

		   $user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user

		  //check for op exists on database
		  if($user_info == NULL){
		  return array('text' => str_replace("#user#", $user_name, LTChatCore_user_doesnt_exists), 
		               'type' => 'private', 
					   'other_options' => array('type_handle' => 'error'));
		  }else{
		   
		   $level = $this->LTChatDataKeeper->get_user_level();
		   $color = $this->LTChatDataKeeper->get_user_color($level);
		   $range = range(from_level, to_level);
		   $ttime = $this->LTChatDataKeeper->get_total_time();
			
		   $myhour = $ttime / 3600;
		   $showtimebar = 0;
		   
		   if($level >= from_level && $level <= to_level){
			  if(is_array($range))
				if(in_array($level, $range))
					$limitaz = $autoup[$level];
			
		   $max = $limitaz;
			
		   $dopercent = ( $myhour / $max ) * 100;
		   
		   $percent = ceil($dopercent);
		   $showtimebar = 1;
		   }
		   		   
		   $other_options = array('function' => 'timebar', 
								  'showtimebar' => $showtimebar,
								  'percent' => $percent,
								  'LTChatTemplatePath' => LTChatTemplatePath
								  );
	
		  return array('data_type' => 'functions', 'text' => '', 'type' => 'private', 'other_options' => $other_options);
		 }
	  }
  	  else 
  	  return array('text' => '', 'type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------

  	function command_whois($command_info)
  	{
  	  $row = $command_info['row'];
	  $room = $command_info['room'];
	  $time = time();
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
	       $this->LTChatDataKeeper->delete_message_id($row->id, -1);

		   $user_name = trim($command_info['params'][1]);
		   unset($command_info['params'][1]);

		   $user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user

		  //check for op exists on database
		  if($user_info == NULL){
		  return array('text' => str_replace("#user#", $user_name, LTChatCore_user_doesnt_exists), 
		               'type' => 'private', 
					   'other_options' => array('type_handle' => 'error'));
		  }else{
		  $online  = $this->LTChatDataKeeper->online($user_info->id); //get status (on)||(off) line
		  
		  //check online for stop command if offline mode!
		  if($online == FALSE){
		  return array('text' => str_replace(array("#user#"), array($user_info->nick), ChFun_offline), 
		               'type' => 'private', 
					   'other_options' => array('type_handle' => 'error'));
		  }
		   
		   $get = $this->LTChatDataKeeper->show_whois($user_info->id, $room, $user_info->nick);
		   $status = ($get['online'] == 1) ? 'online' :"No data";
		   $onlinetime = last_login($get['login_time']);
		   $public_idle = ($get['last_public_msg_time'] > 0) ? last_login($get['last_public_msg_time']) :"No data";
		   $private_idle = ($get['last_private_msg_time'] > 0) ? last_login($get['last_private_msg_time']) :"No data";
		  
		   $other_options = array('function' => 'show_whois', 
								  'nickname' => $user_info->nick,
								  'maskip' => $user_info->rights,
								  'room' => $room,
								  'onlinetime' => $onlinetime,
								  'public_idle' => $public_idle,
								  'private_idle' => $private_idle,
								  'status' => $status
								  );
	
		  return array('data_type' => 'functions', 'text' => '', 'type' => 'private', 'other_options' => $other_options);
		 }
	  }
  	  else 
  	  return array('text' => '', 'type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------

  	function command_remove_user($command_info)
  	{
  	  $row = $command_info['row'];
	  $room = $command_info['room'];
	  $type = 'remove';
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
				
		$op_name = trim($command_info['params'][1]);
		unset($command_info['params'][1]);
		
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($op_name); //get user
		
		//check for op exists on database
		if($user_info == NULL){
		return array('text' => str_replace("#user#", $op_name, LTChatCore_user_doesnt_exists), 
				     'type' => 'private', 
				     'other_options' => array('type_handle' => 'error'));
		}else{
		
		if($user_info->level == 0){
		return array('text' => str_replace("#user#", $op_name, LTChatCore_user_doesnt_exists), 
				     'type' => 'private', 
				     'other_options' => array('type_handle' => 'error'));
		}
		
		if($user_info->level >= 48){
		return array('text' => str_replace("#user#", $op_name, LTChatCore_user_doesnt_exists), 
				     'type' => 'private', 
				     'other_options' => array('type_handle' => 'error'));
		}
		
		$out = "** The member name <b>$op_name</b> has been removed from database";
				
		$this->LTChatDataKeeper->delete_member($user_info->id);
		
		//post logs
		$this->LTChatDataKeeper->post_logs($out, $room, $type, FALSE);
		
		return array('text' => $out, 'type' => 'private');
		
		}
      }
	  else
  	  return array('type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------

  	function command_del($command_info)
  	{
  	  $row = $command_info['row'];
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
		$level = $this->LTChatDataKeeper->get_user_level(); //get sender level
		
		(int)$getid = trim($command_info['params'][1]);
		unset($command_info['params'][1]);
		
		if($level > 0){
		$getmsgid = $this->LTChatDataKeeper->delete_private_by_id();
		}else{
		$getmsgid = $this->LTChatDataKeeper->delete_private_by_id_rec($getid);
		}
		//check for id null
	    if($getmsgid == false)
		return array('text' => ChFun_fl_nomsg, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else
	    
		return array('data_type' => 'refresh', 'type' => 'private');

      }
	  else
  	  return array('type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------

  	function command_forward($command_info)
  	{
  	  $row = $command_info['row'];
	  $room = $command_info['room'];
	  $type = 'forward';
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {	    
	    //delete the command from talk for duplicate
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
		//get the opreator want forward
		$f_send_to = trim($command_info['params'][1]);
		unset($command_info['params'][1]);

		//get the info for forwarded to
		$f_send_to_info = $this->LTChatDataKeeper->get_user_by_nick($f_send_to); //get sender info
		
		//check for op exists on database
        if($f_send_to_info == NULL){
  		  $u_doesnt_exists = str_replace("#user#",trim($f_send_to), LTChatCore_user_doesnt_exists);
  		  return array('text' => $u_doesnt_exists, 'type' => 'private','other_options' => array('type_handle' => 'error'));
  	  	}else{
		$online  = $this->LTChatDataKeeper->online($f_send_to_info->id); //get status (on)||(off)line
		
		//check command for stop forward to user with offline mode!
		if($online == FALSE){
		return array('text' => str_replace(array("#user#"), array($f_send_to), ChFun_forward_notonline), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}
		//forward the message and post in forward logs
		$this->LTChatDataKeeper->forward_message_id($f_send_to_info->id, $type, $room, $f_send_to);
		}## check exists
      }##
	  else
  	  return array('type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------

  	function command_abuse($command_info)
  	{
  	  $row = $command_info['row'];
	  $room = $command_info['room'];
	  $type = 'abuse';
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $room)
  	  {
	    //delete the command from talk for duplicate
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
		//post logs for abuse room
		$this->LTChatDataKeeper->post_logs($row->user, $room, $type, FALSE);
		//save the public room for abuse
		$this->LTChatDataKeeper->abuse_public($this->LTChatDataKeeper->get_user_id(),$room);

  	    return array('text' => str_replace(array("#room#"), array($room), ChFun_abuse_msg),
		             'type' => 'private', 
					 'other_options' => array('type_handle' => 'error'));
      }
	  else
  	  return array('type' => 'skip');
  	}
  	//---------------------------------------------------------------------

  	function command_check()
  	{
      return array();
  	}
	
  	//---------------------------------------------------------------------

  	function command_flash($command_info)
  	{
  	  $params = $command_info['params'];
  	  $param = implode(" ", $params);
	  $flash = trim($params[1]);
	  $final = str_replace('-','',$flash);
      $param = trim($param);
	  
	  if(!$flash){
	  return array('text' => ERROR_flash_msg_empty, 'type' => 'private');
	  }

      if($command_info['command_help']['except_params_static']["$final"] == trim($params[1])){
	  $out = str_replace(array("#flash#"), array($final), ChFun_Flash_Style);
	  }else{
	  return array('text' => ERROR_flash_msg_notfound, 'type' => 'private');
	  }

      return array('text' => $out, 'type' => 'public');
  	}
  	//---------------------------------------------------------------------
	
  	function command_ping($command_info)
  	{
  	  $row = $command_info['row'];
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
  	  	$HOST = trim($command_info['params'][1]);
  	  	if($HOST == "")
  	  	  $text = ChFun_ping_BadHost;
  	  	elseif(!function_exists("socket_create"))
  	  	  $text = ChFun_ping_Disabled;
  	  	elseif(($ip = gethostbyname($HOST)) == $HOST && !eregi("([0-9]*)\.([0-9]*)\.([0-9]*)\.([0-9]*)", $HOST))
  	  	{
  	  	  $text = str_replace("#host#", $HOST, ChFun_ping_ResolveErr);
  	  	}
  	  	else
  	  	{
          include_once(LTChart_path."/include/class.Net_Ping.inc.php");
          
		  $ping = new Net_Ping();
		  $text .= str_replace(array("#host#","#ip#"), array($HOST, $ip), ChFun_ping_Info).ChFun_ping_Separator;

		  for($i = 0; $i < 3; $i ++)
		  {
			  $ping->ping($HOST);
			  $b = 32;
		   	  if ($ping->time)		$text .= str_replace(array("#ip#","#b#","#time#"), array($ip, $b, $ping->time), ChFun_ping_Info_resp);
			  else				    $text .= ChFun_ping_Info_Timeout;
			  $text .= ChFun_ping_Separator;
		  }
  	  	}
	    return array('text' => $text, 'type' => 'private');
  	  }
  	  return array('type' => 'skip');
  	}
  	//---------------------------------------------------------------------
  	
  	function command_whoami($command_info)
  	{
  	   $text = str_replace("#user#", $this->LTChatDataKeeper->get_user_name(), ChFun_whoami);
	   return array('text' => $text, 'type' => 'private');
  	}
  	//---------------------------------------------------------------------

  	function command_logout($command_info)
  	{
	
	global $autoup;
	
  	  $row = $command_info['row'];
	  $room = $command_info['room'];
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
		
        $quit_msg = trim(implode(" ",$command_info['params'])); //implode the reason
	  
	    $user_info = $this->LTChatDataKeeper->get_user_by_nick($row->user); //get sender info
	    $level = $this->LTChatDataKeeper->get_user_level(); //get sender level
		$enouq = time() - $user_info->last_seen;
		
		//check the time login for stop quit before 2 min
		if ($level == 0 && $enouq <= QUIT_MSG){
        return array('text' => ChFunQuitb4twomin, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		
		}else{
		
		$quit_msg = ($quit_msg != '') ? "($quit_msg)" : '' ;

		//the quit message
		$msg = str_replace(array("#user#", "#ip#", "#room#", "#quit_msg#"),
		                   array($user_info->nick, $user_info->rights, $room, $quit_msg), 
						   ChFun_chat_exit);	   
		//post the quit message on public
		$this->LTChatDataKeeper->post_reason($msg, $room);
		
		$other_options = array('function' => 'logout');
  	    return array('data_type' => 'functions', 'text' => '', 'type' => 'private', 'other_options' => $other_options);
		}
		
  	  }

  	  return array('type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------

  	function command_allmsg($command_info)
  	{
	  $row = $command_info['row'];
	  $room = $command_info['room'];
	  $sender_id = $this->LTChatDataKeeper->get_user_id();
	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {	  
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	    $msg = trim(implode(" ",$command_info['params']));
        
		if($msg == NULL)
		return array('text' => ERROR_allmsg, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else				
	    $get = $this->LTChatDataKeeper->send_allmsg($sender_id, $msg, $room);
		if(isset($get['info']) && $get['info'] > 0)
		$text = str_replace(array("#count_sent#") , array($get['info']) , ChFun_allmsg_ok);
		return array('text' => $text, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	  }
	  else
	  return array('type' => 'skip');
	}
	
  	//---------------------------------------------------------------------

  	function command_guestmsg($command_info)
  	{
	  $row = $command_info['row'];
	  $room = $command_info['room'];
	  $sender_id = $this->LTChatDataKeeper->get_user_id();
	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {	  
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	    $msg = trim(implode(" ",$command_info['params']));
        
		if($msg == null)
		return array('text' => ERROR_guestmsg, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else				
	    $get = $this->LTChatDataKeeper->send_usermsg($sender_id, $msg, $room);
		if(isset($get['info']) && $get['info'] > 0)
		$text = str_replace(array("#count_sent#") , array($get['info']) , ChFun_guestmsg_ok);
		return array('text' => $text, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	  }
	  else
	  return array('type' => 'skip');
	}
	
  	//---------------------------------------------------------------------

  	function command_opmsg($command_info)
  	{
	  $row = $command_info['row'];
	  $room = $command_info['room'];
	  $sender_id = $this->LTChatDataKeeper->get_user_id();
	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {	  
	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	    $msg = trim(implode(" ",$command_info['params']));
        
		if($msg == null)
		return array('text' => ERROR_opmsg, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else				
	    $get = $this->LTChatDataKeeper->send_opmsg($sender_id, $msg, $room);
		if(isset($get['info']) && $get['info'] > 0)
		$text = str_replace(array("#count_sent#") , array($get['info']) , ChFun_opmsg_ok);
		return array('text' => $text, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	  }
	  else
	  return array('type' => 'skip');
	}
	
  	//---------------------------------------------------------------------

  	function command_tpl_avatar()
  	{
	  $avatars = array();
	  if (file_exists(LTChart_path . "/img/avatars/") && $handle = opendir(LTChart_path . "/img/avatars/"))
	  {		
	  	 $owners = $this->LTChatDataKeeper->get_avatars_list();
	  	 $noavatar_img = "";
		 while (false !== ($file = readdir($handle)))
		   if($file != "." && $file != "..")
		   {
		   	 $link = "./img/avatars/{$file}";
		   	 if($file == "noavatar.gif" || $file == "noavatar.jpg")
		       $noavatar_img = $file;
		   	 else
		   	 {
		       $avatars[$file]['link'] = $link;
		       $avatars[$file]['file_name'] = $file;
		       $avatars[$file]['owner'] = $owners[$link];
		   	 }
		   }
		 closedir($handle);
	  }

	  return array('avatars' => $avatars, 'noavatar' => $noavatar_img);
  	}
  	//---------------------------------------------------------------------

  	function command_tpl_me()
  	{
  	  return array();
  	}
  	
  	//---------------------------------------------------------------------
	
  	function command_actionlogs()
  	{
	   return array();
  	}
	
  	//---------------------------------------------------------------------
	
  	function command_actionstop()
  	{
	   return array();
  	}
	
  	//---------------------------------------------------------------------

  	function command_ban($command_info)
  	{
	  if($room != "")		$room = $command_info['room'];
	  else							$room = 'Arabia';
  	  $row = $command_info['row'];
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $command_info['room'])
  	  {
	    //delete the command from talk for duplicate
        $this->LTChatDataKeeper->delete_message_id($row->id, -1);
        $action_on = trim($command_info['params'][1]); //get the IP / NICK to ban
        unset($command_info['params'][1]); //unset params 1 for replace
        $reason = trim(implode(" ",$command_info['params'])); //implode the reason
		
		if (!preg_match("/^(\d+\.?)+$/", $action_on)){
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($action_on); //get sender info
		$online  = $this->LTChatDataKeeper->online($user_info->id); //get status (on)||(off)line )
		}

		$my_info = $this->LTChatDataKeeper->get_user_by_nick($row->user); //get sender info
		$get_user_color = $this->LTChatDataKeeper->get_user_color($my_info->level);
		
		//check valid ip
		if (is_ip($action_on) !== false){
		$trueip = $action_on;
		$text = str_replace(array("#ip#","#sendercolor#","#sender#","#senderrightcolor#","#reason#"), 
		                    array($trueip, $my_info->color,$my_info->nick,$get_user_color,$reason), 
							ChFun_banip_OkReason);
		//check valid user
		}elseif($user_info != NULL && $user_info->level == 0 && $online == 1 ){
		$ban_user = $user_info->last_ip;
		$text = str_replace(array("#user#","#sendercolor#","#sender#","#senderrightcolor#","#reason#"), 
		                    array($user_info->nick, $my_info->color,$my_info->nick,$get_user_color,$reason), 
							ChFun_banuser_OkReason);
		
		}else{
		return array('text' => ChFun_not_valid_ip, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}

		//check for not null reason
	    if($reason == NULL)
		return array('text' => ChFun_write_banip_reason, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else 
		
		if($trueip != NULL){
		$type = 'banip';
		$this->LTChatDataKeeper->banip_address($text, $type, $trueip, FALSE);
		}
		if($ban_user != NULL){
		$type = 'banuser';
		$this->LTChatDataKeeper->ban_nick($text, $type, $ban_user, $user_info->nick);
		}
		
		if($user_info != NULL){
		$this->LTChatDataKeeper->insert_check4action($user_info->id, $type);
		}
		
		$check_vaild = ($user_info != NULL) ? $user_info->nick : $trueip;
		
		$this->LTChatDataKeeper->post_logs($text, $room, $type, $check_vaild);
		$this->LTChatDataKeeper->post_reason($text, $room);		
		$this->LTChatDataKeeper->post_private_logs(str_replace(array("#text#"), array($text), ChFun_private_logs_syntex));
		$this->LTChatDataKeeper->abuse_public($this->LTChatDataKeeper->get_user_id(),$room);
  	  }
  	  else
  	    return array('text' => '', 'type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------

  	function command_banpc($command_info)
  	{
	  if($room != "")		$room = $command_info['room'];
	  else							$room = 'Arabia';
  	  $row = $command_info['row'];
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $command_info['room'])
  	  {
	    //delete the command from talk for duplicate
        $this->LTChatDataKeeper->delete_message_id($row->id, -1);
        $action_on = trim($command_info['params'][1]); //get the IP / NICK to ban
        unset($command_info['params'][1]); //unset params 1 for replace
        $reason = trim(implode(" ",$command_info['params'])); //implode the reason
		
		if (!preg_match("/^(\d+\.?)+$/", $action_on)){
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($action_on); //get sender info
		$online  = $this->LTChatDataKeeper->online($user_info->id); //get status (on)||(off)line )
		}

		$my_info = $this->LTChatDataKeeper->get_user_by_nick($row->user); //get sender info
		$get_user_color = $this->LTChatDataKeeper->get_user_color($my_info->level);
		
		//check valid ip
		if (is_ip($action_on) !== false){
		$trueip = $action_on;
		$text = str_replace(array("#ip#","#sendercolor#","#sender#","#senderrightcolor#","#reason#"), 
		                    array($trueip, $my_info->color,$my_info->nick,$get_user_color,$reason), 
							ChFun_banip_OkReason);
		//check valid user
		}elseif($user_info != NULL && $online == 1 ){
		$ban_user = $user_info->last_pcip;
		$text = str_replace(array("#user#","#sendercolor#","#sender#","#senderrightcolor#","#reason#"), 
		                    array($user_info->nick, $my_info->color, $my_info->nick, $get_user_color, $reason), 
							ChFun_banuser_OkReason);
		
		}else{
		return array('text' => ChFun_not_valid_ip, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}

		//check for not null reason
	    if($reason == NULL)
		return array('text' => ChFun_write_banip_reason, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else 
		
		if($trueip != NULL){
		$type = 'banpcip';
		$this->LTChatDataKeeper->banip_address($text, $type, $trueip, FALSE);
		}
		if($ban_user != NULL){
		$type = 'banpcuser';
		$this->LTChatDataKeeper->ban_nick($text, $type, $ban_user, $user_info->nick);
		}
		
		if($user_info != NULL){
		$this->LTChatDataKeeper->insert_check4action($user_info->id, $type);
		}
		
		$check_vaild = ($user_info != NULL) ? $user_info->nick : $trueip;
		
		$this->LTChatDataKeeper->post_logs($text, $room, $type, $check_vaild);
		$this->LTChatDataKeeper->post_reason($text, $room);		
		$this->LTChatDataKeeper->post_private_logs(str_replace(array("#text#"), array($text), ChFun_private_logs_syntex));
		$this->LTChatDataKeeper->abuse_public($this->LTChatDataKeeper->get_user_id(),$room);
  	  }
  	  else
  	    return array('text' => '', 'type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------

  	function command_sus($command_info)
  	{
	  if($room != "")		$room = $command_info['room'];
	  else							$room = 'Arabia';
  	  $row = $command_info['row'];
	  $type = 'sus';
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $command_info['room'])
  	  {
	    
        $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	  
        $user_name = trim($command_info['params'][1]);
        unset($command_info['params'][1]);
        $reason = trim(implode(" ",$command_info['params']));
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user will be suspaned
		$my_info = $this->LTChatDataKeeper->get_user_by_nick($row->user); //get sender info
		$online  = $this->LTChatDataKeeper->online($user_info->id); //get suspaned status (on)||(off)line )
		$get_user_color = $this->LTChatDataKeeper->get_user_color($my_info->level); //get sender level color
		
		//check command for stop sus self
		if($user_info->nick == $row->user)
		return array('text' => ChFun_sus_your_self, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));

		//check for op exists on database
        if($user_info == null)
  	  	  return array('text' => str_replace("#user#", $user_name, ChFun_sus_BadNick), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  	else
  	  	{
		
		if($my_info->level < 30){
		//check command for stop sus user with offline mode!
		if($online == FALSE)
		return array('text' => str_replace(
		array("#user#"), 
		array($user_name), ChFun_sus_offline), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		}

		if($user_info->level == 0)
		return array('text' => str_replace(array("#user#"), array($user_name), ChFun_sus_offline), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));		
		
		//check command for stop sus same level or high level
		if($my_info->level <= $user_info->level)
		return array('text' => ChFun_sus_same_level, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));

		$text = str_replace(array("#user#","#rights#","#sendercolor#","#sender#","#senderrightcolor#","#reason#"), 
		                    array($user_info->nick, $user_info->rights, $my_info->color,$my_info->nick,$get_user_color,$reason), 
						   ChFun_sus_OkReason);

		//check for not null reason
	    if($reason == NULL)
		return array('text' => ChFun_write_sus_reason, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else
		$this->LTChatDataKeeper->suspended_user($user_info->id, $text, $type, $user_info->nick);
		$this->LTChatDataKeeper->insert_check4action($user_info->id,$type);
		$this->LTChatDataKeeper->post_reason($text, $room);	
		$this->LTChatDataKeeper->post_logs($text, $room, $type, $user_info->nick);	
		$this->LTChatDataKeeper->post_private_logs(str_replace(array("#text#"), array($text), ChFun_private_logs_syntex));
		$this->LTChatDataKeeper->abuse_public($this->LTChatDataKeeper->get_user_id(),$room);
  	  	}
  	  }
  	  else
  	    return array('text' => '', 'type' => 'skip');
  	}
  	
  	//---------------------------------------------------------------------
	
	function command_unsus($command_info)
	{
	  if($room != "")		$room = $command_info['room'];
	  else							$room = 'Arabia';
  	  $row = $command_info['row'];
	  $type = 'unsus';
	  
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $command_info['room'])
  	  {
        $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	  
        $user_name = trim($command_info['params'][1]);
        unset($command_info['params'][1]);
		
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user will be disable
		$my_info = $this->LTChatDataKeeper->get_user_by_nick($row->user); //get my info
		$get_user_color = $this->LTChatDataKeeper->get_user_color($my_info->level);
		
		//check for user doesnt exists
  		if($user_info == NULL)
  		{
  		  $u_doesnt_exists = str_replace("#user#", $user_name, LTChatCore_user_doesnt_exists);
  		  return array('text' => $u_doesnt_exists, 'type' => 'private','other_options' => array('type_handle' => 'error'));
  		}
  		
		//check command for unsus self
		if($user_info->nick == $row->user)
		return array('text' => ChFun_unsus_your_self, 
		             'type' => 'private', 
					 'other_options' => array('type_handle' => 'error'));
		
		//check command for unsus same level or high level
		if($my_info->level <= $user_info->level)
		return array('text' => ChFun_unsus_same_level, 
		             'type' => 'private', 
					 'other_options' => array('type_handle' => 'error'));
		
		//check command to look for level (unsus command not allowed unsus user)
		if($user_info->level == 0)
		return array('text' => ChFun_unsus_user, 
		             'type' => 'private', 
					 'other_options' => array('type_handle' => 'error'));
		
		//check command for user in disable ro enable him!
		if($this->LTChatDataKeeper->whois_have_action($user_info->id, 'sus') == 0)
		return array('text' => str_replace(array("#user#"), array($user_name), ChFun_already_unsus), 
		             'type' => 'private', 
					 'other_options' => array('type_handle' => 'error'));
					 
	    else
		$text = str_replace(array("#user#","#rights#","#sendercolor#","#sender#","#senderrightcolor#"), 
		                    array($user_info->nick, $user_info->rights, $my_info->color,$my_info->nick,$get_user_color), ChFun_un_sused);
		
		//delele an operator from suspaned and include, postlogs on database and on private logs
		$this->LTChatDataKeeper->delete_sus($user_info->id);
		$this->LTChatDataKeeper->abuse_public($this->LTChatDataKeeper->get_user_id(),$room);
		
		$out = str_replace(array("#user#"), array($user_info->nick), ChFun_un_sused_msg);		
		return array('text' => $out, 'type' => 'private'); 
	  
	  }else
  	    return array('text' => '', 'type' => 'skip');
	}
	
  	//---------------------------------------------------------------------

  	function command_kick($command_info)
  	{
	  if($room != "")		$room = $command_info['room'];
	  else							$room = 'Arabia';
  	  $row = $command_info['row'];
	  $type = 'kick';
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $command_info['room'])
  	  {
	    
        $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	  
        $user_name = trim($command_info['params'][1]);
        unset($command_info['params'][1]);
        $reason = trim(implode(" ",$command_info['params']));
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user will be kicked
		$my_info = $this->LTChatDataKeeper->get_user_by_nick($row->user); //get my info
		$online  = $this->LTChatDataKeeper->online($user_info->id); //get status (on)||(off)line )
		$get_user_color = $this->LTChatDataKeeper->get_user_color($my_info->level);
		
		//check command for stop kick self
		if($user_info->nick == $row->user)
		return array('text' => ChFun_kick_your_self, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));

		//check for op exists on database
        if($user_info == NULL)
  	  	  return array('text' => str_replace("#user#", $user_name, ChFun_kick_BadNick), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  	else
  	  	{
		
		//check command for stop kick user with offline mode!
		if($online == 0)
		return array('text' => str_replace(array("#user#"), array($user_name), ChFun_kick_offline), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));

		
		//check command for stop kick same level or high level
		if($my_info->level <= $user_info->level)
		return array('text' => ChFun_kick_same_level, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));

		$text = str_replace(array("#user#","#rights#","#sendercolor#","#sender#","#senderrightcolor#","#reason#"), 
		                    array($user_info->nick, $user_info->rights, $my_info->color,$my_info->nick,$get_user_color,$reason), ChFun_kick_OkReason);

		//check for not null reason
	    if($reason == NULL)
		return array('text' => ChFun_write_kick_reason, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else 
		$this->LTChatDataKeeper->kick_user($user_info->id, $text, $type, $user_info->nick);		
		$this->LTChatDataKeeper->post_reason($text, $room);	
		$this->LTChatDataKeeper->post_logs($text, $room, $type, $user_info->nick);	
		$this->LTChatDataKeeper->post_private_logs(str_replace(array("#text#"), array($text), ChFun_private_logs_syntex));
		$this->LTChatDataKeeper->abuse_public($this->LTChatDataKeeper->get_user_id(),$room);
		$this->LTChatDataKeeper->insert_check4action($user_info->id,$type);
  	  	}
  	  }
  	  else
  	    return array('text' => '', 'type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------

  	function command_multikick($command_info)
  	{
	  if($room != "")		$room = $command_info['room'];
	  else							$room = 'Arabia';
  	  $row = $command_info['row'];
	  $type = 'mkick';
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $command_info['room'])
  	  {
	    
        $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	  
        $user_name = trim($command_info['params'][1]);
        unset($command_info['params'][1]);
        $reason = trim(implode(" ",$command_info['params']));
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user will be kicked
		
		$my_info = $this->LTChatDataKeeper->get_user_by_nick($row->user); //get my info
		$online  = $this->LTChatDataKeeper->online($user_info->id); //get status (on)||(off)line )
		$get_user_color = $this->LTChatDataKeeper->get_user_color($my_info->level);
		
		//check for not null reason
	    if($user_info == NULL)
		return array('text' => ChFun_write_multikick_nick, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));		
		//check command for stop kick user with offline mode!
		if($online == 0)
		return array('text' => str_replace(array("#user#"), array($user_name), ChFun_kick_offline), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		
		//check command to look for level (multi kick command not allowed kick op)
		if($user_info->level != 0)
		return array('text' => ChFun_multikick_op, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));

		//check for not null reason
	    if($reason == null)
		return array('text' => ChFun_write_multikick_reason, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else 
		$get = $this->LTChatDataKeeper->multikick_user($user_info->id, $user_info->nick, $user_info->last_ip);
		
		if(isset($get['number']) && isset($get['name']) && $get['number'] > 0)
		$text = str_replace(array("#number#","#name#","#user#","#rights#","#sendercolor#","#sender#","#senderrightcolor#","#reason#"), array($get['number'], $get['name'], $user_info->nick, $user_info->rights, $my_info->color,$my_info->nick,$get_user_color,$reason), ChFun_multikick_OkReason);
		
		$this->LTChatDataKeeper->post_reason($text, $room);	
		$this->LTChatDataKeeper->post_logs($text, $room, $type, $user_info->nick);
		$this->LTChatDataKeeper->post_private_logs(str_replace(array("#text#"), array($text), ChFun_private_logs_syntex));
		$this->LTChatDataKeeper->abuse_public($this->LTChatDataKeeper->get_user_id(),$room);
  	  
	  }##end row->user
  	  else
  	    return array('text' => '', 'type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------
	
	function command_disable($command_info)
	{
	  if($room != "")		$room = $command_info['room'];
	  else							$room = 'Arabia';
  	  $row = $command_info['row'];
	  $type = 'disable';
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $command_info['room'])
  	  {
        $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	  
        $user_name = trim($command_info['params'][1]);
        unset($command_info['params'][1]);
        $reason = trim(implode(" ",$command_info['params']));
		
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user will be disable
		$my_info = $this->LTChatDataKeeper->get_user_by_nick($row->user); //get my info
		$get_user_color = $this->LTChatDataKeeper->get_user_color($my_info->level);
		
		//check command for stop disable self
		if($user_info->nick == $row->user)
		return array('text' => ChFun_disable_your_self, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		
		//check for user doesnt exists
  		if($user_info == NULL)
  		{
  		  $u_doesnt_exists = str_replace("#user#", $user_name, LTChatCore_user_doesnt_exists);
  		  return array('text' => $u_doesnt_exists, 'type' => 'private','other_options' => array('type_handle' => 'error'));
  		}
		
		//check command for stop disable same level or high level
		if($my_info->level <= $user_info->level)
		return array('text' => ChFun_disable_same_level, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		
		//check command to look for level (disable command not allowed disable user)
		if($user_info->level == 0)
		return array('text' => ChFun_disable_user, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		
		//check command for stop already user in disable!
		if($this->LTChatDataKeeper->whois_have_action($user_info->id, $type) == 1)
		return array('text' => str_replace(array("#user#"), array($user_name), ERROR_already_disable), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));

		//check for null reason
	    if($reason == NULL)
		return array('text' => ChFun_write_disable_reason, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else
		$text = str_replace(array("#user#","#rights#","#sendercolor#","#sender#","#senderrightcolor#","#reason#"), 
		                    array($user_info->nick, $user_info->rights, $my_info->color,$my_info->nick,$get_user_color,$reason), 
						   ChFun_disable_Ok);
		
		$this->LTChatDataKeeper->disable_member($user_info->id, $text, $type, $user_info->nick);	
		$this->LTChatDataKeeper->insert_check4action($user_info->id, $type);	
		$this->LTChatDataKeeper->post_reason($text, $room);	
		$this->LTChatDataKeeper->post_logs($text, $room, $type, $user_info->nick);	
		$this->LTChatDataKeeper->post_private_logs(str_replace(array("#text#"), array($text), ChFun_private_logs_syntex));
		$this->LTChatDataKeeper->abuse_public($this->LTChatDataKeeper->get_user_id(),$room);
	  
	  }else
  	    return array('text' => '', 'type' => 'skip');
	}
	
  	//---------------------------------------------------------------------
	
	function command_enable($command_info)
	{
	  if($room != "")		$room = $command_info['room'];
	  else							$room = 'Arabia';
  	  $row = $command_info['row'];
	  $type = 'enable';
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $command_info['room'])
  	  {
        $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	  
        $user_name = trim($command_info['params'][1]);
        unset($command_info['params'][1]);
		
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user will be disable
		$my_info = $this->LTChatDataKeeper->get_user_by_nick($row->user); //get my info
		$get_user_color = $this->LTChatDataKeeper->get_user_color($my_info->level);
		
		//check command for stop enable self
		if($user_info->nick == $row->user)
		return array('text' => ChFun_enable_your_self, 
		             'type' => 'private', 
					 'other_options' => array('type_handle' => 'error'));
		
		//check for user doesnt exists
  		if($user_info == NULL)
  		{
  		  $u_doesnt_exists = str_replace("#user#", $user_name, LTChatCore_user_doesnt_exists);
  		  return array('text' => $u_doesnt_exists, 'type' => 'private','other_options' => array('type_handle' => 'error'));
  		}
		
		//check command for stop enable same level or high level
		if($my_info->level <= $user_info->level)
		return array('text' => ChFun_enable_same_level, 
		             'type' => 'private', 
					 'other_options' => array('type_handle' => 'error'));
		
		//check command to look for level (enable command not allowed enable user)
		if($user_info->level == 0)
		return array('text' => ChFun_enable_user, 
		             'type' => 'private', 
					 'other_options' => array('type_handle' => 'error'));
		
		//check command for user in disable ro enable him!
		if($this->LTChatDataKeeper->whois_have_action($user_info->id, 'disable') == 0)
		return array('text' => str_replace(array("#user#"), array($user_name), ChFun_already_enable), 
		             'type' => 'private', 
					 'other_options' => array('type_handle' => 'error'));
					 
	    else
		$text = str_replace(array("#user#","#rights#","#sendercolor#","#sender#","#senderrightcolor#"), 
		                    array($user_info->nick, $user_info->rights, $my_info->color,$my_info->nick,$get_user_color), ChFun_enable_Ok);
		
		$this->LTChatDataKeeper->delete_disable($user_info->id);
		$this->LTChatDataKeeper->post_logs($text, $room, $type, $user_info->nick);	
		$this->LTChatDataKeeper->post_private_logs(str_replace(array("#text#"), array($text), ChFun_private_logs_syntex));
		$this->LTChatDataKeeper->abuse_public($this->LTChatDataKeeper->get_user_id(),$room);
		
		$out = str_replace(array("#user#"), array($user_info->nick), ChFun_enable_msg);		
		return array('text' => $out, 'type' => 'private'); 
	  
	  }else
  	    return array('text' => '', 'type' => 'skip');
	}

  	//---------------------------------------------------------------------
  	function command_warn($command_info)
  	{
	  if($room != "")		$room = $command_info['room'];
	  else							$room = 'Arabia';
  	  $row = $command_info['row'];
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $command_info['room'])
  	  {
	    
        $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	  
        $user_name = trim($command_info['params'][1]);
        unset($command_info['params'][1]);
        $reason = trim(implode(" ",$command_info['params']));
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user will be kicked
		$my_self = $this->LTChatDataKeeper->get_user_name(); //get my name from session for get my info
		$my_info = $this->LTChatDataKeeper->get_user_by_nick($my_self); //get my info
		$get_user_color = $this->LTChatDataKeeper->get_user_color($my_info->level);
		$online  = $this->LTChatDataKeeper->online($user_info->id); //get status (on)||(off)line )
		
		//check command for stop warn self
		if($user_info->nick == $row->user)
		return array('text' => ChFun_warn_your_self, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));

		//check for op exists on database
        if($user_info == null)
  	  	  return array('text' => str_replace("#user#", $user_name, ChFun_warn_BadNick), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  	else
  	  	{
		
		//check command for stop warn user with offline mode!
		if($online == FALSE)
		return array('text' => str_replace(array("#user#"), array($user_name), ChFun_warn_offline), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));

		
		//check command for stop warn same level or high level
		if($my_info->level <= $user_info->level)
		return array('text' => ChFun_warn_same_level, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));


		//check for not null reason
	    if($reason == NULL)
		return array('text' => ChFun_write_warn_reason, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else 
		$text = str_replace(array("#user#","#rights#","#sendercolor#","#sender#","#senderrightcolor#","#reason#"), 
		                    array($user_info->nick, $user_info->rights, $my_info->color,$my_info->nick,$get_user_color,$reason), 
						   ChFun_warn_Ok);
		$this->LTChatDataKeeper->post_reason($text, $room);
        $this->LTChatDataKeeper->post_private_reason($text, $user_info->id, 1);
		$this->LTChatDataKeeper->post_private_logs(str_replace(array("#text#"), array($text), ChFun_private_logs_syntex));
		$this->LTChatDataKeeper->abuse_public($this->LTChatDataKeeper->get_user_id(),$room);
  	  	}
  	  }
  	  else
  	    return array('text' => '', 'type' => 'skip');
  	}
  	//---------------------------------------------------------------------

  	function command_jail($command_info)
  	{
	  if($room != "")		$room = $command_info['room'];
	  else							$room = 'Arabia';
  	  $row = $command_info['row'];
	  $type = 'jail';
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $command_info['room'])
  	  {
	    
        $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	  
        $user_name = trim($command_info['params'][1]);
        unset($command_info['params'][1]);
        $reason = trim(implode(" ",$command_info['params']));
		$user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user will be kicked
		$my_info = $this->LTChatDataKeeper->get_user_by_nick($row->user); //get my info
		$online  = $this->LTChatDataKeeper->online($user_info->id); //get status (on)||(off)line )
		$get_user_color = $this->LTChatDataKeeper->get_user_color($my_info->level);
		
		//check command for stop jail self
		if($user_info->nick == $row->user)
		return array('text' => ChFun_kick_your_self, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));

		//check for op exists on database
        if($user_info == NULL)
  	  	  return array('text' => str_replace("#user#", $user_name, ChFun_kick_BadNick), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  	else
  	  	{
		
		//check command for stop kick user with offline mode!
		if($online == 0)
		return array('text' => str_replace(array("#user#"), array($user_name), ChFun_kick_offline), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));

		//check command for stop already user in jail!
		if($this->LTChatDataKeeper->whois_have_action($user_info->id, $type) == 1)
		return array('text' => str_replace(array("#user#"), array($user_name), ERROR_msg_already_jail), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
		
		//check command for stop kick same level or high level
		if($my_info->level <= $user_info->level)
		return array('text' => ChFun_kick_same_level, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));

		$text = str_replace(array("#user#","#rights#","#sendercolor#","#sender#","#senderrightcolor#","#reason#"), 
		                    array($user_info->nick, $user_info->rights, $my_info->color,$my_info->nick,$get_user_color,$reason), 
						   ChFun_kick_OkReason);

		//check for not null reason
	    if($reason == NULL)
		return array('text' => ChFun_write_kick_reason, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	    else 
		$this->LTChatDataKeeper->jail_user($user_info->id, $text, $type, $user_info->nick);		
		$this->LTChatDataKeeper->post_reason($text, $room);	
		$this->LTChatDataKeeper->post_logs($text, $room, $type, $user_info->nick);	
		$this->LTChatDataKeeper->post_private_logs(str_replace(array("#text#"), array($text), ChFun_private_logs_syntex));
		$this->LTChatDataKeeper->abuse_public($this->LTChatDataKeeper->get_user_id(),$room);
		$this->LTChatDataKeeper->insert_check4action($user_info->id,$type);
  	  	}
  	  }
  	  else
  	    return array('text' => '', 'type' => 'skip');
  	}
		
  	//---------------------------------------------------------------------

  	function command_private_msg($command_info)
  	{
  	  $params = $command_info['params'];
  	  $row = $command_info['row'];
  	  $room = $command_info['room'];
  	  $private_id = $command_info['private_id'];
	  
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $room)
  	  {
  		$this->LTChatDataKeeper->delete_message_id($row->id, $private_id);
  		$user_info = $this->LTChatDataKeeper->get_user_by_nick(trim($params[1]));

  		if($user_info == null)
  		{
  		  $u_doesnt_exists = str_replace("#user#",htmlspecialchars(trim($params[1])), LTChatCore_user_doesnt_exists);
  		  return array('text' => $u_doesnt_exists, 'type' => 'private','other_options' => array('type_handle' => 'error'));
  		}
  		elseif ($this->LTChatDataKeeper->online($user_info->id) != 1)
  		{
  		  return array('text' => str_replace("#user#", $user_info->nick, offline), 'type' => 'private','other_options' => array('type_handle' => 'error'));
  		}
  		elseif ($user_info->nick == $row->user)
  		{
  		  return array('text' => ChFun_prv_msgtome, 'type' => 'private','other_options' => array('type_handle' => 'error'));
  		}

  		unset($params[1]);
  		$text = implode(" ",$params);

        $msg_text = $this->LTChatDataKeeper->post_private_msg($text, $user_info->id, 1);
        $error_exp = explode(" ",$msg_text);

		if($error_exp[0] == "/ERROR")
		{
		  $error_out = $this->command_ERROR(array('params'=> $error_exp));
		  $out = $error_out['text'];
		}
		else 
		{
	      $link = "./private.php?private_id={$user_info->id}";
	      //$text = load_internal_emoticons($text); //replace the *48*
	      //$text = load_internal_letters_emoticons($text); //replace *h*
	      $out = $text;
	      $data_type = "prv_msg_send";
		  
		  $get_user_status = $this->LTChatDataKeeper->whois_have_action($user_info->id, 'away');
		  if($get_user_status == 1){
		  $status = "System alert, the user $user_info->nick is in the away mode but the message has been sent!";
		  }else{
		  $status = "The message is delivered to";
		  }
		  
	      $other_options = array('status' => $status, 'link' => $link, 'nick' => $user_info->nick);
		}

	  	$this->LTChatDataKeeper->delete_message_id($row->id, $private_id);
	  	
	  	if($data_type)
          return array('data_type' => $data_type, 'text' => $out, 'type' => 'private', 'other_options' => $other_options);
        else 
          return array('text' => $out, 'type' => 'private', 'other_options' => $other_options);        
  	  }
  	  else 
  	  {
  	  	return array('text' => '', 'type' => 'skip');
  	  }
  	}
  	//---------------------------------------------------------------------
    function command_emoticons($command_info)
    {
  	   if(file_exists(LTChatTemplateSystemPath."tpl_emoticons.txt"))
  	   {
		  $tpl_emoticons = file(LTChatTemplateSystemPath."tpl_emoticons.txt");
		  foreach ($tpl_emoticons as $line)
		  {
		  	$lines = explode("\t",$line);
		  	if(count($lines) <2) continue;

		  	$from = htmlspecialchars($lines[0]);
		  	$emoticons .= str_replace(array("#path#","#info#"),array(LTChatTemplatePath."img/emoticons/".$lines[count($lines)-1],$from), ChFun_emoticons_Style);
		  }
  	   }
  	   return array('text' => $emoticons, 'type' => 'private');
    }
  	//---------------------------------------------------------------------
    function command_removefriend($command_info)
    {
  	  $params = $command_info['params'];
      $params_new = array();
      $params_new[1] = $this->language_config['help']['friend']['except_params_static']['del'];
	  foreach ($params as $param)
	  	$params_new[] = $param;

	  $command_info['params'] = $params_new;
	  $command_info['command_help'] = $this->language_config['help']['friend'];
	  
	  return $this->command_friend($command_info);
    }
	
  	//---------------------------------------------------------------------

  	function command_unteam($command_info)
  	{
  	  $params = $command_info['params'];
      $params_new = array();
      $params_new[1] = $this->language_config['help']['team']['except_params_static']['del'];
	  foreach ($params as $param)
	  	$params_new[] = $param;

	  $command_info['params'] = $params_new;
	  $command_info['command_help'] = $this->language_config['help']['team'];
	  
	  return $this->command_team($command_info);
  	}
	
  	//---------------------------------------------------------------------

  	function command_showteam($command_info)
  	{
  	  $params = $command_info['params'];
      $params_new = array();
      $params_new[1] = $this->language_config['help']['team']['except_params_static']['show'];
	  foreach ($params as $param)
	  	$params_new[] = $param;

	  $command_info['params'] = $params_new;
	  $command_info['command_help'] = $this->language_config['help']['team'];
	  
	  return $this->command_team($command_info);
  	}
	
  	//---------------------------------------------------------------------
	
  	function command_team($command_info)
  	{
  	  $row = $command_info['row'];
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $command_info['room'])
  	  {
	  	  $params = $command_info['params'];
	  	  $row = $command_info['row'];
	
	  	  $add_team = true;
	  	  if($command_info['command_help']['except_params_static']['add'] == trim($params[1]))
	  	  {
	  	    unset($params[1]);
	  	  }
	  	  elseif($command_info['command_help']['except_params_static']['del'] == trim($params[1]))
	  	  {
	  	    unset($params[1]);
	  	  	$add_team = false;
	  	  }
	  	  elseif($command_info['command_help']['except_params_static']['show'] == trim($params[1]))
	  	  {	
		  
			$res = $this->LTChatDataKeeper->get_team_list();
			if($res == NULL)
			{
	  		  return array('text' => ChFun_team_list_empty, 'type' => 'private');
			}
			else 
			{
			  if(is_array($res['list']))
			    foreach ($res['list'] as $info)
					
			  $out = str_replace(array("#team_user#", "#team_group_title#", "#team_group_name#", "#team_user_level#"), 
			                     array($info->nick, $info->g_title, $info->g_name, $info->level), ChFun_team_Show);
	  		  return array('text' => $out, 'type' => 'private');
			}
	  	  }
	
	  	  $u_name = implode(" ", $params);
	      $u_name = trim($u_name);
	
	      $user_info = $this->LTChatDataKeeper->get_user_by_nick($u_name);
	  	  $this->LTChatDataKeeper->delete_message_id($row->id, $private_id);
	
	  	  if($user_info == NULL)
	  	  {
	  		$u_doesnt_exists = str_replace("#user#", $u_name, LTChatCore_user_doesnt_exists);
	  		return array('text' => $u_doesnt_exists, 
			             'type' => 'private', 
						 'other_options' => array('type_handle' => 'error'));
	  	  }
	
	  	  if($add_team)
	  	  {
		    $check_exists = $this->LTChatDataKeeper->team_user_exists($user_info->id);
		    
			if($check_exists == TRUE){
	  	  	$out = str_replace(array("#user#", "#group_name#"), array($user_info->nick, 'Team Members'), ChFun_Team_Add);
	  	    $this->LTChatDataKeeper->team_user(1, $user_info->id);
			}else{
			$out = str_replace("#user#", $user_info->nick, ChFun_team_user_exists);
			}
			
	  	  }
	  	  else
	  	  {
	  	  	$out = str_replace(array("#user#", "#group_name#"), array($user_info->nick, 'Team Members'), ChFun_Team_Del);
	  	    $this->LTChatDataKeeper->team_user(0, $user_info->id);
	  	  }
	
	  	  return array('text' => $out, 'type' => 'private');
  	  }
  	  else 
  	    return array('text' => '', 'type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------
	
  	function command_friend($command_info)
  	{
  	  $row = $command_info['row'];
  	  if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $command_info['room'])
  	  {
	  	  $params = $command_info['params'];
	  	  $row = $command_info['row'];
	
	  	  $add_friend = true;
	  	  if($command_info['command_help']['except_params_static']['add'] == trim($params[1]))
	  	  {
	  	    unset($params[1]);
	  	  }
	  	  elseif($command_info['command_help']['except_params_static']['del'] == trim($params[1]))
	  	  {
	  	    unset($params[1]);
	  	  	$add_friend = false;
	  	  }
	  	  elseif($command_info['command_help']['except_params_static']['show'] == trim($params[1]))
	  	  {	
			$res = $this->LTChatDataKeeper->get_friend_list();
			if($res == null)
			{
	  		  return array('text' => ChFun_friend_Eempty, 'type' => 'private');
			}
			else 
			{
			  if(is_array($res['from']))
			    foreach ($res['from'] as $info)
			      if($friend_from == null)
			        $friend_from = $info->nick;
			      else 
			        $friend_from .= ChFun_friend_ShowSep.$info->nick;
			       
			  if(is_array($res['to']))
			    foreach ($res['to'] as $info)
			      if($friend_to == null)
			        $friend_to = $info->nick;
			      else
			        $friend_to .= ChFun_friend_ShowSep.$info->nick;
	
			  $out = str_replace(array("#friend_to#", "#friend_from#","#friend_from_text#","#friend_to_text#"), array($friend_to, $friend_from,ChFun_friend_from_text,ChFun_friend_to_text), ChFun_friend_Show);
	  		  return array('text' => $out, 'type' => 'private');
			  		
			}
	  	  }
	
	  	  $u_name = implode(" ", $params);
	      $u_name = trim($u_name);
	
	      $user_info = $this->LTChatDataKeeper->get_user_by_nick($u_name);
	  	  $this->LTChatDataKeeper->delete_message_id($row->id, $private_id);
	
	  	  if($user_info == null)
	  	  {
	  		$u_doesnt_exists = str_replace("#user#", $u_name, LTChatCore_user_doesnt_exists);
	  		return array('text' => $u_doesnt_exists, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	  	  }
	
	  	  if($add_friend)
	  	  {
	  	  	$out = str_replace("#user#", $user_info->nick, ChFun_friend_Add);
	  	    $this->LTChatDataKeeper->friend_user_add($user_info->id);
	  	  }
	  	  else
	  	  {
	  	  	$out = str_replace("#user#", $user_info->nick, ChFun_friend_Del);
	  	    $this->LTChatDataKeeper->friend_user_del($user_info->id);
	  	  }
	
	  	  return array('text' => $out, 'type' => 'private');
  	  }
  	  else 
  	    return array('text' => '', 'type' => 'skip');
  	}
  	//---------------------------------------------------------------------

  	function command_unignore($command_info)
  	{
  	  $params = $command_info['params'];
      $params_new = array();
      $params_new[1] = $this->language_config['help']['ignore']['except_params_static']['del'];
	  foreach ($params as $param)
	  	$params_new[] = $param;

	  $command_info['params'] = $params_new;
	  $command_info['command_help'] = $this->language_config['help']['ignore'];
	  
	  return $this->command_ignore($command_info);
  	}

  	function command_ignore($command_info)
  	{
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  	return array('text' => '', 'type' => 'skip');

  	  $params = $command_info['params'];
  	  $row = $command_info['row'];
	  $private_id = $command_info['private_id'];

  	  $add_ignore = true;
  	  if($command_info['command_help']['except_params_static']['add'] == $params[1])
  	    unset($params[1]);
  	  elseif($command_info['command_help']['except_params_static']['del'] == $params[1])
  	  {
  	    unset($params[1]);
  	  	$add_ignore = false;
  	  }

  	  $u_name = implode(" ", $params);
      $u_name = trim($u_name);

      $user_info = $this->LTChatDataKeeper->get_user_by_nick($u_name);
  	  $this->LTChatDataKeeper->delete_message_id($row->id, $private_id);

  	  if($user_info == null)
  	  {
  		$u_doesnt_exists = str_replace("#user#",$u_name, LTChatCore_user_doesnt_exists);
  		return array('text' => $u_doesnt_exists, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  }

  	  if($add_ignore)
  	  {
  	  	$out = str_replace("#user#", $user_info->nick, ChFun_ignore_Add);
  	    $this->LTChatDataKeeper->ignore_user_add($user_info->id);
  	  }
  	  else
  	  {
  	  	$out = str_replace("#user#", $user_info->nick, ChFun_ignore_Del);
  	    $this->LTChatDataKeeper->ignore_user_del($user_info->id);
  	  }

  	  return array('text' => $out, 'type' => 'private');
  	}
	
  	//---------------------------------------------------------------------

  	function command_unwait($command_info)
  	{
  	  $params = $command_info['params'];
      $params_new = array();
      $params_new[1] = $this->language_config['help']['wait']['except_params_static']['del'];
	  foreach ($params as $param)
	  	$params_new[] = $param;

	  $command_info['params'] = $params_new;
	  $command_info['command_help'] = $this->language_config['help']['wait'];
	  
	  return $this->command_wait($command_info);
  	}
	
  	//---------------------------------------------------------------------
  	
	function command_wait($command_info)
  	{
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  return array('text' => '', 'type' => 'skip');
  	 
  	  $params = $command_info['params'];
  	  $row = $command_info['row'];
	  $private_id = $command_info['private_id'];

  	  $add_wait = true;
  	  if($command_info['command_help']['except_params_static']['add'] == $params[1])
  	    unset($params[1]);
  	  elseif($command_info['command_help']['except_params_static']['del'] == $params[1])
  	  {
  	    unset($params[1]);
  	  	$add_wait = false;
  	  }

  	  $u_name = implode(" ", $params);
      $u_name = trim($u_name);

      $user_info = $this->LTChatDataKeeper->get_user_by_nick($u_name);
  	  $this->LTChatDataKeeper->delete_message_id($row->id, $private_id);

  	  if($user_info == NULL)
  	  {
  		$u_doesnt_exists = str_replace("#user#",$u_name, LTChatCore_user_doesnt_exists);
  		return array('text' => $u_doesnt_exists, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
  	  }

  	  if($add_wait)
  	  {
	    $check_wait = $this->LTChatDataKeeper->check_wait_list($user_info->id);
		if($check_wait == TRUE){
  	  	$out = str_replace("#user#", $user_info->nick, ChFun_wait_Add);
  	    $this->LTChatDataKeeper->wait_user_add($user_info->id);
		}else{
		$out = str_replace("#user#", $user_info->nick, ChFun_wait_exists);
		}
  	  }
  	  else
  	  {
  	  	$out = str_replace("#user#", $user_info->nick, ChFun_wait_Del);
  	    $this->LTChatDataKeeper->wait_user_del($user_info->id);
  	  }

  	  return array('text' => $out, 'type' => 'private');
  	}
  	//---------------------------------------------------------------------
	 	
  	function command_clear_public($command_info)
  	{
  	  $row = $command_info['row'];
	  
	  if($row->user == $this->LTChatDataKeeper->get_user_name()){
  	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
  	    $other_options = array('function' => 'clear');
  	    return array('data_type' => 'functions', 'text' => '', 'type' => 'private', 'other_options' => $other_options);
	  }
	  else
	  return array('text' => '', 'type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------
	 	
  	function command_clear_all_public($command_info)
  	{
  	  $row = $command_info['row'];
	  
	  if($row->user == $this->LTChatDataKeeper->get_user_name()){
	  
  	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);
		
		$check = $this->LTChatDataKeeper->clear_all_public();

		return array('text' => '** All public rooms has been cleared', 'type' => 'private');
	  }
	  else
	  return array('text' => '', 'type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------
   function show_help_groups($command, $id){
        $my_command = substr($command, 1);
		$g_command = $this->LTChatDataKeeper->whois_have_commands($id);
		
		if($g_command !== false){
			$g_command_filtred = array_filter($g_command);	
			if(array_key_exists($my_command, $g_command_filtred))
			   return 1;
		}else{
		     return NULL;
		}
   }
 
  	function command_tpl_fullhelp()
  	{
      $help_info = $this->language_config['help'];
	  $id = $this->LTChatDataKeeper->get_user_id();
	  
      foreach ($help_info as $key => $command_help)
        
		if($this->show_help_groups($command_help['commands'][0], $id) == 1 || $command_help[$this->LTChatDataKeeper->get_user_level()] == true && ( is_callable(array(get_class($this),$command_help['execute_function'])) || is_callable(array(get_class($this),$command_help['execute_tpl_function']))))
          
		$functions[$key] = $this->command_show_help_info_parse($command_help,'info');//str_replace(array("#command#","#args#","#description#"), array($com, $args, $desc), ChFun_help_ListStyle);

  	  return array('functions' => $functions);
  	}

  	function command_tpl_bug()
  	{
	  return array();
  	}

  	function command_tpl_config()
  	{
	  return array();
  	}
  
  	function load_tpl($command_info)
  	{
  	  $row = $command_info['row'];

  	  if(is_object($row))
  	    $this->LTChatDataKeeper->delete_message_id($row->id, -1);

  	  $other_vars = urlencode(serialize($command_info['other_vars']));
  	  $other_options = array('load_template' => $command_info['command_help']['load_template'], 'other_vars' => $other_vars);

  	  return array('data_type' => 'template', 'text' => '', 'type' => 'private', 'other_options' => $other_options);
  	}
  	//---------------------------------------------------------------------

  	function command_info($command_info)
  	{
	  $row = $command_info['row'];
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
	  	  $params = $command_info['params'];

	  	  $this->LTChatDataKeeper->delete_message_id($row->id, -1);

	  	  $nick = implode(" ", $params);
	      $nick = trim($nick);

	      $user = $this->LTChatDataKeeper->get_user_by_nick($nick);
	      $command_info['other_vars']['login'] = $nick;

	      if($user == null)
	  	    return array('text' => str_replace("#user#", $nick, ChFun_info_BadUserName), 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	      else
	        return $this->load_tpl($command_info);
  	  }

  	  return array('type' => 'skip');
  	}
  	//---------------------------------------------------------------------
  	
  	function command_room($command_info)
  	{
	  $row = $command_info['row'];
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
	  	  $params = $command_info['params'];
	
	  	  $this->LTChatDataKeeper->delete_message_id($row->id, -1);

	  	  $room = implode(" ", $params);
	      $room = trim($room);

	  	  if(count($params) > 1 || $room == null)
	  	  {
	  	    return array('text' => ChFun_room_BadName, 'type' => 'private', 'other_options' => array('type_handle' => 'error'));
	  	  }
	
	  	  $text = str_replace("#room#", $room, ChFun_room_Changed);
	  	  $other_options = array('new_room' => $room);
	
	  	  return array('data_type' => 'change_room','text' => $text, 'type' => 'private', 'other_options' => $other_options);
  	  }
  	  return array('type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------
  	
	function command_clean($command_info)
  	{
	  $row = $command_info['row'];
	  $room = $command_info['room'];
	  $type = 'clean';
	  
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
	  $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	  
	  $user_name = $this->LTChatDataKeeper->get_user_name();
	  $id = $this->LTChatDataKeeper->get_user_id();
	  
	  $this->LTChatDataKeeper->truncate();
	  $cleard = $this->LTChatDataKeeper->back_from_clear($id);
	  if($cleard == false)
	  $filterd = $this->LTChatDataKeeper->back_from_filter($id);
	  
	  if($cleard == false && $filterd == false){
	  
	  return array('text' => '** Yor not have premmsion to use this command', 'type' => 'private','other_options' => array('type_handle' => 'error'));
	  
	  }else{

	  return array('data_type' => 'clear', 
				   'text' => '', 
				   'type' => 'private'
				   );
	  }
	  
  	  }
	  else
  	  return array('type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------
  	
	function command_clear($command_info)
  	{
	  $row = $command_info['row'];
	  $room = $command_info['room'];
	  $type = 'clear';
	  
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
	  $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	  
	  $user_name = trim($command_info['params'][1]);
      unset($command_info['params'][1]); 
	  $my_self = $this->LTChatDataKeeper->get_user_name(); //get my name from session
	  $level_color = $this->LTChatDataKeeper->get_user_color($this->LTChatDataKeeper->get_user_level());	  
	  
	  $user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user
	  
		if($user_info == NULL)
		{
			  $u_doesnt_exists = str_replace("#user#",$user_name, LTChatCore_user_doesnt_exists);
			  return array('text' => $u_doesnt_exists, 'type' => 'private','other_options' => array('type_handle' => 'error'));
			
		}else{
			
		//check for not null username
		if($user_name == NULL){
		return array('text' => ChFun_write_multikick_reason, 
					 'type' => 'private', 
					 'other_options' => array('type_handle' => 'error'));
		}else{
			
			$text = str_replace(array("#user#","#levelcolor#","#sender#"),
								array($user_name,$level_color,$my_self), ChFun_msg_clear);
			
			$this->LTChatDataKeeper->clear_user_message($user_info->nick, $my_self, $type);
			$this->LTChatDataKeeper->post_reason($text, $room);
			
		}##null user
	
			return array('data_type' => 'clear', 
						 'text' => '', 
						 'type' => 'private'
						 );
		}##null user info
  	  }
	  else
  	  return array('type' => 'skip');
  	}
	
  	//---------------------------------------------------------------------
  	
	function command_filter($command_info)
  	{
	  $row = $command_info['row'];
	  $room = $command_info['room'];
	  $type = 'filter';
	  
  	  if($row->user == $this->LTChatDataKeeper->get_user_name())
  	  {
	  $this->LTChatDataKeeper->delete_message_id($row->id, -1);
	  
	  $word = trim($command_info['params'][1]);
      unset($command_info['params'][1]); 
	  
	  $my_self = $this->LTChatDataKeeper->get_user_name(); //get my name from session
	  $level_color = $this->LTChatDataKeeper->get_user_color($this->LTChatDataKeeper->get_user_level());	  
	  
	  $user_info = $this->LTChatDataKeeper->get_user_by_nick($user_name); //get user

	  //check for not null username
	  if($word == NULL){
	  return array('text' => ChFun_write_multikick_reason, 
				   'type' => 'private', 
				   'other_options' => array('type_handle' => 'error'));
	  }else{
			
	  $text = str_replace(array("#word#","#levelcolor#","#sender#"),
						  array($word,$level_color,$my_self), ChFun_msg_filter);
	
	  $this->LTChatDataKeeper->filter_user_message($word, $my_self, $type);
	  $this->LTChatDataKeeper->post_reason($text, $room);
	
	  return array('data_type' => 'clear', 
				   'text' => '', 
				   'type' => 'private',
				   );
  	  }
	  }
	  else
  	  return array('type' => 'skip');
  	}

  	//---------------------------------------------------------------------

  	function command_url($command_info)
  	{
  	  $params = $command_info['params'];
  	  $param = implode(" ", $params);
      $param = trim($param);

      if(eregi("(http.?://)(.*)",$param, $r))
        $out .= str_replace(array("#link#","#title#","#text#"), array($r[0].urlencode($param), htmlspecialchars($param),$r[2]), ChFun_url_Style);
      else
        $out .= str_replace(array("#link#","#title#","#text#"), array("http://".urlencode($param),htmlspecialchars($param), $param), ChFun_url_Style);

      return array('text' => $out, 'type' => 'public');
  	}
  	//---------------------------------------------------------------------
  	function command_tpl_configrooms()
  	{
  	  return array('rooms' => $this->LTChatDataKeeper->get_all_rooms());
  	}
  	//---------------------------------------------------------------------

  	function command_ERROR($command_info)
  	{
	  $out['type'] = 'private';
	  $out['text'] = "Unknown error";
	
  	  if(is_array($command_info['params']))
  	  {
  	  	if($command_info['params'][1] == "ignore" && $command_info['params'][1] == ERROR_ignore_from)
  	  	  $out = array('type' => 'private', 'text' => ERROR_ignore_msg_from);

  	  	if($command_info['params'][1] == "ignore" && $command_info['params'][1] == ERROR_ignore_to)
  	  	  $out = array('type' => 'private', 'text' => ERROR_ignore_msg_to);
		  
  	  	if($command_info['params'][1] == "jail" && $command_info['params'][1] == 'jail')
  	  	  $out = array('type' => 'private', 'text' => ERROR_msg_jail);
  	  }

	  return $out;
  	}
	
	function groups_commands($command, $id){
        
		$my_command = substr($command, 1);
		$g_command = $this->LTChatDataKeeper->whois_have_commands($id);

		if($g_command !== false){
			$g_command_filtred = array_filter($g_command);	
			if(array_key_exists($my_command, $g_command_filtred)){
			   return TRUE;
			}
		}else{
		   return NULL;
		}
	}
	
	
  	//---------------------------------------------------------------------

  	function command($row, $room, $private_id)
  	{
  	  $row->nick = "Chat Core";
  	  $command_ar = explode(" ",$row->text);

      $_command = trim($command_ar[0]);
      unset($command_ar[0]);
	  $params = $command_ar;

	  $function_params = array('params' => $params, 'row' => $row, 'room' => $room, 'private_id' => $private_id);

	  if($_command == "/ERROR")
	  {
  	    $this->LTChatDataKeeper->delete_message_id($row->id, 1);
		return $this->command_ERROR($function_params);
	  }
	    
	  $out['type'] = 'private';
	  $out['text'] = str_replace("#command#", $_command, ChFunBadCommand);
	  $out['other_options']['type_handle'] = 'error';
	  $in_group = $this->groups_commands($_command, $this->LTChatDataKeeper->get_user_id());
	    
	  $help = $this->language_config['help'];
	  foreach ($help as $commands)
	    if(is_array($commands['commands']))
		  foreach ($commands['commands'] as $command)
		  {
		    if($command == $_command)
		    {			  
		      if($commands[$this->LTChatDataKeeper->get_user_level()] !== true && $in_group == NULL) 
		      {
			  	$out['type'] = 'private';
	  			$out['text'] = ChFunNoRights;
			  	$out['other_options']['type_handle'] = 'error';
		      }
			  elseif($this->LTChatDataKeeper->whois_have_action($this->LTChatDataKeeper->get_user_id(), 'disable') == 1 && $commands['in_disable_mode'] !== true)
			  {
			  	$out['type'] = 'private';
	  			$out['text'] = ChFunNoRights;
			  	$out['other_options']['type_handle'] = 'error';
			  }
			  elseif(is_callable(array(get_class($this),$commands['execute_function'])))
			  {
			  	$function_params['command_help'] = $commands;
			    $out = call_user_func(array($this,$commands['execute_function']),$function_params);
			  }
			  elseif(is_callable(array(get_class($this),$commands['execute_tpl_function'])))
			  {
			  	if($row->user == $this->LTChatDataKeeper->get_user_name() && $row->room == $room)
			  	{
			  	  $function_params['command_help'] = $commands;
			      $out = $this->load_tpl($function_params);
			  	}
			  	else 
			  	  $out['type'] = 'skip';
			  }
			  else
			  {
			  	$function_params['command_help'] = $commands;
	  			$out['text'] = ChFunBadFunction;
			  }

			  if(!isset($out['other_options']['type_handle']))
				$out['other_options']['type_handle'] = 'info';

			  return $out;
		    }
		  }

	  return $out;
  	}
  }
?>