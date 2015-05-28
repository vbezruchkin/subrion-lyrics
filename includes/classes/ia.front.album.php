<?php
//##copyright##

class iaAlbum extends abstractPackageFront implements iaLyricsPackage
{
	static protected $_table = 'lyrics_albums';
	static protected $_item = 'albums';


	public function url ($action, array $listingData)
	{
		$patterns = array(
			'default' => 'album/:artist_alias/:alias/',
			'view' => 'album/:artist_alias/:alias/'
		);

		$url = iaDb::printf(
			isset($patterns[$action]) ? $patterns[$action] : $patterns['default'],
			array(
				'alias' => isset($listingData['title_alias']) ? $listingData['title_alias'] : '',
				'artist_alias' => isset($listingData['artist_alias']) ? $listingData['artist_alias'] : ''
			)
		);

		return $this->iaCore->packagesData['lyrics']['url'] . $url;
	}

	public function insert(array $entryData)
	{
		$addit = array('date_modified' => 'NOW()','date_added' => 'NOW()');
		$entryData['id'] = $this->iaDb->insert($entryData, $addit, self::getTable());

		return $entryData['id'];
	}

	public function getAlbumByAlias($aAlias)
	{
		return $this->iaDb->row('*', "`title_alias`= '{$aAlias}' AND `status` = 'active' ", self::getTable());
	}

	public function getAlbum($aId)
	{
		return $this->iaDb->row('*', "`id` = '$aId'", self::getTable());
	}

	public function getAlbumsByArtist($aArtist, $where = '')
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS * ";
		$sql .= "FROM `" . self::getTable(true) . "` ";
		$sql .= "WHERE `id_artist` = '{$aArtist}' ";
		$sql .= "AND `status` = 'active' ";
		$sql .= $where;

		return $this->iaDb->getAll($sql);
	}

	public function getAlbumsByAccount($aAccount)
	{
		$sql = "SELECT * ";
		$sql .= "FROM `" . self::getTable(true) . "` ";
		$sql .= "WHERE `status` = 'active' ";
		$sql .= "AND `member_id` = '{$aAccount}' ";

		return $this->iaDb->getAll($sql);
	}

	public function getLatestAlbums()
	{
		$sql = "SELECT * ";
		$sql .= "FROM `" . self::getTable(true) . "` ";
		$sql .= "WHERE `status` = 'active' ";
		$sql .= "ORDER BY `date_modified` DESC ";
		$sql .= "LIMIT 0, " . $this->iaCore->get('new_albums_per_block');

		return $this->iaDb->getAll($sql);
	}

	public function getTopAlbums()
	{
		$sql = "SELECT * ";
		$sql .= "FROM `" . self::getTable(true) . "` ";
		$sql .= "WHERE `status` = 'active' ";
		$sql .= "ORDER BY `views_num` DESC ";
		$sql .= "LIMIT 0, " . $this->iaCore->get('top_albums_per_block');

		return $this->iaDb->getAll($sql);
	}
}