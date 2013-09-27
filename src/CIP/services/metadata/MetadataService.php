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
After storing a search result in a collection you can then either retrieve the metadata for items in the collection using a getfieldvalues operation or combine the collection with the result of a subsequent search operation. Each search always returns the total number of items found. The getfieldvalues operation allows you to specify a starting offset in the item IDs and a maximum number of items to return. This way you can implement client-side “paging” through a long list of resulting items.
 * @see http://samlinger.natmus.dk/CIP/doc/CIP.html#metadata
 */
namespace CIP\services\metadata;
class MetadataService extends \CIP\services\BaseService {
	
	/**
	 * Return a list of all catalogs that the given user is able to work with.
	 */
	public function getcatalogs() {
		$this->_client->call(self::getServiceName(), __FUNCTION__, array(), array());
	}
	
	/**
	 * Return a list of all table names of a catalog.
	 */
	public function gettables($catalog, $catalogname) {
		$this->_client->call(self::getServiceName(), __FUNCTION__, array());
	}
	
}