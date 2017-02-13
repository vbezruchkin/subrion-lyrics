<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	iaCore::fields();
	$iaLyric = $iaCore->factoryModule('lyric', IA_CURRENT_PACKAGE);

	// get lyric fields
	$fields = iaField::filterFields($account_lyrics, $iaLyric->getItemName());
	$iaView->assign('fields', $fields);

	switch ($iaView->name())
	{
		case 'my_lyrics':

			// get artist lyrics
			$limit = 20;
			$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
			$start = (max($page, 1) - 1) * $limit;

			// get account lyrics
			$account_lyrics = $iaLyric->getLyricsByAccount($_SESSION['user']['id'], $start, $limit);
			$iaView->assign('account_lyrics', $account_lyrics);

			// get total number of account lyrics
			$total_lyrics = $iaLyric->getNumLyrics("AND `member_id` = '{$_SESSION['user']['id']}'");
			$iaView->assign('total_lyrics', $total_lyrics);

			// init iaAlbum class
			$iaAlbum = $iaCore->factoryModule('album', IA_CURRENT_PACKAGE);

			// get account albums
			$albums = $iaAlbum->getAlbumsByAccount($_SESSION['user']['id']);
			$iaView->assign('albums', $albums);

			$iaCore->startHook('phpMyLyricsBeforeStart', ['item' => $iaLyric->getItemName()]);

			$iaView->display('mylyrics');

			break;

		case 'album_view':

			if (isset($iaCore->requestPath[1]))
			{
				$album_alias = $iaCore->requestPath[1];
			}

			$iaItem = $iaCore->factory('item');
			$iaAlbum = $iaCore->factoryModule('album', IA_CURRENT_PACKAGE);

			$album = isset($album_alias) ? $iaAlbum->getAlbumByAlias($album_alias) : false;
			if (empty($album))
			{
				iaView::errorPage(iaView::ERROR_NOT_FOUND);
			}

			$iaCore->startHook('phpViewAlbumBeforeStart', ['listing' => $album['id'], 'item' => 'album']);

			$album['@view'] = true;
			$album['item'] = 'album';

			$iaCore->album = $album;

			$album_fav = $iaItem->updateItemsFavorites([$album], $iaAlbum->getItemName());
			unset($album);

			$album = $album_fav[0];

			$iaView->assign('album', $album);

			// get artist information
			$iaArtist = $iaCore->factoryModule('artist', IA_CURRENT_PACKAGE);

			$artist = $iaArtist->getArtist($album['id_artist']);
			$iaView->assign('artist', $artist);

			// get artist albums
			$albums = $iaAlbum->getAlbumsByArtist($album['id_artist'], "AND `id` <> '{$album['id']}'");
			$iaView->assign('albums', $albums);

			// get artist lyrics
			$iaLyric = $iaCore->factoryModule('lyric', IA_CURRENT_PACKAGE);
			$lyrics = $iaLyric->getLyricsByAlbum($album['id']);
			if ($lyrics)
			{
				$lyrics = $iaItem->updateItemsFavorites($lyrics, $iaLyric->getItemName());

				// filter fields
				iaCore::fields();
				$fields = iaField::filterFields($lyrics, $iaLyric->getItemName());
				$iaView->assign('fields', $fields);
			}
			$iaView->assign('lyrics', $lyrics);

			// count views
			$iaAlbum->incrementViewsCounter($album['id']);

			// breadcrumb formation
			iaBreadcrumb::add(_t('artists'), 'artists/');
			iaBreadcrumb::add($artist['title'], $iaCore->iaSmarty->ia_url([
					'type' => 'url',
					'item' => $iaArtist->getItemName(),
					'data' => $artist]
			));

			// set meta keywords and description
			$iaView->set('description', $album['meta_description']);
			$iaView->set('keywords', $album['meta_keywords']);

			$iaView->title($album['title']);

			$iaView->display('albumview');

			break;

		case 'lyric_view':

			if (empty($iaCore->requestPath))
			{
				iaView::errorPage(iaView::ERROR_NOT_FOUND);
			}

			$iaItem = $iaCore->factory('item');
			$iaLyric = $iaCore->factoryModule('lyric', IA_CURRENT_PACKAGE);

			// get genre by alias
			$lyric = isset($iaCore->requestPath[2]) ? $iaLyric->getLyric((int)$iaCore->requestPath[2]) : false;
			if (empty($lyric))
			{
				iaView::errorPage(iaView::ERROR_NOT_FOUND);
			}

			$iaCore->startHook('phpViewLyricBeforeStart', ['listing' => $lyric['id'], 'item' => $iaLyric->getItemName()]);

			$lyric['@view'] = true;
			$lyric['item'] = $iaLyric->getItemName();
			$iaCore->lyric = $lyric;

			$lyric_fav = $iaItem->updateItemsFavorites([$lyric], $iaLyric->getItemName());
			unset($lyric);

			$lyric = $lyric_fav[0];

			if ($lyric['body'])
			{
				$lyric['body'] = preg_replace('/<br>/i', "", $lyric['body']);
			}
			$iaView->assign('lyric', $lyric);

			if (IN_USER && $_SESSION['user']['id'] == $lyric['member_id'])
			{
				$actionUrls = [
					iaCore::ACTION_EDIT => $iaLyric->url(iaCore::ACTION_EDIT, $lyric),
//			iaCore::ACTION_DELETE => $iaLyric->url(iaCore::ACTION_DELETE, $lyric)
				];
				$iaView->assign('tools', $actionUrls);

				$iaItem->setItemTools([
					'title' => _t('edit_lyric'),
					'url' => $actionUrls[iaCore::ACTION_EDIT]
				]);
				/*
						$iaCore->setItemTools(array(
							'title' => _t('delete_listing'),
							'url' => $actionUrls[iaCore::ACTION_DELETE],
							'id' => 'remove_listing" onclick="return confirm(\''._t('do_you_really_want_to_delete_listing').'\')"',
							'js' => ''
						));
				*/
			}

			// get account info
			if ($lyric['member_id'] > 0)
			{
				$author = $iaDb->row('*', "`id`='{$lyric['member_id']}'", iaUsers::getTable());
				$iaView->assign('author', $author);
			}

			// get artist information
			$iaArtist = $iaCore->factoryModule('artist', IA_CURRENT_PACKAGE);
			$artist = $iaArtist->getArtist($lyric['id_artist']);
			$iaView->assign('artist', $artist);

			// get album information
			$iaAlbum = $iaCore->factoryModule('album', IA_CURRENT_PACKAGE);
			$album = $iaAlbum->getAlbum($lyric['id_album']);
			$iaView->assign('album', $album);

			// count views
			$iaLyric->incrementViewsCounter($lyric['id']);

			// breadcrumb formation
			iaBreadcrumb::add(_t('artists'), 'artists/');
			iaBreadcrumb::add($artist['title'], $iaCore->iaSmarty->ia_url([
					'type' => 'url',
					'item' => $iaArtist->getItemName(),
					'data' => $artist]
			));
			iaBreadcrumb::add($album['title'], $iaCore->iaSmarty->ia_url([
					'type' => 'url',
					'item' => $iaAlbum->getItemName(),
					'data' => $album]
			));

			// set meta keywords and description
			$iaView->set('description', isset($lyric['meta_description']) ? $lyric['meta_description'] : '');
			$iaView->set('keywords', isset($lyric['meta_keywords']) ? $lyric['meta_keywords'] : '');

			$iaView->title($lyric['title']);

			$iaView->display('lyricview');

			break;

		case 'lyrics_home':

			// display index file
			$iaView->display();

			break;
	}
}