<HTML>
<HEAD>
<meta http-equiv="content-type" content="text/html; charset=#PageEncoding#">
<LINK REL="STYLESHEET" HREF="#css_link#" TYPE="text/css">
<style>
* {font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 12px; }
</style>
<script type='text/javascript'>
function formValidator(){
	// Make quick references to our fields
	var username = document.getElementById('create');
	var email = document.getElementById('email');
	var password = document.getElementById('password');
	
	// Check each input in the order that it appears in the form!
					if(lengthRestriction(username, 1, 12)){
					if(lengthRestriction(password, 5, 12)){
						if(emailValidator(email, "Please enter a valid email address")){
							return true;
						}
					  }	
					}
	
	return false;	
}

function lengthRestriction(elem, min, max){
	var uInput = elem.value;
	if(uInput.length >= min && uInput.length <= max){
		return true;
	}else{
		alert("Please enter between " +min+ " and " +max+ " characters");
		elem.focus();
		return false;
	}
}

function emailValidator(elem, helperMsg){
	var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
	if(elem.value.match(emailExp)){
		return true;
	}else{
		alert(helperMsg);
		//elem.focus();
		return false;
	}
}

function isEmpty(elem, helperMsg){
	if(elem.value.length == 0){
		alert(helperMsg);
		elem.focus();
		return true;
	}
	return false;
}
</script>
</HEAD>

<body bgcolor="#71828A">
  
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
		    <h1 align="center">#title#</h1>
			<FORM method="POST" onSubmit="return formValidator()">
			<span style="color:red;">#info#</span>
			<TABLE align="center">
			  <tr>
			    <td><b>#required# #login#</b></td>
			    <td><INPUT type="text" value="#post_login#" name="create"></td>
			  </tr>
			  <tr>
			    <td>#required# #pass#</td>
			    <td><INPUT type="password" value="" name="password"></td>
			  </tr>
			  <tr>
			    <td>#required# Level Select</td>
			    <td>
				#level_select#
				</td>
			  </tr>
			  <tr>
			    <td>#required# Email</td>
			    <td>
                <INPUT name="email" value="" type="text">
				</td>
			  </tr>
			  <tr>
              <input name="apply" type="hidden" value="1">
			   <td colspan="2" align="center"><INPUT type="submit" value="#submit#" ><br><br>#required_desc#</td>
			  </tr>
			</TABLE>
			</FORM>
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
<br>	
  </td>
</tr>
</table>

</body>
</html>
