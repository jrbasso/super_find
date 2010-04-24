<?php

class TeamFixture extends CakeTestFixture {

	var $name = 'Team';

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
		array('id' => 1, 'name' => 'Team 1'),
		array('id' => 2, 'name' => 'Team 2'),
		array('id' => 3, 'name' => 'Team 3'),
		array('id' => 4, 'name' => 'Team 4')
	);
}
?>