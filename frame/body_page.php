
<div class="home">
	<div class="breadcrumb">
		<h2 class="kommiku-bread"><a href="<?=HTTP_HOST?><?=$kommiku['url']['series']?>"><?=stripslashes($kommiku["title"]["series"])?></a></h2>
	</div>
	
	<table cellspacing="0" cellpadding="0" border="0" style="width: 100%; text-align: center; margin: 0 auto;">
		<tr><td><?php kommiku_page_navigation(); ?></td></tr>
		
		<tr id="imageWrapper">
			<td style="padding: 25px;"><?php if($page["img"]) img(); else echo stripslashes($page['story']); ?></td>
		</tr>

		<tr><td><?php kommiku_page_navigation(); ?></td></tr>
	</table>   
	<?php //Story Information ?>
	<?php  if($page["img"] && ($page["title"] || $page["story"])){  ?>
		<div id="page-info">
			<?php if($page["title"]){ ?> <h2 id="page-title"><a href="<?=$kommiku['url']['page']?>"><?=$page["title"]?></a></h2> <?php } ?>
			<small><?php echo date('M-d-Y',strtotime($page["pubdate"])); ?></small>
			<?php if($page["story"]){ ?> <p id="page-story"><?=stripslashes($page["story"])?></p> <?php } ?>
		</div>
	<?php } ?>
	
	<?php if($page['wp_post_slug']){ ?>
		<div id="wp-connect">
			<?php query_posts(array('post_status' => 'publish', 'name' => $page['wp_post_slug'] ));the_post();	?>
			<div id="post-<?php the_ID() ?>"">
				<h2 id="page-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<div class="entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages('before=<div class="page-link">' .__('Pages:', 'thematic') . '&after=</div>') ?>
				</div>
				<p style="padding: 0; float: right;"><?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
			</div>
		</div>
	<?php } ?>
	
	
</div>

<?php //Keyboard Commands! No need to touch this. ?>
<script type="text/javascript">
	document.onkeyup = KeyCheck;       
	function KeyCheck(e){
		var ev = e || window.event;
		ev.preventDefault();
		var KeyID = ev.keyCode;
		switch(KeyID){
			case 36: 
			  window.location = '<?=HTTP_HOST?><?=$kommiku['url']['series']?>'
			  break;
			case 33:
			case 37:
				<?php if (checkPrevPage()) { ?>  
					window.location = '<?php prevPage(); ?>';
				<?php } else { ?> 
					alert('You are already on the First Page.'); 
				<?php } ?>
			  break;
			case 34:
			case 39:
				<?php if (checkNextPage()) { ?>  
					window.location = '<?php nextPage(); ?>'; 
				<?php } else { ?> 
					alert('This is the latest page.'); 
				<?php } ?>
			break;
	    }
	}
</script> 





		



