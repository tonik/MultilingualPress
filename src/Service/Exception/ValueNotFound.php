<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\MultilingualPress\Service\Exception;

/**
 * Exception to be thrown when a value or factory callback could not be found in the container.
 *
 * @package Inpsyde\MultilingualPress\Service\Exception
 * @since   3.0.0
 */
class ValueNotFound extends InvalidValueReadAccess {

	/**
	 * Returns a new exception object.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name   The name of the value or factory callback.
	 * @param string $action Optional. Action to be performed. Defaults to 'read'.
	 *
	 * @return ValueNotFound Exception object.
	 */
	public static function for_name( string $name, string $action = 'read' ): ValueNotFound {

		return new static( sprintf(
			'Cannot %2$s "%1$s". There is no value or factory callback with this name.',
			$name,
			$action
		) );
	}
}
