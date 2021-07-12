<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin;

use FernleafSystems\Wordpress\Services\Core\VOs\Assets\WpPluginVo;
use FernleafSystems\Wordpress\Services\Services;

/**
 * Trait Base
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin
 */
trait Base {

	/**
	 * @var string
	 */
	private $sWorkingPluginSlug;

	/**
	 * @var string
	 */
	private $sWorkingPluginVersion;

	/**
	 * @return string
	 */
	public function getWorkingSlug() {
		return $this->sWorkingPluginSlug;
	}

	/**
	 * @return string
	 */
	public function getWorkingVersion() {
		$version = $this->sWorkingPluginVersion;
		if ( empty( $version ) ) {
			$p = Services::WpPlugins()->getPluginAsVo( $this->getWorkingSlug() );
			if ( $p instanceof WpPluginVo ) {
				$version = $p->Version;
			}
		}
		return $version;
	}

	/**
	 * @param string $sSlug
	 * @return $this
	 */
	public function setWorkingSlug( $sSlug ) {
		$this->sWorkingPluginSlug = $sSlug;
		return $this;
	}

	/**
	 * @param string $sV
	 * @return $this
	 */
	public function setWorkingVersion( $sV ) {
		$this->sWorkingPluginVersion = $sV;
		return $this;
	}
}