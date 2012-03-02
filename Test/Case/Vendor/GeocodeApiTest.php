<?php
App::uses('GeocodeApi', 'GoogleMap.Vendor');
// App::import('Vendor', 'GoogleMap.GeocodeApi');

class GeocodeApiTestCase extends CakeTestCase {


/**
 * Method executed before each test
 *
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * Method executed after each test
 *
 */
	public function tearDown() {
		parent::tearDown();
	}


	public function test_get_geo(){
		$resopnse = GeocodeApi::getGeo('福岡市中央区1-4-1');
		$expects = array(
				'lat' => '33.5775337',
				'lng' => '130.3865333',
		);
		$this->assertSame($expects,$resopnse);
	}

	public function test_get_geo_not_found(){
		$resopnse = GeocodeApi::getGeo('こんな住所はひけません');
		$this->assertFalse($resopnse);
	}


}