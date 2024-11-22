// Importing attacks from the module
import attacks from './attacks.js';

// Default background image setup
const backgroundImage = new Image();
backgroundImage.src = 'background.png';

// Attach functions to window to make them globally accessible (needed for button onclick handlers in HTML)
window.startGame = startGame;
window.playerAttack = playerAttack;

// DOM element references for UI sections
const homeScreen = document.querySelector('.home-screen');
const gameScreen = document.querySelector('.game-screen');
const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

// Game state variables
let playerTurn = true;
let playerHP = 100;
let enemyHP = 100;
let isPlayerActionAllowed = true;
let playerCritChance = 0.15; // Base crit chance
let playerAccuracy = 0.85; // Base accuracy (1 - miss chance)
let focusBuffTurns = 0; // Number of turns left for Focus buff

// Smooth health bar variables (for health bar animations)
let smoothPlayerHP = 100;
let smoothEnemyHP = 100;

// Damage indicator object (used for showing damage numbers on the screen)
let damageIndicator = {
  x: 100,
  y: 400,
  value: null,
  visible: false,
  color: 'black'
};

// Initialize game after DOM content is fully loaded
// Shows the home screen initially
document.addEventListener('DOMContentLoaded', () => {
  initializeGame();
});

// Function to initialize the game UI
function initializeGame() {
  homeScreen.style.display = 'block';
  gameScreen.style.display = 'none';
  // Draw the background once it loads
  backgroundImage.onload = function () {
    ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
  };
}

// Function to start the game when 'Start Game' button is pressed
function startGame() {
  homeScreen.style.display = 'none';
  gameScreen.style.display = 'block';
  ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
  updateUI(); // Update the UI once the game starts
}

// Function to handle player attacks
function playerAttack(ability) {
  if (!isPlayerActionAllowed) return; // Prevent action if player is not allowed to attack

  const attack = attacks[ability];
  if (!attack) {
    console.error(`Attack ${ability} not found`);
    return;
  }

  // Handle Focus Buff (non-damage action)
  if (ability === 'focus') {
    addToChatLog('Player used Focus! Increased crit chance and accuracy for 2 turns.', 'player');
    focusBuffTurns = 2;
    playerCritChance = 0.3; // Double the base crit chance
    playerAccuracy = 0.95; // Increase accuracy
    playerTurn = false; // End player's turn
    updateTurn();
    return;
  }

  // Check if attack misses
  if (Math.random() > playerAccuracy) {
    addToChatLog(`Player's ${attack.name} missed!`, 'player');
    addToChatLog(`Enemy took 0 damage!`, 'player');
    showDamage(600, 100, 'Miss...', attack.name, 'gray');
    playerTurn = false;
    updateTurn();
    return;
  }

  // Calculate if attack is a critical hit
  let isCritical = Math.random() < playerCritChance;
  let damage = attack.calculateDamage();
  if (isCritical) {
    damage = Math.floor(damage * 1.5);
    addToChatLog(`Critical Hit! Player used ${attack.name}!`, 'player');
    addToChatLog(`Enemy took ${damage} damage!`, 'player');
    showDamage(600, 100, `-${damage}!`, attack.name, 'orange');
  } else {
    addToChatLog(`Player used ${attack.name}!`, 'player');
    addToChatLog(`Enemy took ${damage} damage!`, 'player');
    showDamage(600, 100, `-${damage}`, attack.name);
  }

  // Reduce enemy health
  enemyHP -= damage;
  if (enemyHP < 0) enemyHP = 0;

  playerTurn = false; // End player's turn
  animateHealthBar(); // Animate health bar to reflect changes
  updateTurn(); // Update turn (switch to enemy)
}

// Function to handle enemy attacks
function enemyAttack() {
  let damage = Math.floor(Math.random() * 10) + 5;
  addToChatLog(`Enemy attacked and dealt ${damage} damage!`, 'enemy');

  // Reduce player health
  playerHP -= damage;
  if (playerHP < 0) playerHP = 0;

  // Show damage on the screen
  showDamage(100, 400, `-${damage}`);
  animateHealthBar(); // Animate health bar to reflect changes
  updateUI(); // Update UI

  // Decrease Focus Buff duration if active
  if (focusBuffTurns > 0) {
    focusBuffTurns--;
    if (focusBuffTurns === 0) {
      playerCritChance = 0.15; // Reset to base crit chance
      playerAccuracy = 0.85; // Reset to base accuracy
    }
  }
}

