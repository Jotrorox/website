<h2>Tetris</h2>
<a href="?page=games" class="back-link">&larr; Back to Games</a>

<style>
    .tetris-layout {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        gap: 14px;
        margin-top: 20px;
        width: 100%;
    }

    .tetris-board-wrap {
        position: relative;
        width: min(300px, 100%);
        flex: 1 1 300px;
        max-width: 300px;
    }

    .tetris-canvas {
        display: block;
        width: 100%;
        height: auto;
        background-color: #9bbc0f;
        border: 4px solid var(--wood-dark);
        border-radius: 8px;
        image-rendering: pixelated;
        box-shadow: inset 0 0 0 2px #0f380f;
    }

    .tetris-side {
        width: 120px;
        border: 3px solid var(--wood-dark);
        border-radius: 8px;
        background: rgba(15, 56, 15, 0.09);
        padding: 8px;
        text-align: center;
    }

    .tetris-side h4 {
        margin: 0;
        font-family: 'Pixelify Sans', sans-serif;
        font-size: 1.1rem;
        color: var(--text-light);
    }

    .tetris-next-canvas {
        display: block;
        margin: 8px auto 0;
        width: 96px;
        height: 96px;
        background: #9bbc0f;
        border: 3px solid #0f380f;
        border-radius: 6px;
        image-rendering: pixelated;
    }

    .tetris-side p {
        margin: 8px 0 0;
        font-size: 0.85rem;
        color: var(--text-light);
    }

    @media (max-width: 560px) {
        .tetris-layout {
            flex-direction: column;
            align-items: center;
        }

        .tetris-side {
            width: min(300px, 100%);
            box-sizing: border-box;
        }
    }
</style>

<div class="game-container" style="text-align: center; margin-top: 20px; max-width: 760px; margin-left: auto; margin-right: auto;">
    <div class="tetris-layout">
        <div class="tetris-board-wrap">
        <canvas id="tetrisCanvas" class="tetris-canvas" width="300" height="400"></canvas>
        <div id="tetrisOverlay" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 10px; background: rgba(15,56,15,0.72); color: #9bbc0f; border-radius: 8px; font-family: 'Pixelify Sans', sans-serif; padding: 20px;">
            <h3 id="overlayTitle" style="margin: 0; font-size: 1.9rem; color: #9bbc0f; letter-spacing: 1px;">Tetris</h3>
            <p id="overlaySubtitle" style="margin: 0; color: #e4f2a1; font-size: 1rem;">Press Play to start.</p>
            <button id="overlayPlay" style="font-family: 'Pixelify Sans', sans-serif; margin-top: 6px; font-size: 1.05rem; padding: 8px 16px; border: 2px solid var(--wood-dark); border-radius: 6px; background: var(--wood-dark); color: #9bbc0f; cursor: pointer;">Play</button>
            <p style="margin: 2px 0 0; color: #e4f2a1; font-size: 0.85rem;">Move: Arrow Keys / A D · Rotate: W / Arrow Up · Soft Drop: S / Arrow Down · Hard Drop: Space · Pause: P</p>
        </div>
        </div>
        <div class="tetris-side">
            <h4>Next</h4>
            <canvas id="nextPieceCanvas" class="tetris-next-canvas" width="96" height="96"></canvas>
            <p>Space: Hard drop</p>
            <p>Hold Left/Right supported</p>
        </div>
    </div>

    <p style="margin-top: 10px; font-family: 'Pixelify Sans', sans-serif; font-size: 1.5rem; color: var(--text-light);">Score: <span id="score">0</span></p>
    <p id="gameHint" style="font-size: 0.9rem; color: var(--text-light);">Clear lines to score points.</p>
</div>

<script>
const canvas = document.getElementById('tetrisCanvas');
const ctx = canvas.getContext('2d');
const nextCanvas = document.getElementById('nextPieceCanvas');
const nextCtx = nextCanvas.getContext('2d');
const overlay = document.getElementById('tetrisOverlay');
const overlayTitle = document.getElementById('overlayTitle');
const overlaySubtitle = document.getElementById('overlaySubtitle');
const overlayPlay = document.getElementById('overlayPlay');
const scoreElement = document.getElementById('score');
const hintElement = document.getElementById('gameHint');

