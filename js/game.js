//Import the get character function and the move effects
//Importamos la funcion para obtener el personaje elegido y los ataques
import { getSelectedCharacter } from './characters.js';
import { MoveEffectMap } from './moves.js';

// Get the canvas and context
// Obtenemos canvas y contexto
const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

// Load background image
// Cargamos imagen de fondo
const background = new Image();
background.src = "img/background.png";

// Game state
// Estados de partida
let isPlayerTurn = true;
let gameOver = false;

// Game stats
// Estadisticas
let totalDamageDealt = 0;
let highestHit = 0;

// Create enemy (red triangle)
// Creación enemigo (triangulo rojo)
const enemy = {
    x: canvas.width - 200,
    y: canvas.height - 450,
    hp: 150,
    maxHp: 150,
    name: 'Enemy',
    poisoned: false,
    barrier: false,
    poisonDamage: 0,
    poisonTurns: 0,
    barrierStrength: 0,
    displayedHp: 0
};

// Player character array
// Definimos el array del jugador
let player = null;

// Load player
// Cargar jugador
// Función asincrona ya que no queremos que pause el contenido
async function loadPlayer() {
    //await ya que es asincrona
    //await since its asynchronous
    player = await getSelectedCharacter();
    //si se ha encontrado el jugador
    //if player is found
    if (player) {
        //player info
        //info del jugador
        player.x = 100;
        player.y = canvas.height - 150;
        player.radius = 50;
        player.currentHp = player.max_hp;
        player.displayedHp = player.max_hp;
        player.poisoned = false;
        player.poisonDamage = 0;
        player.poisonTurns = 0;
        player.barrier = false;
        player.barrierStrength = 0;
        player.damageMultiplier = 1;
    }
}

// Player attack
// Ataque jugador
function playerAttack(moveName) {
    // si no es el turno del jugador o si se ha acabado la partida
    // if its not the player turn or the game is over
    if (!isPlayerTurn || gameOver) {
        return;
    };

    const move = player.moves.find(m => m.move_name.toLowerCase().replace(/\s/g, '') === moveName);
    if (move) {
        // base damage
        // daño base
        let damage = parseInt(move.base_damage);
        
        // check for damage multiplier
        // comprobamos multiplicador de daño
        if (player.damageMultiplier) {
            damage = Math.floor(damage * player.damageMultiplier);
        }

        // check for move effect
        // comprobamos si hay efecto especial
        const effect = MoveEffectMap[move.move_name];
        if (effect) {
            const result = effect(player, enemy, damage);
            damage = result.damage;

            // show effect message in log
            // mostramos mensaje de efecto en el log
            if (result.message) {
                addToLog(result.message, result.buff ? 'player' : 'enemy');
            }
        }

        // apply the damage
        // aplicamos el daño
        if (damage > 0) {
            // check enemy barrier
            // miramos si el enemigo tiene barrera
            if (enemy.barrier && enemy.barrierStrength > 0) {
                //mates
                const absorbed = Math.min(enemy.barrierStrength, damage);
                damage -= absorbed;
                enemy.barrierStrength -= absorbed;
                addToLog(`Barrier absorbed ${absorbed} damage!`, 'enemy');
                if (enemy.barrierStrength <= 0) {
                    enemy.barrier = false;
                    addToLog('Enemy barrier broke!', 'player');
                }
            }

            // update damage stats
            // actualizamos la el daño
            totalDamageDealt += damage;
            if (damage > highestHit) highestHit = damage;

            enemy.hp -= damage;
            addToLog(`${player.name} used ${move.move_name}!`, 'player');
            addToLog(`Enemy took ${damage} damage!`, 'player');
        }

        // show damage numbers
        // mostrar daño
        if (damage > 0) {
            showDamageText(damage, enemy.x, enemy.y - 20);
        }

        // check if enemy has less than 0 health
        // comprobar si enemigo tiene -0 de vida
        if (enemy.hp <= 0) {
            enemy.hp = 0;
            gameOver = true;
            addToLog('Enemy was defeated!', 'player');
            
            // Store game end stats in session
            // Guardar las stats en la sesión
            fetch('game_end.php', {
                //se envia por post
                method: 'POST',
                //enviamos por url encode (si no explota)
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                //enviamos la info en un array asociativo
                body: new URLSearchParams({
                    'character': player.name,
                    'damage': totalDamageDealt,
                    'is_kill': '1',
                    'highest_hit': highestHit
                })
            });
        } else {
            // change turns
            // cambio de turnos
            isPlayerTurn = false;
            setTimeout(enemyTurn, 1000);
        }
    }
}

