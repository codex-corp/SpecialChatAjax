<?
  define("ChFun_new_login","<img border=0 src=./templates/standard/img/online.gif><font face='Palatino Linotype' color='AAAAAA' size='2px'>#login_right#: <font face='Palatino Linotype' color=#user_color#>#rights#</font> <a onclick='parent.message(\"/msg #user# \")' href=\"#\"><b><font color='AAAAAA'>#user#</font></b></a> (#ip#) - #room# / Sallam!</font>");
  
  define("ChFun_chat_exit","<img border=0 src=./templates/standard/img/offline.gif><font face='Palatino Linotype' color='AAAAAA' size='2px'>Signed off: <a onclick='parent.message(\"/msg #user# \")' href=\"#\"><b>#user#</b></a> (#ip#) - #room# . #quit_msg#</font>");
  
  define("ChFun_showops","<tr>
<td width=\"50\" align=\"center\" style=\"border-left:medium none #FFFFFF; border-right:2px solid #FFFFFF; border-top:medium none #FFFFFF; border-bottom:medium none #FFFFFF; padding-left:4; padding-right:4; padding-top:1; padding-bottom:1\" height=\"17\">
<font color=\"#color#\" face=\"Palatino Linotype\">#id#</font></td>
<td width=\"231\" align=\"center\" style=\"border-left:2px solid #FFFFFF; border-right:2px solid #FFFFFF; border-top:medium none #FFFFFF; border-bottom:medium none #FFFFFF; padding-left:4; padding-right:4; padding-top:1; padding-bottom:1\" height=\"17\">
<font color=\"#color#\" face=\"#nickfont#\">#Nick#</font></td>
<td width=\"231\" align=\"center\" style=\"border-left:2px solid #FFFFFF; border-right:2px solid #FFFFFF; border-top:medium none #FFFFFF; border-bottom:medium none #FFFFFF; padding-left:4; padding-right:4; padding-top:1; padding-bottom:1\" height=\"17\">
<font color=\"#color#\" face=\"Palatino Linotype\">#Status#</font></td>
<td width=\"232\" align=\"center\" style=\"border-left:2px solid #FFFFFF; border-right:2px solid #FFFFFF; border-top:medium none #FFFFFF; border-bottom:medium none #FFFFFF; padding-left:4; padding-right:4; padding-top:1; padding-bottom:1\" height=\"17\">
<font color=\"#color#\" face=\"Palatino Linotype\">#level#</font></td>
<td width=\"232\" align=\"center\" style=\"border-left:2px solid #FFFFFF; border-right:2px solid #FFFFFF; border-top:medium none #FFFFFF; border-bottom:medium none #FFFFFF; padding-left:4; padding-right:4; padding-top:1; padding-bottom:1\" height=\"17\">
<font color=\"#color#\" face=\"Palatino Linotype\">#Start#</font></td>
<td width=\"232\" align=\"center\" style=\"border-left:2px solid #FFFFFF; border-right:2px solid #FFFFFF; border-top:medium none #FFFFFF; border-bottom:medium none #FFFFFF; padding-left:4; padding-right:4; padding-top:1; padding-bottom:1\" height=\"17\">
<font color=\"#color#\" face=\"Palatino Linotype\">#lastlogin#</font></td>
</tr>");

// Styl emotikon
  define("LTChat_emotStyle","<img src='#path#'>");
  define("LTTpl_static_html_tpl","<center><span style='color:red'>#error#</span></center>");

// ----------------------- trace ------------------------
  //trace mask ip logs syntex
  define("ChFun_trace_logs","<img src='../templates/standard/img/online.gif' border='0'><font face='arial' color='#AAAAAA' size='2'>#rights# <strong>#user#</strong> (#maskip#) logged in <strong>#room#</strong>! at <strong>#time#</strong></font><br>");
  //trace real ip logs syntex
  define("ChFun_trace2_logs","<img src='../templates/standard/img/online.gif' border='0'><font face='arial' color='#AAAAAA' size='2'>#rights# <strong>#user#</strong> (#remote_addr#) logged in <strong>#room#</strong>! at <strong>#time#</strong></font><br>");//trace pc ip logs syntex
  define("ChFun_trace4_logs","<img src='../templates/standard/img/online.gif' border='0'><font face='arial' color='#AAAAAA' size='2'>#rights# <strong>#user#</strong> (#pcip#) logged in <strong>#room#</strong>! at <strong>#time#</strong></font><br>");

//------------------------ Flash -----------------------
  define("ChFun_Flash_Style",'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="400" height="17">
  <param name="movie" value="./templates/standard/img/flash/#flash#.swf" />
  <param name="quality" value="high" />
  <embed src="./templates/standard/img/flash/#flash#.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="400" height="17"></embed></object>');
  
  define("ERROR_flash_msg_empty","Flash msg Empty");
  define("ERROR_flash_msg_notfound","This flash name not found in flash list");
  
  define("UPGRADE","<FONT face=Arial color=#a4a4a4 size=2>~~«® The operator <b>#user#</b> is upgraded automatically by the system, congratulation ! ®»~~</FONT>"); 
  
  define("timerbar","<tr><td background=\"#LTChatTemplatePath#img/login/member/ann_bg.gif\">
<TABLE align=center cellSpacing=0 cellPadding=0 width=\"96%\" border=0>
  <TR>
    <TD vAlign=top background=\"#LTChatTemplatePath#img/login/member/bar_3.gif\">
                 <img border=\"0\" src=\"#LTChatTemplatePath#img/login/member/bar_1.gif\"><img border=\"0\" src=\"#LTChatTemplatePath#img/login/member/bar_2.gif\" alt=\"#percent#\" width=\"#percent#%\" height=\"11\"><br>
    <TD vAlign=top width=\"10\">
                 <img border=\"0\" src=\"#LTChatTemplatePath#img/login/member/bar_4.gif\"></TABLE>
							</td>
						</tr>
						<tr>
							<td background=\"#LTChatTemplatePath#img/login/member/ann_bg.gif\">
<p align=\"center\"><b><font face=\"Verdana\" size=\"1\">Time progress is #percent#% above is your progress for automatic upgrade</font></b></td></tr>");

  define("monthlybar","<tr><td background=\"#LTChatTemplatePath#img/login/member/ann_bg.gif\">
<TABLE align=center cellSpacing=0 cellPadding=0 width=\"96%\" border=0>
  <TR>
    <TD vAlign=top background=\"#LTChatTemplatePath#img/login/member/bar_3.gif\">
                 <img border=\"0\" src=\"#LTChatTemplatePath#img/login/member/bar_1.gif\"><img border=\"0\" src=\"#LTChatTemplatePath#img/login/member/bar_2.gif\" alt='#monthlypercent#%' width='#monthlypercent#%' height=\"11\"><br>
    <TD vAlign=top width=\"10\">
                 <img border=\"0\" src=\"#LTChatTemplatePath#img/login/member/bar_4.gif\"></TABLE>
							</td>
						</tr>");

  define("ChFun_stop_disabling_sus","<a href=\"./command_tpl.php?load_template=command_showsus.tpl&reason_id=#reason_id#&users_id=#users_id#\" title=\"stop disabling\" target=\"_self\">#link_title#</a>");
  
  define("ChFun_stop_disabling_disable","<a href=\"./command_tpl.php?load_template=command_showdisable.tpl&reason_id=#reason_id#&users_id=#users_id#\" title=\"stop disabling\" target=\"_self\">#link_title#</a>");
  
  define("ChFun_stop_disabling_ban","<a href=\"./command_tpl.php?load_template=command_showban.tpl&reason_id=#reason_id#&users_id=#users_id#\" title=\"stop disabling\" target=\"_self\">#link_title#</a>");
  
  define("ChFun_stop_disabling_banip","<a href=\"./command_tpl.php?load_template=command_showban.tpl&reason_id=#reason_id#&users_id=#users_id#\" title=\"stop disabling\" target=\"_self\">#link_title#</a>");
  
  define("not_in_timerbar","<tr><td align=\"center\" background=\"#LTChatTemplatePath#img/login/member/ann_bg.gif\">Sorry , Your rank is not includes in auto upgrade system!</td></tr>");
  
  define("not_in_monthlybar","<tr><td align=\"center\" background=\"#LTChatTemplatePath#img/login/member/ann_bg.gif\">Sorry , Your rank is not includes in monthly upgrade system!</td></tr>");

  define("ChFun_showsus","<tr>
                    <td width=\"4%\">#id#</td>
                    <td width=\"10%\">#sus_user#</td>
                    <td width=\"10%\">#sus_by#</td>
                    <td width=\"2%\">#level#</td>
                    <td width=\"36%\"><div align=\"center\">#reason#</div></td>
                    <td width=\"9%\">#date#</td>
                    <td width=\"9%\">#time#</td>
                    <td width=\"13%\">#stop_disabling#</td>
                  </tr>");
				  
  define("ChFun_showban","<tr>
                    <td width=\"4%\">#id#</td>
                    <td width=\"10%\">#nick/ip#</td>
                    <td width=\"10%\">#banned_By#</td>
                    <td width=\"2%\">#level#</td>
                    <td width=\"36%\"><div align=\"center\">#reason#</div></td>
                    <td width=\"9%\">#date#</td>
                    <td width=\"9%\">#time#</td>
                    <td width=\"13%\">#stop_disabling#</td>
                  </tr>");
				  
  define("ChFun_showdisable","<tr>
                    <td width=\"4%\">#id#</td>
                    <td width=\"10%\">#nick#</td>
                    <td width=\"10%\">#disabled_by#</td>
                    <td width=\"2%\">#level#</td>
                    <td width=\"36%\"><div align=\"center\">#reason#</div></td>
                    <td width=\"9%\">#date#</td>
                    <td width=\"9%\">#time#</td>
                    <td width=\"13%\">#stop_disabling#</td>
                  </tr>");	
				  
  define("ChFun_list","<tr>
                       <td>#id#</td>
		               <td><font color='#mycolor#'>#nickname#</font></td>
		               <td>#room#</td>
		               <td>#ip_address#</td>
		               <td>#comment#</td>
	                   </tr>"); 	  
########################## emoticons #######################

//------------------------ emoticons -----------------------
  define("ChFun_emoticons_Style","\"#info#\" <img src='#path#'><br>");
########################## emoticons #######################

//------------------------ help ----------------------------
// wyglad listy dostepnych komend z helpa
  define("ChFun_help_ListStyle","#commands#<Br>");
// wyglad jezeli uzytkownik wybierze odpowiednią pozycje z helpa
  define("ChFun_help_InfoStyle","<td valign='top' align='left' nowrap height='22' width='200' bgcolor='#FFFFFF'><span style='color:green;'>#commands#</span> #except_params_static# #params#</td><td valign='top' align='left' nowrap height='22' width='495' bgcolor='#FFFFFF'>#Description#</td>");
// jezeli pozycja z helpa ma wiecej opcji ktore moze robic kazda opcja ma swoj opis ktorego styl mozna wyswietlic
  define("ChFun_help_DescArStyle","<br><span style='color:green;'>#param#</span> => #Description#");
########################## help #############################

//------------------------ url ------------------------------
  define("ChFun_url_Style","<a href=\"#link#\" title=\"#title#\" target=\"_blank\">#text#</a>");
########################## url ##############################

//------------------------ configrooms ----------------------
  define("ChFun_friend_Show","#friend_from_text#:<br><i>#friend_from#</i><br><br>#friend_to_text#:<br><i>#friend_to#</i><br>");
  define("ChFun_friend_ShowSep",", ");
########################## configrooms ######################

//------------------------ configteam ----------------------
  define("ChFun_team_Show","** <font color='red'>#team_user#</font> <u>LEVEL</u> #team_user_level# In group (#team_group_name#): <font color='blue'><i>#team_group_title#</i></font><br>");
  define("ChFun_team_ShowSep",", ");
########################## configteam ######################

//------------------------ configrooms ----------------------
  define("ChFun_configrooms_OotGroup","<optgroup label=\"#label#\">#options#</optgroup>");
  define("ChFun_configrooms_Ootion","<option #selected# value='#value#'>#name# (#users_online#) #default#</option>");
########################## configrooms ######################

//------------------------ configreg ------------------------
//'integer', 'float', 'text', 'date', 'select', 'radio', 'textarea'
  define("ChFun_configreg_add_items", "<option value='#name#'>#name#</option>");
  define("ChFun_configreg_Fields_th", "<tr><th>#var_name#</th><th>#var_type#</th><th>#var_length#</th><th>#required#</th><th>#delete#</th></tr>");
  define("ChFun_configreg_Fields_td", "<tr><td align=center>#var_name#</td><td>#var_type#</td><td>#var_length#</td><td>#required#</td><td><a href='#del_link#'>#delete#</a></td></tr>");
########################## configreg ########################

//------------------------ me -------------------------------
  define("ChFun_me_submit","<tr><td colspan=\"2\" align=\"center\"><br><input type=\"submit\" value=\"#send#\"></td></tr>");
########################## me ###############################

//------------------------ ping -----------------------------
  define("ChFun_ping_Separator", "<br>");
########################## ping #############################

  // menu
  define("ChMenuItemActive", "<a href=\"#link#\">#text#</a>");
  define("ChMenuItemInactive", "#text#");
  define("ChMenuItemSeparator", " | ");  
  
//------------------------ config ---------------------------
  define("ChFun_config_FieldInt", "<tr><td><form method=post>#description#<br><input type = text value='#value#' name='#name#'> <input type=submit value='#submit#'></form></td></tr>");
  define("ChFun_config_FieldBoolean", "<tr><td><form method=post>#description#<br><select name='#name#'><option value='1'>true</option><option value='0'>false</option></select>(#value#)<input type=submit value='#submit#'></form></td></tr>");
  define("ChFun_config_category", "<table align=center><tr><td align=center><b>#name#</b></td></tr>#items#</table>");
########################## config ###########################

//------------------------ avatar ---------------------------
  define("ChFun_avatar_item_available", "<table cellspacing=0 cellpadding=0 width=120 border=0 align='center'><tr valign=bottom> 	  <td background='#LTChatTemplatePath#img/av_img/top.gif' height=25 align=center style='color:red;'>#user#</td>	</tr>	<tr valign=middle align=center> 	  <td background='#LTChatTemplatePath#img/av_img/bottom.gif' height=102> 	    <a href='#select_link#'><img border=0 src='#link#'></a>	  </TD>	</TR>	</TABLE>");
  define("ChFun_avatar_item_unavailable", "  <table cellspacing=0 cellpadding=0 width=120 border=0 align='center'>	<tr valign=bottom> 	  <td background='#LTChatTemplatePath#img/av_img/top.gif' height=25 align=center style='color:red;'>#user#</td>	</tr>	<tr valign=middle align=center> 	  <td background='#LTChatTemplatePath#img/av_img/bottom.gif' height=102> 	    <img style='opacity: .5;filter: alpha(opacity=10);-moz-opacity: .5;' border=0 src='#link#'>	  </TD>	</TR>	</TABLE>");
  
  define("ChFun_avatar_table", "<table align=center>#data#</table>");
  define("ChFun_avatar_table_td", "<td>#item#</td>");
  define("ChFun_avatar_table_tr","<tr>#rows#</tr>");

  define("ChFun_avatar_table_td_items",4);
  define("ChFun_avatar_table_tr_items",4);
  define("ChFun_avatar_pages_body","<table align=center><tr>#items#</tr></table>");
  define("ChFun_avatar_pages_unselected","<td><a href='#link#'>#nr#</a></td>");
  define("ChFun_avatar_pages_selected","<td><b>#nr#</b></td>");
########################## avatar ###########################

// login_form.tpl
	  define("ChLoginRooms","#seltext#<select size='1' name='room' id='room' class='width_150'>#rooms#</select>");
	  define("ChLoginGuest","
		  <script>
		    function guest_change()
		    {
		    	var userlogin = document.getElementById(\"userlogin\");
				var usersend = document.getElementById(\"usersend\");
				
				usersend.disabled = true;
				userlogin.disabled = true;
				
				alert('The user\'s login form forbbiden by admin (Hany alsamman)');
			}
			
		  </script>
		  ");
		  
	  define("ChLoginGuestTrue","<script> function guest_change(){}</script>");
		  
	  define("ChLoginError","
					<tr>
						<td colspan=\"2\">
						<table align=\"center\" width=\"722\" cellspacing=\"0\" cellpadding=\"0\">
							<tr>
								<td width=\"61\">
								<img border=\"0\" src=\"#LTChatTemplatePath#img/enterface/error_pic.gif\" width=\"61\" height=\"74\"></td>
								<td width=\"714\" background=\"#LTChatTemplatePath#img/enterface/error_bg.gif\">#info#</td>
								<td>
								<img border=\"0\" src=\"#LTChatTemplatePath#img/enterface/error_end.gif\" width=\"19\" height=\"74\"></td>
							</tr>
						</table>
						</td>
					</tr>
		  ");

  // pola widoczne przy rejestracji oraz przy wyswietlaniu informacji o uzytkowniku 
  define("ChFieldInteger", "<tr><td valign=\"top\">#required# #name#</td><td><input type=\"text\" name=\"#var_name#\" value=\"#value#\"></td></tr>");
  define("ChFieldFloat", "<tr><td valign=\"top\">#required# #name#</td><td><input type=\"text\" name=\"#var_name#\" value=\"#value#\"></td></tr>");
  define("ChFieldDate", "<tr><td valign=\"top\">#required# #name#</td><td><input type=\"text\" name=\"#var_name#\" value=\"#value#\"></td></tr>");
  define("ChFieldRadio", "<tr><td valign=\"top\">#required# #name#</td><td>#options#</td></td>");
  define("ChFieldRadioOption", "<input type=\"radio\" name=\"#var_name#\" value=\"#val#\" #select#> #text#  <br>");
  define("ChFielSelect","<tr><td valign=\"top\">#required# #name#</td><td><select name=\"#var_name#\">#options#</select></td></td>");
  define("ChFielSelectOption","<option value=\"#val#\" #select#>#text#</option>");
  define("ChFielText","<tr><td valign=\"top\">#required# #name#</td><td><input type=\"text\" name=\"#var_name#\" value=\"#value#\" style=\"width:100%\"></input></td></tr>");
  define("ChFielTextarea","<tr><td valign=\"top\">#required# #name#</td><td><textarea name=\"#var_name#\" style=\"width:100%;height:60px;\">#value#</textarea></td></tr>");

?>