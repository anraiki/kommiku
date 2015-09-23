<?php	
if($_POST['what'] == "scanlator") { 
	global $wpdb;
		$_CLEAN['title']          = $db->clean($_POST['title']);
		$_CLEAN['slug']           = $db->clean($_POST['slug']);
		$_CLEAN['summary']        = $db->clean($_POST['summary']);
	
	$table = $wpdb->prefix."comic_scanlator";
		
	//Updating?
	if(is_numeric($_POST['scanlator_id'])) {
		if($wpdb->get_var("SELECT id FROM `".$table."` WHERE id = '".$_POST['scanlator_id']."'") != $_POST['scanlator_id'])
			die('Bad Hacks!'); 
	} else if(!$_POST['action']) {
			die('Hacks Again!');
	}
	
	//Check if the Title Exist
	if($wpdb->get_var("SELECT title FROM `".$table."` WHERE title = '".$_CLEAN['title']."'") == $_CLEAN['title'])  {
		if (($_POST['action'] == 'create')) 
			//If it does, and our action is to create it, create an error!
			$scanlator['fail']['title'] = true;
		else if($_POST['action'] == "update" && is_numeric($_POST['scanlator_id'])) { 
			//If it does, and out action is to update it, then update it
			//But check if we are updating the correct ID!
			$_OLD['title'] = $wpdb->get_var("SELECT title FROM `".$table."` WHERE id = '".$_POST['scanlator_id']."'"); 
			if($_OLD['title'] == $_CLEAN['title'])
				$noRename = true;
			else
				$rename = true;
		} 
	} //If title doesn't exist, create it :)
			
			
	//Checks for Slug which Omit the creation and renaming of Folders :D
	if($_POST['action'] == "update" && is_numeric($_POST['scanlator_id'])) {
		$_OLD['slug'] = $wpdb->get_var("SELECT slug FROM `".$table."` WHERE id = '".$_POST['scanlator_id']."'"); 
		if($_OLD['slug'] == $_CLEAN['slug'])
			$noRename = true;
	}
		
	//The slug exist what next?
	if($wpdb->get_var("SELECT slug FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."'") == $_CLEAN['slug']) 
		//if exist, check if we are updating, if we aren't we can't create this data!
		if (($_POST['action'] != 'update') || ($_POST['action'] == "update" && $_OLD['slug'] != $_CLEAN['slug']))
			$scanlator['fail']['slug'] = true;
								
	if(!$scanlator['fail']['slug'] && !$scanlator['fail']['title']) {
		
		if($_POST['action'] == "create") {
			$db->scanlator_create($_POST['title'],$_CLEAN['slug'],stripslashes($_CLEAN['summary']));
			$status['pass'] = 'The scanlator has been successfully created';
			$scanlatorID =	$wpdb->get_var("SELECT id FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."'");
			unset($scanlator);
		} else if($_POST['action'] == "update" && is_numeric($_POST['scanlator_id'])) {
			$official = 1;
			$db->scanlator_update($_POST['scanlator_id'],$_CLEAN['title'],$_CLEAN['slug'],stripslashes($_CLEAN['summary']));
			$status['pass'] = 'The scanlator has been updated';						
			
		}
	} else {
		if ($scanlator['fail']['title']) $status['error'] .= __("'The scanlator name has already been taken", 'kommiku').'<br/>';
		if ($scanlator['fail']['slug']) $status['error'] .= __("The scanlator slug has already been taken", 'kommiku').'<br/>';
		$scanlator['title'] = $_POST['title'];
		$scanlator['slug'] = $_POST['slug'];
		$scanlator['summary'] = stripslashes($_POST['summary']);
		$scanlator['chapterless'] = $_POST['chapterless'];
	}
		
} 

$alphabets = $kommiku['alphabets'];
if($db->scanlator_list())
	foreach ($db->scanlator_list() as $row) {
		$singleLetter = ucwords($row->title[0]);
		$letter[$singleLetter][] = '<li><A href="'.$url.'admin.php?page=kommiku&sub=scanlator_edit&scanlator='.$row->id.'">'.$row->title.'</a></li>';
		};	

?>	

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><?php echo '<A href="'.$url.'admin.php?page=kommiku_scanlator">'; ?><?_e('Scanlator List', 'kommiku')?></a></h2>
	<?php if ($status['pass'] || $status['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $status['pass'].$status['error']; ?></p></div>
	<?php } ?>
	<div class="metabox-holder has-right-sidebar" id="poststuff">
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">				
				<div class="postbox">
					<h3 style="cursor: default;"><span><?_e('Name a Scanlator', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<form method="post" action="admin.php?page=kommiku_scanlator" name="post">
							<input type="hidden" value="create" name="action"/>
							<input type="hidden" value="scanlator" name="what"/>
							<input type="hidden" value="scanlator" name="destination"/>
								<div style="background: none;">
										<div style="margin-bottom: 10px;">
											<div class="misc-pub-section ">
												<span <?php if($scanlator['fail']['title'])echo 'style="color: #ff0000;"'; ?>><?_e('Scanlator Name:', 'kommiku')?></span> <input name="title" type="text" value="<?php if($_GET['action'] != 'delete') echo $scanlator['title']; ?>" style="width: 150px; float: right; text-align: left;" />
												<div class="clear"></div> 
											</div>
											<div class="misc-pub-section ">
												<span <?php if($scanlator['fail']['slug'])echo 'style="color: #ff0000;"'; ?>><?_e('Scanlator Slug:', 'kommiku')?></span> <input name="slug" type="text" value="<?php if($_GET['action'] != 'delete') echo $scanlator['slug']; ?>" style="width: 150px; float: right; text-align: left;" />
												<div class="clear"></div> 
											</div>
											<div class="misc-pub-section ">
												<?_e('Summary:', 'kommiku')?> <textarea name="summary" type="text" style="width: 150px; float: right; text-align: left;" /><?php if($_GET['action'] != 'delete') echo stripslashes($scanlator['summary']); ?></textarea>
												<div class="clear"></div> 
											</div>
										</div>
										<div class="clear"></div>
										<div style="width: 100%; float: right; text-align: right">
												<input type="submit" value="<?_e('Create Scanlator', 'kommiku')?>" tabindex="5" class="button-primary" name="scanlator_create"/>
										</div>
										<div class="clear"></div>
								</div>							
						    </form>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div id="post-body-content">
			<div>
				<div class="postbox">
					<h3 style="cursor: default;"><span><?_e('Go to a Letter', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div id="titlediv" style="margin: 0;">
								<div id="titlewrap">
									<ul>
									<?php 
									
										foreach ($alphabets as $alphabet) {
											
											if ($letter[$alphabet])
												echo '<a href="#letter-'.$alphabet.'">'.$alphabet.'</a>  '; 
											else 
												echo $alphabet.'  ';  
											}
										
									?>
									</ul>
								</div>
							</div>	
						</div>
					</div>
				</div>
				<?php foreach ($alphabets as $alphabet) {
						if ($letter[$alphabet]) { ?>			
							<div class="postbox">
								<h3 style="cursor: default;" id="letter-<?php echo $alphabet; ?>"><span><?php echo $alphabet; ?></span></h3>
								<div class="inside">
									<ul>
										<?php foreach ($letter[$alphabet] as $name) {
												echo $name;	}?>
									</ul>
								</div>
							</div>
					<?php }} ?>
			</div>										
		</div>
		</div>
</div>
	
