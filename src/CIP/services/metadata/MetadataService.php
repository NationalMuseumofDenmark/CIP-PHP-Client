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
class MetadataService extends \CIP\services\BaseService {
	
	/**
	 * Return a list of all catalogs that the given user is able to work with.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_getcatalogs
	 * @param string|null $serveraddress The DAM server IP address for later catalog access. e.g. localhost, 192.168.0.2
	 * @param string|null $user string The user name for login to the server for later catalog access.
	 * @param string|null $password string The password for login to the server. The user’s password to be used for later catalog access
	 * @param integer|null $apiversion Determine which API version should be used for the request processing. It's guarantee backwards compatibility for future releases. An application created to work with a given API version will continue to work with that same API version. If this parameter is not present then the newer API version will be used. 1 = CIP 8.5.2, 2 = CIP 8.6, 3 = CIP 8.6.1, 4 = CIP 9.0
	 * @return mixed The result is the list of catalog names.
	 */
	public function getcatalogs($serveraddress = null, $user = null, $password = null, $apiversion = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'serveraddress' => $serveraddress,
			'user' => $user,
			'password' => $password,
			'apiversion' => $apiversion
		));
	}
	
	/**
	 * Return a list of all table names of a catalog.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_gettables
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param string|null $serveraddress The DAM server IP address for later catalog access. e.g. localhost, 192.168.0.2
	 * @param string|null $user string The user name for login to the server for later catalog access.
	 * @param string|null $password string The password for login to the server. The user’s password to be used for later catalog access
	 * @param string|null $catalogname The DAM system catalog name for later catalog access e.g. Sample Catalog
	 * @param integer|null $apiversion Determine which API version should be used for the request processing. It's guarantee backwards compatibility for future releases. An application created to work with a given API version will continue to work with that same API version. If this parameter is not present then the newer API version will be used. 1 = CIP 8.5.2, 2 = CIP 8.6, 3 = CIP 8.6.1, 4 = CIP 9.0
	 * @return mixed The result is the list of table names.
	 */
	public function gettables($catalog, $serveraddress = null, $user = null, $password = null, $catalogname = null, $apiversion = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog ), array(
			'serveraddress' => $serveraddress,
			'user' => $user,
			'password' => $password,
			'catalogname' => $catalogname,
			'apiversion' => $apiversion
		));
	}

	// TODO: Check that the serialization of the $field is actually working correctly.
	// TODO: Check that the optional $view if left out the URL if not specified.
	/**
	 * Return a description of all the fields of a given table.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_getlayout
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param string|null $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters.
	 * @param string|null $table You may want to specify the table to return the layout for. The default is "AssetRecords".
	 * @param string|null $locale The two-letter language code (ISO 639-1) to be used for the metadata field names and values for the result. This parameter affects the way field names and language-dependent metadata values are returned. For example you can specify “fr” to return all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string[]|null $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request.
	 * @param string|null $serveraddress The DAM server IP address for later catalog access. e.g. localhost, 192.168.0.2
	 * @param string|null $user string The user name for login to the server for later catalog access.
	 * @param string|null $password string The password for login to the server. The user’s password to be used for later catalog access
	 * @param string|null $catalogname The DAM system catalog name for later catalog access e.g. Sample Catalog
	 * @param integer|null $apiversion Determine which API version should be used for the request processing. It's guarantee backwards compatibility for future releases. An application created to work with a given API version will continue to work with that same API version. If this parameter is not present then the newer API version will be used. 1 = CIP 8.5.2, 2 = CIP 8.6, 3 = CIP 8.6.1, 4 = CIP 9.0
	 * @return mixed The result is the list of field definitions of the given table. Each field definition contains the type of the field, the field name in the language of the specified locale and the user editable flag. Fields of type Enum also contain a list of possible values which consist of an ID and a name in the given locale.
	 */
	public function getlayout($catalog, $view = null, $table = null, $locale = null, $field = null, $serveraddress = null, $user = null, $password = null, $catalogname = null, $apiversion = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $view ), array(
			'table' => $table,
			'locale' => $locale,
			'field' => $field,
			'serveraddress' => $serveraddress,
			'user' => $user,
			'password' => $password,
			'catalogname' => $catalogname,
			'apiversion' => $apiversion
		));
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
	 * @param string|null $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters. If no view or fields are specified then the resulting list of items is just an array of item IDs.
	 * @param string|null $quicksearchstring The text to perform a quicksearch with. It can be combined with a normal search query string or named query. The result depends on the DAM system and its configuration.
	 * @param string|null $queryname The name of a query defined in the CIP configuration file. It can be combined with a quicksearchstring or a normal search query string. The defined query is either one that is preset in the DAM system or is configured using a query string with optional placeholders. By convention the placeholders are named using an underscore as a prefix to avoid using a name that is later being defined by CIP. See the configuration file section for details on how to define queries.
	 * @param string|null $querystring The complete query string as expected by the DAM system. It can be combined with a quicksearchstring or a named query. You need to make sure all special characters are correctly escaped if you want to pass this parameter in an HTTP request URL. It is recommended to pass the parameter in the body of the request instead.
	 * @param integer|null $startindex The index (zero-based) to start returning the items. Using this parameter you can page through the result list by starting with 0 and then incrementing by a given number. The default is 0 which returns the items starting with the first one.
	 * @param integer|null $maxreturned The maximum number of items returned by this operation. You may use this parameter to limit the size of the resulting JSON data. When used together with the startindex parameter you can implement paging through the result list. The default is to return all items starting at the one specified by the startindex parameter. Due to changes being encountered in the catalog this operation may return less than the specified number of items if items in the given range have been deleted from the catalog. However, to do proper paging you should start the next getfieldvalues operation at the index you calculate from the given parameters startindex and maxreturned.
	 * @param string|null $locale The two-letter language code (ISO 639-1) to be used for parsing the query string and also for the metadata field values to be returned. This parameter affects the way language-dependent metadata values are returned. For example you can specify “fr” to interpret date values in the query in French format and to return all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the "user.language" Java VM parameter when starting the web application server).
	 * @param string[]|null $sortby A field name or field ID with optional sort direction separated by colon (e.g. ID:descending or ID) to specify the fields to be used for sorting the result. The default is that the result is not sorted by any field. Supported values for sort direction: ascending (Default), descending.
	 * @param string|null $collection The name of a collection to save the resulting list of IDs. If you leave the value empty then CIP will create a unique collection name for you and will return this name in the result. This can be used for a temporary collection to make sure the name is unique in the session. When also using the parameter combine you can combine the existing collection contents with the result of this search operation.
	 * @param string|null $combine This parameter is only used when using a stored collection (see collection parameter above). It specifies how the result of this search operation is combined with the contents of the collection specified. If not specified the collection is created from this search operation’s result. Supported values: "new" Default, do not use the previous contents of the specified collection but store the result of this search operation in the collection. "narrow" Only keep items in the collection that are also in the result of this search operation. "broaden" Add all items of the result of this search operation to the collection (if not contained already).
	 * @param string|null $table You may want to specify the table in which the search should be performed. The default is "AssetRecords".
	 * @param string[]|null $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request. Virtual fields (Available since: 8.6.1): {50f54d0a-0ebe-46ce-bf3c-dbb744349650} UID of a virtual field that contains the number of records being assigned to a category. {b46eddc9-dc90-4e31-9474-bee1b9a3fd12} UID of a virtual field that contains the number of records being assigned to a category including its sub-categories. {e85fd04a-7e4f-4718-9879-92c0f22ba892} UID of a virtual field that contains list of names of fields that the current user is allowed to modify.
	 * @param string|null $serveraddress The DAM server IP address for later catalog access. e.g. localhost, 192.168.0.2
	 * @param string|null $user string The user name for login to the server for later catalog access.
	 * @param string|null $password string The password for login to the server. The user’s password to be used for later catalog access
	 * @param string|null $catalogname The DAM system catalog name for later catalog access e.g. Sample Catalog
	 * @param integer|null $apiversion Determine which API version should be used for the request processing. It's guarantee backwards compatibility for future releases. An application created to work with a given API version will continue to work with that same API version. If this parameter is not present then the newer API version will be used. 1 = CIP 8.5.2, 2 = CIP 8.6, 3 = CIP 8.6.1, 4 = CIP 9.0
	 * @return mixed The result is returned in JSON format and consists of the total number of items returned and a list of items with the field values defined in the given view. Since version 4 (CIP 9.0) of the API the value for a field of type "User UID" is returned as a structure containing the user unique ID string as well as a display string. If you want the old behavior of just returning the display string you can specify an older API version using the apiversion named parameter. If no view and no collection are specified then the list of items is just an array of item IDs. If no view but a collection is specified the result just returns the total count and the name of the collection. The item field values or IDs can then be retrieved using the getfieldvalues operation.
	 */
	public function search($catalog, $view = null, $quicksearchstring = null, $queryname = null, $querystring = null, $startindex = null, $maxreturned = null, $locale = null, $sortby = null, $collection = null, $combine = null, $table = null, $field = null, $serveraddress = null, $user = null, $password = null, $catalogname = null, $apiversion = null) {
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
			'serveraddress' => $serveraddress,
			'user' => $user,
			'password' => $password,
			'catalogname' => $catalogname,
			'apiversion' => $apiversion
		));
	}

	/**
	 * Set a query string or named query as a user live filter at the CIP session (see session handling above).
	 * This query is always used when searching inside a catalog to limit the result to items also matching this query.
	 * This way clients can be limited to see only a subset of assets.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_setfilterquery
	 * @param string|null $table You may want to specify the table to which the filter should be assigned. The default is "AssetRecords".
	 * @param string|null $queryname The name of a query defined in the CIP configuration file. The defined query is either one that is preset in the DAM system or is configured using a query string with placeholders. See the configuration file section for details on how to define queries.
	 * @param string|null $querystring The complete query string as expected by the DAM system. You need to make sure all special characters are correctly escaped if you want to pass this parameter in an HTTP request URL. It is recommended to pass the parameter in the body of the request instead.
	 * @param integer|null $apiversion Determine which API version should be used for the request processing. It's guarantee backwards compatibility for future releases. An application created to work with a given API version will continue to work with that same API version. If this parameter is not present then the newer API version will be used. 1 = CIP 8.5.2, 2 = CIP 8.6, 3 = CIP 8.6.1, 4 = CIP 9.0
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function setfilterquery($table = null, $queryname = null, $querystring = null, $apiversion = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'table' => $table,
			'queryname' => $queryname,
			'querystring' => $querystring,
			'apiversion' => $apiversion
		));
	}
	
	/**
	 * Clear stored user live filter from the CIP session. 
	 * You can use this operation to remove user live filter from the CIP session.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_clearfilterquery
	 * @param string|null $table The name of table for which the stored filter should be removed. The default is "AssetRecords".
	 * @param integer|null $apiversion Determine which API version should be used for the request processing. It's guarantee backwards compatibility for future releases. An application created to work with a given API version will continue to work with that same API version. If this parameter is not present then the newer API version will be used. 1 = CIP 8.5.2, 2 = CIP 8.6, 3 = CIP 8.6.1, 4 = CIP 9.0
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function clearfilterquery($table = null, $apiversion = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'table' => $table,
			'apiversion' => $apiversion
		));
	}

	/**
	 * Retrieve the metadata fields or IDs of items in a stored collection. You can specify an offset to start at and a maximum number of items returned.
	 * You specify the items either by setting the named parameter collection or by specifying the catalog and item ID using path parameters 1 and 2 respectively.
	 * This is the collection-based version of the service call.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_getfieldvalues
	 * @param string|null $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters. If no view or fields are specified then the resulting list of items is just an array of item IDs.
	 * @param string $collection The name of an existing collection in the current session.
	 * @param integer|null $startindex The index (zero-based) to start returning the items. Using this parameter you can page through the result list by starting with 0 and then incrementing by a given number. The default is 0 which returns the items starting with the first one.
	 * @param integer|null $maxreturned The maximum number of items returned by this operation. You may use this parameter to limit the size of the resulting JSON data. When used together with the startindex parameter you can implement paging through the result list. The default is to return all items starting at the one specified by the startindex parameter. Due to changes being encountered in the catalog this operation may return less than the specified number of items if items in the given range have been deleted from the catalog. However, to do proper paging you should start the next getfieldvalues operation at the index you calculate from the given parameters startindex and maxreturned.
	 * @param string|null $locale The two-letter language code (ISO 639-1) to be used for the metadata field values for the result. This parameter affects the way language-dependent metadata values are returned. For example you can specify “fr” to return all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string[]|null $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request. Virtual fields (Available since: 8.6.1): {50f54d0a-0ebe-46ce-bf3c-dbb744349650} UID of a virtual field that contains the number of records being assigned to a category. {b46eddc9-dc90-4e31-9474-bee1b9a3fd12} UID of a virtual field that contains the number of records being assigned to a category including its sub-categories. {e85fd04a-7e4f-4718-9879-92c0f22ba892} UID of a virtual field that contains list of names of fields that the current user is allowed to modify.
	 * @param string|null $serveraddress The DAM server IP address for later catalog access. e.g. localhost, 192.168.0.2
	 * @param string|null $user string The user name for login to the server for later catalog access.
	 * @param string|null $password string The password for login to the server. The user’s password to be used for later catalog access
	 * @param string|null $catalogname The DAM system catalog name for later catalog access e.g. Sample Catalog
	 * @param integer|null $apiversion Determine which API version should be used for the request processing. It's guarantee backwards compatibility for future releases. An application created to work with a given API version will continue to work with that same API version. If this parameter is not present then the newer API version will be used. 1 = CIP 8.5.2, 2 = CIP 8.6, 3 = CIP 8.6.1, 4 = CIP 9.0
	 * @return mixed The result is returned in JSON format and consists of the total number of items returned and an optional list of items with IDs or field values defined in the given view. If no view is specified then the list of items is just an array of item IDs. Since version 4 (CIP 9.0) of the API the value for a field of type "User UID" is returned as a structure containing the user unique ID string as well as a display string. If you want the old behavior of just returning the display string you can specify an older API version using the apiversion named parameter.
	 **/
	public function getfieldvalues_collection($view = null, $collection, $startindex = null, $maxreturned = null, $locale = null, $field = null, $serveraddress = null, $user = null, $password = null, $catalogname = null, $apiversion = null) {
		return $this->_client->call(self::getServiceName(), 'getfieldvalues', array( $view ), array(
			'collection' => $collection,
			'startindex' => $startindex,
			'maxreturned' => $maxreturned,
			'locale' => $locale,
			'field' => $field,
			'serveraddress' => $serveraddress,
			'user' => $user,
			'password' => $password,
			'catalogname' => $catalogname,
			'apiversion' => $apiversion
		));
	}

	/**
	 * Retrieve the metadata fields or IDs of items in a stored collection. You can specify an offset to start at and a maximum number of items returned.
	 * You specify the items either by setting the named parameter collection or by specifying the catalog and item ID using path parameters 1 and 2 respectively.
	 * This is the catalog-based version of the service call.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_getfieldvalues
	 * @param string $catalog The catalog alias for the catalog to work with.
	 * @param string|null $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters. If no view or fields are specified then the resulting list of items is just an array of item IDs.
	 * @param number $id The ID of the item in the catalog to return field values for.
	 * @param string|null $table The name of the table to return field values for. The default is "AssetRecords".
	 * @param string|null $locale The two-letter language code (ISO 639-1) to be used for the metadata field values for the result. This parameter affects the way language-dependent metadata values are returned. For example you can specify “fr” to return all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string[]|null $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request. Virtual fields (Available since: 8.6.1): {50f54d0a-0ebe-46ce-bf3c-dbb744349650} UID of a virtual field that contains the number of records being assigned to a category. {b46eddc9-dc90-4e31-9474-bee1b9a3fd12} UID of a virtual field that contains the number of records being assigned to a category including its sub-categories. {e85fd04a-7e4f-4718-9879-92c0f22ba892} UID of a virtual field that contains list of names of fields that the current user is allowed to modify.
	 * @param string|null $serveraddress The DAM server IP address for later catalog access. e.g. localhost, 192.168.0.2
	 * @param string|null $user string The user name for login to the server for later catalog access.
	 * @param string|null $password string The password for login to the server. The user’s password to be used for later catalog access
	 * @param string|null $catalogname The DAM system catalog name for later catalog access e.g. Sample Catalog
	 * @param integer|null $apiversion Determine which API version should be used for the request processing. It's guarantee backwards compatibility for future releases. An application created to work with a given API version will continue to work with that same API version. If this parameter is not present then the newer API version will be used. 1 = CIP 8.5.2, 2 = CIP 8.6, 3 = CIP 8.6.1, 4 = CIP 9.0
	 * @return mixed The result is returned in JSON format and consists of the total number of items returned and an optional list of items with IDs or field values defined in the given view. If no view is specified then the list of items is just an array of item IDs. Since version 4 (CIP 9.0) of the API the value for a field of type "User UID" is returned as a structure containing the user unique ID string as well as a display string. If you want the old behavior of just returning the display string you can specify an older API version using the apiversion named parameter.
	 **/
	public function getfieldvalues_catalog($catalog, $view = null, $id, $table = null, $locale = null, $field = null, $serveraddress = null, $user = null, $password = null, $catalogname = null, $apiversion = null) {
		return $this->_client->call(self::getServiceName(), 'getfieldvalues', array( $catalog, $view, $id ), array(
			'table' => $table,
			'locale' => $locale,
			'field' => $field,
			'serveraddress' => $serveraddress,
			'user' => $user,
			'password' => $password,
			'catalogname' => $catalogname,
			'apiversion' => $apiversion
		));
	}

	// TODO: Implement the possiblity of specifying a request body.
	/**
	 * Set the metadata fields of catalog items.
	 * The field values are specified using a JSON structure transferred in the request body of a HTTP POST request.
	 * The JSON data always contains the ID of the item to be modified.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata_setfieldvalues
	 * @param string $catalog The catalog alias for the catalog for the item to be modified.
	 * @param string|null $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters.
	 * @param string|null $table The name of a table for the items to be modified. The default is "AssetRecords".
	 * @param string|null $locale The two-letter language code (ISO 639-1) to be used for the metadata field values. This parameter affects the way language-dependent metadata values are parsed. For example you can specify “fr” to specify all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string[]|null $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request. Virtual fields (Available since: 8.6.1): {50f54d0a-0ebe-46ce-bf3c-dbb744349650} UID of a virtual field that contains the number of records being assigned to a category. {b46eddc9-dc90-4e31-9474-bee1b9a3fd12} UID of a virtual field that contains the number of records being assigned to a category including its sub-categories. {e85fd04a-7e4f-4718-9879-92c0f22ba892} UID of a virtual field that contains list of names of fields that the current user is allowed to modify.
	 * @param string|null $serveraddress The DAM server IP address for later catalog access. e.g. localhost, 192.168.0.2
	 * @param string|null $user string The user name for login to the server for later catalog access.
	 * @param string|null $password string The password for login to the server. The user’s password to be used for later catalog access
	 * @param string|null $catalogname The DAM system catalog name for later catalog access e.g. Sample Catalog
	 * @param integer|null $apiversion Determine which API version should be used for the request processing. It's guarantee backwards compatibility for future releases. An application created to work with a given API version will continue to work with that same API version. If this parameter is not present then the newer API version will be used. 1 = CIP 8.5.2, 2 = CIP 8.6, 3 = CIP 8.6.1, 4 = CIP 9.0
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function setfieldvalues($catalog, $view = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $view ), array(
			'table' => $table,
			'locale' => $locale,
			'field' => $field,
			'serveraddress' => $serveraddress,
			'user' => $user,
			'password' => $password,
			'catalogname' => $catalogname,
			'apiversion' => $apiversion
		));
	}

	public function createitem() {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array());
	}

	public function getcategories() {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array());
	}

	public function createcategory() {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array());
	}

	public function assigntocategories() {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array());
	}

	public function detachfromcategories() {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array());
	}

	public function deletecategory() {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array());
	}

	public function getrelatedassets() {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array());
	}

	public function linkrelatedasset() {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array());
	}

	public function unlinkrelatedasset() {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array());
	}

	public function getfieldstatistics() {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array());
	}

	public function sendcollectionlink() {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array());
	}
	
}