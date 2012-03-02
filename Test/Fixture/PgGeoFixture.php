<?php
/**
 * PgGeoFixture
 *
 */
class PgGeoFixture extends CakeTestFixture {

	public $table = 'pg_geos';
/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => true),
		'foreign_key' => array('type' => 'integer', 'null' => false),
		// 'point' => array('type' => 'point', 'null' => false),
		// 'box' => array('type' => 'point', 'null' => false),
		'point' => array('type' => 'string', 'null' => false),
		'is_publish' => array('type' => 'datetime', 'null' => true),
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			// 'id' => ,
			'model' => 'Dammy',
			'foreign_key' => 1,
			'point' => '(130.39887428284,33.590956800341)',
		),
	);
}
