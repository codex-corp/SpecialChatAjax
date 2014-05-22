//   function load_template(data)
//   {   	
//	  config_start('command_tpl.php?load_template='+data['load_template']+'&other_vars='+data['other_vars']);
//   }

	function load_template(data)
	{   			
	    var niu = _el('pasion');
	    var newdivu = document.createElement("divx");
	    niu.innerHTML = "";
		niu.style.visibility="visible";
	    newdivu.setAttribute("id", data['load_template']);
	
		var load_template = data['load_template'];
		var other_vars = data['other_vars'];
		getInternalPages("command_tpl.php", "?load_template="+load_template+"&other_vars="+other_vars+"");
	}
	
   function updatePage(url, responseXML, responseText, responseStatusText, responseStatus) {
         if ((responseStatus == 200) || (responseStatus == 304))
         {
			 document.getElementById('pasion').innerHTML = responseText;
		 }
   }
	
	function privateLoop(){	
		if (processingInProgress == true) 
		timeOutId = setTimeout ("privateLoop ()", waitForProcessing);
		clear_private();
		document.getElementById('loading').innerHTML = 'Filter private...'; 
		prv_msg_last_id = 0;
		return;
	}
	
	function privateRefresh(process){	
		if (process == true) 
		clear_private();
		prv_msg_last_id = 0;
		document.getElementById('loading').innerHTML = 'Refresh private...'; 
		return;
	}
   
   var request_interval;
   var do_xml_reading = false;

   var last_id = 0;
   var msg_last_id = 0;
   var user_status_last_id = 0;
   var prv_msg_last_id = 0;
   var waitForProcessing = 50000;
   var processingInProgress = false;
   var xhrTimeout = null;
   var timeOutId = null;

   function alertContents(url, responseXML, responseText, responseStatusText, responseStatus)
   {	

		 // if status == 200 display text file
         if ((responseStatus == 200) || (responseStatus == 304))
         {
			clearTimeout(xhrTimeout);
            var xmldoc = responseXML;
				if (xmldoc)
					{
						//status property of the XMLHTTPRequest object == OK
						document.getElementById('loading').innerHTML = '';
					}
			
            var root = xmldoc.getElementsByTagName('results').item(0);

            if(root != null)
	            for (var iNode = 0; iNode < root.childNodes.length; iNode++)
	            {
	               var node = root.childNodes.item(iNode);
	               for (i = 0; i < node.childNodes.length; i++)
	               {
	                  var sibl = node.childNodes.item(i);
	                  var len = parseInt(sibl.childNodes.length / 2);
			          var data = new Array();
			          var time = "";
	
	                  for (x = 0; x < sibl.childNodes.length; x++)
	                  {
	                     var sibl2 = sibl.childNodes.item(x);
	                     var sibl3;
	                     if (sibl2.childNodes.length > 0)
	                     {
	                        sibl3 = sibl2.childNodes.item(0);
	   						var node_name = sibl.childNodes.item(x).nodeName;
	
	   						if(node_name == 'time_stamp')		time = sibl3.data;
	                        if(node_name == 'id' && sibl3.data != "null")			last_id = sibl3.data;
	
	   						 var node_value = sibl3.data;
	   						 node_value = str_replace("#pale_close#", ">", node_value);
						     node_value = str_replace("#pale_open#", "<", node_value);
						     node_value = str_replace("#star#", "*", node_value);
						     node_value = str_replace("#and#", "&", node_value);
						     node_value = str_replace("#hash#", "#", node_value);

	   						 data[node_name] = node_value;
	                     }/* end sibl2 */
	                  }

  switch (data['datatype']) {

    case "template":
	  if(data['load_template'] != '')
	  {
		load_template(data);
		if(private_id < 0)
		  msg_last_id = data['id'];
		else
		  prv_msg_last_id = data['id'];
	  }
    break;

    case "clear":
		 clear_msg();
		 msg_last_id = 0;
		 data['user_name'] = 'Chat System';
		 data['text'] = '<font face=arial size=2>** Your Public Has Been Refresh By Chat System</font>';
		 message_received(data);
      break;
	  
    case "change_room":
		 last_id = 0;
		 msg_last_id = 0;
		 user_status_last_id = 0;
		 room = data['new_room'];
		 change_room();
		 message_received(data);
      break;
	 
    case "msg":
		msg_last_id = data['id'];
		message_received(data);
		check_session();
		//get_buzz(data);
      break;
	  
    case "prv_msg_send":
		msg_last_id = data['id'];
		private_message_send_info(data);
      break;

    case "shoutbox_msg":
		msg_last_id = data['id'];
		shoutbox_message_received(data);
      break;
	  
    case "private_msg":
		prv_msg_last_id = data['id'];
		private_message_received(data);
		clearTimeout (timeOutId);
		timeOutId = setTimeout ("privateLoop ()", waitForProcessing);
      break;
      
    case "refresh":
		prv_msg_last_id = data['id'];
		private_message_received(data);
		privateRefresh(1);
      break;  
	  
    case "collection":
		 collection(data);
		 //check clear status if == 1 clear the public and refresh
		 if(data['clear'] == 1 || data['filter'] == 1){
			  message('/clean');
			  key_u(13);
		 }
      break;
	  
    case "user_status":
		if(data['action'] == true)
		 user_status_last_id = data['id'];
		 user_status_received(data);
      break;

    case "functions":
		if(data['function'] == 'clear')
		  clear_msg();
		else if(data['function'] == 'clear_private')
		  clear_private();
		else if(data['function'] == 'show_whois')
		  show_whois(data);
		else if(data['function'] == 'timebar')
		  timebar(data);
		else if(data['function'] == 'changeip')
		  changeip(data);
		else if(data['function'] == 'logout')
		  do_logout();
      break;

    default:
      break;
        
       } /* switch (tagName) */	               
     }          
  }
      		do_xml_reading = false;
         }
         else
         {
		 xhrTimeout = setTimeout( function() { ajaxTimeout(url) } , 2000);
         }
      }

	function callBack(url){
	http_request_callback = new ajaxObject(url, alertContents);
	http_request_callback.update();  // Server is contacted here.
	http_request.abort();
	}

	function ajaxTimeout(url){
	   callBack(url);
	}

//-----------------------------------------------------------------------------

   var req_counter=0;
   function do_xml()
   {
   	  req_counter++;
   	  if(do_xml_reading == true) return;
   	  do_xml_reading = true;
   	  
   	  today = new Date();
   	  var stamp = today.getDay()+"+"+today.getHours()+"+"+today.getMinutes()+"+"+today.getSeconds()+"+"+today.getMilliseconds();  
   	  makeRequest('getdata.php', '?datatype='+datatype+'&prv_msg_last_id='+prv_msg_last_id+'&msg_last_id='+msg_last_id+'&user_status_last_id='+user_status_last_id+'&room='+room+'&today='+stamp+'&private_id='+private_id);
   }

   function LTStart()
   {
     clearInterval(request_interval);
	 request_interval = window.setInterval(do_xml,refresh_after);

     if(datatype == 'all_data')
       change_room();
   }
   window.onload=LTStart;
