<?php
namespace CIP\filters;
interface IValueFilter {
	/**
	 * Apply the filter to the a specific field's value.
	 * @param unknown $field_id The UUID of the field.
	 * @param unknown $field_value The value of the field.
	 * @return string The value after the filter has been applied.
	 */
	public function apply( $service, $action, $key, $value );
}