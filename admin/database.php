<?php

//Forgot who I got this function from. But thanks!
function getFileList($dir, $recurse=false, $depth=false) { # array to hold return value 
 $retval = array(); 
 
 # add trailing slash if missing 
 if(substr($dir, -1) != "/") 
 	$dir .= "/"; 
 	
 # open pointer to directory and read list of files 
 	$d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading"); 
 	while(false !== ($entry = $d->read())) { 
	 	
	# skip hidden files 
 	if($entry[0] == ".") continue; if(is_dir("$dir$entry")) { 
	 	
	 	$retval[] = "$dir$entry/"; 
	 		
			if($recurse && is_readable("$dir$entry/")) {  
				if($depth === false) {  
					$retval = array_merge($retval, getFileList("$dir$entry/", true));  
					} 
				elseif($depth > 0) { 
					$retval = array_merge($retval, getFileList("$dir$entry/", true, $depth-1)); }  
				}
				
			 }
			  
			 elseif(is_readable("$dir$entry")) { 
				 $retval[] =  "$dir$entry"; 
				 } 	} 	$d->close(); 	return $retval; 
}

function delTree($dir) { // bcairns@gmail.com :3 Thanks!
	global $wpdb,$series,$page,$chapter,$db,$status,$settings;		

	if ($dir == UPLOAD_FOLDER || 
	   ($dir == UPLOAD_FOLDER.$series['folder'] && $_GET['chapter']) ||  
	   ($dir == UPLOAD_FOLDER.$series['folder'].$chapter['folder'] && $_GET['pg'])) //Don't Delete the Comic foldeR D:
		return;
		 
		//var_dump($dir);
		//echo '<br/>';
		
	    $files = glob( $dir . '*', GLOB_MARK );
	    foreach( $files as $file ){
	        if( substr( $file, -1 ) == '/' )
	            delTree( $file );
	        else
	            unlink( $file );
	    }
	    rmdir( $dir );
	}

