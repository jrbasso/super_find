<?php
/**
 * Task Fixture
 *
 * @link          http://github.com/jrbasso/super_find
 * @package       super_find
 * @subpackage    super_find.tests.fixtures
 * @since         SuperFind v0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Task Fixture
 *
 */
class TaskFixture extends CakeTestFixture {

/**
 * Name of fixture
 *
 * @var string
 * @access public
 */
	var $name = 'Task';

/**
 * Fields
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false),
		'user_id' => array('type' => 'integer'),
		'category_id' => array('type' => 'integer')
	);

/**
 * records property
 *
 * @var array
 * @access public
 */
	var $records = array(
		array('id' => 1, 'name' => 'Task 1', 'user_id' => 1, 'category_id' => 1),
		array('id' => 2, 'name' => 'Task 2', 'user_id' => 2, 'category_id' => 1),
		array('id' => 3, 'name' => 'Task 3', 'user_id' => 2, 'category_id' => 1),
		array('id' => 4, 'name' => 'Task 4', 'user_id' => 3, 'category_id' => 2)
	);
}
?>