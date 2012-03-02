<?php
App::uses('Inflector', 'Utility');

App::uses('AppModel', 'Model');
/**
 * For PostgreSQL point adn box field
 *
 * 
 */
class PgGeo extends AppModel {

	const PointRegex = '/(\d+(?:\.\d+)),(\d+(?:\.\d+))/';

/**
 * overwride save method,
 * point and box not escape
 *
 */
	public function save($data = null, $validate = true, $fieldList = array()) {
		$this->set($data);
		$id = null;
		$data = $this->data[$this->alias];

		if(array_key_exists('marker_longitude',$data) && array_key_exists('marker_latitude',$data)){
			$data['point'] = 'point('. $data['marker_longitude'].','. $data['marker_latitude'] .')';
			unset($data['longitude']);
			unset($data['latitude']);
			unset($data['marker_longitude']);
			unset($data['marker_latitude']);
		}
		if (array_key_exists($this->primaryKey,$data) && !empty($data[$this->primaryKey])) {
			$id = $data[$this->primaryKey];
		}

		$db = $this->getDatasource();
		foreach($data as $key=>$value){
			if($key != 'point' && $key != 'box' ){
				$data[$key] = $db->value($value);
			}
		}
		if($id){
			$fields = array_keys($data);
			$values = array_values($data);
			foreach($fields as $key=>$val){
				$updatefileds[] = '"'.$val.'"'.'='.$values[$key];
			}
			$query = array(
				'table' => $db->fullTableName($this),
				'fields' => implode(', ',$updatefileds),
				'conditions' => $db->conditions(array('id'=>$id))
			);
			$sql = $db->renderStatement('update',$query);			
		}else{
			unset($data['id']);
			$fields = implode(',',array_keys($data));
			$values = implode(',',array_values($data));
			$query = array(
				'table' => $db->fullTableName($this),
				'fields' => $fields,
				'values' => $values,
			);
			$sql = $db->renderStatement('create',$query);			
		}
		if ($db->execute($sql)) {
			if (empty($id)) {
				$id = $db->lastInsertId($db->fullTableName($this, false), $this->primaryKey);
			}
			$this->setInsertID($id);
			$this->id = $id;
			return true;
		}
		$this->onError();
		return false;
	}

/**
 * afterFind
 *
 * point and box return array
 *
 */

	public function afterFind($results, $primary = false) {
		$replace = function($val) {
			if(preg_match(PgGeo::PointRegex,$val, $result)){
				return array($result['1'],$result['2']);
			}
		};
		if(is_array($results)){
			foreach ($results as $key => $value) {
				$results[$key][$this->alias]['point'] = $replace($value[$this->alias]['point']);
			}
		}
		// var_dump($results);
		return $results;
	}
}
