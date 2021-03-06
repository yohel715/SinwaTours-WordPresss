<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Controller\Config\Ops;

use FernleafSystems\Wordpress\Services\Services;

class Read {

	/**
	 * @param string $path
	 * @return array
	 * @throws \Exception
	 */
	public static function FromFile( string $path ) :array {
		$FS = Services::WpFs();
		foreach ( [ 'json', 'php' ] as $sExt ) {
			$cfgFile = Services::Data()->addExtensionToFilePath( $path, $sExt );
			if ( $FS->isFile( $cfgFile ) ) {
				return self::FromString( $FS->getFileContentUsingInclude( $cfgFile ) );
			}
		}
		throw new \LogicException( 'No config file present for slug: '.basename( $path ) );
	}

	/**
	 * @param string $def
	 * @return array
	 * @throws \Exception
	 */
	public static function FromString( string $def ) :array {
		$spec = [];
		$def = trim( $def );

		if ( !empty( $def ) ) {
			$spec = json_decode( $def, true );
		}
		if ( empty( $spec ) || !is_array( $spec ) ) {
			throw new \Exception( 'Could not parse the definition file.' );
		}

		return $spec;
	}
}