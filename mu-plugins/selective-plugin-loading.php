<?php
/*
todo list:
- check if plugin is active
- handle deactivated plugins in the interface (currently they're invisible and may get forgotten after saving)
- option to handle post requests
- option to handle admin-ajax (or anything else in wp-admin)

*/

$request_uri = urldecode ( parse_url( $_SERVER['REQUEST_URI'],PHP_URL_PATH));
$query_string = parse_url( $_SERVER['REQUEST_URI'],PHP_URL_QUERY );
//echo 'request_uri :' . $request_uri . PHP_EOL;
//echo 'query_string:' . $query_string . PHP_EOL;
$is_admin = strpos( $request_uri, '/wp-admin/' )!== false;
$is_get   = strpos($_SERVER['REQUEST_METHOD'],'GET') !== false; //other request types may require different handling. for now all of them are handeled the same

// add filter in front pages only
if( false === $is_admin ){
	add_filter( 'option_active_plugins', 'kinsta_option_active_plugins' );
}

function check_match_with_wildcard($request_uri,$filter_lines){

	//$found_uri = false; //should eventually be removed
	//var_dump($filter_lines);
	foreach($filter_lines as $filter_line){
		$curr_request_uri = $request_uri;

		//echo "request uri: $curr_request_uri, filter->request_uri: $filter_line\n";
		$ar = explode('*',$filter_line);
		//print_r($ar);
		//if (sizeof($ar)==0 && $request_uri!==''){echo "empty condition - useless\n";continue;} //empty string
		if (sizeof($ar)==1 && $ar[0] !=$curr_request_uri) {/*echo "exact match failed1\n";var_dump($ar[0]);var_dump($curr_request_uri);*/continue;} //exact string
		if ($ar[0]!=='' && strpos($curr_request_uri,$ar[0])!==0) {/*echo "starts with failed2\n";*/continue;} //starts with
		$last = array_pop($ar);
		if ($last!=='' && strrpos($curr_request_uri,$last)!==strlen($curr_request_uri)-strlen($last)){/*echo "ends with failed\n";*/continue;} //ends with
		//echo "1st stage passed with filter $filter_line url $request_uri\n"; 
		
		while($ar){
			$curr = array_shift($ar);
			if ($curr==='') continue;
			$pos = strpos($curr_request_uri,$curr);
			if ($pos===false) {/*echo "2nd stage failed with filter $filter_line url $request_uri\n";*/  continue 2;}
			//echo "before: $curr_request_uri ";
			$curr_request_uri = substr($curr_request_uri,$pos+strlen($curr));
			//echo "after: $curr_request_uri \n";
			
		}
		//echo "line pass!\n";
		//$found_uri = true; //should be eventualy return true
		return true;
	}
	//return $found_uri; //should be eventually return false
	return false;
}

function kinsta_option_active_plugins( $plugins ){
	global $request_uri;
	global $query_string;
	$filters = json_decode(get_option('selective_plugin_loading'));
	//echo "\n\nstarting...\n";
	foreach ($filters as $line_number=>$filter){
		//var_dump($filter);
		if (empty($filter->enabled) || !$filter->enabled) continue;
		if (!check_match_with_wildcard($request_uri,explode("\n",$filter->request_uri))) continue;
		//echo "\nrule " . ($line_number+1) . " uri pass\n\n";
		if (!check_match_with_wildcard($query_string,explode("\n",$filter->query_string))) continue;
		//echo "\nrule " . ($line_number+1) . " pass\n\n";		
		//continue; //should be disabled to act according to the found filter

		if ($filter->action==='unload') {
			foreach ( $filter->plugins as $plugin ) {
				$k = array_search( $plugin, $plugins );
				if( false !== $k ){
					unset( $plugins[$k] );
				}
			}	
		}
		else if ($filter->action==='load') {
			//echo "action=load\n";
			//echo "plugins:";
			//var_dump ($plugins);
			foreach ($plugins as $index=>$plugin){
				$k = array_search( $plugin, $filter->plugins );
				//echo $k;
				if( false === $k ){
					//echo "unsetting\n";
					unset( $plugins[$index] );
				}
			}
		}
//		if ($filter->action==='all') return $plugins;
		//var_dump($plugins);
		return $plugins;
	}
	//var_dump($plugins);
	
	//if no rules match
	return $plugins;
/*	
	$is_contact_page = strpos( $request_uri, '/contact/' );

	$unnecessary_plugins = array();

	// conditions
	// if this is not contact page
	// deactivate plugin
	if( false === $is_contact_page ){
		$unnecessary_plugins[] = 'contact-form-7/wp-contact-form-7.php';
	}

	foreach ( $unnecessary_plugins as $plugin ) {
		$k = array_search( $plugin, $plugins );
		if( false !== $k ){
			unset( $plugins[$k] );
		}
	}
	return $plugins;
	*/
}
