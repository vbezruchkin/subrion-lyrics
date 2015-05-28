<?php
//##copyright##

$iaGenre = $iaCore->factoryPackage('genre', IA_CURRENT_PACKAGE, iaCore::ADMIN);

$iaDb->setTable(iaGenre::getTable());

// process ajax grid actions
if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	switch ($pageAction)
	{
		case iaCore::ACTION_READ:

			switch ($_GET['action'])
			{
				case 'getalias':

					$iaUtil = iaCore::util();
					if (!defined('IA_NOUTF'))
					{
						iaUtf8::loadUTF8Core();
						iaUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
					}

					$title = isset($_GET['title']) ? $_GET['title'] : '';
					$genre_id = isset($_GET['id']) ? $_GET['id'] : '';

					if (!utf8_is_ascii($title))
					{
						$title = utf8_to_ascii($title);
					}
					$title = iaSanitize::alias($title);

					// get id based on title_alias
					$genre = $iaGenre->getGenreByAlias($title);

					if ((!empty($genre_id) && $genre_id != $genre['id']) || empty($genre_id))
					{
						if ($iaGenre->existsAlias($title))
						{
							$output['exists'] = iaLanguage::get('genre_already_exists');
						}
					}
					$output['data'] = $iaGenre->url('view', array('title_alias' => $title));

					break;

				default:

					$output = $iaGenre->gridRead($_GET,
						array('title', 'title_alias', 'description', 'date_modified', 'status'),
						array('title' => 'like', 'status' => 'equal')
					);

					break;
			}

			break;

		case iaCore::ACTION_EDIT:
			$output = $iaGenre->gridUpdate($_POST);
			break;

		case iaCore::ACTION_DELETE:
			$output = $iaGenre->gridDelete($_POST);
	}

	$iaView->assign($output);
}

// process html page actions
if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	// display grid
	if (iaCore::ACTION_READ == $pageAction)
	{
		$iaView->grid('_IA_URL_packages/lyrics/js/admin/genres');
	}
	else
	{
		$baseUrl = IA_ADMIN_URL . 'lyrics/genres/';
		iaBreadcrumb::add(iaLanguage::get('genres'), $baseUrl);

		$error = false;
		$errorFields = array();
		$messages = array();

		$genre = array(
			'id' => 0,
			'status' => iaCore::STATUS_ACTIVE,
			'account_username' => $_SESSION['user']['username']
		);

		$iaField = $iaCore->factory('field');

		if (iaCore::ACTION_EDIT == $pageAction)
		{
			$genre = array(
				'status' => 'active'
			);
			$id = 0;

			if (isset($_GET['id']))
			{
				$id = (int) $_GET['id'];
			}

			if ($id > 0)
			{
				$genre = $iaGenre->getById($id);
			}

			if (empty($genre))
			{
				iaView::errorPage(iaView::ERROR_NOT_FOUND);
			}
		}

		$fields = $iaField->getAllFields(true, '', 'genres');

		if (isset($_POST['save']))
		{
			iaCore::util();
			if ($fields)
			{
				list($data, $error, $messages, $errorFields) = iaField::parsePost($fields, $genre, true);
			}

			if (!defined('IA_NOUTF'))
			{
				iaUtf8::loadUTF8Core();
				iaUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
			}

			if (!$error)
			{
				$iaCore->startHook("phpAdminBeforeGenreSubmit");

				$data['status'] = iaUtil::checkPostParam('status', iaCore::STATUS_ACTIVE);

				// validate title_alias
				$data['title_alias'] = !empty($_POST['title_alias']) ? $_POST['title_alias'] : $_POST['title'];
				if (!utf8_is_ascii($data['title_alias']))
				{
					$data['title_alias'] = utf8_to_ascii($data['title_alias']);
				}
				$data['title_alias'] = iaSanitize::alias($data['title_alias']);

				// check for duplicate title_alias in case a new genre is added or title_alias has been updated
				if ((!empty($genre['id']) && $genre['title_alias'] != $data['title_alias']) || empty($genre['id']))
				{
					if ($iaGenre->existsAlias($data['title_alias']))
					{
						$error = true;
						$messages[] = iaLanguage::get('genre_already_exists');
					}
				}

				// validate account
				if (isset($_POST['account']) && !empty($_POST['account']))
				{
					$member_id = $iaDb->one('id', "`username` = '{$_POST['account']}' ", iaUsers::getTable());

					if (!$member_id)
					{
						$error = true;
						$messages[] = iaLanguage::get('genre_incorrect_account');
					}
					else
					{
						$data['member_id'] = $member_id;
					}
				}

				if (!$error)
				{
					if (iaCore::ACTION_ADD == $pageAction)
					{
						$iaCore->startHook('phpAdminBeforeGenreSubmit');

						$data['id'] = $iaGenre->insert($data);
						$messages[] = iaLanguage::get('genre_added');
					}
					else
					{
						$data['id'] = $genre['id'];

						$iaCore->startHook('phpAdminBeforeGenreUpdate');

						$iaGenre->update($data);
						$messages[] = iaLanguage::get('changes_saved');
					}

					$iaCore->startHook('phpAddItemAfterAll', array(
						'type' => 'admin',
						'listing' => $data['id'],
						'item' => 'genres',
						'data' => $data,
						'old' => $genre,
					));

					$goto = array(
						'add'	=> $baseUrl . 'add/',
						'list'	=> $baseUrl,
						'stay'	=> $baseUrl . 'edit/?id=' . $data['id'],
					);

					iaUtil::post_goto($goto);

					$genre = $iaGenre->getById($data['id']);
				}

				$iaView->setMessages($error ? iaLanguage::get('error_while_saving') : $messages, $error ? iaView::ERROR : iaView::SUCCESS);
			}
		}

		$fields_groups = $iaField->getFieldsGroups(true, false, $iaGenre->getItemName());
		$iaView->assign('fields_groups', $fields_groups);

		$iaView->assign('item', $genre);

		$iaView->display('genres');
	}
}
$iaDb->resetTable();