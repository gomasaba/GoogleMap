<?php
App::uses('Model', 'Model');
App::uses('PgGeo', 'GoogleMap.Model');
/**
 * For PostgreSQL point adn box field
 *
 * 
 */

 /**
 * Test Model
 *
 */
class Post extends Model{

/**
 * name property
 *
 */
	public $name = 'Post';

	public $hasOne = array(
		'Map' => array(
			'className' => 'GoogleMap.PgGeo',
			'foreignKey' => 'foreign_key',
			'conditions' => array('GoogleMap.model' => 'CommercialBuilding', 'GoogleMap.group' => 'venue'),
			'cacheQueries' => true,
		),
	);

}
class PgGeoTestCase extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
			'plugin.google_map.pg_geo',
			'plugin.google_map.post',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp(){
		$this->PgGeo = ClassRegistry::init('GoogleMap.PgGeo');
		$this->Post = ClassRegistry::init('Post');
		parent::setUp();
	}
/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->PgGeo);
		ClassRegistry::flush();
	}

	public function testsave() {
		// var_dump($this->PgGeo->find('all'));
		$insert = array(
			'model' => 'Posts',
			'foreign_key' => '100',
			'longitude' => '130.39887428284',
			'latitude' => '33.590956800341',
			'marker_longitude' => '130.39887428284',
			'marker_latitude' => '33.590956800341',
		);
		$this->PgGeo->set($insert);
		$this->PgGeo->save($insert);
		$results = $this->PgGeo->read(null,$this->PgGeo->id);
	}

	public function testsave_update() {
		// var_dump($this->PgGeo->find('all'));
		$insert = array(
			'id'=>1,
			'model' => 'Posts',
			'foreign_key' => '100',
			'longitude' => '100.9876541231',
			'latitude' => '20.12345678910',
			'marker_longitude' => '100.9876541231',
			'marker_latitude' => '20.12345678910',
		);
		$this->PgGeo->set($insert);
		$this->PgGeo->save($insert);
		$results = $this->PgGeo->read(null,$this->PgGeo->id);
		$expects = array(
			'PgGeo' => array(
				'id' => 1,
				'model' => 'Posts',
				'foreign_key' => 100,
				'point' => array(
					0 => '100.9876541231',
					1 => '20.1234567891',
				),
			)
		);
		$this->assertEqual($results,$expects);
	}

	public function testassociation(){
	}
}
