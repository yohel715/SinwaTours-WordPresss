<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Base;

use FernleafSystems\Wordpress\Services;

/**
 * Class PluginThemeFilesBase
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Base
 */
abstract class PluginThemeFilesBase {

	/**
	 * @param string $fullFilePath
	 * @return bool
	 */
	public function replaceFileFromVcs( $fullFilePath ) {
		$sTmpFile = $this->getOriginalFileFromVcs( $fullFilePath );
		return !empty( $sTmpFile ) && Services\Services::WpFs()->move( $sTmpFile, $fullFilePath );
	}

	/**
	 * Verifies the file exists on the SVN repository for the particular version that's installed.
	 * @param string $sFullFilePath
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function verifyFileContents( $sFullFilePath ) {
		$sTmpFile = $this->getOriginalFileFromVcs( $sFullFilePath );
		return !empty( $sTmpFile )
			   && ( new Services\Utilities\File\Compare\CompareHash() )
				   ->isEqualFilesMd5( $sTmpFile, $sFullFilePath );
	}

	/**
	 * @param string $sFullFilePath
	 * @return string
	 */
	public function getOriginalFileMd5FromVcs( $sFullFilePath ) {
		$sFile = $this->getOriginalFileFromVcs( $sFullFilePath );
		return empty( $sFile ) ? null : md5_file( $sFile );
	}

	/**
	 * @param string $fullFilePath
	 * @return string|null
	 */
	abstract public function getOriginalFileFromVcs( $fullFilePath );

	/**
	 * Gets the path of the plugin file relative to its own home plugin dir. (not wp-content/plugins/)
	 * @param string $file
	 * @return string
	 */
	abstract protected function getRelativeFilePathFromItsInstallDir( $file );
}