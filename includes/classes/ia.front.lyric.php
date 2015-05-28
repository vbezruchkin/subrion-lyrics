<?php
//##copyright##

class iaLyric extends abstractPackageFront implements iaLyricsPackage
{
	static protected $_table = 'lyrics_lyrics';
	static protected $_item = 'lyrics';

	protected $_statuses = array(iaCore::STATUS_ACTIVE, iaCore::STATUS_INACTIVE);

	public function url ($action, array $listingData)
	{
		$patterns = array(
			'default' => 'lyric/:artist_alias/:album_alias/:id-:title_alias.html',
			'edit' => 'edit/:id/'
		);

		$url = iaDb::printf(
			isset($patterns[$action]) ? $patterns[$action] : $patterns['default'],
			array(
				'action' => $action,
				'artist_alias' => isset($listingData['artist_alias']) ? $listingData['artist_alias'] : '',
				'album_alias' => isset($listingData['album_alias']) ? $listingData['album_alias'] : '',
				'title_alias' => isset($listingData['title_alias']) ? $listingData['title_alias'] : '',
				'id' => isset($listingData['id']) ? $listingData['id'] : ''
			)
		);

		return $this->iaCore->packagesData['lyrics']['url'] . $url;
	}

	public function accountActions($aParams)
	{
		$edit_url = $this->url('edit', $aParams['item']);

		return array($edit_url, '');
	}

	public function insert(array $entryData)
	{
		$addit = array('date_modified' => 'NOW()','date_added' => 'NOW()');
		$entryData['id'] = $this->iaDb->insert($entryData, $addit, self::getTable());

		return $entryData['id'];
	}

	public function getLyrics($aWhere, $aStart = 0, $aLimit = 0, $aOrder = '')
	{
		$sql = "SELECT * ";
		$sql .= "FROM `" . self::getTable(true) . "` ";
		$sql .= "WHERE 1 = 1 ";
		$sql .= $aWhere;
		$sql .= "AND `status` = 'active' ";
		$sql .= !empty($aOrder) ? "ORDER BY `{$aOrder}` DESC " : 'ORDER BY `date_added` DESC ';
		$sql .= $aLimit ? "LIMIT {$aStart}, {$aLimit}" : '';

		return $this->iaDb->getAll($sql);
	}

	public function getLyric($aId)
	{
		return $this->iaDb->row('*', "`id`= '{$aId}'", self::getTable());
	}

	public function getNumLyrics($aWhere)
	{
		$sql = "SELECT COUNT(*) ";
		$sql .= "FROM `" . self::getTable(true) . "` ";
		$sql .= "WHERE 1 = 1 ";
		$sql .= $aWhere;

		return $this->iaDb->getOne($sql);
	}

	public function getLyricsByAccount($aArtist, $aStart = 0, $aLimit = null)
	{
		$where = "AND `member_id` = '{$aArtist}' ";

		return $this->getLyrics($where, $aStart, $aLimit);
	}

	public function getLyricsByArtist($aArtist, $aStart = 0, $aLimit = 0)
	{
		$where = "AND `id_artist` = '{$aArtist}' ";

		return $this->getLyrics($where, $aStart, $aLimit);
	}

	public function getLyricsByAlbum($aAlbum)
	{
		$where = "AND `id_album` = '{$aAlbum}' ";

		return $this->getLyrics($where);
	}

	public function getLyricsByGenre($aGenre, $aStart = 0, $aLimit = 0)
	{
		$where = "AND `genres` REGEXP ('{$aGenre},|{$aGenre}$') ";

		return $this->getLyrics($where, $aStart, $aLimit);
	}

	public function getSearchLyrics($aStart = 0, $aLimit = false, $aWhere = '', $aShowInactive = false, $aFeaturedOnTop = false, $aOrder = false)
	{
		$sql  = "SELECT SQL_CALC_FOUND_ROWS t1.*, '0' `favorite` ";
		$sql .= "FROM `" . self::getTable(true) . "` t1 ";

		$sql .= "WHERE 1 ";

		if ($aWhere)
		{
			$sql .= $aWhere;
		}
		$sql .= "ORDER BY " . ($aFeaturedOnTop ? "t1.featured DESC, " : false);
		$sql .= $aOrder ? $aOrder : " t1.`date_added` DESC ";

		if (false !== $aLimit)
		{
			$sql .= "LIMIT {$aStart},{$aLimit}";
		}
		$ret = $this->iaDb->getAll($sql);

		return $ret;
	}
}