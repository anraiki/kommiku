<?php  
if(($isPage)) {
	global $previousPage, $previousLink, $nextPage, $nextLink, $kommiku, $theimage,$naviNextChapter, $naviLastChapter;
	if($kommiku['series_chapter']) {
		foreach ($kommiku['series_chapter'] as $chapterList) { $h++;
			$chapterLists[$h] = $chapterList->slug;
			$chapterListID[$h] = $chapterList->id;
			if($select) {
				$nextChapter = $chapterList->slug;
				$nextChapterID = $chapterList->id;
			}
			unset($select); 
			if($chapterList->slug == $chapter["slug"]) {
				$select = "selected=selected ";
				$chapterSelected = $h;
				$afterChapter = $h+1;
				$beforeChapter = $h-1;
			}
			unset($chapterTitle);
			if ($chapterList->title) $chapterTitle = ' - '.stripslashes($chapterList->title);
			$chapter_items[$chapterList->number] = '<option '.$select.'value="'.$chapterList->slug.'">'.$chapterList->slug.$chapterTitle.'</option>';			
			if($select) {
				$pass = $h-1;
				if(isset($chapterListID[$beforeChapter])) $previousChapter = $chapterLists[$beforeChapter];
				if(isset($chapterListID[$beforeChapter])) $previousChapterID = $chapterListID[$beforeChapter];
			}
		}
		natsort($chapter_items);
		krsort($chapter_items);
		foreach($chapter_items as $chapter_item){
			$kommiku['chapterOption'] .= $chapter_item;
		}
		$chapterOption = $kommiku['chapterOption'];
	}
	
	if($chapter_pages) {
	foreach ($chapter_pages as $pageList) { $i++;
		$pageLists[$i] = $pageList->number;
		if(isset($select)) $nextPage = $pageList->slug;
		unset($select); 
		if($pageList->number == $page["number"]) {
			$select = "selected=selected ";
			$pageSelected = $pageList->number;
		}
		$kommiku['pageSource'][$pageList->number] = $pageList->img;
		$kommiku['pageOption'] .= '<option '.$select.'value="'.$pageList->slug.'">'.$pageList->slug.'</option>';
		$kommiku['pageSlug'][$pageList->number] = $pageList->slug;
		$lastPage = $pageList->number;
		if($select) $previousPage = $pageLists[$i-1];
		}
		$kommiku['pageLists'] = $pageLists;
	}
	
	$pageOption = $kommiku['pageOption'];
	
	if(isset($chapter["number"])){
		$chapter["next"] = $chapter["slug"].'/';
		$chapter["previous"] = $chapter["slug"].'/';
	}
		
	if($lastPage == $pageSelected && $nextChapterID) {
		$number = $wpdb->get_var("SELECT min(number) FROM `".$wpdb->prefix."comic_page` WHERE chapter_id = '".$nextChapterID."'");
		$nextPage = $wpdb->get_var("SELECT min(slug) FROM `".$wpdb->prefix."comic_page` WHERE chapter_id = '".$nextChapterID."' AND number = '".$number."'");
		$chapter["next"] = $nextChapter.'/';
		} else if ($lastPage == $pageSelected) {
			unset($nextPage);	
		}
					
	if(is_null($previousPage) && $previousChapterID) {
		$number = $wpdb->get_var("SELECT max(number) FROM `".$wpdb->prefix."comic_page` WHERE chapter_id = '".$previousChapterID."'");	
		$previousPage = $wpdb->get_var("SELECT slug FROM `".$wpdb->prefix."comic_page` WHERE chapter_id = '".$previousChapterID."' AND number = '".$number."'");	
		$chapter["previous"] = $previousChapter.'/';
	}  
				
	if(KOMMIKU_URL_FORMAT)
		$komUrlDash = KOMMIKU_URL_FORMAT.'/';
		
	$seriesUrl = $series["slug"].'/';
		
	$oneSeries = get_option( 'kommiku_override_index' );
	if($oneSeries)	
		unset($seriesUrl);
		
	if($chapter) {
		if(isset($previousPage)) $previousLink = HTTP_HOST.$komUrlDash.$seriesUrl.$chapter["previous"].$previousPage.'/';
		if(isset($nextPage)) $nextLink = HTTP_HOST.$komUrlDash.$seriesUrl.$chapter["next"].$nextPage.'/';
	} else {
		if(isset($previousPage)) $previousLink = HTTP_HOST.$komUrlDash.$seriesUrl.$previousPage.'/';
		if(isset($nextPage)) $nextLink = HTTP_HOST.$komUrlDash.$seriesUrl.$nextPage.'/';
	}
	if($previousChapter) $naviLastChapter = HTTP_HOST.$komUrlDash.$seriesUrl.$previousChapter.'/';  
	if($nextChapter) $naviNextChapter = HTTP_HOST.$komUrlDash.$seriesUrl.$nextChapter.'/';
			
	function prevPage($link = false,$wrapper = '',$class = NULL,$title = NULL,$chapterNavi = NULL) {
		global $previousPage, $previousLink, $naviLastChapter;
		
		if($chapterNavi == true) $previousLink = $naviLastChapter;
		if($link && isset($previousLink) && $previousLink != "null") {
			if($class) $class = 'class="'.$class.'" '; 
			if($title) {
				$title = 'title="'.$title.'" ';
			} else { 
				$title =  'title="'.__("Read the previous page", 'kommiku').'"';
			}		
			echo '<a '.$class.$title.'href="'.$previousLink.'">'.$wrapper.'</a>';
		} else if($link) {
			echo $wrapper;
		} else {
			echo $previousLink;	
		}
	}

	function checkPrevPage() {
		global $previousPage, $previousLink;

		if(is_null($previousLink)  || strtolower($previousLink) == "null")
			return false;
			
		if(isset($previousLink)) 
			return true;					
	}

	function checkNextPage() {
		global $nextPage, $nextLink;
		
		if(is_null($nextLink) || strtolower($nextLink) == "null")
			return false;
			
		if(isset($nextLink)) 
			return true;
			
	}
			
	function nextPage($link = false,$wrapper = '',$class = NULL,$title = NULL, $chapterNavi = NULL) {
		global $nextPage, $nextLink, $naviNextChapter;
		
           
        if($chapterNavi == true) $nextLink = $naviNextChapter;
		if($link && isset($nextLink) && $nextLink != "null") {
			if($class) $class = 'class="'.$class.'" '; 
			if($title) {
				$title = 'title="'.$title.'" '; 
			} else {
				$title = 'title="'.__("Read the next page", 'kommiku').'"';
			}
			echo '<a '.$class.$title.'href="'.$nextLink.'">'.$wrapper.'</a>';
		} else if($link) {
			echo $wrapper; 
		} else if($nextLink) {
			echo $nextLink;	 
		}
                return;
	}

	function img($echo = true,$class = NULL,$title = NULL,$showall = NULL) {
		global $nextPage, $nextLink, $series, $chapter, $page, $kommiku;
				
		if($chapter["folder"]) {
			$url = '/'.$series["slug"].'/'.$chapter["folder"].'/';	
		} else if($series['chapterless'] != 0) {
			$url = '/'.$series["slug"].'/';
		} else {
			die('Something is wrong here!');
		}
		
		$theimage = UPLOAD_URLPATH.$url.$page["img"];
		$theimage_abs = UPLOAD_FOLDER.$url.$page["img"];
		
		if(file_exists($theimage_abs) && $echo == true) {
			$wrapper = '<img src="'.$theimage.'" />';
		} else {
			return $theimage;	
		}

		if($showall == true) {
			foreach($kommiku['pageLists'] as $pageNumber){
				$chapterImages .= '<img src="'.UPLOAD_URLPATH.$url.$kommiku['pageSource'][$pageNumber].'" />';
			}
			echo ( $chapterImages ); 
			return;
		}  
		
		if(isset($nextLink)) {
			if($class) $class = 'class="'.$class.'" '; 
			if($title) {
				$title = 'title="'.$title.'" '; 
			} else {
				$title = 'title="'.__("Read the next page", 'kommiku').'"';
			}
			echo '<a '.$class.$title.'href="'.$nextLink.'">'.$wrapper.'</a>';
		} else {
			echo $wrapper;
		} 
		
	}
	
}
?>