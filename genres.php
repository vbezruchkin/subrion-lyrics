<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaGenre = $iaCore->factoryPackage('genre', IA_CURRENT_PACKAGE);

	iaCore::fields();

	switch ($iaView->name())
	{
		case 'genre_view':

			if (empty($iaCore->requestPath))
			{
				iaView::errorPage(iaView::ERROR_NOT_FOUND);
			}

			$iaItem = $iaCore->factory('item');
			$iaGenre = $iaCore->factoryPackage('genre', IA_CURRENT_PACKAGE);

			// get genre by alias
			$genre = isset($iaCore->requestPath[0]) ? $iaGenre->getGenreByAlias($iaCore->requestPath[0]) : false;
			if (empty($genre))
			{
				iaView::errorPage(iaView::ERROR_NOT_FOUND);
			}

			$iaCore->startHook('phpViewGenreBeforeStart', array('listing' => $genre['id'], 'item' => $iaGenre->getItemName()));

			$genre['@view'] = true;

			$iaCore->startHook('phpViewListingBeforeStart', array(
				'listing' => $genre['id'],
				'item' => $iaGenre->getItemName(),
				'title' => $genre['title'],
				'desc' => $genre['description'],
			));

			// get sections
			$genre['item'] = $iaGenre->getItemName();
			$iaCore->genre = $genre;
			$iaView->assign('genre', $genre);

			// increment views counter
			$iaGenre->incrementViewsCounter($genre['id']);

			// get artists by genre
			$iaArtist = $iaCore->factoryPackage('artist', IA_CURRENT_PACKAGE);

			$artists = $iaArtist->getArtistsByGenre($genre['id']);
			if ($artists)
			{
				// update favorites icon
				$artists = $iaItem->updateItemsFavorites($artists, $iaArtist->getItemName());

				// filter fields
				$fields = iaField::filterFields($artists, $iaArtist->getItemName());
				$iaView->assign('fields', $fields);
			}
			$iaView->assign('artists', $artists);

			// breadcrumb formation
			iaBreadcrumb::add(_t('genres'), $iaCore->packagesData['lyrics']['url'] . 'genres/');
			iaBreadcrumb::replaceEnd($genre['title'], '');

			// set meta keywords and description
			$iaView->set('description', $genre['meta_description']);
			$iaView->set('keywords', $genre['meta_keywords']);

			$iaView->title($genre['title']);

			$iaView->display('genreview');

			break;

		case 'genres':

			$per_page = $iaCore->get('lyrics_per_page', 10);
			$page = isset($_GET['page']) && 1 < $_GET['page'] ? (int)$_GET['page'] : 1;
			$start = ($page - 1) * $per_page;
			$where = false;

			// gets current page and defines start position
			$num_index = 20;
			$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
			$start = (max($page, 1) - 1) * $num_index;

			$search_alphas = array('0-9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
			$alpha = (isset($iaCore->requestPath[0]) && in_array($iaCore->requestPath[0], $search_alphas)) ? $iaCore->requestPath[0] : false;
			$cause = $alpha ? ('0-9' == $alpha ?  "(`$account_by` REGEXP '^[0-9]') AND " : "(`$account_by` LIKE '{$alpha}%') AND ") : '';

			// get genres
			$genres = $iaGenre->getGenres($where, $start, $per_page);

			// get total number of genres
			$genres_total = $iaGenre->iaDb->foundRows();

			if ($genres)
			{
				$iaItem = $iaCore->factory('item');
				$genres = $iaItem->updateItemsFavorites($genres, 'genres');

				// filter fields
				$fields = iaField::filterFields($genres, 'genres');
				$iaView->assign('fields', $fields);
			}

			$iaView->assign('genres', $genres);
			$iaView->assign('aTotal', $genres_total);
			$iaView->assign('aItemsPerPage', $per_page);
			$iaView->assign('aTemplate', IA_URL . 'lyrics/?page={page}');

			break;

		default:
			break;
	}
}