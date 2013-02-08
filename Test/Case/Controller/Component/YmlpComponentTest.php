<?php
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('YmlpComponent', 'Ymlp.Controller/Component');

class TestManageController extends Controller {
	// hi
}

class YmlpComponentTest extends CakeTestCase {

	public $YmlpSettings = array(
		'Key' => '123456789ABCDEFG',
		'Username' => 'casi',
		'Output' => 'PHP',
	);

	public $YmlpFieldmap = array(
		'email' => 'Email',
		'first_name' => '0',
		'last_name' => '1',
		'address' => '3',
		'city' => '4',
		'state' => '5',
		'zip' => '6'
	);

	public function setUp() {
		parent::setUp();
		$Collection = new ComponentCollection();
		$this->YmlpComponent = new YmlpComponent($Collection);
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$this->Controller = new TestManageController($CakeRequest, $CakeResponse);
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->YmlpComponent);
		unset($this->Controller);
	}

	/**
	 * @expectedException CakeException
	 * @expectedExceptionMessage YMLP configuration not found.
	 */
	public function testStartupNoConfig() {
		Configure::delete('Ymlp.settings');
		Configure::delete('Ymlp.fieldMap');
		$this->YmlpComponent->startup($this->Controller);
	}

	public function testStartupWithConfig() {
		$this->__setConfig();
		$this->YmlpComponent->startup($this->Controller);
	}

	public function testUtilityPing() {
		$this->__setConfig();
		$this->YmlpComponent->startup($this->Controller);

		$method = 'Ping';
		$result = $this->YmlpComponent->utility($method);
		$expected = array(
			'Code' => '0',
			'Output' => 'Hello!'
		);
		$this->assertEquals($expected, $result);
	}

	private function __setConfig() {
		if (!Configure::check('Ymlp.settings')) {
			Configure::write('Ymlp.settings', $this->YmlpSettings);
		}
		if (!Configure::check('Ymlp.fieldMap')) {
			Configure::write('Ymlp.fieldMap', $this->YmlpFieldmap);
		}
	}

}