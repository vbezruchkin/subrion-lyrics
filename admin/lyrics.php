<?php

class iaBackendController extends iaAbstractControllerModuleBackend
{
	protected $_name = 'lyrics';
	protected $_path = 'lyrics';
	protected $_itemName = 'lyrics';

	protected $_helperName = 'lyric';

	protected $_gridColumns = ['title', 'date_added', 'date_modified', 'status'];
	protected $_gridFilters = ['title' => self::LIKE, 'status' => self::EQUAL];

	protected $_activityLog = ['item' => 'lyrics'];


	protected function _setDefaultValues(array &$entry)
	{
		$entry = [
			'featured' => false,
			'status' => iaCore::STATUS_ACTIVE,
		];
	}
}