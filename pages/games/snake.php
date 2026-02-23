<h2>Snake</h2>
<a href="?page=games" class="back-link">&larr; Back to Games</a>

<div class="game-container" style="text-align: center; margin-top: 20px; max-width: 430px; margin-left: auto; margin-right: auto;">
    <div style="position: relative; width: 400px; margin: 0 auto;">
        <canvas id="snakeCanvas" width="400" height="400" style="display:block; background-color: #9bbc0f; border: 4px solid var(--wood-dark); border-radius: 8px; image-rendering: pixelated; box-shadow: inset 0 0 0 2px #0f380f;"></canvas>
        <div id="snakeOverlay" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 10px; background: rgba(15,56,15,0.72); color: #9bbc0f; border-radius: 8px; font-family: 'Pixelify Sans', sans-serif; padding: 20px;">
            <h3 id="overlayTitle" style="margin: 0; font-size: 1.9rem; color: #9bbc0f; letter-spacing: 1px;">Snake Garden</h3>
            <p id="overlaySubtitle" style="margin: 0; color: #e4f2a1; font-size: 1rem;">Pick a speed and start.</p>
            <div id="speedSelect" style="display: flex; gap: 8px; margin-top: 4px;">
                <button class="speed-btn" data-speed="140" style="font-family: 'Pixelify Sans', sans-serif; padding: 7px 12px; border: 2px solid var(--wood-dark); border-radius: 6px; background: #e4f2a1; color: #0f380f; cursor: pointer;">Calm</button>
                <button class="speed-btn" data-speed="105" style="font-family: 'Pixelify Sans', sans-serif; padding: 7px 12px; border: 2px solid var(--wood-dark); border-radius: 6px; background: #0f380f; color: #9bbc0f; cursor: pointer;">Normal</button>
                <button class="speed-btn" data-speed="80" style="font-family: 'Pixelify Sans', sans-serif; padding: 7px 12px; border: 2px solid var(--wood-dark); border-radius: 6px; background: #e4f2a1; color: #0f380f; cursor: pointer;">Fast</button>
            </div>
            <button id="overlayPlay" style="font-family: 'Pixelify Sans', sans-serif; margin-top: 6px; font-size: 1.05rem; padding: 8px 16px; border: 2px solid var(--wood-dark); border-radius: 6px; background: var(--wood-dark); color: #9bbc0f; cursor: pointer;">Play</button>
            <p style="margin: 2px 0 0; color: #e4f2a1; font-size: 0.85rem;">Move: Arrow Keys / WASD · Pause: P</p>
        </div>
    </div>

    <p style="margin-top: 10px; font-family: 'Pixelify Sans', sans-serif; font-size: 1.5rem; color: var(--text-light);">Score: <span id="score">0</span></p>
    <p id="gameHint" style="font-size: 0.9rem; color: var(--text-light);">Use Arrow Keys or WASD to move.</p>
</div>

<script>
const canvas = document.getElementById('snakeCanvas');
const ctx = canvas.getContext('2d');
const overlay = document.getElementById('snakeOverlay');
const overlayTitle = document.getElementById('overlayTitle');
const overlaySubtitle = document.getElementById('overlaySubtitle');
const overlayPlay = document.getElementById('overlayPlay');
const speedButtons = document.querySelectorAll('.speed-btn');
const scoreElement = document.getElementById('score');
const hintElement = document.getElementById('gameHint');

const box = 20;
const tiles = canvas.width / box;

let score = 0;
let snake = [];
let food = null;
let direction = 'RIGHT';
let nextDirection = 'RIGHT';
let game = null;
let isRunning = false;
let isGameOver = false;
let gameSpeed = 105;

function getRandomFoodPosition() {
    let position;

    do {
        position = {
            x: Math.floor(Math.random() * tiles) * box,
            y: Math.floor(Math.random() * tiles) * box
        };
    } while (snake.some(segment => segment.x === position.x && segment.y === position.y));

    return position;
}

function resetGame() {
    score = 0;
    scoreElement.innerText = score;
    direction = 'RIGHT';
    nextDirection = 'RIGHT';
    snake = [{ x: 9 * box, y: 10 * box }];
    food = getRandomFoodPosition();
    isGameOver = false;
    draw();
}

function collision(head, array) {
    return array.some(segment => head.x === segment.x && head.y === segment.y);
}

function drawBoard() {
    ctx.fillStyle = '#9bbc0f';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    for (let y = 0; y < tiles; y++) {
        for (let x = 0; x < tiles; x++) {
            if ((x + y) % 2 === 0) {
                ctx.fillStyle = 'rgba(15,56,15,0.06)';
                ctx.fillRect(x * box, y * box, box, box);
            }
        }
    }
}

function drawSnakeAndFood() {
    snake.forEach((segment, index) => {
        ctx.fillStyle = index === 0 ? '#306230' : '#0f380f';
        ctx.fillRect(segment.x, segment.y, box, box);

        ctx.strokeStyle = '#9bbc0f';
        ctx.strokeRect(segment.x + 1, segment.y + 1, box - 2, box - 2);
    });

    ctx.fillStyle = '#8b1c1c';
    ctx.fillRect(food.x, food.y, box, box);
    ctx.fillStyle = '#e4f2a1';
    ctx.fillRect(food.x + 6, food.y + 6, 4, 4);
}

