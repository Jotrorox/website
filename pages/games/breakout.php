<h2>Breakout</h2>
<a href="?page=games" class="back-link">&larr; Back to Games</a>

<div class="game-container" style="text-align: center; margin-top: 20px; max-width: 430px; margin-left: auto; margin-right: auto;">
    <div style="position: relative; width: 400px; margin: 0 auto;">
        <canvas id="breakoutCanvas" width="400" height="400" style="display:block; background-color: #f1d6b8; border: 4px solid #5f3a22; border-radius: 8px; image-rendering: pixelated; box-shadow: inset 0 0 0 2px #9b6a3a;"></canvas>
        <div id="breakoutOverlay" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 10px; background: rgba(67,39,23,0.72); color: #f8e4ce; border-radius: 8px; font-family: 'Pixelify Sans', sans-serif; padding: 20px;">
            <h3 id="overlayTitle" style="margin: 0; font-size: 1.9rem; color: #f8e4ce; letter-spacing: 1px;">Breakout</h3>
            <p id="overlaySubtitle" style="margin: 0; color: #f7d0a5; font-size: 1rem;">Press Play to start.</p>
            <button id="overlayPlay" style="font-family: 'Pixelify Sans', sans-serif; margin-top: 6px; font-size: 1.05rem; padding: 8px 16px; border: 2px solid #5f3a22; border-radius: 6px; background: #8d5128; color: #f8e4ce; cursor: pointer;">Play</button>
            <p style="margin: 2px 0 0; color: #f7d0a5; font-size: 0.85rem;">Move: Arrow Keys / A D · Pause: P</p>
        </div>
    </div>

    <p style="margin-top: 10px; font-family: 'Pixelify Sans', sans-serif; font-size: 1.5rem; color: var(--text-light);">Score: <span id="score">0</span> · Lives: <span id="lives">3</span></p>
    <p id="gameHint" style="font-size: 0.9rem; color: var(--text-light);">Break all bricks to win.</p>
</div>

<script>
const canvas = document.getElementById('breakoutCanvas');
const ctx = canvas.getContext('2d');
const overlay = document.getElementById('breakoutOverlay');
const overlayTitle = document.getElementById('overlayTitle');
const overlaySubtitle = document.getElementById('overlaySubtitle');
const overlayPlay = document.getElementById('overlayPlay');
const scoreElement = document.getElementById('score');
const livesElement = document.getElementById('lives');
const hintElement = document.getElementById('gameHint');

const COLORS = {
    background: '#f1d6b8',
    checker: 'rgba(95,58,34,0.07)',
    paddle: '#6f4324',
    paddleBorder: '#f7d0a5',
    ball: '#ffd9b0',
    ballBorder: '#5f3a22',
    brickA: '#d87b37',
    brickB: '#b5612f',
    brickC: '#8d5128',
    brickBorder: '#f8e4ce'
};

const paddle = {
    width: 76,
    height: 10,
    x: canvas.width / 2 - 38,
    y: canvas.height - 24,
    speed: 6,
    moveLeft: false,
    moveRight: false
};

const ball = {
    radius: 6,
    x: canvas.width / 2,
    y: canvas.height - 38,
    vx: 3.3,
    vy: -3.3
};

const bricksConfig = {
    rows: 5,
    cols: 8,
    width: 42,
    height: 14,
    padding: 6,
    offsetTop: 40,
    offsetLeft: 15
};

let bricks = [];
let score = 0;
let lives = 3;
let running = false;
let paused = false;
let gameOver = false;
let animationId = null;

function createBricks() {
    bricks = [];

    for (let row = 0; row < bricksConfig.rows; row++) {
        const line = [];
        for (let col = 0; col < bricksConfig.cols; col++) {
            line.push({
                x: bricksConfig.offsetLeft + col * (bricksConfig.width + bricksConfig.padding),
                y: bricksConfig.offsetTop + row * (bricksConfig.height + bricksConfig.padding),
                active: true
            });
        }
        bricks.push(line);
    }
}

function drawBackground() {
    ctx.fillStyle = COLORS.background;
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    const cell = 20;
    const xCells = canvas.width / cell;
    const yCells = canvas.height / cell;

    for (let y = 0; y < yCells; y++) {
        for (let x = 0; x < xCells; x++) {
            if ((x + y) % 2 === 0) {
                ctx.fillStyle = COLORS.checker;
                ctx.fillRect(x * cell, y * cell, cell, cell);
            }
        }
    }
}