const COLS = 10;
const ROWS = 20;
const SIZE = 20;
const EMPTY = 0;

const COLORS = [
    null,
    '#0f380f',
    '#306230',
    '#8bac0f',
    '#1f4f1f',
    '#5f7f1f',
    '#7f5f1f',
    '#8b1c1c'
];

const SHAPES = [
    [[1, 1, 1, 1]],
    [[2, 0, 0], [2, 2, 2]],
    [[0, 0, 3], [3, 3, 3]],
    [[4, 4], [4, 4]],
    [[0, 5, 5], [5, 5, 0]],
    [[0, 6, 0], [6, 6, 6]],
    [[7, 7, 0], [0, 7, 7]]
];

let board = [];
let currentPiece = null;
let nextPiece = null;
let currentX = 0;
let currentY = 0;
let score = 0;
let timer = null;
let dropDelay = 450;
let isRunning = false;
let isGameOver = false;
let holdLeft = false;
let holdRight = false;
let preferredHoldDirection = 0;
let holdTimeout = null;
let holdInterval = null;

function createEmptyBoard() {
    return Array.from({ length: ROWS }, () => Array(COLS).fill(EMPTY));
}

function cloneShape(shape) {
    return shape.map(row => [...row]);
}

function rotateShape(shape) {
    const rows = shape.length;
    const cols = shape[0].length;
    const rotated = Array.from({ length: cols }, () => Array(rows).fill(EMPTY));

    for (let y = 0; y < rows; y++) {
        for (let x = 0; x < cols; x++) {
            rotated[x][rows - 1 - y] = shape[y][x];
        }
    }

    return rotated;
}

function randomShape() {
    const shape = SHAPES[Math.floor(Math.random() * SHAPES.length)];
    return cloneShape(shape);
}

function spawnPiece() {
    if (!nextPiece) {
        nextPiece = randomShape();
    }

    currentPiece = cloneShape(nextPiece);
    nextPiece = randomShape();
    currentY = 0;
    currentX = Math.floor((COLS - currentPiece[0].length) / 2);
    drawNextPiece();

    if (!canMove(currentX, currentY, currentPiece)) {
        endGame();
    }
}

function canMove(targetX, targetY, shape) {
    for (let y = 0; y < shape.length; y++) {
        for (let x = 0; x < shape[y].length; x++) {
            if (shape[y][x] === EMPTY) {
                continue;
            }

            const boardX = targetX + x;
            const boardY = targetY + y;

            if (boardX < 0 || boardX >= COLS || boardY >= ROWS) {
                return false;
            }

            if (boardY >= 0 && board[boardY][boardX] !== EMPTY) {
                return false;
            }
        }
    }

    return true;
}

function mergePiece() {
    for (let y = 0; y < currentPiece.length; y++) {
        for (let x = 0; x < currentPiece[y].length; x++) {
            if (currentPiece[y][x] !== EMPTY) {
                const boardY = currentY + y;
                const boardX = currentX + x;

                if (boardY >= 0) {
                    board[boardY][boardX] = currentPiece[y][x];
                }
            }
        }
    }
}

function clearLines() {
    let cleared = 0;

    for (let y = ROWS - 1; y >= 0; y--) {
        if (board[y].every(cell => cell !== EMPTY)) {
            board.splice(y, 1);
            board.unshift(Array(COLS).fill(EMPTY));
            cleared++;
            y++;
        }
    }

    if (cleared > 0) {
        score += cleared * 100;
        scoreElement.innerText = score;

        if (dropDelay > 160) {
            dropDelay = Math.max(160, dropDelay - cleared * 10);
            restartTimer();
        }
    }
}

function drawBackground() {
    ctx.fillStyle = '#9bbc0f';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    for (let y = 0; y < ROWS; y++) {
        for (let x = 0; x < COLS; x++) {
            if ((x + y) % 2 === 0) {
                ctx.fillStyle = 'rgba(15,56,15,0.06)';
                ctx.fillRect(x * SIZE, y * SIZE, SIZE, SIZE);
            }
        }
    }
}

