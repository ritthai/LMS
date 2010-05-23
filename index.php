<?php
include("includes/config.inc");
include("includes/mysql.inc");
include("includes/misc.inc");
include("classes/course.class.php");
include("classes/coursedefn.class.php");
include("search/google.php");
include("search/youtube.php");
include("search/itunesu.php");
include("search/khanacad.php");

session_start();
database_connect();

$first_load = false;
$google_results = array();
$youtube_results = array();
$itunesu_results = array();
$khanacad_results = array();
$gcourse_code = "";
$gtitle = "";
$gdescr = "";
$gtags = "";

/**	
	Identify course, populate fields
*/
if(isset($_POST['terms'])) {
	$gcourse_code = $_POST['terms'];
	$crs = new CourseDefn($_POST['terms']);
	if(!$crs->load())
		echo "Course not found<br/>";
	$_POST['descr'] = $crs->descr;
	$gtitle = $crs->title;
}
if(isset($_POST['tags']))
	$gtags = $_POST['tags'];
if(isset($_POST['descr'])) {
	$gdescr = ereg_replace("\\\\", "", $_POST['descr']);
	$tags = split('[,]', $_POST['tags']);
	$procd_descr = process_description($gdescr);
	
	foreach($procd_descr as $descr) {
if(!$CONFIG_DEBUG) {
		$google_results = array_merge($google_results, google_search($descr));
		$youtube_results = array_merge($youtube_results, youtube_search($descr, $tags));
		$itunesu_results = array_merge($itunesu_results, itunesu_search($descr));
}
		$khanacad_results = array_merge($khanacad_results, khanacad_search($descr));
	}
} else if(isset($_POST['save_name'])) {
	$crs = new Course(urlencode($_POST['save_name']), urlencode($_POST['course_prof']),
					$_SESSION['google'], $_SESSION['youtube']);
	$crs->save();
} else if(isset($_GET['course'])) {
	$crs = new Course($_GET['course'], $_GET['prof'], null, null);
	$crs->load();
	$_SESSION['google'] = $crs->goog_res;
	$_SESSION['youtube'] = $crs->youtube_res;
} else $first_load = true;

$PAGE_REL_URL = "index.php";
$PAGE_TITLE = "Homepage";
if($CONFIG_DEBUG) $PAGE_TITLE .= " - Debugging Mode";
if(!$first_load)
	$COURSE = array("title" => $gtitle,
			"code" => $gcourse_code,
			"descr" => $gdescr);
$COURSES = Course::ListCourses();
$G_RESULTS = $google_results;
$YT_RESULTS = $youtube_results;
$iTU_RESULTS = $itunesu_results;
$KHANACAD_RESULTS = $khanacad_results;
//$FIRST_LOAD = $first_load;
eval("?>".file_get_contents("index.view.php"));

database_close();
?>
