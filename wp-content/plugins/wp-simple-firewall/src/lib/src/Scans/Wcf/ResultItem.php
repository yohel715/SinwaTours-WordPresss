<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Scans\Wcf;

/**
 * Class ResultItem
 * @package FernleafSystems\Wordpress\Plugin\Shield\Scans\Wcf
 * @property string $path_full
 * @property string $path_fragment
 * @property string $md5_file_wp
 * @property bool   $is_checksumfail
 * @property bool   $is_missing
 */
class ResultItem extends \FernleafSystems\Wordpress\Plugin\Shield\Scans\Base\BaseResultItem {

	public function generateHash() :string {
		return md5( $this->path_full );
	}

	public function isReady() :bool {
		return !empty( $this->path_full ) && !empty( $this->md5_file_wp ) && !empty( $this->path_fragment );
	}
}