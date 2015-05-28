<?php
//##copyright##

class iaGenre extends abstractPackageAdmin implements iaLyricsPackage
{
	static public $_table = 'lyrics_genres';
	static protected $_item = 'genres';

	protected $_moduleUrl = 'lyrics/genres/';

	public $dashboardStatistics = array('icon' => 'genres');

	private $_patterns = array('view' => 'genre/:alias.html');

	public function url($action, $data = array())
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
		$addit = array('date_modified' => 'NOW()','date_added' => 'NOW()');
		$entryData['id'] = $this->iaDb->insert($entryData, $addit, self::getTable());

		return $entryData['id'];
	}

	public function update(array $aData)
	{
		return $this->iaDb->update($aData, "`id` = {$aData['id']}", array('date_modified' => 'NOW()'), self::getTable());
	}

	public function getById($aId)
	{
		$iaDb = &$this->iaDb;

		$sql = "SELECT t1.*, t2.`username` `account_username` FROM `".self::getTable(true)."` t1 ";
		$sql .= "LEFT JOIN `{$iaDb->prefix}members` t2 ";
		$sql .= "ON t1.`member_id` = t2.`id` ";
		$sql .= "WHERE t1.`id` = {$aId}";

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
		return $this->iaDb->exists("`title_alias` = :alias", array('alias' => $alias), self::getTable());
	}
}