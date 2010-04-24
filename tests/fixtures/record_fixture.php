<?php

class RecordFixture extends CakeTestFixture {

	var $name = 'Record';

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