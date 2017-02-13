<?php

class iaBackendController extends iaAbstractControllerModuleBackend
{
	protected $_name = 'albums';
	protected $_path = 'albums';
	protected $_itemName = 'albums';

	protected $_helperName = 'album';

	protected $_gridColumns = ['title', 'date_added', 'date_modified', 'status'];
	protected $_gridFilters = ['title' => self::LIKE, 'status' => self::EQUAL];

	protected $_activityLog = ['item' => 'albums'];


	protected function _setDefaultValues(array &$entry)
	{
		$entry = [
			'featured' => false,
			'status' => iaCore::STATUS_ACTIVE,
		];
	}
}