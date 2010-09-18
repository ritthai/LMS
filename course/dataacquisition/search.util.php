<?php
@include("google.util.php");
@include("youtube.util.php");
@include("itunesu.util.php");
@include("khanacad.util.php");

function prefetch_search($service, $descr, $tags, $crs) {
	Error::generate('debug', "preload_search($service,$descr,$tags,crs)");
	switch(strtolower($service)) {
	case 'youtube':
		return prefetch_youtube_search($descr, $tags, $crs);
	case 'google':
		return prefetch_google_search($descr);
	case 'itunesu':
		return prefetch_itunesu_search($descr);
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
	case 'khanacad':
		return khanacad_search($descr);
	}
}
