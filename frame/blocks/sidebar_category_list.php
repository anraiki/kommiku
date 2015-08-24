<table width="27%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 15px; float: right;">														
	<tbody>
	<tr><td style="width: 45%;" class="series">
			<form action="<?=HTTP_HOST?>/<?=get_option("kommiku_url_search")?>/" method="get">	
				<input style="width:100px;" type="text" name="this" value="Search" onfocus="if (this.value == 'Search') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search';}"/>
				<input type="submit" value="Search"/>
			</form>
	</td></tr>
	<tr class="headline"><td style="width: 45%; padding-left: 15px;" class="series"><strong><a href="<?=HTTP_HOST?><?=KOMMIKU_URL_INDEX?>/">Directory</a></strong></td></tr>
	<?php if($category["list"]){ ?>
		<tr class="headline"><td style="width: 45%; padding-left: 15px;" class="series"><strong>Categories</strong></td></tr>
		<?php foreach ($category["list"] as $item)
			echo '<tr><td class="series" style="padding-left: 15px;"><a href="'.HTTP_HOST.KOMMIKU_URL_INDEX.'/'.$item->slug.'/">'.$item->title.'</a></td></tr>';
	}?>
	</tbody>
</table>