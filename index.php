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
    ['id' => 11, 'name' => 'Torres', 'position' => 'Delantero', 'number' => 11]
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
        $titular = $_POST['titular_' . $player['id']] ?? '1';
        $minutes = $_POST['minutes_' . $player['id']] ?? '90';

        $lineup[] = [
            'playerId' => $player['id'],
            'name' => $player['name'],
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
    foreach ($players as $player) {
        $goals = intval($_POST['goals_' . $player['id']] ?? 0);
        $assists = intval($_POST['assists_' . $player['id']] ?? 0);
        $yellow = intval($_POST['yellow_' . $player['id']] ?? 0);
        $red = intval($_POST['red_' . $player['id']] ?? 0);

        $playerStats[] = [
            'playerId' => $player['id'],
            'name' => $player['name'],
            'goals' => $goals,
            'assists' => $assists,
            'yellowCards' => $yellow,
            'redCards' => $red
        ];
    }

    $_SESSION['matchData']['execution'] = [
        'goalsFor' => intval($_POST['goalsFor'] ?? 0),
        'goalsAgainst' => intval($_POST['goalsAgainst'] ?? 0),
        'substitutions' => $_POST['substitutions'] ?? '',
        'playerStats' => $playerStats
    ];

    $_SESSION['message'] = 'Datos del partido guardados correctamente!';
}

