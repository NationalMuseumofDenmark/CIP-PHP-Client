<?php
namespace CIP\filters;
interface IResponseFilter {
	/**
	 * Apply the filter to the a specific field's value.
	 * @param string $service The name of the service called.
	 * @param string $operation The name of the operation called on the service.
	 * @param string $key The key in the reponse.
	 * @param mixed $value The value in the response.
	 */
	public function apply( $service, $operation, &$response );
}