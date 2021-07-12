<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Scans\Wpv;

use FernleafSystems\Wordpress\Plugin\Shield;
use FernleafSystems\Wordpress\Services\Services;
use FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Vulnerabilities;

class Scan extends Shield\Scans\Base\BaseScan {

	protected function scanSlice() {
		/** @var ScanActionVO $oAction */
		$oAction = $this->getScanActionVO();
		$oTempRs = $oAction->getNewResultsSet();

		$oCopier = new Shield\Scans\Helpers\CopyResultsSets();
		foreach ( $oAction->items as $sFile => $sContext ) {
			$oNewRes = $this->scanItem( $sContext, $sFile );
			if ( $oNewRes instanceof Shield\Scans\Base\BaseResultsSet ) {
				$oCopier->copyTo( $oNewRes, $oTempRs );
			}
		}

		$aNewItems = [];
		if ( $oTempRs->hasItems() ) {
			foreach ( $oTempRs->getAllItems() as $item ) {
				$aNewItems[] = $item->getRawData();
			}
		}
		$oAction->results = $aNewItems;
	}

	/**
	 * @param string $context
	 * @param string $file
	 * @return ResultsSet
	 */
	protected function scanItem( $context, $file ) {
		$results = new ResultsSet();

		$sApiToken = $this->getCon()
						  ->getModule_License()
						  ->getWpHashesTokenManager()
						  ->getToken();

		if ( $context == 'plugins' ) {
			$WPP = Services::WpPlugins();
			$slug = $WPP->getSlug( $file );
			if ( empty( $slug ) ) {
				$slug = dirname( $file );
			}
			$version = $WPP->getPluginAsVo( $file )->Version;
			$lookerUpper = new Vulnerabilities\Plugin( $sApiToken );
		}
		else {
			$slug = $file;
			$version = Services::WpThemes()->getTheme( $slug )->get( 'Version' );
			$lookerUpper = new Vulnerabilities\Theme( $sApiToken );
		}

		$rawVuls = $lookerUpper->getVulnerabilities( $slug, $version );
		if ( is_array( $rawVuls ) && !empty( $rawVuls[ 'meta' ] ) && $rawVuls[ 'meta' ][ 'total' ] > 0 ) {

			foreach ( array_filter( $rawVuls[ 'vulnerabilities' ] ) as $vul ) {
				$VO = ( new Shield\Scans\Wpv\WpVulnDb\VulnVO() )->applyFromArray( $vul );
				$VO->provider = $rawVuls[ 'meta' ][ 'provider' ];

				$item = new ResultItem();
				$item->slug = $file;
				$item->context = $context;
				$item->wpvuln_id = $VO->id;
				$item->wpvuln_vo = $VO->getRawData();

				$results->addItem( $item );
			}
		}

		return $results;
	}
}