<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin;

use FernleafSystems\Wordpress\Services;

/**
 * Class Files
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin
 */
class Files extends Services\Utilities\WpOrg\Base\PluginThemeFilesBase {

	use Base;

	/**
	 * Given a full root path on the file system for a file, locate the plugin to which this file belongs.
	 * @param string $fullFilePath
	 * @return Services\Core\VOs\Assets\WpPluginVo|null
	 */
	public function findPluginFromFile( $fullFilePath ) {
		$thePlugin = null;

		$fragment = $this->getPluginPathFragmentFromPath( $fullFilePath );

		if ( !empty( $fragment ) && strpos( $fragment, '/' ) > 0 ) {
			$WPP = Services\Services::WpPlugins();
			$dir = substr( $fragment, 0, strpos( $fragment, '/' ) );
			foreach ( $WPP->getInstalledPluginFiles() as $pluginFile ) {
				if ( $dir == dirname( $pluginFile ) ) {
					$thePlugin = $WPP->getPluginAsVo( $pluginFile );
					break;
				}
			}
		}
		return $thePlugin;
	}

	/**
	 * Verifies the file exists on the SVN repository for the particular version that's installed.
	 * @param string $fullFilePath
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function isValidFileFromPlugin( $fullFilePath ) {

		$thePlugin = $this->findPluginFromFile( $fullFilePath );
		if ( !$thePlugin instanceof Services\Core\VOs\Assets\WpPluginVo ) {
			throw new \InvalidArgumentException( 'Not actually a plugin file.', 1 );
		}
		if ( !$thePlugin->isWpOrg() ) {
			throw new \InvalidArgumentException( 'Not a WordPress.org plugin.', 2 );
		}

		// if uses SVN tags, use that version. Otherwise trunk.
		return ( new Repo() )
			->setWorkingSlug( $thePlugin->slug )
			->setWorkingVersion( ( $thePlugin->svn_uses_tags ? $thePlugin->Version : 'trunk' ) )
			->existsInVcs( $this->getRelativeFilePathFromItsInstallDir( $fullFilePath ) );
	}

	/**
	 * @param string $fullFilePath
	 * @return bool
	 */
	public function replaceFileFromVcs( $fullFilePath ) {
		$tmpFile = $this->getOriginalFileFromVcs( $fullFilePath );
		return !empty( $tmpFile ) && Services\Services::WpFs()->move( $tmpFile, $fullFilePath );
	}

	/**
	 * @param string $fullFilePath
	 * @return string|null
	 */
	public function getOriginalFileFromVcs( $fullFilePath ) {
		$tmpFile = null;
		$thePlugin = $this->findPluginFromFile( $fullFilePath );
		if ( $thePlugin instanceof Services\Core\VOs\Assets\WpPluginVo ) {
			$tmpFile = ( new Repo() )
				->setWorkingSlug( $thePlugin->slug )
				->setWorkingVersion( ( $thePlugin->svn_uses_tags ? $thePlugin->Version : 'trunk' ) )
				->downloadFromVcs( $this->getRelativeFilePathFromItsInstallDir( $fullFilePath ) );
		}
		return $tmpFile;
	}

	/**
	 * @param string $sFile - can either be absolute, or relative to ABSPATH
	 * @return string|null - the path to the file relative to Plugins Dir.
	 */
	public function getPluginPathFragmentFromPath( $sFile ) {
		$sFragment = null;

		if ( !Services\Services::WpFs()->isAbsPath( $sFile ) ) { // assume it's relative to ABSPATH
			$sFile = path_join( ABSPATH, $sFile );
		}
		$sFile = wp_normalize_path( $sFile );
		$sPluginsDir = wp_normalize_path( WP_PLUGIN_DIR );

		if ( strpos( $sFile, $sPluginsDir ) === 0 ) {
			$sFragment = ltrim( str_replace( $sPluginsDir, '', $sFile ), '/' );
		}

		return $sFragment;
	}

	/**
	 * Gets the path of the plugin file relative to its own home plugin dir. (not wp-content/plugins/)
	 * @param string $file
	 * @return string
	 */
	protected function getRelativeFilePathFromItsInstallDir( $file ) {
		$sRelDirFragment = $this->getPluginPathFragmentFromPath( $file );
		return substr( $sRelDirFragment, strpos( $sRelDirFragment, '/' ) + 1 );
	}
}