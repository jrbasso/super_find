# Super Find Plugin

The Super Find is a plugin to CakePHP that allows you to search using the fields in models of relationships hasMany and HABTM (hasAndBelongsToMany).

Tested with CakePHP 1.3.0.

## Requirements

- PHP 4.3.2 or higher
- CakePHP

## Usage

To use, add the content of this plugin in app/plugins/super_find and add 'SuperFind.SuperFind' in your Models. Example:

	<?php
		class User extends AppModel {
			var $name = 'User';
			var $actsAs = array('SuperFind.SuperFind');
			var $hasMany = array('Task');
		}
	?>

To make a search, change method 'find' to 'superFind' with the some parameters, example:

	$this->User->superFind('all', array('conditions' => array('Task.name' => 'Name of task')));

To see more examples, read the test cases. :)

## It's not a Containable

This plugin does the same as Containable. The containable filters the results of the associations, but does not filter the records of the main model. For example, when it is filtered using containable, it shows all records of the table and leaves the associations empty if there are no related records.

Example: If we query the `User` model that has records 'User 1', 'User 2' and 'User 3' in the 'name' field, and records with values of 'Task 1' (of user 1) and 'Task 2' (of user 2) in the 'name' field of the 'Task' model, and our association is User hasMany Tasks.

Here are the results if we do a regular find on the 'User' model for users associated with 'Task 1':

	$this->User->find('all', array('contain' => array('Task.name = "Task 1")));
	/* Output:
		array(
			array(
				'User' => array('id' => 1, 'name' => 'User 1'),
				'Task' => array(
					array('id' => 1, 'name' => 'Task 1', 'user_id' => 1)
				)
			),
			array(
				'User' => array('id' => 2, 'name' => 'User 2'),
				'Task' => array(
				)
			),
			array(
				'User' => array('id' => 3, 'name' => 'User 3'),
				'Task' => array(
				)
			),
		)
	*/

Here are the results using SuperFind:

	$this->User->superFind('all', array('conditions' => array('Task.name' => 'Task 1')));
	/* Output:
		array(
			array(
				'User' => array('id' => 1, 'name' => 'User 1'),
				'Task' => array(
					array('id' => 1, 'name' => 'Task 1', 'user_id' => 1)
				)
			)
		)
	*/

Note that SuperFind does not show 'User 2' and 'User 3' because they are not associated with 'Task 1'.

## Known Limitations

- Not tested with others behaviors (like Containable).
- No work with 'NOT', 'OR' and others in conditions.

## License

Licensed under The MIT License (http://www.opensource.org/licenses/mit-license.php).