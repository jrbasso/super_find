<?php
/**
 * Record Fixture
 *
 * @link          http://github.com/jrbasso/super_find
 * @package       super_find
 * @subpackage    super_find.tests.fixtures
 * @since         SuperFind v0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Record Fixture
 *
 */
class RecordFixture extends CakeTestFixture {

/**
 * Name of fixture
 *
 * @var string
 * @access public
 */
	var $name = 'Record';

/**
 * Fields
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false),
		'task_id' => array('type' => 'integer')
	);

/**
 * records property
 *
 * @var array
 * @access public
 */
	var $records = array(
		array('id' => 1, 'title' => 'Record 1', 'task_id' => 1),
		array('id' => 2, 'title' => 'Record 2', 'task_id' => 2),
		array('id' => 3, 'title' => 'Record 3', 'task_id' => 2),
		array('id' => 4, 'title' => 'Record 4', 'task_id' => 3)
	);
}
?>