<?php
/**
 * The comments service allow working with user comments (annotations).
 * @see http://crc.canto.com/CIP/doc/CIP.html#comments
 */
namespace CIP\services\comments;
class CommentsService extends \CIP\services\BaseService {
	
	/**
	 * Return a list of all comments in a structure that has the "User Comment Thread" sub-table items on top level and nested within the "User Comments" sub-table.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#comments_get
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed Return a list of all comments in a structure that has the "User Comment Thread" sub-table items on top level and nested within the "User Comments" sub-table. Since version 4 (CIP 9.0) of the API the user is returned as a structure containing the user unique ID string as well as a display string. If you want the old behavior of just returning the display string you can specify an older API version using the apiversion named parameter.
	 */
	public function get($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
				'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Return a list of all comments from "User Comments" sub-table for the specified "User Comment Thread"
	 * @see http://crc.canto.com/CIP/doc/CIP.html#comments_getthread
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed Return a list of all comments from "User Comments" sub-table for the specified "User Comment Thread"
	 */
	public function getthread($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
				'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Add a new item to the "User Comment Thread" sub-table and an additional item to the "User Comments" sub-table.
	 * Request Body: JSON structure containing coordinates, type (icon, rectangle, polygon, timesegment, discussion), comment and optionally creationdate.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#comments_add
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result is returned in JSON format and consists of the ID of the created "User Comment Thread" item.
	 */
	public function add($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
				'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Add a new item to the "User Comments" sub-table.
	 * Request Body: JSON structure containing comment and optionally creationdate.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#comments_addcomment
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result is returned in JSON format and consists of the ID of the created "User Comments" item.
	 */
	public function addcomment($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
				'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Add a new discussion type item to the "User Comment Thread" sub-table and an additional item to the "User Comments" sub-table.
	 * Request Body: JSON structure containing comment and optionally creationdate.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#comments_adddiscussion
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result is returned in JSON format and consists of the ID of the created "User Comment Thread" item.
	 */
	public function adddiscussion($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
				'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Update a user comments thread's coordinates
	 * Request Body: JSON structure containing coordinates and type (icon, rectangle, polygon, timesegment, discussion).
	 * @see http://crc.canto.com/CIP/doc/CIP.html#comments_updatecoordinates
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function updatecoordinates($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
				'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Update a comment text in the "User Comments" sub-table.
	 * Request Body: JSON structure containing comment and optionally modificationdate.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#comments_updatecomment
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function updatecomment($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
				'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Update a discussion comment text in the "User Comments" sub-table.
	 * Request Body: JSON structure containing comment and optionally modificationdate.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#comments_updatediscussion
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function updatediscussion($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
				'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Delete an item from the "User Comment Thread" sub-table and the corresponding items from the "User Comments" sub-table.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#comments_deletethread
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function deletethread($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
				'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Delete an item from the "User Comments" sub-table.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#comments_deletecomment
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the comments thread in the catalog specified by the first path parameter.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function deletecomment($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
				'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Delete a discussion item from the "User Comment Thread" sub-table and the corresponding items from the "User Comments" sub-table.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#comments_deletediscussion
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the comments thread in the catalog specified by the first path parameter.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function deletediscussion($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
				'catalogname' => $catalogname
		), true);
	}
}