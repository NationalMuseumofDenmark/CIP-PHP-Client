<?php
/**
 * The Asset service offers operations that work with the original assets: importing, downloading, checking out, checking in, deleting an asset.
 * @see http://crc.canto.com/CIP/doc/CIP.html#asset
 */
namespace CIP\services\asset;
class AssetService extends \CIP\services\BaseService {
	/**
	 * You can import an asset either by referencing an existing asset accessible to the CIP server or by uploading the asset along with the request. Optionally you can also set metadata field values when importing the asset.
	 * There are three ways of specifying the asset to be imported:
	 * 1. Specify an existing asset using a URL (using the location parameter).
	 * 2. Specify an existing asset based on a configured location and a relative path (using the location parameter).
	 * 3. Upload the asset in the HTTP request body. This is done by putting the asset contents into the request body and using the HTTP POST method. If no metadata fields are to be set with this request the body only contains the asset contents and you specify the file name to be used in the location parameter. If you want to be able to set metadata fields along with the importing you need to post the request using the MIME type multipart/form-data.
	 * If you also want to set metadata fields for the asset you specify the field values in JSON format in the request body and use the HTTP POST method. 
	 * If you want to both upload the asset with the request and set field values the request body needs to have a MIME type of multipart/form-data. This is compatible with the way web browsers upload files to a web server. The asset contents are then embedded in the request body as a part named "file". If you also want to set metadata field values you also include a part named "fields" which contains the JSON structure with the field values similar to the way the metadata/setfieldvalues operation accepts them.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#asset_import
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param string|null $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters.
	 * @param string|null $location This is either the asset name if the request body only consists of the asset contants or the location path to an existing asset. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string.
	 * @param string|null $options The name of an options set defined in the configuration file. The exact options are DAM system dependent.
	 * @param string|null $locale The two-letter language code (ISO 639-1) to be used for the metadata field values for the result. This parameter affects the way language-dependent metadata values are returned. For example you can specify “fr” to return all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the "user.language" Java VM parameter when starting the web application server).
	 * @param string[string]|null $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result is returned in JSON format and consists of the ID of the imported asset.
	 */
	public function import($catalog, $view = null, $location = null, $options = null, $locale = null, $field = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $view ), array(
			'location' => $location,
			'options' => $options,
			'locale' => $locale,
			'field' => $field,
			'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * After you have created an item using the metadata/createitem operation you can assign an asset to this item using this asset/update operation. The operation has parameters similar to importing an asset but instead of adding a new asset to the catalog it updates the asset contents of an existing asset.
	 * There are three ways of specifying the source asset:
	 * 1. Specify an existing asset using a URL (using the location parameter).
	 * 2. Specify an existing asset based on a configured location and a relative path (using the location parameter).
	 * 3. Upload the asset with the HTTP request. This is done by putting the asset contents into the request body and using the HTTP POST method. This request the body only contains the asset contents.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#asset_update
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param string|null $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $location The location path for an existing asset. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string.
	 * @param string|null $options The name of an options set defined in the configuration file. The exact options are DAM system dependent.
	 * @param string[string]|null $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function update($catalog, $view = null, $id, $location = null, $options = null, $field = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $view, $id ), array(
			'location' => $location,
			'options' => $options,
			'field' => $field,
			'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * You can download an asset using the "download" operation of the "asset" service. If the asset is available in several versions you can optionally specify the version that you want to download.
	 * Optionally you can convert the asset prior to downloading.
	 * An optional location allows you to store the result in the local file system of the CIP server or on an FTP server instead of downloading the result to the client.
	 * In place of location you can specify e-mail parameters to send the asset as an e-mail attachment.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#asset_download
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param integer|null $version The version of the asset to download. The default is to download the latest version.
	 * @param string|null $options The name of a configured option set that contains the conversion options to be applied before downloading. The configuration contains the parameters for the actual conversion.
	 * @param string|null $location The location path of a folder to store the result. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string.
	 * @param string|null $name The name of the file to store the result as. This parameter is only used when a "location" is specified.
	 * @param string|null $ifexists Specify what to do when the result refers to an existing file or directory in the location: "newuniquename" (Default) Generate a unique name for the result. "replace" Replace the existing file or directory with the result. "error" Do nothing and return an error instead.
	 * @param string|null $mailfrom The sender e-mail address.
	 * @param string[]|null $mailrecipients The list of e-mail recipients. Required to send an e-mail.
	 * @param string[]|null $mailcc The list of e-mail Cc (carbon copy) recipients.
	 * @param string[]|null $mailbcc The list of e-mail Cc (carbon copy) recipients.
	 * @param string|null $mailsubject The subject line of the e-mail message.
	 * @param string|null $mailbody The body text of the e-mail message.
	 * @param string|null $cachecontrol Allow to control client cache policy switch for CIP operation results. This could lead to better performance in case the browser can use cached images. "no-cache" (Default) - Switch off caching of images in the browser. "clientdefault" Allow caching of images. The cache lifetime depend on constraint com.canto.cip.constraint.clientcachemaxage configuration. The default value is 3600 seconds (1 hour). See configuration section for details.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed If you specified a location to store the resulting asset then the response body contains JSON data specifying the exact location. If you specified e-mail parameters to send asset as an e-mail attachment then the response body contains JSON data specifying the name of the sent file. If you did not specify either a location or an e-mail then the response body contains the resulting asset contents.
	 */
	public function download($catalog, $id, $version = null, $options = null, $location = null, $name = null, $ifexists = null, $mailfrom = null, $mailrecipients = null, $mailcc = null, $mailbcc = null, $mailsubject = null, $mailbody = null, $cachecontrol = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
			'version' => $version,
			'options' => $options,
			'location' => $location,
			'name' => $name,
			'ifexists' => $ifexists,
			'mailfrom' => $mailfrom,
			'mailrecipients' => $mailrecipients,
			'mailcc' => $mailcc,
			'mailbcc' => $mailbcc,
			'mailsubject' => $mailsubject,
			'mailbody' => $mailbody,
			'cachecontrol' => $cachecontrol,
			'catalogname' => $catalogname
		), true);
	}

