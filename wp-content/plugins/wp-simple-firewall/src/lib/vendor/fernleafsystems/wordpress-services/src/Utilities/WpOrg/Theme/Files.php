<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme;

use FernleafSystems\Wordpress\Services;

/**
 * Class Files
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme
 */
class Files extends Services\Utilities\WpOrg\Base\PluginThemeFilesBase {

	use Base;

	/**
	 * Given a full root path on the file system for a file, locate the plugin to which this file belongs.
	 * @param string $fullFilePath
	 * @return Services\Core\VOs\Assets\WpThemeVo|null
	 */
	public function findThemeFromFile( string $fullFilePath ) {
		$theTheme = null;

		$sFragment = $this->getThemePathFragmentFromPath( $fullFilePath );

		if ( !empty( $sFragment ) && strpos( $sFragment, '/' ) > 0 ) {
			$oWpThemes = Services\Services::WpThemes();
			$dir = substr( $sFragment, 0, strpos( $sFragment, '/' ) );
			foreach ( $oWpThemes->getThemes() as $theme ) {
				if ( $dir == $theme->get_stylesheet() ) {
					$theTheme = $oWpThemes->getThemeAsVo( $dir );
					break;
				}
			}
		}
		return $theTheme;
	}

	/**
	 * Verifies the file exists on the SVN repository for the particular version that's installed.
	 * @param string $sFullFilePath
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function isValidFileFromTheme( $sFullFilePath ) {

		$theTheme = $this->findThemeFromFile( $sFullFilePath );
		if ( !$theTheme instanceof Services\Core\VOs\Assets\WpThemeVo ) {
			throw new \InvalidArgumentException( 'Not actually a theme file.', 1 );
		}
		if ( !$theTheme->isWpOrg() ) {
			throw new \InvalidArgumentException( 'Not a WordPress.org theme.', 2 );
		}

		// if uses SVN tags, use that version. Otherwise trunk.
		return ( new Repo() )
			->setWorkingSlug( $theTheme->stylesheet )
			->setWorkingVersion( $theTheme->version )
			->existsInVcs( $this->getRelativeFilePathFromItsInstallDir( $sFullFilePath ) );
	}

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
	 * @param string $fullFilePath
	 * @return string|null
	 */
	public function getOriginalFileFromVcs( $fullFilePath ) {
		$tmpFile = null;
		$theTheme = $this->findThemeFromFile( $fullFilePath );
		if ( $theTheme instanceof Services\Core\VOs\Assets\WpThemeVo ) {
			$tmpFile = ( new Repo() )
				->setWorkingSlug( $theTheme->stylesheet )
				->setWorkingVersion( $theTheme->version )
				->downloadFromVcs( $this->getRelativeFilePathFromItsInstallDir( $fullFilePath ) );
		}
		return $tmpFile;
	}

	/**
	 * @param string $sFile - can either be absolute, or relative to ABSPATH
	 * @return string|null - the path to the file relative to Plugins Dir.
	 */
	public function getThemePathFragmentFromPath( $sFile ) {
		$sFragment = null;

		if ( !Services\Services::WpFs()->isAbsPath( $sFile ) ) { // assume it's relative to ABSPATH
			$sFile = path_join( ABSPATH, $sFile );
		}
		$sFile = wp_normalize_path( $sFile );
		$sThemesDir = wp_normalize_path( get_theme_root() );

		if ( strpos( $sFile, $sThemesDir ) === 0 ) {
			$sFragment = ltrim( str_replace( $sThemesDir, '', $sFile ), '/' );
		}

		return $sFragment;
	}

	/**
	 * Gets the path of the plugin file relative to its own home plugin dir. (not wp-content/plugins/)
	 * @param string $file
	 * @return string
	 */
	protected function getRelativeFilePathFromItsInstallDir( $file ) {
		$sRelDirFragment = $this->getThemePathFragmentFromPath( $file );
		return substr( $sRelDirFragment, strpos( $sRelDirFragment, '/' ) + 1 );
	}
}