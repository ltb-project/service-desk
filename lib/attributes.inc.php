<?php

namespace Ltb;

class Attributes {
    public static function isMandatoryAttribute($config, $operation) {
        if (!isset($config['mandatory']) || !is_array($config['mandatory'])) {
            return false;
        }

        return in_array('all', $config['mandatory']) || in_array($operation, $config['mandatory']);
    }

    public static function mandatoryAttributeHasValue($value) {
        if (is_array($value)) {
            foreach ($value as $item) {
                if ($item !== null && $item !== '') {
                    return true;
                }
            }

            return false;
        }

        return $value !== null && $value !== '';
    }

    public static function findMissingMandatoryAttributes($operation, $attributes_map, $entry_attributes, $items = null) {
        $missing = array();

        foreach ($attributes_map as $item => $config) {
            if ($items !== null && !in_array($item, $items, true)) {
                continue;
            }

            if (!self::isMandatoryAttribute($config, $operation)) {
                continue;
            }

            $attribute = $config['attribute'] ?? null;
            if (!$attribute || !self::mandatoryAttributeHasValue($entry_attributes[$attribute] ?? null)) {
                $missing[] = $item;
            }
        }

        return $missing;
    }
}
