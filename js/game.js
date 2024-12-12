import { getSelectedCharacter } from './characters.js';
import { MoveEffectMap } from './moves.js';

// Get the canvas and context
const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

// Load background image
const background = new Image();
background.src = "img/background.png";

// Game state
let isPlayerTurn = true;
let gameOver = false;
let turnEffects = []; // Store active effects

// Create enemy (red triangle)
const enemy = {
    x: canvas.width - 200,
    y: canvas.height - 450,
    hp: 100,
    maxHp: 100,
    name: 'Enemy',
    poisoned: false,
    barrier: false,
    poisonDamage: 0,
    poisonTurns: 0,
    barrierStrength: 0
};

// Player character
let player = null;

// Load player character
async function loadPlayer() {
    player = await getSelectedCharacter();
    if (player) {
        player.x = 100;
        player.y = canvas.height - 150;
        player.radius = 50;
        player.currentHp = player.max_hp;
        player.poisoned = false;
        player.poisonDamage = 0;
        player.poisonTurns = 0;
        player.barrier = false;
        player.barrierStrength = 0;
        player.damageMultiplier = 1;
    }
}

// Handle player attack
function playerAttack(moveName) {
    if (!isPlayerTurn || gameOver) return;

    const move = player.moves.find(m => m.move_name.toLowerCase().replace(/\s/g, '') === moveName);
    if (move) {
        // Calculate base damage
        let damage = parseInt(move.base_damage);
        
        // Apply damage multiplier if it exists
        if (player.damageMultiplier) {
            damage = Math.floor(damage * player.damageMultiplier);
        }

        // Apply move effect if it exists
        const effect = MoveEffectMap[move.move_name];
        if (effect) {
            const result = effect(player, enemy, damage);
            damage = result.damage;

            // Add effect message to log if there is one
            if (result.message) {
                addToLog(result.message, result.buff ? 'player' : 'enemy');
            }
        }

        // Apply damage
        if (damage > 0) {
            // Check for enemy barrier
            if (enemy.barrier && enemy.barrierStrength > 0) {
                const absorbed = Math.min(enemy.barrierStrength, damage);
                damage -= absorbed;
                enemy.barrierStrength -= absorbed;
                addToLog(`Barrier absorbed ${absorbed} damage!`, 'enemy');
                if (enemy.barrierStrength <= 0) {
                    enemy.barrier = false;
                    addToLog('Enemy barrier broke!', 'player');
                }
            }

            enemy.hp -= damage;
            addToLog(`${player.name} used ${move.move_name}!`, 'player');
            addToLog(`Enemy took ${damage} damage!`, 'player');
        }

        // Show damage text
        if (damage > 0) {
            showDamageText(damage, enemy.x, enemy.y - 20);
        }

        // Check if enemy is defeated
        if (enemy.hp <= 0) {
            enemy.hp = 0;
            gameOver = true;
            addToLog('Enemy was defeated!', 'player');
        } else {
            // Switch turns
            isPlayerTurn = false;
            setTimeout(enemyTurn, 1000);
        }
    }
}

// Process turn effects
function processTurnEffects() {
    // Process poison
    if (enemy.poisoned) {
        const poisonDamage = enemy.poisonDamage;
        enemy.hp -= poisonDamage;
        addToLog(`Enemy took ${poisonDamage} poison damage!`, 'player');
        enemy.poisonTurns--;
        if (enemy.poisonTurns <= 0) {
            enemy.poisoned = false;
            addToLog('Poison wore off!', 'neutral');
        }
    }

    if (player.poisoned) {
        const poisonDamage = player.poisonDamage;
        player.currentHp -= poisonDamage;
        addToLog(`${player.name} took ${poisonDamage} poison damage!`, 'enemy');
        player.poisonTurns--;
        if (player.poisonTurns <= 0) {
            player.poisoned = false;
            addToLog('Poison wore off!', 'neutral');
        }
    }
}

