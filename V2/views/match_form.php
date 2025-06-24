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
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: bold;
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

        .form-section {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }

        .form-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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

        .data-display {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .phase-section {
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .phase-section h3 {
            color: #fff;
            margin-bottom: 10px;
            border-bottom: 2px solid rgba(255,255,255,0.2);
            padding-bottom: 5px;
        }

        .data-item {
            background: rgba(255,255,255,0.1);
            padding: 8px 12px;
            margin: 5px 0;
            border-radius: 5px;
            border-left: 3px solid #ff6b6b;
        }

        .no-data {
            color: rgba(255,255,255,0.6);
            font-style: italic;
        }

        .success-message {
            background: rgba(76, 175, 80, 0.2);
            border: 1px solid rgba(76, 175, 80, 0.5);
            color: #4CAF50;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚽ Sistema de Gestión de Partidos</h1>
            <p>Planificación, Ejecución y Análisis</p>
        </div>

        <div class="tabs">
            <div class="tab active" onclick="showTab('planning')">📋 Planificación</div>
            <div class="tab" onclick="showTab('execution')">⚡ Ejecución</div>
            <div class="tab" onclick="showTab('analysis')">📊 Análisis</div>
        </div>

        <div class="content">
            <div id="success-message" class="success-message">
                Datos guardados correctamente!
            </div>

            <!-- PLANIFICACIÓN -->
            <div id="planning" class="form-section active">
                <h2>📋 Planificación del Partido</h2>
                <form method="post" action="" id="planningForm">
                    <input type="hidden" name="action" value="planning">
                    <div class="form-group">
                        <label>Datos de Planificación</label>
                        <textarea name="data" rows="4" placeholder="Describe la estrategia, alineación, objetivos del partido..." required></textarea>
                    </div>
                    <button type="submit" class="btn">💾 Guardar Planificación</button>
                </form>
            </div>

            <!-- EJECUCIÓN -->
            <div id="execution" class="form-section">
                <h2>⚡ Ejecución del Partido</h2>
                <form method="post" action="" id="executionForm">
                    <input type="hidden" name="action" value="execution">
                    <div class="form-group">
                        <label>Datos de Ejecución</label>
                        <textarea name="data" rows="4" placeholder="Registra el resultado, cambios, estadísticas del partido..." required></textarea>
                    </div>
                    <button type="submit" class="btn">💾 Guardar Ejecución</button>
                </form>
            </div>

            <!-- ANÁLISIS -->
            <div id="analysis" class="form-section">
                <h2>📊 Análisis del Partido</h2>
                <form method="post" action="" id="analysisForm">
                    <input type="hidden" name="action" value="analysis">
                    <div class="form-group">
                        <label>Datos de Análisis</label>
                        <textarea name="data" rows="4" placeholder="Analiza el rendimiento, puntos fuertes, áreas de mejora..." required></textarea>
                    </div>
                    <button type="submit" class="btn">💾 Guardar Análisis</button>
                </form>
            </div>

            <!-- MOSTRAR DATOS GUARDADOS -->
            <div class="data-display">
                <h2>📋 Datos Guardados</h2>
                <?php
                if (isset($_SESSION['matchData'])) {
                    $phases = [
                        'planning' => ['📋 Planificación', '#ff6b6b'],
                        'execution' => ['⚡ Ejecución', '#74b9ff'],
                        'analysis' => ['📊 Análisis', '#00b894']
                    ];

                    foreach ($phases as $phase => $info) {
                        echo "<div class='phase-section'>";
                        echo "<h3>" . $info[0] . "</h3>";

                        if (isset($_SESSION['matchData'][$phase]) && is_array($_SESSION['matchData'][$phase]) && !empty($_SESSION['matchData'][$phase])) {
                            foreach ($_SESSION['matchData'][$phase] as $entrada) {
                                echo "<div class='data-item' style='border-left-color: " . $info[1] . "'>";
                                if (is_string($entrada)) {
                                    echo htmlspecialchars($entrada);
                                } elseif (is_array($entrada)) {
                                    echo htmlspecialchars(print_r($entrada, true));
                                } else {
                                    echo htmlspecialchars((string)$entrada);
                                }
                                echo "</div>";
                            }
                        } else {
                            echo "<div class='no-data'>No hay datos registrados para esta fase</div>";
                        }
                        echo "</div>";
                    }
                } else {
                    echo "<div class='no-data'>No hay datos guardados aún.</div>";
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Ocultar todas las secciones
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('active');
            });

            // Remover clase active de todos los tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            // Mostrar la sección seleccionada
            document.getElementById(tabName).classList.add('active');

            // Activar el tab correspondiente
            event.target.classList.add('active');
        }

        // Mostrar mensaje de éxito si hay parámetro en URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success') === '1') {
                document.getElementById('success-message').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('success-message').style.display = 'none';
                }, 3000);
            }
        });
    </script>
</body>
</html>
