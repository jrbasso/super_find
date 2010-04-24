<?php

class TaskFixture extends CakeTestFixture {

	var $name = 'Task';

	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false),
		'user_id' => array('type' => 'integer')
	);

/**
 * records property
 *
 * @var array
 * @access public
 */
	var $records = array(
		array('id' => 1, 'name' => 'Task 1', 'user_id' => 1),
		array('id' => 2, 'name' => 'Task 2', 'user_id' => 2),
		array('id' => 3, 'name' => 'Task 3', 'user_id' => 2),
		array('id' => 4, 'name' => 'Task 4', 'user_id' => 3)
	);
}
?>