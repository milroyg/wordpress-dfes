<?php
/**
 * Class: Condition
 *
 * Description: Class abstraite des conditions
 *
 * @since 2.1.7
 */

namespace EACCustomWidgets\Includes\DisplayConditions\Conditions;

/**
 * Abstract Class Condition.
 */
abstract class Condition_Base {

	/**
	 * Get Controls Options.
	 *
	 * @access public
	 * @since 2.1.7
	 *
	 * @return array  controls options
	 */
	public function get_target_control(): array {}

	/**
	 * Le nom de la class namespace inclus
	 *
	 * @access public
	 * @since 2.1.7
	 *
	 * @return string
	 */
	public function get_called_classname(): string {}

	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 2.1.7
	 *
	 * @param array       $settings element settings.
	 * @param string      $operator condition operator.
	 * @param string      $value    condition value.
	 * @param string|bool $tz        time zone.
	 *
	 * @return bool
	 */
	public function check( $settings, $value, $operateur, $tz ): bool {}
}
