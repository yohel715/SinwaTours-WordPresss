<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Scans\Ptg;

use FernleafSystems\Wordpress\Plugin\Shield;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\HackGuard\Lib;
use FernleafSystems\Wordpress\Services\Core\VOs\Assets;
use FernleafSystems\Wordpress\Services\Utilities\File\Compare\CompareHash;
use FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\CrowdSourcedHashes\Query;
use FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin;
use FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme;

/**
 * Class FileScanner
 * @package FernleafSystems\Wordpress\Plugin\Shield\Scans\Ptg
 */
class FileScanner extends Shield\Scans\Base\Files\BaseFileScanner {

	/**
	 * @var Lib\Snapshots\Store
	 */
	private $assetStore;

	/**
	 * @param string $fullPath - in this case it's relative to ABSPATH
	 * @return ResultItem|null
	 */
	public function scan( string $fullPath ) {
		$item = null;
		// file paths are stored in the queue relatives to ABSPATH
		$fullPath = path_join( wp_normalize_path( ABSPATH ), $fullPath );
		try {
			$asset = ( new Plugin\Files() )->findPluginFromFile( $fullPath );
			if ( empty( $asset ) ) {
				$asset = ( new Theme\Files() )->findThemeFromFile( $fullPath );
			}
			if ( empty( $asset ) ) {
				throw new \Exception( sprintf( 'Could not load asset for: %s', $fullPath ) );
			}

			$item = $this->scanWithStore( $fullPath, $asset );
		}
		catch ( \Exception $e ) {
			error_log( $e->getMessage() );
		}

		return $item;
	}

	/**
	 * @param string                             $fullPath
	 * @param Assets\WpPluginVo|Assets\WpThemeVo $asset
	 * @return ResultItem|null
	 * @throws \InvalidArgumentException|\Exception
	 */
	private function scanWithStore( string $fullPath, $asset ) {
		$assetHashes = $this->getStore( $asset )->getSnapData();
		$pathFragment = str_replace( $asset->getInstallDir(), '', $fullPath );
		if ( empty( $assetHashes[ $pathFragment ] ) ) {
			$item = $this->getNewItem( $asset, $fullPath );
			$item->path_fragment = $pathFragment;
			$item->is_unrecognised = true;
		}
		elseif ( !( new CompareHash() )->isEqualFileMd5( $fullPath, $assetHashes[ $pathFragment ] ) ) {
			$item = $this->getNewItem( $asset, $fullPath );
			$item->path_fragment = $pathFragment;
			$item->is_different = true;
		}
		else {
			$item = null;
		}
		return $item;
	}

	/**
	 * @param string                             $fullPath
	 * @param Assets\WpPluginVo|Assets\WpThemeVo $asset
	 * @return ResultItem|null
	 * @throws \InvalidArgumentException|\Exception
	 */
	private function scanWithCsHashes( string $fullPath, $asset ) {
		$assetHashes = $this->loadCsHashes( $asset );
		$pathFragment = str_replace( $asset->getInstallDir(), '', $fullPath );

		$item = null;
		if ( empty( $assetHashes[ $pathFragment ] ) ) {
			$item = $this->getNewItem( $asset, $fullPath );
			$item->path_fragment = $pathFragment;
			$item->is_unrecognised = true;
		}
		else {
			$found = false;
			foreach ( $assetHashes[ $pathFragment ] as $hash ) {
				if ( ( new CompareHash() )->isEqualFileSha1( $fullPath, $hash ) ) {
					$found = true;
					break;
				}
			}

			if ( !$found ) {
				$item = $this->getNewItem( $asset, $fullPath );
				$item->path_fragment = $pathFragment;
				$item->is_different = true;
			}
		}
		return $item;
	}

	private function loadCsHashes( $asset ) {
		$uniqueId = md5( ( $asset instanceof Assets\WpPluginVo ) ? $asset->file : $asset->stylesheet );
		$tmpFileHandler = ( new Shield\Utilities\Tool\TmpFileStore() )
			->setCon( $this->getCon() );

		$hashes = $tmpFileHandler->load( $uniqueId );
		if ( empty( $hashes ) ) {
			$hashesResponse = ( $asset instanceof Assets\WpPluginVo ? new Query\Plugin() : new Query\Theme() )
				->getHashesFromVO( $asset );
			if ( !empty( $hashesResponse[ 'hashes' ] ) ) {
				$hashes = [ 'hashes' ];
				$tmpFileHandler->store( $uniqueId, $hashes );
			}
		}
		return $hashes;
	}

	/**
	 * @param Assets\WpPluginVo|Assets\WpThemeVo $asset
	 * @return string[]
	 * @throws \Exception
	 */
	private function getCSHashes( $asset ) {
		return $this->getStore( $asset )->getSnapData();
	}

	/**
	 * @param Assets\WpPluginVo|Assets\WpThemeVo $asset
	 * @return Lib\Snapshots\Store
	 * @throws \Exception
	 */
	private function getStore( $asset ) {

		// Re-Use the previous store if it's for the same Asset.
		if ( !empty( $this->assetStore ) ) {
			$uniqueId = ( $asset instanceof Assets\WpPluginVo ) ? $asset->file : $asset->stylesheet;
			$meta = $this->assetStore->getSnapMeta();
			if ( $uniqueId !== $meta[ 'unique_id' ] ) {
				unset( $this->assetStore );
			}
		}

		if ( empty( $this->assetStore ) ) {
			$this->assetStore = ( new Lib\Snapshots\StoreAction\Load() )
				->setMod( $this->getMod() )
				->setAsset( $asset )
				->run();
		}

		return $this->assetStore;
	}

	/**
	 * @param Assets\WpPluginVo|Assets\WpThemeVo $asset
	 * @param string                             $file
	 * @return ResultItem
	 */
	private function getNewItem( $asset, $file ) {
		/** @var ResultItem $item */
		$item = $this->getScanActionVO()->getNewResultItem();
		$item->path_full = $file;
		$item->path_fragment = $file; // will eventually be overwritten
		$item->is_unrecognised = false;
		$item->is_different = false;
		$item->is_missing = false;
		$item->context = ( $asset instanceof Assets\WpPluginVo ) ? 'plugins' : 'themes';
		$item->slug = ( $asset instanceof Assets\WpPluginVo ) ? $asset->file : $asset->stylesheet;
		return $item;
	}
}