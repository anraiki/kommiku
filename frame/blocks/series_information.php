<?php global $kommiku, $series;

switch($series['status']) { 
	case 0: $seriesStatus = 'Unknown'; break; 
	case 1: $seriesStatus = 'Ongoing'; break;
	case 2: $seriesStatus = 'On-hold'; break;
	case 3: $seriesStatus = 'Dropped'; break;
	case 4: $seriesStatus = 'Complete'; break;
}

switch($series['type']) { 
	case 0: unset($seriesType); break; 
	case 1: $seriesType = 'Manga'; break;
	case 2: $seriesType = 'Manhwa'; break;
	case 3: $seriesType = 'Manhua'; break;
	case 4: $seriesType = 'Comic'; break;
	case 5: $seriesType = 'Unknown'; break;
	case 6: $seriesType = 'Novel'; break;
}

switch($series['read']) { 
	case 0: $readDirection = 'Left to Right'; break; 
	case 1: $readDirection = 'Right to Left'; break;
	case 2: $readDirection = 'Up to Down'; break;
}

if($series['categories']) {
	$cats = explode(',',$series['categories']);
	
	foreach ($cats as $cat) {
		if($category_divided) $category_divided .= ", ";
		$cat_slug = str_replace(' ','_',trim($cat));
		$category_divided .= '<a href="/'.KOMMIKU_URL_INDEX.'/'.$cat_slug.'/">'.$cat.'</a>';	
	}
}

?>

<?php //Image ?>
<?php if($series['img']) {echo '<img style="float: right; max-width:165px; padding: 0 15px;" src="'.UPLOAD_URLPATH.'/'.strtolower($series['slug']).'/'.$series['img'].'"/>'; $topwidth = "75%"; } else { $topwidth = "75%"; }?>

<?php //Table with Information ?>
<table width="<?=$topwidth?>" border="0" cellpadding="0" cellspacing="0">
	<?php if($series['alt_name']) {?><tr><td class="infoTabOne"><strong>Alternate Names:</strong></td><td><?=$series['alt_name']?></td><?php } ?>
	<?php if($seriesType) {?><tr><td class="infoTabOne"><strong>Type:</strong></td><td><?=$seriesType?></td><?php } ?>
	<?php if($seriesStatus) {?><tr><td class="infoTabOne"><strong>Status:</strong></td><td><?=$seriesStatus?></td><?php } ?>
	<?php if($series['author']) {?><tr><td class="infoTabOne"><strong>Author</strong></td><td><?=$series['author']?></td><?php } ?>
	<?php if($series['illustrator']) {?><tr><td class="infoTabOne"><strong>Illustrator:</strong></td><td><?=$series['illustrator']?></td><?php } ?>
	<?php if($series['categories']) {?><tr><td class="infoTabOne"><strong>Categorize in:</strong></td><td><?=$category_divided?></td><?php } ?>
	<?php if($series['creation']) {?><tr><td class="infoTabOne"><strong>Date Created:</strong></td><td><?=$series['creation']?></td><?php } ?>
	<?php if($readDirection) {?><tr><td class="infoTabOne"><strong>Reading Direction:</strong></td><td><?=$readDirection?></td><?php } ?>		
	<?php if($series['summary']) {?><tr><td class="infoTabOne"><strong>Summary:</strong></td><td><?=$series['summary']?></td><?php } ?>
</table>