<?php
require_once 'CIPTest.php';

class ArtifactRotationSeriesTest extends CIPTest {

	protected $catalog_alias = 'AS';
	protected $rotational_asset_id = null;
	protected $rotational_assets_category_name = 'Rotationsbilleder';
	
	public function testSearchForRotationalAsset() {
		$front_facing_rotational_assets = $this->_client->metadata()->search(
			$this->catalog_alias,
			'web',
			null,
			null,
			'Categories is ' . $this->rotational_assets_category_name
		);
		$this->assertNotEmpty($front_facing_rotational_assets);
		$this->assertGreaterThan(10, $front_facing_rotational_assets['totalcount']);
		$this->assertNotEmpty($front_facing_rotational_assets['items']);

		$first_rotational_asset_id = $front_facing_rotational_assets['items'][1]['id'];
		$this->assertGreaterThan(0, $first_rotational_asset_id);
		return $first_rotational_asset_id;
	}
	
	public function testGetAssetFromID() {
		$master_asset_id = $this->testSearchForRotationalAsset();

		$master_asset = $this->_client->metadata()->search(
			$this->catalog_alias,
			'web',
			null,
			null,
			'ID is ' . $master_asset_id
		);

		$this->assertNotEmpty($master_asset);
		$this->assertGreaterThan(0, $master_asset['totalcount']);
		$this->assertNotEmpty($master_asset['items']);

		return $master_asset['items'][0];
	}
	
	public function testGetRotationalSeriesAssets() {
		$master_asset = $this->testGetAssetFromID();

		$related_assets = $this->_client->metadata()->getrelatedassets(
			$this->catalog_alias,
			$master_asset['id'],
			'isalternatemaster'
		);

		$this->assertNotEmpty($related_assets);
		
		$rotational_series_ids = $related_assets['ids'];
		// Include the master asset's id.
		$rotational_series_ids[] = $master_asset['id'];
		// Add 'ID is' to every element
		$rotational_series_ids = array_map( function($id) {
			return 'ID is '.$id;
		}, $rotational_series_ids);
		// Join with ' OR '
		$rotational_asset_querystring = implode(' OR ', $rotational_series_ids);

		$rotational_assets_metadata = $this->_client->metadata()->search(
			$this->catalog_alias,
			'web',
			null,
			null,
			$rotational_asset_querystring
		);

		// Now sort by filename (the {af4b2e00-5f6a-11d2-8f20-0000c0e166dc} field)
		$filename_field_guid = '{af4b2e00-5f6a-11d2-8f20-0000c0e166dc}';
		$sort_result = usort($rotational_assets_metadata['items'], function($a, $b)
		 use ($filename_field_guid) {
			$a_filename = $a[$filename_field_guid];
			$b_filename = $b[$filename_field_guid];
			return strcmp($a_filename, $b_filename);
		});

		$this->assertTrue($sort_result);
		echo "\nAssets in the correct ordering:\n";
		foreach($rotational_assets_metadata['items'] as $asset) {
			echo "\t" . $asset['id'] . "\n";
		}
	}
}
