<?php global $kommiku; ?>
<div class="manga-navi">
	<span class="previousLink"><?php prevPage(true,'[Previous]'); ?></span>
	<span class="nextLink"><?php nextPage(true,'[Next]'); ?></span>
	<?php if($kommiku['chapterOption']){ ?> 
		Chapter 
			<select onchange="javascript:window.location='<?=HTTP_HOST?><?=$kommiku['url']['series']?>'+this.value+'/';" name="Chapters" class="viewerChapter">
				<?=$kommiku['chapterOption']?>
			</select>
	<?php } ?>
		Page 
			<select onchange="javascript:window.location='<?=HTTP_HOST?><?=$kommiku['url']['series']?><?=$kommiku['url']['chapter']?>'+this.value+'/';" name="Pages" class="viewerPage">
				<?=$kommiku['pageOption']?>
			</select>
</div>