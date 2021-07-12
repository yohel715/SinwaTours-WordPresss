<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes;

use FernleafSystems\Wordpress\Services\Utilities\HttpRequest;
use FernleafSystems\Wordpress\Services\Utilities\Integrations\RequestVO;

/**
 * Class ApiBase
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes
 */
abstract class ApiBase {

	const API_URL = 'https://wphashes.com/api/apto-wphashes';
	const API_VERSION = 1;
	const API_ENDPOINT = '';
	const REQUEST_TYPE = 'GET';
	const RESPONSE_DATA_KEY = '';

	protected static $API_TOKEN;

	/**
	 * @var RequestVO
	 */
	private $req;

	/**
	 * @var bool
	 */
	private $useQueryCache = false;

	/**
	 * @var array
	 */
	private static $QueryCache = [];

	/**
	 * @param string $apiToken
	 */
	public function __construct( $apiToken = null ) {
		$this->setApiToken( $apiToken );
	}

	protected function getApiUrl() :string {
		return sprintf( '%s/v%s/%s', static::API_URL, static::API_VERSION, static::API_ENDPOINT );
	}

	protected function getQueryData() :array {
		return empty( static::$API_TOKEN ) ? [] : [ 'token' => static::$API_TOKEN ];
	}

	/**
	 * @return RequestVO|mixed
	 */
	protected function getRequestVO() {
		if ( !isset( $this->req ) ) {
			$this->req = $this->newReqVO();
		}
		return $this->req;
	}

	/**
	 * @return RequestVO
	 */
	protected function newReqVO() {
		return new RequestVO();
	}

	/**
	 * @return array|mixed|null
	 */
	public function query() {
		$data = $this->fireRequestDecodeResponse();
		if ( is_array( $data ) ) {
			if ( strlen( static::RESPONSE_DATA_KEY ) > 0 ) {
				$data = $data[ static::RESPONSE_DATA_KEY ] ?? null;
			}
		}
		else {
			$data = null;
		}
		return $data;
	}

	/**
	 * @return array|null - null on failure
	 */
	protected function fireRequestDecodeResponse() {
		$sResponse = $this->fireRequest();
		return empty( $sResponse ) ? null : json_decode( $sResponse, true );
	}

	/**
	 * @return string
	 */
	protected function fireRequest() {
		$this->preRequest();
		switch ( static::REQUEST_TYPE ) {
			case 'POST':
				$response = $this->fireRequest_POST();
				break;
			case 'GET':
			default:
				$response = $this->fireRequest_GET();
				break;
		}
		return $response;
	}

	protected function preRequest() {
	}

	/**
	 * @return string
	 */
	protected function fireRequest_GET() {
		$response = null;

		$url = add_query_arg( $this->getQueryData(), $this->getApiUrl() );
		$sig = md5( $url );

		if ( $this->isUseQueryCache() && isset( self::$QueryCache[ $sig ] ) ) {
			$response = self::$QueryCache[ $sig ];
		}

		if ( is_null( $response ) ) {
			$response = ( new HttpRequest() )->getContent( $url );
			if ( $this->isUseQueryCache() ) {
				self::$QueryCache[ $sig ] = $response;
			}
		}

		return $response;
	}

	/**
	 * @return string|null
	 */
	protected function fireRequest_POST() {
		$http = new HttpRequest();
		$http
			->post(
				add_query_arg( $this->getQueryData(), $this->getApiUrl() ),
				[ 'body' => $this->getRequestVO()->getRawData() ]
			);
		return $http->isSuccess() ? $http->lastResponse->body : null;
	}

	/**
	 * @return bool
	 */
	public function isUseQueryCache() :bool {
		return (bool)$this->useQueryCache;
	}

	/**
	 * @param string $token
	 * @return $this
	 */
	public function setApiToken( $token ) {
		if ( is_string( $token ) && preg_match( '#^[a-z0-9]{32,}$#', $token ) ) {
			static::$API_TOKEN = $token;
		}
		return $this;
	}

	/**
	 * @param bool $useQueryCache
	 * @return $this
	 */
	public function setUseQueryCache( bool $useQueryCache ) {
		$this->useQueryCache = $useQueryCache;
		return $this;
	}
}