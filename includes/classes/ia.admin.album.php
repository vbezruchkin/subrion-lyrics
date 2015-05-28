<?php
//##copyright##

class iaAlbum extends abstractPackageAdmin implements iaLyricsPackage
{
	static public $_table = 'lyrics_albums';
	static protected $_item = 'albums';

	protected $_moduleUrl = 'lyrics/albums/';

	public $dashboardStatistics = array('icon' => 'albums');

	private $patterns = array(
		'view' => ':artist_alias:alias',
	);

	public function url($action, $data = array(), $generate = false)
	{
		$data['action'] = $action;
		$data['alias'] = (isset($data['album_alias']) ? $data['album_alias'] : $data['title_alias']);

		if (!isset($this->patterns[$action]))
		{
			$action = 'view';
		}

		$url = iaDb::printf($this->patterns[$action], $data);

		return self::get('url') . $url;
	}
	
	function getAlbumById($aId)
	{
		$sql = "SELECT t1.*, t2.`title` `artist_title` ";
		$sql .= "FROM `".self::getTable(true)."` `t1` ";
		$sql .= "LEFT JOIN `{$this->iaDb->prefix}lyrics_artists` t2 ON t1.`id_artist` = t2.`id` ";
		$sql .= "WHERE t1.`id` = '{$aId}' ";

		return $this->iaDb->getRow($sql);
	}
	
	function getAlbumByTitle($aTitle)
	{
		$sql = "SELECT * ";
		$sql .= "FROM `".self::getTable(true)."` ";
		$sql .= "WHERE `title` = '{$aTitle}' ";

		return $this->iaDb->getRow($sql);
	}

	function getAlbumsByAlbum($aAlbum)
	{
		$where = " t2.`title` = '{$aAlbum}' ";
		$order = " t1.`title` ASC ";
		
		return $this->getAlbums($where, 0, null, $order);
	}

	function getAlbumsByArtistId($aId)
	{
		$where = " t1.`id_artist` = '{$aId}' ";
		$order = " t1.`title` ASC ";
		
		return $this->getAlbums($where, 0, null, $order, self::getTable());
	}

	function getAlbums($aWhere, $aStart = 0, $aLimit = '', $aOrder = '')
	{
		$iaDb = &$this->iaDb;

		$order = $aOrder ? " ORDER BY ".$aOrder : ' ';
		$limit = $aLimit ? " LIMIT {$aStart}, {$aLimit} " : ' ';

		$sql = "SELECT t1.*, t2.`title` `artist_title`, '1' `edit`, '1' `remove`, ";
		$sql .= "IF(t1.`member_id` IS NULL, 'Global', IF(t3.`fullname` <> '', t3.`fullname`, t3.`username`)) `account` ";
		$sql .= "FROM `".self::getTable(true)."` t1 ";
		$sql .= "LEFT JOIN `{$iaDb->prefix}lyrics_artists` t2 ";
		$sql .= "ON t1.`id_artist` = t2.`id` ";
		$sql .= "LEFT JOIN `{$iaDb->prefix}members` t3 ";
		$sql .= "ON t1.`member_id` = t3.`id` ";
		$sql .= "WHERE ";
		$sql .= $aWhere.$order.$limit;

		return $iaDb->getAll($sql);
	}

	public function getNumAlbums($aWhere)
	{
		return $this->iaDb->one('COUNT(*)', $aWhere, 0, null, self::getTable().'` as `t1');
	}

	public function insert(array $entryData)
	{
		$addit = array('date_modified' => 'NOW()','date_added' => 'NOW()');
		$entryData['id'] = $this->iaDb->insert($entryData, $addit, self::getTable());

		return $entryData['id'];
	}

	public function update(array $aData, $aOldData = array())
	{
		$this->iaDb->update($aData, "`id` = {$aData['id']}", array('date_modified' => 'NOW()'), self::getTable());

		return true;
	}

	public function delete($where)
	{
		$this->iaDb->delete($where, array(), false, self::getTable());

		return true;
	}

	public function existsAlias($alias)
	{
		return $this->iaDb->exists("`title_alias` = :alias", array('alias' => $alias), self::getTable());
	}
}