<?php
//##copyright##

class iaArtist extends abstractLyricsModuleAdmin
{
	static public $_table = 'lyrics_artists';
	static protected $_item = 'artists';

	protected $_moduleUrl = 'lyrics/artists/';

	public $dashboardStatistics = ['icon' => 'artists'];

	private $patterns = [
		'view' => ':alias',
	];

	public function url($action, $data = [], $generate = false)
	{
		$data['action'] = $action;
		$data['alias'] = (isset($data['artist_alias']) ? $data['artist_alias'] : $data['title_alias']);

		if (!isset($this->patterns[$action]))
		{
			$action = 'view';
		}

		if ($generate)
		{
			$iaUtil = $iaCore->factory('core', 'util');
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

		return self::get('url') . $url;
	}

	public function getArtistByTitle($aTitle)
	{
		$sql = "SELECT * ";
		$sql .= "FROM `".self::getTable(true)."` ";
		$sql .= "WHERE `title` = '{$aTitle}' ";
		 
		return $this->iaDb->getRow($sql);
	}

	public function getArtistById($aId)
	{
		$sql = "SELECT * ";
		$sql .= "FROM `".$this->iaDb->prefix.self::getTable()."` ";
		$sql .= "WHERE `id` = '{$aId}' ";

		return $this->iaDb->getRow($sql);
	}

	public function getArtists($aWhere, $aStart = 0, $aLimit = '', $aOrder = '')
	{
		$iaDb = &$this->iaDb;

		$order = $aOrder ? " ORDER BY ".$aOrder : ' ';
		$limit = $aLimit ? " LIMIT {$aStart}, {$aLimit} " : ' ';

		$sql = "SELECT t1.*, '1' `edit`, '1' `remove`, ";
		$sql .= "IF(t1.`member_id` IS NULL, 'Global', IF(t3.`fullname` <> '', t3.`fullname`, t3.`username`)) `account` ";
		$sql .= "FROM `".$iaDb->prefix.self::getTable()."` t1 ";
		$sql .= "LEFT JOIN `{$iaDb->prefix}members` t3 ";
		$sql .= "ON t1.`member_id` = t3.`id` ";
		$sql .= "WHERE ";
		$sql .= $aWhere.$order.$limit;

		return $iaDb->getAll($sql);
	}

	public function getArtistsNum($aWhere)
	{
		return $this->iaDb->one('COUNT(*)', $aWhere, 0, null, self::getTable().'` as `t1');
	}

	public function insert(array $entryData)
	{
		$addit = ['date_modified' => 'NOW()','date_added' => 'NOW()'];
		$entryData['id'] = $this->iaDb->insert($entryData, $addit, self::getTable());

		return $entryData['id'];
	}

	public function update(array $aData, $aOldData = [])
	{
		$this->iaDb->update($aData, "`id` = {$aData['id']}", ['date_modified' => 'NOW()'], self::getTable());

		return true;
	}

	public function delete($where)
	{
		$this->iaDb->delete($where, [], false, self::getTable());

		return true;
	}

	public function existsAlias($alias)
	{
		$iaDb = &$this->iaDb;

		return $iaDb->exists("`title_alias` = :alias", ['alias' => $alias], self::getTable());
	}
}