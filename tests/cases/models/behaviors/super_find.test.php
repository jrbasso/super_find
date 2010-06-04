<?php
/**
 * Super Find Behavior Tests
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          http://github.com/jrbasso/super_find
 * @package       super_find
 * @subpackage    super_find.tests.cases.models.behaviors
 * @since         SuperFind v0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * User Model for Test
 *
 */
class User extends CakeTestModel {
	var $name = 'User';
	var $actsAs = array('SuperFind.SuperFind');
	var $hasMany = array('Task');
	var $hasAndBelongsToMany = array('Team');
}

/**
 * Task Model for Test
 *
 */
class Task extends CakeTestModel {
	var $name = 'Task';
	var $actsAs = array('SuperFind.SuperFind');
	var $belongsTo = array('Category', 'User');
	var $hasMany = array('Record');
}

/**
 * Category Model for Test
 *
 */
class Category extends CakeTestModel {
	var $name = 'Category';
	var $actsAs = array('SuperFind.SuperFind');
	var $hasMany = array('Task');
}

/**
 * Record Model for Test
 *
 */
class Record extends CakeTestModel {
	var $name = 'Record';
	var $actsAs = array('SuperFind.SuperFind');
	var $belongsTo = array('Task');
}

/**
 * Team Model for Test
 *
 */
class Team extends CakeTestModel {
	var $name = 'Team';
	var $actsAs = array('SuperFind.SuperFind');
	var $hasAndBelongsToMany = array('User');
}

/**
 * SuperFindBehavior tests
 *
 */
class SuperFindBehaviorTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 * @access public
 */
	var $fixtures = array(
		'plugin.super_find.user', 'plugin.super_find.task', 'plugin.super_find.team', 'plugin.super_find.teams_user',
		'plugin.super_find.record', 'plugin.super_find.category'
	);

/**
 * startTest
 *
 * @access public
 * @return void
 */
	function startTest() {
		$this->User =& ClassRegistry::init('User');
		$this->Record =& ClassRegistry::init('Record');
	}

/**
 * testAllFields
 *
 * This tests works in core, just checking if keeping working... :)
 *
 * @access public
 * @return void
 */
	function testAllFields() {
		$result = $this->Record->superFind('all', array('fields' => 'Record.*', 'recursive' => -1, 'limit' => 1));
		$expected = array(array('Record' => array('id' => 1, 'title' => 'Record 1', 'task_id' => 1)));
		$this->assertEqual($result, $expected);

		$result = $this->Record->superFind('all', array('fields' => array('Record.*'), 'recursive' => -1, 'limit' => 1));
		$expected = array(array('Record' => array('id' => 1, 'title' => 'Record 1', 'task_id' => 1)));
		$this->assertEqual($result, $expected);

		$result = $this->Record->superFind('all', array('fields' => array('Record.title', 'Task.*'), 'recursive' => 0, 'limit' => 1));
		$expected = array(array('Record' => array('title' => 'Record 1'), 'Task' => array('id' => 1, 'name' => 'Task 1', 'user_id' => 1, 'category_id' => 1)));
		$this->assertEqual($result, $expected);
	}

