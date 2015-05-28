<?php
//##copyright##

$iaLyric = $iaCore->factoryPackage('lyric', IA_CURRENT_PACKAGE, iaCore::ADMIN);

// process ajax actions
if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	if (isset($_GET['action']))
	{
		$action = $_GET['action'];

		switch ($action)
		{
			// return lyrics by account used on recipes page
			case 'getlyrics':

				// get account id by username
				$where = '';
				if ($_GET['account'])
				{
					$member_id = $iaDb->one('id', "`username` = '{$_GET['account']}' ", iaUsers::getTable());

					$where .= 'OR `member_id` = '.$member_id;
				}

				// get lyrics for account
				$lyrics = $iaLyric->getLyrics($where, 0, '', "`global` DESC, `title` ASC ");

				$out['data'] = $lyrics;

				break;

			// return title_alias for a lyric
			case 'getalias':

				$out['data'] = '';
				$title = isset($_GET['title']) ? $_GET['title'] : '';

				$iaUtil = iaCore::util();
				if (!defined('IA_NOUTF'))
				{
					iaUtf8::loadUTF8Core();
					iaUtf8::loadUTF8Util('ascii', 'utf8_to_ascii');
				}

				if (!utf8_is_ascii($title))
				{
					$title = utf8_to_ascii($title);
				}
				$title = iaSanitize::convertStr($title).IA_URL_DELIMITER;

				$out['data'] = $iaLyric->url('view', array('title_alias' => $title));

				break;

			// return grid records
			case 'get':

				$out = array('data' => '', 'total' => 0);

				$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
				$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
				$sort = $_GET['sort'];
				$order = '';

				$dir = in_array($_GET['dir'], array('ASC', 'DESC')) ? $_GET['dir'] : 'ASC';

				if (!empty($sort) && !empty($dir))
				{
					$order = '`'.$sort.'` '.$dir;
				}

				$where = array('1 = 1');
				$values = array();
				if (isset($_GET['status']) && in_array($_GET['status'], $iaLyric->_statuses))
				{
					$where[] = "`t1`.`status` = :status";
					$values['status'] = $_GET['status'];
				}
				elseif (isset($_SESSION['lyric_status']))
				{
					$where[] = "`t1`.`status` = :status";
					$values['status'] = $_SESSION['lyric_status'];
				}

				if (isset($_GET['title']) && !empty($_GET['title']))
				{
					$where[] = "`t1`.`title` LIKE :title";
					$values['title'] = '%'.$_GET['title'].'%';
				}

				if (isset($_GET['artist']) && !empty($_GET['artist']))
				{
					$iaArtist = $iaCore->factoryPackage('artist', IA_CURRENT_PACKAGE, iaCore::ADMIN);
					$artist_id = $iaArtist->iaDb->one('id', "`title` LIKE '%{$_GET['artist']}%' ", 0, null, $iaArtist->getTable());

					$where[] = "t1.`id_artist` = :id_artist";
					$values['id_artist'] = $artist_id;
				}


				$where = implode(" AND ", $where);
				$iaLyric->iaDb->mysql_bind($where, $values);

				$out['data'] = $iaLyric->getLyrics($where, $start, $limit, $order);
				$out['total'] = $iaLyric->iaDb->foundRows();

				$iaView->assign_all($out);

				break;

			default:
				break;
		}
	}

	// process grid actions
	if (!isset($out))
	{
		$iaView->assign_all($iaLyric->gridActions($_GET));
	}
}

