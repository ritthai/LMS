<?php
session_start();

include("includes/mysql.inc");
include("includes/misc.inc");
include("classes/course.class.php");
include("classes/coursedefn.class.php");

database_connect();

$PAGE_TITLE = "homepage.php";

$course_code = "";
if(isset($_POST['terms'])) {
	$course_code = $_POST['terms'];
	$crs = new CourseDefn($_POST['terms']);
	if(!$crs->load())
		echo "Course not found<br/>";
	$_POST['descr'] = $crs->descr;
}
$descr = "";
if(isset($_POST['descr']))
	$descr = ereg_replace("\\\\", "", $_POST['descr']);
$tags = "";
if(isset($_POST['tags']))
	$tags = $_POST['tags'];
?>
<html>
<body>
<form action="<?php echo $PAGE_TITLE; ?>" method="post">
Enter the course code: <input type="text" id="terms" name="terms" value="<?php echo $course_code; ?>" /><br/>
Description: <textarea cols=40 rows=5 id="descr" name="descr" value="<?php echo $descr; ?>" /><?php echo $descr; ?></textarea><br/>
Tags (comma-delimited): <textarea cols=30 rows=3 id="tags" name="tags" /><?php echo $tags; ?></textarea><br/>
<input type="submit" value="Find resources" />
</form>

<hr/>

<form action="<?php echo $PAGE_TITLE; ?>" method="post">
Course name: <input type="text" id="course_name" name="course_name" /><br/>
Prof: <input type="text" id="course_prof" name="course_prof" /><br/>
<input type="submit" value="Save" />
</form>
<hr/>
<?php
$first_load = false;

if(isset($_POST['descr'])) {
	$descr = $_POST['descr'];
	
	// http://www.sequencepublishing.com/academic.html
	$function_words = array("a bit of","a couple of","a few","a good deal of","a good many","a great deal of","a great many","a lack of","a little","a little bit of","a majority of","a minority of","a number of","a plethora of","a quantity of","all","an amount of","another","any","both","certain","each","either","enough","few","fewer","heaps of","less","little","loads","lots","many","masses of","more","most","much","neither","no","none","numbers of","part","plenty of","quantities of","several","some","the lack of","the majority of","the minority of","the number of","the plethora of","the remainder of","the rest of","the whole","tons of","various","all","another","any","anybody","anyone","anything","both","each","each other","either","everybody","everyone","everything","few","he","her","hers","herself","him","himself","his","I","it","its","itself","many","me","mine","myself","neither","no_one","nobody","none","nothing","one","one another","other","ours","ourselves","several","she","some","somebody","someone","something","such","that","theirs","them","themselves","these","they","this","those","us","we","what","whatever","which","whichever","who","whoever","whom","whomever","whose","you","yours","yourself","yourselves","aboard","about","above","absent","according to","across","after","against","ahead","ahead of","all over","along","alongside","amid","amidst","among","amongst","anti","around","as","as of","as to","aside","astraddle","astride","at","away from","bar","barring","because of","before","behind","below","beneath","beside","besides","between","beyond","but","by","by the time of","circa","close by","close to","concerning","considering","despite","down","due to","during","except","except for","excepting","excluding","failing","following","for","for all","from","given","in","in between","in front of","in keeping with","in place of","in spite of","in view of","including","inside","instead of","into","less","like","minus","near","near to","next to","notwithstanding","of","off","on","on top of","onto","opposite","other than","out","out of","outside","over","past","pending","per","pertaining to","plus","regarding","respecting","round","save","saving","similar to","since","than","thanks to","through","throughout","thru","till","to","toward","towards","under","underneath","unlike","until","unto","up","up to","upon","versus","via","wanting","with","within","without","a","all","an","another","any","both","each","either","every","her","his","its","my","neither","no","other","our","per","some","that","the","their","these","this","those","whatever","whichever","your","accordingly","after","albeit","although",/*"and",*/"as","because","before","both","but","consequently","either","for","hence","however","if","neither","nevertheless","nor","once","or","since","so","than","that","then","thence","therefore","tho'","though","thus","till","unless","until","when","whenever","where","whereas","wherever","whether","while","whilst","yet","be able to","can","could","dare","had better","have to","may","might","must","need to","ought","ought to","shall","should","used to","will","would");
	sort($function_words, SORT_STRING);
	foreach($function_words as $key=>$fword)
		$function_words[$key] = strtolower($fword);
	
	$tags = split('[,]', $_POST['tags']);
	
	$chunks = split('[,.]', $descr);
	$chunks2 = array();
	foreach($chunks as $chunk) {
		$line = ereg_replace("^[a-z]?[a-z]?$", "", $chunk);
		$words = split(' ', $line);
		$left = array();
		foreach($words as $word) {
			$word = ereg_replace("[() ]", "", $word);
			if(!array_search($word, $function_words))
				array_push($left, $word);
		}
		array_push($chunks2, implode(" ", $left));
	}
	
	$_SESSION['google'] = array();
	$_SESSION['youtube'] = array();
	foreach($chunks2 as $descr)
		eval("?>".file_get_contents("search/google.php"));
	foreach($chunks2 as $descr)
		eval("?>".file_get_contents("search/youtube.php"));
} else if(isset($_SESSION['youtube']) && isset($_SESSION['google']) &&
			isset($_POST['course_name']) && isset($_POST['course_prof'])) {
	$crs = new Course($_POST['course_name'], $_POST['course_prof'],
					$_SESSION['google'], $_SESSION['youtube']);
	$crs->save();
} else if(isset($_GET['course'])) {
	$crs = new Course($_GET['course'], $_GET['prof'], null, null);
	$crs->load();
	$_SESSION['google'] = $crs->goog_res;
	$_SESSION['youtube'] = $crs->youtube_res;
} else $first_load = true;

$courses = Course::ListCourses();
?>
<p>List of courses:<br/>
<?php foreach($courses as $course) { ?>
	<a href="homepage.php?course=<?php echo urlencode($course[1]); ?>&prof=<?php echo urlencode($course[2]); ?>">
		<?php echo $course[1].", ".$course[2].", ".$course[3]; ?><br/>
	</a>
<?php	} ?>
</p>

<?php
if(!$first_load) {
	eval("?>".file_get_contents("search/google.view.php"));
	echo "<hr/>";
	eval("?>".file_get_contents("search/youtube.view.php"));
}
?>

</body>
</html>

<?php
database_close();
?>
