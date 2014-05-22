<HTML>
<HEAD>
<TITLE>SpecialChat Messenger!</TITLE>
<META http-equiv=Content-Type content="text/html; charset=UTF-8">
<SCRIPT>


function disableForm(theform) {

if (document.all || document.getElementById) {

for (i = 0; i < theform.length; i++) {

var tempobj = theform.elements[i];

if (tempobj.type.toLowerCase() == "submit" || tempobj.type.toLowerCase() == "reset")

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

<BODY text=#000000 vLink=#5493B4 link=#006699 bgColor=#525252><A name=top></A>
<TABLE cellSpacing=0 cellPadding=10 width="100%" align=center border=0>
  <TBODY>
  <TR>
    <TD>
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=1 bordercolor="#999999" style="border-collapse: collapse">
		<!-- MSTableType="nolayout" -->
		<TBODY>
        <TR>
          <TH class=thTop 
          style="BACKGROUND-IMAGE: url('#path#panel/bg.gif')" 
            width="75%" height="29"><font face="Verdana" color=#FFFFFF style="font-size: 9pt"><span lang="en-us">
			SpecialChat Messenger</span></font></TH></TR>
        <TR>
          <TD class=row1 vAlign=top><BR>
            <CENTER>
            <TABLE cellSpacing=1 cellPadding=0 width="95%" bgColor=#006699 
            border=0>
              <TBODY>
              <TR bgColor=white>
                <TD align=middle>
                  <TABLE cellSpacing=1 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR>
                      <TD class=catLeft width="41%" height=25 bgcolor="#C0C0C0"><SPAN 
                        class=genmedg>
                        <P align=left>
						<font face="Verdana" style="font-size: 9pt">IP: 
						($thechanger[1])</font></P></SPAN></TD>
                      <TD class=catLeft width="32%" bgcolor="#C0C0C0"><SPAN class=genmedg>
                        <P align=left>
						<font face="Verdana" style="font-size: 9pt">Date: 
						$thechanger[2]</font></P></SPAN></TD>
                      <TD class=catLeft width="27%" height=18 bgcolor="#C0C0C0"><SPAN 
                        class=genmedg>
                        <P align=left>
						<font face="Verdana" style="font-size: 9pt">Time: 
						#date#</font></P></SPAN></TD></TR>
                    <TR>
                      <TD class=row1 width="41%" height=25>
                        <P align=left>
						<FONT face="Verdana" style="font-size: 9pt">Sender: <FONT 
                        color=$levelcolor>#from#</FONT> <BR> </FONT>
						<FONT color=#525252 style="font-size: 8pt" face="Verdana">
						cmail 
                        <span lang="en-us">$counter</span> 
                        - (<font color=$levelcolor>$thechanger[4]</font>)</FONT><FONT size=2 face="Verdana" style="font-size: 8pt" color="#525252"> </FONT></P></TD>
                      <TD class=row1 colSpan=2>
                        <P align=left><font face="Verdana">
						<FONT style="font-size: 9pt">Subject: </FONT>
                        <span style="font-size: 9pt">#title#</span></font></P></TD></TR>
                    <TR>
                      <TD align=right colSpan=3>
                      <a href="$homepage/cgi-bin/cmail.cgi?action=forward&nickname=$fields{'nickname'}&ID=$fields{'ID'}&count=$fields{'count'}">
                      <IMG 
                        alt="Forward this cmail to operator" 
                        src="#path#panel/forward.gif" 
                        border=0></a> </TD></TR>
                    <TR>
                      <TD colSpan=3>
                        <P align=left>
						<font face="Verdana" style="font-size: 9pt">
						#message#</font></P></TD></TR></TBODY></TABLE><BR>
                  <a href="$homepage/cgi-bin/cmail.cgi?action=view_inbox&nickname=$fields{'nickname'}&ID=$fields{'ID'}">
                  <IMG 
                  src="#path#panel/long_inbox.gif" 
                  border=0></a>
                  <a href="./messages.php?load_template=delete.tpl&msgid=#id#">
                  <IMG 
                  src="#path#panel/long_delete.gif" 
                  border=0></a>
                  <a href="$homepage/cgi-bin/cmail.cgi?action=write_cmail&nickname=$fields{'nickname'}&ID=$fields{'ID'}&count=$fields{'count'}&nick_reply=$thechanger[0]">
                  <IMG 
                  src="#path#panel/long_reply.gif" 
                  border=0></a>
</TD></TR></TBODY></TABLE>
 </CENTER></TD></TR></TBODY></TABLE>
    <p align="center">

<a href="javascript:top.close()">Close this</a><BR></TD></TR></TBODY>
</TABLE>
</BODY>
</HTML>