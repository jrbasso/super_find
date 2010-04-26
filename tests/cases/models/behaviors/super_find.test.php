<?php

class User extends CakeTestModel {
	var $name = 'User';
	var $actsAs = array('SuperFind.SuperFind');
	var $hasMany = array('Task');
	var $hasAndBelongsToMany = array('Team');
}

class Task extends CakeTestModel {
	var $name = 'Task';
	var $actsAs = array('SuperFind.SuperFind');
	var $belongsTo = array('User');
	var $hasMany = array('Record');
}

class Record extends CakeTestModel {
	var $name = 'Record';
	var $actsAs = array('SuperFind.SuperFind');
	var $belongsTo = array('Task');
}

class Team extends CakeTestModel {
	var $name = 'Team';
	var $actsAs = array('SuperFind.SuperFind');
	var $hasAndBelongsToMany = array('User');
}

class SuperFindBehaviorTest extends CakeTestCase {

	var $fixtures = array(
		'plugin.super_find.user', 'plugin.super_find.task', 'plugin.super_find.team', 'plugin.super_find.teams_user',
		'plugin.super_find.record'
	);

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
		$expected = array(array('Record' => array('title' => 'Record 1'), 'Task' => array('id' => 1, 'name' => 'Task 1', 'user_id' => 1)));
		$this->assertEqual($result, $expected);
	}

	function testConditionsInHasManyOfOneLevel() {
		$result = $this->User->superFind('all', array('conditions' => array('Task.name = "Task 1"'), 'recursive' => -1));
		$expected = array(array('User' => array('id' => 1, 'name' => 'User 1')));
		$this->assertEqual($result, $expected);

		$result = $this->User->superFind('all', array('conditions' => array('Task.name' => 'Task 1'), 'recursive' => -1));
		$this->assertEqual($result, $expected);

		$result = $this->User->superFind('all', array('conditions' => array('Task.user_id' => 2)));
		$expected = array(array(
			'User' => array('id' => 2, 'name' => 'User 2'),
			'Task' => array(
				array('id' => 2, 'name' => 'Task 2', 'user_id' => 2),
				array('id' => 3, 'name' => 'Task 3', 'user_id' => 2)
			),
			'Team' => array(
				array('id' => 1, 'name' => 'Team 1')
			)
		));
		$this->assertEqual($result, $expected);

		$result = $this->User->superFind('all', array('conditions' => array('Task.user_id' => 2, 'User.name' => 'User 2')));
		$this->assertEqual($result, $expected);

		$result = $this->User->superFind('all', array('conditions' => array('Task.user_id' => 2, 'Task.name' => 'Task 2')));
		$expected = array(array(
			'User' => array('id' => 2, 'name' => 'User 2'),
			'Task' => array(
				array('id' => 2, 'name' => 'Task 2', 'user_id' => 2)
			),
			'Team' => array(
				array('id' => 1, 'name' => 'Team 1')
			)
		));
		$this->assertEqual($result, $expected);

		$result = $this->User->superFind('all', array('conditions' => array('Task.name' => 'Task 999')));
		$expected = array();
		$this->assertIdentical($result, $expected);

		$result = $this->User->superFind('all', array('conditions' => array('Task.user_id' => 2, 'Task.name' => 'Task 1')));
		$this->assertIdentical($result, $expected);

		$result = $this->User->superFind('all', array('conditions' => array('Task.user_id' => 2, 'User.name' => 'User 1')));
		$this->assertIdentical($result, $expected);
	}

}

?>