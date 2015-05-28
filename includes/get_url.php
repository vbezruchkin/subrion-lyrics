<?php
//##copyright##

/*
if ('lyrics' == $this->get('default_package') || !$this->checkDomain())
{
	if ($iaView->url)
	{
		$url = implode(IA_URL_DELIMITER, $iaView->url);
		$stmt = sprintf("`title_alias` = '%s' AND `status` = '%s'", $url, iaCore::STATUS_ACTIVE);
		$value = $iaDb->exists($stmt, null, 'coupons_categories');

		if ($url && $value)
		{
			$iaCore->requestPath = $iaView->url;
			$iaView->name('coupons_home');
		}
		else
		{
			$stmt = sprintf("`alias` = '%s' AND `status` = '%s' AND `extras` = '%s'", $url, 'active', 'coupons');
			$value = $iaDb->one('`name`', $stmt, 'pages');

			if ($value)
			{
				$iaView->name($value);
			}
		}
	}
}
*/