function drawCell(x, y, value) {
    ctx.fillStyle = COLORS[value];
    ctx.fillRect(x * SIZE, y * SIZE, SIZE, SIZE);
    ctx.strokeStyle = '#9bbc0f';
    ctx.strokeRect(x * SIZE + 1, y * SIZE + 1, SIZE - 2, SIZE - 2);
}

function drawBoard() {
    for (let y = 0; y < ROWS; y++) {
        for (let x = 0; x < COLS; x++) {
            const value = board[y][x];
            if (value !== EMPTY) {
                drawCell(x, y, value);
            }
        }
    }
}

function drawPiece() {
    for (let y = 0; y < currentPiece.length; y++) {
        for (let x = 0; x < currentPiece[y].length; x++) {
            const value = currentPiece[y][x];
            if (value !== EMPTY) {
                drawCell(currentX + x, currentY + y, value);
            }
        }
    }
}

function draw() {
    drawBackground();
    drawBoard();
    if (currentPiece) {
        drawPiece();
    }
}

function drawNextPiece() {
    nextCtx.fillStyle = '#9bbc0f';
    nextCtx.fillRect(0, 0, nextCanvas.width, nextCanvas.height);

    for (let y = 0; y < 6; y++) {
        for (let x = 0; x < 6; x++) {
            if ((x + y) % 2 === 0) {
                nextCtx.fillStyle = 'rgba(15,56,15,0.06)';
                nextCtx.fillRect(x * 16, y * 16, 16, 16);
            }
        }
    }

    if (!nextPiece) {
        return;
    }

    const blockSize = 16;
    const shapeHeight = nextPiece.length;
    const shapeWidth = nextPiece[0].length;
    const offsetX = Math.floor((nextCanvas.width - shapeWidth * blockSize) / 2);
    const offsetY = Math.floor((nextCanvas.height - shapeHeight * blockSize) / 2);

    for (let y = 0; y < shapeHeight; y++) {
        for (let x = 0; x < shapeWidth; x++) {
            const value = nextPiece[y][x];
            if (value !== EMPTY) {
                const px = offsetX + x * blockSize;
                const py = offsetY + y * blockSize;
                nextCtx.fillStyle = COLORS[value];
                nextCtx.fillRect(px, py, blockSize, blockSize);
                nextCtx.strokeStyle = '#9bbc0f';
                nextCtx.strokeRect(px + 1, py + 1, blockSize - 2, blockSize - 2);
            }
        }
    }
}

function stepDown() {
    if (!isRunning || isGameOver) {
        return;
    }

    if (canMove(currentX, currentY + 1, currentPiece)) {
        currentY++;
    } else {
        mergePiece();
        clearLines();
        spawnPiece();
    }

    draw();
}

function hardDrop() {
    if (!isRunning || isGameOver) {
        return;
    }

    while (canMove(currentX, currentY + 1, currentPiece)) {
        currentY++;
    }

    mergePiece();
    clearLines();
    spawnPiece();
    draw();
}

function clearHoldTimers() {
    if (holdTimeout) {
        clearTimeout(holdTimeout);
        holdTimeout = null;
    }

    if (holdInterval) {
        clearInterval(holdInterval);
        holdInterval = null;
    }
}

function resolveHoldDirection() {
    if (holdLeft && holdRight) {
        return preferredHoldDirection;
    }

    if (holdLeft) {
        return -1;
    }

    if (holdRight) {
        return 1;
    }

    return 0;
}

function refreshHoldMovement(immediateMove) {
    clearHoldTimers();

    const direction = resolveHoldDirection();

    if (!isRunning || isGameOver || direction === 0) {
        return;
    }

    if (immediateMove) {
        moveHorizontal(direction);
    }

    holdTimeout = setTimeout(() => {
        holdInterval = setInterval(() => {
            const loopDirection = resolveHoldDirection();

            if (!isRunning || isGameOver || loopDirection === 0) {
                clearHoldTimers();
                return;
            }

            moveHorizontal(loopDirection);
        }, 55);
    }, 140);
}

function resetHoldState() {
    holdLeft = false;
    holdRight = false;
    preferredHoldDirection = 0;
    clearHoldTimers();
}

function restartTimer() {
    if (!isRunning) {
        return;
    }

    if (timer) {
        clearInterval(timer);
    }

    timer = setInterval(stepDown, dropDelay);
}

