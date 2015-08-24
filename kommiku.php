<?php
/*
Plugin Name: Kommiku Viewer
Version: 3.0
Plugin URI: http://dotspiral.com/kommiku/
Description: Kommiku is a Online Media Viewer.
Author: Henry Tran
Author URI: http://dotspiral.com/
Text Domain: kommiku
*/ 
define('KOMMIKU_VERSION', '3.0' );

if ( !defined('WP_LOAD_PATH') ) {

	/** classic root path if wp-content and plugins is below wp-config.php */
	$classic_root = dirname(dirname(dirname(dirname(__FILE__)))) . '/' ;
	if (file_exists( $classic_root . 'wp-load.php') )
		define( 'WP_LOAD_PATH', $classic_root);
	else
		if (file_exists( $path . 'wp-load.php') )
			define( 'WP_LOAD_PATH', $path);
		else
			exit("Could not find wp-load.php");
}

$comic_upload_directory = get_option( 'kommiku_comic_upload' );
if(!$comic_upload_directory) $comic_upload_directory = 'comic';
define('KOMMIKU_URLPATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
define('KOMMIKU_PLUGIN_PATH', plugin_basename( dirname(__FILE__) ) . '/' );
define('KOMMIKU_FOLDER', dirname(__FILE__) );
define('UPLOAD_FOLDER',WP_LOAD_PATH.$comic_upload_directory  );
define('UPLOAD_URLPATH',get_bloginfo('wpurl').'/'.$comic_upload_directory );
define('KOMMIKU_ABSPATH', str_replace("\\","/", WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ) . '/' ));
define('KOMMIKU_URL_FORMAT', get_option( 'kommiku_url_format' ));
define('KOMMIKU_SKIN', get_option( 'kommiku_skin_directory' ));
define('KOMMIKU_URL_INDEX', get_option( 'kommiku_url_index' ) );
define('HTTP_HOST', get_bloginfo('url').'/' );
define('K_SCANLATOR_URL', get_option('kommiku_scanlator') );
add_action('admin_menu', 'kommiku_menu');
$kommiku['alphabets'] = array('0-9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

function kommiku_sidebar_category_list() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category;
	
		include KOMMIKU_FOLDER.'/frame/blocks/sidebar_category_list.php';
		return;
}

function kommiku_series_table_list() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category;
	
		include KOMMIKU_FOLDER.'/frame/blocks/series_table_list.php';
		return;
}

function kommiku_series_information() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category;
	
		include KOMMIKU_FOLDER.'/frame/blocks/series_information.php';
		return;
}

function kommiku_chapter_table_list() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category;

		include KOMMIKU_FOLDER.'/frame/blocks/chapter_table_list.php';
		return;
}

function kommiku_page_navigation() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category;

		include KOMMIKU_FOLDER.'/frame/blocks/page_navigation.php';
		return;
}

function kommiku_rating() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category;
	
	if(get_option( 'kommiku_rating' )) {
		include KOMMIKU_FOLDER.'/frame/blocks/rating.php';
	}
	
	return;
}

//Counter System and Function
function counter_extension(){
	global $kommiku, $db, $current_user;
	if(get_option( 'kommiku_counter' )) {
	$visitor_ip_address = $db->visitor_ip();
		if($current_user) {
			$data = $db->counter_read($visitor_ip_address,$kommiku['series_id'],$kommiku['chapter_id'],$kommiku['page_id'],$current_user->ID);
			if(!$data['value']) {
				$db->counter_create($visitor_ip_address,$kommiku['series_id'],$kommiku['chapter_id'],$kommiku['page_id'],$current_user->ID);
			} else {
				$db->view_counter_update($visitor_ip_address,$kommiku['series_id'],$kommiku['chapter_id'],$kommiku['page_id'],$current_user->ID);
			}
		} else {
			$data = $db->counter_read($visitor_ip_address,$kommiku['series_id'],$kommiku['chapter_id'],$kommiku['page_id']);
			if(!$data['value']) {
				$db->counter_create($visitor_ip_address,$kommiku['series_id'],$kommiku['chapter_id'],$kommiku['page_id']);
			} else {
				$db->view_counter_update($visitor_ip_address,$kommiku['series_id'],$kommiku['chapter_id'],$kommiku['page_id']);
			}
		}
	}	
}
	
