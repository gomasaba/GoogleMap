<?php
/**
 * AddressSaveGeoBehavior
 * 
 * save geo table. convert address to geocode
 * 
 * @author ohta
 */
App::uses('GeocodeApi', 'GoogleMap.Vendor');

class AddressSaveGeoBehavior extends ModelBehavior {

	public $_settings = array(
		'address_colum' => 'address',
		'zoom'=>18
	);

	public function setup($model, $settings = array())	{
		$this->_settings = array_replace_recursive($this->_settings,$settings);
		$this->_checkColumn($model,$this->_settings['address_colum']);
		$this->PgGeo = ClassRegistry::init('GoogleMap.PgGeo');

	}

	private function _checkColumn($model,$column) {
	   $col = $model->schema($column);
	   if(empty($col)) {
	   		throw new CakeException('Model ' . $model->alias . ' is missing column: ' . $column);
		}
	}

	public function saveGeo($model){
		try{
			if(!$model->data[$model->alias][$this->_settings['address_colum']]){
				throw new CakeException('Model address  is not set ');
			}
			$geo_code = GeocodeApi::getGeo($model->data[$model->alias][$this->_settings['address_colum']]);
			if(!$geo_code){
				throw new CakeException('Address search Geo Code. But Not Found');
			}
		}catch(CakeException $e){
			throw new CakeException($e);
		}
		$geo = $this->_checkExistsGeo($model);
		if($geo){
			$save['id'] = $geo['PgGeo']['id'];
		}
		$save['foreign_key'] = $model->data[$model->alias]['id'];
		$save['model'] = $model->alias;
		$save['marker_longitude'] = $geo_code['lng'];
		$save['marker_latitude'] = $geo_code['lat'];
		$save['zoom'] = $this->_settings['zoom'];
		return $this->PgGeo->save($save);
	}

	/**
	 * PgGeo record exists
	 *
	 * return false or data
	 */
	public function _checkExistsGeo($model){
		$conditions = array(
			'conditions'=>array(
				'model'=>$model->alias,
				'foreign_key'=>$model->data[$model->alias]['id'],
			)
		);
		return $this->PgGeo->find('first',$conditions);
	}



}