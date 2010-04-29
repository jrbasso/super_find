<?php
/**
 * Team Fixture
 *
 * @link          http://github.com/jrbasso/super_find
 * @package       super_find
 * @subpackage    super_find.tests.fixtures
 * @since         SuperFind v0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Team Fixture
 *
 */
class TeamFixture extends CakeTestFixture {

/**
 * Name of fixture
 *
 * @var string
 * @access public
 */
	var $name = 'Team';

/**
 * Fields
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false)
	);

/**
 * records property
 *
 * @var array
 * @access public
 */
	var $records = array(
		array('id' => 1, 'name' => 'Team 1'),
		array('id' => 2, 'name' => 'Team 2'),
		array('id' => 3, 'name' => 'Team 3'),
		array('id' => 4, 'name' => 'Team 4')
	);
}
?>