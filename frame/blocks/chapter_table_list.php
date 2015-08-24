<?php 
if($kommiku['series_chapter']) {
	foreach ($kommiku['series_chapter'] as $item) {
		//Unset Vars that may be set
		unset($title); unset($scanperson);

		//Format the Date Y-m-d
		$thedate = date( 'M d, Y', strtotime($item->pubdate) );
		
		//Make sure the Title does not overwrite non-existing titles.
		//No need to touch this
		if ($item->title) $title = ' - '.stripslashes($item->title);
		
		//Ignore this ine of Code:
		if (!$item->scanlator) { $scanperson = 'n/a'; } else { $scanlators = explode(',',$item->scanlator); $scanlators_slug = explode(',',$item->scanlator_slug); for($i=0; $i < count($scanlators); $i++) { if($scanperson) $scanperson .= ' & '; if($scanlators_slug[$i]) {	$scanperson .= '<a href="'.HTTP_HOST.K_SCANLATOR_URL.'/'.$scanlators_slug[$i].'/">'.$scanlators[$i].'</a>'; $theScanlator[trim($scanlators[$i])] = '<a href="'.HTTP_HOST.K_SCANLATOR_URL.'/'.$scanlators_slug[$i].'/">'.$scanlators[$i].'</a>'; } else { $scanperson .= $scanlators[$i];$theScanlator[trim($scanlators[$i])] = $scanlators[$i]; } } }
		//Hello Again!
		
		//Chapter Formatting - Wrap in a TD
		$listing[$item->number] = 
		'<td class="series" style="padding-left: 15px;">'.
			//Grab the URL to the Chapter
			'<a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$series["slug"].'/'.$item->slug.'/">'.
			//Echo the Chapter title
			'Chapter '.$item->slug.$title.'</a>
		</td>';
		//End of Wrap
		
		//Scanlator and Author Wrap
		if ($scanperson  != 'n/a') 
			$listing[$item->number] .= '<td class="chapter" style="padding-left: 15px; text-align: left;">'.$scanperson.'</td>'; 
		else if($kommiku['scanlator_feature'])
			$listing[$item->number] .= '<td class="chapter" style="padding-left: 15px;">n/a</td>';
		
		//Date Wrap in a TD
		$listing[$item->number] .= '<td class="updated" style="padding-left: 15px;">'.$thedate.'</td>';
	} 
} else if($kommiku['series_pages']) {
	foreach ($kommiku['series_pages'] as $item) {
		//Unset Vars that may be set
		unset($title);

		//Format the Date Y-m-d
		$thedate = date( 'M d, Y', strtotime($item->pubdate) );
		
		//Make sure the Title does not overwrite non-existing titles.
		//No need to touch this
		if($item->title) $title = ' - '.stripslashes($item->title);
				
		//Chapter Formatting - Wrap in a TD
		$listing[$item->number] = 
		'<td class="series" style="padding-left: 15px;">'.
			//Grab the URL to the Chapter
			'<a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$series["slug"].'/'.$item->slug.'/">'.
			//Echo the Page title
			'Page '.$item->slug.$title.'</a>
		</td>';
		//End of Wrap
		
		//Date Wrap in a TD
		$listing[$item->number] .= '<td class="updated" style="padding-left: 15px;">'.$thedate.'</td>';
	} 
}

//Check whether we are on a Chapterless Series
if($series['chapterless'])
	$chaterlessWord = 'Page';
else
	$chaterlessWord = 'Chapter';
	
//Sort the Chapters
if($listing) 
	krsort($listing);

//Start the Output
if($listing) { ?> 
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr class="headline">
			<td class="series" style="width:40%;"><?=$chaterlessWord?></td>
			<?php if($kommiku['scanlator_feature']) {?><td class="chapter" style="width:35%;">Scanlator</td><?php } ?>
			<td class="updated" style="width:15%;">Date Uploaded</td>
		</tr>
		<?php 
		if($listing)
			foreach ($listing as $list) {
				$a++;
				unset($odd); unset($even);
				if(count($listing)%2)
					$odd = ' class="alt"';
				else
				   $even = ' class="alt"';
					
				if ($a % 2) {
					echo '<tr'.$even.'>'.$list.'</tr>';
				} else {
					echo '<tr'.$odd.'>'.$list.'</tr>';
				}
			
			}
		else {
			if($kommiku['scanlator_feature']) $extraTd = '<td></td>'; //<--- This add a Extra TD if you have the Scnlator Feature enabled.
			echo "<tr><td class='series'>There are no ".$chaterlessWord."s for this series yet.</td>".$extraTd."<td></td></tr>";
		} ?>
	</table>
<?php } ?>