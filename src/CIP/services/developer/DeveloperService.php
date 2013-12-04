<?php
/**
 * The developer service offers operations that can be used by developers writing client-side code.
 * @see http://crc.canto.com/CIP/doc/CIP.html#developer
 */
namespace CIP\services\developer;
class DeveloperService extends \CIP\services\BaseService {
	
	/**
	 * Generate C# or Java wrapper class source code for the specified catalog and optionally a view.
	 * The class is always derived from the Canto.Cip.Lib.DamItem class and is named according to the view it was generated for. The package name is always Canto.Cip.Client.Views.
	 * C# programmers can use the class by initializing the CIPManager instance with the package name where these generated view wrapper classes are defined for:
	 *   cipManager = new CIPManager(BaseUrl, "Canto.Cip.Client.Views");
	 * Then CIPManager functions like GetFieldValues() will return an instance of this wrapper class instead of the generic DamItem class. Assume you have generated the code for a wrapper class for the configured view named "fields" (see example below). Then you can use the class like this:
	 *   FieldsView item = cipManager.GetFieldValues (session, Catalog, "fields", id) as FieldsView;
	 *   Console.WriteLine("Name: \"{1}\", Date: {2}, Status: {3}",
	 *                      item.Name, item.RecordModificationDate, item.Status);
	 * The variable and function names generated are stripped to meet the requirements of C# naming so every special character that occurs in field names or values is replaced.
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param string[optional] $view The name of a view definition from the configuration file which defines a list of fields to use. See the configuration section on details on how to define views. The field list can be extended with additional fields specified in named request parameters.
	 * @param string[optional] $language The target programming language for the generated source code. Possible values are: "csharp" (Default) Generate .net wrapper class source code. "java" Generate java wrapper class source code.
	 * @param string[optional] $table You may want to specify the table to generate C# wrapper class for. The default is "AssetRecords".
	 * @param string[optional] $locale The two-letter language code (ISO 639-1) to be used for the metadata field names and values for the result. This parameter affects the way field names and language-dependent metadata values are returned. For example you can specify “fr” to return all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string[][optional] $field You can select the fields that should be returned by specifying one or more of these named parameters. The value for this parameter has the same format as the field specification in the view configuration. For the Cumulus DAM system it is sometimes preferable to specify the field using the field UID and optionally the field name and an alias name. When also specifying a configured view as a path parameter you can extend the view fields with the ones specified in the request. By configuring an empty view you can let CIP return only the fields that are specified in the request.
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog.
	 * @return mixed The result is the generated C# or Java wrapper source.
	 */
	public function describe($catalog, $view = null, $language = null, $table = null, $locale = null, $field = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $view ), array(
			'language' => $language,
			'table' => $table,
			'locale' => $locale,
			'field' => $field,
			'catalogname' => $catalogname
		), true);
	}
	
}