function draw() {
    drawBoard();
    drawSnakeAndFood();
}

function gameLoop() {
    direction = nextDirection;

    let snakeX = snake[0].x;
    let snakeY = snake[0].y;

    if (direction === 'LEFT') snakeX -= box;
    if (direction === 'UP') snakeY -= box;
    if (direction === 'RIGHT') snakeX += box;
    if (direction === 'DOWN') snakeY += box;

    const newHead = { x: snakeX, y: snakeY };

    if (
        snakeX < 0 ||
        snakeX >= canvas.width ||
        snakeY < 0 ||
        snakeY >= canvas.height ||
        collision(newHead, snake)
    ) {
        endGame();
        return;
    }

    snake.unshift(newHead);

    if (snakeX === food.x && snakeY === food.y) {
        score++;
        scoreElement.innerText = score;
        food = getRandomFoodPosition();
    } else {
        snake.pop();
    }

    draw();
}

function updateSpeedButtonStyles(selectedSpeed) {
    speedButtons.forEach(button => {
        const isSelected = Number(button.dataset.speed) === selectedSpeed;
        button.style.background = isSelected ? '#0f380f' : '#e4f2a1';
        button.style.color = isSelected ? '#9bbc0f' : '#0f380f';
    });
}

function showOverlay(mode) {
    overlay.style.display = 'flex';

    if (mode === 'start') {
        overlayTitle.innerText = 'Snake Garden';
        overlaySubtitle.innerText = 'Pick a speed and start.';
        overlayPlay.innerText = 'Play';
        hintElement.innerText = 'Use Arrow Keys or WASD to move.';
    }

    if (mode === 'gameover') {
        overlayTitle.innerText = 'Game Over';
        overlaySubtitle.innerText = 'Score: ' + score;
        overlayPlay.innerText = 'Play Again';
        hintElement.innerText = 'Press Play Again or Enter to restart.';
    }
}

function hideOverlay() {
    overlay.style.display = 'none';
}

function startGame() {
    if (game) {
        clearInterval(game);
    }

    resetGame();
    hideOverlay();
    isRunning = true;
    game = setInterval(gameLoop, gameSpeed);
}

function endGame() {
    clearInterval(game);
    game = null;
    isRunning = false;
    isGameOver = true;
    showOverlay('gameover');
}

function pauseOrResume() {
    if (isGameOver) {
        return;
    }

    if (!isRunning) {
        hideOverlay();
        isRunning = true;
        game = setInterval(gameLoop, gameSpeed);
        hintElement.innerText = 'Use Arrow Keys or WASD to move.';
        return;
    }

    clearInterval(game);
    game = null;
    isRunning = false;
    overlayTitle.innerText = 'Paused';
    overlaySubtitle.innerText = 'Press P to continue.';
    overlayPlay.innerText = 'Resume';
    overlay.style.display = 'flex';
    hintElement.innerText = 'Game paused.';
}

function setDirectionFromKey(key) {
    const map = {
        ArrowLeft: 'LEFT',
        ArrowUp: 'UP',
        ArrowRight: 'RIGHT',
        ArrowDown: 'DOWN',
        a: 'LEFT',
        w: 'UP',
        d: 'RIGHT',
        s: 'DOWN'
    };

    return map[key] || null;
}

function canTurnTo(newDirection) {
    if (newDirection === 'LEFT' && direction === 'RIGHT') return false;
    if (newDirection === 'RIGHT' && direction === 'LEFT') return false;
    if (newDirection === 'UP' && direction === 'DOWN') return false;
    if (newDirection === 'DOWN' && direction === 'UP') return false;
    return true;
}

document.addEventListener('keydown', event => {
    const key = event.key;
    const lowerKey = typeof key === 'string' ? key.toLowerCase() : '';

    if (['ArrowLeft', 'ArrowUp', 'ArrowRight', 'ArrowDown', 'w', 'a', 's', 'd', 'W', 'A', 'S', 'D', ' '].includes(key)) {
        event.preventDefault();
    }

    if (lowerKey === 'p') {
        pauseOrResume();
        return;
    }

    if (key === 'Enter' && (!isRunning || isGameOver)) {
        startGame();
        return;
    }

    const mappedDirection = setDirectionFromKey(key) || setDirectionFromKey(lowerKey);

    if (mappedDirection && isRunning && canTurnTo(mappedDirection)) {
        nextDirection = mappedDirection;
    }
});

speedButtons.forEach(button => {
    button.addEventListener('click', () => {
        gameSpeed = Number(button.dataset.speed);
        updateSpeedButtonStyles(gameSpeed);
    });
});

overlayPlay.addEventListener('click', () => {
    if (!isRunning || isGameOver || overlayTitle.innerText === 'Paused') {
        startGame();
    }
});

updateSpeedButtonStyles(gameSpeed);
showOverlay('start');
resetGame();
</script>