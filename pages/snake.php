<h2>Snake</h2>
<a href="?page=games" class="back-link">&larr; Back to Games</a>

<div class="game-container" style="text-align: center; margin-top: 20px;">
    <canvas id="snakeCanvas" width="400" height="400" style="background-color: #9bbc0f; border: 4px solid var(--wood-dark); border-radius: 8px; box-shadow: inset 0 0 10px rgba(0,0,0,0.2);"></canvas>
    <p style="margin-top: 10px; font-family: 'Pixelify Sans', sans-serif; font-size: 1.5rem;">Score: <span id="score">0</span></p>
    <p style="font-size: 0.9rem; color: var(--text-light);">Use Arrow Keys to move.</p>
</div>

<script>
const canvas = document.getElementById('snakeCanvas');
const ctx = canvas.getContext('2d');
const box = 20;
let score = 0;
let snake = [];
snake[0] = { x: 9 * box, y: 10 * box };
let food = {
    x: Math.floor(Math.random() * 19 + 1) * box,
    y: Math.floor(Math.random() * 19 + 1) * box
};
let d;

document.addEventListener('keydown', direction);

function direction(event) {
    if (event.keyCode == 37 && d != 'RIGHT') d = 'LEFT';
    else if (event.keyCode == 38 && d != 'DOWN') d = 'UP';
    else if (event.keyCode == 39 && d != 'LEFT') d = 'RIGHT';
    else if (event.keyCode == 40 && d != 'UP') d = 'DOWN';
    
    // Prevent default scrolling for arrow keys
    if([37, 38, 39, 40].indexOf(event.keyCode) > -1) {
        event.preventDefault();
    }
}

function collision(head, array) {
    for (let i = 0; i < array.length; i++) {
        if (head.x == array[i].x && head.y == array[i].y) return true;
    }
    return false;
}

function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    for (let i = 0; i < snake.length; i++) {
        ctx.fillStyle = (i == 0) ? '#306230' : '#0f380f';
        ctx.fillRect(snake[i].x, snake[i].y, box, box);
        ctx.strokeStyle = '#9bbc0f';
        ctx.strokeRect(snake[i].x, snake[i].y, box, box);
    }

    ctx.fillStyle = '#8b1c1c';
    ctx.fillRect(food.x, food.y, box, box);

    let snakeX = snake[0].x;
    let snakeY = snake[0].y;

    if (d == 'LEFT') snakeX -= box;
    if (d == 'UP') snakeY -= box;
    if (d == 'RIGHT') snakeX += box;
    if (d == 'DOWN') snakeY += box;

    if (snakeX == food.x && snakeY == food.y) {
        score++;
        document.getElementById('score').innerText = score;
        food = {
            x: Math.floor(Math.random() * 19 + 1) * box,
            y: Math.floor(Math.random() * 19 + 1) * box
        };
    } else {
        snake.pop();
    }

    let newHead = { x: snakeX, y: snakeY };

    if (snakeX < 0 || snakeX >= canvas.width || snakeY < 0 || snakeY >= canvas.height || collision(newHead, snake)) {
        clearInterval(game);
        ctx.fillStyle = 'rgba(0,0,0,0.5)';
        ctx.fillRect(0,0,canvas.width,canvas.height);
        ctx.fillStyle = 'white';
        ctx.font = '30px "Pixelify Sans"';
        ctx.textAlign = 'center';
        ctx.fillText('Game Over', canvas.width/2, canvas.height/2);
        ctx.font = '20px "Pixelify Sans"';
        ctx.fillText('Press F5 to restart', canvas.width/2, canvas.height/2 + 30);
        return;
    }

    snake.unshift(newHead);
}

let game = setInterval(draw, 100);
</script>