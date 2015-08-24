<?php 

$alphabets = array('Series','Categories','Tags','Author','Illustrator');

	if($kommiku['results'])

	foreach ($kommiku['results'] as $row) {
		$solution = '<a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->slug.'/">'.$row->title.'</a>';
		//Series
		if (preg_match("/".$kommiku['find']."/i",$row->title))
			$result['Series'][] = $solution;
		if (preg_match("/".$kommiku['find']."/i",$row->categories))
			$result['Categories'][] = $solution;
		if (preg_match("/".$kommiku['find']."/i",$row->tags))
			$result['Tags'][] = $solution;
		if (preg_match("/".$kommiku['find']."/i",$row->author))
			$result['Author'][] = $solution;
		if (preg_match("/".$kommiku['find']."/i",$row->illustrator))
			$result['Illustrator'][] = $solution;
	}	
?>	

<div style="padding: 20px; width: 600px; margin: 0 auto;">
	<?php if ($result) { ?>
	<h2 style="margin-bottom: 0px;"><a href="<?=$category["url"]?>"><?=$category["name"]?></a></h2>
	<div id="tosho-index">
		<div class="postbox" style="padding: 0pt 20px;">
				<?php foreach ($alphabets as $alphabet) {
						if ($result[$alphabet]) { 
							sort($result[$alphabet]);
							unset($odd); unset($even);
							if(count($letter[$alphabet])%2)
								$odd = ' class="alt"';
							else
								$even = ' class="alt"';
							unset($i);?>			
								<h3 style="cursor: default;" id="search-<?=$alphabet; ?>">
								<span>
								<?php switch($alphabet) {
										case "Series": echo "Found in Series Name"; break;
										case "Categories": echo "Series with Category of  \"".$kommiku['find']."\""; break;
										case "Tags": echo "Series tagged with \"".$kommiku['find']."\""; break;
										case "Author": echo "Series authored by Name of \"".$kommiku['find']."\""; break;
										case "Illustrator": echo "Series illustrated by Name of \"".$kommiku['find']."\""; break;
									}?>
								</span></h3>
								<div class="inside">
									<ul style="padding-left: 15px;">
										<?php foreach ($result[$alphabet] as $name) {
												$i++;
												if ($i % 2) {
													echo '<li'.$odd.'>'.$name.'</li>';	
												} else {
													echo '<li'.$even.'>'.$name.'</li>';	
												}
											}
										?>
									</ul>
								</div>
					<?php }
					} ?>
		</div>									
	</div>
	<?php } else { ?>
	<h2 style="margin-bottom: 10px;">We couldn't find it!</h2>
		<p>Sorry buddy. We couldn't find "<?=$kommiku["find"]?>". <br/>Maybe it went somewhere else? <br/><br/>Try searching again, or try our alternate solutions:</p>
		<ul style="list-style: circle outside none; margin-left: 50px; margin-bottom: 20px;"><li><a href="<?=HTTP_HOST.KOMMIKU_URL_INDEX?>/">Directory</a></li>
		</ul>
	<?php } ?>
</div>


