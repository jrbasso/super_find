<?php

class SuperFindBehavior extends ModelBehavior {

	function setup(&$model, $config = array()) {
	}

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
				if (strpos($check, '.') === false) {
					continue;
				}
				list($modelName, $fieldName) = explode('.', $check, 2);
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
				if (!empty($Model->hasMany[$modelName]['conditions'])) {
					$Model->hasMany[$modelName]['conditions'] = array($Model->hasMany[$modelName]['conditions']);
				}
				if (isset($Model->hasMany[$modelName]['conditions'][$pk])) {
					if (!is_array($Model->hasMany[$modelName]['conditions'][$pk])) {
						$Model->hasMany[$modelName]['conditions'][$pk] = array($Model->hasMany[$modelName]['conditions'][$pk]);
					}
				} else {
					$Model->hasMany[$modelName]['conditions'][$pk] = array();
				}
				$Model->hasMany[$modelName]['conditions'][$pk] = array_merge($Model->hasMany[$modelName]['conditions'][$pk], $otherModelIds);
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
				if (!empty($Model->hasAndBelongsToMany[$modelName]['conditions'])) {
					$Model->hasAndBelongsToMany[$modelName]['conditions'] = array($Model->hasAndBelongsToMany[$modelName]['conditions']);
				}
				if (isset($Model->hasAndBelongsToMany[$modelName]['conditions'][$pk])) {
					if (!is_array($Model->hasAndBelongsToMany[$modelName]['conditions'][$pk])) {
						$Model->hasAndBelongsToMany[$modelName]['conditions'][$pk] = array($Model->hasAndBelongsToMany[$modelName]['conditions'][$pk]);
					}
				} else {
					$Model->hasAndBelongsToMany[$modelName]['conditions'][$pk] = array();
				}
				$Model->hasAndBelongsToMany[$modelName]['conditions'][$pk] = array_merge($Model->hasAndBelongsToMany[$modelName]['conditions'][$pk], $otherModelIds);
			}
		}

		$return = $Model->find($type, $query);
		foreach ($relations as $relation) {
			$Model->$relation = $originalRelations[$relation];
		}

		return $return;
	}
}

?>