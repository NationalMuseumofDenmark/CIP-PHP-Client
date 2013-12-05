<?php
namespace CIP\filters;
/**
 * This filter changes all dates in the responses into real Date objects.
 * @author Kræn Hansen (BIT BLUEPRINT) <kh@bitblueprint.com> for the National Museeum of Denmark
 *
 */
class DateResponseFilter implements IResponseFilter {
	
	const DATE_PATTERN = '|/Date\((\d+)\)/|';
	
	public function apply( $service, $operation, &$response ) {
		$matches = array();
		if(is_array($response)) {
			array_walk_recursive($response, array(&$this, 'applyOnValue'));
		}
	}
	
	protected function applyOnValue(&$value, $key) {
		if(is_string($value) && preg_match(self::DATE_PATTERN, $value, $matches)) {
			$timestamp = intval($matches[1]);
			$timestamp /= 1000; // As PHP has unix timestamps in seconds, not ms.
			$value = new \DateTime();
			$value->setTimestamp($timestamp);
		}
	}
	
}