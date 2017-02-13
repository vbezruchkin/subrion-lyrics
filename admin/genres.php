<?php

class iaBackendController extends iaAbstractControllerModuleBackend
{
	protected $_name = 'genres';
	protected $_path = 'genres';
	protected $_itemName = 'genres';

	protected $_helperName = 'genre';

	protected $_gridColumns = ['title', 'date_added', 'date_modified', 'status'];
	protected $_gridFilters = ['title' => self::LIKE, 'status' => self::EQUAL];

	protected $_activityLog = ['item' => 'genre'];


	protected function _setDefaultValues(array &$entry)
	{
		$entry = [
			'featured' => false,
			'status' => iaCore::STATUS_ACTIVE,
		];
	}
}