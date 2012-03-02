<?php

class GoogleMapHelper extends Helper {

	public $helpers=array('Js', 'Html','Form');

	public $markers = array();

	public $map = null;


	private $_defaultSettings = array(
		'width'    =>400,
		'height'    =>350,
		'zoom'    =>14,
		'type'    =>'ROADMAP',
		'longitude'=>130.3988742828369,
		'latitude'=>33.59095680034125,
		'marker_longitude'=>130.3988742828369,
		'marker_latitude'=>33.59095680034125,
		'localize'=>true,
		'showMarker'  =>true,
		'showInfoWindow'=>true,
		'markerIcon'=>'http://google-maps-icons.googlecode.com/files/home.png',
		'infoWindowText'=>'Change Me',
		'div'=>array(
			'id'=>'map_canvas'
		),
		'usesJquery'=>true,
		'autoCenterMarkers'=>false
	);

	public function prepare($assoc){
		$search = function($conditions,$type){
			foreach ($conditions as $key => $value) {
				if(preg_match("/$type/", $key)){
					return $value;
				}
			}
		};
		foreach ($assoc as $className => $value) {
			if(preg_match('/^PgGeo$|GoogleMap\.PgGeo$/',$value['className'])){
				$maping['className'] = $className;
				$maping['model'] =  $search($value['conditions'],'model');
				$maping['groupname'] = $search($value['conditions'],'groupname');
				return $maping;			
			}
		}
	}
		
	public function editor($options=null){
		$this->Html->script('http://maps.google.com/maps/api/js?sensor=false',false);
		$settings = Set::merge($this->_defaultSettings,$options);
		$script = '';
		if(!isset($settings['ModelName']) && empty($settings['ModelName'])){
			$ModelName = key($this->request->params['models']);
		}
		$modelObj = ClassRegistry::getObject($ModelName);
		$assocconfig = $this->prepare($modelObj->hasOne);

		$settings = array_replace_recursive($assocconfig,$settings);

		$id = $this->value($settings['className'].'.id');
		if(!empty($id)){
			$latitude = $this->value($settings['className'].'.point.1');
			$longitude = $this->value($settings['className'].'.point.0');
			$marker_latitude = $this->value($settings['className'].'.point.1');
			$marker_longitude = $this->value($settings['className'].'.point.0');
			$zoom = $this->value($settings['className'].'.zoom');
			if( !$latitude || !$longitude || !$marker_latitude || !$marker_longitude ){
				$latitude = $this->value($settings['className'].'.latitude');
				$longitude = $this->value($settings['className'].'.longitude');
				$marker_latitude = $this->value($settings['className'].'.marker_latitude');
				$marker_longitude = $this->value($settings['className'].'.marker_longitude');
			}
		}else{
			$latitude = $this->_defaultSettings['latitude'];
			$longitude = $this->_defaultSettings['longitude'];
			$marker_latitude = $this->_defaultSettings['marker_latitude'];
			$marker_longitude = $this->_defaultSettings['marker_longitude'];
			$zoom = $this->_defaultSettings['zoom'];
		}
		$hidden = $this->Form->hidden($settings['className'].'.id',array('value'=>$id,'div'=>false))."\n";
		$hidden .= $this->Form->hidden($settings['className'].'.longitude',array('value'=>$longitude,'div'=>false))."\n";
		$hidden .= $this->Form->hidden($settings['className'].'.latitude',array('value'=>$latitude,'div'=>false))."\n";
		$hidden .= $this->Form->hidden($settings['className'].'.marker_longitude',array('value'=>$marker_longitude,'div'=>false))."\n";
		$hidden .= $this->Form->hidden($settings['className'].'.marker_latitude',array('value'=>$marker_latitude,'div'=>false))."\n";
		$hidden .= $this->Form->hidden($settings['className'].'.zoom',array('value'=>$zoom,'div'=>false))."\n";
		$hidden .= $this->Form->hidden($settings['className'].'.model',array('value'=>$settings['model'],'div'=>false))."\n";
		$script = $hidden;
		$script .= '<div id="'.$settings['div']['id'].'" style="width:'.$settings['width'].'px;height:'.$settings['height'].'px;" /></div>';
		$script .= '<input type="text" id="address" size="30" />';
		$script .= '<input type="button" id="geocode_address" value="検索" onclick="geocode()">';

		$script .='
<script type="text/javascript">
var map;
var geocoder;
var centerChangedLast;
var reverseGeocodedLast;
var currentReverseGeocodeResponse;
var lng = '.$longitude.';
var lat = '.$latitude.';
var maker_lng = '.$marker_longitude.';
var maker_lat = '.$marker_latitude.';
	function initialize() {
		var latlng = new google.maps.LatLng(lat,lng);
		var marker_latlng = new google.maps.LatLng(maker_lat,maker_lng);
		var mapOptions = {
				zoom: '.$zoom.',
				center: latlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				scaleControl: true
		};
		//マップ追加
		map = new google.maps.Map(document.getElementById("'.$settings['div']['id'].'"), mapOptions);
		geocoder = new google.maps.Geocoder();
		//マーカー追加
		marker = new google.maps.Marker({
				position: marker_latlng,
				draggable: true,
				map: map
		});
		//イベントリスナー登録
		//zoom変更時
		google.maps.event.addListener(map, "zoom_changed", function() {
		  document.getElementById("'.$settings['className'].Inflector::camelize('zoom').'").value = map.getZoom();
		});
		//中心変更時
		google.maps.event.addListener(map, "center_changed", function() {
			document.getElementById("'.$settings['className'].Inflector::camelize('latitude').'").value = map.getCenter().lat();
			document.getElementById("'.$settings['className'].Inflector::camelize('longitude').'").value = map.getCenter().lng();
		});
		// マーカードラッグ中のイベントを追加
		google.maps.event.addListener(marker, "drag", function(marker){
			document.getElementById("'.$settings['className'].Inflector::camelize('marker_latitude').'").value = marker.latLng.lat();
			document.getElementById("'.$settings['className'].Inflector::camelize('marker_longitude').'").value = marker.latLng.lng();
		});
	}

	function geocode() {
		var address = document.getElementById("address").value;
		geocoder.geocode({
		"address": address,
		"partialmatch": true}, geocodeResult);
	}

	function geocodeResult(results, status) {
		if (status == "OK" && results.length > 0) {
			map.fitBounds(results[0].geometry.viewport);
			marker.setPosition(map.getCenter());
			document.getElementById("'.$settings['className'].Inflector::camelize('marker_latitude').'").value = map.getCenter().lat();
			document.getElementById("'.$settings['className'].Inflector::camelize('marker_longitude').'").value = map.getCenter().lng();
	    } else {
			alert("見つかりませんでした");
		}
	}

window.onload = initialize;
</script>
';
		return $script;

	}

