<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/turnbasedgame/styles/general.css">
  <link rel="stylesheet" href="/turnbasedgame/styles/game.css">
  <title>Juego</title>
</head>
<body>
    <div class="game-screen">
        <div class="game-container">
            <div class="canvas-container">
                <canvas id="gameCanvas" width="800" height="600"></canvas>
                <div class="attack-buttons">
                    <button class="btn" onclick="playerAttack('slash')">Slash</button>
                    <button class="btn" onclick="playerAttack('focus')">Focus</button>
                    <button class="btn" onclick="playerAttack('bladestorm')">Bladestorm</button>                
                </div>
            </div>
        </div>
        <div class="chat-log">
            <div id="log-container"></div>
        </div>
    </div>
    <script type="module" src="js/game.js"></script>
</body>
</html>