function kommiku() {
		global $wpdb,$series,$page,$chapter,$db,$status,$kommiku_settings;	
		
		//Auto Updater
		if(KOMMIKU_VERSION != get_option('kommiku_version')){
			kommiku_install();
		}

		if(!is_dir(UPLOAD_FOLDER))
			mkdir(UPLOAD_FOLDER, 0755);
			
		error_reporting(E_ALL ^ E_NOTICE);
		
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
		$wpdb->show_errors();
		$phpdate = date("Y-m-d H:i:s O");
		
		if($_POST['delete'] == "Delete It!") {
						
			if(is_numeric($_POST['pg'])) {
				$page = $db->page_detail(intval($_POST['pg']));	
				$chapter = $db->chapter_detail($page['chapter_id']); 
				if($page['chapter_id'] && $chapter['folder']) {
					error_reporting(0); 
					if(!unlink(UPLOAD_FOLDER.$chapter['folder'].$page['img']) && $page['img'])
						$status['error'] = __("The Image could not be deleted (or it doesn't exist) but the record was deleted (or maybe it was already gone?)", 'kommiku');
					$db->page_delete($_POST['pg'],$page['chapter_id'],$page['series_id']);
					error_reporting(E_ALL ^ E_NOTICE);
				} else {
					$db->page_delete($_POST['pg'],$page['chapter_id'],$page['series_id']);
					if(!unlink(UPLOAD_FOLDER.'/'.$series['slug'].'/'.$page['img']) && $page['img'])
						$status['error'] = __("The Image could not be deleted (or it doesn't exist) but the record was deleted (or maybe it was already gone?)", 'kommiku');
				
				}
				unset($page);
				if($status['error']) $status['error'] .= '<br/>';
					$status['pass'] = __('The Page has been deleted', 'kommiku');
				kommiku_model_page();
			} else if(!is_float($_POST['chapter']) && is_numeric($_POST['chapter']) && !isset($_POST['pg'])) {
				$chapter = $db->chapter_detail(intval($_POST['chapter'])); 
				error_reporting(0); 
				delTree(UPLOAD_FOLDER.$chapter['folder']);
				$db->chapter_delete($chapter['id'],$chapter['series_id']);
				error_reporting(E_ALL ^ E_NOTICE);
				unset($chapter);
				if(!$status['error']) {
					$status['pass'] = __('The Chapter has been deleted', 'kommiku');
					kommiku_model_chapter();
				} else {				
					kommiku_model_page();
				}	
			} else if(!is_float($_POST['series']) && is_numeric($_POST['series']) && !isset($_POST['chapter']) &&!isset($_POST['pg'])) {
				$series = $db->series_detail(intval($_POST['series']));
				$series['folder'] = '/'.strtolower($series['slug']).'/';
				//error_reporting(0); 
				delTree(UPLOAD_FOLDER.$series['folder']);
				$db->series_delete($series['id']);
				//error_reporting(E_ALL ^ E_NOTICE);
				unset($series);
				if(!$status['error']) {
					$status['pass'] = __('The Series has been deleted', 'kommiku');
					kommiku_model_series();
				} else				
					kommiku_model_chapter();
			}	
			
		} else if($_POST['action']) {
		$_CLEAN['title']          = $_POST['title'];
		$_CLEAN['name']           = $_POST['name'];
		$_CLEAN['slug']           = urlencode($_POST['slug']);
		$_CLEAN['summary']        = $_POST['summary'];
		$_CLEAN['description']    = $_POST['description'];
		$_CLEAN['number']         = strval(intval($_POST['number']));
		$_CLEAN['series_id']      = strval(intval($_POST['series_id']));
		$_CLEAN['chapter_id']     = $_POST['chapter_id'];
		$_CLEAN['seodescription'] = $_POST['seodescription'];
		$_CLEAN['seokeyword']     = $_POST['seokeyword'];
		$_CLEAN['story']          = $_POST['story'];
		$_CLEAN['scanlator']	  = $_POST['scanlator'];
		$_CLEAN['author']         = $_POST['author'];
		$_CLEAN['illustrator']	  = $_POST['illustrator'];
		$_CLEAN['link'] 		  = $_POST['link'];
		$_CLEAN['creation'] 	  = $_POST['creation'];
		$_CLEAN['alt_name'] 	  = $_POST['alt_name'];
		$_CLEAN['story_type'] 	  = $_POST['story_type'];
		$_CLEAN['text'] 		  = $_POST['text'];
		$_CLEAN['id']             = intval($_POST['id']);
			
			if($_POST['what'] == "scanlator") { 
				$table = $wpdb->prefix."comic_scanlator";
				$oldScanlator = $db->scanlator_detail($_POST['scanlator_id']);
				
				if($wpdb->get_var("SELECT title FROM `".$table."` WHERE title = '".$_CLEAN['title']."'") == $_CLEAN['title'])  
					if ($_POST['action'] == "update" && $oldScanlator['title'] != $_CLEAN['title'])
						$scanlator['fail']['title'] = true;
					
				if($wpdb->get_var("SELECT slug FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."'") == $_CLEAN['slug']) 
					if ($_POST['action'] == "update" && $oldScanlator['slug'] != $_CLEAN['slug'])
						$scanlator['fail']['slug'] = true;
						
				if(!$scanlator['fail']) {
						$db->scanlator_update($_POST['scanlator_id'],$_POST['title'],$_CLEAN['slug'],stripslashes($_CLEAN['summary']));
						$status['pass'] = 'The scanlator has been updated';
					
					kommiku_scanlator_edit();
				} else {
					if ($scanlator['fail']['title']) $status['error'] .= __('The scanlator name has already been taken.<br/>', 'kommiku');
					if ($scanlator['fail']['slug']) $status['error'] .= __('The scanlator slug has already been taken.<br/>', 'kommiku');
					$scanlator['title'] = $_POST['title'];
					$scanlator['slug'] = $_CLEAN['slug'];
					$scanlator['text'] = stripslashes($_POST['text']);
					$scanlator['link'] = stripslashes($_POST['link']);
					kommiku_scanlator_edit();
				}
			}
			
			if($_POST['what'] == "category") { 
				$table = $wpdb->prefix."comic_category";
				$old = $db->category_detail($_CLEAN['slug']);
				
				if(!$_CLEAN['title']) {
					$category['fail']['name']['empty'] = true;
				}
				
				if($wpdb->get_var("SELECT title FROM `".$table."` WHERE title = '".$_CLEAN['title']."'") == $_CLEAN['title'])  
					if ($_POST['action'] == "update" && $old['title'] != $_CLEAN['title'])
						$category['fail']['name'] = true;
					
				if($wpdb->get_var("SELECT slug FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."'") == $_CLEAN['slug']) 
					if ($_POST['action'] == "update" && $old['slug'] != $_CLEAN['slug'])
						$category['fail']['slug'] = true;
						
					
						
				if(!$category['fail']) {
					$db->category_update($_CLEAN['id'],$_CLEAN['title'],stripslashes($_CLEAN['summary']),$_CLEAN['slug']);
					$status['pass'] = 'The Category has been updated';
					$category['title'] = $_POST['title'];
					$category['summary'] = $_POST['summary'];
				} else {
					if ($category['fail']['name']) $status['error'] .= __('The category name has already been taken.<br/>', 'kommiku');
					if ($category['fail']['slug']) $status['error'] .= __('The category slug has already been taken.<br/>', 'kommiku');
					if ($category['fail']['name']['empty']) $status['error'] .= __('The category title can not be empty<br/>', 'kommiku');
					$category['name'] = $_POST['name'];
					$category['slug'] = $_POST['slug'];
					$category['description'] = stripslashes($_POST['description']);
					
				}
					kommiku_category_edit();
			}
			
			if($_POST['what'] == "series") { 
			
				//Updating? Set the $_OLD Vars!
				if($_POST['action'] == "update" && is_numeric($_CLEAN['series_id'])) {
					$_OLD = $db->series_detail($_CLEAN['series_id']);
					if($_OLD['slug'] != $_CLEAN['slug'])
						$rename = true;
				}
				
				$table = $wpdb->prefix."comic_series";
				if($wpdb->get_var("SELECT title FROM `".$table."` WHERE title = '".$_CLEAN['title']."'") == $_CLEAN['title'])  
					if (($_POST['action'] != 'update') || ($_POST['action'] == "update" && $_OLD['title'] != $_CLEAN['title']))
						$series['fail']['title'] = true;
					
				if($wpdb->get_var("SELECT slug FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."'") == $_CLEAN['slug']) 
					if (($_POST['action'] != 'update') || ($_POST['action'] == "update" && $_OLD['slug'] != $_CLEAN['slug']))
						$series['fail']['slug'] = true;
									
				$seriesFolder .= '/'.$_CLEAN["slug"].'/';
				
				if($_POST['chapterless'] == 1) {
					$chapterless = 1;
				}
				
				$series['title'] = $_POST['title'];
				$series['slug'] = urlencode($_POST['slug']);
				$series['summary'] = stripslashes($_POST['summary']);
				$series['chapterless'] = $chapterless;
				$series['author'] = $_POST['author'];
				$series['illustrator'] = $_POST['illustrator'];
				
				if(!$series['fail']['slug'] && !$series['fail']['title']) {
				
					if(is_numeric($_POST['type']))
						$story_type = $_POST['type'];
					else
						$story_type = 0;
					
					if((!empty($_FILES["img"])) && ($_FILES['img']['error'] == 0)) {
						//Check if the file is JPEG image and it's size is less than 350Kb
						$basefilename = basename($_FILES['img']['name']);
						$ext = substr($basefilename, strrpos($basefilename, '.') + 1);
						$filename = 'icon.'.$ext;
						
						if ((strtolower($ext) == "jpeg") || 
							(strtolower($ext) == "jpg") || 
							(strtolower($ext) == "png") || 
							(strtolower($ext) == "gif") && 
							($_FILES["img"]["size"] < 2048000)) 
						{
							//Determine the path to which we want to save this file
								$newname = UPLOAD_FOLDER.$seriesFolder.$filename;
							//Go Ahead and Move :D	
								$_CLEAN['img'] = $filename;
								$series['img'] = $_CLEAN['img'];

						} else {
							$_CLEAN['img'] = $series['img'];
						}
			   		
					} else {

						//No File Uploaded
						if (!$_POST['action'])  
							$page['fail']['nofile'] = true;
						 else 
							$_CLEAN['img'] = $series['img'];
					}
					
					if($newname){
						if(!is_dir(UPLOAD_FOLDER))
							mkdir(UPLOAD_FOLDER, 0755);
							
						if(!is_dir(UPLOAD_FOLDER.strtolower($seriesFolder)))
							mkdir(UPLOAD_FOLDER.strtolower($seriesFolder), 0755);
							
						move_uploaded_file($_FILES['img']['tmp_name'],$newname);	
					}
						
					if($_POST['action'] == "create") {
						if(!is_dir(UPLOAD_FOLDER.'/'.$_CLEAN['slug'])) {
							if(mkdir(UPLOAD_FOLDER.'/'.$_CLEAN['slug'], 0755)) {
								$db->series_create($_CLEAN['title'],$_CLEAN['slug'],$_POST['summary'],$chapterless,$categories,$author,$illustrator,$read,$creation,$alt_name,$status,$rating,$story_type,$_CLEAN['img']);
								$status['pass'] = __('The Series has been successfully created', 'kommiku');
							} else {
								$status['error'] = __('The Series directory could not be created', 'kommiku');
							}
						} else {
							$status['error'] = __('The Series directory already exist. Try deleting it through FTP then try creating again.', 'kommiku');
						}
						kommiku_model_series();
					} else if($_POST['action'] == "update" && is_numeric($_CLEAN['series_id'])) {
						if($rename) {
							if(rename(UPLOAD_FOLDER.'/'.$_OLD['slug'].'/', UPLOAD_FOLDER.'/'.$_CLEAN['slug'].'/')){
								$tableB = 'comic_chapter';
								if($_CLEAN['series_id']) {
									$chapters = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix.$tableB."` WHERE `series_id` = '".$_CLEAN['series_id']."'");
									if($chapters) {
										foreach($chapters as $chapter) {
											if($chapter->slug && $series['slug']) {
												$query = "UPDATE `".$wpdb->prefix.$tableB."` SET `folder` = '/".$_CLEAN['slug']."/".$chapter->slug."/' WHERE `id` = '".$chapter->id."'";
												$wpdb->query($query);
											}
											unset($query);
										}
									}
								}
								$status['pass'] .= __('The Series has been renamed.<br/>', 'kommiku');
								
							} else {
								$status['error'] .= __('The series could not be renamed: <br/>', 'kommiku').'<br/>From: '.UPLOAD_FOLDER.'/'.$_OLD['slug'].'/<br/>To '.UPLOAD_FOLDER.'/'.$_CLEAN['slug'].'/';
							}
						}
						$chapterless = $wpdb->get_var("SELECT chapterless FROM `".$wpdb->prefix."comic_series` WHERE id = '".$_CLEAN['series_id']."'");
						$db->series_update($_CLEAN['series_id'],$_CLEAN['title'],$_CLEAN['slug'],stripslashes($_CLEAN['summary']),$chapterless,$_POST['categories'],$_CLEAN['author'],$_CLEAN['illustrator'],$_POST['read'],$_CLEAN['creation'],$_CLEAN['alt_name'],$_POST['status'],$_POST['mature'],$story_type,$_CLEAN['img']);
						$status['pass'] .= __('The Series has been updated.<br/>', 'kommiku');						

						if($_POST['destination'] == 'chapter')
							kommiku_model_chapter();
						else if($_POST['destination'] == 'page')
							kommiku_model_page();
						else
							kommiku_model_series();
					}
				} else {
					if ($series['fail']['title']) $status['error'] .= __('The series name has already been taken.<br/>', 'kommiku');
					if ($series['fail']['slug']) $status['error'] .= __('The series slug has already been taken.<br/>', 'kommiku');
					
					if(!$_POST['chapter'] && $_POST['action']) {
						if($_POST['action'] == 'create' && $status['error'])
							kommiku_model_series();
						else
							kommiku_model_chapter();
					} else if ($_POST['action'])
						kommiku_model_page();
					else
						kommiku_model_series();
				}
					
			} 

			//
			//Create a New Chapter
			//

			//Push
			if(is_numeric($_POST['series_id']) && $_POST['what'] == "chapter" && ($_POST['action'] == "push")) { 
				$table = $wpdb->prefix."comic_chapter";
				
				$series = $db->series_detail($_POST['series_id']);
				$chapter = $db->chapter_detail($_POST['chapter_id']);
					
				if(is_numeric($_POST['push']) && is_numeric($_POST['chapter_id'])) {
					$table = $wpdb->prefix."comic_chapter";
				    $select = "UPDATE $table SET number = number + 1 WHERE number >= '".$_POST['push']."' AND series_id = '".$series['id']."'";
				    $wpdb->query($select);
				    $select = "UPDATE $table SET number = '".$_POST['push']."' WHERE id = '".$_POST['chapter_id']."' AND series_id = '".$series['id']."'";
				    $wpdb->query($select);
				}
				
				if($_POST['destination'] == "chapter") 
					kommiku_model_chapter();
				else
					kommiku_model_page();
				
				return;
			}
			
			//push
			if(is_numeric($_POST['series_id']) && $_POST['what'] == "chapter" && ($_POST['action'] == "pushnat")) { 
				$table = $wpdb->prefix."comic_chapter";
				
				$series = $db->series_detail($_POST['series_id']);
				$chapter = $db->chapter_detail($_POST['chapter_id']);
				if($chapter['id'] && !$Pchapter) {
					$table = $wpdb->prefix."comic_chapter";
				    $select = "UPDATE ".$table." SET number = number + 1 WHERE `number` >= ".$chapter['number']." AND `series_id` = '".$chapter['series_id']."'";
				    $wpdb->query($select);
					$status['pass'] = "Chapter Pushed Success";
				} else {
					$status['error'] = "Can't Pushed This Chapter because there is a chapter behind it.";
				}
				
				if($_POST['destination'] == "chapter") 
					kommiku_model_chapter();
				else
					kommiku_model_page();
					
				return;
			}
			
			//Pull
			if(is_numeric($_POST['series_id']) && $_POST['what'] == "chapter" && ($_POST['action'] == "pull")) { 
				$table = $wpdb->prefix."comic_chapter";
				
				$series = $db->series_detail($_POST['series_id']);
				$chapter = $db->chapter_detail($_POST['chapter_id']);
				$chapterNumberMinus = $chapter['number']-1;
				$Pchapter = $wpdb->get_var("SELECT `number` FROM `".$table."` WHERE number = ".$chapterNumberMinus." AND series_id = '".$chapter['series_id']."'");
				if($chapter['id'] && !$Pchapter) {
					$table = $wpdb->prefix."comic_chapter";
				    $select = "UPDATE ".$table." SET number = number - 1 WHERE `number` >= ".$chapter['number']." AND `series_id` = '".$chapter['series_id']."'";
				    $wpdb->query($select);
					$status['pass'] = "Chapter Pulled Success";
				} else {
					$status['error'] = "Can't Pull This Chapter because there is a chapter behind it.";
				}
				
				if($_POST['destination'] == "chapter") 
					kommiku_model_chapter();
				else
					kommiku_model_page();
					
				return;
			}
			
			if($_CLEAN['series_id'] && $_POST['what'] == "chapter" && $_POST['action']) {
		
					
				if($_POST['action'] == "create" || $_POST['action'] == "update") {
					$table = $wpdb->prefix."comic_chapter";
					
					$series = $db->series_detail($_CLEAN['series_id']);
					$chapter = $db->chapter_detail(intval($_POST['chapter_id']));
					$_OLD = $chapter;
					
					if($wpdb->get_var("SELECT number FROM ".$table." WHERE number = '".$_CLEAN['number']."' AND series_id = '".$_CLEAN['series_id']."'") == $_CLEAN['number'])  
						if (($_POST['action'] != 'update') || ($_POST['action'] == "update" && $_OLD['number'] != $_CLEAN['number']))
							$chapter['fail']['number']['duplicate'] = true;
					
					if($wpdb->get_var("SELECT slug FROM ".$table." WHERE slug = '".$_CLEAN['slug']."' AND series_id = '".$_CLEAN['series_id']."'") == $_CLEAN['slug'])  
						if (($_POST['action'] != 'update') || ($_POST['action'] == "update" && $_OLD['slug'] != $_CLEAN['slug']))
							$chapter['fail']['slug']['duplicate'] = true;
							
					if (!is_numeric($_POST['number'])) 
						$chapter['fail']['number']['character'] = true;
					
					if (!$_POST['slug']) 
						$chapter['fail']['number']['slug'] = true;
						
					if (!is_numeric($_POST['volume']) && isset($_POST['volume'])) 
						$chapter['fail']['volume'] = true;

					$folder = '/'.$series['slug'].'/'.urlencode($_POST['slug']).'/';
					if($_POST['action'] == "update" && is_numeric(intval($_POST['chapter_id']))) {
						$_OLD['folder'] = $chapter['folder'] ;
						if($_OLD['folder'] != $folder)
							$noRename = true;
					}		
						
						
					if(!$chapter['fail']) {
					
						if($_POST['action'] == "create") {
							$db->chapter_create($_CLEAN['title'],$_POST['number'],$_CLEAN['summary'],$_CLEAN['series_id'],$phpdate,sanitize_title($_POST['slug']),$scanlator,$scanlator_slug,0,$folder,false);
							if(mkdir(UPLOAD_FOLDER.$folder, 0755)) {
								$status['pass'] = __('The Chapter has been successfully created', 'kommiku');
							} else {
								$status['pass'] = __('The Chapter "Folder" seems to have already exist but the Chapter was still Created.', 'kommiku');
							} 
						} else if($_POST['action'] == "update" && is_numeric($_POST['chapter_id'])) {						
							$db->chapter_update($_POST['chapter_id'],$_CLEAN['title'],$_POST['number'],$_CLEAN['summary'],$_CLEAN['series_id'],$chapter['pubdate'],$_POST['slug'],$_POST['scanlator'],$_POST['scanlator_slug'],$_POST['volume'],$folder);
							$status['pass'] = __('The Chapter has been successfully updated');

							if($noRename)
								rename(UPLOAD_FOLDER.$_OLD['folder'], UPLOAD_FOLDER.$folder);
						}
					} else {
						if ($chapter['fail']['number']['duplicate']) $status['error'] .= __('The Chapter number has already been taken.<br/>', 'kommiku');
						if ($chapter['fail']['number']['character']) $status['error'] .= __('The Chapter number has to be in decimals or numbers.<br/>', 'kommiku');
						if($chapter['fail']['number'])  $status['error'] .= __('The "Volume Input" must be Numeric', 'kommiku');
					}
				}
				
					$chapter['scanlator'] = $_POST['scanlator'];
					$chapter['scanlator_slug'] = $_POST['scanlator_slug'];
					$chapter['volume'] = $_POST['volume'];
					$chapter['slug'] = $_POST['slug'];
					$chapter['number'] = $_POST['number'];
					$chapter['title'] = $_POST['title'];
					$chapter['slug'] = $_POST['slug'];
					$chapter['number'] = $_POST['number'];
					$chapter['summary'] = $_POST['summary'];
				
				if($_POST['destination'] == "page")
					kommiku_model_page();
				else
					kommiku_model_chapter();
			}

			
			
	
			//
			//Create a New Page
			//
			
			if($_POST['what'] == "page" && $_CLEAN['series_id']) { 

				$table = $wpdb->prefix."comic_page";
					$page['title']           = $_POST['title'];
					$page['number']          = $_POST['number'];
					$page['story']           = $_POST['story'];
					$page['series_id']       = $_CLEAN['series_id'];
					$page['chapter_id']      = $_POST['chapter_id'];
					$page['slug']      		 = $_POST['slug'];
					
				if (is_numeric($_POST['page_id'])) {
					$oldPage = $db->page_detail($_POST['page_id']);
					$page['id'] = $oldPage['id'];
					$page['img'] = $oldPage['img'];
					if(!$oldPage) {
						die('Oh no 404!');	
					}
				}
					
				$series = $db->series_detail($_CLEAN['series_id']);

				if(is_numeric($_POST['chapter_id'])) {
					$chapter = $db->chapter_detail(intval($_POST['chapter_id']));
				}
				
				if(isset($chapter['folder']) && $chapter['folder'] != '')
					$url = $chapter['folder'];
				else if($series['chapterless'] == 1) {
					$url = '/'.$series['slug'].'/';
				} else {
					$page['fail']['folder'] = true;
				}
					
				if(!is_dir(UPLOAD_FOLDER.$url))
					mkdir(UPLOAD_FOLDER.$url, 0755);
					
				//Check slug //doube check for UPDATE
				if($wpdb->get_var("SELECT slug FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."' AND series_id = '".$_CLEAN['series_id']."' AND chapter_id = '".$_POST['chapter_id']."'") == $_CLEAN['slug'])  
					if (($_POST['action'] && !$oldPage) || ($_POST['action'] == "update" && $oldPage['slug'] != $_CLEAN['slug']))
						$page['fail']['slug'] = true;

				//If Updating Check Number
				if($wpdb->get_var("SELECT number FROM `".$table."` WHERE number = '".$_CLEAN['number']."' AND series_id = '".$_CLEAN['series_id']."' AND chapter_id = '".$_POST['chapter_id']."'") == $_CLEAN['number'])  
					if (($_POST['action'] && !$oldPage) || ($_POST['action'] == "update" && $oldPage['number'] != $_CLEAN['number']))
						$page['fail']['number']['duplicate'] = true;
							
				if(!is_numeric($page['number']))
						$page['fail']['number']['character'] = true;	
						 
				//Check that we have a file
				if((!empty($_FILES["img"])) && ($_FILES['img']['error'] == 0)) {
					//Check if the file is JPEG image and it's size is less than 350Kb
					$basefilename = basename($_FILES['img']['name']);
					$ext = substr($basefilename, strrpos($basefilename, '.') + 1);
					$filename = $_CLEAN['slug'].'.'.$ext;
					
					if ((strtolower($ext) == "jpeg") || 
						(strtolower($ext) == "jpg") || 
				        (strtolower($ext) == "png") || 
				        (strtolower($ext) == "gif") && 
				        ($_FILES["img"]["size"] < 2048000)) {
						//Determine the path to which we want to save this file
				   			$newname = UPLOAD_FOLDER.$url.$filename;
				   		//Go Ahead and Move :D	
				   			$_CLEAN['img'] = $filename;
			   		} else {
			   			//More than 2MB?
			   			$page['error']['toolarge'] = true; 
			   		}
			   		
			   	} else {
			   		//No File Uploaded
			   		if (!$_POST['action'])  
				   		$page['fail']['nofile'] = true;
			   		 else 
				   		$_CLEAN['img'] = $oldPage['img'];
			   	}
			if(!$page['fail']) {
				$page['pubdate'] = date("Y-m-d H:i:s O");
				if($chapter['number']) $chapterHistory = ' Chapter '.$chapter['number'].' -';
				if ($_POST['action'] == "Publish") {
						if($newname){
							if(move_uploaded_file($_FILES['img']['tmp_name'],$newname)) {								
								$table = $wpdb->prefix."comic_page";
								$db->page_create($_CLEAN['title'],$_CLEAN['slug'],$_CLEAN['img'],$page['pubdate'],$_POST['story'],$_POST['number'],$page['series_id'],$_POST['chapter_id'],'');
								$table = $wpdb->prefix."comic_page";
								$page['id'] = $wpdb->get_var("SELECT id FROM `".$table."` WHERE number = '".$_POST['number']."' AND series_id = '".$_CLEAN['series_id']."' AND chapter_id = '".$_POST['chapter_id']."'");								
								
								if ($handle = opendir(UPLOAD_FOLDER.$seriesFolder.$chapterFolder)) {
									while (false !== ($file = readdir($handle))) {
										if ($file != "." && $file != "..") {
											$status['pass'] = __('The page was successfully created.');
										}
									}
									closedir($handle);
								}
								
							} else {
								$status['pass'] = __('Error 1: Could not move file', 'kommiku').' - '.UPLOAD_FOLDER.$url.$filename;
							}
						} else {
							$status['pass'] = __('No file were uploaded. But a page was created.', 'kommiku');
							$table = $wpdb->prefix."comic_page";
							$db->page_create($_CLEAN['title'],$_CLEAN['slug'],$_CLEAN['img'],$page['pubdate'],$_POST['story'],$_POST['number'],$page['series_id'],$_POST['chapter_id'],'');
							$table = $wpdb->prefix."comic_page";
							$page['id'] = $wpdb->get_var("SELECT id FROM `".$table."` WHERE number = '".$_POST['number']."' AND series_id = '".$_CLEAN['series_id']."' AND chapter_id = '".$_POST['chapter_id']."'");								
						}
					} else if (is_numeric($_POST['page_id']) && strtolower($_POST['action']) == "update") {
						//Uploaded a File? Delete The Last File!
						$status['pass'] = __('The Image/Page has been updated', 'kommiku');
						if ($newname) {
							error_reporting(E_ALL ^ E_WARNING); 
							if(!unlink(UPLOAD_FOLDER.$url.$oldPage['img']))
								$status['pass'] .= "<br/>There were no file name ".$oldPage['img']." to Delete";
							error_reporting(E_ALL ^ E_NOTICE); 
							if(move_uploaded_file($_FILES['img']['tmp_name'],$newname))
								$status['pass'] .= __(' | Image moved!', 'kommiku');
							else
								$status['pass'] .= __(' | Image Failed!', 'kommiku');
						}

						if($wpdb->get_var("SELECT * FROM `".$wpdb->prefix."posts` WHERE post_status = 'publish' AND post_name = '".$_POST['wp_post_slug']."'")) {
							$wp_post_slug = $_POST['wp_post_slug'];
						} else if($_POST['wp_post_slug'] != '') {
							$wp_post_slug = '';
							$status['error'] .= __('<br/>No such Wordpress Post', 'kommiku');
						}

						$db->page_update($oldPage['id'],$_CLEAN['title'],$_CLEAN['slug'],$_CLEAN['img'],$page['pubdate'],$_CLEAN['story'],$_CLEAN['number'],$page['series_id'],$page['chapter_id'],$wp_post_slug);
					
					}
					kommiku_model_createpage();
				} else {
					if ($page['fail']['number']['duplicate']) $status['error'] .= __('The Page number has already been taken.<br/>', 'kommiku');
					if ($page['fail']['number']['character']) $status['error'] .= __('The Page number has to be in decimals or numbers.<br/>', 'kommiku');
					if ($page['fail']['slug']) $status['error'] .= __('The Slug for the Page on this Chapter has already been taken', 'kommiku');
					if ($page['fail']['nofile']) $status['error'] .= __("There was no file to upload", 'kommiku');
					if ($page['fail']['toolarge']) $status['error'] .= __("The file is too large", 'kommiku');
					if ($page['fail']['exist']) $status['error'] .= __("The file couldn't be moved!? Please check permission on your folders", 'kommiku');
					kommiku_model_createpage();
				}
			}	
				
		} else {
			
			if(!$_GET['sub'] && !$_POST['action']) {
				kommiku_model_series();
			}
				
			if($_GET['sub'] == 'delete' && is_numeric($_GET['series'])) {
				kommiku_model_delete();
			}
				
			if($_GET['sub'] == "scanlator_edit")
				kommiku_scanlator_edit();
				
			if($_GET['sub'] == "listchapter" && is_numeric($_GET['series'])) 
				kommiku_model_chapter();
				
			if($_GET['sub'] == "listpage" && (is_numeric($_GET['series']) || (is_numeric($_GET['series']) && is_numeric($_GET['chapter']))))
				kommiku_model_page();
				
			if($_GET['sub'] == "edit" && is_numeric($_GET['series']) && is_numeric($_GET['chapter']) && is_numeric($_GET['pg']))
				kommiku_model_page();
				
			if($_GET['sub'] == "createpage" && is_numeric($_GET['series']))
				kommiku_model_createpage();

			if($_GET['sub'] == "category_edit")
				kommiku_category_edit();
				
	}
}

add_action( 'widgets_init', 'load_widgets' );

function load_widgets() {
	register_widget( 'story_lister' );
	register_widget( 'chapter_lister' );
}



class story_lister extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function story_lister() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'kstory-lister', 'description' => __('A widget that lists the Stories under the Kommiku plugin.', 'Kommiku: Story Lister', 'kommiku') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'kommiku-story-lister-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'kommiku-story-lister-widget', __('Kommiku: Story Lister', 'Kommiku: Story Lister', 'kommiku'), $widget_ops, $control_ops );
	}
	

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
		
		$series_list = $db->series_list();
		if($series_list)
			foreach ($series_list as $row) {
				$seriesOption .= '<li><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->slug.'/">'.stripslashes($row->title).'</a></li>';
			};	
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		//Grab Series
		if($seriesOption) {
			echo "<ul>";
			echo $seriesOption;
			echo "</ul>";
			}
			
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Example', 'example', 'kommiku'), 'name' => __('John Doe', 'example', 'kommiku'), 'sex' => 'male', 'show_sex' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid', 'kommiku'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

	<?php
	}
}


class chapter_lister extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function chapter_lister() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'kchapter-lister', 'description' => __("A widget that lists the Story's Chapter under the Kommiku plugin.", 'Kommiku: Chapter Lister', 'kommiku') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'kommiku-chapter-lister-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'kommiku-chapter-lister-widget', __('Kommiku: Chapter Lister', 'Kommiku: Chapter Lister', 'kommiku'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
		
		$upnum = $instance['upnum'];
		
		$chapter_list = $db->chapter_hupdate($upnum);
		if($chapter_list)
			foreach ($chapter_list as $row) {
				$date = date( 'm/d',  strtotime($row->date) );
				$kommiku['chapter_list'] .= '<li><small>['.$date.']</small> <a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->series_slug.'/'.$row->chapter_slug.'">'.$row->series_title.' '.$row->chapter_slug.'</a></li>';
			};	
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		//Grab Series
		if($kommiku['chapter_list']) {
			echo "<ul>";
			echo $kommiku['chapter_list'];
			echo "</ul>";
			}
			
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['upnum'] = strip_tags( $new_instance['upnum'] );
		
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Kommiku Chapter Updates', 'Kommiku Chapter Updates', 'kommiku'), 'upnum' => 30 );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'kommiku'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'upnum' ); ?>"><?php _e('How many updates to show?', 'kommiku'); ?></label>
			<input id="<?php echo $this->get_field_id( 'upnum' ); ?>" name="<?php echo $this->get_field_name( 'upnum' ); ?>" value="<?php echo $instance['upnum']; ?>" style="width:100%;" />
		</p>
	<?php
	}
}

function kommiku_model_series() {
	global $kommiku,$series,$db,$status;	
	include KOMMIKU_FOLDER.'/admin/list_series.php';
	}
	
function kommiku_model_delete() {
	global $series,$page,$chapter,$db,$status;		
	include KOMMIKU_FOLDER.'/admin/delete.php';
	}
	
function kommiku_model_chapter() {
	global $kommiku,$series,$chapter,$db,$status;
        $kommiku['tableless_page'] = get_option( 'tableless_page' );
	include KOMMIKU_FOLDER.'/admin/list_chapter.php';
	}
	
function kommiku_model_page() {
	global $series,$page,$chapter,$db,$status;		
	include KOMMIKU_FOLDER.'/admin/list_page.php';
	}
	
function kommiku_category_edit() {
	global $db, $category, $status;
	include KOMMIKU_FOLDER.'/admin/category_edit.php';
	}	
	
function kommiku_scanlator_edit() {
	global $db, $scanlator, $status;
	include KOMMIKU_FOLDER.'/extension/scanlator/scanlator_edit.php';
	}
	
function kommiku_category() {
	global $kommiku,$kommiku_settings,$status,$wpdb;
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
	
	include KOMMIKU_FOLDER.'/admin/category.php';
	}
	
function kommiku_model_createpage() {
	global $db, $page, $status;
	include KOMMIKU_FOLDER.'/admin/list_page_create.php';
	}

function kommiku_scanlator() {
	global $kommiku;
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
		include KOMMIKU_FOLDER.'/extension/scanlator/scanlator.php';
	}
	
function kommiku_settings() {
	global $kommiku_settings,$status,$wpdb;
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
			
		//Auto Updater
		if(KOMMIKU_VERSION != get_option('kommiku_version')){
			kommiku_install();
		}
		
		if ($_POST['what'] == "settings" && $_POST['action'] == "update") {
			$kommiku_settings['one_comic'] = $db->clean($_POST['one_comic']);
			$kommiku_settings['url'] = $db->clean($_POST['url']);
			$kommiku_settings['upload'] = $db->clean($_POST['upload']);
			$kommiku_settings['skin'] = $db->clean($_POST['skin']);
			
			if($_POST['scanlator_url']){
				update_option('kommiku_scanlator', $_POST['scanlator_url']);
				$kommiku_settings['scanlator_url'] = $_POST['scanlator_url'];
			} else {
				update_option('kommiku_scanlator', 'author');
				$kommiku_settings['scanlator_url'] = 'author';
			}
			
			if($_POST['scanlator_enable'] == 1){
				if (!$wpdb->query("Show columns from `".$wpdb->prefix."comic_scanaltor` like 'title'")) {
					$structure = "CREATE TABLE `".$wpdb->prefix."comic_scanlator` (
					`id` INT NOT NULL AUTO_INCREMENT ,
					`title` VARCHAR( 32 ) NOT NULL ,
					`slug` VARCHAR( 32 ) NOT NULL ,
					`text` TEXT NOT NULL,
					UNIQUE KEY id (id)
					) ;";
					$wpdb->query($structure);
				}
				update_option('kommiku_scanlator_enabled', true);
				$kommiku_settings['scanlator_enable'] = $_POST['scanlator_enable'];
			} else {
				update_option('kommiku_scanlator_enabled', false);
				$kommiku_settings['scanlator_enable'] = false;
			}
			
			if($_POST['counter_enable'] == 1){
				update_option('kommiku_counter', true);
				$kommiku_settings['counter_enable'] = $_POST['counter_enable'];
			} else {
				update_option('kommiku_counter', false);
				$kommiku_settings['counter_enable'] = false;
			}
			
			if($_POST['tableless_page'] == 1){
				update_option('tableless_page', true);
				$kommiku_settings['tableless_page'] = $_POST['tableless_page'];
			} else {
				update_option('tableless_page', false);
				$kommiku_settings['tableless_page'] = false;
			}
                        
			if($_POST['rating_enable'] == 1){
				update_option('kommiku_rating', true);
				$kommiku_settings['rating_enable'] = $_POST['rating_enable'];
			} else {
				update_option('kommiku_rating', false);
				$kommiku_settings['rating_enable'] = false;
			}
			
			if($_POST['search_enable'] == 1){
				update_option('kommiku_search_enable', true);
				$kommiku_settings['search_enable'] = $_POST['search_enable'];
			} else {
				update_option('kommiku_search_enable', false);
				$kommiku_settings['search_enable'] = false;
			}
			
			if($_POST['feed_enable'] == 1){
				update_option('kommiku_feed_enable', true);
				$kommiku_settings['feed_enable'] = $_POST['feed_enable'];
			} else {
				update_option('kommiku_feed_enable', false);
				$kommiku_settings['feed_enable'] = false;
			}
			
			if($_POST['directory']){
				update_option('kommiku_url_index', urlencode($_POST['directory']));
				$kommiku_settings['directory'] = $_POST['directory'];
			} else {
				if(!get_option( 'kommiku_url_index' ))
				add_option("kommiku_url_index", 'directory');
			
				update_option('kommiku_url_index', 'directory');
				$kommiku_settings['directory'] = 'directory';
			}
			
			if($_POST['search']){
				update_option('kommiku_url_search', urlencode($_POST['search']));
				$kommiku_settings['search'] = $_POST['search'];
			} else {
				if(!get_option( 'kommiku_url_search' ))
				add_option("kommiku_url_search", 'search');
			
				update_option('kommiku_url_search', 'find');
				$kommiku_settings['search'] = 'find';
			}
			
			if($_POST['feed']){
				update_option('kommiku_url_feed', urlencode($_POST['feed']));
				$kommiku_settings['feed'] = $_POST['feed'];
			} else {
				if(!get_option( 'kommiku_url_feed' ))
					add_option("kommiku_url_feed", 'feed');
				
				update_option('kommiku_url_feed', 'rss');
				$kommiku_settings['feed'] = 'rss';
			}
			
			if($_POST['override_index'] == 1){
				update_option('kommiku_override_index', true);
				$kommiku_settings['kommiku_override_index'] = true;
			} else {
				delete_option('kommiku_override_index');
				$kommiku_settings['kommiku_override_index'] = false;
			}
			
			if($_POST['desensitise'] == 1){
				update_option('kommiku_desensitise', true);
				$kommiku_settings['desensitise'] = true;
			} else {
				delete_option('kommiku_desensitise');
				$kommiku_settings['desensitise'] = false;
			}
			
			if(!get_option( 'kommiku_skin_directory' ))
				add_option("kommiku_skin_directory", 'default');
			
			if($_POST['url'] == "")
				update_option('kommiku_no_slug', 'true');
			else
				delete_option('kommiku_no_slug');
			
			
			if($kommiku_settings['one_comic'] != "")
				update_option('kommiku_one_comic', $kommiku_settings['one_comic']);
			else
				delete_option('kommiku_one_comic');
								
			//Remove Trialing and Leading Slash
			$kommiku_settings['url'] = $db->trail($kommiku_settings['url']);
			$kommiku_settings['upload'] = $db->trail($kommiku_settings['upload']);
			
			//Check if the Directory Already Exist
			$oldName = WP_LOAD_PATH.'/'.get_option( 'kommiku_comic_upload' );
			$newName = WP_LOAD_PATH.'/'.$kommiku_settings['upload'];
				
				if(is_dir($newName) && $oldName != $newName) {
						$kommiku_settings['error'] = __("The 'Upload Directory' you are trying to rename already exist.", 'kommiku');
						$kommiku_settings['upload'] = get_option( 'kommiku_comic_upload' );
						$kommiku_settings['fail']['upload'] = true;
					} else if($oldName != $newName) {
						rename($oldName,$newName);
						update_option('kommiku_comic_upload', $kommiku_settings['upload']);
					}
				
				if(is_dir(WP_LOAD_PATH.'_kommiku/themes/'.$kommiku_settings['skin']) || ($kommiku_settings['skin'] == 'default')) {
						$kommiku_settings['pass'] = __("Your theme selection has been updated", 'kommiku');
						update_option('kommiku_skin_directory', $kommiku_settings['skin']);
					} else {
						if($kommiku_settings['error']) $kommiku_settings['error'] .= '<br/>';
						$kommiku_settings['error'] .= __('The skin does not exist', 'kommiku');
					}
					
				if(!$kommiku_settings['fail']) $kommiku_settings['pass'] = __("Your Settings has been updated", 'kommiku');
				update_option('kommiku_url_format', $kommiku_settings['url']);
			}
			
	include KOMMIKU_FOLDER.'/admin/settings.php';
}

function kommiku_install() {
	global $wpdb;
	
	if(!get_option( 'kommiku_version' )) add_option ('kommiku_version' , '2.2.1');

	//Update! And if it can't it will be added later.
	if(!KOMMIKU_VERSION) define('KOMMIKU_VERSION','2.2.1');
	update_option('kommiku_version', KOMMIKU_VERSION);

	//Plug Options
	$kommiku_values = array('kommiku_comic_upload' => 'comics',
							'kommiku_counter' => false,
							'kommiku_rating' => false,
							'kommiku_feed_enable' => true,
							'kommiku_search_enable' => true,
							'kommiku_url_format' => 'manga',
							'kommiku_url_feed' => 'rss',
							'kommiku_url_search' => 'find',
							'kommiku_lang' => 'english',
							'kommiku_skin_directory' => 'default',
							'kommiku_one_comic' => false,
							'kommiku_no_slug' => false,
							'kommiku_override_index' => false,
							'kommiku_url_index' => 'directory');
	$kommiku_options = array_keys($kommiku_values);
	foreach($kommiku_options as $option)	{
		if(!get_option( $option )) {
			add_option ( $option, $kommiku_values[$option] );
		}
	}
	
	//Main Kommiku Folder!
	if(!is_dir(WP_LOAD_PATH."/".get_option( 'kommiku_comic_upload' )))
		mkdir(WP_LOAD_PATH."/".get_option( 'kommiku_comic_upload' ), 0755);

	//Custom Stuff!
	if(!is_dir(WP_LOAD_PATH."/_kommiku" )) {
		mkdir(WP_LOAD_PATH."/_kommiku" , 0755);
		mkdir(WP_LOAD_PATH."/_kommiku/themes" , 0755);
		mkdir(WP_LOAD_PATH."/_kommiku/extensions", 0755);
		}
		
	//Create and Update the Tables
	$table = 'comic_series';		
	$attribute[$table] = array('id' => 'int(9) NOT NULL AUTO_INCREMENT',
				'title' => 'varchar(100) NOT NULL',
				'slug' => 'varchar(100) NOT NULL',
				'summary' => 'text NOT NULL',
				'chapterless' => 'tinyint(1) NOT NULL',
				'categories' => 'text NOT NULL',
				'author' => 'varchar(32) NOT NULL',
				'illustrator' => 'varchar(32) NOT NULL',
				'read' => 'int(1) NOT NULL',
				'creation' => 'varchar(32) NOT NULL',
				'alt_name' => 'text NOT NULL',
				'status' => 'int(1) NOT NULL',
				'rating' => 'int(1) NOT NULL',
				'type' => 'int(1) NOT NULL',
				'img' => 'varchar(255) NOT NULL');
	$columns[$table] = array_keys($attribute[$table]);
	
	$table = 'comic_counter';		
	$attribute[$table] = array('user_id' => 'BIGINT(20) NOT NULL',
				'ip_address' => 'varchar(15) NOT NULL',
				'series_id' => 'INT(9) NOT NULL',
				'chapter_id' => 'INT(9) NOT NULL',
				'page_id' => 'INT(9) NOT NULL',
				'rating' => 'INT(2) NULL',
				'value' => 'INT(1) NOT NULL');
	$columns[$table] = array_keys($attribute[$table]);
	
	$table = 'comic_chapter';		
	$attribute[$table] = array('id' => 'INT(9) NOT NULL AUTO_INCREMENT',
				'title' => 'VARCHAR(100) NOT NULL',
				'number' => 'INT(5) NOT NULL',
				'summary' => 'TEXT NOT NULL',
				'series_id' => 'INT(9) NOT NULL',
				'pubdate' => 'VARCHAR(30) NOT NULL',
				'slug' => 'VARCHAR(100) NOT NULL',
				'scanlator' => 'VARCHAR(100) NOT NULL',
				'scanlator_slug' => 'VARCHAR(100) NOT NULL',
				'volume' => 'int(3) NOT NULL',
				'folder' => 'VARCHAR(100) NOT NULL');
	$columns[$table] = array_keys($attribute[$table]);
	
	$table = 'comic_page';		
	$attribute[$table] = array('id' => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
				'title' => 'VARCHAR(100) NOT NULL',
				'slug' => 'VARCHAR(100) NOT NULL',
				'img' => 'VARCHAR(255) NOT NULL',
				'pubdate' => 'VARCHAR(30) NOT NULL',
				'number' => 'int(3) NOT NULL',
				'story' => 'TEXT NOT NULL',
				'series_id' => 'INT(9) NOT NULL',
				'chapter_id' => 'INT(9) NOT NULL',
				'wp_post_slug' => 'VARCHAR(160) NOT NULL');
	$columns[$table] = array_keys($attribute[$table]);
	
	$table = 'comic_category';		
	$attribute[$table] = array('id' => 'INT(3) NOT NULL AUTO_INCREMENT',
				'title' => 'VARCHAR( 32 ) NOT NULL',
				'slug' => 'VARCHAR( 32 ) NOT NULL',
				'summary' => 'TEXT NOT NULL');
	$columns[$table] = array_keys($attribute[$table]);
	
	$table = 'comic_scanlator';		
	$attribute[$table] = array('id' => 'INT(9) NOT NULL AUTO_INCREMENT',
				'title' => 'VARCHAR( 32 ) NOT NULL',
				'slug' => 'TEXT NOT NULL',
				'summary' => 'VARCHAR( 32 ) NOT NULL');
	$columns[$table] = array_keys($attribute[$table]);
	
	$tables = array_keys($columns);
	foreach ($tables as $table){
		foreach ($columns[$table] as $column) {
			if(!$wpdb->query("Show columns from `".$wpdb->prefix.$table."` like '".$column."'")) {
				if($column == 'id') {
					$query = "CREATE TABLE ".$wpdb->prefix.$table." (id ".$attribute[$table]['id'].", UNIQUE KEY id (id));";
				} else {
					$query = "ALTER TABLE `".$wpdb->prefix.$table."` ADD `".$column."` ".$attribute[$table][$column].";";	
				}
				$wpdb->query($query);	
			}
		}
	}
	
	//Chapter Folder Update!
	$tableA = 'comic_series';
	$tableB = 'comic_chapter';
	$series = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix.$tableA."`");
	if($series) {
		foreach($series as $story) {
			$chapters = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix.$tableB."` WHERE `series_id` = '".$story->id."'");
			if($chapters) {
				foreach($chapters as $chapter) {
					if(isset($chapter->slug) && isset($story->slug)) {
						$query = "UPDATE `".$wpdb->prefix.$tableB."` SET `folder` = '/".$story->slug."/".$chapter->slug."/' WHERE `id` = '".$chapter->id."'";
						$wpdb->query($query);
					}
					unset($query);
				}
			}
		}
	}
	return;
}
register_activation_hook(__FILE__, 'kommiku_install');
	
