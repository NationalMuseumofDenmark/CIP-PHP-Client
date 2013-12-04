<?php
/**
 * The main purpose of the metadata service is to provide operations for searching, retrieving, and modifying metadata. When searching you have the following options to keep the result:
 * Immediately return all IDs of the resulting items.
 * Immediately return metadata field values for each of the items of the result.
 * Store the resulting item IDs in a collection by optionally combining this search result with the previous contents of the collection. You then use the getfieldvalues operation to retrieve metadata for items in the collection. This way you can implement “paging” through a long list of items without returning metadata for all items found in a single operation.
 *
 * Collections 
 * The search operations allow you to optionally store the result in a named collection which is stored in the current session (see session handling above). You give the collection a name which has to be unique within the current session. The collection is bound to a specific catalog and table within the catalog and only contains a list of item IDs.
 * 
 * After storing a search result in a collection you can then either retrieve the metadata for items in the collection using a getfieldvalues operation or combine the collection with the result of a subsequent search operation. Each search always returns the total number of items found. The getfieldvalues operation allows you to specify a starting offset in the item IDs and a maximum number of items to return. This way you can implement client-side “paging” through a long list of resulting items.
 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata
 */
namespace CIP\services\metadata;
class BaseMetadataService extends \CIP\services\BaseService {
	
