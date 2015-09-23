<?php kommiku_header();

$alphabets = $kommiku['alphabets'];
	if($kommiku['category'] == 'complete')
		$showCompleteOnly = true;

	if($db->scanlator_list())
	foreach ($db->scanlator_list() as $row) {
		$singleLetter = ucwords($row->title[0]);
		$letter[$singleLetter][] = '<td class="series"><a href="'.$url.'/scanlator/'.$row->slug.'/">'.$row->title.'</a></td>';
	};	
?>	

<div>
<center><script type="text/javascript"><!--
google_ad_client = "pub-0920438246297103";
/* 728x90, created 6/8/10 */
google_ad_slot = "7135228084";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></center>
<div id="directory">
		<div>
			<div class="postbox" style="width: 95%; margin: 0 auto;">
				<h3 style="cursor: default;"><span><?_e('Scanlator Directory', 'kommiku')?></span></h3>
				<?php foreach ($alphabets as $alphabet) {
					if ($letter[$alphabet]) { 
						unset($odd); unset($even);
						if(count($letter[$alphabet])%2)
							$odd = ' class="alt"';
						else
							$even = ' class="alt"';
						
						unset($i);?>
						<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 15px;">							
							<tr id="#letter-<?=$alphabet?>" class="headline">
								<td class="series" style="width: 60%;"><?=$alphabet?></td>
							</tr>
							
							<?php foreach ($letter[$alphabet] as $name) {
									$i++;
									if ($i % 2) {
										echo '<tr'.$odd.'>'.$name.'</tr>';	
									} else {
										echo '<tr'.$even.'>'.$name.'</tr>';	
									}
								}
							?>
							
						</table>
					<?php }
				} ?>
			</div>
		</div>										
</div>
</div>

<?php kommiku_footer(); ?>
