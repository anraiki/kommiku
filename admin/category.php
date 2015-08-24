<?php	
if($_POST['what'] == "category") { 
	
		$_CLEAN['name']           = $db->clean($_POST['name']);
		$_CLEAN['slug']           = $db->clean($_POST['slug']);
		$_CLEAN['description']    = $db->clean($_POST['description']);
	
	$table = $wpdb->prefix."comic_category";
	if($_POST['action'] == "delete" && is_numeric($_POST['id'])) {
		$db->category_delete($_POST['id']);
		$status['pass'] .= 'The Category has been Deleted';
	}
	
	if($_POST['action'] == "create") {
		//Updating?
		if(is_numeric($_POST['id'])) {
			if($wpdb->get_var("SELECT id FROM `".$table."` WHERE id = '".$_POST['id']."'") != $_POST['id'])
				die('Bad Hacks!'); 
		} else if(!$_POST['action']) {
				die('Hacks Again!');
		}
		
		//Check if the Title Exist
		if($wpdb->get_var("SELECT name FROM `".$table."` WHERE name = '".$_CLEAN['name']."'") == $_CLEAN['name'])  {
			if ($_POST['action'] != 'create') 
				$category['fail']['name'] = true;
			
		} //If name doesn't exist, create it :)
				
				
		//Checks for Slug which Omit the creation and renaming of Folders :D
		if($_POST['action'] == "update" && is_numeric($_POST['id'])) {
			$_OLD['slug'] = $wpdb->get_var("SELECT slug FROM `".$table."` WHERE id = '".$_POST['id']."'"); 
			if($_OLD['slug'] == $_CLEAN['slug'])
				$noRename = true;
		}
			
		//The slug exist what next?
		if($wpdb->get_var("SELECT slug FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."'") == $_CLEAN['slug']) 
			//if exist, check if we are updating, if we aren't we can't create this data!
			if (($_POST['action'] != 'update') || ($_POST['action'] == "update" && $_OLD['slug'] != $_CLEAN['slug']))
				$category['fail']['slug'] = true;
									
		if(!$category['fail']['slug'] && !$category['fail']['name']) {
			
			if($_POST['action'] == "create") {
				$db->category_create($_CLEAN['name'],stripslashes($_CLEAN['description']),$_CLEAN['slug']);
				$status['pass'] = 'The category has been successfully created';
				unset($category);
			} 
			
		} else {
			if ($category['fail']['name']) $status['error'] .= 'The category name has already been taken.<br/>';
			if ($category['fail']['slug']) $status['error'] .= 'The category slug has already been taken.<br/>';
			$category['name'] = $_POST['name'];
			$category['slug'] = $_POST['slug'];
			$category['description'] = stripslashes($_POST['description']);
		}
	}	
} 

$alphabets = $kommiku['alphabets'];
if($list = $db->category_read())
	foreach ($db->category_read() as $row) {
		$singleLetter = ucwords($row->title[0]);
		$letter[$singleLetter][] = '<li><a href="'.$url.'admin.php?page=kommiku&sub=category_edit&category='.$row->slug.'">'.$row->title.'</a></li>';
		};	
?>	

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><?php echo '<a href="'.$url.'admin.php?page=kommiku_category">'; ?><?php _e('Category List', 'kommiku')?></a></h2>
	<?php if ($status['pass'] || $status['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $status['pass'].$status['error']; ?></p></div>
	<?php } ?>
	<div class="metabox-holder has-right-sidebar" id="poststuff">
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">				
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php _e('Name a Category', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<form method="post" action="admin.php?page=kommiku_category" name="post">
							<input type="hidden" value="create" name="action"/>
							<input type="hidden" value="category" name="what"/>
							<input type="hidden" value="category" name="destination"/>
								<div style="background: none;">
										<div style="margin-bottom: 10px;">
											<div class="misc-pub-section ">
												<span <?php if($category['fail']['name'])echo 'style="color: #ff0000;"'; ?>><?php _e('Category Name:', 'kommiku')?></span> <input name="name" type="text" value="<?php if($_GET['action'] != 'delete') echo $category['name']; ?>" style="width: 150px; float: right; text-align: left;" />
												<div class="clear"></div> 
											</div>
											<div class="misc-pub-section ">
												<span <?php if($category['fail']['slug'])echo 'style="color: #ff0000;"'; ?>><?php _e('Category Slug:', 'kommiku')?></span> <input name="slug" type="text" value="<?php if($_GET['action'] != 'delete') echo $category['slug']; ?>" style="width: 150px; float: right; text-align: left;" />
												<div class="clear"></div> 
											</div>
											<div class="misc-pub-section ">
												<?php _e('Summary:', 'kommiku')?> <textarea name="description" type="text" style="width: 150px; float: right; text-align: left;" /><?php if($_GET['action'] != 'delete') echo stripslashes($category['description']); ?></textarea>
												<div class="clear"></div> 
											</div>
										</div>
										<div class="clear"></div>
										<div style="width: 100%; float: right; text-align: right">
												<input type="submit" value="Create Category" tabindex="5" class="button-primary" name="category_create"/>
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
					<h3 style="cursor: default;"><span><?php _e('Go to a Letter', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div id="namediv" style="margin: 0;">
								<div id="namewrap">
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
	
