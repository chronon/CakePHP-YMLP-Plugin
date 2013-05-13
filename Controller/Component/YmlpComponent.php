<?php
/**
 * Ymlp Component
 *
 * PHP version 5
 *
 * @package		YmlpComponent
 * @author		Gregory Gaskill <gregory@chronon.com>
 * @license		MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link		https://github.com/chronon/CakePHP-YmlpComponent-Plugin
 */

App::uses('Component', 'Controller');

/**
 * YmlpComponent
 *
 * An interface to the YMLP API for newsletter subscriber management.
 *
 * @package		YmlpComponent
 */
class YmlpComponent extends Component {

/**
 * URL for the YMLP API
 *
 * @var string
 * @access public
 */
	public $apiUrl = 'https://www.ymlp.com/api/';

/**
 * Component settings
 *
 * @var array
 * @access public
 */
	public $settings = array();

/**
 * Mapping of local fields => YMLP fields
 *
 * @var array
 * @access public
 */
	public $fieldMap = array();

/**
 * Controller startup. Sets options from  APP/Config/bootstrap.php.
 *
 * @param Controller $controller Instantiating controller
 * @return void
 * @throws CakeException
 */
	public function startup(Controller $controller) {
		$this->settings = Configure::read('Ymlp.settings');
		$this->fieldMap = Configure::read('Ymlp.fieldMap');

		if (!$this->settings || !$this->fieldMap) {
			throw new CakeException('YMLP configuration not found.');
		}

		// set the output format for YMLP API calls
		$this->settings['Output'] = 'PHP';
	}

/**
 * A utility method to send anything to the YMLP API
 *
 * @param string $method The YMLP API method call
 * @param array  $data Data to pass to the YMLP method
 * @return string
 * @access public
 */
	public function utility($method, $data = array()) {
		$result = $this->_makePost($method, $data);
		$result = unserialize($result);
		return $result;
	}

/**
 * The primary method to format data, post it to the YMLP API, and then format
 * the returned result.
 *
 * @param string $method The YMLP API method call
 * @param array  $data Data to pass to the YMLP method
 * @return string
 * @access public
 */
	public function command($method, $data) {
		$data = $this->_prepPost($data);
		$result = $this->_makePost($method, $data);
		$message = $this->_setOutput($result);
		return $message;
	}

/**
 * Formats data to match local fields to configured YMLP fields.
 *
 * @param array $data The unformatted data
 * @param boolean $emailOnly Ignore other fields, use email address only
 * @return array The formatted data ready to post
 * @access protected
 */
	protected function _prepPost($data, $emailOnly = false) {
		$fields = array();
		foreach ($this->fieldMap as $local => $id) {
			if ($id == 'Email') {
				$fields["$id"] = $data[$local];
				if ($emailOnly) {
					return $fields;
				}
			} else {
				$fields["Field$id"] = $data[$local];
			}
		}
		return $fields;
	}

/**
 * Unserializes the result from the YMLP API
 *
 * @param array $result Data returned from the API
 * @return string Formatted data
 * @access protected
 */
	protected function _setOutput($result) {
		if ($result) {
			$result = unserialize($result);
			$message = 'Code ' . $result['Code'];
			$message .= ': ' . $result['Output'];
		} else {
			$message = 'YMLP connection error.';
		}
		return $message;
	}

/**
 * Uses cURL to make the API call
 *
 * @param string $method The YMLP API method call
 * @param array  $data Data to pass to the YMLP method
 * @return boolean
 * @access protected
 */
	protected function _makePost($method, $data) {
		// complete the url
		$url = $this->apiUrl . $method;

		// merge the config data and the submitted data
		$data = array_merge($this->settings, $data);

		// turn the data array into a string
		$data = http_build_query($data);

		$defaults = array(
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_URL => $url,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FORBID_REUSE => 1,
			CURLOPT_TIMEOUT => 15,
			CURLOPT_POSTFIELDS => $data,
		);

		$ch = curl_init();
		curl_setopt_array($ch, $defaults);
		if (!$result = curl_exec($ch)) {
			return false;
		}

		curl_close($ch);
		return $result;
	}

}