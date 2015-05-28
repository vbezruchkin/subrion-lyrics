<?php 
//##copyright##

$iaCore->startHook('phpAdminLyricsHomeBeforeCode', array(), 1);

/* initialization all values */
$action = (isset($_GET['action']) ? $_GET['action'] : 'none');

// TODO: add permissions
// get statistics for items
$stats['items'] = array('genres', 'artists', 'albums', 'lyrics');
foreach($stats['items'] as $item)
{
	// TODO: fetch correct class by item name
	$iaDb->setTable('lyrics_'.$item);
	
	$status_stats = $iaDb->keyvalue("`status`, count(id)", '1=1 GROUP BY `status`');
	$stats[$item]['active'] = isset($status_stats['active']) ? $status_stats['active'] : 0;
	$stats[$item]['inactive'] = isset($status_stats['inactive']) ? $status_stats['inactive'] : 0;
	
	// get total stats by item
	$stats[$item]['total'] = $iaDb->one("count(*)");
	
	$iaDb->resetTable();
}
$iaView->assign('stats', $stats);

// return lyrics statistics chart data
if('getlyricschart' == $action && iaView::REQUEST_JSON == $iaView->getRequestType())
{
	$iaView->assign('', array('statuses' => 'Active', 'total' => $stats['lyrics']['active']), 'ajax');
	$iaView->assign('', array('statuses' => 'Approval', 'total' => $stats['lyrics']['approval']), 'ajax');
}

$iaCore->startHook('phpAdminLyricsHomeAfterCode');