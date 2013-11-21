<?php
/**
 * @see http://crc.canto.com/CIP/doc/CIP.html#preview
 */
namespace CIP\services\preview;
class PreviewService extends \CIP\services\BaseService {

	/**
	 * Return a pixel preview for an asset.
	 * An optional location allows you to store the result in the local file system of the CIP server or on an FTP server instead of downloading the result to the client.
	 * Several options allow specifying the parameters for generating the preview image. The options are applied in the following order:
	 * 1. Cropping (use optional parameters left, top, width, height).
	 * 2. Scale down the image (use optional parameter maxsize or size).
	 * 3. Rotate the image in 90 degree steps (use optional the parameter rotate).
	 * 4. Output file format (use optional parameter format and quality).
	 * To improve the performance of delivering preview images the CIP server caches the generated preview images.
	 * The cache location is configured using the predefined name com.canto.cip.location.previewcache. If the image of the original asset cannot be delivered (e.g. the asset is not accessible) then this operation returns the thumbnail image instead. If even then thumbnail cannot be determined you can specify whether it should return a fallback image or an error instead.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#preview_image
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $previewname The name of a preview configured in the CIP server. By using named previews you can keep the actual preview parameters separate from the CIP client code. Every parameter that you specify explicitly in the URL replaces a corresponding value from the configured preview.
	 * @param string[string]|null $cropping An associative array with the following optional elements: [left] integer (optional) Specify the number of pixels to crop off the left side in original image space. The default is 0. [top] integer (optional) Specify the number of pixels to crop off the top side in original image space. The default is 0. [width] integer (optional) Specify the width of the cropping area in original image space. The default is the width of the original image. [height] integer (optional) Specify the height of the cropping area in original image space. The default is the height of the original image.
	 * @param string[string]|null $scaling An associative array with the following optional elements: [maxsize] integer (optional)	 Scale the image down so that the longest side fits into the given number of pixels preserving the aspect ratio of the image. [size] integer (optional) Scale down the image so that the shortest side fits into the square whose size is given in pixels. This option centers the image inside the square and crops away parts of the longer dimension. The resulting image is always square.
	 * @param integer|null $rotate Rotate the image clockwise by the given degrees. Possible values are 0, 90, 180, and 270. Default is 0.
	 * @param string|null $format The output file format to be used. Possible values are "jpeg" and "png" with the default being "jpeg"
	 * @param string|null $quality When using a file format that supports a lossy compression method (e.g. "jpeg") you can specify the quality level that you want to be preserved. The value ranges from "1" (least quality, smallest resulting data size) to "10" (best quality, largest resulting data size).
	 * @param string[string]|null $location An associative array with the following optional elements: [location] The location path of a folder to store the result. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. When using a complete URL the protocol (e.g. "file") needs to be activated in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string. [name] The name of the file to store the result as. This parameter is only used when a "location" is specified. [ifexists] Specify what to do when the result refers to an existing file or directory in the location: "newuniquename" (Default) Generate a unique name for the result. "replace" Replace the existing file or directory with the result. "error" Do nothing and return an error instead.
	 * @param string|null $usecache Specifies how to use the preview cache. Possible values are: "true" (Default) - Return a cached preview if it exists and matches the asset modification date. Otherwise return a newly generated preview and store it in the cache. "only" Only generate the preview to store it in cache. No preview is returned to the caller. "false" Do not use the cache at all, return a newly generated preview.
	 * @param string|null $cachecontrol Allow to control client cache policy switch for CIP operation results. This could lead to better performance in case the browser can use cached images. "no-cache" (Default) - Switch off caching of images in the browser. "clientdefault" Allow caching of images. The cache lifetime depend on constraint com.canto.cip.constraint.clientcachemaxage configuration. The default value is 3600 seconds (1 hour). See configuration section for details.
	 * @param string|null $fallbackimageonerror Specifies whether the fallback image should be returned instead of the error message. Possible values are: "true" Return a fallback image instead of error message. The error is logged in the server log only. "false" (Default) - Do not use the fallback image, return the error message if any problem occurs.
	 * @param integer|null $version The number of the asset version to generate a preview for. A value of 0 represents the latest version. You can retrieve the list of available versions for an asset using the asset/getversions operation. Default is 0 (latest version).
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed Unless you specify "usecache=only" the response body contains: If you specified a location to store the resulting asset then the response body contains JSON data specifying the exact location. If you did not specify a location then the response body contains the image data you requested.
	 */
	public function image($catalog, $id, $previewname = null, $cropping = null, $scaling = null, $rotate = null, $format = null, $quality = null, $location = null, $usecache = null, $cachecontrol = null, $fallbackimageonerror = null, $version = null, $catalogname = null) {
		// $cropping
		$left = isset($cropping['left']) ? $cropping['left'] : null;
		$top = isset($cropping['top']) ? $cropping['top'] : null;
		$width = isset($cropping['width']) ? $cropping['width'] : null;
		$height = isset($cropping['height']) ? $cropping['height'] : null;
		// $scaling
		$maxsize = isset($scaling['maxsize']) ? $scaling['maxsize'] : null;
		$size = isset($scaling['size']) ? $scaling['size'] : null;
		// $location
		$name = isset($location['name']) ? $location['name'] : null;
		$ifexists = isset($location['ifexists']) ? $location['ifexists'] : null;
		$location = isset($location['location']) ? $location['location'] : null;
		
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id, $previewname ), array(
			'left' => $left,
			'top' => $top,
			'width' => $width,
			'height' => $height,
			'maxsize' => $maxsize,
			'size' => $size,
			'rotate' => $rotate,
			'format' => $format,
			'quality' => $quality,
			'location' => $location,
			'name' => $name,
			'ifexists' => $ifexists,
			'usecache' => $usecache,
			'cachecontrol' => $cachecontrol,
			'fallbackimageonerror' => $fallbackimageonerror,
			'version' => $version,
			'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Purge specific cached preview files from the cache. You can purge all previews generated for a specific asset as well as previews generated with a specific parameter set.
	 * This operation uses the same named parameters as the preview/image operation but it uses them only to determine the files in the cache that are to be purged. All optional parameters that are missing are replaced internally with their default values to form a complete parameter set.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#preview_image
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string|null $previewname The name of a preview configured in the CIP server. By using named previews you can keep the actual preview parameters separate from the CIP client code. Every parameter that you specify explicitly in the URL replaces a corresponding value from the configured preview.
	 * @param string[string]|null $cropping An associative array with the following optional elements: [left] integer (optional) Specify the number of pixels to crop off the left side in original image space. The default is 0. [top] integer (optional) Specify the number of pixels to crop off the top side in original image space. The default is 0. [width] integer (optional) Specify the width of the cropping area in original image space. The default is the width of the original image. [height] integer (optional) Specify the height of the cropping area in original image space. The default is the height of the original image.
	 * @param string[string]|null $scaling An associative array with the following optional elements: [maxsize] integer (optional)	 Scale the image down so that the longest side fits into the given number of pixels preserving the aspect ratio of the image. [size] integer (optional) Scale down the image so that the shortest side fits into the square whose size is given in pixels. This option centers the image inside the square and crops away parts of the longer dimension. The resulting image is always square.
	 * @param integer|null $rotate Rotate the image clockwise by the given degrees. Possible values are 0, 90, 180, and 270. Default is 0.
	 * @param string|null $format The output file format to be used. Possible values are "jpeg" and "png" with the default being "jpeg"
	 * @param string|null $quality When using a file format that supports a lossy compression method (e.g. "jpeg") you can specify the quality level that you want to be preserved. The value ranges from "1" (least quality, smallest resulting data size) to "10" (best quality, largest resulting data size).
	 * @param string[string]|null $location An associative array with the following optional elements: [location] The location path of a folder to store the result. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. When using a complete URL the protocol (e.g. "file") needs to be activated in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string. [name] The name of the file to store the result as. This parameter is only used when a "location" is specified. [ifexists] Specify what to do when the result refers to an existing file or directory in the location: "newuniquename" (Default) Generate a unique name for the result. "replace" Replace the existing file or directory with the result. "error" Do nothing and return an error instead.
	 * @param string|null $usecache Specifies how to use the preview cache. Possible values are: "true" (Default) - Return a cached preview if it exists and matches the asset modification date. Otherwise return a newly generated preview and store it in the cache. "only" Only generate the preview to store it in cache. No preview is returned to the caller. "false" Do not use the cache at all, return a newly generated preview.
	 * @param string|null $cachecontrol Allow to control client cache policy switch for CIP operation results. This could lead to better performance in case the browser can use cached images. "no-cache" (Default) - Switch off caching of images in the browser. "clientdefault" Allow caching of images. The cache lifetime depend on constraint com.canto.cip.constraint.clientcachemaxage configuration. The default value is 3600 seconds (1 hour). See configuration section for details.
	 * @param string|null $fallbackimageonerror Specifies whether the fallback image should be returned instead of the error message. Possible values are: "true" Return a fallback image instead of error message. The error is logged in the server log only. "false" (Default) - Do not use the fallback image, return the error message if any problem occurs.
	 * @param integer|null $version The number of the asset version to generate a preview for. A value of 0 represents the latest version. You can retrieve the list of available versions for an asset using the asset/getversions operation. Default is 0 (latest version).
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function purgecache($catalog, $id, $previewname = null, $cropping = null, $scaling = null, $rotate = null, $format = null, $quality = null, $location = null, $usecache = null, $cachecontrol = null, $fallbackimageonerror = null, $version = null, $catalogname = null) {
		// $cropping
		$left = isset($cropping['left']) ? $cropping['left'] : null;
		$top = isset($cropping['top']) ? $cropping['top'] : null;
		$width = isset($cropping['width']) ? $cropping['width'] : null;
		$height = isset($cropping['height']) ? $cropping['height'] : null;
		// $scaling
		$maxsize = isset($scaling['maxsize']) ? $scaling['maxsize'] : null;
		$size = isset($scaling['size']) ? $scaling['size'] : null;
		// $location
		$name = isset($location['name']) ? $location['name'] : null;
		$ifexists = isset($location['ifexists']) ? $location['ifexists'] : null;
		$location = isset($location['location']) ? $location['location'] : null;
		
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id, $previewname ), array(
			'left' => $left,
			'top' => $top,
			'width' => $width,
			'height' => $height,
			'maxsize' => $maxsize,
			'size' => $size,
			'rotate' => $rotate,
			'format' => $format,
			'quality' => $quality,
			'location' => $location,
			'name' => $name,
			'ifexists' => $ifexists,
			'usecache' => $usecache,
			'cachecontrol' => $cachecontrol,
			'fallbackimageonerror' => $fallbackimageonerror,
			'version' => $version,
			'catalogname' => $catalogname
		), true);
	}
	
	/**
	 * Return the thumbnail image of an asset. Typically the thumbnail image is a small representation of the asset stored in the catalog itself so retrieving the thumbnail.
	 * An optional location allows you to store the result in the local file system of the CIP server or on an FTP server instead of downloading the result to the client.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#preview_image
	 * @param string $catalog The name of a catalog alias definition from the configuration file. See the configuration section for details on how to define catalog aliases.
	 * @param integer $id The ID of the asset in the catalog specified by the first path parameter.
	 * @param string[string]|null $scaling An associative array with the following optional elements: [maxsize] integer (optional)	 Scale the image down so that the longest side fits into the given number of pixels preserving the aspect ratio of the image. [size] integer (optional) Scale down the image so that the shortest side fits into the square whose size is given in pixels. This option centers the image inside the square and crops away parts of the longer dimension. The resulting image is always square.
	 * @param integer|null $rotate Rotate the image clockwise by the given degrees. Possible values are 0, 90, 180, and 270. Default is 0.
	 * @param string|null $format The output file format to be used. Possible values are "jpeg" and "png" with the default being "jpeg"
	 * @param string|null $quality When using a file format that supports a lossy compression method (e.g. "jpeg") you can specify the quality level that you want to be preserved. The value ranges from "1" (least quality, smallest resulting data size) to "10" (best quality, largest resulting data size).
	 * @param string[string]|null $location An associative array with the following optional elements: [location] The location path of a folder to store the result. The path is either a complete URL that starts with a URL protocol like “ftp”, “sftp”, “ftps”, “file” or is based on a location defined in the configuration file. When using a complete URL the protocol (e.g. "file") needs to be activated in the configuration file. By using a configured location and a relative path you can hide details such as FTP passwords from the user of the service. When executed the location name is replaced with the configured location string. [name] The name of the file to store the result as. This parameter is only used when a "location" is specified. [ifexists] Specify what to do when the result refers to an existing file or directory in the location: "newuniquename" (Default) Generate a unique name for the result. "replace" Replace the existing file or directory with the result. "error" Do nothing and return an error instead.
	 * @param string|null $cachecontrol Allow to control client cache policy switch for CIP operation results. This could lead to better performance in case the browser can use cached images. "no-cache" (Default) - Switch off caching of images in the browser. "clientdefault" Allow caching of images. The cache lifetime depend on constraint com.canto.cip.constraint.clientcachemaxage configuration. The default value is 3600 seconds (1 hour). See configuration section for details.
	 * @param string|null $fallbackimageonerror Specifies whether the fallback image should be returned instead of the error message. Possible values are: "true" Return a fallback image instead of error message. The error is logged in the server log only. "false" (Default) - Do not use the fallback image, return the error message if any problem occurs.
	 * @param integer|null $version The number of the asset version to generate a thumbnail for. A value of 0 represents the latest version. You can retrieve the list of available versions for an asset using the asset/getversions operation. Default is 0 (latest version).
	 * @param string|null $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @return mixed The result does not have any contents. Returns true on success.
	 */
	public function thumbnail($catalog, $id, $scaling = null, $rotate = null, $format = null, $quality = null, $location = null, $cachecontrol = null, $fallbackimageonerror = null, $version = null, $catalogname = null) {
		// $scaling
		$maxsize = isset($scaling['maxsize']) ? $scaling['maxsize'] : null;
		$size = isset($scaling['size']) ? $scaling['size'] : null;
		// $location
		$name = isset($location['name']) ? $location['name'] : null;
		$ifexists = isset($location['ifexists']) ? $location['ifexists'] : null;
		$location = isset($location['location']) ? $location['location'] : null;
		
		return $this->_client->call(self::getServiceName(), __FUNCTION__, array( $catalog, $id, $previewname ), array(
			'maxsize' => $maxsize,
			'size' => $size,
			'rotate' => $rotate,
			'format' => $format,
			'quality' => $quality,
			'location' => $location,
			'name' => $name,
			'ifexists' => $ifexists,
			'usecache' => $usecache,
			'cachecontrol' => $cachecontrol,
			'fallbackimageonerror' => $fallbackimageonerror,
			'version' => $version,
			'catalogname' => $catalogname
		), true);
	}
}