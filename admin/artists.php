<?php

class iaBackendController extends iaAbstractControllerModuleBackend
{
	protected $_name = 'artists';
	protected $_path = 'artists';
	protected $_itemName = 'artists';

	protected $_helperName = 'artist';

	protected $_gridColumns = ['title', 'date_added', 'date_modified', 'status'];
	protected $_gridFilters = ['title' => self::LIKE, 'status' => self::EQUAL];

	protected $_activityLog = ['item' => 'artists'];


	protected function _setDefaultValues(array &$entry)
	{
		$entry = [
			'featured' => false,
			'status' => iaCore::STATUS_ACTIVE,
		];
	}
}