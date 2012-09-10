<?php
#Nicholas Osborne


#Poll BBC ION
function query_content($aString){
	$aString = urlencode($aString);
	$url = "http://www.bbc.co.uk/iplayer/ion/searchextended/search_availability/iplayer/service_type/radio/format/json/q/$aString";
	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($c, CURLOPT_URL, $url);
	$contents = curl_exec($c);
	$err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
	curl_close($c);
	$data = json_decode($contents);
	return $data;
}

#Parse the Data
function parse_results($aResult){

	$results = array();

	foreach ($aResult->blocklist as $value) {

		#Programme Name
		$toplevel_container_title = $value->toplevel_container_title;
		
		#Synopsis
		$synopsis = $value->synopsis;

		#Programme Title
		$hierarchical_title = $value->hierarchical_title;

		#Programme URL
		$my_url = $value->my_url;
	
		#Package into an Array for JSON return
		$temp = array('hierarchical_title'=>$hierarchical_title,'synopsis'=>$synopsis,'my_url'=>$my_url);
		
		#Store Episodes by Programme
	
		if(!isset($results[$toplevel_container_title])){
			$results[$toplevel_container_title] = array();
		}
	
		array_push($results[$toplevel_container_title], $temp);
	}
	
	#Generate HTML for the array structure
	
	$output = "<ul>";
	
	foreach ($results as $programme => $episodes) {
	
		$output.= "<li><span class=\"programme_title\">$programme</span>";
				
		foreach($episodes as $episode){
			$output.="<ul>";
			$output.="<li><span class=\"episode_title\"><a href=\"http://www.bbc.co.uk".$episode["my_url"]."\" target=\"_blank\">".$episode["hierarchical_title"]."</a></span>";
			$output.="<span class=\"episode_synopsis\">".$episode["synopsis"]."</span></li>";
			$output.="</ul>";
		}
	
	
		$output.= "</li>";
	}
	
	$output.="</ul>";
	
	return $output;
}

#SEARCH
function search(){


	$query = $_REQUEST['query'];
	

	$data = query_content($query);
	
	#Check and see if there are any results
	
	if($data->count > 0){
		$return_data['status'] = "ok";
    	$return_data['count'] = $data->count;
    	$return_data['content'] = parse_results($data);
	
	}else{
		$return_data['status'] = "no_results";
	}
	
	
	return $return_data;

}




#GET FUNCTION
$function = $_REQUEST['function'];

$return_data = array();

switch($function){

	case "search":
	$return_data = search();
	break;
	
	default:
   	$return_data['status'] = "error";
   	$return_data['error'] = "nofunction";
   	break;
}

echo json_encode($return_data);
?>