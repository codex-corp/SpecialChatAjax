<HTML>
<HEAD>
<TITLE>SpecialChat Messenger!</TITLE>
<META http-equiv=Content-Type content="text/html; charset=UTF-8">

<BODY text=#000000 vLink=#5493B4 link=#006699 bgColor=#525252>
<TABLE class=forumline height=230 cellSpacing=1 cellPadding=2 width="100%" border=0>
  <TBODY>
  <TR>
    <TD class=row1 align=middle height=234>

      <SCRIPT language=JavaScript>
<!--
function CheckAll()
  {
  for (var i=0;i<document.check.elements.length;i++)
    {
    var e = document.check.elements[i];
    if (e.name != 'allbox')
      e.checked = document.check.allbox.checked;
    }
  }
//-->
</SCRIPT>
<style>
<!--
TD {
	FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif
}
TD {
	FONT-SIZE: 11px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif
}
.TDBorder6 {
	BACKGROUND-IMAGE: url('#path#cellpic4.gif'); BORDER-BOTTOM: 1px solid #00caca; HEIGHT: 28px
}
P {
	FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif
}
-->
</style>

<SCRIPT>


function disableForm(theform) {

if (document.all || document.getElementById) {

for (i = 0; i < theform.length; i++) {

var tempobj = theform.elements[i];

if (tempobj.type.toLowerCase() == "delete" || tempobj.type.toLowerCase() == "reset")

tempobj.disabled = true;

}
}
}

  function checkCR(evt) {

    var evt  = (evt) ? evt : ((event) ? event : null);

    var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);

    if ((evt.keyCode == 13) && (node.type=="text")) {return false;}

  }

  document.onkeypress = checkCR;


  </SCRIPT>

</HEAD>

</A>
<TABLE cellSpacing=0 cellPadding=10 width="100%" align=center border=0>
  <TBODY>
  <TR>
    <TD><BR>
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=1 bordercolor="#C0C0C0"><TBODY>
        <TR>
          <TH class=thTop 
          style="BACKGROUND-IMAGE: url('#path#panel/bg.gif')" 
            width="75%" height="30"><span lang="en-us">
			<font size="2" color="#FFFFFF">SpecialChat Messenger</font></span></TH></TR>
        <TR>
          <TD class=row1 vAlign=top>
<SCRIPT language=JavaScript>
<!--
function CheckAll()
  {
  for (var i=0;i<document.check.elements.length;i++)
    {
    var e = document.check.elements[i];
    if (e.name != 'allbox')
      e.checked = document.check.allbox.checked;
    }
  }
//-->
</SCRIPT>
<FORM name='check' onSubmit="return disableForm(this);" method='post' action="messages.php?load_template=delete_selected.tpl"><BR>
            <CENTER>
            <TABLE cellSpacing=1 cellPadding=0 width="95%" bgColor=#C0C0C0 
            border=0 style="border-collapse: collapse">
              <TBODY>
              <TR bgColor=white>
                <TD align=middle>
                  <TABLE cellSpacing=1 cellPadding=2 width="100%" border=0>
                    <TBODY>
                    <TR>
                      <TD class=catLeft colSpan=5 height=30 bgcolor="#C0C0C0"><SPAN 
                        class=genmedg>
                        <CENTER>Check Your Cmail</CENTER></SPAN></TD></TR>
                    <TR>
                      <TD></TD>
                      <TD><INPUT onclick=CheckAll() type=checkbox value="Check All" name=allbox></TD>
                      <TD>
                        <P align=left><FONT size=2>Subject</FONT></P></TD>
                      <TD><FONT size=2>Sent by</FONT></TD>
                      <TD width="20%"><FONT size=2>Date</FONT></TD></TR>
                      
                      #pm_header##messages_zero#

</TBODY></TABLE><FONT size=1><FONT color=red>$new</FONT> new msg(s)<BR>$allcmail 
					total msg(s) in your inbox</FONT><BR><BR>
                  
<INPUT style="FONT-SIZE: 10px" onClick="this.value='Delete'" type='submit' name='delete' value='Delete'> 
<INPUT style="FONT-SIZE: 10px" type=reset value='Reset' name='Reset'>
<BR><BR></TD></TR></TBODY></TABLE></CENTER></FORM>
<tr>
          <td bgcolor="#CCCCCC">
          <center>
            <a href="./messages.php?load_template=write.tpl">
            <img border="0" src="#path#panel/write.gif"></a></center><br></td></tr>
</TD></TR></TBODY></TABLE>
    <p align="center">

<a href="javascript:top.close()"><font size="2" color="#FFFFFF">
<span style="text-decoration: none">Close this</span></font></a><font size="2" color="#FFFFFF">
</font>
<BR></TD></TR></TBODY></TABLE>
</BODY>
</HTML>