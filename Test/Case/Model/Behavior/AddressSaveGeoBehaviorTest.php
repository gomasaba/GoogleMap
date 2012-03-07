<?php
App::uses('Model', 'Model');
App::uses('AddressSaveGeoBehavior', 'GoogleMap.Model/Behavior');

/**
 * Test Model
 *
 */
class Address extends Model{

/**
 * name property
 *
 */
	public $name = 'Address';

	public $actsAs = array(
		'GoogleMap.AddressSaveGeo'=>array(
			'address_colum'=>'address',
		)
	);

	public $hasOne = array(
		'Map' => array(
			'className' => 'GoogleMap.PgGeo',
			'foreignKey' => 'foreign_key',
			'conditions' => array('Map.model' => 'Address'),
			'cacheQueries' => true,
		),
	);
}


class AddressSaveGeoBehaviorTestCase extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
			'plugin.google_map.address',
			'plugin.google_map.pg_geo',
	);

/**
 * Do not load the fixtures by default
 *
 * @var boolean
 */
	// public $autoFixtures = false;

/**
 * Method executed before each test
 *
 */
	public function setUp() {
		$this->TestModel = ClassRegistry::init('Address');
		parent::setUp();
	}

/**
 * Method executed after each test
 *
 */	
	public function tearDown() {
		unset($this->TestModel);
		parent::tearDown();
	}	

	public function test_SaveGeo(){
		$this->loadFixtures('Address');
		$this->TestModel->id = 1;
		$this->TestModel->data = $this->TestModel->read();
		$this->assertTrue($this->TestModel->Behaviors->AddressSaveGeo->saveGeo($this->TestModel));
		$result = $this->TestModel->read();

		$expects = array(
			'Address' => array(
				'id' => 1,
				'name' => 'Lorem ipsum dolor sit amet',
				'address' => '福岡市中央区1-4-1',
				'created' => '2012-02-06 16:59:43',
				'modified' => '2012-02-06 16:59:43',
			),
			'Map' => array(
				'id' => 2,
				'model' => 'Address',
				'foreign_key' => 1,
			'point' => array(
				0 => '130.3865333',
				1 => '33.5775337',
			),
			),
		);
		$this->assertEqual($result,$expects);
	}


	public function test_SaveGeo_update(){
		// new geo record
		$this->TestModel->id = 1;
		$this->TestModel->data = $this->TestModel->read();
		$this->assertTrue($this->TestModel->Behaviors->AddressSaveGeo->saveGeo($this->TestModel));
		$result = $this->TestModel->read();
		// update geo record
		$this->TestModel->id = 1;
		$this->TestModel->saveField('address','東京都新宿区西新宿二丁目８番１号');
		$this->TestModel->data = $this->TestModel->read();
		$this->assertTrue($this->TestModel->Behaviors->AddressSaveGeo->saveGeo($this->TestModel));
		$result = $this->TestModel->read();

		unset($result['Address']['created']);
		unset($result['Address']['modified']);
		$expects = array(
			'Address' => array(
				'id' => 1,
				'name' => 'Lorem ipsum dolor sit amet',
				'address' => '東京都新宿区西新宿二丁目８番１号',
			),
			'Map' => array(
				'id' => 2,
				'model' => 'Address',
				'foreign_key' => 1,
				'point' => array(
					0 => '139.6916481',
					1 => '35.6891848',
				),
			),
		);
		$this->assertEqual($result,$expects);
	}


}