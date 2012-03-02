<?php
/**
 * GeocodeApi
 *
 * @author ohta
 */
App::uses('HttpSocket', 'Network/Http');

class GeocodeApi {
	
	const API = 'http://maps.google.com/maps/api/geocode/';

	/**
	 * parse geo code
	 *
	 * @return array('lat','lang') or false
	 */
	public static function getGeo($address,$type='json'){
		$params = array(
			'address'=>$address,
			'sensor'=>'false'
		);
		$url = self::API.$type.'?'.http_build_query($params);		

		$HttpSocket = new HttpSocket();
		$response = $HttpSocket->get($url);

		$body = ($response) ? json_decode($response->body(),true) : false;
		if(isset($body['results'][0])){
			$floattostr = function($val){
				preg_match( "#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#",trim($val),$o);
				return $o[1].sprintf('%d',$o[2]).($o[3]!='.'?$o[3]:'');
			};
			return array_map($floattostr,$body['results'][0]['geometry']['location']);
		}else{
			return false;
		}
	}

}