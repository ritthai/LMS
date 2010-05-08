<?php
$TERMS = urlencode($terms." Lecture Notes");
$USERIP = $_SERVER['REMOTE_ADDR'];

$url = "http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=$TERMS&userip=$USERIP&rsz=small";
$data = file_get_contents($url);
$arr = json_decode($data, true);
//var_dump($arr);
$results = $arr['responseData']['results'];
//var_dump($results);
?>
<ul>
<?php
foreach($results as $elem) {
	//print_r($elem);
	$link = $elem['url'];
	$title = $elem['titleNoFormatting'];
?>
<li><a href="<?php echo $link; ?>"><?php echo $title; ?></a></li>
<?php
}
?>
</ul>
