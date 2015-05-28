<?php
//##copyright##

class iaLyric extends abstractPackageAdmin implements iaLyricsPackage
{
	static public $_table = 'lyrics_lyrics';
	static protected $_item = 'lyrics';

	protected $_moduleUrl = 'lyrics/lyrics/';

	public $dashboardStatistics = array('icon' => 'lyrics');

	private $patterns = array(
		'view' => ':artist_alias:album_alias:alias:id'
	);

	public function url($action, $data = array(), $generate = false)
	{
		$data['action'] = $action;
		$data['alias'] = (isset($data['genre_alias']) ? $data['genre_alias'] : $data['title_alias']);

		if (!isset($this->patterns[$action]))
		{
			$action = 'view';
		}

		if ($generate)
		{
			iaCore::util();
			if (!defined('IA_NOUTF'))
			{
				iaUtf8::loadUTF8Core();
				iaUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
			}
			if (!utf8_is_ascii($data['alias']))
			{
				$data['alias'] = $iaCore->convertStr(utf8_to_ascii($data['alias']));
			}
		}

		$url = iaDb::printf($this->patterns[$action], $data);

		return $this->iaCore->packagesData[self::PACKAGE_NAME]['url'] . $url;
	}

	public function delete($where)
	{
		$this->iaDb->delete($where, array(), false, self::getTable());

		return true;
	}

	public function insert(array $entryData)
	{
		$addit = array('date_added' => 'NOW()', 'date_modified' => 'NOW()');

		$entryData['id'] = $this->iaDb->insert($entryData, $addit, self::getTable());

		return $entryData['id'];
	}

	function update(array $aData, $aOldData = array())
	{
		$this->iaDb->update($aData, "`id` = {$aData['id']}", array('date_modified' => 'NOW()'), self::getTable());

		return true;
	}

	public function existsAlias($alias)
	{
		return $this->iaDb->exists("`title_alias` = :alias", array('alias' => $alias), self::getTable());
	}

	public function getLyrics($aWhere, $aStart = 0, $aLimit = '', $aOrder = '')
	{
		$order = $aOrder ? " ORDER BY ".$aOrder : ' ';
		$limit = $aLimit ? " LIMIT {$aStart}, {$aLimit} " : ' ';
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS t1.*, t2.`title` `artist`, t2.`genres` `artist_genres`, '1' `edit`, '1' `remove`, ";
		$sql .= "IF(t3.`fullname` <> '', t3.`fullname`, t3.`username`) `account` ";
		$sql .= "FROM `".self::getTable(true)."` t1 ";
		$sql .= "LEFT JOIN `{$this->iaDb->prefix}lyrics_artists` t2 ";
		$sql .= "ON t1.`id_artist` = t2.`id` ";
		$sql .= "LEFT JOIN `{$this->iaDb->prefix}members` t3 ";
		$sql .= "ON t1.`member_id` = t3.`id` ";
		$sql .= 'WHERE ';
		$sql .= $aWhere . $order . $limit;

		return $this->iaDb->getAll($sql);
	}

	public function getById($aId)
	{
		$sql = "SELECT t1.*, t2.`title` `artist`, t2.`genres` `artist_genres`, ";
		$sql .= "IF(t3.`fullname` <> '', t3.`fullname`, t3.`username`) `account` ";
		$sql .= "FROM `".self::getTable(true)."` t1 ";
		$sql .= "LEFT JOIN `{$this->iaDb->prefix}lyrics_artists` t2 ";
		$sql .= "ON t1.`id_artist` = t2.`id` ";
		$sql .= "LEFT JOIN `{$this->iaDb->prefix}members` t3 ";
		$sql .= "ON t1.`member_id` = t3.`id` ";
		$sql .= "WHERE t1.`id` = '{$aId}'";
		
		return $this->iaDb->getRow($sql);
	}
}