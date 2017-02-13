<?php
//##copyright##
define('INTELLI_REALM', $cfg['name']);

$iaDb->setTable('lyrics');
$iaLyric = $iaCore->factoryModules(IA_CURRENT_PACKAGE, 'front', 'lyric');

$plans = [];
$errorFields = [];

if(isset($_GET['id'])) $id = (int)$_GET['id'];
else $id = false;

$lyric = $id ? $iaDb->row('*, \'lyrics\' as `item`', "`id`={$id}") : [];

// plans
$iaPlan = $iaCore->factory('front','plan');
$plans = $iaPlan->getPlans('lyrics');
$iaCore->assign('plans', $plans);

if (isset($_GET['id']))
{
	if (empty($lyric))
	{
		iaView::errorPage(iaView::ERROR_NOT_FOUND);
	}
	elseif ($_SESSION['user']['id'] != $lyric['member_id'])
	{
		iaView::errorPage(iaView::ERROR_FORBIDDEN);
	}
}

if ($id)
{
	$sections = $iaCore->getAcoGroupsFields(false, 'lyrics', "`f`.`type` <> 'pictures'");
	$iaCore->assign_by_ref('sections', $sections);

	$pictures_sections = $iaCore->getAcoGroupsFields(false, 'lyrics', "`f`.`type`='pictures'", false);
	$iaCore->assign_by_ref('pictures_sections', $pictures_sections);
}
else
{
	$sections = $iaCore->getAcoGroupsFields(false, 'lyrics');
	$iaCore->assign_by_ref('sections', $sections);
}

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	if (!empty($_POST))
	{
		$fields = $iaCore->getAcoFieldsList(false, 'lyrics', false, true);
		if($fields)
		{
			$data = '';
			iaCore::util();
			list($data, $error, $messages, $errorFields) = iaUtil::updateItemPOSTFields($fields, $lyric);
		}
		
		if (!$error)
		{
			$iaCore->startHook("beforeEstateSubmit");
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
			
			if (empty($lyric))
			{
				$action = 'added';
				$data['id'] = $iaLyric->add($data);
				if($data['id'] == 0)
				{
					$error = true;
					$messages[] = _t('mysql_error');
				}
			}
			else
			{
				if(isset($_POST['delete_lyric']))
				{
					$iaDb->delete('`id` = '.$lyric['id']);
					$action = 'deleted';
					$data['id'] = 0;
				}
				else
				{
					$action = 'updated';
					$data['id'] = $lyric['id'];
					$iaDb->update($data);
				}
			}
			
			if (!$error)
			{
				$url = IA_PACKAGE_URL . ( $data['id'] != 0 ? 'add/?id='.$data['id'] : 'accounts/' );
				$messages[] = _t('lyric_'.$action.$dmsg);

				iaCore::util();
				iaUtil::redirect(_t('thanks'), $messages, $url, isset($_POST['ajax']));
			}
		}
		
		if (isset($_POST['ajax']))
		{
			header('Content-type: text/xml');
			echo '<?xml version="1.0" encoding="'.$iaCore->get('charset').'" ?>'
				.'<root><error>' . $error . '</error><msg><![CDATA[<li>' . implode('</li><li>',$messages).']]></msg></root>';
			exit;
		}
	}

	
	$iaCore->assign('error_fields', $errorFields);
	$iaCore->assign('item', $lyric);
	
	$iaCore->set_cfg('body', 'request');
	$iaCore->set_cfg('title', _t('page_title_'.INTELLI_REALM));
}
$iaDb->resetTable();