// Process effects
// Procesar efectos
function processTurnEffects() {
    // check poison effects
    // comprobar efectos de veneno
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

// Enemy turn function
// Función de turno enemigo
function enemyTurn() {
    if (gameOver) return;

    // process start of turn effects
    // procesar efectos de inicio de turno
    processTurnEffects();

    // basic enemy attack
    // ataque básico enemigo
    const damage = 10;
    
    // check player barrier
    // comprobar la barrera jugador
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

    // show damage numbers
    // mostrar números de daño
    showDamageText(finalDamage, player.x, player.y - 20);

    // check if player died
    // comprobar si jugador esta muerto
    if (player.currentHp <= 0) {
        player.currentHp = 0;
        gameOver = true;
        addToLog(`${player.name} was defeated!`, 'enemy');
    } else {
        // add extra delay before player can act again
        // añadir retraso extra antes de que el jugador pueda actuar
        setTimeout(() => {
            isPlayerTurn = true;
        }, 500);
    }
}

// Add message to battle log
// Añadir mensaje al chat de batalla
function addToLog(message, turnType = 'neutral') {
    const logContainer = document.getElementById('log-container');
    const messageElement = document.createElement('div');
    messageElement.className = `log-message ${turnType}-turn`;
    messageElement.textContent = message;
    logContainer.appendChild(messageElement);
    // scroll
    logContainer.scrollTop = logContainer.scrollHeight;
}

// Show damage numbers on screen
// Mostrar números de daño en pantalla
function showDamageText(damage, x, y) {
    ctx.fillStyle = 'white';
    ctx.font = '20px Arial';
    ctx.fillText(damage, x, y);
}

// Draw health bars
// Dibujar barras de vida
function drawHealthBar(x, y, currentHp, maxHp, width = 100) {
    const height = 10;
    
    // if this is the enemy's health bar, use smooth transition
    // si esta es la vida del enemigo, transicion limpia
    //bajando cada 0.5
    if (currentHp === enemy.hp) {
        if (!enemy.displayedHp) enemy.displayedHp = enemy.hp;
        if (enemy.displayedHp > enemy.hp) {
            enemy.displayedHp = Math.max(enemy.hp, enemy.displayedHp - 0.5);
        }
        currentHp = enemy.displayedHp;
    }
    // if this is the player's health bar, use smooth transition
    // si esta es la vida del jugador, transicion limpia
    //bajando cada 0.5
    else if (currentHp === player.currentHp) {
        if (player.displayedHp > player.currentHp) {
            player.displayedHp = Math.max(player.currentHp, player.displayedHp - 0.5);
        }
        currentHp = player.displayedHp;
    }
    
    const healthPercentage = currentHp / maxHp;

    // draw background with outline
    // dibujar fondo con borde
    ctx.strokeStyle = 'black';
    ctx.lineWidth = 2;
    ctx.fillStyle = '#333';
    ctx.fillRect(x - width/2, y, width, height);
    ctx.strokeRect(x - width/2, y, width, height);

    // draw health with outline
    // dibujar vida con borde
    ctx.fillStyle = healthPercentage > 0.5 ? 'green' : healthPercentage > 0.25 ? 'yellow' : 'red';
    ctx.fillRect(x - width/2, y, width * healthPercentage, height);
    ctx.strokeRect(x - width/2, y, width * healthPercentage, height);

    // draw health numbers above bar
    // dibujar numeros de vida
    ctx.fillStyle = 'white';
    ctx.font = '16px Arial';
    ctx.textAlign = 'center';
    ctx.fillText(`${Math.round(currentHp)}/${maxHp}`, x, y - 5);
}

// Draw everything on canvas
// Dibujar todo en el canvas
function draw() {
    // clear previous frame
    // limpiar frame anterior
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // draw background
    // dibujar fondo
    if (background.complete) {
        ctx.drawImage(background, 0, 0, canvas.width, canvas.height);
    }

    // draw player
    // dibujar jugador
    if (player) {
        ctx.strokeStyle = 'black';
        ctx.lineWidth = 3;
        ctx.fillStyle = player.character_color;
        ctx.beginPath();
        ctx.arc(player.x, player.y, player.radius, 0, Math.PI * 2);
        ctx.fill();
        ctx.stroke();
        
        // draw player health
        // dibujar vida jugador
        drawHealthBar(player.x, player.y - 70, player.currentHp, player.max_hp);
        
        // draw barrier if active
        // dibujar barrera si hay
        if (player.barrier && player.barrierStrength > 0) {
            ctx.fillStyle = '#4287f5'; // Light blue color for barrier
            ctx.font = '16px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(`Shield: ${player.barrierStrength}`, player.x, player.y - 90);
        }
    }

    // draw enemy triangle
    // dibujar triángulo enemigo
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

    // draw enemy health
    // dibujar vida del enemigo
    drawHealthBar(enemy.x, enemy.y - 20, enemy.hp, enemy.maxHp);
    
    // draw barrier if active
    // dibujar barrera si hay
    if (enemy.barrier && enemy.barrierStrength > 0) {
        ctx.fillStyle = '#4287f5'; // Light blue color for barrier
        ctx.font = '16px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(`Shield: ${enemy.barrierStrength}`, enemy.x, enemy.y - 40);
    }

    // draw turn indicator
    // dibujar el indicador de turno
    ctx.fillStyle = 'white';
    ctx.font = '24px Arial';
    ctx.fillText(isPlayerTurn ? 'Your Turn' : 'Enemy Turn', canvas.width/2 - 50, 30);

    // draw game over text
    // dibujar texto game over
    if (gameOver) {
        ctx.fillStyle = 'white';
        ctx.font = '48px Arial';
        ctx.fillText(player.currentHp > 0 ? 'You Win!' : 'Game Over', canvas.width/2 - 100, canvas.height/2);
    }
}

// Animation loop
// Bucle de animación
function gameLoop() {
    draw();
    requestAnimationFrame(gameLoop);
}

// Make attack function global for buttons
// Hacer función de ataque global para botones
window.playerAttack = playerAttack;

// Start game when loaded
// Empezar juego cuando cargue
window.addEventListener('load', async () => {
    await loadPlayer();
    gameLoop();
});