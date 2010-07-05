<?php
@include("$ROOT/includes/mysql.inc");
@include("$ROOT/includes/tags.inc");
@include("$ROOT/includes/prefix.inc");
@include("dataacquisition/google.util.php");
@include("dataacquisition/youtube.util.php");
@include("dataacquisition/itunesu.util.php");
@include("dataacquisition/khanacad.util.php");

session_start();
db_connect();

controller_prefix();

$CONTROLLER = 'course';
$PAGE_REL_URL = "$HTMLROOT/course";

$ACTIONS = array(	'search'	=> new HttpAction("$PAGE_REL_URL/search", 'get',
													array('terms', 'tags')),
					'list'		=> new HttpAction("$PAGE_REL_URL/list", 'get',
													array()),
					'show'		=> new HttpAction("$PAGE_REL_URL/show", 'get',
													array('id')),
					'list2'		=> new HttpAction("$PAGE_REL_URL/show", 'get',
													array())
				);

$search_results = array();

$action = false;
$params = array();
foreach($ACTIONS as $key => $val) {
	if($val->wasCalled()) {
		if(!$action) $action = $key;
		$params = array_merge($params, $ACTIONS[$action]->getParams());
		break;
	}
}
if($action == 'list2') $action = $list;

/**	
	Identify course, populate fields
*/
if($action == 'show') {
	$crs = new CourseDefn((int)$params['id']);
	if(!$crs->load()) {
		Error::generate(Error::$PRIORITY['warn'], 'Course not found.');
		redirect('search');
	} else {
		$procd_descr = process_description($crs->descr);
		$tags = split('[,]', get_tags($crs));

		foreach($procd_descr as $descr) {
			if($descr == ' ') continue;
			$descr = ereg_replace('[^A-Za-z0-9& -]', '', $descr);
			array_push(	$search_results,
					array(	'subject' => $descr,
							'google' => google_search($descr),
							'youtube' => youtube_search($descr, $tags, $crs),
							'itunesu' => itunesu_search($descr),
							'khanacad' => khanacad_search($descr)));
		}

		$args = array(	'pagetitle'		=> 'Show',
						'pageurl'		=> $_SERVER['REQUEST_URI'],
						'course'		=> array(	'id'	=> $crs->id,
													'title'	=> $crs->title,
													'code'	=> $crs->code,
													'descr' => $crs->descr),
						'searchresults'	=> $search_results,
						'actions'		=> $ACTIONS);
		include("views/show.view.php");
	}
} else if($action == 'search') {
	$crs = new CourseDefn($params['terms']);
	if(!$crs->load())
		Error::generate(Error::$PRIORITY['warn'], 'Course not found.');
	
	//$tags = split('[,]', $params['tags']);
	$tags = split('[,]', get_tags($crs->code));
	$procd_descr = process_description($crs->descr);
	
	foreach($procd_descr as $descr) {
		if($descr == ' ') continue;
		$descr = ereg_replace('[^A-Za-z0-9 -]', '', $descr);
		array_push(	$search_results,
					array(	'subject' => $descr,
							'google' => google_search($descr),
							'youtube' => youtube_search($descr, $tags, $crs),
							'itunesu' => itunesu_search($descr),
							'khanacad' => khanacad_search($descr)));
	}

	$args = array(	'pagetitle'		=> 'Search',
					'pageurl'		=> $_SERVER['REQUEST_URI'],
					'course'		=> array(	'id'	=> $crs->id,
												'title'	=> $crs->title,
												'code'	=> $crs->code,
												'descr' => $crs->descr),
					'searchresults'	=> $search_results,
					'actions'		=> $ACTIONS);
	include("views/search.view.php");
} else if($action == 'list') {
	$args = array(	'pagetitle'		=> 'List',
					'pageurl'		=> $_SERVER['REQUEST_URI'],
					'courses'		=> CourseDefn::ListAll(),
					'actions'		=> $ACTIONS);
	include("views/list.view.php");
} else if(isset($_GET['action']) && $_GET['action'] != '') { // Action with no params
	$action = $_GET['action'];
	$args = array(	'pagetitle'		=> ucfirst($action),
					'pageurl'		=> $_SERVER['REQUEST_URI'],
					'actions'		=> $ACTIONS);
	switch($action) {
		case 'search':
		case 'list':
			include("views/$action.view.php");
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
	include("views/index.view.php");
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
