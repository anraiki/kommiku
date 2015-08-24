<?php global $kommiku;

	//Check condition if we want to output only Completed Series
	if($kommiku['category'] == 'complete')
		$showCompleteOnly = true;
	
	//Check if we Series in the List	
	if($kommiku['series_list_raw'])
	foreach ($kommiku['series_list_raw'] as $row) {
		$singleLetter = ucwords($row->series_name[0]);
		
		//Check if the first letter of the Series Name is a Number
		if(is_numeric($singleLetter)) { $singleLetter = '0-9'; }
		
		//Translate the Status from Integer to Number
		switch($row->status){
			case 0: $status = 'Unknown'; break;
			case 1: $status = 'Ongoing'; break;
			case 2: $status = 'On-Hold'; break;
			case 3: $status = 'Dropped'; break;
			case 4: $status = 'Complete'; break;
			default: unset($status);
		}
		
		//Format the Date of the Series
		//Y-m-d = Years - Month in Number - Day in Number
		$thedate = date( 'Y-m-d', strtotime($row->last_update) );

		//Format the Series
		if($row->chapterless == 1) 
			$chapterless = 'Page';
		else
			$chapterless = 'Chapter';		

		$letter[$singleLetter][strtolower($row->series_name)] = 
			//Series name and Title
			'<td class="series"><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->series_slug.'/">'.$row->series_name.'</a></td>'.
			//The latest update from the Series
			'<td class="chapter"><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->series_slug.'/'.$row->latest_slug.'/">'.$chapterless.' '.$row->latest_slug.'</a></td>'. 
			//When was the series Last Updated?
			'<td class="updated">'.$thedate.'</td>'.
			//What is the Status of the Series?
			'<td class="status">'.$status.'</td>';
		ksort($letter[$singleLetter]);
	}	
	
	//Start the Output
	 foreach ($kommiku['alphabets'] as $alphabet) {
		if ($letter[$alphabet]) { 
		
			//Give Alternate Row Coloring
			unset($odd); unset($even);
			if(count($letter[$alphabet])%2)
				$odd = ' class="alt"';
			else
				$even = ' class="alt"';
				
			//Start the Output
			unset($i);
			$table .= '
			<table width="72%" border="0" cellpadding="0" cellspacing="0">'.							
				//This <tr> will the header.
				'<tr id="#letter-'.$alphabet.'" class="headline">
					<td class="series" style="width: 40%;">'.$alphabet.'</td>
					<td class="chapter" style="width: 23%;">Latest Update</td>
					<td class="updated" style="width: 22%;">Last Updated</td>
					<td class="status" style="width: 15%;">Status</td>
				</tr>';
			
				//Now output the Series of this "letter"
				 foreach ($letter[$alphabet] as $name) {
						$i++;
						if ($i % 2) {
							$table .= '<tr'.$even.'>'.$name.'</tr>';	
						} else {
							$table .= '<tr'.$odd.'>'.$name.'</tr>';	
						}
					}
			
			//End of the Table Wrap
			$table .= '</table>';
		}
	}  
	
	//Echo the Table
	echo $table;

			?>