function drawPaddle() {
    ctx.fillStyle = COLORS.paddle;
    ctx.fillRect(paddle.x, paddle.y, paddle.width, paddle.height);
    ctx.strokeStyle = COLORS.paddleBorder;
    ctx.strokeRect(paddle.x + 1, paddle.y + 1, paddle.width - 2, paddle.height - 2);
}

function drawBall() {
    ctx.fillStyle = COLORS.ball;
    ctx.beginPath();
    ctx.arc(ball.x, ball.y, ball.radius, 0, Math.PI * 2);
    ctx.fill();
    ctx.closePath();

    ctx.strokeStyle = COLORS.ballBorder;
    ctx.stroke();
}

function drawBricks() {
    for (let row = 0; row < bricks.length; row++) {
        for (let col = 0; col < bricks[row].length; col++) {
            const brick = bricks[row][col];
            if (!brick.active) {
                continue;
            }

            if (row % 3 === 0) {
                ctx.fillStyle = COLORS.brickA;
            } else if (row % 3 === 1) {
                ctx.fillStyle = COLORS.brickB;
            } else {
                ctx.fillStyle = COLORS.brickC;
            }
            ctx.fillRect(brick.x, brick.y, bricksConfig.width, bricksConfig.height);
            ctx.strokeStyle = COLORS.brickBorder;
            ctx.strokeRect(brick.x + 1, brick.y + 1, bricksConfig.width - 2, bricksConfig.height - 2);
        }
    }
}

function draw() {
    drawBackground();
    drawBricks();
    drawPaddle();
    drawBall();
}

function resetBallAndPaddle() {
    paddle.x = canvas.width / 2 - paddle.width / 2;
    ball.x = canvas.width / 2;
    ball.y = canvas.height - 38;
    ball.vx = (Math.random() > 0.5 ? 1 : -1) * 3.3;
    ball.vy = -3.3;
}

function showOverlay(mode) {
    overlay.style.display = 'flex';

    if (mode === 'start') {
        overlayTitle.innerText = 'Breakout';
        overlaySubtitle.innerText = 'Press Play to start.';
        overlayPlay.innerText = 'Play';
        hintElement.innerText = 'Break all bricks to win.';
    }

    if (mode === 'paused') {
        overlayTitle.innerText = 'Paused';
        overlaySubtitle.innerText = 'Press P to continue.';
        overlayPlay.innerText = 'Resume';
        hintElement.innerText = 'Game paused.';
    }

    if (mode === 'lose') {
        overlayTitle.innerText = 'Game Over';
        overlaySubtitle.innerText = 'Score: ' + score;
        overlayPlay.innerText = 'Play Again';
        hintElement.innerText = 'Press Play Again or Enter to restart.';
    }

    if (mode === 'win') {
        overlayTitle.innerText = 'You Win';
        overlaySubtitle.innerText = 'Score: ' + score;
        overlayPlay.innerText = 'Play Again';
        hintElement.innerText = 'All bricks cleared.';
    }
}

function hideOverlay() {
    overlay.style.display = 'none';
}

function resetGame() {
    score = 0;
    lives = 3;
    scoreElement.innerText = score;
    livesElement.innerText = lives;
    paused = false;
    gameOver = false;
    createBricks();
    resetBallAndPaddle();
    draw();
}

function startGame() {
    if (animationId) {
        cancelAnimationFrame(animationId);
        animationId = null;
    }

    resetGame();
    running = true;
    hideOverlay();
    animationId = requestAnimationFrame(gameLoop);
}

function stopWithState(mode) {
    running = false;
    paused = false;
    gameOver = mode === 'lose' || mode === 'win';

    if (animationId) {
        cancelAnimationFrame(animationId);
        animationId = null;
    }

    showOverlay(mode);
}

function pauseOrResume() {
    if (gameOver) {
        return;
    }

    if (!running) {
        running = true;
        paused = false;
        hideOverlay();
        hintElement.innerText = 'Break all bricks to win.';
        animationId = requestAnimationFrame(gameLoop);
        return;
    }

    running = false;
    paused = true;

    if (animationId) {
        cancelAnimationFrame(animationId);
        animationId = null;
    }

    showOverlay('paused');
}

