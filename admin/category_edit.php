<?php	

$category = $db->category_detail($db->clean($_GET['category']));

switch(rand(0,3)) {
	case 0:
		$deleteWord = __("Don't Do it!", 'kommiku');
		break;
	case 1:
		$deleteWord = __("It's a TRAP!", 'kommiku');
		break;
	case 2:
		$deleteWord = __("Why???!", 'kommiku');
		break;
	case 3:
		$deleteWord = __("But I am your friend :(", 'kommiku');
		break;
		
		}
		
	
?>	

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><a href="<?php echo $url; ?>admin.php?page=kommiku_category"><?php _e('Category List', 'kommiku')?></a> &raquo; <a href="admin.php?page=kommiku&sub=category_edit&category=<?php echo $category['slug']; ?>"><?php echo $category['title']; ?></a></h2>
	<?php if ($status['pass'] || $status['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $status['pass'].$status['error']; ?></p></div>
	<?php } ?>
	<div class="metabox-holder has-right-sidebar">
	<form method="post" action="admin.php?page=kommiku&sub=category_edit&category=<?php echo $category['slug']; ?>" name="post" enctype="multipart/form-data">
		<input type="hidden" value="<?php echo $category['id']; ?>" name="id"/>
		<input type="hidden" value="category" name="what"/>	
		<input type="hidden" value="update" name="action"/>	
		<input type="hidden" value="category_edit" name="destination"/>	
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php _e('Update?', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox" style="padding: 5px;">
							<div style="background: none; font-size: 11px;">
								<div style="width: 100%; float: right; text-align: right">
									<input style="margin-top:10px; width: 100px;" type="submit" value="<?php _e('Update', 'kommiku')?>" accesskey="p" tabindex="5" class="button-primary" name="publish"/>
								</div>
								<div class="clear"></div>
								<div class="misc-pub-section "></div>
							</div>								    
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div id="post-body-content">
			<div class="postbox" style="margin-bottom: 0px;">
				<h3 style="cursor: default;"><span><?php _e('Category Name', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="name" class="screen-reader-text"><?php _e('Name', 'kommiku')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?php echo $category['title']; ?>" tabindex="1" size="30" name="title"/>
					</div>
				</div>
			</div>			
			
			<div style="margin-bottom: 10px;">
				<div class="inside">
					<div id="edit-slug-box">
						<strong><?php _e('Permalink:', 'kommiku')?></strong> <span id="sample-permalink"><?php echo HTTP_HOST.KOMMIKU_URL_INDEX.'/'.__("category", 'kommiku').'/'; ?>
						<input type="text" value="<?php echo $category['slug']; ?>" name="slug" style="width: 10%; background: #FFFBCC;" /> /
						</span>
					</div>
				</div>
			</div>

			
		<div class="metabox-holder">
		
			<div class="postbox">
				<h3 style="cursor: default;"><span><?php _e('Description', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="summary" class="screen-reader-text"><?php _e('Description', 'kommiku')?></label>
						<textarea tabindex="2" name="summary" style="width: 99.5%;" rows="5"><?php echo stripslashes($category['summary'])?></textarea>									
						<p><?php _e('Add a description of the Category', 'kommiku')?></p>
					</div>
				</div>
			</div>
			</form>
				<?php if ($category['id']) { ?>
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php echo $deleteWord; ?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
								<div class="clear"></div>							
								<div style="padding: 10px 0; width: 100%; float: right; text-align: right;">		
								<form method="post" action="admin.php?page=kommiku_category" name="post" enctype="multipart/form-data">
									<input type="hidden" value="<?php echo $category['id']; ?>" name="id"/>
									<input type="hidden" value="category" name="what"/>	
									<input type="hidden" value="delete" name="action"/>	
									<input type="hidden" value="category" name="destination"/>	
									<input type="submit" name="category_create" class="button-primary" tabindex="5" value="<?php _e('Delete this Category', 'kommiku')?>">
								</form>
								</div>
								<div class="clear"></div>									
							</div>								    
						</div>
					</div>
				</div>
				<?php } ?>
		</div>
	</div>
		
	
</div>