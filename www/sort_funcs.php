<?php
function quicksort(array $arr): array {
    $n = count($arr);
    if ($n < 2) return $arr;
    $pivot = $arr[intval($n/2)];
    $left = $right = $equal = [];
    foreach ($arr as $v) {
        if ($v < $pivot) $left[] = $v;
        elseif ($v > $pivot) $right[] = $v;
        else $equal[] = $v;
    }
    return array_merge(quicksort($left), $equal, quicksort($right));
}