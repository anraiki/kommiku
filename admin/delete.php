<?php	

if(is_numeric($_GET['series']))
	$series = $db->series_detail($_GET['series']);
if(is_numeric($_GET['chapter'])) {
	$chapter = $db->chapter_detail($_GET['chapter']);

	if(is_numeric($chapter['number'])) {
		$chapter_number = str_replace('.0','',$chapter['number']);
		$notChapterless = $chapter_number."/";
		$chapterUrl = 'admin.php?page=kommiku&sub=listpage&series='.$series['id'].'&chapter='.$chapter["id"];
		$chapterTitle = '&raquo; <a href="'.$url.$chapterUrl.'">Chapter '.$chapter_number.'</a>';
	}
}
if(is_numeric($_GET['pg'])) {
	$page = $db->page_detail($_GET['pg']);
	if(is_numeric($page['number'])) {
		$chapterTitle .= " &raquo; Page ".$page['number'];	
	}
}

if (is_numeric($_GET['pg'])) 
	$inputs .= '<input type="hidden" name="pg" value="'.$_GET['pg'].'"/>';


if (is_numeric($_GET['chapter'])) 
	$inputs .= '<input type="hidden" name="chapter" value="'.$_GET['chapter'].'"/>';

if ($series['chapterless']) 
	$chapterless = 'listpage';
else
	$chapterless = 'listchapter';
	
if ($chapterUrl) 
  $actionUrl = $chapterUrl;
else if($_GET['pg'] && $_GET['chapter'])
  $actionUrl = 'admin.php?page=kommiku&sub='.$chapterless.'&series='.$series['id'];
else if($_GET['pg'])
  $actionUrl = 'admin.php?page=kommiku&sub='.$chapterless.'&series='.$series['id'];
else
  $actionUrl = 'admin.php?page=kommiku';

?>	

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><a href="<?php echo $url.'admin.php?page=kommiku&sub='.$chapterless.'&series='.$series['id'];?>"><?php echo $series['title']; ?></a><?php echo $chapterTitle; ?></h2>
	<?php if ($series['pass'] || $series['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $series['pass'].$series['error']; ?></p></div>
	<?php } ?>
	<form method="post" action="<?php echo $actionUrl?>" name="post">
	<input type="hidden" value="<?php echo $series['id']; ?>" name="series"/>
	<?php echo $inputs; ?>
	<div class="metabox-holder has-right-sidebar" id="poststuff">	
		<div id="post-body-content">
			<div>
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php _e('Delete?! Are you sure?', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div id="titlediv" style="margin: 0;">
								<div id="titlewrap">
									<ul>
									<?php if (is_numeric($series['id']) && is_numeric($chapter['id']) && is_numeric($page['id'])) { 
										//Series with Chapter. On Page ?>
										<li><h4><?php echo $series['title'].' - Chapter '.$chapter_number.' - Page '.$page['number']; ?></h4></li>
										<li><?php _e('Are you sure you want to delete this Page?', 'kommiku')?></li>
									<?php } if (is_numeric($series['id']) && is_numeric($chapter['id']) && !is_numeric($page['id'])) { 
										//Series. On Chapter ?>
										<li><h4><?php echo $series['title'].' - Chapter '.$chapter_number; ?></h4></li>
										<li><?php _e('Are you sure you want to delete this Chapter?', 'kommiku')?></li>
										<li><?php _e('All <strong>Pages</strong> within the Chapters will all be deleted', 'kommiku')?></li>
									<?php } if (is_numeric($series['id']) && !is_numeric($chapter['id']) && !is_numeric($page['id'])) { 
										//Just deleteing the Series? ?>
										<li><h4><?php echo $series['title'] ?></h4></li>
										<li><?php _e('Are you sure you want to delete this Series?', 'kommiku')?></li>
										<li><?php _e("The <strong>Series</strong>, and <strong>ALL it's Content</strong> will all be deleted", 'kommiku')?></li>
									<?php } if (is_numeric($series['id']) && !is_numeric($chapter['id']) && is_numeric($page['id']) && $series['chapterless']) {
										//Chapterless Series. On Page  ?>
										<li><h4><?php echo $series['title'].' - Page '.$page['number']; ?></h4></li>
										<li><?php _e('Are you sure you want to delete this Page?', 'kommiku')?></li>
									<?php } ?>
									</ul>
								</div>
							</div>	
						</div>
					</div>
				</div>
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php _e('The Delete Button', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
								<div class="clear"></div>
								<div style="width: 100%; float: right; text-align: right">
									<input name="delete" value="<?php _e('Delete It!', 'kommiku')?>" type="submit" style="float: right; margin: 5px;" class="button-primary"/>
								</div>
								<div class="clear"></div>
							</div>							
						</div>
					</div>
				</div>
			</div>										
		</div>
		
		
		
		

		</div>
		
	</form>
</div>
	
