<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Hashes;

/**
 * Class ApiInfo
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Hashes
 */
class ApiInfo extends Base {

	const RESPONSE_DATA_KEY = 'info';

	/**
	 * @return array|null
	 */
	public function getInfo() {
		return $this->query();
	}

	protected function getApiUrl() :string {
		return parent::getApiUrl().'/info';
	}
}