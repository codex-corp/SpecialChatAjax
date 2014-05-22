/*

  author: (c) 2006-2007 Hany alsamman. All Rights Reserved

*/

// initialize XMLHttpRequest object
var send_request = false;
var http_request = false;
var InternalPage = false;



	function GetXmlHttpObject() {
		if (typeof XMLHttpRequest != "undefined")
					return new XMLHttpRequest();
				var xhrVersion = ["MSXML2.XMLHttp.6.0","MSXML2.XMLHttp.5.0", "MSXML2.XMLHttp.4.0","MSXML2.XMLHttp.3.0","MSXML2.XMLHttp","Microsoft.XMLHttp" ];
				for (var i = 0; i < xhrVersion.length; i++) {
					try {
						var xhrObj = new ActiveXObject(xhrVersion[i]);
						return xhrObj;
					} catch (e) { }
				}
				return null;
	}
	

//   function getInternalPages(param) {
//	 mypages = new GetXmlHttpObject();
//     var url = param;
//     mypages.open("GET", url, true);
//     mypages.onreadystatechange = updatePage;
//     mypages.send(null);
//   }

	function ajaxObject(url, callbackFunction) {
	  var that=this;      
	  this.updating = false;
	  this.abort = function() {
		if (that.updating) {
		  that.updating=false;
		  that.AJAX.abort();
		  that.AJAX=null;
		}
	  }
	  this.update = function(passData,postMethod) { 
		if (that.updating) { return false; }                                                      
		  that.AJAX=new GetXmlHttpObject();                                             
		if (that.AJAX==null) {                             
		  return false;                               
		} else {
		  that.AJAX.onreadystatechange = function() { 
			if (that.AJAX.readyState == 2) {             
		     // HTML for the Loading MSG. AND status property of the XMLHTTPRequest object == Loaded
		     document.getElementById('loading').innerHTML = 'Message sent...';   			 
			}
			if (that.AJAX.readyState==4) {
			  that.updating=false; 
			  that.callback(url, that.AJAX.responseXML, that.AJAX.responseText, that.AJAX.statusText, that.AJAX.status);        
			  that.AJAX=null;                                         
			}                                                   
		  }                                                        
		  that.updating = new Date();                              
		  if (/post/i.test(postMethod)) {
			var uri=urlCall+'?'+that.updating.getTime();
			that.AJAX.open("POST", uri, true);
			that.AJAX.setRequestHeader("Connection","close");
			that.AJAX.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			that.AJAX.setRequestHeader("Content-Length", passData.length);
			that.AJAX.send(passData);
		  } else {
			var uri=urlCall+'?'+passData+'&amp;timestamp='+(that.updating.getTime()); 
			that.AJAX.open("GET", uri, true);                             
			that.AJAX.send(null);                                         
		  }              
		  return true;                                             
		}                                                                           
	  }
	  var urlCall = url;        
	  this.callback = callbackFunction || function () { };
	}

	/* httpReq object */
	function send_info(private_id_checked, message, login)
	{	  
	  // open socket connection
	  send_request = new ajaxObject('senddata.php');
	
	  if(login == undefined)  login = '';
	  // replace and filter message || login
	  message = str_replace("&","#and#",message);
	  login = str_replace("&","#and#",login);
	  
//	  message = encodeURIComponent(message);
//	  login = encodeURIComponent(login);

	  // send request
	  if($newbold){var sbold="[b]";var ebold="[/b]";}else{sbold='';ebold='';}
		
	  if($newitalic){var sitalic="[i]";var eitalic="[/i]";}else{sitalic='';eitalic='';}
		
	  if($newunline){var sunline="[u]";var eunline="[/u]";}else{sunline='';eunline='';}
	
	  message = ""+sbold+""+sitalic+""+sunline+""+message+""+ebold+""+eitalic+""+eunline+"";
	
	  send_request.update('login='+login+'&datatype='+datatype+'&private_id='+private_id+'&private_id_checked='+private_id_checked+'&room='+room+'&msg='+message, 'POST');
	}
	
	/* httpReq object */
	function send_info_priv(message, login)
	{	  
	  // open socket connection
	  send_request = new ajaxObject('senddata.php');
	
	  if(login == undefined)  login = '';
	  // replace and filter message || login
	  message = str_replace("&","#and#",message);
	  login = str_replace("&","#and#",login);
	  
	  // send request
	  if($newbold){var sbold="<b>";var ebold="</b>";}else{sbold='';ebold='';}
		
	  if($newitalic){var sitalic="<i>";var eitalic="</i>";}else{sitalic='';eitalic='';}
		
	  if($newunline){var sunline="<u>";var eunline="</u>";}else{sunline='';eunline='';}
	
	  message = ""+sbold+""+sitalic+""+sunline+""+message+""+ebold+""+eitalic+""+eunline+"";
	
	  send_request.update('login='+login+'&datatype='+datatype+'&private_id='+private_id+'&room='+room+'&msg='+message, 'POST');
	}
  
	/* Object for AJAX chat communication */
	function makeRequest(url, parameters)
	{
	  http_request = new ajaxObject(url + parameters, alertContents);
	  http_request.update(parameters);  // Server is contacted here.
	}
	
	/* Object for AJAX chat communication */
	function getInternalPages(url, parameters)
	{
	  InternalPage = new ajaxObject(url + parameters, updatePage);
	  InternalPage.update(false);
	}