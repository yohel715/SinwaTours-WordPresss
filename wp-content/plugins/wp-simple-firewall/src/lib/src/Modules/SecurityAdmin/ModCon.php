<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\SecurityAdmin;

use FernleafSystems\Wordpress\Plugin\Shield;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\BaseShield;
use FernleafSystems\Wordpress\Services\Services;

class ModCon extends BaseShield\ModCon {

	const HASH_DELETE = '32f68a60cef40faedbc6af20298c1a1e';

	/**
	 * @var Lib\WhiteLabel\WhitelabelController
	 */
	private $whitelabelCon;

	/**
	 * @var Lib\SecurityAdmin\SecurityAdminController
	 */
	private $securityAdminCon;

	protected function setupCustomHooks() {
		add_action( $this->prefix( 'pre_deactivate_plugin' ), [ $this, 'preDeactivatePlugin' ] );
	}

	public function getWhiteLabelController() :Lib\WhiteLabel\WhitelabelController {
		if ( !$this->whitelabelCon instanceof Lib\WhiteLabel\WhitelabelController ) {
			$this->whitelabelCon = ( new Lib\WhiteLabel\WhitelabelController() )
				->setMod( $this );
		}
		return $this->whitelabelCon;
	}

	public function getSecurityAdminController() :Lib\SecurityAdmin\SecurityAdminController {
		if ( !$this->securityAdminCon instanceof Lib\SecurityAdmin\SecurityAdminController ) {
			$this->securityAdminCon = ( new Lib\SecurityAdmin\SecurityAdminController() )
				->setMod( $this );
		}
		return $this->securityAdminCon;
	}

	public function getSecAdminLoginAjaxData() :array {
		return $this->getAjaxActionData( 'sec_admin_login' );
	}

	protected function preProcessOptions() {
		/** @var Options $opts */
		$opts = $this->getOptions();

		// Verify whitelabel images
		$this->getWhiteLabelController()->verifyUrls();

		$opts->setOpt( 'sec_admin_users',
			( new Lib\SecurityAdmin\VerifySecurityAdminList() )
				->setMod( $this )
				->run( $opts->getSecurityAdminUsers() )
		);

		if ( hash_equals( $opts->getSecurityPIN(), self::HASH_DELETE ) ) {
			$opts->clearSecurityAdminKey();
			( new Lib\SecurityAdmin\Ops\ToggleSecAdminStatus() )
				->setMod( $this )
				->turnOff();
			// If you delete the PIN, you also delete the sec admins. Prevents a lock out bug.
			$opts->setOpt( 'sec_admin_users', [] );
		}
	}

	protected function handleModAction( string $action ) {
		switch ( $action ) {
			case  'remove_secadmin_confirm':
				( new Lib\SecurityAdmin\Ops\RemoveSecAdmin() )
					->setMod( $this )
					->remove();
				break;
			default:
				parent::handleModAction( $action );
				break;
		}
	}

	/**
	 * Used by Wizard. TODO: sort out the wizard requests!
	 * @param string $pin
	 * @return $this
	 * @throws \Exception
	 */
	public function setNewPinManually( string $pin ) {
		if ( empty( $pin ) ) {
			throw new \Exception( 'Attempting to set an empty Security PIN.' );
		}
		if ( !$this->getCon()->isPluginAdmin() ) {
			throw new \Exception( 'User does not have permission to update the Security PIN.' );
		}

		$this->setIsMainFeatureEnabled( true );
		$this->getOptions()->setOpt( 'admin_access_key', md5( $pin ) );
		( new Lib\SecurityAdmin\Ops\ToggleSecAdminStatus() )
			->setMod( $this )
			->turnOn();

		return $this->saveModOptions();
	}

	/**
	 * This is the point where you would want to do any options verification
	 */
	protected function doPrePluginOptionsSave() {
		/** @var Options $opts */
		$opts = $this->getOptions();

		// Restricting Activate Plugins also means restricting the rest.
		$plugins = $opts->getOpt( 'admin_access_restrict_plugins', [] );
		if ( in_array( 'activate_plugins', is_array( $plugins ) ? $plugins : [] ) ) {
			$opts->setOpt( 'admin_access_restrict_plugins',
				array_unique( array_merge( $plugins, [
					'install_plugins',
					'update_plugins',
					'delete_plugins'
				] ) )
			);
		}

		// Restricting Switch (Activate) Themes also means restricting the rest.
		$themes = $opts->getOpt( 'admin_access_restrict_themes', [] );
		if ( is_array( $themes ) && in_array( 'switch_themes', $themes ) && in_array( 'edit_theme_options', $themes ) ) {
			$opts->setOpt( 'admin_access_restrict_themes',
				array_unique( array_merge( $themes, [
					'install_themes',
					'update_themes',
					'delete_themes'
				] ) )
			);
		}

		$posts = $opts->getOpt( 'admin_access_restrict_posts', [] );
		if ( in_array( 'edit', is_array( $posts ) ? $posts : [] ) ) {
			$opts->setOpt( 'admin_access_restrict_posts',
				array_unique( array_merge( $posts, [ 'create', 'publish', 'delete' ] ) )
			);
		}
	}

	public function preDeactivatePlugin() {
		if ( !$this->getCon()->isPluginAdmin() ) {
			Services::WpGeneral()->wpDie(
				__( "Sorry, this plugin is protected against unauthorised attempts to disable it.", 'wp-simple-firewall' )
				.'<br />'.sprintf( '<a href="%s">%s</a>',
					$this->getUrl_AdminPage(),
					__( "You'll just need to authenticate first and try again.", 'wp-simple-firewall' )
				)
			);
		}
	}
}