	/**
	 * The checkout operation allows you to lock the asset for further modifications and also to download the latest version of the asset or to store it at a given location. This is typically done to work on a new version of the asset and check this back in later.
	 * An optional location allows you to store the current asset in the local file system of the CIP server or on an FTP server instead of downloading it to the client.
	 * After you have finished modifying the asset you can generate a new version in the catalog by using the asset/checkin operation. To revert to the normal state without checking in a new asset version call the asset/undocheckout operation.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#asset_checkout
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $location The location path of a folder to store the result. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string.
	 * @param string|null $name The name of the file to store the result as. This parameter is only used when a "location" is specified.
	 * @param string|null $ifexists Specify what to do when the result refers to an existing file or directory in the location. "newuniquename" (Default) Generate a unique name for the result. "replace" Replace the existing file or directory with the result. "error" Do nothing and return an error instead.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed If you specified a location to store the resulting asset then the response body contains JSON data specifying the exact location. If you did not specify a location then the response body contains the resulting asset contents.
	 */
	public function checkout($catalog, $id, $location = null, $name = null, $ifexists = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
			'location' => $location,
			'name' => $name,
			'ifexists' => $ifexists,
			'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * After you have checked out an asset using the asset/checkout operation you can check in a new version of the asset using this checkin operation. The operation has parameters similar to importing an asset but instead of adding a new asset to the catalog it adds a new version to an existing asset.
	 * There are three ways of specifying the asset to be checked in:
	 * 1. Specify an existing asset using a URL (using the location parameter).
	 * 2. Specify an existing asset based on a configured location and a relative path (using the location parameter).
	 * 3. Upload the asset with the HTTP request. This is done by putting the asset contents into the request body and using the HTTP POST method. This request the body only contains the asset contents.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#asset_checkin
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $location The location path for an existing asset. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string.
	 * @param string|null $options The name of an options set defined in the configuration file. The exact options are DAM system dependent.
	 * @param string|null $comment The comment for this new version of the asset. This comment is available as version-specific metadata.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function checkin($catalog, $id, $location = null, $options = null, $comment = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
			'location' => $location,
			'options' => $options,
			'comment' => $comment,
			'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * If you have checked out an asset using the asset/checkout operation you can undo this action by calling this operation.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#asset_undocheckout
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function undocheckout($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
			'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * You can returns an asset to the previous version by using rollback operation.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#asset_rollback
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param integer $version The version number of the asset to be restored as newer one. You can retrieve the list of available versions for an asset using the asset/getversions operation.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result is the newly created version number given to the restored (rolled back) asset.
	 */
	public function rollback($catalog, $id, $version, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id, $version ), array(
			'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * You can retrieve the list of available versions for a DAM item.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#asset_getversions
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result is returned in JSON format and consists of the item id and a list of available versions described by checkin user, checkin comment, checkin date and version number.
	 */
	public function getversions($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
			'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Delete an item and optionally also the asset that is referenced by this item.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#asset_delete
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $withasset This parameter specifies whether you want to delete not just the item but the asset as well. Possible values are: "false" (Default) - Only the item will be deleted. The corresponding asset will be preserved. "true" The item as well as the corresponding asset will be deleted.
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function delete($catalog, $id, $withasset = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
			'withasset' => $withasset,
			'catalogname' => $catalogname
		), true);
	}
	
}