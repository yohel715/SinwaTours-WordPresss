<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\Plugin\Lib\Debug;

use FernleafSystems\Wordpress\Plugin\Shield\Databases\Events;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\Insights\Strings;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\ModConsumer;
use FernleafSystems\Wordpress\Services\Services;

class RecentEvents {

	use ModConsumer;

	public function build() :string {
		$con = $this->getCon();
		return $this->getMod()
					->renderTemplate(
						'/wpadmin_pages/insights/overview/recent_events.twig',
						[
							'vars'    => [
								'insight_events' => $this->getData()
							],
							'strings' => [
								'title_recent'        => __( 'Recent Events Log', 'wp-simple-firewall' ),
								'box_receve_subtitle' => sprintf( __( 'Some of the most recent %s events', 'wp-simple-firewall' ), $con->getHumanName() ),
							]
						],
						true
					);
	}

	private function getData() :array {
		$con = $this->getCon();

		$aTheStats = array_filter(
			$con->loadEventsService()->getEvents(),
			function ( $evt ) {
				return isset( $evt[ 'recent' ] ) && $evt[ 'recent' ];
			}
		);

		/** @var Strings $oStrs */
		$oStrs = $con->getModule_Insights()->getStrings();
		$aNames = $oStrs->getInsightStatNames();

		/** @var Events\Select $oSel */
		$oSel = $con->getModule_Events()
					->getDbHandler_Events()
					->getQuerySelector();

		$aRecentStats = array_intersect_key(
			array_map(
				function ( $oEntryVO ) use ( $aNames ) {
					/** @var Events\EntryVO $oEntryVO */
					return [
						'name' => isset( $aNames[ $oEntryVO->event ] ) ? $aNames[ $oEntryVO->event ] : '*** '.$oEntryVO->event,
						'val'  => Services::WpGeneral()->getTimeStringForDisplay( $oEntryVO->created_at )
					];
				},
				$oSel->getLatestForAllEvents()
			),
			$aTheStats
		);

		$sNotYetRecorded = __( 'Not yet recorded', 'wp-simple-firewall' );
		foreach ( array_keys( $aTheStats ) as $sStatKey ) {
			if ( !isset( $aRecentStats[ $sStatKey ] ) ) {
				$aRecentStats[ $sStatKey ] = [
					'name' => isset( $aNames[ $sStatKey ] ) ? $aNames[ $sStatKey ] : '*** '.$sStatKey,
					'val'  => $sNotYetRecorded
				];
			}
		}

		return $aRecentStats;
	}
}