	public function to_script(){
		$script='<script type="text/javascript">
		$(function(){
		';

		$script.=$this->map;

		if($this->_defaultSettings['showMarker'] && !empty($this->markers) && is_array($this->markers)){
		  $script.=implode($this->markers, " ");
		}

		if($this->_defaultSettings['autoCenterMarkers'])
		{ $script.= '
		var bounds = new google.maps.LatLngBounds();
		$.each(gMarkers,function (index, marker){ bounds.extend(marker.position);});
		gMap.fitBounds(bounds);
		';
		}

		$script.='
		});
		</script>';

		return $script;
	}

	function map($options=null){
		$settings = Set::merge($this->_defaultSettings,$options);
		$this->Js->link('http://maps.google.com/maps/api/js?sensor=true',false);
		$this->Js->link("http://code.google.com/apis/gears/gears_init.js",false);
		$map = "
			gMarkers = new Array();
			var noLocation = new google.maps.LatLng(".$settings['latitude'].", ".$settings['longitude'].");
			var initialLocation;
			var browserSupportFlag =  new Boolean();
			var myOptions = {
			  zoom: ".$settings['zoom'].",
			  mapTypeId: google.maps.MapTypeId.".$settings['type'].",
			  center:noLocation
			};

			//Global variables
			gMap = new google.maps.Map(document.getElementById(\"".$settings['div']['id']."\"), myOptions);

			";
			$this->map = $map;
	}


	function addMarker($options){
		if($options==null) return null;
		if(!isset($options['latitude']) || $options['latitude']==null || !isset($options['longitude']) || $options['longitude']==null) return null;
		if (!preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $options['latitude']) || !preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $options['longitude'])) return null;


		$marker = "
			gMarkers.push(
			  new google.maps.Marker({
			   position:new google.maps.LatLng(".$options['latitude'].",".$options['longitude']."),
			   map:gMap
			  }));
		";

		$this->markers[] = $marker;
	}

  }
?>