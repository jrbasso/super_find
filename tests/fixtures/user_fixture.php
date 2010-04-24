<?php

class UserFixture extends CakeTestFixture {

	var $name = 'User';

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
		array('id' => 1, 'name' => 'User 1'),
		array('id' => 2, 'name' => 'User 2'),
		array('id' => 3, 'name' => 'User 3'),
		array('id' => 4, 'name' => 'User 4')
	);
}
?>