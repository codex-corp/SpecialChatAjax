<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=#PageEncoding#">
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<title>#chat_title# -- #user_name#</title>

<link href="#LTChatTemplatePath#css/global.css" rel="stylesheet" type="text/css">
<link href="#css_link#" rel="stylesheet" type="text/css">

<SCRIPT language="JavaScript">
	var room = "#room#";
	var private_id = '#private_id#';
	var datatype = 'all_data';
	var refresh_after = '#refresh_after#';
	var ChatTemplatePath = '#LTChatTemplatePath#';
	var change_room_msg = '#LTChatRoomChangeMsg#';
	
</SCRIPT>

<script type="text/javascript" src="#LTChatTemplatePath#highslide/highslide-with-html.js"></script>

<script type="text/javascript">    
    hs.graphicsDir = '#LTChatTemplatePath#highslide/graphics/';
    hs.outlineType = 'rounded-white';
    hs.outlineWhileAnimating = true;
</script>

<script language="JavaScript" type="text/javascript" src="#LTChatTemplatePath#functions.js"></script>
<script language="JavaScript" type="text/javascript" src="#LTChatTemplatePath#communication.js"></script>
<script language="JavaScript" type="text/javascript" src="#LTChatTemplatePath#chat_commands_handle.js"></script>
<script language="JavaScript" type="text/javascript" src="#LTChatTemplatePath#chat_functions.js"></script>

<SCRIPT language="JavaScript">
//get the time and date
goforit();
</SCRIPT>

</head>
<body bgcolor="#FFFFFF">

<div id="sexyBG"></div><div id="sexyBOX" onMouseDown="document.onclick=function(){};" onMouseUp="setTimeout('sexyTOG()',1);"></div> 
<div id='loading' style='position:absolute;right:0px;top:0px;color:white;background-color:red; font-size:x-small; font-family:arial;font-weight:bold'></div>

<div id='user_list' style='position: absolute; margin-top:38px; visibility:hidden; width:15%; right:0; border:solid 1px #808080;'>
<table border='0' cellspacing='0' cellpadding='0' style='width:100%; height:100%;'><tr><td bgcolor='#71828A'><img alt='close this internal window' style='margin-left:10px;' src='./templates/standard/img/close.gif' onClick='hide_user_list(); return false' width='24' height='24' /></td></tr></table>
<div style="background-color:#FFFFFF;" id='user_list_data' name='user_list_data'></div>
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
  
  <table border="0" cellpadding="0" style="border-collapse: collapse" bordercolor="#111111" width="100%"  height="38">
      <tr>
         <td width="50%" bgcolor="#DFDFDF" height="26" style="padding-left:10px; text-align:left">
		 <div id='room'></div></td>          
         <td width="50%" bgcolor="#DFDFDF" height="26" style="padding-right:10px; text-align:right">
		 <div id="clock"></div></td>
	  </tr>
      
      <tr>
        <td width="100%" bgcolor="#999999" height="6" colspan="3"></td>
      </tr>
        
      <tr>
        <td width="100%" bgcolor="#666666" height="6" colspan="3"></td>
      </tr>
  </table>

  <tr>
    <td><div id='data' name='data'></div></td>
  </tr>
  
  <tr>
    <td align="left" id="do_error"></td>
  </tr>
  
  <tr>
    <td><div id='private' name='private' style="margin:0px; padding:0px;"></div></td>
  </tr>
  
  <tr><td>
<form name='myform' onSubmit='return false'>
<input size="100%" type='text' name='message' id='message' onKeyPress="if(this.value.length > 255) this.value = this.value.substring(0, 255);" onKeyDown="keydown(event.keyCode);" onKeyup="if(!isNS4){key_u(event.keyCode);} else { key_u(event.which);}">

<input TYPE="button" NAME="post" id='post' OnClick="key_u(13);" VALUE=" POST ">
<br>
	<select name="userlist" onChange="document.myform.message.value=form.userlist.options[form.userlist.selectedIndex].value;">
	<option value="">Room</option>
	<option value="/room Syria">Syria</option>
	</select>
	
	<select name="color" onChange="document.myform.message.value=form.color.options[form.color.selectedIndex].value;">
	<option value="">Mode</option>
	<option value="/away">Away</option>
	</select>
    
	<select id="list" name="list" onChange="document.myform.message.value=form.list.options[form.list.selectedIndex].value;">
	<option value="/list">List</option>
	</select>
    
	<img onClick="boldup();" id="boldpic" src="#LTChatTemplatePath#pix/editor_bold_off.gif" />
	<img onClick="underlup();" id="underlinepic" src="#LTChatTemplatePath#pix/underline_off.gif" />
	<img onClick="italicup();" id="italicpic" src="#LTChatTemplatePath#pix/italic_off.gif" />
	<br>
	<span class="style2">[</span> <span class="style1">Flash</span> <span class="style3">soon</span><span class="style2">|</span> <a href="javascript:void(0);" class="style1" onClick="javascript:popupusersetails('./messages.php', '600', '300');">Messenger</a> <span class="style3">New</span> <span class="style2">|</span><a href="javascript:void(0);" class="style1" onClick="javascript:popupusersetails('./emoticons/default.htm', '650', '500');"> Images </a> <span class="style2">|</span><a href="javascript:show_user_list()" class="style1"> Show user list </a><span class="style2">|</span> <span class="style1">Rules</span> <span class="style2">|</span> <span class="style1">Control Panel</span> <span class="style3">soon</span> <span class="style2">|</span> <span class="style1">ToolBar</span> <span class="style2">|</span> <a href="javascript:void(0);" class="style1" onClick="javascript:popupusersetails('./command_tpl.php?load_template=command_register.tpl&other_vars=N%3B', '550', '300');">Signup</a> <span class="style2">|</span> <a href="#" onclick="return logout()" class="style1">Logout</a> <span class="style2"> ]</span><br>

 <p align="center"><font face='Verdana' size='1'><font color="#666666">Copyright 2004-2008</font><font color='#5F7EA9'> Special Version 1.2 Beta</font> <font color='#666666'>&copy;, All Rights Reserved<br>
   Programming and Designing  by Hany Alsamman<br>
 </font></font></p>
 <tr><td>
 
</td></tr></table>
</body>
</html>