// Enemy turn
function enemyTurn() {
    if (gameOver) return;

    // Process effects at start of turn
    processTurnEffects();

    // Simple enemy attack
    const damage = 10;
    
    // Check for player barrier
    let finalDamage = damage;
    if (player.barrier && player.barrierStrength > 0) {
        const absorbed = Math.min(player.barrierStrength, damage);
        finalDamage -= absorbed;
        player.barrierStrength -= absorbed;
        addToLog(`Barrier absorbed ${absorbed} damage!`, 'player');
        if (player.barrierStrength <= 0) {
            player.barrier = false;
            addToLog('Your barrier broke!', 'enemy');
        }
    }

    player.currentHp -= finalDamage;
    addToLog('Enemy attacks!', 'enemy');
    addToLog(`${player.name} took ${finalDamage} damage!`, 'enemy');

    // Show damage text
    showDamageText(finalDamage, player.x, player.y - 20);

    // Check if player is defeated
    if (player.currentHp <= 0) {
        player.currentHp = 0;
        gameOver = true;
        addToLog(`${player.name} was defeated!`, 'enemy');
    }

    // Switch turns back to player
    isPlayerTurn = true;
}

// Add message to chat log
function addToLog(message, turnType = 'neutral') {  // turnType can be 'player', 'enemy', or 'neutral'
    const logContainer = document.getElementById('log-container');
    const messageElement = document.createElement('div');
    messageElement.className = `log-message ${turnType}-turn`;
    messageElement.textContent = message;
    logContainer.appendChild(messageElement);
    // Auto scroll to bottom
    logContainer.scrollTop = logContainer.scrollHeight;
}

// Show damage text
function showDamageText(damage, x, y) {
    ctx.fillStyle = 'white';
    ctx.font = '20px Arial';
    ctx.fillText(damage, x, y);
}

// Draw health bars
function drawHealthBar(x, y, currentHp, maxHp, width = 100) {
    const height = 10;
    const healthPercentage = currentHp / maxHp;

    // Background with outline
    ctx.strokeStyle = 'black';
    ctx.lineWidth = 2;
    ctx.fillStyle = '#333';
    ctx.fillRect(x - width/2, y, width, height);
    ctx.strokeRect(x - width/2, y, width, height);

    // Health with outline
    ctx.fillStyle = healthPercentage > 0.5 ? 'green' : healthPercentage > 0.25 ? 'yellow' : 'red';
    ctx.fillRect(x - width/2, y, width * healthPercentage, height);
    ctx.strokeRect(x - width/2, y, width * healthPercentage, height);
}

// Draw everything
function draw() {
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Draw background
    if (background.complete) {
        ctx.drawImage(background, 0, 0, canvas.width, canvas.height);
    }

    // Draw player if loaded
    if (player) {
        // Player circle with outline
        ctx.strokeStyle = 'black';
        ctx.lineWidth = 3;
        ctx.fillStyle = player.character_color;
        ctx.beginPath();
        ctx.arc(player.x, player.y, player.radius, 0, Math.PI * 2);
        ctx.fill();
        ctx.stroke();
        
        // Draw player health bar
        drawHealthBar(player.x, player.y - 70, player.currentHp, player.max_hp);
    }

    // Draw enemy triangle with outline
    ctx.strokeStyle = 'black';
    ctx.lineWidth = 3;
    ctx.fillStyle = 'red';
    ctx.beginPath();
    ctx.moveTo(enemy.x, enemy.y);
    ctx.lineTo(enemy.x + 50, enemy.y + 80);
    ctx.lineTo(enemy.x - 50, enemy.y + 80);
    ctx.closePath();
    ctx.fill();
    ctx.stroke();

    // Draw enemy health bar
    drawHealthBar(enemy.x, enemy.y - 20, enemy.hp, enemy.maxHp);

    // Draw turn indicator
    ctx.fillStyle = 'white';
    ctx.font = '24px Arial';
    ctx.fillText(isPlayerTurn ? 'Your Turn' : 'Enemy Turn', canvas.width/2 - 50, 30);

    // Draw game over
    if (gameOver) {
        ctx.fillStyle = 'white';
        ctx.font = '48px Arial';
        ctx.fillText(player.currentHp > 0 ? 'You Win!' : 'Game Over', canvas.width/2 - 100, canvas.height/2);
    }
}

// Animation loop
function gameLoop() {
    draw();
    requestAnimationFrame(gameLoop);
}

// Make playerAttack available globally
window.playerAttack = playerAttack;

// Start the game when everything is loaded
window.addEventListener('load', async () => {
    await loadPlayer();
    gameLoop();
});