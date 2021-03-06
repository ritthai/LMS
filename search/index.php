<?php
include("$ROOT/includes/mysql.inc");
include("dataacquisition/google.util.php");
include("dataacquisition/youtube.util.php");
include("dataacquisition/itunesu.util.php");
include("dataacquisition/khanacad.util.php");

session_start();
database_connect();

$ACTIONS = array('search' => new HttpAction($_SERVER["REQUEST_URI"], 'post', array('terms', 'tags')));
$first_load = false;

$search_results = array();
$gcourse_code = "";
$gtitle = "";
$gtags = "";
$gdescr = "";

/*foreach($ACTIONS as $key => $val)
	if($val->wasCalled())
		$action = $key;*/

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
		array_push(	$search_results,
					array(	'subject' => $descr,
							'google' => google_search($descr),
							'youtube' => youtube_search($descr, $tags),
							'itunesu' => itunesu_search($descr),
							'khanacad' => khanacad_search($descr)));
	}
}
/* else if(isset($_POST['save_name'])) {
	$crs = new Course(	urlencode($_POST['save_name']), urlencode($_POST['course_prof']),
						$_SESSION['google'], $_SESSION['youtube']);
	$crs->save();
} else if(isset($_GET['course'])) {
	$crs = new Course($_GET['course'], $_GET['prof'], null, null);
	$crs->load();
	$_SESSION['google'] = $crs->goog_res;
	$_SESSION['youtube'] = $crs->youtube_res;
} else $first_load = true;
*/

$args = array(	'pagetitle'		=> 'Homepage',
				'pageurl'		=> $_SERVER['REQUEST_URI'],
				'course'		=> array(	'title'	=> $gtitle,
											'code'	=> $gcourse_code,
											'descr' => $gdescr),
				'courses'		=> CourseDefn::ListAll(),
				'searchresults'	=> $search_results,
				'actions'		=> $ACTIONS);
if($CONFIG['debug']) $args['pageurl'] .= ' - Debugging Mode';
eval("?>".file_get_contents("views/index.view.php"));

database_close();
?>
