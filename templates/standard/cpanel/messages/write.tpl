<HTML>
<HEAD>
<TITLE>SpecialChat Messenger!</TITLE>
<META http-equiv=Content-Type content="text/html; charset=UTF-8">
<SCRIPT language=JavaScript type=text/javascript>

function filterlist(selectobj) {

  this.selectobj = selectobj;
  this.flags = 'i';
  this.match_text = true;
  this.match_value = false;
  this.show_debug = false;

  this.init = function() {
    if (!this.selectobj) return this.debug('selectobj not defined');
    if (!this.selectobj.options) return this.debug('selectobj.options not defined');

    this.optionscopy = new Array();
    if (this.selectobj && this.selectobj.options) {
      for (var i=0; i < this.selectobj.options.length; i++) {

        // Create a new Option
        this.optionscopy[i] = new Option();

        // Set the text for the Option
        this.optionscopy[i].text = selectobj.options[i].text;

        if (selectobj.options[i].value) {
          this.optionscopy[i].value = selectobj.options[i].value;
        } else {
          this.optionscopy[i].value = selectobj.options[i].text;
        }

      }
    }
  }

  //--------------------------------------------------
  this.reset = function() {
    this.set('');
  }
  //--------------------------------------------------
  this.set = function(pattern) {
    var loop=0, index=0, regexp, e;

    if (!this.selectobj) return this.debug('selectobj not defined');
    if (!this.selectobj.options) return this.debug('selectobj.options not defined');

    // Clear the select list so nothing is displayed
    this.selectobj.options.length = 0;

    try {
      regexp = new RegExp(pattern, this.flags);
    } catch(e) {
      if (typeof this.hook == 'function') {
        this.hook();
      }

      return;
    }

    for (loop=0; loop < this.optionscopy.length; loop++) {

      // This is the option that we're currently testing
      var option = this.optionscopy[loop];

      // Check if we have a match
      if ((this.match_text && regexp.test(option.text)) ||
          (this.match_value && regexp.test(option.value))) {
        this.selectobj.options[index++] =
          new Option(option.text, option.value, false);

      }
    }
    if (typeof this.hook == 'function') {
      this.hook();
    }

  }


  //--------------------------------------------------
  this.set_ignore_case = function(value) {

    if (value) {
      this.flags = 'i';
    } else {
      this.flags = '';
    }
  }


  //--------------------------------------------------
  this.debug = function(msg) {
    if (this.show_debug) {
      alert('FilterList: ' + msg);
    }
  }


  //==================================================
  // Initialize the object
  //==================================================
  this.init();

}

// Startup variables
var imageTag = false;
var theSelection = false;

// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav  = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));

var is_win   = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac    = (clientPC.indexOf("mac")!=-1);

// Replacement for arrayname.length property
function getarraysize(thearray) {
        for (i = 0; i < thearray.length; i++) {
                if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
                        return i;
                }
        return thearray.length;
}

// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray,value) {
        thearray[ getarraysize(thearray) ] = value;
}

// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray) {
        thearraysize = getarraysize(thearray);
        retval = thearray[thearraysize - 1];
        delete thearray[thearraysize - 1];
        return retval;
}

function disableForm(theform) {

if (document.all || document.getElementById) {

for (i = 0; i < theform.length; i++) {

var tempobj = theform.elements[i];

if (tempobj.type.toLowerCase() == "send" || tempobj.type.toLowerCase() == "reset")

tempobj.disabled = true;

}
}

}

</SCRIPT>


<script language="javascript" type="text/javascript" src="./templates/standard/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
<!-- tinyMCE -->

	tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		cleanup_callback : "Cleanup" ,
		plugins : "advhr,emotions,insertdatetime,preview,zoom,directionality,searchreplace",
		theme_advanced_buttons1_add : "fontselect,fontsizeselect",
		theme_advanced_buttons2_add : "emotions,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor",
        theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
	    plugi2n_insertdate_dateFormat : "%Y-%m-%d",
	    plugi2n_insertdate_timeFormat : "%H:%M:%S"
	});
	
function Cleanup(type, value) {
	if (type == "get_from_editor" || type == "insert_to_editor") {
		// Do some custom cleanup to the HTML with regexps		
		value = value.replace(/<(a|meta|script)([^>]*)>/g, '');
		return value;
	}

	return value;
}

</script>
<!-- /TinyMCE -->
  </HEAD>
<BODY>

<table border="1" width="100%" height="371">
  <FORM name='post' onSubmit="return disableForm(this);" method='POST' action="./messages.php?load_template=send.tpl">
	<tr>
		<td height="30" colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td width="17%"></td>
		<td width="19%">

<SELECT size=5 name=ops>
#member_list_name#
</SELECT>

<SCRIPT type=text/javascript>
var myfilter = new filterlist(document.post.ops);
</SCRIPT>
		</td>
		<td width="62%">
						<font face="Verdana" style="font-size: 9pt">[filter by] 
						:</font> <SELECT 
                        onchange=javascript:myfilter.set(this.value) size=1 
                        name=letters> <OPTION value="" selected>Show all<OPTION value=^A>
						A<OPTION value=^B>B<OPTION 
                          value=^C>C<OPTION value=^D>D<OPTION value=^E>E<OPTION 
                          value=^F>F<OPTION value=^G>G<OPTION value=^H>H<OPTION 
                          value=^I>I<OPTION value=^J>J<OPTION value=^K>K<OPTION 
                          value=^L>L<OPTION value=^M>M<OPTION value=^N>N<OPTION 
                          value=^O>O<OPTION value=^P>P<OPTION value=^Q>Q<OPTION 
                          value=^R>R<OPTION value=^S>S<OPTION value=^T>T<OPTION 
                          value=^U>U<OPTION value=^V>V<OPTION value=^W>W<OPTION 
                          value=^X>X<OPTION value=^Y>Y<OPTION 
                        value=^Z>Z</OPTION></SELECT><br><br>
                        
                        <font face="Verdana" style="font-size: 9pt">Search 
						nickname</font><BR>
                      <INPUT onkeyup=myfilter.set(this.value) 
                        name=regexp size="20"> <INPUT class=mainoption onClick="myfilter.reset();this.form.regexp.value=''" type=button value=Clear><BR><INPUT 
                        onclick="myfilter.set_ignore_case(!this.checked)"
                        type="checkbox" value="ON" name="toLowerCase"> 
                        <font face="Verdana" style="font-size: 9pt">
						Case-sensitive</font>
		</td>
	</tr>
	<tr>
		<td width="17%" height="26">Subject</td>
		<td width="81%" height="26" colspan="2">
		<INPUT class=post style="WIDTH: 450px" tabIndex=2 maxLength=30 size=45 name=subject>
		</td>
	</tr>
	<tr>
		<td width="17%" height="202">&nbsp;</td>
		<td width="81%" height="202" colspan="2">
<TEXTAREA style="WIDTH: 450px" tabIndex=3 name=message rows=11 wrap=virtual cols=35></TEXTAREA>
		</td>
	</tr>
<TR>
<TD align=middle colSpan=4 height=28>
<INPUT onClick="this.value='Send'" tabIndex='5' type='submit' value='Send' name='send'>
&nbsp;
<INPUT accessKey='s' tabIndex='6' type='reset' value='Reset' name='reset'>
</TD>
</TR>

	</FORM>
</table>
<P align=center><A href="javascript:top.close()"><font face=verdana color=black size=2 >Close this</font></A></P>

</BODY>
</HTML>