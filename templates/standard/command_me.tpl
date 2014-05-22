<head>
<STYLE>
	* { text-decoration: none;}
    a { text-decoration: none;}

	H2 {
		FONT-SIZE: 16px; BACKGROUND: none transparent scroll repeat 0% 0%; COLOR: #666666; FONT-FAMILY: Arial, Verdana, Helvetica, sans-serif; TEXT-DECORATION: none
		}
	.stars
	{
	  cursor:pointer;
	  cursor:hand;
	}
	
	/* Used in some of the example templates below. */
	.tipClass { font: 12px Arial, Helvetica; color: black; font-weight:bold; }
	
	.table1 { font: 12px Arial; color: black; font-weight:bold; }
	.table2 { font: 12px Arial; color: blue;}
	
	/* Format links inside tips a little, feel free to remove this. */
	.tipClass A { text-decoration: none; color: black }

</STYLE>

<SCRIPT LANGUAGE="Javascript" SRC="#LTChatTemplatePath#ColorPicker.js"></SCRIPT>
<SCRIPT LANGUAGE="Javascript" SRC="#LTChatTemplatePath#tipster.js"></SCRIPT>

<script type="text/javascript"><!--
// Here's one illustrating a decimal tipStick value so it floats along behind the cursor.
var stickyTip = new TipObj('stickyTip');
with (stickyTip)
{
 template = '<table bgcolor="#FFFFFF" cellpadding="1" cellspacing="0" width="%6%" border="1">' +
  '<tr><td><table bgcolor="#cccccc" cellpadding="4" cellspacing="0" width="100%" border="0">' +
  '<tr><td align="center" class="tipClass">%3%</td></tr></table></td></tr></table>';

  tips.nickcolor = new Array(5, 5, 100, 'Select your nickname color from change link!');
  tips.color = new Array(5, 5, 100, 'Select your typing color from change link!');
  tips.font = new Array(5, 5, 100, 'Select your typing font from scrool change!');
  tips.nickfont = new Array(5, 5, 100, 'Select your typing nickname font from scrool change!');

 tipStick = 0.2;
}
//--></script>

<SCRIPT LANGUAGE="JavaScript">
var cp = new ColorPicker('window'); // Popup window
var cp2 = new ColorPicker(); // DIV style
</SCRIPT>

<script type="text/javascript">

function insert_font(font)
{
document.getElementById('font').value = ""+font+"";
}

function insert_nick_font(nickfont)
{
document.getElementById('nickfont').value = ""+nickfont+"";
}

function getposOffset(overlay, offsettype){
var totaloffset=(offsettype=="left")? overlay.offsetLeft : overlay.offsetTop;
var parentEl=overlay.offsetParent;
while (parentEl!=null){
totaloffset=(offsettype=="left")? totaloffset+parentEl.offsetLeft : totaloffset+parentEl.offsetTop;
parentEl=parentEl.offsetParent;
}
return totaloffset;
}

function overlay(curobj, subobjstr, opt_position){
if (document.getElementById){
var subobj=document.getElementById(subobjstr)
subobj.style.display=(subobj.style.display!="block")? "block" : "none"
var xpos=getposOffset(curobj, "left")+((typeof opt_position!="undefined" && opt_position.indexOf("right")!=-1)? -(subobj.offsetWidth-curobj.offsetWidth) : 0) 
var ypos=getposOffset(curobj, "top")+((typeof opt_position!="undefined" && opt_position.indexOf("bottom")!=-1)? curobj.offsetHeight : 0)
subobj.style.left=xpos+"px"
subobj.style.top=ypos+"px"
return false
}
else
return true
}

function overlayclose(subobj){
document.getElementById(subobj).style.display="none"
}

</script>

<title>#title#</title>
</head>
<body bgcolor="#71828A">

<div id="stickyTipLayer" style="position: absolute; z-index: 10000; visibility: hidden; left: 0px; top: 0px; width: 10px">&nbsp;</div>

