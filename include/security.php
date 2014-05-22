<?PHP	

class security{
	
	var $get_magic_quotes   = 0;
	var $allow_unicode      = 1;
	
	var $input              = array( 'room' 		=> NULL,
									 'lastid'		=> 0,
									 'user'			=> NULL,
									 'text'			=> NULL,
									 'time'			=> 0,
									 'msg'			=> NULL,
									 'private_id'	=> 0,
									 'load_template'=> NULL,
									 'other_vars'	=> NULL,
									 'back'			=> NULL,
									 'type'		    => NULL );
									 
	function strip_tags_except($text, $allowed_tags, $strip=TRUE) {
	  if (!is_array($allowed_tags))
	   return $text;
	
	  if (!count($allowed_tags))
	   return $text;
	
	  $open = $strip ? '' : '&lt;';
	  $close = $strip ? '' : '&gt;';
	
	  preg_match_all('!<\s*(/)?\s*([a-zA-Z]+)[^>]*>!',$text, $all_tags);
	  array_shift($all_tags);
	  $slashes = $all_tags[0];
	  $all_tags = $all_tags[1];
	  foreach ($all_tags as $i => $tag) {
	   if (in_array($tag, $allowed_tags))
		 continue;
	   $text =
		 preg_replace('!<(\s*' . $slashes[$i] . '\s*' .
		   $tag . '[^>]*)>!', $open . '$1' . $close,
		   $text);
	  }
	
	  return $text;
	}					 
	
	/*-------------------------------------------------------------------------*/
	// txt_stripslashes
	// ------------------
	// Make Big5 safe - only strip if not already...
	/*-------------------------------------------------------------------------*/
	
	/**
	* Remove slashes if magic_quotes enabled
	*
	* @param	string	Input String
	* @return	string	Parsed string
	* @since	2.0
	*/
	function txt_stripslashes($t)
	{
		if ( $this->get_magic_quotes )
		{
    		$t = stripslashes($t);
    		$t = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $t );
    	}
    	
    	return $t;
    }
	
    function parse_clean_value($val)
    {
    	if ( $val == "" )
    	{
    		return "";
    	}
        
		// Strip HTML JAVA PHP XML CDATA TAGS
	    $val = $this->strip_tags_except($val,array('b','i','u'),FALSE);
	    
    	$val = str_replace( "&#032;", " ", $this->txt_stripslashes($val) );
    	// As cool as this entity is...
    	$val = str_replace( "&#8238;"		, ''			  , $val );
    	$val = str_replace( "&"				, "&amp;"         , $val );
		$val = str_replace( '"'				, "&quot;"        , $val );
    	$val = str_replace( "\n"			, "<br />"        , $val ); // Convert literal newlines
    	$val = str_replace( "$"				, "&#036;"        , $val );
    	$val = str_replace( "\r"			, ""              , $val ); // Remove literal carriage returns
    	$val = str_replace( "!"				, "&#33;"         , $val );
    	$val = str_replace( "'"				, "&#39;"         , $val ); // IMPORTANT: It helps to increase sql query safety.
    	
    	// Ensure unicode chars are OK
    	
    	if ( $this->allow_unicode )
		{
			$val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );
			
			//-----------------------------------------
			// Try and fix up HTML entities with missing ;
			//-----------------------------------------

			$val = preg_replace( "/&#(\d+?)([^\d;])/i", "&#\\1;\\2", $val );
		}
    	
    	return $val;
    }
	
	/*-------------------------------------------------------------------------*/
    // Key Cleaner - ensures no funny business with form elements             
    /*-------------------------------------------------------------------------*/
    
    /**
	* Clean _GET _POST key
    *
	* @param	string	Key name
	* @return	string	Cleaned key name
	* @since	2.1
	*/
    function parse_clean_key($key)
    {
    	if ($key == "")
    	{
    		return "";
    	}
    	
    	$key = htmlspecialchars(urldecode($key));
    	$key = str_replace( ".."           , ""  , $key );
    	$key = preg_replace( "/\_\_(.+?)\_\_/"  , ""  , $key );
    	$key = preg_replace( "/^([\w\.\-\_]+)$/", "$1", $key );
    	
    	return $key;
    }
	
	/*-------------------------------------------------------------------------*/
    // parse_incoming_recursively
    /*-------------------------------------------------------------------------*/
	/**
	* Recursively cleans keys and values and
	* inserts them into the input array
	*/
	function parse_incoming_recursively( &$data, $input=array(), $iteration = 0 )
	{
		// Crafty hacker could send something like &foo[][][][][][]....to kill Apache process
		// We should never have an input array deeper than 10..
		
		if( $iteration >= 10 )
		{
			return $input;
		}
		
		if( count( $data ) )
		{
			foreach( $data as $k => $v )
			{
				if ( is_array( $v ) )
				{
					//$input = $this->parse_incoming_recursively( $data[ $k ], $input );
					$input[ $k ] = $this->parse_incoming_recursively( $data[ $k ], array(), $iteration+1 );
				}
				else
				{	
					$k = $this->parse_clean_key( $k );
					$v = $this->parse_clean_value( $v );
					
					$input[ $k ] = $v;
				}
			}
		}
		
		return $data = $input;
	}	
	
	/*-------------------------------------------------------------------------*/
    // clean_globals
    /*-------------------------------------------------------------------------*/
	/**
	* Performs basic cleaning
	* Null characters, etc
	*/
	function clean_globals( &$data, $iteration = 0 )
	{
		// Crafty hacker could send something like &foo[][][][][][]....to kill Apache process
		// We should never have an globals array deeper than 10..
		
		if( $iteration >= 10 )
		{
			return $data;
		}
		
		if( count( $data ) )
		{
			foreach( $data as $k => $v )
			{
				if ( is_array( $v ) )
				{
					$this->clean_globals( $data[ $k ], $iteration+1 );
				}
				else
				{	
					# Null byte characters
					$v = preg_replace( '/\\\0/' , '&#92;&#48;', $v );
					$v = preg_replace( '/\\x00/', '&#92;x&#48;&#48;', $v );
					$v = str_replace( '%00'     , '%&#48;&#48;', $v );
					
					# File traversal
					$v = str_replace( '../'    , '&#46;&#46;/', $v );
					
					$data[ $k ] = $v;
				}
			}
		}
	}
	
	
    function parse_incoming()
    {
		//-----------------------------------------
		// Attempt to switch off magic quotes
		//-----------------------------------------

		@set_magic_quotes_runtime(0);

		$this->get_magic_quotes = @get_magic_quotes_gpc();
		
    	//-----------------------------------------
    	// Clean globals, first.
    	//-----------------------------------------
    	
		$this->clean_globals( $_GET );
		$this->clean_globals( $_POST );
		$this->clean_globals( $_COOKIE );
		$this->clean_globals( $_REQUEST );
    	
		# GET first
		$input = $this->parse_incoming_recursively( $_GET, array() );
		
		# Then overwrite with POST
		$input = $this->parse_incoming_recursively( $_POST, $input );

		unset( $input );
		
		# Assign request method
		$_SERVER["REQUEST_METHOD"] = strtolower($_SERVER["REQUEST_METHOD"]);
	}
}
	
?>