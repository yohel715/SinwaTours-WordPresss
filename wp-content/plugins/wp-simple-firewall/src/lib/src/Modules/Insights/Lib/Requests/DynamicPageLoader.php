<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\Insights\Lib\Requests;

use FernleafSystems\Utilities\Data\Adapter\DynPropertiesClass;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\Base\ModCon;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\ModConsumer;

/**
 * Class DynamicPageLoader
 * @package FernleafSystems\Wordpress\Plugin\Shield\Modules\Insights\Lib\Requests
 * @property string $load_type
 * @property string $load_variant
 */
class DynamicPageLoader extends DynPropertiesClass {

	use ModConsumer;

	private $params = [];

	/**
	 * @param array $params
	 * @return array
	 * @throws \Exception
	 */
	public function build( array $params ) :array {
		if ( empty( $params ) || empty( $params[ 'load_params' ] ) ) {
			throw new \Exception( 'No dynamic page loading params' );
		}
		$this->applyFromArray( $params[ 'load_params' ] );

		$this->verifyLoadParams();

		return [
			'html'       => $this->getContent(),
			'page_url'   => $this->getPageUrl(),
			'page_title' => $this->getPageTitle(),
		];
	}

	/**
	 * @throws \Exception
	 */
	private function verifyLoadParams() {
		if ( !in_array( $this->load_type, [ 'configuration' ] ) ) {
			throw new \Exception( 'Unsupported dynamic page load type' );
		}
	}

	private function getContent() :string {

		switch ( $this->load_type ) {
			case 'configuration':
				$content = $this->renderConfiguration();
				break;

			default:
				throw new \Exception( 'Unsupported dynamic page load type' );
		}

		return $content;
	}

	private function getPageUrl() :string {
		$con = $this->getCon();

		switch ( $this->load_type ) {
			case 'configuration':
				$url = $con->getModule( $this->load_variant )->getUrl_AdminPage();
				break;

			default:
				throw new \Exception( 'Unsupported dynamic page load type' );
		}
		return $url;
	}

	private function getPageTitle() :string {
		$con = $this->getCon();

		switch ( $this->load_type ) {
			case 'configuration':
				$title = sprintf( '%s: %s',
					__( 'Configuration', 'wp-simple-firewall' ),
					$con->getModule( $this->load_variant )->getMainFeatureName()
				);
				break;

			default:
				throw new \Exception( 'Unsupported dynamic page load type' );
		}
		return $title;
	}

	/**
	 * @throws \Exception
	 */
	private function renderConfiguration() :string {

		$mod = $this->getCon()->getModule( $this->load_variant );
		if ( !$mod instanceof ModCon ) {
			throw new \Exception( 'Invalid dynamic page load data (variant)' );
		}

		return $mod->renderOptionsForm();
	}
}