<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\License;

use FernleafSystems\Wordpress\Plugin\Shield\Modules\BaseShield;

class Processor extends BaseShield\Processor {

	protected function run() {
		/** @var ModCon $mod */
		$mod = $this->getMod();
		$mod->getLicenseHandler()->execute();
	}

	public function onWpLoaded() {
		( new Lib\PluginNameSuffix() )
			->setMod( $this->getMod() )
			->execute();
	}
}