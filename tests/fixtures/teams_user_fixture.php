<?php
/**
 * Teams vs Users Fixture
 *
 * @link          http://github.com/jrbasso/super_find
 * @package       super_find
 * @subpackage    super_find.tests.fixtures
 * @since         SuperFind v0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Teams vs Users Fixture
 *
 */
class TeamsUserFixture extends CakeTestFixture {

/**
 * Name of fixture
 *
 * @var string
 * @access public
 */
	var $name = 'TeamsUser';

/**
 * Fields
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'team_id' => array('type' => 'integer'),
		'user_id' => array('type' => 'integer')
	);

/**
 * records property
 *
 * @var array
 * @access public
 */
	var $records = array(
		array('team_id' => 1, 'user_id' => 2),
		array('team_id' => 2, 'user_id' => 3),
		array('team_id' => 2, 'user_id' => 1),
		array('team_id' => 4, 'user_id' => 1)
	);
}
?>