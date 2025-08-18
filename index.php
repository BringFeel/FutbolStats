<?php
session_start();

// Inicializar datos de sesión si no existen
if (!isset($_SESSION['matchData'])) {
    $_SESSION['matchData'] = [
        'planning' => [],
        'execution' => [],
        'analysis' => []
    ];
}

// Datos de jugadores
$players = [
    ['id' => 1, 'name' => 'García', 'position' => 'Portero', 'number' => 1],
    ['id' => 2, 'name' => 'Rodríguez', 'position' => 'Defensa', 'number' => 2],
    ['id' => 3, 'name' => 'López', 'position' => 'Defensa', 'number' => 3],
    ['id' => 4, 'name' => 'Martínez', 'position' => 'Defensa', 'number' => 4],
    ['id' => 5, 'name' => 'Sánchez', 'position' => 'Defensa', 'number' => 5],
    ['id' => 6, 'name' => 'Fernández', 'position' => 'Centrocampista', 'number' => 6],
    ['id' => 7, 'name' => 'González', 'position' => 'Centrocampista', 'number' => 7],
    ['id' => 8, 'name' => 'Pérez', 'position' => 'Centrocampista', 'number' => 8],
    ['id' => 9, 'name' => 'Ruiz', 'position' => 'Delantero', 'number' => 9],
    ['id' => 10, 'name' => 'Díaz', 'position' => 'Delantero', 'number' => 10],
    ['id' => 11, 'name' => 'Torres', 'position' => 'Delantero', 'number' => 11],
    ['id' => 12, 'name' => 'Morales', 'position' => 'Defensa', 'number' => 12],
    ['id' => 13, 'name' => 'Castro', 'position' => 'Centrocampista', 'number' => 13],
    ['id' => 14, 'name' => 'Silva', 'position' => 'Delantero', 'number' => 14],
    ['id' => 15, 'name' => 'Vargas', 'position' => 'Portero', 'number' => 15],
    ['id' => 16, 'name' => 'Herrera', 'position' => 'Defensa', 'number' => 16]
];

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'save_planning':
                savePlanning();
                break;
            case 'save_execution':
                saveExecution();
                break;
            case 'save_analysis':
                saveAnalysis();
                break;
            case 'export_data':
                exportData();
                break;
        }
    }
}

function savePlanning() {
    global $players;

    $lineup = [];
    foreach ($players as $player) {
        $titular = $_POST['titular_' . $player['id']] ?? '0';
        $minutes = ($titular == '1') ? ($_POST['minutes_' . $player['id']] ?? '90') : '0';

        $lineup[] = [
            'playerId' => $player['id'],
            'name' => $player['name'],
            'position' => $player['position'],
            'number' => $player['number'],
            'titular' => $titular,
            'estimatedMinutes' => $minutes
        ];
    }

    $_SESSION['matchData']['planning'] = [
        'date' => $_POST['matchDate'] ?? '',
        'rival' => $_POST['rival'] ?? '',
        'location' => $_POST['location'] ?? '',
        'strategy' => $_POST['strategy'] ?? '',
        'lineup' => $lineup
    ];

    $_SESSION['message'] = 'Planificación guardada correctamente!';
}

function saveExecution() {
    global $players;

    $playerStats = [];
    $totalGoalsFromPlayers = 0;
    
    foreach ($players as $player) {
        $goals = intval($_POST['goals_' . $player['id']] ?? 0);
        $assists = intval($_POST['assists_' . $player['id']] ?? 0);
        $yellow = intval($_POST['yellow_' . $player['id']] ?? 0);
        $red = intval($_POST['red_' . $player['id']] ?? 0);
        $minutesPlayed = intval($_POST['minutes_played_' . $player['id']] ?? 0);
        
        $totalGoalsFromPlayers += $goals;

        $playerStats[] = [
            'playerId' => $player['id'],
            'name' => $player['name'],
            'position' => $player['position'],
            'number' => $player['number'],
            'goals' => $goals,
            'assists' => $assists,
            'yellowCards' => $yellow,
            'redCards' => $red,
            'minutesPlayed' => $minutesPlayed
        ];
    }

    // Usar directamente la suma de goles de jugadores
    $goalsFor = $totalGoalsFromPlayers;

    $_SESSION['matchData']['execution'] = [
        'goalsFor' => $goalsFor,
        'goalsAgainst' => intval($_POST['goalsAgainst'] ?? 0),
        'substitutions' => $_POST['substitutions'] ?? '',
        'playerStats' => $playerStats
    ];

    $_SESSION['message'] = 'Datos del partido guardados correctamente! Goles totales: ' . $goalsFor;
}

