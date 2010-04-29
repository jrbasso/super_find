<?php
/**
 * Super Find Behavior
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          http://github.com/jrbasso/super_find
 * @package       super_find
 * @subpackage    super_find.models.behaviors
 * @since         SuperFind v0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SuperFindBehavior extends ModelBehavior {

/**
 * Makes the find with the possibility of using conditions of all relationships
 *
 * @access public
 * @param object $Model Pointer to model
 * @param array $conditions SQL conditions array, or type of find operation (all / first / count /
 *              neighbors / list / threaded)
 * @param mixed $fields Either a single string of a field name, or an array of field names, or
 *               options for matching
 * @param string $order SQL ORDER BY conditions (e.g. "price DESC" or "name ASC")
 * @param integer $recursive The number of levels deep to fetch associated records
 * @return array Array of records
 * @access public
 */
	function superFind(&$Model, $conditions = null, $fields = array(), $order = null, $recursive = null) {
		if (!is_string($conditions)) {
			$type = 'first';
			$query = array_merge(compact('conditions', 'fields', 'order', 'recursive'), array('limit' => 1));
		} else {
			list($type, $query) = array($conditions, $fields);
		}

		$relations = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');
		$originalRelations = array();
		foreach ($relations as $relation) {
			$originalRelations[$relation] = $Model->$relation;
		}
		if (isset($query['conditions'])) {
			if (!is_array($query['conditions'])) {
				$query['conditions'] = (array)$query['conditions'];
			}
			$extraFinds = array(
				'hasMany' => array(),
				'HABTM' => array()
			);
			foreach ($query['conditions'] as $key => $value) {
				$check = $value;
				if (is_string($key)) {
					$check = $key;
				}
				if (!preg_match('/^(\w+)\.(\w+)/', $check, $matches)) {
					continue;
				}
				list(, $modelName, $fieldName) = $matches;
				if ($modelName === $Model->alias || isset($Model->belongsTo[$modelName]) || isset($Model->hasOne[$modelName])) {
					continue;
				}
				if (isset($Model->hasMany[$modelName], $Model->$modelName)) {
					$extraFinds['hasMany'][$modelName][$key] = $value;
					unset($query['conditions'][$key]);
				} elseif (isset($Model->hasAndBelongsToMany[$modelName], $Model->$modelName)) {
					$extraFinds['HABTM'][$modelName][$key] = $value;
					unset($query['conditions'][$key]);
				}
			}
			foreach ($extraFinds['hasMany'] as $modelName => $extraConditions) {
				$fk = $Model->hasMany[$modelName]['foreignKey'];
				$pk = $modelName . '.' . $Model->$modelName->primaryKey;
				$data = $Model->$modelName->find('all', array(
					'fields' => array($fk, $pk),
					'conditions' => $extraConditions,
					'recursive' => -1
				));
				$masterModelIds = array_unique(Set::extract('{n}.' . $modelName . '.' . $fk, $data));
				if (empty($masterModelIds)) {
					$query['conditions'] = '1 = 0';
					break;
				}
				$query['conditions'][$Model->primaryKey] = $masterModelIds;
				$otherModelIds = array_unique(Set::extract('{n}.' . $pk, $data));
				$this->_addCondition($Model, 'hasMany', $modelName, $pk, $otherModelIds);
			}
			foreach ($extraFinds['HABTM'] as $modelName => $extraConditions) {
				$pk = $modelName . '.' . $Model->$modelName->primaryKey;
				$data = $Model->$modelName->find('all', array(
					'fields' => array($pk),
					'conditions' => $extraConditions,
					'recursive' => -1
				));
				if (empty($data)) {
					$query['conditions'] = '1 = 0';
					break;
				}
				$otherModelIds = array_unique(Set::extract('{n}.' . $pk, $data));

				$relationModel = new Model(array(
					'table' => $Model->hasAndBelongsToMany[$modelName]['joinTable'],
					'ds' => $Model->useDbConfig,
					'name' => 'Relation'
				));
				$data = $relationModel->find('all', array(
					'fields' => array($Model->hasAndBelongsToMany[$modelName]['foreignKey']),
					'conditions' => array($Model->hasAndBelongsToMany[$modelName]['associationForeignKey'] => $otherModelIds)
				));
				unset($relationModel);
				if (empty($data)) {
					$query['conditions'] = '1 = 0';
					break;
				}
				$masterModelIds = array_unique(Set::extract('{n}.Relation.' . $Model->hasAndBelongsToMany[$modelName]['foreignKey'], $data));

				$query['conditions'][$Model->primaryKey] = $masterModelIds;
				$this->_addCondition($Model, 'hasAndBelongsToMany', $modelName, $pk, $otherModelIds);
			}
		}

		$return = $Model->find($type, $query);
		foreach ($relations as $relation) {
			$Model->$relation = $originalRelations[$relation];
		}

		return $return;
	}

/**
 * Add a conditions in model
 *
 * @access protected
 * @return void
 */
	function _addCondition(&$Model, $type, $otherModel, $field, $value) {
		$relation =& $Model->$type;
		if (!empty($relation[$otherModel]['conditions'])) {
			$relation[$otherModel]['conditions'] = array($relation[$otherModel]['conditions']);
		}
		if (isset($relation[$otherModel]['conditions'][$field])) {
			if (!is_array($relation[$otherModel]['conditions'][$field])) {
				$relation[$otherModel]['conditions'][$field] = array($relation[$otherModel]['conditions'][$field]);
			}
		} else {
			$relation[$otherModel]['conditions'][$field] = array();
		}
		$relation[$otherModel]['conditions'][$field] = array_merge($relation[$otherModel]['conditions'][$field], $value);
	}
}

?>