/**
 * testConditionsInHasManyOfOneLevel
 *
 * @access public
 * @return void
 */
	function testConditionsInHasManyOfOneLevel() {
		// Conditions as string
		$result = $this->User->superFind('all', array('conditions' => 'Task.name = "Task 1"', 'recursive' => -1));
		$expected = array(array('User' => array('id' => 1, 'name' => 'User 1')));
		$this->assertEqual($result, $expected);

		// Conditions with string value
		$result = $this->User->superFind('all', array('conditions' => array('Task.name = "Task 1"'), 'recursive' => -1));
		$this->assertEqual($result, $expected);

		// Conditions with key/value
		$result = $this->User->superFind('all', array('conditions' => array('Task.name' => 'Task 1'), 'recursive' => -1));
		$this->assertEqual($result, $expected);

		// Multiples records of relation
		$result = $this->User->superFind('all', array('conditions' => array('Task.user_id' => 2)));
		$expected = array(array(
			'User' => array('id' => 2, 'name' => 'User 2'),
			'Task' => array(
				array('id' => 2, 'name' => 'Task 2', 'user_id' => 2, 'category_id' => 1),
				array('id' => 3, 'name' => 'Task 3', 'user_id' => 2, 'category_id' => 1)
			),
			'Team' => array(
				array('id' => 1, 'name' => 'Team 1')
			)
		));
		$this->assertEqual($result, $expected);

		// Conditions with relation model and self model
		$result = $this->User->superFind('all', array('conditions' => array('Task.user_id' => 2, 'User.name' => 'User 2')));
		$this->assertEqual($result, $expected);

		// Conditions with two conditions of relation model
		$result = $this->User->superFind('all', array('conditions' => array('Task.user_id' => 2, 'Task.name' => 'Task 2')));
		$expected = array(array(
			'User' => array('id' => 2, 'name' => 'User 2'),
			'Task' => array(
				array('id' => 2, 'name' => 'Task 2', 'user_id' => 2, 'category_id' => 1)
			),
			'Team' => array(
				array('id' => 1, 'name' => 'Team 1')
			)
		));
		$this->assertEqual($result, $expected);

		// Find first
		$result = $this->User->superFind('first', array('conditions' => array('Task.user_id' => 2, 'Task.name' => 'Task 2')));
		$this->assertEqual($result, $expected[0]);

		// hasMany empty
		$result = $this->User->superFind('all', array('conditions' => array('Task.name' => 'Task 999')));
		$expected = array();
		$this->assertIdentical($result, $expected);

		// Conditions in hasMany model not exists
		$result = $this->User->superFind('all', array('conditions' => array('Task.user_id' => 2, 'Task.name' => 'Task 1')));
		$this->assertIdentical($result, $expected);

		// hasMany model return value, but not have user with this values
		$result = $this->User->superFind('all', array('conditions' => array('Task.user_id' => 2, 'User.name' => 'User 1')));
		$this->assertIdentical($result, $expected);
	}

/**
 * testConditionsInHasManyOfOneLevel
 *
 * @access public
 * @return void
 */
	function testConditionsInHABTMOfOneLevel() {
		// HABTM with one condition
		$result = $this->User->superFind('all', array('conditions' => array('Team.name' => 'Team 1')));
		$expected = array(array(
			'User' => array('id' => 2, 'name' => 'User 2'),
			'Task' => array(
				array('id' => 2, 'name' => 'Task 2', 'user_id' => 2, 'category_id' => 1),
				array('id' => 3, 'name' => 'Task 3', 'user_id' => 2, 'category_id' => 1)
			),
			'Team' => array(
				array('id' => 1, 'name' => 'Team 1')
			)
		));
		$this->assertEqual($result, $expected);

		// Multiples records of Users
		$result = $this->User->superFind('all', array('conditions' => array('Team.name' => 'Team 2')));
		$expected = array(
			array(
				'User' => array('id' => 1, 'name' => 'User 1'),
				'Task' => array(
					array('id' => 1, 'name' => 'Task 1', 'user_id' => 1, 'category_id' => 1)
				),
				'Team' => array(
					array('id' => 2, 'name' => 'Team 2')
				)
			),
			array(
				'User' => array('id' => 3, 'name' => 'User 3'),
				'Task' => array(
					array('id' => 4, 'name' => 'Task 4', 'user_id' => 3, 'category_id' => 2)
				),
				'Team' => array(
					array('id' => 2, 'name' => 'Team 2')
				)
			)
		);
		$this->assertEqual($result, $expected);

		// Find first
		$result = $this->User->superFind('first', array('conditions' => array('Team.name' => 'Team 2')));
		$this->assertEqual($result, $expected[0]);

		// Filter in HABTM and master model
		$result = $this->User->superFind('all', array('conditions' => array('Team.name' => 'Team 2', 'User.id' => 1)));
		$expected = array(
			array(
				'User' => array('id' => 1, 'name' => 'User 1'),
				'Task' => array(
					array('id' => 1, 'name' => 'Task 1', 'user_id' => 1, 'category_id' => 1)
				),
				'Team' => array(
					array('id' => 2, 'name' => 'Team 2')
				)
			)
		);
		$this->assertEqual($result, $expected);
	}

