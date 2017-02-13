<?php
//##copyright##

class iaGenre extends abstractLyricsModuleAdmin
{
	protected static $_table = 'lyrics_genres';
	protected $_itemName = 'genres';

	protected $_activityLog = ['item' => 'genre'];

	protected $_moduleUrl = 'lyrics/genres/';

	public $dashboardStatistics = ['icon' => 'genres'];

	private $_patterns = ['view' => 'genre/:alias.html'];


	public function url($action, $data = [])
	{
		$data['action'] = $action;
		$data['alias'] = isset($data['genre_alias']) ? $data['genre_alias'] : $data['title_alias'];

		if (!isset($this->_patterns[$action]))
		{
			$action = 'view';
		}

		$url = iaDb::printf($this->_patterns[$action], $data);

		return $this->iaCore->packagesData[self::PACKAGE_NAME]['url'] . $url;
	}

	public function insert(array $entryData)
	{
		$addit = ['date_modified' => 'NOW()','date_added' => 'NOW()'];
		$entryData['id'] = $this->iaDb->insert($entryData, $addit, self::getTable());

		return $entryData['id'];
	}

	public function update(array $itemData, $id)
	{
		return $this->iaDb->update($itemData, "`id` = {$id}", ['date_modified' => 'NOW()'], self::getTable());
	}

	public function getById($id, $process = true)
	{
		$iaDb = &$this->iaDb;

		$sql = "SELECT t1.*, t2.`username` `account_username` FROM `".self::getTable(true)."` t1 ";
		$sql .= "LEFT JOIN `{$iaDb->prefix}members` t2 ";
		$sql .= "ON t1.`member_id` = t2.`id` ";
		$sql .= "WHERE t1.`id` = {$id}";

		return $iaDb->getRow($sql);
	}

	public function getGenreByTitle($aTitle)
	{
		$sql = "SELECT t1.* FROM `".self::getTable(true)."` t1 ";
		$sql .= "WHERE t1.`title` = '{$aTitle}'";

		return $this->iaDb->getRow($sql);
	}

	public function getGenreByAlias($aTitle)
	{
		$sql = "SELECT t1.* FROM `".self::getTable(true)."` t1 ";
		$sql .= "WHERE t1.`title_alias` = '{$aTitle}'";

		return $this->iaDb->getRow($sql);
	}

	public function existsAlias($alias)
	{
		return $this->iaDb->exists("`title_alias` = :alias", ['alias' => $alias], self::getTable());
	}
}