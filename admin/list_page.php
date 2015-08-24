<?php	
$series = $db->series_detail();
$page_list = $db->page_list($page['series_id'],$page['chapter_id']);
if(!$chapter) $chapter = $db->chapter_detail();
if($page_list)
foreach ($page_list as $row) {
	if ($row->title) $ptitle = " - ".stripslashes($row->title);
	$listing[$row->number] = '<li>#'.$row->number.' - <a href="'.$url.'admin.php?page=kommiku&sub=createpage&series='.$series["id"].'&chapter='.$chapter["id"].'&pg='.$row->id.'">'.$row->slug.$ptitle.'</a></li>';
	unset($ptitle);
	}	
$chapter_number = str_replace('.0','',$chapter['number']);
if ($listing) ksort($listing,SORT_NUMERIC);

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
		
$scanlator = get_option('kommiku_scanlator_enabled');
		
if (is_numeric($_GET["series"])) $chapterless = $db->chapterless();
if ($chapterless == 0) { 
	$chapterTitle = ' &raquo; <a href="'.$url.'admin.php?page=kommiku&sub=listpage&series='.$series['id'].'&chapter='.$chapter["id"].'">'.__("Chapter",'kommiku').' '.$chapter_number.'</a>';	
	$chapterURL = '&chapter='.$chapter["id"]; 
	$chapterWord = 'Chapter';
	$sub = '&sub=listchapter';
} else {
	$chapterWord = 'Series';
	$sub = '&sub=listpage';
}
?>	

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><a href="<?php echo $url?>admin.php?page=kommiku"><?php _e('Series Listing', 'kommiku')?></a> &raquo; <a href="<?php echo $url.'admin.php?page=kommiku'.$sub.'&series='.$series['id'];?>"><?php echo $series['title']; ?></a><?php echo $chapterTitle; ?></h2>
	<?php if ($status['pass'] || $status['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $status['error'].$status['pass']; ?></p></div>
	<?php } ?>
	<div class="metabox-holder has-right-sidebar" id="poststuff">
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">	
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php _e('Create a Page', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
									<div class="clear"></div>
									<div style="padding: 10px 0; width: 100%; float: right; text-align: right">
										<a href="<?php echo $url; ?>admin.php?page=kommiku&sub=createpage&series=<?php echo $series['id']; ?><?php echo $chapterURL; ?>" class="button-primary"><?php _e('Create a Page', 'kommiku')?></a>
									</div>
									<div class="clear"></div>
							</div>								    
						</div>
					</div>
				</div>
				<?php if($chapter){ ?>
				<form method="post" action="admin.php?page=kommiku&sub=listpage&series=<?php echo $series['id']; ?>&chapter=<?php echo $chapter['id']; ?>" name="post">
				<input type="hidden" value="<?php echo $series['id']; ?>" name="series_id"/>
				<input type="hidden" value="<?php echo $chapter['id']; ?>" name="chapter_id"/>
				<input type="hidden" value="update" name="action"/>
				<input type="hidden" value="chapter" name="what"/>
				<input type="hidden" value="page" name="destination"/>		
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php _e('Chapter Detail', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<input type="hidden" value="<?php echo $series['id']; ?>" name="series_id"/>
							<div style="background: none;">
									<div style="margin-bottom: 10px;">
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['title'])echo 'style="color: #ff0000;"'; ?>><?php _e('Chapter Name:', 'kommiku')?></span> <input name="title" type="text" value="<?php echo stripslashes($chapter['title']); ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['number'])echo 'style="color: #ff0000;"'; ?>><?php _e('Chapter #:', 'kommiku')?></span> <input name="number" type="text" value="<?php echo $chapter['number']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['slug'])echo 'style="color: #ff0000;"'; ?>><?php _e('Chapter Slug', 'kommiku')?>:</span> <input name="slug" type="text" value="<?php echo $chapter['slug']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											<?php _e('Summary:', 'kommiku')?> <textarea name="summary" type="text" style="width: 150px; float: right; text-align: left;"><?php echo stripslashes($chapter['summary']); ?></textarea>
											<div class="clear"></div> 
										</div>
										<?php if($scanlator){ ?>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['scanlator'])echo 'style="color: #ff0000;"'; ?>><?php _e('Scanlator:', 'kommiku')?></span> <input name="scanlator" type="text" value="<?php echo $chapter['scanlator']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['scanlator_slug'])echo 'style="color: #ff0000;"'; ?>><?php _e('Scanlator Slug:', 'kommiku')?></span> <input name="scanlator_slug" type="text" value="<?php echo $chapter['scanlator_slug']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<?php } else {?>
											<input type="hidden" value="" name="scanlator"/>
											<input type="hidden" value="" name="scanlator_slug"/>
										<?php } ?>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['number'])echo 'style="color: #ff0000;"'; ?>><?php _e('Volume:', 'kommiku')?></span> <input name="volume" type="text" value="<?php echo $chapter['volume']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
									</div>
									<div class="clear"></div>
									<div style="width: 100%; float: right; text-align: right;">
											<input type="submit" value="<?php _e('Update Chapter', 'kommiku')?>" accesskey="c" tabindex="5" class="button-primary" name="series_update"/>
									</div>
									<div class="clear"></div>
							</div>								    
						</div>
					</div>
				</div>
				</form>
				<?php } ?>
				<?php if($chapterless) { ?>
				<form method="post" enctype="multipart/form-data" action="admin.php?page=kommiku&sub=listpage&series=<?php echo $series['id']; ?>" name="post">
				<input type="hidden" value="<?php echo $series['id']; ?>" name="series_id"/>
				<input type="hidden" value="update" name="action"/>
				<input type="hidden" value="series" name="what"/>	
				<input type="hidden" value="page" name="destination"/>		
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php _e('Series Detail', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
									<div style="margin-bottom: 10px;">
										<div class="misc-pub-section">
											<span <?php if($series['fail']['title'])echo 'style="color: #ff0000;"'; ?>><?php _e('Series Name:', 'kommiku')?></span> <input name="title" type="text" value="<?php echo $series['title']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['slug'])echo 'style="color: #ff0000;"'; ?>><?php _e('Series Slug:', 'kommiku')?></span> <input name="slug" type="text" value="<?php echo $series['slug']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section">
											<?php _e('Summary:', 'kommiku')?> <textarea name="summary" type="text" style="width: 150px; float: right; text-align: left;" /><?php echo stripslashes($series['summary']); ?></textarea>
											<div class="clear"></div> 
										</div>
										<?php if($scanlator){ ?>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['author'])echo 'style="color: #ff0000;"'; ?>><?php _e('Author:', 'kommiku')?></span> <input name="author" type="text" value="<?php echo $series['author']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['illustrator'])echo 'style="color: #ff0000;"'; ?>><?php _e('Illustrator:', 'kommiku')?></span> <input name="illustrator" type="text" value="<?php echo $series['illustrator']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<?php } ?>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['alternate'])echo 'style="color: #ff0000;"'; ?>><?php _e('Date Created:', 'kommiku')?></span> <input name="creation" type="text" value="<?php echo $series['creation']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['alternate'])echo 'style="color: #ff0000;"'; ?>><?php _e('Other Names:', 'kommiku')?></span> <input name="alt_name" type="text" value="<?php echo $series['alt_name']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['alternate'])echo 'style="color: #ff0000;"'; ?>><?php _e('Categories:', 'kommiku')?></span> <input name="categories" type="text" value="<?php echo $series['categories']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section" style="text-align: right;"> 
											<?php _e('Read Direction:', 'kommiku')?>  
											<select name="read">
												<option <?php  if($series['read'] == '0') echo 'selected="selected"'; ?>value="0"><?php _e('Left to Right', 'kommiku')?></option>
												<option <?php  if($series['read'] == '1') echo 'selected="selected"'; ?>value="1"><?php _e('Right to Left', 'kommiku')?></option>
												<option <?php  if($series['read'] == '2') echo 'selected="selected"'; ?>value="2"><?php _e('Top to Bottom', 'kommiku')?></option>
											</select>
										</div>
										<div class="misc-pub-section" style="text-align: right;">
											<?php _e('Status: ', 'kommiku')?>
											<select name="status">
												<option <?php if($series['status'] == '0') echo 'selected="selected"'; ?>value="0"><?php _e('Unknown', 'kommiku')?></option>
												<option <?php if($series['status'] == '1') echo 'selected="selected"'; ?>value="1"><?php _e('Ongoing', 'kommiku')?></option>
												<option <?php if($series['status'] == '2') echo 'selected="selected"'; ?>value="2"><?php _e('On-Hold', 'kommiku')?></option>
												<option <?php if($series['status'] == '3') echo 'selected="selected"'; ?>value="3"><?php _e('Dropped', 'kommiku')?></option>
												<option <?php if($series['status'] == '4') echo 'selected="selected"'; ?>value="4"><?php _e('Complete', 'kommiku')?></option>
											</select>
										</div>	
										<div class="misc-pub-section" style="text-align: right;">
											<?php _e('Story Type:', 'kommiku')?> 
											<select name="type">
												<option <?php if($series['type'] == '0') echo 'selected="selected"'; ?>value="0"><?php _e('(_blank)', 'kommiku')?></option>
												<option <?php if($series['type'] == '1') echo 'selected="selected"'; ?>value="1"><?php _e('Manga', 'kommiku')?></option>
												<option <?php if($series['type'] == '2') echo 'selected="selected"'; ?>value="2"><?php _e('Manhwa', 'kommiku')?></option>
												<option <?php if($series['type'] == '3') echo 'selected="selected"'; ?>value="3"><?php _e('Manhua', 'kommiku')?></option>
												<option <?php if($series['type'] == '4') echo 'selected="selected"'; ?>value="4"><?php _e('Comic', 'kommiku')?></option>
												<option <?php if($series['type'] == '5') echo 'selected="selected"'; ?>value="5"><?php _e('Unknown', 'kommiku')?></option>
												<option <?php if($series['type'] == '6') echo 'selected="selected"'; ?>value="6"><?php _e('Novel', 'kommiku')?></option>
											</select>
										</div>	
										<?php //For Mature Series?
											if($matureEnable) { ?>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['rating'])echo 'style="color: #ff0000;"'; ?>><?php _e('Age Rating:', 'kommiku')?></span> <input name="rating" type="text" value="<?php echo $series['rating']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<?php } ?>
										<div class="misc-pub-section "><?php _e('Series Book Cover:', 'kommiku')?> 
										<?php if($series['img']) echo '<strong>[Exist]</strong>'; ?>
										<br/><br/>
											<input type="file" style="background: none repeat scroll 0% 0% rgb(238, 238, 238); width: 100%; -moz-background-inline-policy: continuous;" autocomplete="off" value="" tabindex="1" size="30" name="img">
										</div>
									</div>
									<div class="clear"></div>
									<div style="width: 100%; float: right; text-align: right">
											<input type="submit" value="Update Series" accesskey="p" tabindex="5" class="button-primary" name="series_update"/>
									</div>
									<div class="clear"></div>
							</div>							
						</div>
					</div>
				</div>
				</form>
				<?php } ?>

				<div class="postbox">
					<h3 style="cursor: default;"><span><?php echo $deleteWord; ?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<input type="hidden" value="<?php echo $series['id']; ?>" name="series_id"/>
							<div style="background: none;">
								<div class="clear"></div>							
								<div style="padding: 10px 0; width: 100%; float: right; text-align: right;">
									<a class="button-primary" href="admin.php?page=kommiku&amp;sub=delete&amp;series=<?php echo $series['id']; ?>&amp;chapter=<?php echo $chapter['id']; ?>"><?php _e('Delete', 'kommiku')?> <?php echo $chapterWord; ?>!</a>
								</div>
								<div class="clear"></div>									
							</div>								    
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div id="post-body-content">
			<div>
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php _e('Page Listing', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div id="titlediv" style="margin: 0;">
								<div id="titlewrap">
									<ul>
								<?php
								if($listing)
								
									foreach ($listing as $list) {
										echo $list;
									}
									
								else
								_e("There are no page in this Chapter.", 'kommiku');
							
								?>
									</ul>
								</div>
							</div>	
						</div>
					</div>
				</div>
			</div>										
		</div>
		</div>
</div>