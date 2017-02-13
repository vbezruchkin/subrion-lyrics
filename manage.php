<?php
//##copyright##

$iaLyric = $iaCore->factoryModule('lyric', IA_CURRENT_PACKAGE);
$iaArtist = $iaCore->factoryModule('artist', IA_CURRENT_PACKAGE);
$iaAlbum = $iaCore->factoryModule('album', IA_CURRENT_PACKAGE);

// process ajax actions
if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	// artists return
	if (isset($_GET['q']))
	{
		$where = "`title` LIKE '{$_GET['q']}%' ";
		$order = "ORDER BY `title` ASC ";

		$artists['options'] = $iaDb->onefield('title', $where . $order, 0, 15, iaArtist::getTable());
		$iaView->assign($artists);
	}

	// albums return
	if (isset($_GET['artist']))
	{
		// get artist by title
		$artist = $iaArtist->getArtistByTitle($_GET['artist']);

		// get artist albums
		$out['data'] = $iaAlbum->getAlbumsByArtist($artist['id']);

		$iaView->assign($out);
	}
}

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	iaCore::fields();
	$iaUtil = $iaCore->factory('util');

	$errorFields = [];

	$id = isset($iaCore->requestPath[0]) ? (int)$iaCore->requestPath[0] : false;
	$lyric = $id ? $iaDb->row('*, \'lyrics\' as `item`', "`id`={$id}", 0, 1, iaLyric::getTable()) : [];

	if (!empty($id) && empty($lyric))
	{
		iaView::errorPage(iaView::ERROR_NOT_FOUND);
	}
	elseif (!empty($id) && $_SESSION['user']['id'] != $lyric['member_id'])
	{
		iaView::errorPage(iaView::ERROR_FORBIDDEN);
	}

	if (!empty($lyric))
	{
		// get lyric artist
		$lartist = $iaArtist->getArtist($lyric['id_artist']);
		$lyric['artist'] = $lartist['title'];

		// get lyric album
		$lalbum = $iaAlbum->getAlbum($lyric['id_album']);
		$lyric['album'] = $lalbum['title'];

		// get artist albums
		$albums = $iaAlbum->getAlbumsByArtist($lartist['id']);
		$iaView->assign('albums', $albums);

		if ($lyric['body'])
		{
			$lyric['body'] = preg_replace('/<br>/i', "", $lyric['body']);
		}
	}

	if (!$id)
	{
		$fields = iaField::filterFieldsByGroup($lyric, $iaLyric->getItemName());
	}
	else
	{
		$fields = iaField::getAcoFieldsList(false, 'lyrics', false, true);
	}

	if (!empty($_POST))
	{
		$data = [];

		if ($fields)
		{
			list($data, $error, $messages, $errorFields) = iaField::parsePost($fields, $lyric);
		}

		$artist = isset($_POST['artist']) && !empty($_POST['artist'])? $_POST['artist'] : 0;
		if (empty($artist))
		{
			$error = true;
			$messages[] = _t('artist_empty', 'Please choose an artist');
		}
		else
		{
			$artist = $iaArtist->getArtistByTitle($artist);

			$data['id_artist'] = $artist['id'];
			$data['artist_alias'] = $artist['title_alias'];
		}

		$data['id_album'] = isset($_POST['album']) && !empty($_POST['album']) ? (int)$_POST['album'] : 0;
		if (empty($data['id_album']))
		{
			$error = true;
			$messages[] = _t('album_empty', 'Please choose an album');
		}
		else
		{
			$album = $iaAlbum->getAlbum($data['id_album']);

			$data['album_alias'] = $album['title_alias'];
		}

		if (!empty($data['title']))
		{
			$data['title_alias'] = iaSanitize::convertStr($data['title']);
		}

		if (!$error)
		{
			$iaCore->startHook("beforeLyricSubmit");

			$data['member_id'] = (int)$_SESSION['user']['id'];

			$dmsg = '';
			if ($iaCore->get('lyrics_auto_approval') || $action == 'deleted')
			{
				$data['status'] = 'active';
			}
			else
			{
				$data['status'] = 'approval';
				$dmsg = '_apporval';
			}

			if (empty($id))
			{
				$action = 'added';

				$data['id'] = $iaLyric->add($data);
			}
			else
			{
				if (isset($_POST['delete_lyric']))
				{
					$action = 'deleted';
					$data['id'] = 0;

					$iaLyric->delete('`id` = ' . $lyric['id']);
				}
				else
				{
					$action = 'updated';
					$data['id'] = $lyric['id'];

					$iaLyric->update($data);
				}
			}

			if (!$error)
			{
				$messages[] = _t('lyric_' . $action . $dmsg);

				iaUtil::redirect(_t('thanks'), $messages, $iaLyric->url('view', $data));
			}
		}

		if (isset($_POST['ajax']))
		{
			header('Content-type: text/xml');
			echo '<?xml version="1.0" encoding="' . $iaCore->get('charset') . '" ?>'
				. '<root><error>' . $error . '</error><msg><![CDATA[<li>' . implode('</li><li>', $messages) . ']]></msg></root>';
			exit;
		}
	}

	$itemInfo = false;
	$sections = iaField::getAcoGroupsFields(false, 'lyrics', '', $itemInfo);
	$iaView->assign('sections', $sections);

	$iaView->assign('fields', $fields);
	$iaView->assign('error_fields', $errorFields);
	$iaView->assign('item', $lyric);

	$iaView->display('manage');
}