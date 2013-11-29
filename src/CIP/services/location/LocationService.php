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
	public function get($location) {
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
	public function createdir($location = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'location' => $location
		), true);
	}
	
	/**
	 * Copy a file or directory.
	 * The user specified in the request needs the server permission $CanCopyFiles to perform this action.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#location_copy
	 * @param string $from The location path of the file or directory to copy. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string.
	 * @param string $to The location path for the destination. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string. If "to" is an existing directory then the "from" file/dir is copied there. If "to" does not exist then the "from" file/directory is copied to the parent of "to" which needs to be an existent dir and the copy gets the name specified in "to". If "to" is an existing file and "from" is a file too the parameter ifexists controls how to handle this case.
	 * @param string|null $ifexists Specify what to do when the parameter to refers to an existing file or directory. Possible values are: "newuniquename" (Default) Generate a unique name for the target and copy the source there. "replace" Replace the existing file or directory with the copied one. "error" Do nothing and return an error instead.
	 * @return mixed The location of the copy.
	 */
	public function copy($from, $to, $ifexists = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'from' => $from,
			'to' => $to,
			'ifexists' => $ifexists
		), true);
	}
	
	/**
	 * Move a file or directory. This works the same as first calling location/copy and then location/delete but is just faster because it does it in one step. 
	 * This can also be used to rename a file or directory.
	 * The user specified in the request needs the server permission $CanMoveFiles to perform this action.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#location_move
	 * @param string $from The location path of the file or directory to copy. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string.
	 * @param string $to The location path for the destination. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string. If "to" is an existing directory then the "from" file/dir is copied there. If "to" does not exist then the "from" file/directory is copied to the parent of "to" which needs to be an existent dir and the copy gets the name specified in "to". If "to" is an existing file and "from" is a file too the parameter ifexists controls how to handle this case.
	 * @param string|null $ifexists Specify what to do when the parameter to refers to an existing file or directory. Possible values are: "newuniquename" (Default) Generate a unique name for the target and move the source there. "replace" Replace the existing file or directory with the moved one. "error" Do nothing and return an error instead.
	 * @return mixed The location of the moved file / directory.
	 */
	public function move($from, $to, $ifexists = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'from' => $from,
			'to' => $to,
			'ifexists' => $ifexists
		), true);
	}
	
	/**
	 * Delete a file or directory
	 * The user specified in the request needs the server permission $CanDeleteFiles to perform this action.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#location_delete
	 * @param string $location The complete location path of the directory or file to delete. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string.
	 * @param string|null $recursive When deleting a directory this parameter controls whether to delete all the contents of the directory also. Possible values are: "false" (Default) Only delete the directory if it is empty. Return an error otherwise. "true" Delete the specified directory and all of its contents.
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function delete($location, $recursive = null) {
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'from' => $from,
			'to' => $to,
			'ifexists' => $ifexists
		), true);
	}
	
}