/**
 * testConditionsInHasManyOfMultipleLevels
 *
 * @access public
 * @return void
 */
	function testConditionsInHasManyOfMultipleLevels() {
		// Invalid record
		$result = $this->User->superFind('all', array('conditions' => array('Task.Record.title' => 'Record 999')));
		$expected = array();
		$this->assertIdentical($result, $expected);

		// Record that exists
		$result = $this->User->superFind('all', array('conditions' => array('Task.Record.title' => 'Record 1')));
		$expected = array(
			array(
				'User' => array('id' => 1, 'name' => 'User 1'),
				'Task' => array(
					array('id' => 1, 'name' => 'Task 1', 'user_id' => 1, 'category_id' => 1)
				),
				'Team' => array(
					array('id' => 2, 'name' => 'Team 2'),
					array('id' => 4, 'name' => 'Team 4'),
				)
			)
		);
		$this->assertEqual($result, $expected);

		// Record that exists (with string)
		$result = $this->User->superFind('all', array('conditions' => 'Task.Record.title = "Record 1"'));
		$this->assertEqual($result, $expected);

		// Record that exists (with string in array)
		$result = $this->User->superFind('all', array('conditions' => array('Task.Record.title = "Record 1"')));
		$this->assertEqual($result, $expected);

		// Record that exists with Task too
		$result = $this->User->superFind('all', array('conditions' => array('Task.Record.title' => 'Record 1', 'Task.name' => 'Task 1')));
		$this->assertEqual($result, $expected);

		// Record that exists but Task no
		$result = $this->User->superFind('all', array('conditions' => array('Task.Record.title' => 'Record 1', 'Task.name' => 'Task 999')));
		$expected = array();
		$this->assertIdentical($result, $expected);
	}

/**
 * testConditionsOfHABTMAndHasMany
 *
 * @access public
 * @return void
 */
	function testConditionsOfHABTMAndHasMany() {
		$result = $this->User->superFind('all', array('conditions' => array('Team.name' => 'Team 1', 'Task.name' => 'Task 2')));
		$expected = array(array(
			'User' => array('id' => 2, 'name' => 'User 2'),
			'Task' => array(
				array('id' => 2, 'name' => 'Task 2', 'user_id' => 2, 'category_id' => 1)
			),
			'Team' => array(
				array('id' => 1, 'name' => 'Team 1')
			)
		));
		$this->assertEqual($result, $expected);
	}

/**
 * testConditionsWithBelongsTo
 *
 * @access public
 * @return void
 */
	function testConditionsWithBelongsTo() {
		$result = $this->User->superFind('all', array('conditions' => array('Task.Category.name' => 'Category 2')));
		$expected = array(
			array(
				'User' => array('id' => '3', 'name' => 'User 3'),
				'Task' => array(
					array('id' => '4', 'name' => 'Task 4', 'user_id' => '3', 'category_id' => '2')
				),
				'Team' => array(
					array('id' => '2', 'name' => 'Team 2')
				)
			)
		);
		$this->assertEqual($result, $expected);

		$result = $this->Record->superFind('all', array('conditions' => array('Task.User.Team.name' => 'Team 1')));
		$expected = array(
			array(
				'Record' => array('id' => '2', 'title' => 'Record 2', 'task_id' => '2'),
				'Task' => array('id' => '2', 'name' => 'Task 2', 'user_id' => '2', 'category_id' => '1')
			),
			array(
				'Record' => array('id' => '3', 'title' => 'Record 3', 'task_id' => '2'),
				'Task' => array('id' => '2', 'name' => 'Task 2', 'user_id' => '2', 'category_id' => '1')
			),
			array(
				'Record' => array('id' => '4', 'title' => 'Record 4', 'task_id' => '3'),
				'Task' => array('id' => '3', 'name' => 'Task 3', 'user_id' => '2', 'category_id' => '1')
			)
		);
		$this->assertEqual($result, $expected);
	}

/**
 * testVarious
 *
 * @access public
 * @return void
 */
	function testVarious() {
		$result = $this->User->superFind('all', array('conditions' => array('name = "User.1"')));
		$expected = array();
		$this->assertIdentical($result, $expected);
	}
}

?>