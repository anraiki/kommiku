<?php kommiku_header(); ?>
<?php
$kommiku['series_list_raw'] = $db->series_list();
if($kommiku['series_list_raw'] )
foreach ($kommiku['series_list_raw'] as $row) {
	if(strtolower($row->slug) == strtolower($kommiku['series']))
		$seriesOption .= '<option selected=selected value="'.$row->slug.'">'.$row->title.'</option>';
	else
		$seriesOption .= '<option value="'.$row->slug.'">'.$row->title.'</option>';
	$kommiku['series_list'] .= '<li><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->slug.'/">'.$row->title.'</a></li>';
};	

$releaseCount = count($scanlator['releases']);
if($scanlator['text']){
		
	 	$re1='(\\[.*?\\])';
	 	$re2='(\\(.*\\))';
  		$explosion = explode("\r\n",$scanlator['text']);
		foreach ($explosion as $dividedLinks) {
			if ($c=preg_match_all ("/".$re1.$re2."/is", $dividedLinks, $matches))
			{
			    $linkWord = str_replace(array("[","]"),"",strtolower(stripslashes($matches[1][0])));
				$linkUrl = str_replace(array("(",")"),"",$matches[2][0]);
			}
			
			if($linkWord == "siteimg" || $linkWord == "img" || $linkWord == "image") {
				$scaninfo['img']  = '<img style="padding: 5px; border:2px solid #33CCFF; float: right;" src="'.$linkUrl.'" />';	
			} else if($linkWord == "website" || $linkWord == "home site" || $linkWord == "site") {
				$scaninfo['img'] = '<a href="'.$linkUrl.'">'.$scaninfo['img'] .'</a>';
				$scaninfo['website'] .= '<strong>Website:</strong> <a href="'.$linkUrl.'">'.$linkUrl.'</a>';
			} else if($linkWord == "associated name") {
				$scaninfo['name'] = '<strong>Associated Name:</strong> '.$linkUrl;
			}
		}
	}
	
	
	
if($scanlator['link']){
		
	 	$re1='(\\[.*?\\])';
	 	$re2='(\\(.*\\))';
  		$explosion = explode("\r\n",$scanlator['text']);
		foreach ($explosion as $dividedLinks) {
			if ($c=preg_match_all ("/".$re1.$re2."/is", $dividedLinks, $matches))
			{
			    $linkWord = str_replace(array("[","]"),"",stripslashes($matches[1][0]));
				$linkUrl = str_replace(array("(",")"),"",$matches[2][0]);
			}
			
		if($linkWord != "RSS")
			$Links[$linkWord] = "<li><a class='iconExternal' rel='nofollow' href='" . $linkUrl . "'>" . $linkWord . "</a></li>";		
		else if($linkWord == "RSS") 
			$rssLink = $linkUrl;
		else 
			$twitter = $linkUrl;
		}
		
		ksort($Links);
	}

?>

<div>
	<div class="wrap" style="padding: 0pt 25px 25px;">
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
    <div style="margin: 0 0 0 10px; padding: 0; float: left; position:absolute;"><?_e('Series', 'kommiku')?> <select onchange="javascript:window.location='<?php if(KOMMIKU_URL_FORMAT) echo '/'.KOMMIKU_URL_FORMAT; ?>/'+this.value+'/';" name="Series" style="width: 250px" class="viewerSeries"><?php echo $seriesOption; ?></select></div>
		<?php if($scaninfo['img']) echo $scaninfo['img'];?>
		<h2><A href="<?=$url?>"><?=$scanlator['title']?></a></h2>
		<div class="icon32" id="icon-edit">
		<?php if($scaninfo['name']) echo $scaninfo['name'].'<br/>';?>
		<?php if($scaninfo['website']) echo $scaninfo['website'].'<br/>';?>
		<strong><?_e('Release Count:', 'kommiku')?></strong> <?=$releaseCount?> <?_e('Chapters', 'kommiku')?></div>
		<?php if($scanlator['releases']) { ?>
			<div class="postbox" style="clear:both;">
				<h3 style="cursor: default; bordeR: 0;"><span><?_e('Releases', 'kommiku')?></span></h3>
				<div class="inside">
					<table id="scanlator-updates" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr class="headline">
							<td class="series"><?_e('Series', 'kommiku')?></td>
							<td class="chapter"><?_e('Chapter', 'kommiku')?></td>
							<td class="date"><?_e('Upload Date', 'kommiku')?></td>
						</tr>
						<?php 
							foreach ($scanlator['releases'] as $row) {
							$i++;
							if ($i % 2) { $alt = "";} else { $alt = " class='alt'";}
								echo "<tr".$alt.">";
								echo "<td class='series'><a href='" . HTTP_HOST . KOMMIKU_URL_FORMAT . "/" . $row->series_slug . "/'>" . $row->series_title . "</a></td>";
								echo "<td class='chapter'><a href='" . HTTP_HOST . KOMMIKU_URL_FORMAT . "/" . $row->series_slug . "/" . $row->chapter_number . "/" . "'>Chapter " . $row->chapter_number . "</a></td>";
								echo "<td class='date'>" . date( 'm-d-Y', strtotime($row->chapter_date)) . "</td>";
								echo "</tr>";
							};	
						?>
					</table>
				</div>
			</div>
		<?php } ?>			
	</div>
</div>


<?php kommiku_footer(); ?>