<DIV id="fontlayer" style="position:absolute; display:none; border: 1px solid black; background-color: white; width: 120px; height: 171px; padding: 8px">
<table style="width:120px; height:171px;>
<tr><td><font face="Book Antiqua" color="#000000"><a onClick="javascript:insert_font('Book Antiqua')">#login#</a></font></td></tr>
<tr><td><font face="Georgia" color="#000000"><a onClick="javascript:insert_font('Georgia')">#login#</a></font></td></tr>
<tr><td><font face="Lucida Bright" color="#000000"><a onClick="javascript:insert_font('Lucida Bright')">#login#</a></font></td></tr>
<tr><td><font face="Comic Sans MS" color="#000000"><a onClick="javascript:insert_font('Comic Sans MS')">#login#</a></font></td>
</tr>
<tr><td><font face="Roman" color="#000000"><a onClick="javascript:insert_font('Roman')">#login#</a></font></td></tr>
<tr><td><font face="Terminal" color="#000000"><a onClick="javascript:insert_font('Terminal')">#login#</a></font></td></tr>
<tr><td><font face="Verdana" color="#000000"><a onClick="javascript:insert_font('Verdana')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Palatino Linotype" color="#000000"><a onClick="javascript:insert_font('Palatino Linotype')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Arial" color="#000000"><a onClick="javascript:insert_font('Arial')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Arial Black" color="#000000"><a onClick="javascript:insert_font('Arial Black')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Symbol" color="#000000"><a onClick="javascript:insert_font('Symbol')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Tahoma" color="#000000"><a onClick="javascript:insert_font('Tahoma')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Century Gothic" color="#000000"><a onClick="javascript:insert_font('Century Gothic')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Vrinda" color="#000000"><a onClick="javascript:insert_font('Vrinda')">#login#</a></font></td></tr>
</table>
<div align="right"><font face='arial' size='2'><a href="#" onClick="overlayclose('fontlayer'); return false">Close Box</a></font></div>
</div>

<DIV id="nickfontlayer" style="position:absolute; display:none; border: 1px solid black; background-color: white; width: 120px; height: 171px; padding: 8px">
<table style="width:120px; height:171px;>
<tr><td><font face="Book Antiqua" color="#000000"><a onClick="javascript:insert_nick_font('Book Antiqua')">#login#</a></font></td></tr>
<tr><td><font face="Georgia" color="#000000"><a onClick="javascript:insert_nick_font('Georgia')">#login#</a></font></td></tr>
<tr><td><font face="Lucida Bright" color="#000000"><a onClick="javascript:insert_nick_font('Lucida Bright')">#login#</a></font></td></tr>
<tr><td><font face="Comic Sans MS" color="#000000"><a onClick="javascript:insert_nick_font('Comic Sans MS')">#login#</a></font></td>
</tr>
<tr><td><font face="Roman" color="#000000"><a onClick="javascript:insert_nick_font('Roman')">#login#</a></font></td></tr>
<tr><td><font face="Terminal" color="#000000"><a onClick="javascript:insert_nick_font('Terminal')">#login#</a></font></td></tr>
<tr><td><font face="Verdana" color="#000000"><a onClick="javascript:insert_nick_font('Verdana')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Palatino Linotype" color="#000000"><a onClick="javascript:insert_nick_font('Palatino Linotype')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Arial" color="#000000"><a onClick="javascript:insert_nick_font('Arial')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Arial Black" color="#000000"><a onClick="javascript:insert_nick_font('Arial Black')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Symbol" color="#000000"><a onClick="javascript:insert_nick_font('Symbol')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Tahoma" color="#000000"><a onClick="javascript:insert_nick_font('Tahoma')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Century Gothic" color="#000000"><a onClick="javascript:insert_nick_font('Century Gothic')">#login#</a></font></td></tr>
<tr><td width="111"><font face="Vrinda" color="#000000"><a onClick="javascript:insert_nick_font('Vrinda')">#login#</a></font></td></tr>
</table>
<div align="right"><font face='arial' size='2'><a href="#" onClick="overlayclose('nickfontlayer'); return false">Close Box</a></font></div>
</div>



