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

		if (isset($query['conditions'])) {
			if (!is_array($query['conditions'])) {
				$query['conditions'] = (array)$query['conditions'];
			}
			$extraFinds = array();
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
					$extraFinds[$modelName][$key] = $value;
					unset($query['conditions'][$key]);
				}
			}
			foreach ($extraFinds as $modelName => $extraConditions) {
				$fk = $Model->hasMany[$modelName]['foreignKey'];
				$data = $Model->$modelName->find('all', array(
					'fields' => array($fk),
					'conditions' => $extraConditions,
					'recursive' => -1
				));
				$ids = Set::extract('{n}.' . $modelName . '.' . $fk, $data);
				if (empty($ids)) {
					$query['conditions'] = '1 = 0';
					break;
				}
				$query['conditions'][$Model->primaryKey] = array_unique($ids);
			}
		}

		return $Model->find($type, $query);
	}
}

?>