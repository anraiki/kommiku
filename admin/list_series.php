<?php	
$alphabets = $kommiku['alphabets'];
$series_admin_list = $db->series_admin_list();
	if($series_admin_list)
	foreach ($series_admin_list as $row) {
		$singleLetter = ucwords($row->title[0]);
		if(is_numeric($singleLetter)) { $singleLetter = '0-9'; }
		if($row->chapterless) $chapterless = 'listpage';
			else $chapterless = 'listchapter';
		$key = strtolower($row->title);
		$letter[$singleLetter][$key] = '<li><a href="'.$url.'admin.php?page=kommiku&sub='.$chapterless.'&series='.$row->id.'">'.$row->title.'</a></li>';
		ksort($letter[$singleLetter]);
		};	
	
?>	

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><?php echo '<a href="'.$url.'admin.php?page=kommiku">'; ?><?php _e('Series Listing', 'kommiku')?></a></h2>
	<?php if ($status['pass'] || $status['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $status['pass'].$status['error']; ?></p></div>
	<?php } ?>
	<div class="metabox-holder has-right-sidebar" id="poststuff">
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">				
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php _e('Create a Series', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<form method="post" action="admin.php?page=kommiku" name="post">
							<input type="hidden" value="create" name="action"/>
							<input type="hidden" value="series" name="what"/>
								<div style="background: none;">
										<div style="margin-bottom: 10px;">
											<div class="misc-pub-section ">
												<span <?php if($series['fail']['title'])echo 'style="color: #ff0000;"'; ?>><?php _e('Series Name:', 'kommiku')?></span> <input name="title" type="text" value="<?php if($_GET['action'] != 'delete') echo $series['title']; ?>" style="width: 150px; float: right; text-align: left;" />
												<div class="clear"></div> 
											</div>
											<div class="misc-pub-section ">
												<span <?php if($series['fail']['slug'])echo 'style="color: #ff0000;"'; ?>><?php _e('Series Slug:', 'kommiku')?></span> <input name="slug" type="text" value="<?php if($_GET['action'] != 'delete') echo $series['slug']; ?>" style="width: 150px; float: right; text-align: left;" />
												<div class="clear"></div> 
											</div>
											<div class="misc-pub-section ">
												<?php _e('Summary:', 'kommiku')?> <textarea name="summary" type="text" style="width: 150px; float: right; text-align: left;" /><?php if($_GET['action'] != 'delete') echo stripslashes($series['summary']); ?></textarea>
												<div class="clear"></div> 
											</div>
											<div class="misc-pub-section ">
												<?php _e('Chapter-Less:', 'kommiku')?> <input type="checkbox" <?php if ($series['chapterless']) echo 'checked="checked" '; ?>value="1" name="chapterless"/>
												<div class="clear"></div> 
											</div>
										</div>
										<div class="clear"></div>
										<div style="width: 100%; float: right; text-align: right">
												<input type="submit" value="Create Series" tabindex="5" class="button-primary" name="series_create"/>
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
	
