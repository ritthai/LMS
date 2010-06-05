<?php
include("includes/mysql.inc");
include("search/google.php");
include("search/youtube.php");
include("search/itunesu.php");
include("search/khanacad.php");

session_start();
database_connect();

$PAGE_REL_URL = $_SERVER["REQUEST_URI"];
$first_load = false;
$google_results = array();
$youtube_results = array();
$itunesu_results = array();
$khanacad_results = array();
$gcourse_code = "";
$gtitle = "";
$gtags = "";
$gdescr = "";

$ACTIONS = array('search' => new HttpAction($_SERVER["REQUEST_URI"], 'post', array('terms', 'tags')));

/**	
	Identify course, populate fields
*/
if($ACTIONS['search']->wasCalled()) {
	$params = $ACTIONS['search']->getParams();
	$gcourse_code = $gtitle = $params[0];
	$crs = new CourseDefn($gtitle);
	if(!$crs->load())
		Error::generate(Error::$PRIORITY['warn'], 'Course not found.');
	$gdescr = $crs->descr;
	
	$tags = split('[,]', $gtags);
	$procd_descr = process_description($gdescr);
	
	foreach($procd_descr as $descr) {
		$google_results = array_merge($google_results, google_search($descr));
		$youtube_results = array_merge($youtube_results, youtube_search($descr, $tags));
		$itunesu_results = array_merge($itunesu_results, itunesu_search($descr));
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

$PAGE_TITLE = "Homepage";
if($CONFIG['debug']) $PAGE_TITLE .= " - Debugging Mode";
if(!$first_load)
	$COURSE = array("title" => $gtitle,
			"code" => $gcourse_code,
			"descr" => $gdescr);
$COURSES = CourseDefn::ListAll();
$G_RESULTS = $google_results;
$YT_RESULTS = $youtube_results;
$iTU_RESULTS = $itunesu_results;
$KHANACAD_RESULTS = $khanacad_results;
//$FIRST_LOAD = $first_load;
eval("?>".file_get_contents("index.view.php"));

database_close();
?>
