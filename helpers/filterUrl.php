<?php
// ?filter=type:alert_idUser:5
// ?filter=type:offer_idUser:5_read:0
/**
 * Función para parsear la variable filter de la URL
 */
function parseFilter($filterString) {
    $filters = [];
    $filterPairs = explode('_', $filterString);
    foreach ($filterPairs as $pair) {
        list($key, $value) = explode(':', $pair);
        $filters[$key] = $value;
    }
    return $filters;
}
?>