<?php
/**
 * This class helps mapping UUID field ID's to human understandable names and vice versa.
 */
namespace CIP\services\metadata;
class LayoutMapper {

	/**
	 * Mapping from a fields simplified name to the field's GUID.
	 * @var \string[string]
	 */
	protected $_name2uuid = array();
	
	/**
	 * Mapping from a fields UUID name to its display name.
	 * @var \string[string]
	 */
	protected $_uuid2displayname = array();
	
	/**
	 * The one way translation of a display name to the name of the 
	 * @param string $display_name The display name of the field.
	 * @return string The name of the field.
	 */
	public function DisplayName2Name($display_name) {
		$name = strtolower($display_name);
		// Turn spaces and dashes into underscores.
		$name = preg_replace("/ /", "_", $name);
		$name = preg_replace("/-/", "_", $name);
		// Turn any subsequent unserscores into as single.
		$name = preg_replace("/_+/", "_", $name);
		// For the titles in from the nordics.
		$name = preg_replace("/[æÆ]/u", "ae", $name);
		$name = preg_replace("/[øØ]/u", "oe", $name);
		$name = preg_replace("/[åÅ]/u", "aa", $name);
		// Remove anything which is not alhanummeric.
		$name = preg_replace("/[^a-z0-9_]+/", "", $name);
		return $name;
	}
	
	/**
	 * Translates a fields name into to its UUID.
	 * @param string $name The name of the field.
	 * @throws \RuntimeException If the simplified name couldn't be translated to a display name.
	 * @return \string[string] The UUID of the field.
	 */
	public function Name2UUID($name) {
		if(array_key_exists($name, $this->_name2uuid)) {
			return $this->_name2uuid[$name];
		} else {
			throw new \RuntimeException("Couldn't translate display name '$name' into UUID. Have you fetched a layout where it's present?");
		}
	}
	
	/**
	 * Translates a fields UUID to its display name.
	 * @param unknown $uuid The UUID of a field.
	 * @throws \RuntimeException If the UUID couldn't be translated to a display name.
	 * @return string[string] The display name of the field.
	 */
	public function UUID2DisplayName($uuid) {
		if(array_key_exists($uuid, $this->_uuid2displayname)) {
			return $this->_uuid2displayname[$uuid];
		} else {
			throw new \RuntimeException("Couldn't translate UUID '$uuid' into a display name. Have you fetched a layout where it's present?");
		}
	}
	
	/**
	 * Translates a fields name to its display name.
	 * @param string $name The name of the field.
	 * @return string[string] The display name of the field.
	 */
	public function Name2DisplayName($name) {
		$uuid = $this->Name2UUID($name);
		return $this->UUID2DisplayName($uuid);
	}
	
	/**
	 * Translates a fields UUID to its simplified name.
	 * @param string $uuid The UUID of a field.
	 * @return string The simplified name of the field.
	 */
	public function UUID2Name($uuid) {
		$display_name = $this->UUID2DisplayName($uuid);
		return $this->DisplayName2Name($display_name);
	}
	
	/**
	 * Translates a fields display name into its UUID.
	 * @param string $display_name The fields display name.
	 * @return string[string] The UUID of the field.
	 */
	public function DisplayName2UUID($display_name) {
		$name = $this->DisplayName2Name($display_name);
		return $this->Name2UUID($name);
	}
	
	public function updateField($uuid, $display_name) {
		$this->_uuid2displayname[$uuid] = $display_name;
		$name = $this->DisplayName2Name($display_name);
		$this->_name2uuid[$name] = $uuid;
	}
}