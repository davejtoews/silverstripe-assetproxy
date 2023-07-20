<?php

namespace BCairns\AssetProxy;

use SilverStripe\Assets\Filesystem;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTP;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extension;

/**
 * Class AssetProxy
 * Controller extension, handles requests for non-existent assets, tries to fetch from AssetProxy host
 */
class AssetProxy extends Extension
{
    use Configurable;

    private static $source = null;

	public static function getHost(){
		return self::config()->get('source');
	}

	public static function ensureDirectoryExists($path){
		$dirPath = dirname( Director::baseFolder() . '/' . $path );
		if( !file_exists($dirPath) ){
			Filesystem::makeFolder($dirPath);
		}
	}

	public static function copyFromSource($path){
		return self::getHost() && copy( self::getHost() . '/' . $path, '../' . $path );
	}

	public function onBeforeHTTPError404($request){
		$path = $request->getURL();
		if( substr($path,0,7) == 'assets/' && self::copyFromSource($path) ){
			$relPath = '../'.$path; // path relative to cwd
			header('Content-Type: '.HTTP::get_mime_type( $relPath ) );
			header('Content-Length: '.filesize( $relPath ) );
			readfile( $relPath );
			exit;
		}
	}

}
