<?php
@include("$ROOT/includes/tags.inc");
@include("dataacquisition/google.util.php");
@include("dataacquisition/youtube.util.php");
@include("dataacquisition/itunesu.util.php");
@include("dataacquisition/khanacad.util.php");
@include("$ROOT/includes/subjects.inc");
@include("$ROOT/includes/universities.inc");
@include("$ROOT/includes/geography.inc");

db_connect();

Error::showSeparator();
Error::generate('debug', 'Starting preload process');
Error::showSeparator();

foreach(CourseDefn::ListAll() as $crs) {
	Error::showSeparator();
	Error::generate('debug', 'Preloading '.$crs->university.': '.$crs->code);
	// TODO: preload
	Error::showSeparator();
}

Error::showSeparator();
Error::generate('debug', 'Ending preload process');
Error::showSeparator();

