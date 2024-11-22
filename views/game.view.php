<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../wip/style.css">
  <link rel="stylesheet" href="../styles/general.css">

  <title>Juego</title>
</head>
<body>
    <div class="home-screen">
        <h1>Game</h1>
        <button class="btn" onclick="startGame()">Start Game</button>
    </div>

    <div class="game-screen">
        <div class="canvas-container">
            <canvas id="gameCanvas" width="800" height="600"></canvas>
            <div class="attack-buttons">
                <button class="btn" onclick="playerAttack('slash')">Slash</button>
                <button class="btn" onclick="playerAttack('focus')">Focus</button>
                <button class="btn" onclick="playerAttack('bladestorm')">Bladestorm</button>                
            </div>
        </div>
        <div class="chat-log">
            <div id="log-container"></div>
        </div>
    </div>

    <script type="module" src="../wip/script.js"></script>
    
</body>
</html>