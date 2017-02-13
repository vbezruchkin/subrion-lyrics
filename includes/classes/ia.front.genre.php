<?php
//##copyright##

class iaGenre extends abstractModuleFront implements iaLyricsPackage
{
	static protected $_table = 'lyrics_genres';
	static protected $_item = 'genres';

	public function title($data)
	{
		$title = '';
		if (isset($data['title']))
		{

		}
		return $title;
	}

	public function url($action, array $listingData)
	{
		$patterns = [
			'default' => 'genre/:alias.html',
			'view' => 'genre/:alias.html'
		];

		$url = iaDb::printf(
			isset($patterns[$action]) ? $patterns[$action] : $patterns['default'],
			[
				'action' => $action,
				'alias' => isset($listingData['title_alias']) ? $listingData['title_alias'] : ''
			]
		);

		return $this->iaCore->packagesData['lyrics']['url'] . $url;
	}

	function insert(array $entryData)
	{
		$addit = ['date_modified' => 'NOW()','date_added' => 'NOW()'];
		$entryData['id'] = $this->iaDb->insert($entryData, $addit, self::getTable());

		return $entryData['id'];
	}

	function accountActions($aParams)
	{
		$edit_url = IA_URL.'genre/edit/?id='.$aParams['item']['id'];
		
		return [$edit_url, ''];
	}

	function getGenre($aId)
	{
		return $this->iaDb->row('*', "`id`= '$aId'", self::getTable());
	}

	function getGenreByAlias($aAlias)
	{
		return $this->iaDb->row('*', "`title_alias` = '{$aAlias}' AND `status`='active' ", self::getTable());
	}

	function getGenres()
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS * ";
		$sql .= "FROM `".self::getTable(true)."` ";
		$sql .= "WHERE `status` = 'active' ";
		$sql .= "ORDER BY `title` ASC";

		return $this->iaDb->getAll($sql);
	}
}