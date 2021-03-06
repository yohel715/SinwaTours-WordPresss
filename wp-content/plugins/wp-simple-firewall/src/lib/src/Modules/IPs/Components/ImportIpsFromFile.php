<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\IPs\Components;

use FernleafSystems\Wordpress\Plugin\Shield;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\IPs;
use FernleafSystems\Wordpress\Services\Services;

class ImportIpsFromFile extends Shield\Modules\Base\Common\ExecOnceModConsumer {

	protected function run() {
		foreach ( [ 'black', 'white', 'block', 'bypass' ] as $type ) {
			$this->runFileImport( $type );
		}
	}

	private function runFileImport( string $type ) {
		$FS = Services::WpFs();

		$fileImport = $FS->findFileInDir( 'ip_import_'.$type, $this->getCon()->paths->forFlag() );
		if ( $FS->isFile( $fileImport ) ) {
			$content = $FS->getFileContent( $fileImport );
			if ( !empty( $content ) ) {
				$add = ( new IPs\Lib\Ops\AddIp() )->setMod( $this->getMod() );
				foreach ( array_map( 'trim', explode( "\n", $content ) ) as $sIP ) {
					$add->setIP( $sIP );
					try {
						in_array( $type, [ 'white', 'bypass' ] ) ?
							$add->toManualWhitelist( 'file import' )
							: $add->toManualBlacklist( 'file import' );
					}
					catch ( \Exception $e ) {
					}
				}
			}
			$FS->deleteFile( $fileImport );
		}
	}
}