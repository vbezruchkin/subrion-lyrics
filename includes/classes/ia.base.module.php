<?php
//##copyright##

interface iaLyricsModule
{
	const PACKAGE_NAME = 'lyrics';

	const COLUMN_ID = 'id';
}

abstract class abstractLyricsModuleAdmin extends abstractModuleAdmin implements iaLyricsModule
{
	protected $_packageName = 'lyrics';
}

abstract class abstractLyricsModuleFront extends abstractModuleFront implements iaLyricsModule
{
	protected $_packageName = 'lyrics';
}