add_filter('rewrite_rules_array', 'add_rewrite_rules');
function add_rewrite_rules($aRules) {
    $aNewRules = array(get_option('kommiku_url_format').'/([^/]+)/?$' => 'index.php?pagename='.get_option('kommiku_url_format').'&series=$matches[1]');
    $aRules = $aNewRules + $aRules;
    $aNewRules = array(get_option('kommiku_url_format').'/([^/]+)/([^/]+)/([^/]+)/?$' => 'index.php?pagename='.get_option('kommiku_url_format').'&series=$matches[1]&chapter=$matches[2]&kpage=$matches[3]');
    $aRules = $aNewRules + $aRules;
    $aNewRules = array(get_option('kommiku_url_format').'/([^/]+)/([^/]+)/?$' => 'index.php?pagename='.get_option('kommiku_url_format').'&series=$matches[1]&chapter=$matches[2]');
    $aRules = $aNewRules + $aRules;
    $aNewRules = array(get_option("kommiku_url_search").'/([^/]+)/?$' => 'index.php?pagename='.get_option("kommiku_url_search"). '&series=$matches[1]&search=true');
    $aRules = $aNewRules + $aRules;
    $aNewRules = array(KOMMIKU_URL_INDEX.'/([^/]+)/?$' => 'index.php?pagename='.KOMMIKU_URL_INDEX. '&series=$matches[1]&directory=true');
    $aRules = $aNewRules + $aRules;
    //$aNewRules = array(    get_option( 'kommiku_url_feed' ).'/([^/]+)/?$' => 'index.php?pagename='.    get_option( 'kommiku_url_feed' ). '&series=$matches[1]&rss=true');
    //$aRules = $aNewRules + $aRules;

    return $aRules;
}

