<?php
//##copyright##

function lyrics_search($aQuery, $aFields, $aStart, $aLimit, &$aNumAll, $aWhere = '', $cond = 'AND')
{
	$iaCore = &iaCore::instance();
	$iaLyric = $iaCore->factoryPackage('lyric', 'lyrics');

	$ret = array();
	$match = array();

	// additional fields
	if ($aFields && is_array($aFields))
	{
		foreach ($aFields as $fname => $data)
		{
			if ('LIKE' == $data['cond'])
			{
				$data['val'] = "%{$data['val']}%";
			}

			// for multiple values, like combo or checkboxes
			if (is_array($data['val']))
			{
				if ('!=' == $data['cond'])
				{
					$data['cond'] = count($data['val']) > 1 ? 'NOT IN' : '!=';
				}
				else
				{
					$data['cond'] = count($data['val']) > 1 ? 'IN' : '=';
				}
				$data['val'] = count($data['val']) > 1 ? '(' . implode(',', $data['val']) . ')' : array_shift($data['val']);
			}
			else if (preg_match('/^(\d+)\s*-\s*(\d+)$/', $data['val'], $range))
			{
				// search in range
				$data['cond'] = sprintf('BETWEEN %d AND %d', $range[1], $range[2]);
				$data['val'] = '';
			}
			else
			{
				$data['val'] = "'" . iaSanitize::sql($data['val']) . "'";
			}

			$match[] = "t1.`{$fname}` {$data['cond']} {$data['val']} ";
		}
	}

	$lyrics = array();

	$lyrics = $match ? $iaLyric->getSearchLyrics($aStart, $aLimit, ' AND (' . implode(' ' . $cond . ' ', $match) . ')') : array();
	$aNumAll += $iaCore->iaDb->foundRows();

	foreach ($lyrics as $lyric)
	{
		$iaCore->iaSmarty->assign('lyric', $lyric);
		$lyricinfo = $iaLyric->goToItem(array('item' => $lyric));
		//$ret[] = $iaCore->iaSmarty->fetch(IA_PACKAGES . 'lyrics/templates/common/brief_article.tpl');
		$ret[] = sprintf('<p><a href="%s">%s</a></p>', $lyricinfo[0], $lyric['title']);
	}

	return $ret;
}

function artists_search($aQuery, $aFields, $aStart, $aLimit, &$aNumAll, $aWhere = '', $cond = 'AND')
{
	$iaCore = &iaCore::instance();
	$iaArtist = $iaCore->factoryPackage('artist', 'lyrics');

	$ret = array();

	$where = "`title` LIKE '%$aQuery%' OR `description` LIKE '%$aQuery%'";
	$artists = $iaCore->iaDb->all('sql_calc_found_rows `title`, `title_alias`', $where, $aStart, $aLimit, iaArtist::getTable());
	$aNumAll += $iaCore->iaDb->foundRows();

	foreach ($artists as $artist)
	{
		$artist_url = $iaCore->iaSmarty->ia_url(array('item' => $iaArtist->getItemName(), 'data' => $artist, 'type' => 'url'));
		$ret[] = sprintf('<p><a href="%s">%s</a></p>', $artist_url, $artist['title']);
	}

	return $ret;
}

function albums_search($aQuery, $aFields, $aStart, $aLimit, &$aNumAll, $aWhere = '', $cond = 'AND')
{
	$iaCore = &iaCore::instance();
	$iaAlbum = $iaCore->factoryPackage('album', 'lyrics');

	$ret = array();

	$where = "`title` LIKE '%$aQuery%' OR `description` LIKE '%$aQuery%'";
	$albums = $iaCore->iaDb->all('sql_calc_found_rows `title`, `title_alias`, `artist_alias`', $where, $aStart, $aLimit, iaAlbum::getTable());
	$aNumAll += $iaCore->iaDb->foundRows();

	foreach ($albums as $album)
	{
		$album_url = $iaCore->iaSmarty->ia_url(array('item' => $iaAlbum->getItemName(), 'data' => $album, 'type' => 'url'));
		$ret[] = sprintf('<p><a href="%s">%s</a></p>', $album_url, $album['title']);
	}

	return $ret;
}