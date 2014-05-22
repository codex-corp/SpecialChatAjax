var users_images = new Array();
var private_id_checked = new Object();
var from=new Date();
var timejail = null;

	function CheckAll()
	{
	var checkboxIndex = 0;
	var inputFields = document.getElementById("private").getElementsByTagName("input");
	 for (var inputIndex=0;inputIndex<inputFields.length;inputIndex++)
	   {
		if (inputFields[inputIndex].className.indexOf("cbStyled")) 
		{
			if (inputFields[inputIndex].getAttribute("type")!=null){
			var styleType=inputFields[inputIndex].getAttribute("type");
			}	
			 var stylename=inputFields[inputIndex].getAttribute("name");
			 if(styleType == "checkbox" && stylename != 'allbox'){
			   if (!inputFields[inputIndex].checked){
				  inputFields[inputIndex].checked = true;
			   }else{
					inputFields[inputIndex].checked = false;
			   }
			 }
			checkboxIndex++;
		}
	   }
	}
	
	function SmallSmileyes (s,Smile) {
		find = new RegExp('\\*(' + Smile + ')\\*',"ig");
		s = s.replace(find,"<img src='emoticons/smiles/" + Smile + ".gif'>");
		return s;
	}
	
	function smile(s, smiley, image) {
	var yp = './emoticons/';
		return replace_i_with_a(s, smiley, '<img alt="'+smiley+'" src="' + yp + 'smiles/' + image + '" />');
	}
	
	function MySmileyes (s) {	         
	         var i = 10;			 
	         while (i != 1086)  {
			     var find = new RegExp('\\*(' + i + ')\\*',"g");
			     s = s.replace(find, "<img src='emoticons/smiles/icon$1.gif'>");
				 i++;
	         }	         
			 return s;
	}
	
	function smileys(s) {
		s = MySmileyes(s);
		s = SmallSmileyes(s, 'ba');		
		s = SmallSmileyes(s, 'brb');
		s = SmallSmileyes(s, 'ws');
		s = SmallSmileyes(s, 'w');
		s = SmallSmileyes(s, 'sc');
		s = SmallSmileyes(s, 'sa');
		s = SmallSmileyes(s, 'ha1');
		s = SmallSmileyes(s, 'gf');
		s = SmallSmileyes(s, 'dc');
		s = SmallSmileyes(s, 'b');
		s = SmallSmileyes(s, 'c');
		s = SmallSmileyes(s, 'r');
		s = SmallSmileyes(s, 'o');
		s = SmallSmileyes(s, 'l');
		s = SmallSmileyes(s, 'z');
		s = SmallSmileyes(s, 'y');
		s = SmallSmileyes(s, 't');
		s = SmallSmileyes(s, 's');
		s = SmallSmileyes(s, 'd');
		s = SmallSmileyes(s, 'g');
		s = SmallSmileyes(s, 'h');
		return s;
	}
	
	function bbcode(s) {
		s = replace_i_with_a(s, '[i]', '<i>');
		s = replace_i_with_a(s, '[/i]', '</i>');
		s = replace_i_with_a(s, '[I]', '<i>');
		s = replace_i_with_a(s, '[/I]', '</i>');

		s = replace_i_with_a(s, '[b]', '<b>');
		s = replace_i_with_a(s, '[/b]', '</b>');
		s = replace_i_with_a(s, '[B]', '<b>');
		s = replace_i_with_a(s, '[/B]', '</b>');

		s = replace_i_with_a(s, '[u]', '<u>');
		s = replace_i_with_a(s, '[/u]', '</u>');
		s = replace_i_with_a(s, '[U]', '<u>');
		s = replace_i_with_a(s, '[/U]', '</u>');
		
		s = replace_i_with_a(s, '-red', '<span style="color:#FF0000">');
		s = replace_i_with_a(s, '/red', '</span>');

		return s;
	}
	
   function remove_element(element)
   {
	 var el = document.getElementById(element);
	 el.parentNode.removeChild(el);
   }

   function message_received(data)
   {
     var out = "";
	 var error = "";
		
	    error = document.createElement("error");
		error.setAttribute("id", "error");
		document.getElementById("do_error").appendChild(error);
		
     if(data['is_command'] == "true")
     {
		if(data['type_handle'] == 'error')

     	  error = "<hr><TABLE cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"center\"><a style=\"text-decoration: none\" onclick='javascript:parent.close_error()'><img alt=\"Close this error\" src='"+data['LTChatTemplatePath']+"img/close_error.gif'></a></td><td align=\"left\"><span style=\"font-family:arial; font-size:10pt; color:red;\">"+data['text']+"</span></td></tr></TABLE>";
     	else
     	  error = "<hr><TABLE align=\"left\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"center\"><a style=\"text-decoration: none\" onclick='javascript:parent.close_error()'><img alt=\"Close this error\" src='"+data['LTChatTemplatePath']+"img/close_error.gif'></a></td><td align=\"left\"><span style=\"font-family:arial; font-size:10pt;\">"+data['text']+"</span></td></tr></TABLE>";

	 document.getElementById("error").innerHTML = error;
     
	 }//end if is_command
     else
	 
     if(data['user_name'] == 'Chat System'){
     out = "<TABLE cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td width=100%> "+data['text']+"</td></tr></TABLE>";
     
	 document.getElementById("data").innerHTML += out;
	 
	 }else{
		 
	 if(data['text'] != null){
		 data['text'] = smileys(data['text']);
		 data['text'] = bbcode(data['text']); 
		 data['text'] = links(data['text']); 
	 }
		 
	var Test = document.getElementById("data").getElementsByTagName("table");
	if(Test.length > 19){
	var Arr  = ObjToArray(Test);
	Arr.reverse();
	var NewArr = new Array();
	var NewMsg = '';
	for (var i =0;i < 19 ; i++ ) {
		 NewArr.push(Arr[i]);
	}
	NewArr.reverse();
	NewMsg = NewArr.join("\n");
	NewMsg = NewMsg.replace(/<tbody>/ig,"<table cellpadding='0' cellspacing='0' border='0' width='100%'>");
	NewMsg = NewMsg.replace(/<\/tbody>/ig,"</table>");
	document.getElementById("data").innerHTML = NewMsg;
	}
	
	if(data['type_handle'] !== "undefined" && data['type_handle'] == 'd_message'){
	out = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td><span style=\"font-family: "+data['nickfont']+"; color:"+data['nickcolor']+"; font-weight:bold; font-size:11pt\"><a style=\"text-decoration: none\" onclick='parent.message(\"/msg " + data['user_name'] + " \")'>* "+data['user_name']+"</a></span><span style=\"visibility:hidden; font-size:xx-small;\">i</span><span style=\"font-family: "+data['font']+"; color:"+data['color']+"\">"+data['text']+"</span></td></tr></table>";
	
	}else{

	out = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td><span style=\"font-family: "+data['nickfont']+"; color:"+data['nickcolor']+"; font-weight:bold; font-size:11pt\"><a style=\"text-decoration: none\" onclick='parent.message(\"/msg " + data['user_name'] + " \")'>["+data['user_name']+"]</a></span><span style=\"visibility:hidden; font-size:xx-small;\">i</span><span style=\"font-family: "+data['font']+"; color:"+data['color']+"\">"+data['text']+"</span></td></tr></table>";
	}
     document.getElementById("data").innerHTML += out;

	 }//end if chat system
   }//end function
   
   
   //-----------------------------------------------------------------------------------
   
   function logout()
   {

	body_self = document.getElementsByTagName('html');
	body_self[0].style.filter = "progid:DXImageTransform.Microsoft.BasicImage(grayscale=1)";
	if (confirm('Are you sure that you want to logout ?'))
	{
		parent.message('/quit');
		key_u(13);
	}
	else
	{
		body_self[0].style.filter = '';
		return false;
	}
     
   }
   
   function do_logout()
   {
	    window.location.href = "logout.php?back="+escape(location.href);
   }
   
   function check_session()
   {
	  var session = 'NO_SESSION_LIFE';
	  var get_session = (readCookie("session_life") != null) ? readCookie("session_life") : session;
	  return get_session;
   }
   
   function create_session()
   {
	  if(check_session() == 'NO_SESSION_LIFE'){
		createCookie("session_life",'han44y',30);
	  }
   }
   
   function show_user_list()
   {
	var user_list = document.getElementById('user_list');
	user_list.style.visibility = "visible";
   }
   
   function hide_user_list()
   {
	var user_list = document.getElementById('user_list');
	user_list.style.visibility = "hidden";
   }
   
   /**
	  wyczyszczenie wszystkich wiadomosci w chacie
   */
   function clear_msg()
   {
     document.getElementById("data").innerHTML = '';
   }
   
   function clear_private()
   {
     document.getElementById("private").innerHTML = '';
   }
   
   /**
    write me nick name with /msg in the message.value
   */

   function message(msg)
   {
	 document.getElementById('message').value = ""+msg+"";
   }
   
   /**
    close the error msg
   */

   function close_error()
   {
	 remove_element('error');
   }
   
   /**
   the flood function control
   */
   
   function flood(data){
	  
	  var flood_msg = '';
	   if(data['flood'] == 1){
		  flood_msg = '<hr><font face=arial size=2>You can only send one message every 3 seconds!</font>';
    document.getElementById("error").innerHTML = flood_msg; 
	 
	   return false;
	   }
    }
   
   /**
    @param url(string) adres gdzie jest config do strony
   */
   function config_start(url)
   {
     window.open(url,"","width=385,height=275,scrollbars=0,resizable=0");
   }
   //-----------------------------------------------------------------------------------

   /**
     command_private_msg
   */
   function private_message_send_info(data)
   {
	if(data['text'] == "undefined")  data['text'] = " ";
	
	if(data['text'] != null){
	 data['text'] = smileys(data['text']);
	 data['text'] = bbcode(data['text']); 
	 data['text'] = links(data['text']); 
	}
	
		var out = "<hr><TABLE cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"100%\" ><tr><td align=\"center\"><a style=\"text-decoration: none\" onclick='javascript:parent.close_error()'><img alt=\"Close this private message\" src='"+data['LTChatTemplatePath']+"img/close_error.gif'></a></td><td><font color='blue' face='Arial' size='2'>>> "+data['status']+" :[<span style='font-family:"+data['nickfont']+"; color:"+data['nickcolor']+";'><b>"+data['nick']+"</b></span>]:<a href=\""+data['link']+"\" onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\"><img  border='0' valign=middle src=\""+ChatTemplatePath+"img/prv_msg.bmp\"></a> <span style='font-family: Tahoma; font-size: 12px; color:"+data['color']+";'>"+data['text']+"</span></font></td></tr></TABLE>";
		
		document.getElementById("error").innerHTML = out;
	}
	
	
	function private_message_received(data)
	{   
	
		if(data['text'] != null){
		 data['text'] = smileys(data['text']);
		 data['text'] = bbcode(data['text']); 
		 data['text'] = links(data['text']); 
		}
		
		var out = '';
		var checkall = '';
		var sum_private = document.getElementById("private").getElementsByTagName("table");
		
		
		if(sum_private.length <= 0 && data['level'] > 0){
		 
		checkall = '<hr><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width="729" ><tr bgcolor=\"#F3F3F3\"><td align=\"center\" width="28"><font face="Palatino Linotype" color=CC0000><img border="0" src="'+ChatTemplatePath+'img/boxlogo.gif" alt="Tick the box to select all messages"></font></td><td align=center width="20"><INPUT TYPE="CHECKBOX" NAME="allbox" VALUE="pbox" onClick="CheckAll()"></td><td align=\"left\" width="667"><p align=left><font face="Verdana" color="#666666" size="1"><b>- To delete msgs tick the box(s) and then type /del - or - to forward msgs tick the box(s) and then type d [nickname]</b></font><font color="#EA742A" face="Verdana" size="1"></b></span></font><font face=Verdana size=1 color=\"#660099\"> </font></p></td></tr></table>';
		}
		
		if(sum_private.length <= '0' && data['level'] == '0'){
			
		checkall = '<hr><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width="729" ><tr bgcolor=\"#F3F3F3\"><td align=\"center\" width="28"><font face="Palatino Linotype" color=CC0000><img border="0" src="'+ChatTemplatePath+'img/boxlogo.gif" alt="Tick the box to select all messages"></font></td><td align=\"left\" width="667"><p align=left><font face="Verdana" color="#666666" size="1"><b>- To delete msgs tick the box(s) and then type /del - or - to forward msgs tick the box(s) and then type d [nickname]</b></font><font color="#EA742A" face="Verdana" size="1"></b></span></font><font face=Verdana size=1 color=\"#660099\"> </font></p></td></tr></table>';
		}

		if(data['level'] <= '0' && data['user_id'] !== '50'){
		
			out = "<TABLE style='font-size:11pt' cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"100%\" ><tr><td><span class='cbStyled'>&nbsp;</span><a onclick='parent.message(\"/msg " + data['user_name'] + " \")'><span style='font-family:"+data['nickfont']+"; color:"+data['nickcolor']+";'><strong>"+data['user_name']+" (# "+data['rights']+" #):</strong></span></a> <a href=\"./private.php?private_id="+data['user_id']+"\" onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\"><img valign=middle src=\""+ChatTemplatePath+"img/prv_msg.bmp\" border=0></a><span style='font-family:"+data['font']+"; color:"+data['color']+";'> "+data['text']+"</span><a onclick='parent.message(\"/del "+data['id']+" \")'><font color='red'> del</font></a></td></tr></TABLE>";
		
		}else if(data['level'] > '0' && data['user_id'] !== '50'){
						 
			out = "<TABLE style='font-size:11pt' cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"100%\" ><tr><td><span class='cbStyled'><input type='checkbox' id='msg' name='msg[]' value='"+data['id']+"'></span><a onclick='parent.message(\"/msg " + data['user_name'] + " \")'><span style='font-family:"+data['nickfont']+"; color:"+data['nickcolor']+";'><strong>"+data['user_name']+" (# "+data['rights']+" #):</strong></span></a> <a href=\"./private.php?private_id="+data['user_id']+"\" onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\"><img valign=middle src=\""+ChatTemplatePath+"img/prv_msg.bmp\" border=0></a><span style='font-family:"+data['font']+"; color:"+data['color']+";'> "+data['text']+"</span><a onclick='parent.message(\"/del \")'><font color='red'> del</font></a></td></tr></TABLE>";
		
		
		}else if(data['user_id'] == '50'){
		
			out = "<TABLE cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td width=100%><span class='cbStyled'><input type='checkbox' id='msg' name='msg[]' value='"+data['id']+"'></span>"+data['text']+"<a onclick='parent.message(\"/del \")'><font color='red'> del</font></a></td></tr></TABLE>";
			
		}
		
       document.getElementById("private").innerHTML += checkall+out;
    }
   

   //-----------------------------------------------------------------------------------


   function change_room()
   {
   	 document.getElementById("user_list_data").innerHTML = "";
   	 var msg = str_replace("#room_name#",room,change_room_msg)
     document.getElementById("room").innerHTML = "<TABLE cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td>"+msg+"</td></tr></TABLE>";
   }
   
   //-----------------------------------------------------------------------------------
   
   function show_whois(data)
   {

   	 msg = "<table class=\"whois\" style=\"border-collapse: collapse\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"1\" bordercolor=\"#c0c0c0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td bgcolor=\"orange\" align=\"center\" colspan=\"7\">Information For <b>"+data['nickname']+"</b></span></td></tr><tr><td>Nickname</td><td>IP</td><td>Room</td><td>Online Time</td><td>public idle</td><td>private idle</td><td>Status</td></tr><tr><td><a onclick='parent.message(\"/msg " + data['nickname'] + " \")'>"+data['nickname']+"</a></td><td>"+data['maskip']+"</td><td>"+data['room']+"</td><td>"+data['onlinetime']+"</td><td>"+data['public_idle']+"</td><td>"+data['private_idle']+"</td><td>"+data['status']+"</td></tr></table>";
	 
     document.getElementById("error").innerHTML = ""+msg+"";
   }
   
   //-----------------------------------------------------------------------------------
   
   function changeip(data)
   {
	   
	 if(data['showchangeip'] == 1){
	 msg = "<table dir='rtl' cellSpacing='0' cellPadding='0' width='96%' border=0><tr><td align=\"center\">Status 3</td><td align=\"center\">Status 2</td><td align=\"center\">Status 1</td><td align=\"center\">Nickname</td></tr><tr><td align=\"center\">"+data['status_3']+"</td><td align=\"center\">"+data['status_2']+"</td><td align=\"center\">"+data['status_1']+"</td><td align=\"center\">"+data['nickname']+"</td></tr></table>";
	 }else{
   	 msg = "<tr><td align='center'><span style='font-family: arial; font-size:12px;'>Sorry , Your rank is not includes in auto upgrade system!</span></td></tr>";
	 }
	 
     document.getElementById("error").innerHTML = ""+msg+"";
   }
   
   //-----------------------------------------------------------------------------------
   
   function timebar(data)
   {
	   
	 if(data['showtimebar'] == 1){
	 msg = "<span style='padding-right: 5px; float:right'><a style=\"text-decoration: none\" onclick='javascript:parent.close_error()'><img src='"+data['LTChatTemplatePath']+"img/close_error.gif'></a></span><TABLE align=center cellSpacing='0' cellPadding='0' width='96%' border=0><TR><TD vAlign='top' background='"+data['LTChatTemplatePath']+"img/login/member/bar_3.gif'><img border='0' src='"+data['LTChatTemplatePath']+"img/login/member/bar_1.gif'><img border='0' src='"+data['LTChatTemplatePath']+"img/login/member/bar_2.gif' alt='#percent#' width='"+data['percent']+"%' height='11'><br><TD vAlign=top width='10'><img border='0' src='"+data['LTChatTemplatePath']+"img/login/member/bar_4.gif'></TABLE></td></tr><tr><td background='"+data['LTChatTemplatePath']+"img/login/member/ann_bg.gif'><p align='center'><b><font face='Verdana' size='1'>Time progress is "+data['percent']+"% above is your progress for automatic upgrade</font></b></td></tr></table>";
	 }else{
   	 msg = "<tr><td align='center'><span style='font-family: arial; font-size:12px;'>Sorry , Your rank is not includes in auto upgrade system!</span></td></tr>";
	 }
	 
     document.getElementById("error").innerHTML = ""+msg+"";
   }
   
   
   //-----------------------------------------------------------------------------------
   
   /**
    check for user on database if have kicked and do action
   */

   function kicked_check(data)
   {
   	 if(data['kick'] == 1)
     {
     var message = document.getElementById("message");
	 message.value = '******';
	 message.disabled = true;
	 var msg = "<font face='arial' size='2'><strong>You have kicked from chat</strong><br>"+data['reason']+"!</font>";
	 sexyBOX(msg,'300');
	 alert('Bye Bye');
	 window.location.href = "logout.php?back="+escape(location.href);	 
     }
   }
   
   function multi_kicked_check(data)
   {
   	 if(data['mkick'] == 1)
     {
     var message = document.getElementById("message");
	 message.value = '******';
	 message.disabled = true;
	 var msg = "<font face='arial' size='2'><strong>You have multi kick from chat</strong><br>"+data['reason']+"!</font>";
	 sexyBOX(msg,'300');
	 alert('Bye Bye');
	 window.location.href = "logout.php?back="+escape(location.href);	 	 
     }
   }
   
   function sus_check(data)
   {
   	 if(data['sus'] == 1)
     {
     var message = document.getElementById("message");
	 message.value = '******';
	 message.disabled = true;
	 var msg = "<font face='arial' size='2'><strong>You have suspaned from chat</strong><br>"+data['reason']+"!</font>";
	 sexyBOX(msg,'300');
	 alert('Bye Bye');
	 window.location.href = "logout.php?back="+escape(location.href);	 
     }
   }
   
   function banuser_check(data)
   {
   	 if(data['banuser'] == 1)
     {
     var message = document.getElementById("message");
	 message.value = '******';
	 message.disabled = true;
	 var msg = "<font face='arial' size='2'><strong>You have banned from chat</strong><br>"+data['reason']+"!</font>";
	 sexyBOX(msg,'300');
	 alert('Bye Bye');
	 window.location.href = "logout.php?back="+escape(location.href);	 	 
     }
   }
   //-----------------------------------------------------------------------------------
   
   /**
    check for user on database if have kicked and do action
   */

   function jail_check(data)
   {
   	if(data['jail'] == 1)
     {
	 	  var msg = document.getElementById("message");
	      var post = document.getElementById("post");
		  
		  post.disabled = true;
		  msg.disabled = true;
     }
   }   
   
   function collection(data)
   {
   jail_check(data); //jail check
   kicked_check(data); //kick check
   sus_check(data); //sus check
   multi_kicked_check(data); //multi kick check
   banuser_check(data); //ban user check
   flood(data); //flood check
   //create_session();
   }

   //-----------------------------------------------------------------------------------

   /**
    Funkcja ustawiajaca zmiany statusow w informacje

    @param data(array) Informacje o uzytkowniku
   */
   function user_status_received(data)
   {
	 var id_user = "uinfo"+data['users_id'];
	 var list_id_user = "list_"+data['users_id'];
     var out = "";

     if(data['online'] == 1 && data['my_id'] != data['users_id'])
       out = "<a title="+data['rights']+" href=\"./private.php?private_id="+data['users_id']+"\" onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\"><img src='"+ChatTemplatePath+"img/online.gif' border=0><span style='font-family:"+data['nickfont']+"; color:"+data['users_color']+";'>"+ data['nick']+"</font></a><BR>";
     else
       out = "<img src='"+ChatTemplatePath+"img/online.gif' border=0><span style='font-family:"+data['nickfont']+"; color:"+data['users_color']+";'>"+ data['nick']+"</font><BR>";

       
			var selectBox = document.getElementById('list');
			var option = document.createElement('option');
			option.text = data['nick'];
			option.id = list_id_user;
			option.value = "\/msg "+data['nick']+"";
			

     if(private_id >=0)
       return;

     if(data['online'] == 1 || data['friend'] == true)
     {
     	if(document.getElementById(id_user) == null)
     	  document.getElementById("user_list_data").innerHTML += "<div id='"+id_user+"'></div>";

     	document.getElementById(id_user).innerHTML = out;
     	document.getElementById(id_user).style.display = 'block';
		
			try
			{
			selectBox.add(option, null); // standards compliant
			}
			catch(ex)
			{
			selectBox.add(option); // IE only
			}
			if(document.getElementById(list_id_user).value != option.value)
		    selectBox.insertBefore(selectBox,option); 
			
     }
     else
     {  
     	
		if(document.getElementById(id_user) != null){
     	  document.getElementById(id_user).style.display = 'none';
		  remove_element(list_id_user);
		}
	
     }
   }
   
   function get_checkbox()
   {
   	    var checkboxIndex = 0;
		//var id = '';
		var NewId = new Array();
	   	var inputFields = document.getElementById("private").getElementsByTagName("input");
   		 for (var inputIndex=0;inputIndex<inputFields.length;inputIndex++)
		   {
		   	if (inputFields[inputIndex].className.indexOf("cbStyled")) 
			{
                if (inputFields[inputIndex].getAttribute("type")!=null){
				var styleType=inputFields[inputIndex].getAttribute("type");}	
				 if(styleType == "checkbox"){
				   if (inputFields[inputIndex].checked){
				    //id += ""+inputFields[inputIndex].value+",";
					NewId.push(inputFields[inputIndex].value);
				   }
				 }								   
				checkboxIndex++;
			}
		   }
		   return NewId;
   }
   //-----------------------------------------------------------------------------------   
   var isNS4 = (navigator.appName=="Netscape")?1:0;
   var checked = '';
   var msg = '';
   function key_u(code)
   {
     if(code == 13)
     {
	   msg = document.getElementById("message").value;
	   checked = get_checkbox();
		 if(checke_bbcode())
			  send_info(checked, msg);
			  var post = document.getElementById("post");
			  post.value = ' WAIT ';
			  post.disabled=true;
       document.getElementById('message').value = '';
	   document.myform.message.focus();
	   history_lines.push(msg);
	   history_now=-1
     }
   }
   
   function key_u_priv(code)
   {
     if(code == 13)
     {
	   msg = document.getElementById("message").value;

	   if(checke_prvi_msg(msg) == true){
       send_info_priv(msg);
			  var post = document.getElementById("post");
			  post.value = 'wait';
			  post.disabled=true;
	   }
       document.getElementById('message').value = '';
     }
   }
