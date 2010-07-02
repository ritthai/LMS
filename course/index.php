<?php
include("$ROOT/includes/mysql.inc");
include("dataacquisition/google.util.php");
include("dataacquisition/youtube.util.php");
include("dataacquisition/itunesu.util.php");
include("dataacquisition/khanacad.util.php");

session_start();
db_connect();

$PAGE_REL_URL = "$HTMLROOT/course";

$ACTIONS = array(	'search' => new HttpAction("$PAGE_REL_URL/search", 'get',
								array('terms', 'tags')),
					'list' => new HttpAction("$PAGE_REL_URL/list", 'get',
								array())
				);

$search_results = array();

/*foreach($ACTIONS as $key => $val)
	if($val->wasCalled())
		$action = $key;*/

/**	
	Identify course, populate fields
*/
if($ACTIONS['search']->wasCalled()) {
	$params = $ACTIONS['search']->getParams();
	$crs = new CourseDefn($params['terms']);
	if(!$crs->load())
		Error::generate(Error::$PRIORITY['warn'], 'Course not found.');
	
	$tags = split('[,]', $params['tags']);
	$procd_descr = process_description($crs->descr);
	
	foreach($procd_descr as $descr) {
		array_push(	$search_results,
					array(	'subject' => $descr,
							'google' => google_search($descr),
							'youtube' => youtube_search($descr, $tags),
							'itunesu' => itunesu_search($descr),
							'khanacad' => khanacad_search($descr)));
	}

	$args = array(	'pagetitle'		=> 'Search',
					'pageurl'		=> $_SERVER['REQUEST_URI'],
					'course'		=> array(	'title'	=> $crs->title,
												'code'	=> $crs->code,
												'descr' => $crs->descr),
					'searchresults'	=> $search_results,
					'actions'		=> $ACTIONS);
	if($CONFIG['debug']) $args['pagetitle'] .= ' - Debugging Mode';
	eval("?>".file_get_contents("views/search.view.php"));
} else if($ACTIONS['list']->wasCalled()) {
	$args = array(	'pagetitle'		=> 'List',
					'pageurl'		=> $_SERVER['REQUEST_URI'],
					'courses'		=> CourseDefn::ListAll(),
					'actions'		=> $ACTIONS);
	if($CONFIG['debug']) $args['pagetitle'] .= ' - Debugging Mode';
	eval("?>".file_get_contents("views/list.view.php"));
} else if(isset($_GET['action']) && $_GET['action'] != '') { // Action with no params
	$action = $_GET['action'];
	$args = array(	'pagetitle'		=> ucfirst($action),
					'pageurl'		=> $_SERVER['REQUEST_URI'],
					'actions'		=> $ACTIONS);
	switch($action) {
		case 'search':
		case 'list':
			eval('?>'.file_get_contents("views/$action.view.php"));
			break;
		default:
			Error::generate('suspicious', "Invalid action $action in /course/");
			header("Location: $PAGE_REL_URL");
			break;
	}
} else {
	$args = array(	'pagetitle'		=> 'Course Index',
					'pageurl'		=> $_SERVER['REQUEST_URI'],
					'courses'		=> CourseDefn::ListAll(),
					'actions'		=> $ACTIONS);
	if($CONFIG['debug']) $args['pagetitle'] .= ' - Debugging Mode';
	eval("?>".file_get_contents("views/index.view.php"));
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
*/

db_close();
?>
