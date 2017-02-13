<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaItem = $iaCore->factory('item');
	$iaArtist = $iaCore->factoryModule('artist', IA_CURRENT_PACKAGE);

	// initialize fields class
	iaCore::fields();

	switch ($iaView->name())
	{
		case 'artist_view':

			if (isset($iaCore->requestPath[0]))
			{
				$artist_alias = $iaCore->requestPath[0];
				// TODO: perform alias validation
			}

			$artist = isset($artist_alias) ? $iaArtist->getArtistByAlias($artist_alias) : false;
			if (empty($artist))
			{
				iaView::errorPage(iaView::ERROR_NOT_FOUND);
			}

			$iaCore->startHook('phpViewArtistBeforeStart', ['listing' => $artist['id'], 'item' => $iaArtist->getItemName()]);
			$artist['@view'] = true;
			$artist['item'] = $iaArtist->getItemName();

			$iaCore->artist = $artist;

			$iaView->assign('artist', $artist);

			// increment views counter
			$iaArtist->incrementViewsCounter($artist['id']);

			// get artist albums
			$iaAlbum = $iaCore->factoryModule('album', IA_CURRENT_PACKAGE);
			$albums = $iaAlbum->getAlbumsByArtist($artist['id']);
			// update favorites icon
			if ($albums)
			{
				$albums = $iaItem->updateItemsFavorites($albums, $iaAlbum->getItemName());
			}
			$iaView->assign('albums', $albums);

			// get artist lyrics
			$lyric_limit = 20;
			$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
			$lyric_start = (max($page, 1) - 1) * $lyric_limit;

			$iaLyric = $iaCore->factoryModule('lyric', IA_CURRENT_PACKAGE);
			$lyrics = $iaLyric->getLyricsByArtist($artist['id'], $lyric_start, $lyric_limit);
			$total_lyrics = $iaLyric->getNumLyrics("AND `id_artist` = '{$artist['id']}'");

			if ($lyrics)
			{
				$lyrics = $iaItem->updateItemsFavorites($lyrics, $iaLyric->getItemName());

				// filter fields
				$fields = iaField::filterFields($lyrics, 'lyrics');
				$iaView->assign('fields', $fields);
			}
			$iaView->assign('lyrics', $lyrics);

			$iaView->assign('total_lyrics', $total_lyrics);

			// breadcrumb formation
			iaBreadcrumb::add(_t('artists'), 'artists/');

			// set meta keywords and description
			$iaView->set('description', $artist['meta_description']);
			$iaView->set('keywords', $artist['meta_keywords']);

			$iaView->title($artist['title']);

			$iaView->display('artistview');

			break;
		case 'artists':

			// gets current page and defines start position
			$per_page = $iaCore->get('artists_per_page', 20);
			$page = isset($_GET['page']) && 1 < $_GET['page'] ? (int)$_GET['page'] : 1;
			$start = ($page - 1) * $per_page;

			$search_alphas = iaUtil::getLetters();
			$alpha = (isset($iaCore->requestPath[0]) && in_array($iaCore->requestPath[0], $search_alphas)) ? $iaCore->requestPath[0] : false;
			$cause = $alpha ? ('0-9' == $alpha ?  "(`title` REGEXP '^[0-9]') AND " : "(`title` LIKE '{$alpha}%') AND ") : '';

			$iaView->assign('search_alphas', $search_alphas);
			$iaView->assign('alpha', $alpha);

			$iaArtist->where = $cause . "`status`='active' ORDER BY `title` ASC ";
			$artists = $iaArtist->getArtists(false, $start, $per_page);
			$total_artists = $iaArtist->count;

			if ($artists)
			{
				$iaItem = $iaCore->factory('item');
				$artists = $iaItem->updateItemsFavorites($artists, $iaArtist->getItemName());

				// filter fields
				$fields = iaField::filterFields($artists, 'artists');
				$iaView->assign('fields', $fields);
			}

			$iaView->assign('artists', $artists);
			$iaView->assign('aTotal', $total_artists);
			$iaView->assign('aItemsPerPage', $per_page);

			if ($alpha)
			{
				iaBreadcrumb::add(_t('artists'), IA_PACKAGE_URL . 'artists/');
				iaBreadcrumb::replaceEnd($alpha, '');

				$iaView->assign('aTemplate', IA_URL . 'artists/' . $alpha . '/?page={page}');
			}
			else
			{
				$iaView->assign('aTemplate', IA_URL . 'artists/?page={page}');
			}

			break;
		default:
			break;
	}
}