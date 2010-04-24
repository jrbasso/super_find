<?php

class TeamsUserFixture extends CakeTestFixture {

	var $name = 'TeamsUser';

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