add_action('init', 'k_flush_rewrite_rules');
function k_flush_rewrite_rules() {
    flush_rewrite_rules();
    return;
}

add_filter( 'query_vars', 'k_query_vars' );
function k_query_vars( $query_vars )
{
    $query_vars[] = 'series';
    $query_vars[] = 'chapter';
    $query_vars[] = 'kpage';
    $query_vars[] = 'directory';
    $query_vars[] = 'search';
   // $query_vars[] = 'rss';
    return $query_vars;
}

add_filter( 'wp_title', 'baw_hack_wp_title_for_home' );
function baw_hack_wp_title_for_home( $title )
{
	global $wp_query, $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category, $db ,$current_user, $nextLink, $previousLink;
	$explodeURL = array(get_option('kommiku_url_format'),$wp_query->query['series'], $wp_query->query['chapter'], $wp_query->query['kpage']);
	require_once(KOMMIKU_FOLDER.'/admin/database.php');
	$db = new kommiku_database();
				
	if(get_option('kommiku_url_format') ) {
		if($kommiku['series_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_series` WHERE slug = '".$wp_query->query['series']."'")) {
			$series = $db->series_detail($kommiku['series_id']);
			$kommiku['seotitle'] = $series['title'].' ';
			 
			if(!$kommiku['chapter_id']) $kommiku['chapter_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_chapter` WHERE series_id = '".$kommiku['series_id']."' AND slug = '".$wp_query->query['chapter']."'"); 
            if($kommiku['chapter_id']) {
				$chapter = $db->chapter_detail($kommiku['chapter_id']);
				$kommiku['seotitle'] .= " - ch ".$wp_query->query['chapter'].' ';
				
				
				
				}
				$kommiku['seotitle'] .= "| ";
		}
	}
	
	return $kommiku['seotitle'];
}

add_shortcode( 'kommiku_base' , 'kommiku_base_short' );
	function kommiku_base_short() {
		global $wp_query, $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category, $db ,$current_user, $nextLink, $previousLink, $chapter;
                $explodeURL = array(get_option('kommiku_url_format'),$wp_query->query['series'], $wp_query->query['chapter'], $wp_query->query['kpage']);
                require_once(KOMMIKU_FOLDER.'/admin/database.php');
				$db = new kommiku_database();
	
                if($kommiku['one_comic'] && get_option('kommiku_override_index') != false) {
                        $kommiku['override'] = true;
                        if($explodeURL[0] == ''){
                                $kommiku['manga'] = true;
                                $kommiku['series'] = get_option( 'kommiku_one_comic' );
                                $kommiku['chapter'] = "latest";
                                $kommiku['pages'] = "latest";
                                $kommiku['index'] = true;
                        } else if ($explodeURL[0] == get_option( 'kommiku_one_comic' )) {
                                if($explodeURL[1]) $url = $explodeURL[1]."/";
                                header("Location: ".HTTP_HOST.$url);
                        } else if (is_numeric($explodeURL[0]) && $explodeURL[2] == '') {
                                $kommiku['manga'] = true;
                                $kommiku['series'] = get_option( 'kommiku_one_comic' );
                                $kommiku['chapter'] = $explodeURL[0];
                                $kommiku['pages'] = $explodeURL[1];
                        }
                } else if(get_option('kommiku_url_format') == '' && $explodeURL[0] != '') {
                        $kommiku['series'] = strtolower($explodeURL[0]);
						if($kommiku['series_id']) $kommiku['manga'] = true;
						$kommiku['chapter'] = $explodeURL[1];
						$kommiku['pages'] = $explodeURL[2];
                } else if($_GET['this']) {
                        //Search or Find
                        $kommiku['manga'] = true;
                        $kommiku['find'] = $_GET['this'];
                } else if($wp_query->query['directory'] == true || (get_option('kommiku_desensitise') && $wp_query->query['directory'])) {
                        //Index
                        $kommiku['manga'] = true;
                        if($explodeURL[0] != '')
                                $kommiku['category'] = $explodeURL[1];
                } else if((get_option('kommiku_desensitise') && strtolower($explodeURL[0]) == strtolower(KOMMIKU_URL_FORMAT) && $explodeURL[0] != '') || ($explodeURL[0] == KOMMIKU_URL_FORMAT && $explodeURL[0] != '')) {
                        //If you are only hosting one series on the site
                        if(get_option('kommiku_one_comic') != 0 && get_option('kommiku_one_comic') != false) {
                                $kommiku['manga'] = true;
                                $kommiku['series'] = get_option( 'kommiku_one_comic' );
                                $kommiku['chapter'] = $explodeURL[1];
                                $kommiku['pages'] = $explodeURL[2];

                        } else if($explodeURL[1] != '') {
                                $kommiku['chapter'] = $explodeURL[2];
                                $kommiku['pages'] = $explodeURL[3];
                                $kommiku['series'] = strtolower($explodeURL[1]);
                                if($kommiku['series_id']) $kommiku['manga'] = true;
										
                        } else {
                                $kommiku['manga'] = true;
                        } 
                } else if ($explodeURL[0] == K_SCANLATOR_URL && get_option('kommiku_scanlator_enabled')) {
                        $kommiku['scanlator'] = true;
                        $kommiku['scanlator_slug'] = $explodeURL[1];	
                } else if((count($explodeURL) <= 4) && (count($explodeURL) >= 1) && ($explodeURL[0] != '')) {
                        if(get_option('kommiku_no_slug')) {
                                if(get_option('kommiku_one_comic') != 'false' && is_numeric($explodeURL[0]))  {
                                        $kommiku['manga'] = true;
                                        $kommiku['series'] = get_option( 'kommiku_one_comic' );
                                        $kommiku['chapter'] = $explodeURL[0];
                                        $kommiku['pages'] = $explodeURL[1];
                                } else if(!$kommiku['series']) {
                                        global $wpdb;
                                        $kommiku['series'] = $explodeURL[0];
                                        if($kommiku['series_id'])
                                                $kommiku['manga'] = true;
                                        $kommiku['chapter'] = $explodeURL[1];
                                        $kommiku['pages'] = $explodeURL[2];	
                                }
                        }	
                } 

       
                    //Search Engine
                    if($kommiku['find']) {
                            $kommiku['find'] = urldecode($kommiku['find']);
                            $kommiku['results'] = $db->find_series($kommiku['find']);
                            $kommiku['description'] = __("Search Results for: ", 'kommiku').$kommiku['find'];	
                            $kommiku['keyword'] = "Manga, Comics, ".$kommiku['find'];		
                            $kommiku['seotitle'] = __('Search Results for: ', 'kommiku')."'".$kommiku['find'].'"';

                                    include KOMMIKU_FOLDER.'/frame/body_search.php';

                            if(get_option( 'tableless_page' ))
								return;
							else
								exit;
                    }		

                     if($kommiku['category']) {
                            $category = $db->category_detail($kommiku['category']);
                            $category["url"] = HTTP_HOST.KOMMIKU_URL_INDEX.'/'.$category["slug"].'/';
                            $category["name"] = ucfirst($category["title"]);
                            $category["list"] = $db->category_read();
                            $kommiku['description'] = __("Dead page is Dead.", 'kommiku');
                            if ($category["name"]) {
                                    $kommiku['keyword'] .= ', '.$category["name"];
                                    $kommiku['seotitle'] = __('Category: ', 'kommiku').$category["name"];
                                    $kommiku['description'] = __('Category: ', 'kommiku').$category["name"].". Series Listing.";
                            } else {
                                    $kommiku['seotitle'] = __('OMG! 404?! ', 'kommiku'); 
                            }
                            $search_results = $db->search_category($kommiku['category']);

                                    include KOMMIKU_FOLDER.'/frame/body_category.php';

                            if(get_option( 'tableless_page' ))
								return;
							else
								exit;
                    }

                    if(!empty($kommiku['series'])) {
                            if(!$kommiku['series_id'])
                                    $kommiku['series_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_series` WHERE slug = '".$kommiku['series']."'"); 
                            $series = $db->series_detail($kommiku['series_id']);
                            $kommiku['seotitle'] = $series['title'];
                            $kommiku['slug']['series'] = $series['slug'];	
                            $kommiku['title']['series'] = $series['title'];
                            if(!$kommiku['override']) $kommiku['url']['series'] = KOMMIKU_URL_FORMAT.'/'.$series['slug'].'/';
                    }

                    if(!empty($series['chapterless']) && !$kommiku['index']) {
                            $kommiku['pages'] = $kommiku['chapter'];
                            $kommiku['chapter'] = 0; //or False?
                    } 

                    //Replace Index - 2
                    if($kommiku['pages'] == "latest") {
                            $kommiku['page_id']  = $wpdb->get_var("SELECT max(id) FROM `".$wpdb->prefix."comic_page`");
                            $kommiku['chapter_id']  = $wpdb->get_var("SELECT max(id) FROM `".$wpdb->prefix."comic_chapter`"); 			
                    }

                    if(isset($kommiku['chapter']) || is_numeric($kommiku['chapter_id'] && $kommiku['chapter'] != '')) {

                            if($chapter['slug']) {
                                    $kommiku['seotitle'] .= " : Chapter ".$chapter['slug'];
                                    $kommiku['slug']['chapter'] = $chapter['slug'];	
                                    $kommiku['number']['chapter'] = $chapter['number'];
                                    $kommiku["breacrumb"] = "Chapter ".$kommiku["number"]["chapter"]." ";
                                    $kommiku['title']['chapter'] = $chapter['title'];
                                    $kommiku['url']['chapter'] = $series['url'].$chapter['slug']."/";

                                if(get_option( 'tableless_page' )) {
                                        unset($kommiku['series_chapter']);
                                        $chapterDir = UPLOAD_FOLDER.'/'.$kommiku['slug']['series'].'/';
																			
                                        $ftpChapters = getFileList($chapterDir); 
                                        natsort($ftpChapters);
                                        foreach($ftpChapters as $ftpChapter) {
                                           $modDate = date('M d, Y',filemtime($ftpChapter));
                                           $kommiku['series_chapter']['name'] = str_replace($chapterDir,'',$ftpChapter); 
                                           if(substr($kommiku['series_chapter']['name'], -1) == '/') $kommiku['series_chapter']['name'] = substr($kommiku['series_chapter']['name'], 0, -1);
                                           if(isset($select) && !$nextChapter) $nextChapter = $kommiku['series_chapter']['name'];
                                           unset($select);
                                           if($kommiku['series_chapter']['name'] == $explodeURL[2]) {
                                                   $select = "selected=selected ";
                                                   $chapterSelected = $kommiku['series_chapter']['name'];
                                                   $previousChapterTwo = $lastChapter;
                                           }
                                         
                                           $lastChapter = $kommiku['series_chapter']['name'];
                                          //$kommiku['chapterOption'] .= '<option '.$select.'value="'.$ftpChapter[0].'">'.$ftpChapter[0].'</option>';

                                           /*     $listing[$kommiku['series_chapter']['name']] = 
                                                '<td class="series" style="padding-left: 15px;">'.
                                                                //Grab the URL to the Chapter
                                                                '<a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$series["slug"].'/'.$kommiku['series_chapter']['name'].'/">'.
                                                                //Echo the Chapter title
                                                                'Chapter '.$kommiku['series_chapter']['name'].'</a>
                                                </td>';

                                           $listing[$kommiku['series_chapter']['name']] .= '<td class="updated" style="padding-left: 15px;">'.$modDate.'</td>'; */
                                        }
										
                                        $chapterDir = UPLOAD_FOLDER.'/'.$kommiku['slug']['series'].'/'.$kommiku['slug']['chapter'].'/';
                                        $ftpPages = getFileList($chapterDir); 
                                        sort($ftpPages);
                                        foreach($ftpPages as $ftpPage) {
                                           $ftpPage = str_replace($chapterDir,'',$ftpPage); 
                                           $ftpPage = explode('.',$ftpPage);
                                           if(isset($pageSelected) && !$nextPageTwo) $nextPageTwo = HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$kommiku['slug']['series'].'/'.$kommiku['slug']['chapter'].'/'.$ftpPage[0].'/';
                                           unset($select);
                                           if($ftpPage[0] == $kommiku['pages'] || (!$kommiku['pages'] && !$pageContainer)) {
                                               $select = "selected=selected ";
                                               $pageSelected = $ftpPage[0];
                                               $kommiku['page_id'] = true;
                                               $pageContainer = $ftpPage[0].'.'.$ftpPage[1];
                                               $page = array();
                                               if($lastIteration) $previousPageTwo = HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$kommiku['slug']['series'].'/'.$kommiku['slug']['chapter'].'/'.$lastIteration.'/';
                                           }
                                           $lastIteration = $ftpPage[0];
                                           $kommiku['pageOptionTwo'] .= '<option '.$select.'value="'.$ftpPage[0].'">'.$ftpPage[0].'</option>';
                                        }
										if(!$kommiku['pages']) $kommiku['pages'] = true;
                                        $nextChapterLink = HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$kommiku['slug']['series'].'/'.$nextChapter.'/';
                                        $previousChapterLink = HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$kommiku['slug']['series'].'/'.$previousChapterTwo.'/';
                    
                                        //First chapter of next
                                       $nextChapterDir = UPLOAD_FOLDER.'/'.$kommiku['slug']['series'].'/'.$nextChapter.'/';
                                       $nextChapterList = getFileList($nextChapterDir); 
                                       sort($nextChapterList);
                                       foreach($nextChapterList as $list) {
                                           $list = str_replace($nextChapterDir,'',$list); 
                                           $list = explode('.',$list);
                                          if(!$nextChapterPage) $nextChapterPage = $list[0];
                                       }         
                                       
                                       $preChapterDir = UPLOAD_FOLDER.'/'.$kommiku['slug']['series'].'/'.$previousChapterTwo.'/';
                                       $preChapterList = getFileList($preChapterDir); 
                                       sort($preChapterList);
                                       foreach($preChapterList as $list) {
                                           $list = str_replace($preChapterDir,'',$list); 
                                           $list = explode('.',$list);
                                           $lastChapterPage = $list[0];
                                       }
                             
      
                                }

                            }
                    }

                    
                    if(empty($kommiku['chapter_id'])) {
                            $kommiku['chapter_id'] = 0;
                    } 	

                    if(isset($kommiku['pages']) && ($chapter || $series['chapterless']) && $kommiku['pages']) {               
                        if(!$kommiku['page_id']) $kommiku['page_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_page` WHERE series_id = '".$kommiku['series_id']."' AND slug = '".$kommiku['pages']."' AND chapter_id = '".$kommiku['chapter_id']."'"); 
                            $page = $db->page_detail($kommiku['page_id']);
                            $chapter_pages = $db->chapter_pages($kommiku['series_id'],$kommiku['chapter_id']);
                            $kommiku['seotitle'] .= " page ".$page['slug'];
                            $kommiku['title']['page'] = stripslashes($page['title']);
                            if($kommiku['override'] && $series['chapterless']) {
                                    $kommiku['seotitle'] = $page['slug'];
                                    if($page['title']) $kommiku['seotitle'] .= ' - '.$kommiku['title']['page'];
                            }
                            $kommiku['slug']['page'] = $page['slug'];	
                            $kommiku['number']['page'] = $page['number'];
                            $kommiku['url']['page'] = $page['slug']."/";
                    } else if($chapter) {
                            $kommiku['pages'] = $wpdb->get_var("SELECT min(number) FROM `".$wpdb->prefix."comic_page` WHERE series_id = '".$kommiku['series_id']."' AND chapter_id = '".$kommiku['chapter_id']."'"); 
                            $kommiku['page_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_page` WHERE series_id = '".$kommiku['series_id']."' AND number = '".$kommiku['pages']."' AND chapter_id = '".$kommiku['chapter_id']."'"); 
                            $page = $db->page_detail($kommiku['page_id']);
                            $chapter_pages = $db->chapter_pages($kommiku['series_id'],$kommiku['chapter_id']);
                            $kommiku['seotitle'] .= " : Page ".$page['slug'];
                            $kommiku['slug']['page'] = $page['slug'];	
                            $kommiku['number']['page'] = $page['number'];
                            $kommiku['title']['page'] = stripslashes($page['title']);
                            $kommiku['url']['page'] = $series['url'].$chapter['url'].$page['slug']."/";
                    }

                    $kommiku['series_list_raw_chapters'] = $db->series_list();
                    $kommiku['series_list_raw_chapterless'] = $db->series_list_chapterless();
                    $kommiku['series_list_raw'] = array_merge($kommiku['series_list_raw_chapters'] , $kommiku['series_list_raw_chapterless']);
                    if($kommiku['series_list_raw'] )
                            foreach ($kommiku['series_list_raw'] as $row) {
                            $chapterTitle = stripslashes($row->title);
                                    if(strtolower($row->slug) == strtolower($kommiku['series']))
                                            $seriesOption .= '<option selected=selected value="'.$row->slug.'">'.$chapterTitle.'</option>';
                                    else
                                            $seriesOption .= '<option value="'.$row->slug.'">'.$chapterTitle.'</option>';
                                    $kommiku['series_list'] .= '<li><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->slug.'/">'.$chapterTitle.'</a></li>';
                            };	

                    if(!$series['chapterless'])
                            $kommiku['series_chapter'] = $db->series_chapter($kommiku['series_id']);
                    else
                            $kommiku['series_pages'] = $db->series_pages($kommiku['series_id']);

                    if($kommiku['feed']) {
                            if(!$kommiku['series_chapter'] && !$kommiku['series_pages']){
                                    $tableA = $wpdb->prefix."comic_series";
                                    $tableB = $wpdb->prefix."comic_chapter";
                                    $query = "
                                    SELECT 
                                            $tableB.slug as chapter_slug,
                                            $tableB.pubdate as date, 
                                            $tableA.title as series_name, 
                                            $tableA.slug as series_slug 
                                    FROM $tableA,$tableB 
                                    WHERE $tableA.id = $tableB.series_id
                                    ORDER BY $tableB.pubdate DESC ";
                                    $queryA .= $query."LIMIT 0,15";
                                    $kommiku['series_list_raw'] = $wpdb->get_results( $queryA );	
                            }
                            include KOMMIKU_FOLDER.'/extension/feed.php';
                            exit;
                    } else 

                    if(get_option('kommiku_rating')) {
                            global $voteUrl, $voteMyRating, $voteRating, $voteCount, $current_user;
                            $voteRating = $db->get_rating($series['id'],$chapter['id'],$page['id']); 
                            $voteCount = $db->get_votes($series['id'],$chapter['id'],$page['id']); 

                                    $visitor_ip_address = $db->visitor_ip();
                                    $voteMyRating = $db->get_my_rating($visitor_ip_address,$series['id'],$chapter['id'],$page['id'],$current_user->ID); 

                            if (!$voteRating) $voteRating = 0;
                            if (!$voteCount) $voteCount = 0;
                            if (!$voteMyRating) $voteMyRating = 0;

                            $voteUrl = $series['id']; 
                            if($chapter['id']) $voteUrl .= '/'.$chapter['id'].'/'; 
                            if($page['id']) $voteUrl .= $page['id'].'/'; 
                    }

                    //Page, Chapter, Series		
                    if((!empty($kommiku['series']) && isset($kommiku['chapter']) && $kommiku['page']) || 
                            (!empty($kommiku['series_id']) && isset($kommiku['chapter_id']) && !empty($kommiku['page_id']))){
                            counter_extension();
                            $isPage = true; 
                            
                            include KOMMIKU_FOLDER.'/reader.php';
                           
                            if(get_option( 'tableless_page' )) {
                                       $kommiku['pageOption'] = $kommiku['pageOptionTwo'];
                                       $page['img'] = $pageContainer;
                                       if($nextPageTwo) 
                                           $nextLink = $nextPageTwo;
                                       else 
                                           $nextLink = "null";
                                       
                                       if($nextLink == "null" && $nextChapter && $nextChapterPage) {
                                           $nextLink = $nextChapterLink.$nextChapterPage;
                                       }
                                       
                                       if($previousPageTwo) 
                                           $previousLink = $previousPageTwo;
                                       else 
                                           $previousLink = "null";
                                       
                                       if($previousLink == "null" && $previousChapter) {
                                           $previousLink = $previousChapterLink.$lastChapterPage.'/';
                                       }
                                }
                            include KOMMIKU_FOLDER.'/frame/body_page.php';
                            
                    //Series
                    } else if(!empty($kommiku['series']) && $kommiku['series'] != 'index.php') {
                            $isChapter = true; 
                           include KOMMIKU_FOLDER.'/frame/body_chapter.php';
                            
                    //Main Page with no Series Selected
                    } else {
                            $chapterUpdates = $db->chapter_update_list();
                            $pageUpdates = $wpdb->get_results($pageUquery);
                            $kommiku['seotitle'] .= __("Story Listing", 'kommiku');
                            $category["list"] = $db->category_read();
                            if($kommiku['override'])
                                    header('Location:'.HTTP_HOST);
                            else
                                    include KOMMIKU_FOLDER.'/frame/body_index.php';
                            
                    }
		 
		 return;

	}
        

        
add_shortcode( 'kommiku_series_list' , 'series_list' );
	function series_list() {
		global $wpdb;
		$table = $wpdb->prefix."comic_series";
	  	$select = "SELECT * FROM `".$table."` ORDER BY `title` ASC";
	  	$series_list = $wpdb->get_results( $select );
		if($series_list)
			foreach ($series_list as $row) {
				$theLIST .= '<li><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->slug.'/">'.$row->title.'</a></li>';
			};	
			
		return $theLIST;
	}

add_shortcode( 'kommiku_chapter_update_list' , 'chapter_update_list' );
	function chapter_update_list() {
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
		$chapterUpdateList = $db->chapter_update_list();
		if($chapterUpdateList)
			foreach ($chapterUpdateList as $item) {
				$theLIST .= '<li>'.strftime('[%m.%d]',strtotime($item->pubdate)).' <a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$item->series_slug.'/'.$item->chapter_slug.'/">'.$item->series_name.' - Chapter '.$item->chapter_slug.'</a></li>';
			};	
			
		return $theLIST;
	}
		
function km_get_root()
{
    $base = dirname(__FILE__);
    $path = false;

    if (@file_exists(dirname(dirname($base))."/wp-config.php"))
    {
        $path = dirname(dirname($base))."/wp-config.php";
    }
    else
    if (@file_exists(dirname(dirname(dirname($base)))."/"))
    {
        $path = dirname(dirname(dirname($base)))."/";
    }
    else
    $path = false;
	
    if ($path != false)
    {
        $path = str_replace("\\", "/", $path);
    }
	
	$path = explode('/',$path);
	
	$countRoot = count($path)-2;
	if($path[count($path)] != "") {
		$countRoot = count($path)-1;
	}
	for($i=0;$i<$countRoot;$i++) {
		array_shift($path);
	}
	$result = $path[0];
    return $result;
}

		
function kommiku_menu() {
	add_menu_page('Kommiku', 'Comic', 8, KOMMIKU_FOLDER, 'kommiku', KOMMIKU_URLPATH.'comic.png'); //Thanks Lokis :)
	add_submenu_page(KOMMIKU_FOLDER, 'Kommiku', __('List', 'kommiku'), 8, 'kommiku', 'kommiku'); 
	add_submenu_page(KOMMIKU_FOLDER, 'Kommiku', __('Settings', 'kommiku'), 8, 'kommiku_settings', 'kommiku_settings'); 
	

	if(get_option('kommiku_scanlator_enabled')) {
		add_submenu_page(KOMMIKU_FOLDER, 'Kommiku', __('Scanlators', 'kommiku'), 8, 'kommiku_scanlator', 'kommiku_scanlator'); 
	}

	add_submenu_page(KOMMIKU_FOLDER, 'Kommiku', __('Categories', 'kommiku'), 5, 'kommiku_category', 'kommiku_category'); 
		
	}
	
?>