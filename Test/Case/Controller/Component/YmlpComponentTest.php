<?php
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('YmlpComponent', 'Ymlp.Controller/Component');

class TestManageController extends Controller {
	// hi
}

class YmlpTestComponent extends YmlpComponent {

/**
 * Convenience method for testing.
 *
 * @return string
 */
	public function prepPost($data, $emailOnly = false) {
		return parent::_prepPost($data, $emailOnly);
	}

/**
 * Convenience method for testing.
 *
 * @return string
 */
	public function setOutput($result) {
		return parent::_setOutput($result);
	}
/**
 * Convenience method for testing.
 *
 * @return string
 */
	public function makePost($method, $data) {
		return parent::_makePost($method, $data);
	}

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
		$this->YmlpComponent = new YmlpTestComponent($Collection);
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$this->Controller = new TestManageController($CakeRequest, $CakeResponse);
		$this->YmlpComponent->startup($this->Controller);
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

	public function testPrepPost() {
		$data = array(
			'first_name' => 'Casi',
			'last_name' => 'Robot',
			'address' => '189 Chillsbury Rd',
			'city' => 'Santa Monica',
			'state' => 'CA',
			'other' => '',
			'zip' => '91471',
			'country' => 'US',
			'email' => 'casi@robot.com',
		);

		$expected = array(
			'Email' => 'casi@robot.com',
			'Field1' => 'Casi',
			'Field2' => 'Robot',
			'Field3' => '189 Chillsbury Rd',
			'Field4' => 'Santa Monica',
			'Field5' => 'CA',
			'Field6' => '91471',
		);

		$result = $this->YmlpComponent->prepPost($data);
		$this->assertEquals($expected, $result);

		// email only
		$expected = array(
			'Email' => 'casi@robot.com',
		);

		$result = $this->YmlpComponent->prepPost($data, true);
		$this->assertEquals($expected, $result);
	}

	public function testSetOutput() {
		$data = 'a:2:{s:4:"Code";s:1:"0";s:6:"Output";s:6:"Hello!";}';
		$expected = 'Code 0: Hello!';
		$result = $this->YmlpComponent->setOutput($data);
		$this->assertEquals($expected, $result);

		$data = null;
		$expected = 'YMLP connection error.';
		$result = $this->YmlpComponent->setOutput($data);
		$this->assertEquals($expected, $result);
	}

	public function testMakePost() {
		$method = 'Ping';
		$data = array();
		$result = $this->YmlpComponent->makePost($method, $data);
		$expected = 'a:2:{s:4:"Code";s:1:"0";s:6:"Output";s:6:"Hello!";}';
		$this->assertEquals($expected, $result);
	}

	public function testUtilityPing() {
		$method = 'Ping';
		$result = $this->YmlpComponent->utility($method);
		$expected = array(
			'Code' => '0',
			'Output' => 'Hello!'
		);
		$this->assertEquals($expected, $result);
	}

	public function testCommandContactsAdd() {
		$data = array(
			'first_name' => 'Casi',
			'last_name' => 'Robot',
			'address' => '189 Chillsbury Rd',
			'city' => 'Santa Monica',
			'state' => 'CA',
			'other' => '',
			'zip' => '91471',
			'country' => 'US',
			'email' => 'casi@robot.com',
		);
		$method = 'Contacts.Add';
		$expected = 'Code 0: casi@robot.com has been added';
		$result = $this->YmlpComponent->command($method, $data);
		$this->assertEquals($expected, $result);

		// remove the just added contact
		$data = array('email' => 'casi@robot.com');
		$result = $this->__removeContact($data);
		$expected = array(
			'Code' => '0',
			'Output' => 'casi@robot.com has been removed'
		);
		$this->assertEquals($expected, $result);
	}

	private function __removeContact($data) {
		$method = 'Contacts.Delete';
		$data = $this->YmlpComponent->prepPost($data, true);
		return $this->YmlpComponent->utility($method, $data);
	}

}