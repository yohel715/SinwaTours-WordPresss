<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Scans\Wpv\Utilities;

use FernleafSystems\Wordpress\Plugin\Shield\Scans\Base;
use FernleafSystems\Wordpress\Plugin\Shield\Scans\Wpv;
use FernleafSystems\Wordpress\Services\Services;

class ItemActionHandler extends Base\Utilities\ItemActionHandlerAssets {

	/**
	 * @return Repair
	 */
	public function getRepairer() {
		return ( new Repair() )->setScanItem( $this->getScanItem() );
	}

	/**
	 * @param bool $success
	 */
	protected function fireRepairEvent( $success ) {
		/** @var Wpv\ResultItem $oItem */
		$oItem = $this->getScanItem();
		$this->getCon()->fireEvent(
			$this->getScanController()->getSlug().'_item_repair_'.( $success ? 'success' : 'fail' ),
			[
				'audit' => [
					'name' => Services::WpPlugins()->getPluginAsVo( $oItem->slug )->Name
				]
			]
		);
	}
}
