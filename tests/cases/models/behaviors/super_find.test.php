<?php

class User extends CakeTestModel {
	var $name = 'User';
	var $hasMany = array('Task');
	var $hasAndBelongsToMany = array('Team');
}

class Task extends CakeTestModel {
	var $name = 'Task';
	var $belongsTo = array('User');
	var $hasMany = array('Record');
}

class Record extends CakeTestModel {
	var $name = 'Record';
	var $belongsTo = array('Task');
}

class Team extends CakeTestModel {
	var $name = 'Team';
	var $hasAndBelongsToMany = array('User');
}

class SuperFindBehaviorTest extends CakeTestCase {

	var $fixtures = array(
		'plugin.super_find.user', 'plugin.super_find.task', 'plugin.super_find.team', 'plugin.super_find.teams_user',
		'plugin.super_find.record'
	);

	function startTest() {
		$this->User =& ClassRegistry::init('User');
	}

	function testHasMany() {
		$result = $this->User->find('all', array('conditions' => array('Task.name' => 'Task 1')));
		$expected = array(array('User' => array('id' => 1, 'name' => 'User 1'), 'Task' => array(array('id' => 1, 'name' => 'Task 1', 'user_id' => 1))));
		$this->assertIdentical($result, $expected);

		$result = $this->User->find('all', array('conditions' => array('Task.user_id' => 2)));
		$expected = array(array('User' => array('id' => 2, 'name' => 'User 2'), 'Task' => array(array('id' => 2, 'name' => 'Task 2', 'user_id' => 2), array('id' => 3, 'name' => 'Task 3', 'user_id' => 2))));
		$this->assertIdentical($result, $expected);

		$result = $this->User->find('all', array('conditions' => array('Task.name' => 'Task 999')));
		$expected = array();
		$this->assertIdentical($result, $expected);

		$result = $this->User->find('all', array('order' => array('Task.name' => 'DESC'), 'limit' => 1));
		$expected = array(array('User' => array('id' => 3, 'name' => 'User 3'), 'Task' => array(array('id' => 4, 'name' => 'Task 4', 'user_id' => 3))));
		$this->assertIdentical($result, $expected);
	}

}

?>