function saveAnalysis() {
    $_SESSION['matchData']['analysis'] = [
        'observations' => $_POST['matchAnalysis'] ?? '',
        'injuries' => $_POST['injuries'] ?? '',
        'improvements' => $_POST['improvements'] ?? '',
        'nextMatchFocus' => $_POST['nextMatchFocus'] ?? '',
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $_SESSION['message'] = 'Análisis guardado correctamente!';
}

function calculateStats() {
    if (!isset($_SESSION['matchData']['execution']['playerStats'])) {
        return [
            'totalGoals' => 0, 
            'totalAssists' => 0, 
            'totalCards' => 0, 
            'result' => '-',
            'topScorer' => '-',
            'topAssists' => '-',
            'totalMinutes' => 0
        ];
    }

    $stats = $_SESSION['matchData']['execution']['playerStats'];
    $totalGoals = array_sum(array_column($stats, 'goals'));
    $totalAssists = array_sum(array_column($stats, 'assists'));
    $totalCards = array_sum(array_column($stats, 'yellowCards')) + array_sum(array_column($stats, 'redCards'));
    $totalMinutes = array_sum(array_column($stats, 'minutesPlayed'));

    $goalsFor = $_SESSION['matchData']['execution']['goalsFor'] ?? 0;
    $goalsAgainst = $_SESSION['matchData']['execution']['goalsAgainst'] ?? 0;

    $result = 'Empate';
    if ($goalsFor > $goalsAgainst) $result = 'Victoria';
    elseif ($goalsFor < $goalsAgainst) $result = 'Derrota';

    // Encontrar máximo goleador y asistente
    $topScorer = '-';
    $topAssists = '-';
    $maxGoals = 0;
    $maxAssists = 0;
    
    foreach ($stats as $player) {
        if ($player['goals'] > $maxGoals) {
            $maxGoals = $player['goals'];
            $topScorer = $player['name'] . ' (' . $player['goals'] . ')';
        }
        if ($player['assists'] > $maxAssists) {
            $maxAssists = $player['assists'];
            $topAssists = $player['name'] . ' (' . $player['assists'] . ')';
        }
    }

    return [
        'totalGoals' => $totalGoals,
        'totalAssists' => $totalAssists,
        'totalCards' => $totalCards,
        'totalMinutes' => $totalMinutes,
        'result' => $result,
        'topScorer' => $topScorer,
        'topAssists' => $topAssists
    ];
}

function getPlayerLineup($playerId) {
    if (!isset($_SESSION['matchData']['planning']['lineup'])) {
        return ['titular' => '0', 'estimatedMinutes' => '0'];
    }
    
    foreach ($_SESSION['matchData']['planning']['lineup'] as $player) {
        if ($player['playerId'] == $playerId) {
            return $player;
        }
    }
    return ['titular' => '0', 'estimatedMinutes' => '0'];
}

function exportData() {
    $filename = 'partido_' . date('Y-m-d_H-i-s') . '.json';
    $data = json_encode($_SESSION['matchData'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $data;
    exit;
}

$currentTab = $_GET['tab'] ?? 'planning';
$stats = calculateStats();
$rivalName = $_SESSION['matchData']['planning']['rival'] ?? 'Rival';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Partidos de Fútbol</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .tabs {
            display: flex;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }

        .tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .tab a {
            color: white;
            text-decoration: none;
            display: block;
        }

        .tab.active {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        .tab:hover {
            background: rgba(255,255,255,0.15);
        }

        .content {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #fff;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: rgba(255,255,255,0.9);
            color: #333;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        input:disabled, select:disabled {
            background: rgba(255,255,255,0.3);
            color: #666;
            cursor: not-allowed;
        }

        .lineup-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .player-card {
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            padding: 15px;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .player-card.titular {
            background: rgba(76, 175, 80, 0.2);
            border: 2px solid rgba(76, 175, 80, 0.5);
        }

        .player-card.suplente {
            background: rgba(255, 152, 0, 0.2);
            border: 2px solid rgba(255, 152, 0, 0.5);
        }

        .player-card.inactive {
            background: rgba(158, 158, 158, 0.2);
            border: 2px solid rgba(158, 158, 158, 0.5);
            opacity: 0.6;
        }

        .player-card h4 {
            margin-bottom: 10px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .player-status {
            font-size: 0.8em;
            padding: 4px 8px;
            border-radius: 12px;
            background: rgba(255,255,255,0.2);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .stat-card {
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            backdrop-filter: blur(5px);
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #fff;
        }

        .stat-label {
            color: rgba(255,255,255,0.8);
            margin-top: 5px;
        }

        .btn {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
            margin: 10px 5px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #74b9ff, #0984e3);
        }

        .match-result {
            text-align: center;
            padding: 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            margin: 20px 0;
        }

        .score {
            font-size: 3em;
            font-weight: bold;
            margin: 20px 0;
        }

        .team-name {
            font-size: 1.5em;
            margin: 10px 0;
        }

        .message {
            background: rgba(76, 175, 80, 0.8);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .report-section {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .auto-update-info {
            background: rgba(33, 150, 243, 0.2);
            border-left: 4px solid #2196F3;
            padding: 10px 15px;
            margin: 15px 0;
            border-radius: 5px;
        }

        .goals-counter {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .goals-display {
            background: rgba(76, 175, 80, 0.3);
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: bold;
        }

        .minutes-input {
            background: rgba(255, 193, 7, 0.2) !important;
            border: 1px solid rgba(255, 193, 7, 0.5) !important;
        }
    </style>
    <script>
        function updatePlayerMinutes(playerId) {
            const titularSelect = document.querySelector(`select[name="titular_${playerId}"]`);
            const minutesInput = document.querySelector(`input[name="minutes_${playerId}"]`);
            const playerCard = titularSelect.closest('.player-card');
            
            if (titularSelect.value === '1') {
                minutesInput.disabled = false;
                minutesInput.value = minutesInput.value || '90';
                playerCard.classList.remove('suplente', 'inactive');
                playerCard.classList.add('titular');
            } else {
                minutesInput.disabled = true;
                minutesInput.value = '0';
                playerCard.classList.remove('titular');
                playerCard.classList.add('suplente');
            }
        }

        function updateGoalTotal() {
            const goalInputs = document.querySelectorAll('input[name^="goals_"]');
            let totalGoals = 0;
            
            goalInputs.forEach(input => {
                if (!input.disabled) {
                    totalGoals += parseInt(input.value) || 0;
                }
            });
            
            const goalsForInput = document.querySelector('input[name="goalsFor"]');
            if (goalsForInput) {
                // Siempre usar el total de goles de jugadores, permitiendo que baje
                goalsForInput.value = totalGoals;
            }
            
            // Update display
            const goalsDisplay = document.getElementById('goalsDisplay');
            if (goalsDisplay) {
                goalsDisplay.textContent = `Goles automáticos: ${totalGoals}`;
            }
        }

        function initializeExecution() {
            <?php if ($currentTab === 'execution'): ?>
            // Disable inputs for non-titular players
            <?php foreach ($players as $player): ?>
                <?php $playerData = getPlayerLineup($player['id']); ?>
                <?php if ($playerData['titular'] == '0'): ?>
                    const playerCard<?php echo $player['id']; ?> = document.querySelector('.player-card[data-player-id="<?php echo $player['id']; ?>"]');
                    if (playerCard<?php echo $player['id']; ?>) {
                        playerCard<?php echo $player['id']; ?>.classList.add('inactive');
                        const inputs = playerCard<?php echo $player['id']; ?>.querySelectorAll('input');
                        inputs.forEach(input => {
                            input.disabled = true;
                            input.value = '0';
                        });
                    }
                <?php endif; ?>
            <?php endforeach; ?>
            
            // Add event listeners for goal inputs
            const goalInputs = document.querySelectorAll('input[name^="goals_"]');
            goalInputs.forEach(input => {
                input.addEventListener('input', updateGoalTotal);
            });
            
            // Initial goal calculation
            updateGoalTotal();
            <?php endif; ?>
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializeExecution();
            
            // Initialize minutes controls for planning tab
            const titularSelects = document.querySelectorAll('select[name^="titular_"]');
            titularSelects.forEach(select => {
                const playerId = select.name.split('_')[1];
                select.addEventListener('change', () => updatePlayerMinutes(playerId));
                updatePlayerMinutes(playerId); // Initialize on load
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚽ Sistema de Gestión de Partidos</h1>
            <p>Planificación, Ejecución y Análisis</p>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="tabs">
            <div class="tab <?php echo $currentTab === 'planning' ? 'active' : ''; ?>">
                <a href="?tab=planning">📋 Planificación</a>
            </div>
            <div class="tab <?php echo $currentTab === 'execution' ? 'active' : ''; ?>">
                <a href="?tab=execution">⚡ Ejecución</a>
            </div>
            <div class="tab <?php echo $currentTab === 'analysis' ? 'active' : ''; ?>">
                <a href="?tab=analysis">📊 Análisis</a>
            </div>
        </div>

        <div class="content">
            <?php if ($currentTab === 'planning'): ?>
            <!-- PLANIFICACIÓN -->
            <h2>📋 Planificación del Partido</h2>
            <form method="POST">
                <input type="hidden" name="action" value="save_planning">

                <div class="form-row">
                    <div class="form-group">
                        <label>Fecha del Partido</label>
                        <input type="date" name="matchDate" value="<?php echo $_SESSION['matchData']['planning']['date'] ?? date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Rival</label>
                        <input type="text" name="rival" placeholder="Nombre del equipo rival" value="<?php echo $_SESSION['matchData']['planning']['rival'] ?? ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Localía</label>
                        <select name="location" required>
                            <option value="">Seleccionar</option>
                            <option value="Local" <?php echo ($_SESSION['matchData']['planning']['location'] ?? '') === 'Local' ? 'selected' : ''; ?>>Local</option>
                            <option value="Visitante" <?php echo ($_SESSION['matchData']['planning']['location'] ?? '') === 'Visitante' ? 'selected' : ''; ?>>Visitante</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Descripción/Estrategia</label>
                    <textarea name="strategy" rows="4" placeholder="Describe la estrategia y objetivos del partido..."><?php echo $_SESSION['matchData']['planning']['strategy'] ?? ''; ?></textarea>
                </div>

                <h3>Alineación del Equipo</h3>
                <div class="auto-update-info">
                    <strong>💡 Consejo:</strong> Los jugadores no titulares tendrán sus minutos automáticamente en 0 y estarán desactivados en la ejecución.
                </div>
                <div class="lineup-grid">
                    <?php foreach ($players as $player): ?>
                        <?php $playerData = getPlayerLineup($player['id']); ?>
                        <div class="player-card <?php echo $playerData['titular'] == '1' ? 'titular' : 'suplente'; ?>">
                            <h4>
                                <?php echo $player['number']; ?>. <?php echo $player['name']; ?>
                                <span class="player-status"><?php echo $player['position']; ?></span>
                            </h4>
                            <div class="form-group">
                                <label>Estado</label>
                                <select name="titular_<?php echo $player['id']; ?>">
                                    <option value="1" <?php echo $playerData['titular'] == '1' ? 'selected' : ''; ?>>Titular</option>
                                    <option value="0" <?php echo $playerData['titular'] == '0' ? 'selected' : ''; ?>>Suplente</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Minutos Estimados</label>
                                <input type="number" name="minutes_<?php echo $player['id']; ?>" 
                                       min="0" max="90" 
                                       value="<?php echo $playerData['estimatedMinutes']; ?>"
                                       class="minutes-input">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="submit" class="btn">💾 Guardar Planificación</button>
            </form>

            <?php elseif ($currentTab === 'execution'): ?>
            <!-- EJECUCIÓN -->
            <h2>⚡ Ejecución del Partido</h2>
            <div class="auto-update-info">
                <strong>⚽ Auto-sincronización:</strong> Los goles del equipo se sincronizan automáticamente con la suma de goles individuales de los jugadores. Si reduces goles de un jugador, el total también bajará.
                <div id="goalsDisplay" class="goals-display">Goles automáticos: 0</div>
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="save_execution">

                <div class="match-result">
                    <div class="team-name">Mi Equipo</div>
                    <div class="score">
                        <input type="number" name="goalsFor" min="0" max="20" 
                               value="<?php echo $_SESSION['matchData']['execution']['goalsFor'] ?? 0; ?>" 
                               style="width: 80px; text-align: center; font-size: 2em;">
                        -
                        <input type="number" name="goalsAgainst" min="0" max="20" 
                               value="<?php echo $_SESSION['matchData']['execution']['goalsAgainst'] ?? 0; ?>" 
                               style="width: 80px; text-align: center; font-size: 2em;">
                    </div>
                    <div class="team-name"><?php echo $rivalName; ?></div>
                </div>

                <h3>Estadísticas de Jugadores</h3>
                <div class="lineup-grid">
                    <?php foreach ($players as $player): ?>
                        <?php $playerData = getPlayerLineup($player['id']); ?>
                        <div class="player-card <?php echo $playerData['titular'] == '1' ? 'titular' : ($playerData['titular'] == '0' ? 'inactive' : ''); ?>" 
                             data-player-id="<?php echo $player['id']; ?>">
                            <h4>
                                <?php echo $player['number']; ?>. <?php echo $player['name']; ?>
                                <span class="player-status">
                                    <?php echo $playerData['titular'] == '1' ? 'TITULAR' : 'SUPLENTE'; ?>
                                </span>
                            </h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Goles</label>
                                    <input type="number" name="goals_<?php echo $player['id']; ?>" 
                                           min="0" max="10" value="0">
                                </div>
                                <div class="form-group">
                                    <label>Asistencias</label>
                                    <input type="number" name="assists_<?php echo $player['id']; ?>" 
                                           min="0" max="10" value="0">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>T. Amarillas</label>
                                    <input type="number" name="yellow_<?php echo $player['id']; ?>" 
                                           min="0" max="2" value="0">
                                </div>
                                <div class="form-group">
                                    <label>T. Rojas</label>
                                    <input type="number" name="red_<?php echo $player['id']; ?>" 
                                           min="0" max="1" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Minutos Jugados</label>
                                <input type="number" name="minutes_played_<?php echo $player['id']; ?>" 
                                       min="0" max="90" 
                                       value="<?php echo $playerData['titular'] == '1' ? $playerData['estimatedMinutes'] : '0'; ?>"
                                       class="minutes-input">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h3>Cambios Realizados</h3>
                <div class="form-group">
                    <textarea name="substitutions" rows="3" placeholder="Registra los cambios realizados durante el partido (ej: Min 65: Sale Pérez, Entra Silva)..."><?php echo $_SESSION['matchData']['execution']['substitutions'] ?? ''; ?></textarea>
                </div>

                <button type="submit" class="btn">💾 Guardar Resultado</button>
            </form>

            <?php else: ?>
            <!-- ANÁLISIS -->
            <h2>📊 Análisis del Partido</h2>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['totalGoals']; ?></div>
                    <div class="stat-label">Goles del Equipo</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['totalAssists']; ?></div>
                    <div class="stat-label">Asistencias</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['totalCards']; ?></div>
                    <div class="stat-label">Tarjetas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['result']; ?></div>
                    <div class="stat-label">Resultado</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['topScorer']; ?></div>
                    <div class="stat-label">Máximo Goleador</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['topAssists']; ?></div>
                    <div class="stat-label">Más Asistencias</div>
                </div>
            </div>

            <?php if (isset($_SESSION['matchData']['execution']['playerStats'])): ?>
            <div class="report-section">
                <h3>📈 Rendimiento Individual</h3>
                <div class="lineup-grid">
                    <?php foreach ($_SESSION['matchData']['execution']['playerStats'] as $playerStat): ?>
                        <?php if ($playerStat['goals'] > 0 || $playerStat['assists'] > 0 || $playerStat['minutesPlayed'] > 0): ?>
                        <div class="player-card <?php echo $playerStat['minutesPlayed'] > 0 ? 'titular' : 'inactive'; ?>">
                            <h4><?php echo $playerStat['number']; ?>. <?php echo $playerStat['name']; ?></h4>
                            <p><strong>Minutos:</strong> <?php echo $playerStat['minutesPlayed']; ?>'</p>
                            <p><strong>Goles:</strong> <?php echo $playerStat['goals']; ?> | <strong>Asistencias:</strong> <?php echo $playerStat['assists']; ?></p>
                            <p><strong>Tarjetas:</strong> <?php echo $playerStat['yellowCards']; ?>🟡 <?php echo $playerStat['redCards']; ?>🟥</p>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="action" value="save_analysis">

                <h3>📝 Análisis de Rendimiento</h3>
                <div class="form-group">
                    <label>Observaciones del Partido</label>
                    <textarea name="matchAnalysis" rows="6" placeholder="Analiza el rendimiento del equipo, puntos fuertes, áreas de mejora, táctica utilizada..."><?php echo $_SESSION['matchData']['analysis']['observations'] ?? ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>🏥 Lesiones Reportadas</label>
                        <textarea name="injuries" rows="3" placeholder="Registra cualquier lesión ocurrida durante el partido..."><?php echo $_SESSION['matchData']['analysis']['injuries'] ?? ''; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>🎯 Áreas de Mejora</label>
                        <textarea name="improvements" rows="3" placeholder="Identifica áreas específicas donde el equipo puede mejorar..."><?php echo $_SESSION['matchData']['analysis']['improvements'] ?? ''; ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label>⚽ Enfoque para Próximo Partido</label>
                    <textarea name="nextMatchFocus" rows="3" placeholder="Define los puntos clave a trabajar para el próximo encuentro..."><?php echo $_SESSION['matchData']['analysis']['nextMatchFocus'] ?? ''; ?></textarea>
                </div>

                <button type="submit" class="btn">💾 Guardar Análisis</button>
            </form>

            <div style="text-align: center; margin: 20px 0;">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="export_data">
                    <button type="submit" class="btn btn-secondary">📤 Exportar Datos</button>
                </form>
                <button onclick="window.print()" class="btn btn-secondary">🖨️ Imprimir Reporte</button>
            </div>

            <?php if (!empty($_SESSION['matchData']['planning']) || !empty($_SESSION['matchData']['execution'])): ?>
            <div class="report-section">
                <h3>📋 Reporte Completo del Partido</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <h4>ℹ️ Información General</h4>
                        <p><strong>Fecha:</strong> <?php echo $_SESSION['matchData']['planning']['date'] ?? 'No especificada'; ?></p>
                        <p><strong>Rival:</strong> <?php echo $_SESSION['matchData']['planning']['rival'] ?? 'No especificado'; ?></p>
                        <p><strong>Localía:</strong> <?php echo $_SESSION['matchData']['planning']['location'] ?? 'No especificada'; ?></p>
                        <p><strong>Resultado:</strong> <?php echo $stats['result']; ?></p>
                        <p><strong>Marcador:</strong> <?php echo ($_SESSION['matchData']['execution']['goalsFor'] ?? 0) . ' - ' . ($_SESSION['matchData']['execution']['goalsAgainst'] ?? 0); ?></p>
                    </div>
                    <div class="form-group">
                        <h4>📊 Estadísticas Generales</h4>
                        <p>⚽ Goles del equipo: <?php echo $stats['totalGoals']; ?></p>
                        <p>🎯 Asistencias totales: <?php echo $stats['totalAssists']; ?></p>
                        <p>🟨🟥 Tarjetas totales: <?php echo $stats['totalCards']; ?></p>
                        <p>⏱️ Minutos jugados: <?php echo $stats['totalMinutes']; ?>'</p>
                        <p>🥇 Goleador: <?php echo $stats['topScorer']; ?></p>
                        <p>🎁 Más asistencias: <?php echo $stats['topAssists']; ?></p>
                    </div>
                </div>

                <?php if (!empty($_SESSION['matchData']['planning']['strategy'])): ?>
                <h4>🎯 Estrategia Planificada</h4>
                <p><?php echo nl2br(htmlspecialchars($_SESSION['matchData']['planning']['strategy'])); ?></p>
                <?php endif; ?>

                <?php if (!empty($_SESSION['matchData']['execution']['substitutions'])): ?>
                <h4>🔄 Cambios Realizados</h4>
                <p><?php echo nl2br(htmlspecialchars($_SESSION['matchData']['execution']['substitutions'])); ?></p>
                <?php endif; ?>

                <?php if (!empty($_SESSION['matchData']['analysis']['observations'])): ?>
                <h4>📝 Análisis del Partido</h4>
                <p><?php echo nl2br(htmlspecialchars($_SESSION['matchData']['analysis']['observations'])); ?></p>
                <?php endif; ?>

                <?php if (!empty($_SESSION['matchData']['analysis']['improvements'])): ?>
                <h4>🎯 Áreas de Mejora</h4>
                <p><?php echo nl2br(htmlspecialchars($_SESSION['matchData']['analysis']['improvements'])); ?></p>
                <?php endif; ?>

                <?php if (!empty($_SESSION['matchData']['analysis']['nextMatchFocus'])): ?>
                <h4>⚽ Enfoque Próximo Partido</h4>
                <p><?php echo nl2br(htmlspecialchars($_SESSION['matchData']['analysis']['nextMatchFocus'])); ?></p>
                <?php endif; ?>

                <?php if (!empty($_SESSION['matchData']['analysis']['injuries'])): ?>
                <h4>🏥 Lesiones Reportadas</h4>
                <p style="color: #ffcdd2;"><?php echo nl2br(htmlspecialchars($_SESSION['matchData']['analysis']['injuries'])); ?></p>
                <?php endif; ?>

                <?php if (isset($_SESSION['matchData']['analysis']['timestamp'])): ?>
                <p style="margin-top: 20px; font-size: 0.9em; color: rgba(255,255,255,0.7);">
                    <strong>Reporte generado:</strong> <?php echo $_SESSION['matchData']['analysis']['timestamp']; ?>
                </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>

    <style media="print">
        body { background: white !important; color: black !important; }
        .container { box-shadow: none !important; }
        .content { background: white !important; backdrop-filter: none !important; }
        .tabs, .btn { display: none !important; }
        .player-card, .stat-card, .report-section { 
            background: #f5f5f5 !important; 
            color: black !important; 
            break-inside: avoid; 
        }
        .header h1 { color: black !important; text-shadow: none !important; }
    </style>
</body>
</html>