// process html page actions
if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaArtist = $iaCore->factoryPackage('artist', IA_CURRENT_PACKAGE, iaCore::ADMIN);
	$iaAlbum = $iaCore->factoryPackage('album', IA_CURRENT_PACKAGE, iaCore::ADMIN);

	// init classes
	$iaFields = iaCore::fields();

	$error = false;
	$errorFields = array();
	$messages = array();

	switch ($pageAction)
	{
		default:

			$iaView->grid('_IA_URL_packages/lyrics/js/admin/lyrics');

			break;

		case 'edit':

		case 'add':

			iaBreadcrumb::add(iaLanguage::get('lyrics'), IA_ADMIN_URL . 'lyrics/lyrics/');

			// these fields are system and used in system template
			$item = array('status' => 'active', 'account_username' => $_SESSION['user']['username'], 'featured' => false);

			if ('edit' == $pageAction)
			{
				$item = $iaLyric->getById((int)$_GET['id']);

				if (empty($item))
				{
					iaView::errorPage(iaView::ERROR_NOT_FOUND);
				}

				// get albums
				$albums = $iaAlbum->getAlbumsByArtistId($item['id_artist']);
				$iaView->assign('albums', $albums);
			}

			$fields = iaField::getAllFields(true, '', 'lyrics');

			if (isset($_POST['save']))
			{
				iaCore::util();

				if ($fields)
				{
					list($data, $error, $messages, $errorFields) = iaField::parsePost($fields, $item, true);
				}

				// validate account
				if (isset($_POST['account']) && !empty($_POST['account']))
				{
					$member_id = $iaDb->one('id', "`username` = '{$_POST['account']}' ", iaUsers::getTable());

					if (!$member_id)
					{
						$error = true;
						$messages[] = iaLanguage::get('lyric_incorrect_account');
					}
					else
					{
						$data['member_id'] = $member_id;
					}
				}
				else
				{
					$data['member_id'] = iaUsers::getIdentity()->id;
				}

				if (!defined('IA_NOUTF'))
				{
					iaUtf8::loadUTF8Core();
					iaUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
				}

				// validate title_alias
				$data['title_alias'] = !empty($_POST['title_alias']) ? $_POST['title_alias'] : $_POST['title'];
				if (!utf8_is_ascii($data['title_alias']))
				{
					$data['title_alias'] = utf8_to_ascii($data['title_alias']);
				}
				$data['title_alias'] = iaSanitize::convertStr($data['title_alias']);

				// check for duplicate title_alias in case a new lyric is added or title_alias has been updated
				if (!isset($item['title_alias']) || (isset($item['title_alias']) && $data['title_alias'] != $item['title_alias']))
				{
					if ($iaLyric->existsAlias($data['title_alias']))
					{
						$error = true;
						$messages[] = iaLanguage::get('lyric_already_exists');
					}
				}

				if (!empty($_POST['artist']))
				{
					$artist_info = $iaArtist->getArtistByTitle($_POST['artist']);

					$data['id_artist'] = $artist_info['id'];
					$data['artist_alias'] = $artist_info['title_alias'];
				}

				if (!empty($_POST['album']))
				{
					$album_info = $iaAlbum->getAlbumById($_POST['album']);

					$data['id_album'] = $album_info['id'];
					$data['album_alias'] = $album_info['title_alias'];
				}

				if (!$error)
				{
					$iaCore->startHook("phpAdminBeforeLyricSubmit");

					$data['status'] = check_post('status');

					if ('add' == $pageAction)
					{
						$iaCore->startHook("phpAdminBeforeLyricAdd");

						$data['id'] = $iaLyric->insert($data);

						// implement common hook for all items
						$iaCore->startHook('phpAddItemAfterAll', array(
							'type' => 'admin',
							'listing' => $data['id'],
							'item' => 'lyrics',
							'data' => $data,
							'old' => $item,
						));

						$iaView->setMessages(iaLanguage::get('lyric_added'), 'success');

						$url = IA_ADMIN_URL . 'manage/lyrics/';
						$goto = array(
							'add'	=> $url . 'add/',
							'list'	=> $url,
							'stay'	=> $url . 'edit/?id='.$data['id'],
						);
						$iaCore->post_goto($goto);
					}
					elseif ('edit' == $pageAction)
					{
						$data['id'] = $item['id'];

						$iaCore->startHook("phpAdminBeforeLyricUpdate");

						$iaLyric->update($data);
						$messages = iaLanguage::get('changes_saved');
					}
				}
				else
				{
					unset($data['title_alias']);
				}
				$item = array_merge($item, $data);

				$iaView->setMessages($messages, ($error ? 'error' : 'success'));
			}
			$iaView->assign('item', $item);

			$fields_groups = $iaFields->getFieldsGroups(true, false, 'lyrics');
			$iaView->assign('fields_groups', $fields_groups);

			$iaView->display('lyrics');
			break;
	}
}