<?php
spl_autoload_extensions(".php");
spl_autoload_register("CaseSensitiveAutoload");
	
/**
 * An autoload method that is case sensitive unlike the build in autoload method in PHP 5.3.
 * Performance can properly be improved.
 * @param string $className
 * @param string $extensions
 * @return bool
 */
function CaseSensitiveAutoload($className, $extensions = null)
{
	$extensions = explode(",", is_null($extensions) ? spl_autoload_extensions() : $extensions);
	$paths = explode(PATH_SEPARATOR, get_include_path());
	$classPath = str_replace("\\", DIRECTORY_SEPARATOR, $className);
	$filePath = null;
	
	foreach($paths as $path) {
		foreach($extensions as $extension) {
			$filePath = realpath($path . DIRECTORY_SEPARATOR . $classPath .$extension);
			if($filePath !== false) {
				include($filePath);
				return true;
			}
		}
	}
	
	return false;
}
?>