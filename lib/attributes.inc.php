<?php

function is_mandatory_attribute($config, $operation) {
    if (!isset($config['mandatory']) || !is_array($config['mandatory'])) {
        return false;
    }

    return in_array('all', $config['mandatory']) || in_array($operation, $config['mandatory']);
}

function mandatory_attribute_has_value($value) {
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

function find_missing_mandatory_attributes($operation, $attributes_map, $entry_attributes, $items = null) {
    $missing = array();

    foreach ($attributes_map as $item => $config) {
        if ($items !== null && !in_array($item, $items, true)) {
            continue;
        }

        if (!is_mandatory_attribute($config, $operation)) {
            continue;
        }

        $attribute = $config['attribute'] ?? null;
        if (!$attribute || !mandatory_attribute_has_value($entry_attributes[$attribute] ?? null)) {
            $missing[] = $item;
        }
    }

    return $missing;
}
?>