function showOverlay(mode) {
    overlay.style.display = 'flex';

    if (mode === 'start') {
        overlayTitle.innerText = 'Tetris';
        overlaySubtitle.innerText = 'Press Play to start.';
        overlayPlay.innerText = 'Play';
        hintElement.innerText = 'Clear lines to score points.';
    }

    if (mode === 'gameover') {
        overlayTitle.innerText = 'Game Over';
        overlaySubtitle.innerText = 'Score: ' + score;
        overlayPlay.innerText = 'Play Again';
        hintElement.innerText = 'Press Play Again or Enter to restart.';
    }

    if (mode === 'paused') {
        overlayTitle.innerText = 'Paused';
        overlaySubtitle.innerText = 'Press P to continue.';
        overlayPlay.innerText = 'Resume';
        hintElement.innerText = 'Game paused.';
    }
}

function hideOverlay() {
    overlay.style.display = 'none';
}

function resetGame() {
    board = createEmptyBoard();
    score = 0;
    scoreElement.innerText = score;
    dropDelay = 450;
    isGameOver = false;
    nextPiece = randomShape();
    resetHoldState();
    spawnPiece();
    draw();
}

function startGame() {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }

    resetGame();
    hideOverlay();
    isRunning = true;
    restartTimer();
}

function endGame() {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }

    isRunning = false;
    isGameOver = true;
    resetHoldState();
    showOverlay('gameover');
}

function pauseOrResume() {
    if (isGameOver) {
        return;
    }

    if (!isRunning) {
        hideOverlay();
        isRunning = true;
        restartTimer();
        hintElement.innerText = 'Clear lines to score points.';
        return;
    }

    if (timer) {
        clearInterval(timer);
        timer = null;
    }

    isRunning = false;
    resetHoldState();
    showOverlay('paused');
}

function moveHorizontal(direction) {
    const targetX = currentX + direction;
    if (canMove(targetX, currentY, currentPiece)) {
        currentX = targetX;
        draw();
    }
}

function rotateCurrent() {
    const rotated = rotateShape(currentPiece);

    if (canMove(currentX, currentY, rotated)) {
        currentPiece = rotated;
        draw();
        return;
    }

    if (canMove(currentX - 1, currentY, rotated)) {
        currentX--;
        currentPiece = rotated;
        draw();
        return;
    }

    if (canMove(currentX + 1, currentY, rotated)) {
        currentX++;
        currentPiece = rotated;
        draw();
    }
}

document.addEventListener('keydown', event => {
    const key = event.key;
    const lowerKey = typeof key === 'string' ? key.toLowerCase() : '';

    if (['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'a', 'd', 'w', 's', 'A', 'D', 'W', 'S', ' '].includes(key)) {
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

    if (!isRunning || isGameOver) {
        return;
    }

    if (key === 'ArrowLeft' || lowerKey === 'a') {
        holdLeft = true;
        preferredHoldDirection = -1;

        if (!event.repeat) {
            refreshHoldMovement(true);
        }
    }

    if (key === 'ArrowRight' || lowerKey === 'd') {
        holdRight = true;
        preferredHoldDirection = 1;

        if (!event.repeat) {
            refreshHoldMovement(true);
        }
    }

    if ((key === 'ArrowUp' || lowerKey === 'w') && !event.repeat) {
        rotateCurrent();
    }

    if (key === 'ArrowDown' || lowerKey === 's') {
        stepDown();
    }

    if (key === ' ' && !event.repeat) {
        hardDrop();
    }
});

document.addEventListener('keyup', event => {
    const key = event.key;
    const lowerKey = typeof key === 'string' ? key.toLowerCase() : '';

    if (key === 'ArrowLeft' || lowerKey === 'a') {
        holdLeft = false;
        refreshHoldMovement(false);
    }

    if (key === 'ArrowRight' || lowerKey === 'd') {
        holdRight = false;
        refreshHoldMovement(false);
    }
});

overlayPlay.addEventListener('click', () => {
    if (overlayTitle.innerText === 'Paused' && !isGameOver) {
        pauseOrResume();
        return;
    }

    if (!isRunning || isGameOver) {
        startGame();
    }
});

showOverlay('start');
resetGame();
</script>
