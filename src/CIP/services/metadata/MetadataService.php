<?php
/**
 * This class extends the base Metadata Service and adds auxillary helping functions.
 */
namespace CIP\services\metadata;
class MetadataService extends \CIP\services\metadata\BaseMetadataService {
	
	protected $_layout_cache = array();

	/**
	 * A helper implementation of the search, translating the field ID's into usable fieldnames.
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
	public function search_with_layout($catalog, $view = null, $quicksearchstring = null, $queryname = null, $querystring = null, $startindex = null, $maxreturned = null, $locale = null, $sortby = null, $collection = null, $combine = null, $table = null, $field = null, $catalogname = null) {
		$response = parent::search($catalog, $view, $quicksearchstring, $queryname, $querystring, $startindex, $maxreturned, $locale, $sortby, $collection, $combine, $table, $field, $catalogname);
		// We want to use a cached response if possible.
		$this->_client->cacheNextResponse();
		// Request the layout but throw away the result.
		$this->getlayout($catalog, $view, $table, $locale, $field, $catalogname);
		$this->_client->cacheNextResponse();
		$this->getlayout($catalog, $view, $table, $locale, $field, $catalogname);
		// Apply the layout using the layout manager.
		$this->apply_layout($response);
		// Return the response.
		return $response;
	}

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
		$response = parent::getlayout($catalog, $view, $table, $locale, $field, $catalogname);
		foreach($response['fields'] as $field) {
			$key = $field['key'];
			$display_name = $field['name'];
			$this->getLayoutMapper()->updateField($key, $display_name);
		}
		return $response;
	}

	/**
	 * Apply the layout to a response from the service.
	 * @param mixed The response to apply layout field translations to.
	 */
	protected function apply_layout(&$response) {
		$mapper = $this->getLayoutMapper();
		// Altering the response.
		if(isset($response['items'])) {
			foreach($response['items'] as &$item) {
				$item_replacement = array();
				foreach($item as $key => $value) {
					if(strlen($key) == 38) {
						// This is probably a UUID.
						$name = $mapper->UUID2Name($key);
						$item_replacement[$name] = $value;
					}
				}
				$item = $item_replacement;
			}
		}
		
		return $response;
	}
	
	protected $_layout_mapper_singleton;
	
	public function getLayoutMapper() {
		if($this->_layout_mapper_singleton == null) {
			$this->_layout_mapper_singleton = new LayoutMapper();
		}
		return $this->_layout_mapper_singleton;
	}
	
}