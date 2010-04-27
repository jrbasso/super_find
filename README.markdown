# Super Find Plugin

The Super Find is a plugin to CakePHP that allows you to search using the fields in models of relationships hasMany and HABTM (hasAndBelongsToMany).

Tested with CakePHP 1.3.0.

## Requirements

- PHP 4.3.2 or higher

## Usage

To use, add the content of this plugin in app/plugins/super_find and add 'SuperFind.SuperFind' in your Models. Exemple:

	<?php
		class User extends AppModel {
			var $name = 'User';
			var $actsAs = array('SuperFind.SuperFind');
			var $hasMany = array('Task');
		}
	?>

To make a search, change method 'find' by 'superFind' with the some parameters, exemple:

	$this->User->superFind('all', array('conditions' => array('Task.name' => 'Name of task')));

To see more exemples, read the tests cases. :)

## It's not a Containable

This plugin does the same as the Containable. The containable filters the results of the associations, but does not filter the records of main model. For example, when it is filtered using containable, it shows all records of the table and studied the associations empty.

Exemple: If we have a model `User` with records 'User 1', 'User 2' and 'User 3' in field 'name' and records with values 'Task 1' (of user 1) and 'Task 2' (of user 2) for field 'name' of model Task. User has many Tasks. See the results:

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

Note that in Super Find does not show 'User 2' and 'User 3' because they lack the 'Task 1'.

## Know Limitations

- So far the plugin supports the search for just one level, ie you can't use `Task.Record.field` in conditions;
- Not tested with others behaviors (like Behaviors).

## License

Licensed under The MIT License (http://www.opensource.org/licenses/mit-license.php).