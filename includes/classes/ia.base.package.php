<?php
//##copyright##

interface iaLyricsPackage
{
	const PACKAGE_NAME = 'lyrics';

	const COLUMN_ID = 'id';
}

abstract class abstractLyricsPackageAdmin extends abstractPackageAdmin implements iaLyricsPackage
{
	protected $_packageName = 'lyrics';
}

abstract class abstractLyricsPackageFront extends abstractPackageFront implements iaLyricsPackage
{
	protected $_packageName = 'lyrics';
}