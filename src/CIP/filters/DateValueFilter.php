<?php
namespace CIP\filters;
/**
 * This filter changes all dates in the responses into real Date objects.
 * @author Kræn Hansen (BIT BLUEPRINT) <kh@bitblueprint.com> for the National Museeum of Denmark
 *
 */
class DateValueFilter implements IValueFilter {
	
	const DATE_PATTERN = '|/Date\((\d+)\)/|';
	
	public function apply( $service, $operation, &$key, &$value ) {
		$matches = array();
		if(is_string($value) && preg_match(self::DATE_PATTERN, $value, $matches)) {
			$timestamp = intval($matches[1]);
			$timestamp /= 1000; // As PHP has unix timestamps in seconds, not ms.
			$value = new \DateTime();
			$value->setTimestamp($timestamp);
		}
	}
	
}