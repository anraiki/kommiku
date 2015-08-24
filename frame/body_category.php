<?php	
$alphabets = array('0-9',A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z);
	if($search_results)
	foreach ($search_results as $row) {
		$singleLetter = ucwords($row->title[0]);
		if(is_numeric($singleLetter)) { $singleLetter = '0-9'; }
		
			$letter[$singleLetter][] = 
				'<td class="series"><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->slug.'/">'.$row->title.'</a></td>';
	};	

?>	


<div id="content" class="narrowcolumn home">
	<?php if ($category["title"]) { ?>
	<h3 style="margin-bottom: 10px;">Category: <a href="<?=$category["url"]?>"><?=$category["title"]?></a></h3>
	<?php  
	  	$explosion = explode("\r\n",stripslashes($category["description"]));
		foreach ($explosion as $paragraph) {
			echo '<p>'. $paragraph .'</p>';	
		} 
	 ?>
	<?php kommiku_sidebar_category_list(); ?>
	<?php foreach ($alphabets as $alphabet) {
		if ($letter[$alphabet]) { 
			unset($odd); unset($even);
			if(count($letter[$alphabet])%2)
				$odd = ' class="alt"';
			else
				$even = ' class="alt"';
			unset($i);?>
			<table width="72%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 15px;">							
				<tr id="#letter-<?=$alphabet?>" class="headline">
					<td class="series" style="width: 45%;"><?=$alphabet?></td>
				</tr>
				
				<?php foreach ($letter[$alphabet] as $name) {
						$i++;
						if ($i % 2) {
							echo '<tr'.$even.'>'.$name.'</tr>';	
						} else {
							echo '<tr'.$odd.'>'.$name.'</tr>';	
						}
					}
				?>
				
			</table>
		<?php }
	} ?>
	
	<?php } else { ?>
	<h2 style="margin-bottom: 10px;">Category does not Exist - 404!</h2>
		<p>Sorry this category does not exist</p>
	<?php } ?>
</div>
