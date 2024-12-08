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

// PART 2 
$path = 'input.txt';
$map = textToMap(file_get_contents($path));

// Function to convert the input text into a 2D array
function textToMap($text) {
    $lines = explode("\n", trim($text));  // Split the text by lines and trim any leading/trailing whitespace
    $map = [];
    foreach ($lines as $line) {
        $map[] = str_split($line);  // Split each line into an array of characters
    }
    return $map;
}

// Directions: Up (^), Right (>), Down (v), Left (<)
$directions = [
    '^' => [-1, 0],  // Move up
    '>' => [0, 1],   // Move right
    'v' => [1, 0],   // Move down
    '<' => [0, -1]   // Move left
];

// Turn right mapping for each direction
$turnRight = [
    '^' => '>',  // Turn right from up to right
    '>' => 'v',  // Turn right from right to down
    'v' => '<',  // Turn right from down to left
    '<' => '^'   // Turn right from left to up
];

// Function to find the guard's starting position
function findGuard($map) {
    foreach ($map as $r => $row) {
        foreach ($row as $c => $cell) {
            if (in_array($cell, ['^', '>', 'v', '<'])) {
                return [$r, $c, $cell];  // Return position and direction
            }
        }
    }
    return null;
}

// Function to simulate the guard's movement and check if it gets stuck in a loop
function simulateGuardPath($map, $directions, $turnRight) {
    list($row, $col, $direction) = findGuard($map);

    $visited = [];
    $rows = count($map);
    $cols = count($map[0]);

    // Track the starting position
    $visited["$row,$col,$direction"] = true;

    while (true) {
        // Calculate the next position based on current direction
        $nextRow = $row + $directions[$direction][0];
        $nextCol = $col + $directions[$direction][1];

        // Check if the guard has gone out of bounds
        if ($nextRow < 0 || $nextRow >= $rows || $nextCol < 0 || $nextCol >= $cols) {
            break;  // Guard leaves the map
        }

        // If there's an obstacle, turn right
        if ($map[$nextRow][$nextCol] === '#' || $map[$nextRow][$nextCol] === 'O') {
            $direction = $turnRight[$direction];
        } else {
            // Otherwise, move forward
            $row = $nextRow;
            $col = $nextCol;
        }

        // Check if the current position and direction were visited before (loop)
        if (isset($visited["$row,$col,$direction"])) {
            return true;  // Guard is stuck in a loop
        }

        // Mark the position and direction as visited
        $visited["$row,$col,$direction"] = true;
    }

    return false;  // No loop detected
}

// Function to find all valid positions for obstructions that cause the guard to loop
function findValidObstructionPositions($map, $directions, $turnRight) {
    $validPositions = [];
    $rows = count($map);
    $cols = count($map[0]);

    // Try placing the obstruction in every empty position
    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            if ($map[$r][$c] === '.' && !in_array($map[$r][$c], ['^', '>', 'v', '<'])) {
                // Temporarily place the obstruction
                $map[$r][$c] = 'O';

                // Simulate the guard's movement
                if (simulateGuardPath($map, $directions, $turnRight)) {
                    // If the guard gets stuck in a loop, this position is valid
                    $validPositions[] = [$r, $c];
                }

                // Remove the obstruction
                $map[$r][$c] = '.';
            }
        }
    }

    return count($validPositions);  // Return the count of valid positions
}

// Run the simulation and print the result
$validPositionsCount = findValidObstructionPositions($map, $directions, $turnRight);
echo "<br>There are $validPositionsCount valid positions for the obstruction.\n";