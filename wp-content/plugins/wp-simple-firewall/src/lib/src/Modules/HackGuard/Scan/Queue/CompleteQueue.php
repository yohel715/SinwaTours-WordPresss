<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\HackGuard\Scan\Queue;

use FernleafSystems\Wordpress\Plugin\Shield\Databases;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\HackGuard;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\HackGuard\ModCon;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\ModConsumer;
use FernleafSystems\Wordpress\Plugin\Shield\Scans;
use FernleafSystems\Wordpress\Services\Services;

/**
 * Class CompleteQueue
 * @package FernleafSystems\Wordpress\Plugin\Shield\Modules\HackGuard\Scan\Queue
 */
class CompleteQueue {

	use Databases\Base\HandlerConsumer;
	use ModConsumer;

	/**
	 * Take care here not to confuse the 2x DB Handlers
	 */
	public function complete() {
		/** @var ModCon $mod */
		$mod = $this->getMod();
		$con = $this->getCon();
		/** @var Databases\ScanQueue\Handler $dbh */
		$dbh = $this->getDbHandler();
		$oSel = $dbh->getQuerySelector();

		foreach ( $oSel->getDistinctForColumn( 'scan' ) as $scanSlug ) {

			$oScanCon = $mod->getScanCon( $scanSlug );

			$oResultsSet = ( new CollateResults() )
				->setScanController( $oScanCon )
				->setDbHandler( $dbh )
				->collate( $scanSlug );

			$con->fireEvent( $scanSlug.'_scan_run' );

			if ( $oResultsSet instanceof Scans\Base\BaseResultsSet ) {
				( new HackGuard\Scan\Results\ResultsUpdate() )
					->setScanController( $oScanCon )
					->update( $oResultsSet );

				if ( $oResultsSet->countItems() > 0 ) {
					$con->fireEvent( $scanSlug.'_scan_found' );
				}
			}

			/** @var Databases\ScanQueue\Delete $deleter */
			$deleter = $dbh->getQueryDeleter();
			$deleter->filterByScan( $scanSlug )->query();
		}

		/** @var HackGuard\Options $opts */
		$opts = $this->getOptions();
		if ( $opts->isScanCron() && !wp_next_scheduled( $con->prefix( 'post_scan' ) ) ) {
			wp_schedule_single_event(
				Services::Request()->ts() + 5,
				$con->prefix( 'post_scan' )
			);
		}
		$opts->setIsScanCron( false );
	}
}