	/**
	 * Return a list of all catalogs that the given user is able to work with.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_getcatalogs
	 * @return mixed The result is the list of catalog names.
	 */
	public function getcatalogs() {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(), true);
	}
	
	/**
	 * Return a list of all table names of a catalog.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_gettables
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result is the list of table names.
	 */
	public function gettables($catalog, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog ), array(
			'catalogname' => $catalogname
		), true);
	}

	// TODO: Check that the serialization of the $field is actually working correctly.
	// TODO: Check that the optional $view if left out the URL if not specified.
	/**
	 * Return a description of all the fields of a given table.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_getlayout
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param string[optional] $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters.
	 * @param string[optional] $table You may want to specify the table to return the layout for. The default is "AssetRecords".
	 * @param string[optional] $locale The two-letter language code (ISO 639-1) to be used for the metadata field names and values for the result. This parameter affects the way field names and language-dependent metadata values are returned. For example you can specify “fr” to return all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string[][optional] $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request.
	 * @param string[optional] $serveraddress The DAM server IP address for later catalog access. e.g. localhost, 192.168.0.2
	 * @param string[optional] $user string The user name for login to the server for later catalog access.
	 * @param string[optional] $password string The password for login to the server. The user’s password to be used for later catalog access
	 * @param string[optional] $catalogname The DAM system catalog name for later catalog access e.g. Sample Catalog
	 * @return mixed The result is the list of field definitions of the given table. Each field definition contains the type of the field, the field name in the language of the specified locale and the user editable flag. Fields of type Enum also contain a list of possible values which consist of an ID and a name in the given locale.
	 */
	public function getlayout($catalog, $view = null, $table = null, $locale = null, $field = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $view ), array(
			'table' => $table,
			'locale' => $locale,
			'field' => $field,
			'catalogname' => $catalogname
		), true);
	}

	// TODO: Add support for _parameters in the URL, this is needed when using the queryname parameter. Check example b) in the documentation.
	/**
	 * Perform a search using a combination of QuickSearch, query string, or configured named query. 
	 * You must specify at least one of the following parameters: quicksearchstring, queryname, querystring to perform a search. 
	 * You can combine a quicksearch string with a normal search query string or named query. If you specify more than one parameter the single queries will be joined to complex one with the AND operator.
	 * A query configuration can be defined in different ways:
 	 * - A “QuickSearch” performed using a simple search string that searches across multiple metadata fields depending on the DAM system (use parameter quicksearchstring).
 	 * - A preset query stored in the DAM system (use parameter queryname). This is best suited for searches using a fixed query that is also used by other users of the DAM system.
 	 * - A query string that may contain placeholders to be replaced with URL named parameters at runtime (use parameter queryname). This is best suited for searches using a query with some variable parts that are specified in the request.
 	 * - A query string explicitly provided with the request (use parameter querystring). This is best suited for any query that is created at runtime.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_search
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param string[optional] $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters. If no view or fields are specified then the resulting list of items is just an array of item IDs.
	 * @param string[optional] $quicksearchstring The text to perform a quicksearch with. It can be combined with a normal search query string or named query. The result depends on the DAM system and its configuration.
	 * @param string[optional] $queryname The name of a query defined in the CIP configuration file. It can be combined with a quicksearchstring or a normal search query string. The defined query is either one that is preset in the DAM system or is configured using a query string with optional placeholders. By convention the placeholders are named using an underscore as a prefix to avoid using a name that is later being defined by CIP. See the configuration file section for details on how to define queries.
	 * @param string[optional] $querystring The complete query string as expected by the DAM system. It can be combined with a quicksearchstring or a named query. You need to make sure all special characters are correctly escaped if you want to pass this parameter in an HTTP request URL. It is recommended to pass the parameter in the body of the request instead.
	 * @param integer[optional] $startindex The index (zero-based) to start returning the items. Using this parameter you can page through the result list by starting with 0 and then incrementing by a given number. The default is 0 which returns the items starting with the first one.
	 * @param integer[optional] $maxreturned The maximum number of items returned by this operation. You may use this parameter to limit the size of the resulting JSON data. When used together with the startindex parameter you can implement paging through the result list. The default is to return all items starting at the one specified by the startindex parameter. Due to changes being encountered in the catalog this operation may return less than the specified number of items if items in the given range have been deleted from the catalog. However, to do proper paging you should start the next getfieldvalues operation at the index you calculate from the given parameters startindex and maxreturned.
	 * @param string[optional] $locale The two-letter language code (ISO 639-1) to be used for parsing the query string and also for the metadata field values to be returned. This parameter affects the way language-dependent metadata values are returned. For example you can specify “fr” to interpret date values in the query in French format and to return all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the "user.language" Java VM parameter when starting the web application server).
	 * @param string[][optional] $sortby A field name or field ID with optional sort direction separated by colon (e.g. ID:descending or ID) to specify the fields to be used for sorting the result. The default is that the result is not sorted by any field. Supported values for sort direction: ascending (Default), descending.
	 * @param string[optional] $collection The name of a collection to save the resulting list of IDs. If you leave the value empty then CIP will create a unique collection name for you and will return this name in the result. This can be used for a temporary collection to make sure the name is unique in the session. When also using the parameter combine you can combine the existing collection contents with the result of this search operation.
	 * @param string[optional] $combine This parameter is only used when using a stored collection (see collection parameter above). It specifies how the result of this search operation is combined with the contents of the collection specified. If not specified the collection is created from this search operation’s result. Supported values: "new" Default, do not use the previous contents of the specified collection but store the result of this search operation in the collection. "narrow" Only keep items in the collection that are also in the result of this search operation. "broaden" Add all items of the result of this search operation to the collection (if not contained already).
	 * @param string[optional] $table You may want to specify the table in which the search should be performed. The default is "AssetRecords".
	 * @param string[][optional] $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request. Virtual fields (Available since: 8.6.1): {50f54d0a-0ebe-46ce-bf3c-dbb744349650} UID of a virtual field that contains the number of records being assigned to a category. {b46eddc9-dc90-4e31-9474-bee1b9a3fd12} UID of a virtual field that contains the number of records being assigned to a category including its sub-categories. {e85fd04a-7e4f-4718-9879-92c0f22ba892} UID of a virtual field that contains list of names of fields that the current user is allowed to modify.
	 * @param string[optional] $catalogname The DAM system catalog name for later catalog access e.g. Sample Catalog
	 * @return mixed The result is returned in JSON format and consists of the total number of items returned and a list of items with the field values defined in the given view. Since version 4 (CIP 9.0) of the API the value for a field of type "User UID" is returned as a structure containing the user unique ID string as well as a display string. If you want the old behavior of just returning the display string you can specify an older API version using the apiversion named parameter. If no view and no collection are specified then the list of items is just an array of item IDs. If no view but a collection is specified the result just returns the total count and the name of the collection. The item field values or IDs can then be retrieved using the getfieldvalues operation.
	 */
	public function search($catalog, $view = null, $quicksearchstring = null, $queryname = null, $querystring = null, $startindex = null, $maxreturned = null, $locale = null, $sortby = null, $collection = null, $combine = null, $table = null, $field = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $view ), array(
			'quicksearchstring' => $quicksearchstring,
			'queryname' => $queryname,
			'querystring' => $querystring,
			'startindex' => $startindex,
			'maxreturned' => $maxreturned,
			'locale' => $locale,
			'sortby' => $sortby,
			'collection' => $collection,
			'combine' => $combine,
			'table' => $table,
			'field' => $field,
			'catalogname' => $catalogname
		), true);
	}

	/**
	 * Set a query string or named query as a user live filter at the CIP session (see session handling above).
	 * This query is always used when searching inside a catalog to limit the result to items also matching this query.
	 * This way clients can be limited to see only a subset of assets.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_setfilterquery
	 * @param string[optional] $table You may want to specify the table to which the filter should be assigned. The default is "AssetRecords".
	 * @param string[optional] $queryname The name of a query defined in the CIP configuration file. The defined query is either one that is preset in the DAM system or is configured using a query string with placeholders. See the configuration file section for details on how to define queries.
	 * @param string[optional] $querystring The complete query string as expected by the DAM system. You need to make sure all special characters are correctly escaped if you want to pass this parameter in an HTTP request URL. It is recommended to pass the parameter in the body of the request instead.
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function setfilterquery($table = null, $queryname = null, $querystring = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'table' => $table,
			'queryname' => $queryname,
			'querystring' => $querystring
		));
	}
	
	/**
	 * Clear stored user live filter from the CIP session. 
	 * You can use this operation to remove user live filter from the CIP session.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_clearfilterquery
	 * @param string[optional] $table The name of table for which the stored filter should be removed. The default is "AssetRecords".
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function clearfilterquery($table = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'table' => $table
		));
	}

	/**
	 * Retrieve the metadata fields or IDs of items in a stored collection. You can specify an offset to start at and a maximum number of items returned.
	 * You specify the items either by setting the named parameter collection or by specifying the catalog and item ID using path parameters 1 and 2 respectively.
	 * This is the collection-based version of the service call.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_getfieldvalues
	 * @param string[optional] $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters. If no view or fields are specified then the resulting list of items is just an array of item IDs.
	 * @param string $collection The name of an existing collection in the current session.
	 * @param integer[optional] $startindex The index (zero-based) to start returning the items. Using this parameter you can page through the result list by starting with 0 and then incrementing by a given number. The default is 0 which returns the items starting with the first one.
	 * @param integer[optional] $maxreturned The maximum number of items returned by this operation. You may use this parameter to limit the size of the resulting JSON data. When used together with the startindex parameter you can implement paging through the result list. The default is to return all items starting at the one specified by the startindex parameter. Due to changes being encountered in the catalog this operation may return less than the specified number of items if items in the given range have been deleted from the catalog. However, to do proper paging you should start the next getfieldvalues operation at the index you calculate from the given parameters startindex and maxreturned.
	 * @param string[optional] $locale The two-letter language code (ISO 639-1) to be used for the metadata field values for the result. This parameter affects the way language-dependent metadata values are returned. For example you can specify “fr” to return all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string[][optional] $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request. Virtual fields (Available since: 8.6.1): {50f54d0a-0ebe-46ce-bf3c-dbb744349650} UID of a virtual field that contains the number of records being assigned to a category. {b46eddc9-dc90-4e31-9474-bee1b9a3fd12} UID of a virtual field that contains the number of records being assigned to a category including its sub-categories. {e85fd04a-7e4f-4718-9879-92c0f22ba892} UID of a virtual field that contains list of names of fields that the current user is allowed to modify.
	 * @param string[optional] $catalogname The DAM system catalog name for later catalog access e.g. Sample Catalog
	 * @return mixed The result is returned in JSON format and consists of the total number of items returned and an optional list of items with IDs or field values defined in the given view. If no view is specified then the list of items is just an array of item IDs. Since version 4 (CIP 9.0) of the API the value for a field of type "User UID" is returned as a structure containing the user unique ID string as well as a display string. If you want the old behavior of just returning the display string you can specify an older API version using the apiversion named parameter.
	 **/
	public function getfieldvalues_collection($view = null, $collection, $startindex = null, $maxreturned = null, $locale = null, $field = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $view ), array(
			'collection' => $collection,
			'startindex' => $startindex,
			'maxreturned' => $maxreturned,
			'locale' => $locale,
			'field' => $field,
			'catalogname' => $catalogname
		), true);
	}

	/**
	 * Retrieve the metadata fields or IDs of items in a stored collection. You can specify an offset to start at and a maximum number of items returned.
	 * You specify the items either by setting the named parameter collection or by specifying the catalog and item ID using path parameters 1 and 2 respectively.
	 * This is the catalog-based version of the service call.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_getfieldvalues
	 * @param string $catalog The catalog alias for the catalog to work with.
	 * @param string[optional] $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters. If no view or fields are specified then the resulting list of items is just an array of item IDs.
	 * @param number $id The ID of the item in the catalog to return field values for.
	 * @param string[optional] $table The name of the table to return field values for. The default is "AssetRecords".
	 * @param string[optional] $locale The two-letter language code (ISO 639-1) to be used for the metadata field values for the result. This parameter affects the way language-dependent metadata values are returned. For example you can specify “fr” to return all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string[][optional] $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request. Virtual fields (Available since: 8.6.1): {50f54d0a-0ebe-46ce-bf3c-dbb744349650} UID of a virtual field that contains the number of records being assigned to a category. {b46eddc9-dc90-4e31-9474-bee1b9a3fd12} UID of a virtual field that contains the number of records being assigned to a category including its sub-categories. {e85fd04a-7e4f-4718-9879-92c0f22ba892} UID of a virtual field that contains list of names of fields that the current user is allowed to modify.
	 * @param string[optional] $catalogname The DAM system catalog name for later catalog access e.g. Sample Catalog
	 * @return mixed The result is returned in JSON format and consists of the total number of items returned and an optional list of items with IDs or field values defined in the given view. If no view is specified then the list of items is just an array of item IDs. Since version 4 (CIP 9.0) of the API the value for a field of type "User UID" is returned as a structure containing the user unique ID string as well as a display string. If you want the old behavior of just returning the display string you can specify an older API version using the apiversion named parameter.
	 **/
	public function getfieldvalues_catalog($catalog, $view = null, $id, $table = null, $locale = null, $field = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $view, $id ), array(
			'table' => $table,
			'locale' => $locale,
			'field' => $field,
			'catalogname' => $catalogname
		), true);
	}

	// TODO: Implement the possiblity of specifying a request body.
	/**
	 * Set the metadata fields of catalog items.
	 * The field values are specified using a JSON structure transferred in the request body of a HTTP POST request.
	 * The JSON data always contains the ID of the item to be modified.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_setfieldvalues
	 * @param string $catalog The catalog alias for the catalog for the item to be modified.
	 * @param string[optional] $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters.
	 * @param string[optional] $table The name of a table for the items to be modified. The default is "AssetRecords".
	 * @param string[optional] $locale The two-letter language code (ISO 639-1) to be used for the metadata field values. This parameter affects the way language-dependent metadata values are parsed. For example you can specify “fr” to specify all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string[][optional] $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request. Virtual fields (Available since: 8.6.1): {50f54d0a-0ebe-46ce-bf3c-dbb744349650} UID of a virtual field that contains the number of records being assigned to a category. {b46eddc9-dc90-4e31-9474-bee1b9a3fd12} UID of a virtual field that contains the number of records being assigned to a category including its sub-categories. {e85fd04a-7e4f-4718-9879-92c0f22ba892} UID of a virtual field that contains list of names of fields that the current user is allowed to modify.
	 * @param string[optional] $catalogname The DAM system catalog name for later catalog access e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function setfieldvalues($catalog, $view = null, $table = null, $locale = null, $field = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $view ), array(
			'table' => $table,
			'locale' => $locale,
			'field' => $field,
			'catalogname' => $catalogname
		), true);
	}

	// TODO: Implement the possiblity of specifying a request body.
	/**
	 * Create a new catalog item and optionally set the metadata fields for it.
	 * The field values are specified using a JSON structure transferred in the request body of a HTTP POST request.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_createitem
	 * @param string $catalog The catalog alias for the catalog for the item to be modified.
	 * @param string[optional] $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters.
	 * @param string[optional] $table The name of a table for the items to be modified. The default is "AssetRecords".
	 * @param string[optional] $locale The two-letter language code (ISO 639-1) to be used for the metadata field values. This parameter affects the way language-dependent metadata values are parsed. For example you can specify “fr” to specify all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string[][optional] $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request.
	 * @param string[optional] $catalogname The DAM system catalog name for later catalog access e.g. Sample Catalog
	 * @return mixed The result is returned in JSON format and consists of the ID of the created catalog item.
	 **/
	public function createitem($catalog, $view = null, $table = null, $locale = null, $field = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $view ), array(
			'table' => $table,
			'locale' => $locale,
			'field' => $field,
			'catalogname' => $catalogname
		), true);
	}

	/**
	 * Get the sub-categories for a given parent category. You can either get the direct sub-categories of the parent only or the whole sub-tree.
	 * Three options allow specifying the parent category:
	 * 1. Specify the category by the complete path (use parameter path).
	 * 2. Specify the category by its ID (use parameter categoryid).
	 * 3. If neither the path nor categoryid parameter is specified the operation will return the top-level categories.
	 * The result can be returned as a JSON structure containing metadata field values for the categories or the IDs of the categories can be stored in a named collection of the session.
	 * When storing the result in a named collection the hierarchical structure of a possible sub-tree will be lost and all IDs will be stored in a flat list.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_getcategories
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param string $collection The name of a collection to save the resulting list of IDs. If you leave the value empty then CIP will create a unique collection name for you and will return this name in the result. This can be used for temporary collection to make sure the name is unique in the session.
	 * @param string[optional] $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters. If no view or fields are specified then the resulting list of items is just an array of item IDs.
	 * @param string[optional] $path The complete path of the parent category in the tree starting at the top-level category name. The category names for each level are separated by colon. Use double-colon to escape a colon appearing in a category name.
	 * @param number[optional] $categoryid The ID of the parent category.
	 * @param string[optional] $levels This parameter specifies whether you want the result to contain not just the direct sub-categories of the given parent but the whole sub-tree including all categories down to the given level. Possible values are: "1" (Default) Return the direct sub-categories of the given parent category only. "0" Return the requested category id only. "n" (Where “n” is a positive number) Return all the categories underneath the parent category down to the “n” level. They are returned in “depth-first”. The result nests sub-categories inside their parent category item so that the tree structure can be reconstructed. If you specify a collection to store the result the collection will contain the category IDs as a flat list. "all" Return all the categories underneath the parent category down to the bottom level. They are returned in “depth-first”. The result nests sub-categories inside their parent category item so that the tree structure can be reconstructed. If you specify a collection to store the result the collection will contain the category IDs as a flat list.
	 * @param string[optional] $locale The two-letter language code (ISO 639-1) to be used for the metadata field values for the result. This parameter affects the way language-dependent metadata values are returned. For example you can specify “fr” to return all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result is returned in JSON format and consists of the total number of category items returned and a list of items with the field values defined in the given view. The sub-categories are returned as a list with the name subcategories. Empty sub-category arrays are suppressed in the output. If no view and no collection are specified then the format of the result depends on the value of the parameter levels. If only direct sub-categories are returned then the result is just an array of category IDs. If you wanted to get a whole sub-tree then the result is the same as if you would have specified the field “ID” as the only field of the view. If no view but a collection is specified the result just returns the total count and the name of the collection. The item field values or IDs can then be retrieved using the getfieldvalues operation.
	 */
	public function getcategories($catalog, $collection, $view = null, $path = null, $categoryid = null, $levels = null, $locale = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog ), array(
			'collection' => $collection,
			'view' => $view,
			'path' => $path,
			'categoryid' => $categoryid,
			'levels' => $levels,
			'locale' => $locale,
			'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Create a new category as a sub-category of a given other category.
	 * Three options allow specifying the parent category:
	 * 1. Specify the parent category by the complete path (use parameter path).
	 * 2. Specify the parent category by its ID (use parameter categoryid).
	 * 3. If neither the path nor categoryid parameter is specified the operation will create a top-level category.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_createcategory
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param string $name The name for the newly created category
	 * @param string[optional] $path The complete path of the parent category in the tree starting at the top-level category name. The category names for each level are separated by colon. Use double-colon to escape a colon appearing in a category name.
	 * @param number[optional] $categoryid The ID of the parent category.
	 * @param string[optional] $ifcategoryexists Specify what to do when the newly created category refers to an existing one: "createwithsamename" (Default) Create another category with the requested name. "returnexistingone" Return category ID of the existing one. "error" Do nothing and return an error instead.
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog.
	 * @return mixed The result is returned in JSON format and consists of the ID of the newly created category.
	 */

	public function createcategory($catalog, $name, $path = null, $categoryid = null, $ifcategoryexists = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog ), array(
			'name' => $name,
			'path' => $path,
			'categoryid' => $categoryid,
			'ifcategoryexists' => $ifcategoryexists,
			'catalogname' => $catalogname
		), true);
	}

	/**
	 * Assign categories to given catalog item.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_assigntocategories
	 * @param string $catalog The catalog alias for the catalog for the item to be modified.
	 * @param number $id The ID of the item in the catalog to be assigned to given categories.
	 * @param string[optional] $locale The two-letter language code (ISO 639-1) to be used for the metadata field values. This parameter affects the way language-dependent metadata values are parsed. For example you can specify “fr” to specify all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string[][optional] $path The complete path of the categories in the tree starting at the top-level category name to assign an item to. The category names for each level are separated by colon. Use double-colon to escape a colon appearing in a category name.
	 * @param number[][optional] $categoryid The IDs of the categories to assign an item to.
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function assigntocategories($catalog, $id, $locale = null, $path = null, $categoryid = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
			'locale' => $locale,
			'path' => $path,
			'categoryid' => $categoryid,
			'catalogname' => $catalogname
		), true);
	}

	/**
	 * Detach categories from given catalog item.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_detachfromcategories
	 * @param string $catalog The catalog alias for the catalog for the item to be modified.
	 * @param number $id The ID of the item in the catalog to be detached from given categories.
	 * @param string[optional] $locale The two-letter language code (ISO 639-1) to be used for the metadata field values. This parameter affects the way language-dependent metadata values are parsed. For example you can specify “fr” to specify all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string[][optional] $path The complete path of the categories in the tree starting at the top-level category name to assign an item to. The category names for each level are separated by colon. Use double-colon to escape a colon appearing in a category name.
	 * @param number[][optional] $categoryid The IDs of the categories to assign an item to.
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function detachfromcategories($catalog, $id, $locale = null, $path = null, $categoryid = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
			'locale' => $locale,
			'path' => $path,
			'categoryid' => $categoryid,
			'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Delete a given category and all of its sub-categories.
	 * Two options allow specifying the category to be deleted:
	 * 1. Specify the category by the complete path (use parameterpath).
	 * 2. Specify the category by its ID (use parametercategoryid).
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_deletecategory
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param string[optional] $path The complete path of the category in the tree starting at the top-level category name. The category names for each level are separated by colon. Use double-colon to escape a colon appearing in a category name.
	 * @param number[optional] $categoryid The ID of the category to delete.
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog.
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function deletecategory($catalog, $path = null, $categoryid = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog ), array(
			'path' => $path,
			'categoryid' => $categoryid,
			'catalogname' => $catalogname
		), true);
	}

	/**
	 * Return IDs list of all related assets.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_getrelatedassets
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param number $id The ID of the item in the catalog.
	 * @param string $relation The relation type - Possible values: contains, iscontainedin, references, isreferencedby, isvariantmasterof, isvariantof, isalternatemaster, isalternateof
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result is a list of IDs
	 */
	public function getrelatedassets($catalog, $id, $relation, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id, $relation ), array(
			'catalogname' => $catalogname
		), true);
	}

	/**
	 * Add a new relation to the given other record.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_linkrelatedasset
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param number $id The ID of the item in the catalog.
	 * @param string $relation The relation type - Possible values: contains, iscontainedin, references, isreferencedby, isvariantmasterof, isvariantof, isalternatemaster, isalternateof
	 * @param number $otherId The ID of the other item in the catalog.
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function linkrelatedasset($catalog, $id, $relation, $otherId, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id, $relation, $otherId ), array(
			'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Remove the relation to the given other record.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_unlinkrelatedasset
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param number $id The ID of the item in the catalog.
	 * @param string $relation The relation type - Possible values: contains, iscontainedin, references, isreferencedby, isvariantmasterof, isvariantof, isalternatemaster, isalternateof
	 * @param number $otherId The ID of the other item in the catalog.
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function unlinkrelatedasset($catalog, $id, $relation, $otherId, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id, $relation, $otherId ), array(
			'catalogname' => $catalogname
		), true);
	}

	/**
	 * Retrieve the number of records having a specific date value for each day.
	 * Based on collection.
	 * @param string $collection The name of an existing collection in the current session.
	 * @param number $startdatetime The starting date and time in UTC as the number of milliseconds since January 1, 1970, 00:00:00 UTC.
	 * @param number $numberofdays The number of days starting at startdatetime.
	 * @param string[] $field You must select at least one field that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request.
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The operation returns a list of integer values, one for each day. The integer specifies the number of records have a field value within the given day. The total number of integer values is the same as the "numberofdays" parameter value.
	 */
	public function getfieldstatistics_collection($collection, $startdatetime, $numberofdays, $field, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'collection' => $collection,
			'startdatetime' => $startdatetime,
			'numberofdays' => $numberofdays,
			'field' => $field,
			'catalogname' => $catalogname
		), true);
	}

	/**
	 * Retrieve the number of records having a specific date value for each day.
	 * Based on catalog.
	 * @param string $catalog The catalog alias for the catalog to work with.
	 * @param string $table The name of the table to return field values for. The default is "AssetRecords".
	 * @param number $startdatetime The starting date and time in UTC as the number of milliseconds since January 1, 1970, 00:00:00 UTC.
	 * @param number $numberofdays The number of days starting at startdatetime.
	 * @param string[] $field You must select at least one field that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request.
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The operation returns a list of integer values, one for each day. The integer specifies the number of records have a field value within the given day. The total number of integer values is the same as the "numberofdays" parameter value.
	 */
	public function getfieldstatistics_catalog($catalog, $table, $startdatetime, $numberofdays, $field, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array($catalog), array(
				'table' => $table,
				'startdatetime' => $startdatetime,
				'numberofdays' => $numberofdays,
				'field' => $field,
				'catalogname' => $catalogname
		), true);
	}

	// TODO: Implement the HTTP POST request body.
	/**
	 * Send collection link per e-mail
	 * The ID of the items are specified using a JSON structure transferred in the request body of a HTTP POST request.
	 * Based on collection
	 * @param string $collection The name of an existing collection in the current session.
	 * @param number[optional] $startindex The index (zero-based) to start including the items. Using this parameter you can page through the result list by starting with 0 and then incrementing by a given number. The default is 0 which returns the items starting with the first one.
	 * @param number[optional] $maxreturned The maximum number of items included by this operation. You may use this parameter to limit the size of the resulting collection. When used together with the startindex parameter you can implement paging through the result list. The default is to return all items starting at the one specified by the startindex parameter. Due to changes being encountered in the catalog this operation may return less than the specified number of items if items in the given range have been deleted from the catalog. However, to do proper paging you should start the next getfieldvalues operation at the index you calculate from the given parameters startindex and maxreturned.
	 * 
	 * @param string $linkcollection The name used to store a newly created link collection.
	 * @param string[optional] $embargodate The link embargo date. Date format is YYYYMMDD (ISO 8601) e.g. 20110520 (20 May 2011)
	 * @param string $expirationdate The link expiration date. Date format is YYYYMMDD (ISO 8601) e.g. 20110520 (20 May 2011)
	 * @param string[optional] $linkpassword The collection link password.
	 * @param string $linkbaseurl The base URL of the collection link server.
	 * @param string[][optional] $permit Specifies the link collection permissions. Possible values are: "download" Recipients can download assets with the provided asset actions. See also options named parameter. "preview" Recipients can preview assets. "openinfowindow" Recipients can see info window with the metadata. "print" Recipients can print assets.
	 * @param string[optional] $options The name of an options set defined in the configuration file. The exact options are DAM system dependent.
	 * 
	 * @param string[optional] $mailfrom The sender e-mail address.
	 * @param string[] $mailrecipients The list of e-mail recipients. Required to send an e-mail.
	 * @param string[][optional] $mailcc The list of e-mail Cc (carbon copy) recipients.
	 * @param string[][optional] $mailbcc The list of e-mail Bcc (blind carbon copy) recipients.
	 * @param string[optional] $mailsubject The subject line of the e-mail message.
	 * @param string[optional] $mailbody The body text of the e-mail message.
	 * 
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function sendcollectionlink_collection($collection, $startindex = null, $maxreturned = null, $linkcollection, $embargodate = null, $expirationdate, $linkpassword = null, $linkbaseurl, $permit = null, $options = null, $mailfrom = null, $mailrecipients, $mailcc = null, $mailbcc = null, $mailsubject = null, $mailbody = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'collection' => $collection,
			'startindex' => $startindex,
			'maxreturned' => $maxreturned,
			'linkcollection' => $linkcollection,
			'embargodate' => $embargodate,
			'expirationdate' => $expirationdate,
			'linkpassword' => $linkpassword,
			'linkbaseurl' => $linkbaseurl,
			'permit' => $permit,
			'options' => $options,
			'mailfrom' => $mailfrom,
			'mailrecipients' => $mailrecipients,
			'mailcc' => $mailcc,
			'mailbcc' => $mailbcc,
			'mailsubject' => $mailsubject,
			'mailbody' => $mailbody,
			'catalogname' => $catalogname
		));
	}

	// TODO: Implement the HTTP POST request body.
	/**
	 * Send collection link per e-mail
	 * The ID of the items are specified using a JSON structure transferred in the request body of a HTTP POST request.
	 * Based on catalog
	 * @param string $catalog The catalog alias for the catalog to work with.
	 * @param string[optional] $table The name of the table to return field values for. The default is "AssetRecords".
	 * 
	 * @param string $linkcollection The name used to store a newly created link collection.
	 * @param string[optional] $embargodate The link embargo date. Date format is YYYYMMDD (ISO 8601) e.g. 20110520 (20 May 2011)
	 * @param string $expirationdate The link expiration date. Date format is YYYYMMDD (ISO 8601) e.g. 20110520 (20 May 2011)
	 * @param string[optional] $linkpassword The collection link password.
	 * @param string $linkbaseurl The base URL of the collection link server.
	 * @param string[][optional] $permit Specifies the link collection permissions. Possible values are: "download" Recipients can download assets with the provided asset actions. See also options named parameter. "preview" Recipients can preview assets. "openinfowindow" Recipients can see info window with the metadata. "print" Recipients can print assets.
	 * @param string[optional] $options The name of an options set defined in the configuration file. The exact options are DAM system dependent.
	 * 
	 * @param string[optional] $mailfrom The sender e-mail address.
	 * @param string[] $mailrecipients The list of e-mail recipients. Required to send an e-mail.
	 * @param string[][optional] $mailcc The list of e-mail Cc (carbon copy) recipients.
	 * @param string[][optional] $mailbcc The list of e-mail Bcc (blind carbon copy) recipients.
	 * @param string[optional] $mailsubject The subject line of the e-mail message.
	 * @param string[optional] $mailbody The body text of the e-mail message.
	 * 
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function sendcollectionlink_catalog($catalog, $table = null, $startindex = null, $maxreturned = null, $linkcollection, $embargodate = null, $expirationdate, $linkpassword = null, $linkbaseurl, $permit = null, $options = null, $mailfrom = null, $mailrecipients, $mailcc = null, $mailbcc = null, $mailsubject = null, $mailbody = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog ), array(
			'table' => $table,
			'linkcollection' => $linkcollection,
			'embargodate' => $embargodate,
			'expirationdate' => $expirationdate,
			'linkpassword' => $linkpassword,
			'linkbaseurl' => $linkbaseurl,
			'permit' => $permit,
			'options' => $options,
			'mailfrom' => $mailfrom,
			'mailrecipients' => $mailrecipients,
			'mailcc' => $mailcc,
			'mailbcc' => $mailbcc,
			'mailsubject' => $mailsubject,
			'mailbody' => $mailbody,
			'catalogname' => $catalogname
		));
	}
	
}