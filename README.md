### Postgresql insert point Plugin for cakephp2

pg_geosテーブルにpoint型を保存するだけのプラグイン。


1. GooglemMapヘルパーで地図表示
2. PgGeoモデルのsaveでマーカーをpointに保存

対象のモデルにはhasOneで

	'Map' => array(
			'className' => 'GoogleMap.PgGeo',
			'foreignKey' => 'foreign_key',
			'conditions' => array('Map.model' => 'Post'),
			'cacheQueries' => true,
		),
とか。

ビューでは

		echo $this->GoogleMap->editor();

コントローラーでは

	saveAll