<table width="100%" height="100%">
<tr>
  <td align="center" valign="middle">
	<TABLE  cellspacing="0" cellpadding="0">
	<tr>
	<td>
	  <table width="100%" border="0" style='height:6px;' cellspacing="0"> 
	  <tr>
	    <td background="#LTChatTemplatePath#img/chat_box/lt.bmp" style='width:4px;'></td>
	    <td bgcolor="White" style=""></td>
	    <td background="#LTChatTemplatePath#img/chat_box/rt.bmp" style='width:5px;'></td>
	  </tr>
	  </table>
	</td>
	</tr>
	<tr>
	  <td bgcolor="White" style="padding-left:5px;padding-right:5px;border-right:1px #7A7B7B solid;">
		<span style="color:red">#error#</span>
		<form method="POST">
		<table width="100%">
		<tr>
		  <td align="center" valign="middle">
		    <table cellspacing=0 cellpadding=0 width=120 border=0 align='center'>
		      <tr valign=bottom>
		        <td background='#LTChatTemplatePath#/img/av_img/top.gif' height=25 align=center style='color:red;'>#login#</td>
		      </tr>
		      <tr valign=middle align=center> 	  
		        <td background='#LTChatTemplatePath#/img/av_img/bottom.gif' height=102><img border=0 src='#LTChatTemplatePath#img/nophoto.gif'></TD>
		      </TR>
		    </TABLE>
		  </td>
		  <td>
		    <table class="table1">
		    <tr>
		      <td valign="top">#registration_text#:</td>
		  	  <td>#registration_date#</td> 
		  	</tr>
		    <tr>
		      <td valign="top">#posted_msg_text#:</td>
		  	  <td>#posted_msg_value#</td> 
		  	</tr>
		    <tr>
		  	  <td valign="top">#last_seen_text#:</td> 
		      <td>#last_seen_value#</td>
		  	</tr>
		    <tr>
		      <td valign="top">#last_host_text#:</td>
		  	  <td>#last_host_value#</td> 
		  	</tr>
		    <tr>
		      <td valign="top">#last_ip_text#:</td>
		  	  <td>#last_ip_value#</td> 
		  	</tr>
		    <tr>
		      <td valign="top"><strong>Color Type:</strong></td>
		  	  <td>
              <input name="color" onMouseOver="stickyTip.show('color')" onMouseOut="stickyTip.hide()" value="#colorvalue#"><font face='arial' size='2'><A HREF="#" onClick="cp2.select(document.forms[0].color,'pick2');return false;" NAME="pick2" ID="pick2">Change</A></font>
	<SCRIPT LANGUAGE="JavaScript">cp.writeDiv()</SCRIPT></td> 
		  	</tr>
		    <tr>
		      <td valign="top"><strong>Nick Color:</strong></td>
		  	  <td><input name="nickcolor" onMouseOver="stickyTip.show('nickcolor')" onMouseOut="stickyTip.hide()" value="#nickcolorvalue#"><font face='arial' size='2'><A HREF="#" onClick="cp2.select(document.forms[0].nickcolor,'pick2');return false;" NAME="pick" ID="pick">Change</A></font>
	<SCRIPT LANGUAGE="JavaScript">cp.writeDiv()</SCRIPT></td> 
		  	</tr>
		    <tr>
		      <td valign="top"><strong>Font Type:</strong></td>
		  	  <td><input id="font" name="font" onMouseOver="stickyTip.show('font')" onMouseOut="stickyTip.hide()" value="#fontvalue#"><font face='arial' size='2'><a onClick="return overlay(this, 'fontlayer')">Show Content</a></font></td> 
		  	</tr>
		    <tr>
		      <td valign="top"><strong>Nick Font:</strong></td>
		  	  <td><input id="nickfont" name="nickfont" onMouseOver="stickyTip.show('nickfont')" onMouseOut="stickyTip.hide()" value="#nickfontvalue#"><font face='arial' size='2'><a onClick="return overlay(this, 'nickfontlayer')">Show Content</a></font></td> 
		  	</tr>
		    </table>
		  </td>
		</tr>
		#other_fields#
		#submit#
		</table>
		</form>
	  
	  </td>
	</tr>
	<tr>
	  <td>
	    <table width="100%" border="0" style='height:7px;' cellspacing="0"> 
	    <tr>
	      <td background="#LTChatTemplatePath#img/chat_box/lb.bmp"	style='width:5px;'></td>
	      <td bgcolor="White" style=""></td>
	      <td background="#LTChatTemplatePath#img/chat_box/rb.bmp" style='width:5px;'></td>
	    </tr>
	    </table>
	  </td>
	</tr>
	</TABLE>

</body>