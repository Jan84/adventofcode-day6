<?php

$path = 'input.txt';
$map = file($path, FILE_IGNORE_NEW_LINES);
$startX = 0;
$startY = 0;

// starting direction UP
$direction = 0;
$directions = [
    [-1, 0], // UP
    [0, 1], // RIGHT
    [1, 0], // DOWN
    [0, -1] // LEFT
];

// Find the starting position
foreach ($map as $y => $line) {
    $x = strpos($line, '^');
    if ($x) {
        $startX = $x;
        $startY = $y;
        break;
    }
}

$visited = [];
// Mark starting point as visited
$visited["$startY, $startX"] = true;

// Start mapping
while (1) {
    // Next position
    $nextX = $startX + $directions[$direction][1];
    $nextY = $startY + $directions[$direction][0];

    // Check if the position is out of the map
    if ($nextX < 0 || $nextX >= strlen($map[$nextY]) || $nextY < 0 || $nextY >= count($map)) {
        break;
    }

    // Check if obstacle found
    if ($map[$nextY][$nextX] == '#') {
        // Change direction to the RIGHT
        $direction = ($direction + 1) % 4;
    } else {
        // Continue to the next position in the same direction
        $startX = $nextX;
        $startY = $nextY;
        $visited["$startY, $startX"] = true;
    }
}

echo 'Number of visited positions: ' . count($visited);