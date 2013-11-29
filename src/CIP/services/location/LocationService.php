<?php
/**
 * The location management services offer operation to manipulate files and folders in the file system of the CIP server or on FTP servers. In order to control who is allowed to perform the operations the DAM user needs to have specific permissions.
 * In a Cumulus DAM system the following list shows the server permissions required to perform location management operations:
 * $CanDeleteFiles - allow to delete a files and directories
 * $CanCopyFiles - allow to copy a files and directories
 * $CanMoveFiles - allow to move/rename a files and directories
 * $CanListFiles - allow to list a files and directories
 * $CanCreateDirectories - allow to create a directories
 * The locations can be specified either by a complete URL starting with "ftp:", "sftp", "ftps", or "file" or they can be starting with the name of a configured location.
 * When specifying a complete URL the corresponding protocol (e.g. "file") needs to be activated in the configuration file.
 * In order to be able to check the permission you need to specify the user, password, and server address in the request or use a session that contains these parameters.
 * @see http://crc.canto.com/CIP/doc/CIP.html#location
 */
namespace CIP\services\location;
class LocationService extends \CIP\services\BaseService {
	
	/**
	 * Lists files and directories found in the requested location. This operation can be used to check whether a given file or directory exists.
	 * - If the location specifies a file the result is the same location again.
	 * - If the location specifies a directory the result contains a list of names for the contained files or directories.
	 * - If the location is not valid (e.g. file/directory does not exist) an error is returned.
	 * The user specified in the request needs the server permission $CanListFiles to perform this action.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#location_list
	 * @param string $location The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @return mixed A list of locations for all files/directories contained.
	 */
	public function get($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'location' => $location
		), true);
	}
	
	/**
	 * Create a new directory specified in the location parameter.
	 * The user specified in the request needs the server permission $CanCreateDirectories to perform this action.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#location_createdir
	 * @param string $location The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @return mixed The newly created location.
	 */
	public function createdir($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'location' => $location
		), true);
	}
	
	// TODO: This might not be completely implemented.
	/**
	 * Copy a file or directory.
	 * The user specified in the request needs the server permission $CanCopyFiles to perform this action.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#location_copy
	 * @param string $from The location path of the file or directory to copy. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string.
	 * 
	 * @return mixed The newly created location.
	 */
	public function copy($catalog, $id, $catalogname = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id ), array(
			'catalogname' => $catalogname
		), true);
	}
}