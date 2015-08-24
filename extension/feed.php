<?php
/**
 * RSS 0.92 Feed Template for displaying RSS 0.92 Posts feed.
 *
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<rss version="0.92">
<channel>	
<?php 
if($kommiku['series_chapter']) {
	foreach ($kommiku['series_chapter'] as $item) {
		//Unset Vars that may be set
		unset($title); 

		//Format the Date Y-m-d
		$thedate = date( 'D, d M Y H:i:s +0000', strtotime($item->pubdate) );
		$lastBuildDate = $thedate;
		
		//Make sure the Title does not overwrite non-existing titles.
		//No need to touch this
		if ($item->title) $title = ' - '.stripslashes($item->title);
		
		//Chapter Formatting - Wrap in a TD
		$listing[$item->number] = 
		'<item>'.
			'<title>'.$series["title"].' '.$item->slug.$title.'</title>'.
			'<description><![CDATA[Chapter '.$item->slug.' of '.$series["title"].']]></description>'.
			'<link>'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$series["slug"].'/'.$item->slug.'</link>'.
			'<pubDate>'.$thedate.'</pubDate>'.
		'</item>';
		//End of Wrap

	} 
} else if($kommiku['series_pages']) {
	foreach ($kommiku['series_pages'] as $item) {
		//Unset Vars that may be set
		unset($title);

		//Format the Date Y-m-d
		$thedate = date( 'D, d M Y H:i:s +0000', strtotime($item->pubdate) );
		$lastBuildDate = $thedate;
		
		//Make sure the Title does not overwrite non-existing titles.
		//No need to touch this
		if($item->title) $title = ' - '.stripslashes($item->title);

		$listing[$item->number] = 
		'<item>'.
			'<title>'.$series["title"].' '.$item->slug.$title.'</title>'.
			'<description><![CDATA[Page '.$item->slug.' of '.$series["title"].']]></description>'.
			'<link>'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$series["slug"].'/'.$item->slug.'</link>'.
			'<pubDate>'.$thedate.'</pubDate>'.
		'</item>';
		
		//End of Wrap

	} 
} else { //Main PAge
	foreach ($kommiku['series_list_raw'] as $item) {
		//Unset Vars that may be set
		unset($title); 

		//Format the Date Y-m-d
		$thedate = date( 'D, d M Y H:i:s +0000', strtotime($item->last_update) );
		$lastBuildDate = $thedate;
		
		//Chapter Formatting - Wrap in a TD
		$listing[] = 
		'<item>'.
			'<title>'.$item->series_name.' '.$item->chapter_slug.'</title>'.
			'<description><![CDATA[Chapter '.$item->chapter_slug.' of '.$item->series_name.']]></description>'.
			'<link>'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$item->series_name.'/'.$item->chapter_slug.'</link>'.
			'<pubDate>'.$thedate.'</pubDate>'.
		'</item>';
		//End of Wrap

	} 
}
	
if($series["title"]) {
	$rssTitle = $series["title"].' | ';
	$description = __('Latest updates for '.$series["title"], 'kommiku');
	krsort($listing);
} else {
	ksort($listing);
}

?>

	<title><?php echo $rssTitle; ?><?php bloginfo_rss('name'); ?></title>
	<link><?php echo HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$series["slug"].'/'; ?></link>
	<description><?php echo $description; ?><?php if(!$description) { _e('Newest updates from ', 'kommiku'); bloginfo_rss('name');} ?></description>
	<lastBuildDate><?php echo $lastBuildDate; ?></lastBuildDate>
	<generator>Kommiku</generator>
	<language>english</language>

	<?php
	//Sort the Chapters
		if($listing) {
			foreach ($listing as $list) { echo $list; }
		}
	
	?>

</channel>
</rss>
