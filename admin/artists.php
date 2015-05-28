<?php
//##copyright##

$iaArtist = $iaCore->factoryPackage('artist', IA_CURRENT_PACKAGE, iaCore::ADMIN);

// process ajax grid actions
if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	if (isset($_GET['action']))
	{
		$action = $_GET['action'];

		switch ($action)
		{
			// return artists
			case 'getartist':

				// ajax artists return
				if (isset($_GET['q']))
				{
					$where = "`title` LIKE '{$_GET['q']}%' ";
					$order = "ORDER BY `title` ASC ";

					$artists = $iaArtist->iaDb->all("`title`", $where.$order, 0, 15, iaArtist::$_table);

					if ($artists)
					{
						foreach($artists as $artist)
						{
							echo $artist['title'] . "\r\n";
						}
					}
				}
				exit;

				break;

			// return title_alias for a artist
			case 'getalias':

				$iaUtil = iaCore::util();
				if (!defined('IA_NOUTF'))
				{
					iaUtf8::loadUTF8Core();
					iaUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
				}
				$out['data'] = '';

				$title = isset($_GET['title']) ? $_GET['title'] : '';
				if (!utf8_is_ascii($title))
				{
					$title = utf8_to_ascii($title);
				}
				$title = iaSanitize::convertStr($title);

				$out['data'] = $iaArtist->url('view', array('title_alias' => $title));

				break;

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
				if (isset($_GET['status']) && in_array($_GET['status'], $iaArtist->_statuses))
				{
					$where[] = "`status` = :status";
					$values['status'] = $_GET['status'];
				}
				elseif (isset($_SESSION['artist_status']))
				{
					$where[] = "`status` = :status";
					$values['status'] = $_SESSION['artist_status'];
				}

				if (isset($_GET['title']) && !empty($_GET['title']))
				{
					$where[] = "`title` LIKE :title";
					$values['title'] = '%'.$_GET['title'].'%';
				}

				$where = implode(" AND ", $where);
				$iaArtist->iaDb->mysql_bind($where, $values);

				$out['data'] = $iaArtist->iaDb->all(
					"SQL_CALC_FOUND_ROWS *, `id` - 1 as `edit`, 1 `remove` ",
						$where . ' ORDER BY '.$order,
					$start, $limit, iaArtist::$_table);
				$out['total'] = $iaDb->foundRows();

				$iaView->assign_all($out);

				break;

			default:
				break;
		}
	}

	if (!isset($out))
	{
		$iaView->assign_all($iaArtist->gridActions($_GET));
	}
}

// process html page actions
if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaGenre = $iaCore->factoryPackage('genre', IA_CURRENT_PACKAGE, iaCore::ADMIN);

	$genres = $iaGenre->getGenres("t1.`status` = 'active'");
	$iaView->assign('genres', $genres);

	// init classes
	$iaField = $iaCore->factory('field');

	$error = false;
	$errorFields = array();
	$messages = array();

	switch ($pageAction)
	{
		default:

			$iaView->grid('_IA_URL_packages/lyrics/js/admin/artists');

			break;
		case 'edit':
		case 'add':

			iaBreadcrumb::add(iaLanguage::get('artists'), IA_ADMIN_URL . 'lyrics/artists/');

			if ('edit' == $pageAction)
			{
				$item = $iaArtist->getById((int)$_GET['id']);

				if (empty($item))
				{
					iaView::errorPage(iaView::ERROR_NOT_FOUND);
				}
			}
			elseif ('add' == $pageAction)
			{
				// these fields are system and used in system template
				$item = array('status' => 'active', 'account_username' => $_SESSION['user']['username'], 'featured' => false);
			}

			$fields = iaField::getAllFields(true, '', 'artists');

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
						$messages[] = iaLanguage::get('artist_incorrect_account');
					}
					else
					{
						$data['member_id'] = $member_id;
					}
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

				// check for duplicate title_alias in case a new artist is added or title_alias has been updated
				if (!isset($item['title_alias']) || (isset($item['title_alias']) && $data['title_alias'] != $item['title_alias']))
				{
					if ($iaArtist->existsAlias($data['title_alias']))
					{
						$error = true;
						$messages[] = iaLanguage::get('artist_already_exists');
					}
				}

				if (!$error)
				{
					$iaCore->startHook("phpAdminBeforeArtistSubmit");

					$data['status'] = check_post('status');

					// generate genre record
					$data['genres'] = !empty($_POST['genres']) ? $_POST['genres'] : array();
					$data['genres'] = implode(',', $data['genres']);

					if ('add' == $pageAction)
					{
						$iaCore->startHook("phpAdminBeforeArtistAdd");

						$data['id'] = $iaArtist->insert($data);

						// implement common hook for all items
						$iaCore->startHook('phpAddItemAfterAll', array(
							'type' => 'admin',
							'listing' => $data['id'],
							'item' => 'artists',
							'data' => $data,
							'old' => $item,
						));

						$iaView->setMessages(iaLanguage::get('artist_added'), 'success');

						$url = IA_ADMIN_URL . 'manage/artists/';
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

						$iaCore->startHook("phpAdminBeforeArtistUpdate");

						$iaArtist->update($data);
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
			$item['genres'] = !empty($item['genres']) ? explode(',', $item['genres']) : array();
			$iaView->assign('item', $item);

			$fields_groups = $iaField->getFieldsGroups(true, false, 'artists');
			$iaView->assign('fields_groups', $fields_groups);

			$iaView->display('artists');
			break;
	}
}