// Function to update the turn logic
function updateTurn() {
  if (playerHP <= 0 || enemyHP <= 0) {
    checkWinCondition(); // Check if the game is over
    return;
  }

  if (playerTurn) {
    isPlayerActionAllowed = true; // Player's turn
  } else {
    isPlayerActionAllowed = false;
    setTimeout(() => {
      enemyAttack();
      playerTurn = true;
      isPlayerActionAllowed = true;
      updateUI();
    }, 1000); // Enemy attacks after a delay
  }
}

// Function to show damage indicators on the screen
function showDamage(x, y, value, attackName = '', color = 'black') {
  damageIndicator.x = x;
  damageIndicator.y = y;
  damageIndicator.value = attackName ? `${attackName}: ${value}` : value;
  damageIndicator.visible = true;
  damageIndicator.color = color;

  // Animate damage moving upwards
  let animationInterval = setInterval(() => {
    damageIndicator.y -= 2;
    if (damageIndicator.y < y - 50) {
      damageIndicator.visible = false;
      clearInterval(animationInterval);
    }
    updateUI();
  }, 10);
}

// Function to update the UI elements on the canvas
function updateUI() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height); // Redraw background

  // Draw player and enemy health bars
  ctx.fillStyle = 'blue';
  ctx.fillRect(100, 380, 100, 100);
  ctx.fillStyle = 'blue';
  ctx.fillRect(50, 350, (smoothPlayerHP / 100) * 200, 20);
  ctx.strokeStyle = 'black';
  ctx.strokeRect(50, 350, 200, 20);
  ctx.fillStyle = 'black';
  ctx.font = '14px Arial';
  ctx.fillText(`${Math.round(smoothPlayerHP)}/100`, 150, 345);

  ctx.fillStyle = 'red';
  ctx.fillRect(600, 100, 100, 100);
  ctx.fillStyle = 'red';
  ctx.fillRect(550, 70, (smoothEnemyHP / 100) * 200, 20);
  ctx.strokeStyle = 'black';
  ctx.strokeRect(550, 70, 200, 20);
  ctx.fillStyle = 'black';
  ctx.font = '14px Arial';
  ctx.fillText(`${Math.round(smoothEnemyHP)}/100`, 650, 65);

  // Display damage indicator if visible
  if (damageIndicator.visible) {
    ctx.fillStyle = damageIndicator.color || 'black';
    ctx.font = '20px Arial';
    ctx.fillText(damageIndicator.value, damageIndicator.x + 30, damageIndicator.y);
  }

  // Display win message if game is over
  if (playerHP <= 0 || enemyHP <= 0) {
    ctx.fillStyle = 'black';
    ctx.font = '40px Arial';
    ctx.textAlign = 'center';
    ctx.fillText(playerHP <= 0 ? "Red Wins!" : "Blue Wins!", canvas.width / 2, canvas.height / 2);
  }
}

// Function to animate the health bar smoothly
function animateHealthBar() {
  let playerDiff = playerHP - smoothPlayerHP;
  let enemyDiff = enemyHP - smoothEnemyHP;

  // Smoothly update health bars
  if (Math.abs(playerDiff) > 0.1) smoothPlayerHP += playerDiff * 0.1;
  else smoothPlayerHP = playerHP;

  if (Math.abs(enemyDiff) > 0.1) smoothEnemyHP += enemyDiff * 0.1;
  else smoothEnemyHP = enemyHP;

  updateUI();

  if (Math.abs(playerDiff) > 0.1 || Math.abs(enemyDiff) > 0.1) {
    requestAnimationFrame(animateHealthBar);
  }
}

// Function to check win conditions
function checkWinCondition() {
  if (playerHP <= 0 || enemyHP <= 0) {
    updateUI(); // Ensure the final UI is shown
  }
} 

// Function to reset the game state
function resetGame() {
  playerHP = 100;
  enemyHP = 100;
  playerTurn = true;
  isPlayerActionAllowed = true;
  homeScreen.style.display = 'block';
  gameScreen.style.display = 'none';
}

// Function to add messages to the chat log
function addToChatLog(message, type = 'player') {
  const logContainer = document.getElementById('log-container');
  const logMessage = document.createElement('div');
  logMessage.className = `log-message log-${type}`;
  logMessage.textContent = message;

  logContainer.appendChild(logMessage);
  logContainer.scrollTop = logContainer.scrollHeight; // Auto-scroll to the latest message
}