function saveAnalysis() {
    $_SESSION['matchData']['analysis'] = [
        'observations' => $_POST['matchAnalysis'] ?? '',
        'injuries' => $_POST['injuries'] ?? '',
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $_SESSION['message'] = 'Análisis guardado correctamente!';
}

function calculateStats() {
    if (!isset($_SESSION['matchData']['execution']['playerStats'])) {
        return ['totalGoals' => 0, 'totalAssists' => 0, 'totalCards' => 0, 'result' => '-'];
    }

    $stats = $_SESSION['matchData']['execution']['playerStats'];
    $totalGoals = array_sum(array_column($stats, 'goals'));
    $totalAssists = array_sum(array_column($stats, 'assists'));
    $totalCards = array_sum(array_column($stats, 'yellowCards')) + array_sum(array_column($stats, 'redCards'));

    $goalsFor = $_SESSION['matchData']['execution']['goalsFor'] ?? 0;
    $goalsAgainst = $_SESSION['matchData']['execution']['goalsAgainst'] ?? 0;

    $result = 'Empate';
    if ($goalsFor > $goalsAgainst) $result = 'Victoria';
    elseif ($goalsFor < $goalsAgainst) $result = 'Derrota';

    return [
        'totalGoals' => $totalGoals,
        'totalAssists' => $totalAssists,
        'totalCards' => $totalCards,
        'result' => $result
    ];
}

function exportData() {
    $filename = 'partido_' . date('Y-m-d') . '.json';
    $data = json_encode($_SESSION['matchData'], JSON_PRETTY_PRINT);

    header('Content-Type: application/json');
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

        .lineup-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .player-card {
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            padding: 15px;
            backdrop-filter: blur(5px);
        }

        .player-card h4 {
            margin-bottom: 10px;
            color: #fff;
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
    </style>
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

                <h3>Alineación Titular</h3>
                <div class="lineup-grid">
                    <?php foreach (array_slice($players, 0, 11) as $player): ?>
                        <div class="player-card">
                            <h4><?php echo $player['number']; ?>. <?php echo $player['name']; ?></h4>
                            <div class="form-group">
                                <label>Posición</label>
                                <input type="text" value="<?php echo $player['position']; ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>Titular</label>
                                <select name="titular_<?php echo $player['id']; ?>">
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Minutos Estimados</label>
                                <input type="number" name="minutes_<?php echo $player['id']; ?>" min="0" max="90" value="90">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="submit" class="btn">💾 Guardar Planificación</button>
            </form>

            <?php elseif ($currentTab === 'execution'): ?>
            <!-- EJECUCIÓN -->
            <h2>⚡ Ejecución del Partido</h2>
            <form method="POST">
                <input type="hidden" name="action" value="save_execution">

                <div class="match-result">
                    <div class="team-name">Mi Equipo</div>
                    <div class="score">
                        <input type="number" name="goalsFor" min="0" max="20" value="<?php echo $_SESSION['matchData']['execution']['goalsFor'] ?? 0; ?>" style="width: 80px; text-align: center; font-size: 2em;">
                        -
                        <input type="number" name="goalsAgainst" min="0" max="20" value="<?php echo $_SESSION['matchData']['execution']['goalsAgainst'] ?? 0; ?>" style="width: 80px; text-align: center; font-size: 2em;">
                    </div>
                    <div class="team-name"><?php echo $rivalName; ?></div>
                </div>

                <h3>Estadísticas de Jugadores</h3>
                <div class="lineup-grid">
                    <?php foreach (array_slice($players, 0, 11) as $player): ?>
                        <div class="player-card">
                            <h4><?php echo $player['number']; ?>. <?php echo $player['name']; ?></h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Goles</label>
                                    <input type="number" name="goals_<?php echo $player['id']; ?>" min="0" max="10" value="0">
                                </div>
                                <div class="form-group">
                                    <label>Asistencias</label>
                                    <input type="number" name="assists_<?php echo $player['id']; ?>" min="0" max="10" value="0">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Tarjetas Amarillas</label>
                                    <input type="number" name="yellow_<?php echo $player['id']; ?>" min="0" max="2" value="0">
                                </div>
                                <div class="form-group">
                                    <label>Tarjetas Rojas</label>
                                    <input type="number" name="red_<?php echo $player['id']; ?>" min="0" max="1" value="0">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h3>Cambios Realizados</h3>
                <div class="form-group">
                    <textarea name="substitutions" rows="3" placeholder="Registra los cambios realizados durante el partido..."><?php echo $_SESSION['matchData']['execution']['substitutions'] ?? ''; ?></textarea>
                </div>

                <button type="submit" class="btn">💾 Guardar Resultado</button>
            </form>

            <?php else: ?>
            <!-- ANÁLISIS -->
            <h2>📊 Análisis del Partido</h2>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['totalGoals']; ?></div>
                    <div class="stat-label">Goles Totales</div>
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
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="save_analysis">

                <h3>Análisis de Rendimiento</h3>
                <div class="form-group">
                    <label>Observaciones del Partido</label>
                    <textarea name="matchAnalysis" rows="6" placeholder="Analiza el rendimiento del equipo, puntos fuertes, áreas de mejora..."><?php echo $_SESSION['matchData']['analysis']['observations'] ?? ''; ?></textarea>
                </div>

                <h3>Lesiones Reportadas</h3>
                <div class="form-group">
                    <textarea name="injuries" rows="3" placeholder="Registra cualquier lesión ocurrida durante el partido..."><?php echo $_SESSION['matchData']['analysis']['injuries'] ?? ''; ?></textarea>
                </div>

                <button type="submit" class="btn">💾 Guardar Análisis</button>
            </form>

            <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="export_data">
                <button type="submit" class="btn btn-secondary">📤 Exportar Datos</button>
            </form>

            <?php if (!empty($_SESSION['matchData']['planning']) || !empty($_SESSION['matchData']['execution'])): ?>
            <div class="report-section">
                <h3>📋 Reporte del Partido</h3>
                <p><strong>Fecha:</strong> <?php echo $_SESSION['matchData']['planning']['date'] ?? 'No especificada'; ?></p>
                <p><strong>Rival:</strong> <?php echo $_SESSION['matchData']['planning']['rival'] ?? 'No especificado'; ?></p>
                <p><strong>Localía:</strong> <?php echo $_SESSION['matchData']['planning']['location'] ?? 'No especificada'; ?></p>
                <p><strong>Resultado:</strong> <?php echo $stats['result']; ?></p>
                <p><strong>Marcador:</strong> <?php echo ($_SESSION['matchData']['execution']['goalsFor'] ?? 0) . ' - ' . ($_SESSION['matchData']['execution']['goalsAgainst'] ?? 0); ?></p>

                <h4>Estadísticas:</h4>
                <ul style="list-style: none; padding-left: 20px;">
                    <li>• Goles totales: <?php echo $stats['totalGoals']; ?></li>
                    <li>• Asistencias: <?php echo $stats['totalAssists']; ?></li>
                    <li>• Tarjetas: <?php echo $stats['totalCards']; ?></li>
                </ul>

                <?php if (!empty($_SESSION['matchData']['analysis']['observations'])): ?>
                <h4>Análisis:</h4>
                <p><?php echo nl2br(htmlspecialchars($_SESSION['matchData']['analysis']['observations'])); ?></p>
                <?php endif; ?>

                <?php if (!empty($_SESSION['matchData']['analysis']['injuries'])): ?>
                <h4>Lesiones:</h4>
                <p><?php echo nl2br(htmlspecialchars($_SESSION['matchData']['analysis']['injuries'])); ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</body>
</html>