Class kommiku_database {
		
	function chapter_update_list() {
		global $wpdb;
		$chapterUquery = "SELECT `".$wpdb->prefix."comic_series`.`slug` as series_slug, 
		`".$wpdb->prefix."comic_chapter`.`slug` as chapter_slug, 
		`".$wpdb->prefix."comic_chapter`.`pubdate` as pubdate,
		`".$wpdb->prefix."comic_series`.`title` as series_name
		FROM `".$wpdb->prefix."comic_chapter`,`".$wpdb->prefix."comic_series` 
		WHERE `".$wpdb->prefix."comic_chapter`.`series_id` = `".$wpdb->prefix."comic_series`.`id`
		ORDER BY `".$wpdb->prefix."comic_chapter`.`pubdate` DESC LIMIT 0 , 15";
		$pageUquery = "SELECT `".$wpdb->prefix."comic_series`.`slug` as series_slug, 
		`".$wpdb->prefix."comic_chapter`.`slug` as chapter_slug, 
		`".$wpdb->prefix."comic_page`.`slug` as page_slug, 
		`".$wpdb->prefix."comic_page`.`pubdate` as pubdate,
		`".$wpdb->prefix."comic_series`.`title` as series_name,
		`".$wpdb->prefix."comic_series`.`chapterless` as chapterless
		FROM `".$wpdb->prefix."comic_page`,`".$wpdb->prefix."comic_chapter`,`".$wpdb->prefix."comic_series` 
		WHERE `".$wpdb->prefix."comic_page`.`series_id` = `".$wpdb->prefix."comic_series`.`id`
		AND `".$wpdb->prefix."comic_page`.`chapter_id` = `".$wpdb->prefix."comic_chapter`.`id`
		ORDER BY `".$wpdb->prefix."comic_page`.`pubdate` DESC LIMIT 0 , 15";
		$chapterUpdatesList = $wpdb->get_results($chapterUquery);
	
		return $chapterUpdatesList;
	
	}
		
	function trailingslash($str) {
		
		if (substr($str, -1, 1) != "/" && $str != '')
			$str .= "/";
			
		return $str;
	}
	
	function trail($str) {
		
		if(!$str)
			return;	
		
		if ($str[0] == "/")
			$str = substr($str, 1);
			
		if (substr($str, -1, 1) == "/")
			$str = substr($str, 0, -1);
			
		return $str;
	}
	
	function get_top_rated($count = 10) {
		global $wpdb;

		$result = $wpdb->get_var("
			SELECT *, 
			
			(SELECT sum(`".$wpdb->prefix."comic_counter`.rating)
			FROM `".$wpdb->prefix."comic_counter`
			WHERE `".$wpdb->prefix."comic_counter`.series_id = `".$wpdb->prefix."comic_series`.id) as total_rating,
			
			(SELECT count(*)
			FROM `".$wpdb->prefix."comic_counter`
			WHERE `".$wpdb->prefix."comic_counter`.series_id = `".$wpdb->prefix."comic_series`.id AND `".$wpdb->prefix."comic_counter`.rating IS NOT NULL) as total_votes
			
			FROM `".$wpdb->prefix."comic_series`
			ORDER by total_rating DESC
			LIMIT 0 , ".$count
		);
		
		return $result;
	}
	
	
	function counter_create($ip_address,$series_id,$chapter_id = 0,$page_id = 0,$user_id = 0) {
	    global $wpdb;
		$table = $wpdb->prefix."comic_counter";
	  	$wpdb->insert( $table , 
	 
	  	array( 'user_id' => $user_id,
	  		   'ip_address' => $ip_address,  
	  		   'series_id' => $series_id, 
	  		   'chapter_id' => $chapter_id,
	  		   'page_id' => $page_id,
	  		   'value' => 1
	  		 ), 
	  	
	  	array( '%d', 
	  	       '%s',
	  	       '%d',
	  	       '%d',
	  	       '%d',
	  	       '%d'
	  	       )  
	  	    );
	
	}
	
	function counter_read($ip,$series_id,$chapter_id = 0,$page_id = 0,$user_id = 0) {
		global $wpdb;
		$table = $wpdb->prefix."comic_counter";
		
		if($ip) $ipWord = " AND ip_address = '$ip'";
		
		$result = $wpdb->get_var("SELECT value FROM `".$table."` 
		WHERE user_id = '".$user_id."' 
		AND series_id = '".$series_id."'
		AND chapter_id = '".$chapter_id."'
		AND page_id = '".$page_id."'".$ipWord);
		
		return $result;
	}
		
	function visitor_ip() {
		if ( isset($_SERVER["REMOTE_ADDR"]) )    {
		    return $_SERVER["REMOTE_ADDR"];
		} else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )    {
		    return $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )    {
		    return $_SERVER["HTTP_CLIENT_IP"];
		}
	}
	
	function get_rating($series_id,$chapter_id,$page_id) {
		global $wpdb;
		$table = $wpdb->prefix."comic_counter";
		$results = $wpdb->get_var("SELECT sum(rating)/count(*) FROM `$table` WHERE series_id = '$series_id' AND chapter_id = '$chapter_id' AND page_id = '$page_id' AND rating IS NOT NULL");
		return $results/4;
	}
	
	function get_my_rating($ip_address,$series_id,$chapter_id = 0,$page_id = 0,$user_id = 0) {
		global $wpdb;
		$table = $wpdb->prefix."comic_counter";
		$results = $wpdb->get_var("SELECT rating FROM `$table` WHERE series_id = '$series_id' AND chapter_id = '$chapter_id' AND page_id = '$page_id' AND `ip_address` = '$ip_address'  AND `user_id` = '$user_id'");
		return $results;
	}
	
	function get_votes($series_id,$chapter_id = 0,$page_id = 0) {
		global $wpdb;
		$table = $wpdb->prefix."comic_counter";
		$results = $wpdb->get_var("SELECT count(*) FROM `$table` WHERE series_id = '$series_id' AND chapter_id = '$chapter_id' AND page_id = '$page_id' AND rating IS NOT NULL");
		return $results;
	}
	
	function rating_counter_update($ip_address,$series_id,$chapter_id = 0,$page_id = 0,$rating,$user_id = 0) {
		global $wpdb;
		$table = $wpdb->prefix."comic_counter";
		$query = "UPDATE $table SET `rating` = $rating WHERE `series_id` = '$series_id' AND `chapter_id` = '$chapter_id' AND `page_id` = $page_id AND `user_id` = '$user_id' AND `ip_address` = '$ip_address'";
		if(is_numeric($chapter_id) && is_numeric($page_id)) {
			$wpdb->query($query); 
		}
	}
	
	function view_counter_update($ip_address,$series_id,$chapter_id = 0,$page_id = 0,$user_id = 0) {
		global $wpdb;
		$table = $wpdb->prefix."comic_counter";
		$query = "UPDATE $table SET `value` = `value` + 1 WHERE `series_id` = '$series_id' AND `chapter_id` = '$chapter_id' AND `page_id` = $page_id AND `user_id` = '$user_id' AND `ip_address` = '$ip_address'";
		if(is_numeric($chapter_id) && is_numeric($page_id)) {
			$wpdb->query($query); 
		}
	}
	
	function slug($str) {
		
		$str = str_replace("'","",$str);
		$str = str_replace(" ","_",$str);
		return strtolower($str);
		
	}
	
	function clean($str) {
		if(!get_magic_quotes_gpc()) {
			$str = addslashes($str);
		}
		$str = strip_tags(htmlspecialchars($str));
		return $str;
	}

	function page_create(
		$title = "",
		$slug,
		$img,
		$pubdate,
		$story      	 = '',
		$number,
		$series_id,
		$chapter_id,
		$wp_post_slug
	) {
	    global $wpdb;
			
		$table = $wpdb->prefix."comic_page";
	
	  	$wpdb->insert( $table , 
	  	
	  	array( 'title' => $title, 
	  		   'slug' => $slug, 
	  		   'img' => $img,
	  		   'pubdate' => $pubdate,
	  		   'story' => $story,
	  		   'number' => $number,
	  		   'series_id' => $series_id,
	  		   'chapter_id' => $chapter_id,
			   'wp_post_slug' => $wp_post_slug
	  		 ), 
	  	
			array( '%s', '%s','%s','%s','%s','%d','%d','%d', '%s' )
	  	       
	  	    );
	
	  		
	}
	
	function category_update($id,$name,$summary,$slug)  {
	    global $wpdb;
	    
	    if(is_numeric($id)) {
			$table = $wpdb->prefix."comic_category";
		  	$wpdb->update( $table , 
		  	
			  	array( 'title' => $name, 
			  		   'summary' => $summary, 
	 		   		   'slug' => $slug
			  	  		 ), 
			  	array( 'id' => $id ),
			  	array( '%s', '%s', '%s'),  
				array( '%d' ) );
		}
	}
	
	function category_detail($slug) {
	    global $wpdb;
		
		$table = $wpdb->prefix."comic_category";
	  		$select = "SELECT * FROM ".$table." WHERE slug = '".$slug."'";
	  		$results = $wpdb->get_row( $select , ARRAY_A );
			return $results;
		
	}
	
	function category_read() {
	    global $wpdb;
	    
			$table = $wpdb->prefix."comic_category";
	  		$select = "SELECT * FROM ".$table." ORDER BY title";
	  		$results = $wpdb->get_results( $select );
			return $results;
		
	}

	function category_create($name,$summary,$slug) {
	    global $wpdb;
		
		$table = $wpdb->prefix."comic_category";
	  	$wpdb->insert( $table , 
	  	
	  	array( 'title' => $name, 
	  		   'summary' => $summary,
			   'slug' => $slug
	  		 ), 
	  	
	  	array( '%s', '%s', '%s' )  
	  	       
	  	    );
	  	    
	}
	
	function category_delete($category) {
	    global $wpdb;
	    
		if(is_numeric($category)) {
		$table = $wpdb->prefix."comic_category";
	  		$select = "DELETE FROM ".$table." WHERE id = '".$category."'";
		    $wpdb->query($select);
		    
		}
	}
	
	function search_category($category)  {
	    global $wpdb;
			$table = $wpdb->prefix."comic_series";
			$category = str_replace("_"," ",$category);
	  		$select = "SELECT * FROM ".$table." WHERE `categories` LIKE '%".$category."%'";
	  		$results = $wpdb->get_results( $select );
			return $results;
	}
	
	function find_series($find)  {
	    global $wpdb;
	    $find = stripslashes($find);
			$table = $wpdb->prefix."comic_series";			
	  		$select = "SELECT * FROM `".$table."` WHERE `categories` LIKE '%".$find."%' OR `title` LIKE '%".$find."%' OR `alt_name` LIKE '%".$find."%' OR `author` LIKE '%".$find."%' OR `illustrator` LIKE '%".$find."%'";
	  		$results = $wpdb->get_results( $select );
			return $results;
	}
	
	function check_chapter_slug($series_id,$slug) {
	global $wpdb;
	
	$table = $wpdb->prefix."comic_chapter";
	if($wpdb->get_var("SELECT slug FROM `".$table."` WHERE slug = '".$slug."' AND series_id = '".$series_id."'"))
		return true;
	else
		return false;
	}
	
	function scanlators_chapter($scanlators_name) {
	    global $wpdb;
		$tableA = $wpdb->prefix."comic_chapter";
		$tableB = $wpdb->prefix."comic_series";
	  		$select = "SELECT ".$tableB.".slug as series_slug, ".$tableB.".title as series_title, ".$tableA.".number as chapter_number, ".$tableA.".pubdate as chapter_date, ".$tableA.".title as chapter_title 
	  		FROM ".$tableA.", ".$tableB."  
	  		WHERE 
	  		    ".$tableA.".scanlator_slug LIKE '%".$scanlators_name."%' 
	  		AND ".$tableB.".id = ".$tableA.".series_id 
	  		ORDER BY ".$tableA.".id DESC";
	  		
	  		$results = $wpdb->get_results( $select );
		return $results;
	}
	
	function check_chapter_number($series_id,$number) {
	    global $wpdb;
		
		$table = $wpdb->prefix."comic_chapter";
		if($wpdb->get_var("SELECT number FROM `".$table."` WHERE number = '".$number."' AND series_id = '".$series_id."'"))
			return true;
		else
			return false;
	}	
	
	function chapter_create($title = '', $number, $summary = '', $series_id, $date, $slug,$scanlator = '',$scanlator_slug = '',$volume = 0,$folder = '',$returner = false) {
	    global $wpdb;
		
		if($volume = '') $volume = 0;
		
		$table = $wpdb->prefix."comic_chapter";
	  	$wpdb->insert( $table , 
	  	
	  	array( 'title' => $title, 
	  		   'number' => $number, 
	  		   'summary' => $summary,
	  		   'series_id' => $series_id,
			   'pubdate' => $date,
			   'slug' => $slug,
			   'scanlator' => $scanlator,
			   'scanlator_slug' => $scanlator_slug,
			   'volume' => $volume,
			   'folder' => $folder
	  		 ), 
	  	
	  	array( '%s', '%s', '%s', '%d', '%s', '%s','%s', '%s', '%d', '%s' )  
	  	       
	  	    );
	  	 if($returner == true)
	  	 	return $wpdb->insert_id;
	
	}

	function historyu($what,$action,$pubdate,$series_name,$series_slug,$chapter_name,$chapter_number,$page_name,$page_slug) {
	    global $wpdb;
		
		$table = $wpdb->prefix."comic_history";
	  	$wpdb->insert( $table , 
	  	
	  	array( 'what' => $what, 
	  		   'action' => $action, 
	  		   'pubdate' => $pubdate,
	  		   'series_name' => $series_name,
	  		   'series_slug' => $series_slug,
	  		   'chapter_name' => $chapter_name,
	  		   'chapter_number' => $chapter_number,
	  		   'page_name' => $page_name,
	  		   'page_slug' => $page_slug
	  		 ), 
	  	
	  	array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )  
	  	       
	  	    );
	  	  
	
	}
	
	function series_create($title, $slug, $summary = '', $chapterless = 0, $categories = "",$author,$illustrator,$read,$creation = '',$alt_name = '', $status = 'ongoing', $rating = 0,$story_type = 0,$img = '') {
	    global $wpdb;
			
		$table = $wpdb->prefix."comic_series";
	  	$wpdb->insert( $table , 
	 
	  	array( 'title' => $title, 
	  		   'slug' => $slug, 
	  		   'summary' => $summary,
	  		   'chapterless' => $chapterless,
	  		   'categories' => $categories,
	  		   'author' => $author,
	  		   'illustrator' => $illustrator,
	  		   'read' => $read,
	  		   'creation' => $creation,
	  		   'alt_name' => $alt_name,
	  		   'status' => $status,
			   'rating' => $rating,
			   'type' => $story_type,
			   'img' => $img
	  		 ), 
	  	
	  	array( '%s', 
	  	       '%s',
	  	       '%s',
	  	       '%d',
	  	       '%s',
	  	       '%s',
	  	       '%s',
	  	       '%d',
	  	       '%s',
	  	       '%s',
	  	       '%d',
			   '%d',
	  	       
	  	       )  
	  	    );
	
		return $wpdb->insert_id;
	
	}
	
	//Read
	function chapterless($series_id = NULL) {
	    global $wpdb;
	    
	    if(!$series_id)
			$series_id      = $_GET['series'];
	    
		if(is_numeric($series_id)) {
			$chapterless = $wpdb->get_var("SELECT chapterless FROM `".$wpdb->prefix."comic_series` WHERE id = '".$series_id."'");
		}
		
		return $chapterless;
    }
	
	function page_number($series_id = NULL, $chapter_id = NULL) {
	    global $wpdb;
	
	    if(!is_numeric($series_id))
			$series_id      = $_GET['series'];
		
		if(!is_numeric($chapter_id))
			$chapter_id     = $_GET['chapter'];
			
		if(is_numeric($series_id) && is_numeric($chapter_id)){
			$table = $wpdb->prefix."comic_page";
		  	$select = "SELECT max(number) as max_number FROM ".$table." WHERE chapter_id = '".$chapter_id."' AND series_id = '".$series_id."'";
	  		$results = $wpdb->get_row( $select , ARRAY_A );
			return $results["max_number"]+1;
		}
	}	
	function chapter_hupdate($y = 30){
	    global $wpdb;

		if(is_numeric($y))
			$query = '
			SELECT b.slug AS series_slug, b.title as series_title, a.slug as chapter_slug, a.pubdate as date
			FROM `'.$wpdb->prefix.'comic_chapter` as a, `'.$wpdb->prefix.'comic_series` as b
			WHERE b.id = a.series_id
			ORDER BY a.`pubdate` DESC 
			LIMIT 0 , '.$y;
			
		$results = $wpdb->get_results( $query );
		return $results;
		
		
	}
	
	function page_list($series_id = NULL, $chapter_id = NULL) {
	    global $wpdb;
		$table = $wpdb->prefix."comic_page";

	    if(!$series_id)
			$series_id   = $_GET['series'];
		
		if(!$chapter_id)
			$chapter_id  = $_GET['chapter'];
		
		if(!$chapter_id && $series_id) {
			$chapterless = $wpdb->get_var("SELECT chapterless FROM `".$wpdb->prefix."comic_series` WHERE id = '".$series_id."'");
		}
		
		if(is_numeric($series_id) && is_numeric($chapter_id)){
		  	$select = "SELECT * FROM ".$table." WHERE chapter_id = '".$chapter_id."' AND series_id = '".$series_id."'";
		} else if($chapterless == 1) {
		  	$select = "SELECT * FROM ".$table." WHERE chapter_id = '0' AND series_id = '".$series_id."'";
		}
		
		$results = $wpdb->get_results( $select );
		return $results;
	}
	
	function chapter_list() {
	    global $wpdb;
	    
		$series_id      = $_GET['series'];
		
	    if(is_numeric($series_id)){
			$table = $wpdb->prefix."comic_chapter";
		  		$select = "SELECT * FROM ".$table." WHERE series_id = '".$series_id."'";
		  		$results = $wpdb->get_results( $select );
				return $results;
		}
	}
	
	function series_list() {
		global $wpdb;
	  		$select = "SELECT
					c.id as id,
					c.title as series_name,
					c.slug as series_slug,
					c.title as title,
					c.slug as slug,
					a.slug as latest_slug,
					a.pubdate as last_update,
					c.chapterless as chapterless,
					c.status as status
					FROM `".$wpdb->prefix."comic_chapter` a,
					(SELECT series_id, max(number) as number
					FROM `".$wpdb->prefix."comic_chapter`
					GROUP BY series_id) b, 
					`".$wpdb->prefix."comic_series` c
					WHERE b.series_id = a.series_id AND b.number = a.number AND b.series_id = c.id
					";
	  		$results = $wpdb->get_results( $select );
			return $results;
	}
	
	function series_list_chapterless() {
		global $wpdb;
	  		$select = "SELECT
					c.id as id,
					c.title as series_name,
					c.slug as series_slug,
					c.title as title,
					c.slug as slug,
					a.slug as latest_slug,
					a.pubdate as last_update,
					c.chapterless as chapterless,
					c.status as status
					FROM `".$wpdb->prefix."comic_page` a,
					(SELECT series_id, max(number) as number
					FROM `".$wpdb->prefix."comic_page`
					GROUP BY series_id) b, 
					`".$wpdb->prefix."comic_series` c
					WHERE b.series_id = a.series_id AND b.number = a.number 
					AND b.series_id = c.id AND c.chapterless = 1
					";
	  		$results = $wpdb->get_results( $select );
			return $results;
	}
	
	function series_admin_list() {
		global $wpdb;
	  		$select = "SELECT * FROM `".$wpdb->prefix."comic_series`";
	  		$results = $wpdb->get_results( $select );
			return $results;
	}
	
	function series_chapter($series_id) {
	    global $wpdb;
	    if (!is_numeric($series_id)) return;
		$table = $wpdb->prefix."comic_chapter";
	  		$select = "SELECT * FROM ".$table." WHERE series_id = '".$series_id."' ORDER BY number";
	  		$results = $wpdb->get_results( $select );
			return $results;
	}
	
	function chapter_pages($series_id,$chapter_id) {
	    global $wpdb;
	    if (!is_numeric($series_id) || !is_numeric($chapter_id)) return;
		$table = $wpdb->prefix."comic_page";
	  		$select = "SELECT * FROM ".$table." WHERE series_id = '".$series_id."' AND chapter_id = '".$chapter_id."' ORDER BY number asc";
	  		$results = $wpdb->get_results( $select );
			return $results;
	}
	
	function series_pages($series_id) {
	    global $wpdb;
		$tableA = $wpdb->prefix."comic_page";
	  	$select = "SELECT * FROM `".$tableA."` WHERE `series_id` = '".$series_id."' ORDER BY `number` DESC";
	  	$results = $wpdb->get_results( $select );
		return $results;
	}
	
	function page_detail($id = NULL) {
	    global $wpdb;
	
	   if(!is_numeric($id))
		$id        = $_GET['pg'];

	    if(is_numeric($id)){
		$table = $wpdb->prefix."comic_page";
	  		$select = "SELECT * FROM ".$table." WHERE id = '".$id."'";
	  		$results = $wpdb->get_row( $select , ARRAY_A );
			return $results;
		}
	}
	
	function page_read($pageNumber,$chapterNumber,$series) {
	    global $wpdb;
	    
	    if($pageNumber && is_numeric($chapterNumber) && $series){
			$table = $wpdb->prefix."comic_series";
	  		$series_id_query = "SELECT * FROM ".$table." WHERE slug = '".$series."'";
	  		$series = $wpdb->get_row( $series_id_query , ARRAY_A );
	  		$series_id = $series['id'];

			$table = $wpdb->prefix."comic_chapter";
	  		$chapter_id_query = "SELECT * FROM ".$table." WHERE number = '".$chapterNumber."' AND series_id = '".$series_id."'";
	  		$chapter = $wpdb->get_results( $chapter_id_query );
	  		$chapter_id = $wpdb->get_row( $chapter_id_query , ARRAY_A );
		    
			$table = $wpdb->prefix."comic_page";
	  		$select = "SELECT * FROM ".$table." WHERE number = '".$pageNumber."' AND chapter_id = '".$chapter_id."' AND series_id = '".$series_id."'";
	  		$results = $wpdb->get_row( $select , ARRAY_A );
			return $results;
		}
	}
	
	function chapter_read($chapterNumber,$series) {
	    global $wpdb;
	    
	    if(is_numeric($pageNumber) && is_numeric($chapterNumber) && $series){
			$table = $wpdb->prefix."comic_series";
	  		$series_id_query = "SELECT * FROM ".$table." WHERE slug = '".$series."'";
	  		$series = $wpdb->get_row( $series_id_query , ARRAY_A );
	  		$series_id = $series['id'];

			$table = $wpdb->prefix."comic_chapter";
	  		$chapter_id_query = "SELECT * FROM ".$table." WHERE number = '".$chapterNumber."' AND series_id = '".$series_id."'";
	  		$chapter = $wpdb->get_results( $chapter_id_query );
	  		$results = $wpdb->get_row( $chapter_id_query , ARRAY_A );
			return $results;
		}
	}
	
	function series_read($series) {
	    global $wpdb;
	    
	    if(is_numeric($pageNumber) && is_numeric($chapterNumber) && $series){
			$table = $wpdb->prefix."comic_series";
	  		$series_id_query = "SELECT * FROM ".$table." WHERE slug = '".$series."'";
	  		$series = $wpdb->get_row( $series_id_query , ARRAY_A );
			return $series;
		}
	}
		
	function history_read($place = NULL,$value = NULL) {
	    global $wpdb;
	    
	 	$tableA = $wpdb->prefix."comic_history";
	    $select = "SELECT * FROM ".$tableA." LIMIT 0,10";
	    $result = $wpdb->get_results( $select );
	    return $result;
		
	}
	
	function chapter_detail($chapter_id = NULL) {
	    global $wpdb;
	    
		if (!is_numeric($chapter_id))
			$chapter_id = $_GET['chapter'];
					
	    if(is_numeric($chapter_id)){
		$table = $wpdb->prefix."comic_chapter";
	  		$select = "SELECT * FROM ".$table." WHERE id = '".$chapter_id."'";
	  		$results = $wpdb->get_row( $select , ARRAY_A );
			return $results;
		}
	}
	
	function series_detail($series_id = NULL) {
	    global $wpdb;
	    
		if (!$series_id)
			$series_id = $_GET['series'];
		
	    if(is_numeric($series_id)){
		$table = $wpdb->prefix."comic_series";
	  		$select = "SELECT * FROM ".$table." WHERE id = '".$series_id."'";
	  		$results = $wpdb->get_row( $select , ARRAY_A );
			return $results;
		}
	}
	
	function option_detail($type,$type_id,$option) {
	    global $wpdb;
	    		
	    if(is_numeric($type_id)){
		$table = $wpdb->prefix."comic_options";
	  		$select = "SELECT value FROM ".$table." WHERE type = '".$type."' AND type_id = '".$type_id."' AND option_name = '".$option."'";
	  		$results = $wpdb->get_row( $select , ARRAY_A );
			return $results;
		}
	}
	
	function option_create($type,$type_id,$option,$value) {
	    global $wpdb;
			
		$table = $wpdb->prefix."comic_options";
	  	$wpdb->insert( $table , 
	 
	  	array( 'type' => $type, 
	  		   'type_id' => $type_id, 
	  		   'option_name' => $option,
	  		   'value' => $value
	  		 ), 
	  	
	  	array( '%s', 
	  	       '%d',
	  	       '%s',
	  	       '%s'
	  	       )  
	  	       
	  	    );
	
	}
	
	function option_update($type,$type_id,$option,$value) {
	    global $wpdb;
	    
	    if(is_numeric($type_id)) {
			$table = $wpdb->prefix."comic_options";
		  	$wpdb->update( $table , 
		  	
			  	array( 'value' => $value
			  		 ), 
			  	array( 'type_id' => $type_id,
			  		   'type' => $type,
			  		   'option_name' => $option ),
			  	array( '%s' ),  
				array( '%d','%s','%s' ) );
		}
	}
	
	//Update
	function page_update(
		$id,
		$title = '',
		$slug,
		$img,
		$pubdate,
		$story      	 = '',
		$number,
		$series_id,
		$chapter_id,
		$wp_post_slug = ''
	) {
	    global $wpdb;
		
		$table = $wpdb->prefix."comic_page";
		
		$wpdb->update( $table, 
			array( 'title' => $title, 
	  		   'slug' => $slug, 
	  		   'img' => $img,
	  		   'pubdate' => $pubdate,
	  		   'story' => $story,
	  		   'number' => $number,
	  		   'series_id' => $series_id,
	  		   'chapter_id' => $chapter_id,
			   'wp_post_slug' => $wp_post_slug
	  		 ), 
			array( 'id' => $id ),
			array( '%s', '%s','%s','%s','%s','%d','%d','%d', '%s' ), 
			array( '%d' ) );
	
	}
	
	function scanlator_create($title, $slug, $text = '', $link = '') {
	    global $wpdb;
			
		$table = $wpdb->prefix."comic_scanlator";
	  	$wpdb->insert( $table , 
	 
	  	array( 'title' => $title, 
	  		   'slug' => $slug,
	  		   'text' => $text,
			   'link' => $link
	  		 ), 
	  	
	  	array( '%s', 
	  	       '%s',
	  	       '%s',
			   '%s'
	  	       )  
	  	       
	  	    );
	
	}
	
	function scanlator_list() {
		global $wpdb;
		$table = $wpdb->prefix."comic_scanlator";
	  		$select = "SELECT * FROM ".$table;
	  		$results = $wpdb->get_results( $select );
			return $results;
	}
	
	function scanlator_detail($id) {
	    global $wpdb;
	  
	    if(is_numeric($id)){
		$table = $wpdb->prefix."comic_scanlator";
	  		$select = "SELECT * FROM ".$table." WHERE id = '".$id."'";
	  		$results = $wpdb->get_row( $select , ARRAY_A );
			return $results;
		}
	}
	
	function scanlator_update($id,$title,$slug,$text,$link) {
	    global $wpdb;
	    
	    if(is_numeric($id)) {
			$table = $wpdb->prefix."comic_scanlator";
		  	$wpdb->update( $table , 
			  	array( 'title' => $title, 
			  		   'slug' => $slug, 
			  		   'text' => $text,
					   'link' => $link
			  		 ), 
			  	array( 'id' => $id ),
			  	array( '%s', '%s', '%s', '%s' ),  
				array( '%d' ) );
		}
	}
	
	function scanlator_delete($scanlator = NULL) {
	    global $wpdb;
	    
	   if(!is_numeric($scanlator))
		$scanlator    = $_GET['scanlator'];
		
		if(is_numeric($scanlator)) {
		$table = $wpdb->prefix."comic_scanlator";
	  		$select = "DELETE FROM ".$table." WHERE id = '".$scanlator."'";
		    $wpdb->query($select);
		    
		}
	}
	
	function chapter_update($id = NULL,$title = '',$number,$summary,$series_id,$date,$slug,$scanlator,$scanlator_slug,$volume,$folder) {
	    global $wpdb;
	    
	    if(!$id)
	    	$id = $_GET['chapter'];
	    
	    if(is_numeric($id)) {
			$table = $wpdb->prefix."comic_chapter";
		  	$wpdb->update( $table , 
		  	
			  	array( 'title' => $title, 
			  		   'number' => $number, 
			  		   'summary' => $summary,
			  		   'series_id' => $series_id,
					   'pubdate' => $date,
					   'slug' => $slug,
					   'scanlator' => $scanlator,
					   'scanlator_slug' => $scanlator_slug,
					   'volume' => $volume,
					   'folder' => $folder
			  		 ), 
			  	array( 'id' => $id ),
			  	array( '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s' ),  
				array( '%d' ) );
		}
	}
	
	function series_update($id,$title,$slug,$summary,$chapterless,$categories,$author,$illustrator,$read,$creation,$alt_name,$status,$rating,$story_type = 0,$img = '') {
	    global $wpdb;
	    
	    if(is_numeric($id)) {
			$table = $wpdb->prefix."comic_series";
		  	$wpdb->update( $table , 
			  	array( 'title' => $title, 
			  		   'slug' => $slug, 
			  		   'summary' => $summary,
			  		   'chapterless' => $chapterless,
			  		   'categories' => $categories,
			  		   'author' => $author,
			  		   'illustrator' => $illustrator,
			  		   'read' => $read,
			  		   'creation' => $creation,
			  		   'alt_name' => $alt_name,
			  		   'status' => $status,
					   'rating' => $rating,
					   'type' => $story_type,
					   'img' => $img
			  		 ), 
			  	array( 'id' => $id ),
			  	array( '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%s' ),  
				array( '%d' ) );
		}
	}
	
	//Delete	
	function page_delete($id = NULL, $chapter = NULL, $series = NULL) {
	    global $wpdb;
	
	   if(!is_numeric($series))
		$series      = $_GET['series'];
	   if(!is_numeric($chapter))
		$chapter     = $_GET['chapter'];
	   if(!is_numeric($id))
		$id        = $_GET['pg'];
	    
	    if(is_numeric($id) && is_numeric($chapter) && is_numeric($series)) {
		$table = $wpdb->prefix."comic_page";
	  		$select = "DELETE FROM ".$table." WHERE id = '".$id."' AND chapter_id = '".$chapter."' AND series_id = '".$series."'";
		    $wpdb->query($select);
		}
	}
	
	function chapter_delete($chapter_id,$series_id) {
	    global $wpdb;
	    
		if(is_numeric($chapter_id) && is_numeric($series_id)) {
		$table = $wpdb->prefix."comic_chapter";
	  		$select = "DELETE FROM ".$table." WHERE id = '".$chapter_id."' AND series_id = '".$series_id."'";
		    $wpdb->query($select);
	
		$table = $wpdb->prefix."comic_page";
	  		$select = "DELETE FROM ".$table." WHERE chapter_id = '".$chapter_id."' AND series_id = '".$series_id."'";
		    $wpdb->query($select);
	    }
	}
	
	function series_delete($series_id = NULL) {
	    global $wpdb;
	    
	   if(!is_numeric($series_id))
		$series_id      = $_GET['series'];
		
		if(is_numeric($series_id)) {
		$table = $wpdb->prefix."comic_series";
	  		$select = "DELETE FROM ".$table." WHERE id = '".$series_id."'";
		    $wpdb->query($select);
		    
		$table = $wpdb->prefix."comic_chapter";
	  		$select = "DELETE FROM ".$table." WHERE series_id = '".$series_id."'";
		    $wpdb->query($select);
		    
		$table = $wpdb->prefix."comic_page";
	  		$select = "DELETE FROM ".$table." WHERE series_id = '".$series_id."'";
		    $wpdb->query($select);
		}
	
	}

}

?>