function updatePaddle() {
    if (paddle.moveLeft) {
        paddle.x -= paddle.speed;
    }

    if (paddle.moveRight) {
        paddle.x += paddle.speed;
    }

    if (paddle.x < 0) {
        paddle.x = 0;
    }

    if (paddle.x + paddle.width > canvas.width) {
        paddle.x = canvas.width - paddle.width;
    }
}

function handleWallCollisions() {
    if (ball.x + ball.vx < ball.radius || ball.x + ball.vx > canvas.width - ball.radius) {
        ball.vx = -ball.vx;
    }

    if (ball.y + ball.vy < ball.radius) {
        ball.vy = -ball.vy;
    }
}

function handlePaddleCollision() {
    const intersectsPaddle =
        ball.y + ball.radius >= paddle.y &&
        ball.y + ball.radius <= paddle.y + paddle.height &&
        ball.x >= paddle.x &&
        ball.x <= paddle.x + paddle.width &&
        ball.vy > 0;

    if (!intersectsPaddle) {
        return;
    }

    const hitPoint = (ball.x - (paddle.x + paddle.width / 2)) / (paddle.width / 2);
    ball.vx = hitPoint * 4;
    ball.vy = -Math.abs(ball.vy);
}

function handleBrickCollisions() {
    for (let row = 0; row < bricks.length; row++) {
        for (let col = 0; col < bricks[row].length; col++) {
            const brick = bricks[row][col];
            if (!brick.active) {
                continue;
            }

            const withinX = ball.x + ball.radius >= brick.x && ball.x - ball.radius <= brick.x + bricksConfig.width;
            const withinY = ball.y + ball.radius >= brick.y && ball.y - ball.radius <= brick.y + bricksConfig.height;

            if (withinX && withinY) {
                brick.active = false;

                const centerX = brick.x + bricksConfig.width / 2;
                const centerY = brick.y + bricksConfig.height / 2;
                const dx = Math.abs(ball.x - centerX);
                const dy = Math.abs(ball.y - centerY);

                if (dx > dy) {
                    ball.vx = -ball.vx;
                } else {
                    ball.vy = -ball.vy;
                }

                score += 10;
                scoreElement.innerText = score;

                const speed = Math.sqrt(ball.vx * ball.vx + ball.vy * ball.vy);
                const targetSpeed = Math.min(6.2, speed + 0.03);
                const factor = targetSpeed / speed;
                ball.vx *= factor;
                ball.vy *= factor;

                if (score === bricksConfig.rows * bricksConfig.cols * 10) {
                    stopWithState('win');
                }

                return;
            }
        }
    }
}

function handleBottomCollision() {
    if (ball.y + ball.vy <= canvas.height - ball.radius) {
        return;
    }

    lives--;
    livesElement.innerText = lives;

    if (lives <= 0) {
        stopWithState('lose');
        return;
    }

    resetBallAndPaddle();
}

function gameLoop() {
    if (!running) {
        return;
    }

    updatePaddle();
    handleWallCollisions();
    handlePaddleCollision();
    handleBrickCollisions();
    handleBottomCollision();

    ball.x += ball.vx;
    ball.y += ball.vy;

    draw();

    if (running) {
        animationId = requestAnimationFrame(gameLoop);
    }
}

document.addEventListener('keydown', event => {
    const key = event.key;
    const lowerKey = typeof key === 'string' ? key.toLowerCase() : '';

    if (['ArrowLeft', 'ArrowRight', 'a', 'd', 'A', 'D', ' '].includes(key)) {
        event.preventDefault();
    }

    if (lowerKey === 'p') {
        pauseOrResume();
        return;
    }

    if (key === 'Enter' && (!running || gameOver)) {
        startGame();
        return;
    }

    if (key === 'ArrowLeft' || lowerKey === 'a') {
        paddle.moveLeft = true;
    }

    if (key === 'ArrowRight' || lowerKey === 'd') {
        paddle.moveRight = true;
    }
});

document.addEventListener('keyup', event => {
    const key = event.key;
    const lowerKey = typeof key === 'string' ? key.toLowerCase() : '';

    if (key === 'ArrowLeft' || lowerKey === 'a') {
        paddle.moveLeft = false;
    }

    if (key === 'ArrowRight' || lowerKey === 'd') {
        paddle.moveRight = false;
    }
});

overlayPlay.addEventListener('click', () => {
    if (!running || gameOver || paused) {
        if (paused && !gameOver) {
            pauseOrResume();
            return;
        }

        startGame();
    }
});

showOverlay('start');
resetGame();
</script>
