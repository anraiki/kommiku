<?php	

$scanlator = $db->scanlator_detail($db->clean($_GET['scanlator']));

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
	<h2><A href="<?php echo $url; ?>admin.php?page=kommiku_scanlator"><?_e('Scanlator List', 'kommiku')?></a> &raquo; <?php echo $scanlator['title']; ?></h2>
	<?php if ($status['pass'] || $status['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $status['pass'].$status['error']; ?></p></div>
	<?php } ?>
	<form method="post" action="admin.php?page=kommiku&sub=scanlator_edit&scanlator=<?php echo $scanlator['id']; ?>" name="post" enctype="multipart/form-data">
	<div class="metabox-holder has-right-sidebar">
		<input type="hidden" value="<?=$scanlator['id']?>" name="scanlator_id"/>
		<input type="hidden" value="scanlator" name="what"/>	
		<input type="hidden" value="update" name="action"/>	
		<input type="hidden" value="scanlator_edit" name="destination"/>	
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">
				<div class="postbox">
					<h3 style="cursor: default;"><span><?_e('Update?', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox" style="padding: 5px;">
							<div style="background: none; font-size: 11px;">
								<div style="width: 100%; float: right; text-align: right">
									<input style="margin-top:10px; width: 100px;" type="submit" value="<?_e('Update', 'kommiku')?>" accesskey="p" tabindex="5" class="button-primary" name="publish"/>
								</div>
								<div class="clear"></div>
								<div class="misc-pub-section "></div>
							</div>								    
						</div>
					</div>
				</div>

				<?php if ($scanlator['id']) { ?>
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php echo $deleteWord; ?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
								<div class="clear"></div>							
								<div style="padding: 10px 0; width: 100%; float: right; text-align: right;">
									<a class="button-primary" href="admin.php?page=kommiku&amp;sub=delete&amp;scanlator=<?=$scanlator['id']?>"><?_e('Delete Scanlator!', 'kommiku')?></a>
								</div>
								<div class="clear"></div>									
							</div>								    
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	
		<div id="post-body-content">
			<div class="postbox" style="margin-bottom: 0px;">
				<h3 style="cursor: default;"><span><?_e('Scanlator Name', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="title" class="screen-reader-text"><?_e('Title', 'kommiku')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?=$scanlator['title']?>" tabindex="1" size="30" name="title"/>
					</div>
				</div>
			</div>			
			
			<div style="margin-bottom: 10px;">
				<div class="inside">
					<div id="edit-slug-box">
						<strong><?_e('Permalink:', 'kommiku')?></strong> <span id="sample-permalink"><?php echo HTTP_HOST.KOMMIKU_URL_INDEX.'/'._e("category", 'kommiku').'/'; ?>
						<input type="text" value="<?=$scanlator['slug']?>" name="slug" style="width: 10%; background: #FFFBCC;" /> /
						</span>
					</div>
				</div>
			</div>

			
		<div class="metabox-holder">
		
		<?php if($scanlator['img']){ ?>
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('The Page', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px; overflow-x: scroll; text-align: center;">
						<?php echo '<img src="'.UPLOAD_URLPATH.'/'.strtolower($scanlator['slug']).'/'.$db->trailingslash($chapter_number).$scanlator['img'].'" />'; ?>
					</div>
				</div>
			</div>
		<?php } ?>
		
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Info', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="text" class="screen-reader-text">text</label>
						<textarea tabindex="2" name="text" style="width: 99.5%;" rows="5"><?=stripslashes($scanlator['text'])?></textarea>									
							<p><?_e('General Information of the Scanlator', 'kommiku')?></p>
					</div>
				</div>
			</div>
		
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Links', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="link" class="screen-reader-text">link</label>
						<textarea tabindex="2" name="link" style="width: 99.5%;" rows="5"><?=stripslashes($scanlator['link'])?></textarea>									
							<p><?_e('Seperate each line for new external link.', 'kommiku')?><br/><?_e('Then wrap in BB-Code, see example.', 'kommiku')?><br/><?_e('Example: [The Tosho](http://thetosho.com/)', 'kommiku')?></p>
					</div>
				</div>
			</div>

		</div>
	</div>
		
	</form>
</div>