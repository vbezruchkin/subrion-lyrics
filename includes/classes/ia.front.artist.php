<?php
//##copyright##

class iaArtist extends abstractModuleFront implements iaLyricsPackage
{
	static protected $_table = 'lyrics_artists';
	static protected $_item = 'artists';

	protected $_statuses = [iaCore::STATUS_ACTIVE, iaCore::STATUS_INACTIVE];

	var $where = " ar.`status` = 'active'";
	var $count = 0;

	public function url ($action, array $listingData)
	{
		$patterns = [
			'default' => 'artist/:alias.html',
			'artist_info' => 'artist/:artist_alias.html'
		];

		$url = iaDb::printf(
			isset($patterns[$action]) ? $patterns[$action] : $patterns['default'],
			[
				'action' => $action,
				'alias' => isset($listingData['title_alias']) ? $listingData['title_alias'] : '',
				'artist_alias' => isset($listingData['artist_alias']) ? $listingData['artist_alias'] : ''
			]
		);

		return $this->iaCore->packagesData['lyrics']['url'] . $url;
	}

	public function accountActions($aParams)
	{
		$edit_url = IA_URL . 'artist/edit/?id=' . $aParams['item']['id'];

		return [$edit_url, ''];
	}

	public function insert(array $entryData)
	{
		$addit = ['date_modified' => 'NOW()','date_added' => 'NOW()', 'member_id' => $_SESSION['user']['id']];

		return $this->iaDb->insert($entryData, $addit, self::getTable());
	}

	public function getArtist($aId)
	{
		return $this->iaDb->row('*', "`id`= '$aId'", self::getTable());
	}

	public function getArtistByAlias($aAlias)
	{
		return $this->iaDb->row('*', "`title_alias`= '{$aAlias}'", self::getTable());
	}

	public function getArtistByTitle($aTitle)
	{
		return $this->iaDb->row('*', "`title`= '{$aTitle}'", self::getTable());
	}

	public function getNumArtistsByGenre($aGenre)
	{
		$sql = "SELECT COUNT(*) FROM `" . self::getTable(true) . "` ";
		$sql .= "WHERE `genres` REGEXP ('{$aGenre},|{$aGenre}$') ";
		$sql .= "AND `status` = 'active' ";

		return $this->iaDb->getOne($sql);
	}

	public function getArtistsByGenre($aGenre)
	{
		$sql = "SELECT * ";
		$sql .= "FROM `" . self::getTable(true) . "` ";
		$sql .= "WHERE `genres` REGEXP ('{$aGenre},|{$aGenre}$') ";
		$sql .= "AND `status` = 'active' ";

		return $this->iaDb->getAll($sql);
	}

	public function getArtists($where = false, $aStart = 0, $aLimit = 0, $order = false)
	{
		$sql  = "FROM `" . self::getTable(true) . "` AS ar WHERE " . $this->where;
		if (is_array($where))
		{
			foreach ($where as $name => $value)
			{
				$sql .= " AND `$name` = '$value' ";
			}
		}
		elseif ($where)
		{
			$sql .= $where;
		}

		if ($order)
		{
			$sql .= "ORDER BY {$order} ";
		}
		$this->count = $this->iaDb->getOne('SELECT COUNT(`id`) as `count` ' . $sql);

		$sql .= $aStart || $aLimit ? "LIMIT {$aStart}, {$aLimit} " : '';

		return $this->iaDb->getAll('SELECT * ' . $sql);
	}

	public function getCountArtists()
	{
		return $this->count;
	}

	public function getLatestArtists()
	{
		$sql = "SELECT * ";
		$sql .= "FROM `" . self::getTable(true) . "` ";
		$sql .= "WHERE `status` = 'active' ";
		$sql .= "ORDER BY `date_modified` DESC ";
		$sql .= "LIMIT 0, " . $this->iaCore->get('new_artists_per_block');

		return $this->iaDb->getAll($sql);
	}

	public function getTopArtists()
	{
		$sql = "SELECT * ";
		$sql .= "FROM `" . self::getTable(true) . "` ";
		$sql .= "WHERE `status` = 'active' ";
		$sql .= "ORDER BY `views_num` DESC ";
		$sql .= "LIMIT 0, " . $this->iaCore->get('top_artists_per_block');

		return $this->iaDb->getAll($sql);
	}
}