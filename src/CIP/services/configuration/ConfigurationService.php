<?php
/**
 * This service offers operations that retrieve information about the CIP server system configuration.
 * @see http://crc.canto.com/CIP/doc/CIP.html#configuration
 */
namespace CIP\services\configuration;
class ConfigurationService extends \CIP\services\BaseService {

	/**
	 * Access client custom configuration stored in the CIP configuration
	 * @see http://crc.canto.com/CIP/doc/CIP.html#configuration_getclientconfiguration
	 * @param string $name The name of the client configuration.
	 * @return mixed The result is the configuration of the client.
	 */
	public function getclientconfiguration($catalog, $view = null, $location = null, $options = null, $locale = null, $field = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'name' => $name,
		));
	}

	/**
	 * Return a view configuration stored in the DAM system
	 * @see http://crc.canto.com/CIP/doc/CIP.html#configuration_getview
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param string $name The name of the view definition on the DAM system.
	 * @param string|null $variant The name the variant of the view definition. The view definition may provide different variants for different usages, e.g. one for a "thumbnail view", another variant for the "details view.". In the Cumulus DAM system the following values are supported: "com.canto.recordviewsets.views.ThumbnailView" (Default) The "Thumbnail View" variant. "com.canto.recordviewsets.views.InfoView" The "Info View" variant. "com.canto.recordviewsets.views.InfoWindow" The "Asset Info Window" variant. "com.canto.recordviewsets.views.ReportView" The "Report View" variant. "com.canto.recordviewsets.views.DetailsView" The "Details View" variant. "com.canto.recordviewsets.views.PaletteMode" The "Palette Mode" variant. "com.canto.recordviewsets.views.PreviewView" The "Preview View" variant. "com.canto.recordviewsets.views.PreviewWindow" The "Preview Window" variant. "com.canto.recordviewsets.views.CalendarPane" The "Calendar Pane" variant.
	 * @param string|null $table You may want to specify the table to return the view definition for. The default is "AssetRecords".
	 * @param string|null $locale The two-letter language code (ISO 639-1) to be used for the metadata field names and values for the result. This parameter affects the way field names and language-dependent metadata values are returned. For example you can specify “fr” to return all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog.
	 * @return mixed The result is the view definition in a format specific for the DAM system. The resulting view only contains fields that the specified catalog contains.
	 */
	public function getview($catalog, $name, $variant = null, $table = null, $locale = null, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $name, $variant ), array(
			'table' => $table,
			'locale' => $locale,
			'catalogname' => $catalogname
		));
	}
	
}