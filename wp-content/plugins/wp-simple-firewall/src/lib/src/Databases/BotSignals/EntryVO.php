<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Databases\BotSignals;

/**
 * Class EntryVO
 * @property string $ip
 * @property int    $notbot_at
 * @property int    $frontpage_at
 * @property int    $loginpage_at
 * @property int    $bt404_at
 * @property int    $btcheese_at
 * @property int    $btfake_at
 * @property int    $btinvalidscript_at
 * @property int    $btloginfail_at
 * @property int    $btlogininvalid_at
 * @property int    $btua_at
 * @property int    $btxml_at
 * @property int    $cooldown_at
 * @property int    $auth_at
 * @property int    $offense_at
 * @property int    $blocked_at
 * @property int    $unblocked_at
 * @property int    $bypass_at
 * @property int    $humanspam_at
 * @property int    $markspam_at
 * @property int    $unmarkspam_at
 * @property int    $captchapass_at
 * @property int    $captchafail_at
 * @property int    $ratelimit_at
 * @property int    $updated_at
 * @property int    $snsent_at
 */
class EntryVO extends \FernleafSystems\Wordpress\Plugin\Shield\Databases\Base\EntryVO {

	/**
	 * @inheritDoc
	 */
	public function __get( string $key ) {
		switch ( $key ) {

			case 'ip':
				$value = inet_ntop( parent::__get( $key ) );
				break;

			default:
				$value = parent::__get( $key );
				break;
		}
		return $value;
	}

	/**
	 * @inheritDoc
	 */
	public function __set( string $key, $value ) {
		switch ( $key ) {

			case 'ip':
				$value = inet_pton( $value );
				break;

			default:
				break;
		}

		parent::__set( $key, $value );
	}
}