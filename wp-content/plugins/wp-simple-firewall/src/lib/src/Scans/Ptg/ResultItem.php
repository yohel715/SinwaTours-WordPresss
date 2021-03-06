<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Scans\Ptg;

/**
 * Class ResultItem
 * @package FernleafSystems\Wordpress\Plugin\Shield\Scans\Ptg
 * @property string $path_full
 * @property string $path_fragment
 * @property string $slug
 * @property string $context
 * @property string $is_unrecognised
 * @property string $is_different
 * @property string $is_missing
 */
class ResultItem extends \FernleafSystems\Wordpress\Plugin\Shield\Scans\Base\BaseResultItem {

	public function generateHash() :string {
		return md5( $this->path_full );
	}
}