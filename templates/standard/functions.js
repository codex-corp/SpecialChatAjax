/*

  (c) 2006-2007 Hany alsamman. All Rights Reserved

*/

var history_lines=new Array();
var history_now=-1;

var $newbold=null;
var $newitalic=null;
var $newunline=null;
var $blodupB=0;
var $undeleup=0;
var $undeleup=0;
var $italicup=0;

	function popupusersetails(URLStr,width, height) {
	var dynamwin=0;
	  if(dynamwin){
		if(!dynamwin.closed) dynamwin.close();
	  }
	var dynamwin = window.open(URLStr, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width='+width+',height='+height+'');
}

	function _el(t){var i = document.getElementById(t);return i;}
	
	function $(v) { return(document.getElementById(v)); }
	function $S(v) { return($(v).style); }
	function agent(v) { return(Math.max(navigator.userAgent.toLowerCase().indexOf(v),0)); }
	function isset(v) { return((typeof(v)=='undefined' || v.length==0)?false:true); }
	function XYwin(v) { var z=agent('msie')?Array(document.body.clientHeight,document.body.clientWidth):Array(window.innerHeight,window.innerWidth); return(isset(v)?z[v]:z); }
	
	function sexyTOG() { document.onclick=function(){ 
	$S('sexyBG').display='none'; 
	$S('sexyBOX').display='none'; 
	document.onclick=function(){}; }; }
	function sexyBOX(v,b) { setTimeout("sexyTOG()",100); $S('sexyBG').height=XYwin(0)+'px'; 
	$S('sexyBG').display='block'; 
	$('sexyBOX').innerHTML=v+'<div class="sexyX">click outside box to close)'+"<\/div>"; 
	$S('sexyBOX').left=Math.round((XYwin(1)-b)/2)+'px'; 
	$S('sexyBOX').width=b+'px'; $S('sexyBOX').display='block'; 
	}
	
	function boldup(){
	if($blodupB==1){
	var oTix = _el("message");oTix.style.fontWeight="";$newbold=null;
	var oBix = _el("boldpic");oBix.setAttribute("src",""+ChatTemplatePath+"pix/editor_bold_off.gif");$blodupB=0; 
	}else{
	var oTix = _el("message");oTix.style.fontWeight="bold";$newbold=1;
	var oBix = _el("boldpic");oBix.setAttribute("src",""+ChatTemplatePath+"pix/editor_bold_on.gif");$blodupB=1;}
	}
	
	function underlup(){if($undeleup==1){
	var oTix = _el("message");oTix.style.textDecoration="";$newunline=null;
	var oBix = _el("underlinepic");oBix.setAttribute("src",""+ChatTemplatePath+"pix/underline_off.gif");$undeleup=0;
	}else{
	var oTix = _el("message");oTix.style.textDecoration="underline";$newunline=1;
	var oBix = _el("underlinepic");oBix.setAttribute("src",""+ChatTemplatePath+"pix/underline_on.gif");$undeleup=1;}}
	
	function italicup(){
	if($italicup==1)
	{
	var oTix = _el("message");oTix.style.fontStyle="";$newitalic=null;
	var oBix = _el("italicpic");oBix.setAttribute("src",""+ChatTemplatePath+"pix/italic_off.gif");$italicup=0;
	
	}else{
	var oTix = _el("message");oTix.style.fontStyle="italic";$newitalic=1;
	var oBix = _el("italicpic");oBix.setAttribute("src",""+ChatTemplatePath+"pix/italic_on.gif");$italicup=1;}}
	
	function hidebox(){
	var crossobj= document.getElementById("pasion");
	crossobj.style.visibility = "hidden";
	}
	
	function replace_i_with_a (s, find, replace) {
		s.toLowerCase();
		return s.split(find).join(replace);
	}
	
   function checke_bbcode()
   {
	 var msg = document.getElementById('message').value;
	 var error = "Please dont use <strong>Bold</strong> OR <em>italic</em> OR <u>Underline</u> with command";
	 
	 var slash = /^\//;
	 if(msg.match(slash) && ( $blodupB == 1 || $undeleup == 1 || $italicup == 1 ) ){
		 sexyBOX(error,'300');
		 return false;
	 }
	 return true;
   }
   
   function checke_prvi_msg(msg)
   {
	 var msg = document.getElementById('message').value;
	 var error = "Please dont use any command from this window";
	 
	 var slash = /^\//;
	 if(msg.match(slash)){
		 alert(error);
		 return false;
	 }
	 return true;
   }
   
	function links(s) {
		return s.replace(/((https|http|ftp):\/\/[\S]+)/gi, '<a  href="$1" target="_blank">$1</a>');
	}

	function click(e) {
	
	var message="Dont use right click, focus on the smiles (only!)";
	
		if (document.all) {
			if (event.button == 2) {
			alert(message);
			return false;
			}
		}
		
		if (document.layers) {
			if (e.which == 3) {
			alert(message);
			return false;
			}
		}
	}

	function ObjToArray (Obj) {
			 var a = new Array();
			 for (var i = 0; i < Obj.length; i++) {
				  a[i] = Obj[i].innerHTML;
			 }
		return a;
	}

	function createCookie(name,value,days)
	{
		if (days)
		{
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	}
	
	function readCookie(name)
	{
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++)
		{
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}
	
	function eraseCookie(name)
	{
		createCookie(name,"",-1);
	}

	var out_print_r = "";
	function print_r(theObj)
	{
	  if(theObj.constructor == Array || theObj.constructor == Object)
	  {
		out_print_r += "<ul>";
		for(var p in theObj)
		{
		  if(theObj[p].constructor == Array || theObj[p].constructor == Object)
		  {
			out_print_r += "<li>["+p+"] => "+typeof(theObj)+"</li>";
			out_print_r += "<ul>";
			print_r(theObj[p]);
			out_print_r += "</ul>";
		  }
		  else
		  {
			out_print_r += "<li>["+p+"] => "+theObj[p]+"</li>";
		  }
		}
		out_print_r += "</ul>";
	  }
	}
	
	function str_replace(str_find, str_replace, str_normal)
	{
	  var int_case_insensitive = true;
	  if (arguments.length<3 || str_find=="" || str_normal=="" || typeof("".split)!="function")
	    return(str_normal);
	
	  //no parm means default, "case SENSITIVE"...
	  if(!(int_case_insensitive))
	  return(str_normal.split(str_find)).join(str_replace);
	
	  str_find=str_find.toLowerCase();
	
	  var rv=""; 
	  var ix=str_normal.toLowerCase().indexOf(str_find);
	  while(ix>-1)
	  {
	    rv+=str_normal.substring(0,ix)+str_replace;
	    str_normal=str_normal.substring(ix+str_find.length);
	    ix=str_normal.toLowerCase().indexOf(str_find);
	  };
	  return(rv+str_normal);
	}
	
	var dayarray=new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday")
	var montharray=new Array("January","February","March","April","May","June","July","August","September","October","November","December")
	
	function getthedate(){
	var mydate=new Date()
	var year=mydate.getYear()
	if (year < 1000)year+=1900
	var day=mydate.getDay()
	var month=mydate.getMonth()
	var daym=mydate.getDate()
	if (daym<10)daym="0"+daym
	var hours=mydate.getHours()
	var minutes=mydate.getMinutes()
	var seconds=mydate.getSeconds()
	var dn="AM"
	if (hours>=12)
	dn="PM"
	if (hours>12){
	hours=hours-12
	}
	if (hours==0)
	hours=12
	if (minutes<=9)
	minutes="0"+minutes
	if (seconds<=9)
	seconds="0"+seconds
	//change font size here
	var cdate="<font face='Trebuchet MS' size='2' color='#147DB8'>Time</font><font face='Trebuchet MS' color='#D20A04' size='2'>:</font><font face='Trebuchet MS' color='#808080' size='2'></font><font face='Trebuchet MS' size='2' color='#D20A04'>"+hours+":"+minutes+":"+seconds+" "+dn+"</font><font face='Trebuchet MS' color='#808080' size='2'></font>, <font face='Trebuchet MS' size='2' color='#147DB8'>Date</font><font face='Trebuchet MS' color='#D20A04' size='2'>:</font><font face='Trebuchet MS' color='#808080' size='2'></font><font face='Trebuchet MS' size='2' color='#D20A04'> "+dayarray[day]+", "+montharray[month]+" "+daym+", "+year+"</font>"
	if (document.all)
	document.all.clock.innerHTML=cdate
	else if (document.getElementById)
	document.getElementById("clock").innerHTML=cdate
	else
	document.write(cdate)
	}
	if (!document.all&&!document.getElementById)
	getthedate()
	function goforit(){
	if (document.all||document.getElementById)
	setInterval("getthedate()",1000)
	}
	
	function keydown(key) {
		if (history_now != -1 && key == 40) {
			if (history_now >= history_lines.length-1) document.getElementById('message').value='';
			else { history_now++; document.getElementById('message').value=history_lines[history_now]; }
		}
		else if (key == 38) {
			if (history_now == -1) {history_now=history_lines.length-1; document.getElementById('message').value=history_lines[history_now];}
			else if (history_now > 0) { history_now--; document.getElementById('message').value=history_lines[history_now]; }
		}
	}