<?php

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	// init iaItem class
	$iaItem = $iaCore->factory('item');

	// init main item classes
	$iaLyric = $iaCore->factoryModule('lyric', 'lyrics');
	$iaArtist = $iaCore->factoryModule('artist', 'lyrics');
	$iaAlbum = $iaCore->factoryModule('album', 'lyrics');

	$defaultLimit = $iaCore->get('lyrics_per_block', 5);

	// generate sql query to fetch listings
	$sql  = "SELECT t1.`id`, t1.`id_artist`, t1.`title`, t1.`title_alias`, t1.`date_added`, ";
	$sql .= "t1.`views_num`, t1.`artist_alias`, t1.`album_alias`, t2.`title` `artist_title`";
	$sql .= "FROM `" . iaLyric::getTable(true) . "` t1 ";
	$sql .= "LEFT JOIN `" . iaArtist::getTable(true) . "` AS t2 ";
	$sql .= "ON t1.`id_artist` = t2.`id` ";
	$sql .= "WHERE t1.`status`='active' ";

	// random lyrics
	if ($iaView->blockExists('random_lyrics'))
	{
		$max = (int)$iaDb->one('MAX(`id`) as `max`', null, iaLyric::getTable());
		$sql2 = $iaCore->iaDb->orderByRand($max, 't1.`id`') . " ORDER BY RAND() LIMIT 0, " . $defaultLimit;
		if ($data = $iaDb->getAll($sql . $sql2))
		{
			$iaView->assign('random_lyrics', $data);
		}
	}

	// latest lyrics
	if ($iaView->blockExists('latest_lyrics'))
	{
		$sql2  = "ORDER BY t1.`date_added` DESC LIMIT 0, ".$defaultLimit;
		$iaView->assign('latest_lyrics', $iaDb->getAll($sql.$sql2));
	}

	// popular lyrics
	if ($iaView->blockExists('popular_lyrics'))
	{
		$sql2  = "ORDER BY t1.`views_num` DESC LIMIT 0, ".$defaultLimit;
		$iaView->assign('popular_lyrics', $iaDb->getAll($sql.$sql2));
	}

	// new lyrics
	$new_lyrics = $iaLyric->getLyrics(false, 0, 50);
	if ($new_lyrics)
	{
		$new_lyrics = $iaItem->updateItemsFavorites($new_lyrics, 'lyrics');
	}
	else
	{
		iaLanguage::set('no_lyrics', str_replace('{%URL%}', $iaCore->packagesData['lyrics']['url'] . 'add/', iaLanguage::get('no_lyrics')));
	}
	$iaView->assign('new_lyrics', $new_lyrics);

	// genres
	$iaCore->iaDb->setTable('lyrics_genres');
	$genres_list = $iaCore->iaDb->all("*", "`status` = 'active' ORDER BY `title` ASC");
	$iaCore->iaDb->resetTable();
	$iaView->assign('genres_list', $genres_list);

	// top artists
	if ($iaView->blockExists('top_artists'))
	{
		$top_artists = $iaArtist->getTopArtists();
		$iaView->assign('top_artists', $top_artists);
	}

	// new artists
	if ($iaView->blockExists('new_artists'))
	{
		$new_artists = $iaArtist->getLatestArtists();
		$iaView->assign('new_artists', $new_artists);
	}

	// top albums
	if ($iaView->blockExists('top_albums'))
	{
		$top_albums = $iaAlbum->getTopAlbums();
		$iaView->assign('top_albums', $top_albums);
	}

	// new albums
	if ($iaView->blockExists('new_albums'))
	{
		$new_albums = $iaAlbum->getLatestAlbums();
		$iaView->assign('new_albums', $new_albums);
	}
}