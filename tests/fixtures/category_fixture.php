<?php
/**
 * Category Fixture
 *
 * @link          http://github.com/jrbasso/super_find
 * @package       super_find
 * @subpackage    super_find.tests.fixtures
 * @since         SuperFind v0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Category Fixture
 *
 */
class CategoryFixture extends CakeTestFixture {

/**
 * Name of fixture
 *
 * @var string
 * @access public
 */
	var $name = 'Category';

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
		array('id' => 1, 'name' => 'Category 1'),
		array('id' => 2, 'name' => 'Category 2'),
		array('id' => 3, 'name' => 'Category 3'),
		array('id' => 4, 'name' => 'Category 4')
	);
}
?>