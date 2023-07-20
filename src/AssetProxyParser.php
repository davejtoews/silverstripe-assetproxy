<?php

namespace BCairns\AssetProxy;

use BCairns\AssetProxy\AssetProxy;
use SilverStripe\Core\Extension;

/**
 * Class AssetProxy_Parser
 * Extends ShortcodeParser, looks for links to /assets/ in HTML fields and ensures their directories exist
 */
class AssetProxyParser extends Extension
{

	public function onAfterParse($content){

		if( AssetProxy::getHost() ){
			// find paths to /assets/, ensure their directories exist
			preg_match_all(
				'@(["|\'])/?(assets/.+?)\\1@',
				$content,
				$matches
			);
			foreach( $matches[2] as $match ){
				AssetProxy::ensureDirectoryExists($match);
			}
		}

	}

}
