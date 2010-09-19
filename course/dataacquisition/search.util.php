<?php
@include("google.util.php");
@include("youtube.util.php");
@include("itunesu.util.php");
@include("wikipedia.util.php");
@include("khanacad.util.php");

/**
	How to add a data source:
		- add a <datasource>.util.php file under /course/dataacquisition/ using other files in that directory as a template
		- modify this file ( /course/dataacquisition/search.util.php ) to handle perform and optionally prefetch requests
		- modify the controller to call perform_search and optionally prefetch_search when loading an uncached page
		- modify the view, using code for other data sources as a template
*/

function prefetch_search($service, $descr, $tags, $crs) {
	Error::generate('debug', "preload_search($service,$descr,$tags,crs)");
	switch(strtolower($service)) {
	case 'youtube':
		return prefetch_youtube_search($descr, $tags, $crs);
	case 'google':
		return prefetch_google_search($descr);
	case 'itunesu':
		return prefetch_itunesu_search($descr);
	case 'wikipedia':
		return prefetch_wikipedia_search($descr);
	case 'khanacad':
		return prefetch_khanacad_search($descr);
	}
}
function perform_search($service, $descr, $tags, $crs) {
	Error::generate('debug', "perform_search($service,$descr,$tags,crs)");
	switch(strtolower($service)) {
	case 'youtube':
		return youtube_search($descr, $tags, $crs);
	case 'google':
		return google_search($descr);
	case 'itunesu':
		return itunesu_search($descr);
	case 'wikipedia':
		return wikipedia_search($descr);
	case 'khanacad':
		return khanacad_search($descr);
	}
}
