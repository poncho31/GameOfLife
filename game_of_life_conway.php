<?php

/** ############### RUN ###################
 *
 * Command : php -S localhost:8000
 * Url     : http://localhost:8000/game_of_life_conway.php
 *
 * */

// Définir la taille de la grille
$cols        = 100; // Vous pouvez modifier cette valeur selon vos besoins
$square_size = 10;
// Génération de la grille aléatoire
function generateRandomGrid($size) {
    $grid = [];
    for ($i = 0; $i < $size; $i++) {
        $grid[$i] = [];
        for ($j = 0; $j < $size; $j++) {
            $grid[$i][$j] = rand(0, 1);
        }
    }
    return $grid;
}

// Convertir la grille en JSON pour l'envoyer au JavaScript
$gridJSON = json_encode(generateRandomGrid($cols));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conway's Game of Life</title>
    <style>
        /* CSS pour la grille */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(<?php echo $cols; ?>, <?php echo $square_size; ?>px);
            background-color: #f0f0f0;
            margin-bottom: <?php echo $square_size; ?>px;
        }
        .grid-item {
            width: <?php echo $square_size; ?>px;
            height: <?php echo $square_size; ?>px;
            border: 1px solid #aaa;
        }
    </style>
</head>
<body>

<select id="configurations">
    <option value="glider">Glider</option>
    <option value="pulsar">Pulsar</option>
    <option value="random">Random</option>
</select>
<button onclick="startGame()">Start</button>

<div class="grid-container" id="gridContainer"></div>

<script defer>
    let grid = <?php echo $gridJSON; ?>;
    let gridSize = <?php echo $cols; ?>;
    let intervalId;

    function createGrid() {
        let gridContainer = document.getElementById('gridContainer');
        let html = '';
        for (let i = 0; i < gridSize; i++) {
            html += '<div>';
            for (let j = 0; j < gridSize; j++) {
                html += '<div class="grid-item" id="' + i + '-' + j + '" onclick="toggleCell(' + i + ',' + j + ')"></div>';
            }
            html += '</div>';
        }
        gridContainer.innerHTML = html;
        updateGridDisplay();
    }

    function toggleCell(row, col) {
        grid[row][col] = grid[row][col] === 1 ? 0 : 1;
        updateGridDisplay();
    }

    function startGame() {
        let selectedConfig = document.getElementById('configurations').value;
        clearInterval(intervalId);
        switch (selectedConfig) {
            case 'glider':
                setGlider();
                break;
            case 'pulsar':
                setPulsar();
                break;
            default:
                generateRandomGrid();
        }
        intervalId = setInterval(updateGrid, 200);
    }

    function setGlider() {
        // Ajoutez ici la configuration Glider
        grid[1][2] = 1;
        grid[2][3] = 1;
        grid[3][1] = 1;
        grid[3][2] = 1;
        grid[3][3] = 1;
    }

    function setPulsar() {
        // Ajoutez ici la configuration Pulsar
        let centerX = Math.floor(gridSize / 2);
        let centerY = Math.floor(gridSize / 2);
        for (let i = -2; i <= 2; i++) {
            for (let j = -2; j <= 2; j++) {
                if (i === -2 || i === 2 || j === -2 || j === 2 || (i === -1 && j === -1) || (i === -1 && j === 1) || (i === 1 && j === -1) || (i === 1 && j === 1)) {
                    grid[centerX + i][centerY + j] = 1;
                }
            }
        }
    }

    function generateRandomGrid() {
        for (let i = 0; i < gridSize; i++) {
            for (let j = 0; j < gridSize; j++) {
                grid[i][j] = Math.round(Math.random());
            }
        }
        updateGridDisplay();
    }

    function updateGrid() {
        let newGrid = [];
        for (let i = 0; i < gridSize; i++) {
            newGrid[i] = [];
            for (let j = 0; j < gridSize; j++) {
                let neighbors = countNeighbors(i, j);
                if (grid[i][j] === 1) {
                    newGrid[i][j] = (neighbors === 2 || neighbors === 3) ? 1 : 0;
                } else {
                    newGrid[i][j] = (neighbors === 3) ? 1 : 0;
                }
            }
        }
        grid = newGrid;
        updateGridDisplay();
    }

    function countNeighbors(row, col) {
        let count = 0;
        for (let i = -1; i <= 1; i++) {
            for (let j = -1; j <= 1; j++) {
                if (!(i === 0 && j === 0)) {
                    let newRow = row + i;
                    let newCol = col + j;
                    if (newRow >= 0 && newRow < gridSize && newCol >= 0 && newCol < gridSize) {
                        count += grid[newRow][newCol];
                    }
                }
            }
        }
        return count;
    }

    function updateGridDisplay() {
        for (let i = 0; i < gridSize; i++) {
            for (let j = 0; j < gridSize; j++) {
                let cell = document.getElementById(i + '-' + j);
                cell.style.backgroundColor = grid[i][j] ? 'black' : 'white';
            }
        }
    }

    window.onload = createGrid;
</script>

</body>
</html>
