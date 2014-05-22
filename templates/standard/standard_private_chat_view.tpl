<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<title>#title#</title>
<LINK REL="STYLESHEET" HREF="#css_link#" TYPE="text/css">
<SCRIPT language="JavaScript" type="text/javascript" >
   var room = "#room#";
   var private_id = '#private_id#';
   var datatype = 'private';
   var refresh_after = '#refresh_after#';
   var ChatTemplatePath = '#LTChatTemplatePath#';   
</SCRIPT>
<script language="JavaScript" type="text/javascript" src="#LTChatTemplatePath#functions.js"></script>
<script language="JavaScript" type="text/javascript" src="#LTChatTemplatePath#communication.js"></script>
<script language="JavaScript" type="text/javascript" src="#LTChatTemplatePath#chat_commands_handle.js"></script>
<script language="JavaScript" type="text/javascript" src="#LTChatTemplatePath#chat_functions.js"></script>

<LINK REL="STYLESHEET" HREF="#LTChatTemplatePath#style/default.css" TYPE="text/css">
</head>

<table>
<tr>
<td>
<div id='loading' style='position:absolute;right:0px;top:0px;color:white;background-color:red;font-size:9px;font-family:verdana;font-weight:bold'></div>
<body bgcolor="#F2F2F2" style="margin:0px; padding:0px; width:100%; height:100%;">

<div class="pmshowdiv" style="overflow: auto;">
<div id='buzz' name='buzz'></div>
<div id='data' name='data' style="width:100%; height:100%;"></div>
<a id="bottom" name="bottom"></a>
<div id="do_error"></div>
 </div>
 <br>
	<img onClick="boldup();" id="boldpic" src="#LTChatTemplatePath#pix/editor_bold_off.gif" />
	<img onClick="underlup();" id="underlinepic" src="#LTChatTemplatePath#pix/underline_off.gif" />
	<img onClick="italicup();" id="italicpic" src="#LTChatTemplatePath#pix/italic_off.gif" />
<textarea rows="3" name='message' id='message' onKeyPress="if(this.value.length > 255) this.value = this.value.substring(0, 255);" onKeyup="if(!isNS4){  key_u_priv(event.keyCode);  } else { key_u_priv(event.which);}" cols="53" class="TBOX" style="width: 296px; height: 40px;"></textarea>

<INPUT TYPE="button" NAME="post" id='post' onKeyDown="keydown(event.keyCode);" OnClick="key_u_priv(13);" VALUE="Post">
</td>
</tr>
</table>


</body>