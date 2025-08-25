// Data model
        const players = [
            { id: 1, name: 'García', position: 'Portero', number: 1 },
            { id: 2, name: 'Rodríguez', position: 'Defensa', number: 2 },
            { id: 3, name: 'López', position: 'Defensa', number: 3 },
            { id: 4, name: 'Martínez', position: 'Defensa', number: 4 },
            { id: 5, name: 'Sánchez', position: 'Defensa', number: 5 },
            { id: 6, name: 'Fernández', position: 'Centrocampista', number: 6 },
            { id: 7, name: 'González', position: 'Centrocampista', number: 7 },
            { id: 8, name: 'Pérez', position: 'Centrocampista', number: 8 },
            { id: 9, name: 'Ruiz', position: 'Delantero', number: 9 },
            { id: 10, name: 'Díaz', position: 'Delantero', number: 10 },
            { id: 11, name: 'Torres', position: 'Delantero', number: 11 },
            { id: 12, name: 'Morales', position: 'Defensa', number: 12 },
            { id: 13, name: 'Castro', position: 'Centrocampista', number: 13 },
            { id: 14, name: 'Silva', position: 'Delantero', number: 14 },
            { id: 15, name: 'Vargas', position: 'Portero', number: 15 },
            { id: 16, name: 'Herrera', position: 'Defensa', number: 16 }
        ];

        // Functions to manage local storage
        function getMatchData() {
            const data = localStorage.getItem('matchData');
            return data ? JSON.parse(data) : {
                planning: {},
                execution: {},
                analysis: {}
            };
        }

        function saveMatchData(data) {
            localStorage.setItem('matchData', JSON.stringify(data));
        }

        function showMessage(msg, type = 'success') {
            const msgArea = document.getElementById('message-area');
            msgArea.textContent = msg;
            msgArea.style.display = 'block';
            msgArea.style.backgroundColor = type === 'success' ? 'rgba(76, 175, 80, 0.8)' : 'rgba(244, 67, 54, 0.8)';
            setTimeout(() => {
                msgArea.style.display = 'none';
            }, 3000);
        }

        // Tab rendering functions
        function renderPlanningTab() {
            const matchData = getMatchData();
            const planningData = matchData.planning;
            const today = new Date().toISOString().split('T')[0];

            document.getElementById('content-area').innerHTML = `
                <h2>📋 Planificación del Partido</h2>
                <form id="planning-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Fecha del Partido</label>
                            <input type="date" name="matchDate" value="${planningData.date || today}" required>
                        </div>
                        <div class="form-group">
                            <label>Rival</label>
                            <input type="text" name="rival" placeholder="Nombre del equipo rival" value="${planningData.rival || ''}" required>
                        </div>
                        <div class="form-group">
                            <label>Localía</label>
                            <select name="location" required>
                                <option value="">Seleccionar</option>
                                <option value="Local" ${planningData.location === 'Local' ? 'selected' : ''}>Local</option>
                                <option value="Visitante" ${planningData.location === 'Visitante' ? 'selected' : ''}>Visitante</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Descripción/Estrategia</label>
                        <textarea name="strategy" rows="4" placeholder="Describe la estrategia y objetivos del partido...">${planningData.strategy || ''}</textarea>
                    </div>

                    <h3>Alineación del Equipo</h3>
                    <div class="auto-update-info">
                        <strong>💡 Consejo:</strong> Los jugadores no titulares tendrán sus minutos automáticamente en 0 y estarán desactivados en la ejecución.
                    </div>
                    <div class="lineup-grid" id="planning-lineup-grid">
                        ${players.map(player => {
                            const lineup = planningData.lineup || [];
                            const playerData = lineup.find(p => p.playerId === player.id) || { titular: '0', estimatedMinutes: '0' };
                            const statusClass = playerData.titular === '1' ? 'titular' : 'suplente';
                            const disabledAttr = playerData.titular === '0' ? 'disabled' : '';
                            return `
                                <div class="player-card ${statusClass}">
                                    <h4>
                                        ${player.number}. ${player.name}
                                        <span class="player-status">${player.position}</span>
                                    </h4>
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select name="titular_${player.id}" onchange="updatePlayerMinutes(${player.id})">
                                            <option value="1" ${playerData.titular === '1' ? 'selected' : ''}>Titular</option>
                                            <option value="0" ${playerData.titular === '0' ? 'selected' : ''}>Suplente</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Minutos Estimados</label>
                                        <input type="number" name="minutes_${player.id}"
                                               min="0" max="90"
                                               value="${playerData.estimatedMinutes}"
                                               class="minutes-input" ${disabledAttr}>
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>

                    <button type="submit" class="btn">💾 Guardar Planificación</button>
                </form>
            `;

            document.getElementById('planning-form').addEventListener('submit', handlePlanningSubmit);
            players.forEach(player => updatePlayerMinutes(player.id));
        }

        function renderExecutionTab() {
            const matchData = getMatchData();
            const planningData = matchData.planning;
            const executionData = matchData.execution || {};
            const rivalName = planningData.rival || 'Rival';

            document.getElementById('content-area').innerHTML = `
                <h2>⚡ Ejecución del Partido</h2>
                <div class="auto-update-info">
                    <strong>⚽ Auto-sincronización:</strong> Los goles del equipo se sincronizan automáticamente con la suma de goles individuales de los jugadores. Si reduces goles de un jugador, el total también bajará.
                    <div id="goalsDisplay" class="goals-display">Goles automáticos: ${executionData.goalsFor || 0}</div>
                </div>

                <form id="execution-form">
                    <div class="match-result">
                        <div class="team-name">Mi Equipo</div>
                        <div class="score">
                            <input type="number" name="goalsFor" id="goalsFor" min="0" max="20"
                                   value="${executionData.goalsFor || 0}"
                                   style="width: 80px; text-align: center; font-size: 2em;" readonly>
                            -
                            <input type="number" name="goalsAgainst" min="0" max="20"
                                   value="${executionData.goalsAgainst || 0}"
                                   style="width: 80px; text-align: center; font-size: 2em;">
                        </div>
                        <div class="team-name">${rivalName}</div>
                    </div>

                    <h3>Estadísticas de Jugadores</h3>
                    <div class="lineup-grid">
                        ${players.map(player => {
                            const playerStats = (executionData.playerStats || []).find(p => p.playerId === player.id) || {
                                goals: 0, assists: 0, yellowCards: 0, redCards: 0, minutesPlayed: 0
                            };
                            const isTitular = (planningData.lineup || []).find(p => p.playerId === player.id)?.titular === '1';
                            const statusClass = isTitular ? 'titular' : 'inactive';
                            const disabledAttr = isTitular ? '' : 'disabled';
                            const minutesValue = isTitular ? playerStats.minutesPlayed : 0;
                            return `
                                <div class="player-card ${statusClass}" data-player-id="${player.id}">
                                    <h4>
                                        ${player.number}. ${player.name}
                                        <span class="player-status">
                                            ${isTitular ? 'TITULAR' : 'SUPLENTE'}
                                        </span>
                                    </h4>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Goles</label>
                                            <input type="number" name="goals_${player.id}" min="0" max="10" value="${playerStats.goals}" ${disabledAttr}>
                                        </div>
                                        <div class="form-group">
                                            <label>Asistencias</label>
                                            <input type="number" name="assists_${player.id}" min="0" max="10" value="${playerStats.assists}" ${disabledAttr}>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>T. Amarillas</label>
                                            <input type="number" name="yellow_${player.id}" min="0" max="2" value="${playerStats.yellowCards}" ${disabledAttr}>
                                        </div>
                                        <div class="form-group">
                                            <label>T. Rojas</label>
                                            <input type="number" name="red_${player.id}" min="0" max="1" value="${playerStats.redCards}" ${disabledAttr}>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Minutos Jugados</label>
                                        <input type="number" name="minutes_played_${player.id}"
                                               min="0" max="90"
                                               value="${minutesValue}"
                                               class="minutes-input" ${disabledAttr}>
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>

                    <h3>Cambios Realizados</h3>
                    <div class="form-group">
                        <textarea name="substitutions" rows="3" placeholder="Registra los cambios realizados durante el partido...">${executionData.substitutions || ''}</textarea>
                    </div>

                    <button type="submit" class="btn">💾 Guardar Resultado</button>
                </form>
            `;

            document.getElementById('execution-form').addEventListener('submit', handleExecutionSubmit);
            document.querySelectorAll('input[name^="goals_"]').forEach(input => {
                input.addEventListener('input', updateGoalTotal);
            });
            updateGoalTotal();
        }

        function renderAnalysisTab() {
            const matchData = getMatchData();
            const stats = calculateStats(matchData);
            const planningData = matchData.planning || {};
            const executionData = matchData.execution || {};
            const analysisData = matchData.analysis || {};
            const playerStats = executionData.playerStats || [];
            const hasData = Object.keys(planningData).length > 0 || Object.keys(executionData).length > 0;

            document.getElementById('content-area').innerHTML = `
                <h2>📊 Análisis del Partido</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">${stats.totalGoals}</div>
                        <div class="stat-label">Goles del Equipo</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${stats.totalAssists}</div>
                        <div class="stat-label">Asistencias</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${stats.totalCards}</div>
                        <div class="stat-label">Tarjetas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${stats.result}</div>
                        <div class="stat-label">Resultado</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${stats.topScorer}</div>
                        <div class="stat-label">Máximo Goleador</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${stats.topAssists}</div>
                        <div class="stat-label">Más Asistencias</div>
                    </div>
                </div>

                ${playerStats.length > 0 ? `
                <div class="report-section">
                    <h3>📈 Rendimiento Individual</h3>
                    <div class="lineup-grid">
                        ${playerStats.filter(p => p.goals > 0 || p.assists > 0 || p.minutesPlayed > 0).map(p => `
                            <div class="player-card ${p.minutesPlayed > 0 ? 'titular' : 'inactive'}">
                                <h4>${p.number}. ${p.name}</h4>
                                <p><strong>Minutos:</strong> ${p.minutesPlayed}'</p>
                                <p><strong>Goles:</strong> ${p.goals} | <strong>Asistencias:</strong> ${p.assists}</p>
                                <p><strong>Tarjetas:</strong> ${p.yellowCards}🟡 ${p.redCards}🟥</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
                ` : ''}

                <form id="analysis-form">
                    <h3>📝 Análisis de Rendimiento</h3>
                    <div class="form-group">
                        <label>Observaciones del Partido</label>
                        <textarea name="matchAnalysis" rows="6" placeholder="Analiza el rendimiento del equipo, puntos fuertes, áreas de mejora, táctica utilizada...">${analysisData.observations || ''}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>🏥 Lesiones Reportadas</label>
                            <textarea name="injuries" rows="3" placeholder="Registra cualquier lesión ocurrida durante el partido...">${analysisData.injuries || ''}</textarea>
                        </div>
                        <div class="form-group">
                            <label>🎯 Áreas de Mejora</label>
                            <textarea name="improvements" rows="3" placeholder="Identifica áreas específicas donde el equipo puede mejorar...">${analysisData.improvements || ''}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>⚽ Enfoque para Próximo Partido</label>
                        <textarea name="nextMatchFocus" rows="3" placeholder="Define los puntos clave a trabajar para el próximo encuentro...">${analysisData.nextMatchFocus || ''}</textarea>
                    </div>

                    <button type="submit" class="btn">💾 Guardar Análisis</button>
                </form>

                <div style="text-align: center; margin: 20px 0;">
                    <button class="btn btn-secondary" onclick="exportData()">📤 Exportar Datos</button>
                    <button class="btn btn-secondary" onclick="window.print()">🖨️ Imprimir Reporte</button>
                </div>

                ${hasData ? `
                <div class="report-section">
                    <h3>📋 Reporte Completo del Partido</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <h4>ℹ️ Información General</h4>
                            <p><strong>Fecha:</strong> ${planningData.date || 'No especificada'}</p>
                            <p><strong>Rival:</strong> ${planningData.rival || 'No especificado'}</p>
                            <p><strong>Localía:</strong> ${planningData.location || 'No especificada'}</p>
                            <p><strong>Resultado:</strong> ${stats.result}</p>
                            <p><strong>Marcador:</strong> ${(executionData.goalsFor || 0)} - ${(executionData.goalsAgainst || 0)}</p>
                        </div>
                        <div class="form-group">
                            <h4>📊 Estadísticas Generales</h4>
                            <p>⚽ Goles del equipo: ${stats.totalGoals}</p>
                            <p>🎯 Asistencias totales: ${stats.totalAssists}</p>
                            <p>🟨🟥 Tarjetas totales: ${stats.totalCards}</p>
                            <p>⏱️ Minutos jugados: ${stats.totalMinutes}'</p>
                            <p>🥇 Goleador: ${stats.topScorer}</p>
                            <p>🎁 Más asistencias: ${stats.topAssists}</p>
                        </div>
                    </div>

                    ${planningData.strategy ? `
                    <h4>🎯 Estrategia Planificada</h4>
                    <p>${planningData.strategy.replace(/\n/g, '<br>')}</p>
                    ` : ''}

                    ${executionData.substitutions ? `
                    <h4>🔄 Cambios Realizados</h4>
                    <p>${executionData.substitutions.replace(/\n/g, '<br>')}</p>
                    ` : ''}

                    ${analysisData.observations ? `
                    <h4>📝 Análisis del Partido</h4>
                    <p>${analysisData.observations.replace(/\n/g, '<br>')}</p>
                    ` : ''}

                    ${analysisData.improvements ? `
                    <h4>🎯 Áreas de Mejora</h4>
                    <p>${analysisData.improvements.replace(/\n/g, '<br>')}</p>
                    ` : ''}

                    ${analysisData.nextMatchFocus ? `
                    <h4>⚽ Enfoque Próximo Partido</h4>
                    <p>${analysisData.nextMatchFocus.replace(/\n/g, '<br>')}</p>
                    ` : ''}

                    ${analysisData.injuries ? `
                    <h4>🏥 Lesiones Reportadas</h4>
                    <p style="color: #ffcdd2;">${analysisData.injuries.replace(/\n/g, '<br>')}</p>
                    ` : ''}

                    ${analysisData.timestamp ? `
                    <p style="margin-top: 20px; font-size: 0.9em; color: rgba(255,255,255,0.7);">
                        <strong>Reporte generado:</strong> ${analysisData.timestamp}
                    </p>
                    ` : ''}
                </div>
                ` : ''}
            `;
            document.getElementById('analysis-form').addEventListener('submit', handleAnalysisSubmit);
        }

        // Event handlers and data processing
        function handlePlanningSubmit(event) {
            event.preventDefault();
            const form = event.target;
            const matchData = getMatchData();

            const lineup = players.map(player => {
                const titularSelect = form.querySelector(`select[name="titular_${player.id}"]`);
                const minutesInput = form.querySelector(`input[name="minutes_${player.id}"]`);
                const titular = titularSelect.value;
                const estimatedMinutes = minutesInput.value;
                return {
                    playerId: player.id,
                    name: player.name,
                    position: player.position,
                    number: player.number,
                    titular: titular,
                    estimatedMinutes: titular === '1' ? estimatedMinutes : '0'
                };
            });

            matchData.planning = {
                date: form.matchDate.value,
                rival: form.rival.value,
                location: form.location.value,
                strategy: form.strategy.value,
                lineup: lineup
            };
            saveMatchData(matchData);
            showMessage('Planificación guardada correctamente!');
        }

        function handleExecutionSubmit(event) {
            event.preventDefault();
            const form = event.target;
            const matchData = getMatchData();
            const playerStats = players.map(player => {
                const goalsInput = form.querySelector(`input[name="goals_${player.id}"]`);
                const assistsInput = form.querySelector(`input[name="assists_${player.id}"]`);
                const yellowInput = form.querySelector(`input[name="yellow_${player.id}"]`);
                const redInput = form.querySelector(`input[name="red_${player.id}"]`);
                const minutesInput = form.querySelector(`input[name="minutes_played_${player.id}"]`);

                return {
                    playerId: player.id,
                    name: player.name,
                    position: player.position,
                    number: player.number,
                    goals: parseInt(goalsInput?.value || 0),
                    assists: parseInt(assistsInput?.value || 0),
                    yellowCards: parseInt(yellowInput?.value || 0),
                    redCards: parseInt(redInput?.value || 0),
                    minutesPlayed: parseInt(minutesInput?.value || 0)
                };
            });

            const goalsFor = playerStats.reduce((sum, p) => sum + p.goals, 0);

            matchData.execution = {
                goalsFor: goalsFor,
                goalsAgainst: parseInt(form.goalsAgainst.value),
                substitutions: form.substitutions.value,
                playerStats: playerStats
            };
            saveMatchData(matchData);
            showMessage('Datos del partido guardados correctamente! Goles totales: ' + goalsFor);
        }

        function handleAnalysisSubmit(event) {
            event.preventDefault();
            const form = event.target;
            const matchData = getMatchData();

            matchData.analysis = {
                observations: form.matchAnalysis.value,
                injuries: form.injuries.value,
                improvements: form.improvements.value,
                nextMatchFocus: form.nextMatchFocus.value,
                timestamp: new Date().toLocaleString()
            };
            saveMatchData(matchData);
            showMessage('Análisis guardado correctamente!');
        }

        function updatePlayerMinutes(playerId) {
            const titularSelect = document.querySelector(`select[name="titular_${playerId}"]`);
            const minutesInput = document.querySelector(`input[name="minutes_${playerId}"]`);
            const playerCard = titularSelect.closest('.player-card');

            if (titularSelect.value === '1') {
                minutesInput.disabled = false;
                minutesInput.value = minutesInput.value || '90';
                playerCard.classList.remove('suplente');
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
            document.getElementById('goalsFor').value = totalGoals;
            document.getElementById('goalsDisplay').textContent = `Goles automáticos: ${totalGoals}`;
        }

        function calculateStats(matchData) {
            const stats = matchData.execution?.playerStats || [];
            const goalsFor = matchData.execution?.goalsFor || 0;
            const goalsAgainst = matchData.execution?.goalsAgainst || 0;

            const totalGoals = stats.reduce((sum, p) => sum + p.goals, 0);
            const totalAssists = stats.reduce((sum, p) => sum + p.assists, 0);
            const totalCards = stats.reduce((sum, p) => sum + p.yellowCards + p.redCards, 0);
            const totalMinutes = stats.reduce((sum, p) => sum + p.minutesPlayed, 0);

            let result = 'Empate';
            if (goalsFor > goalsAgainst) result = 'Victoria';
            else if (goalsFor < goalsAgainst) result = 'Derrota';

            let topScorer = '-', maxGoals = 0;
            let topAssists = '-', maxAssists = 0;

            stats.forEach(player => {
                if (player.goals > maxGoals) {
                    maxGoals = player.goals;
                    topScorer = `${player.name} (${player.goals})`;
                }
                if (player.assists > maxAssists) {
                    maxAssists = player.assists;
                    topAssists = `${player.name} (${player.assists})`;
                }
            });

            return {
                totalGoals, totalAssists, totalCards, totalMinutes, result, topScorer, topAssists
            };
        }

        function exportData() {
            const matchData = getMatchData();
            const dataStr = JSON.stringify(matchData, null, 2);
            const blob = new Blob([dataStr], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `partido_${new Date().toISOString().slice(0, 10)}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        // Main App Logic
        function navigateToTab(tabName) {
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelector(`.tab[data-tab="${tabName}"]`).classList.add('active');

            switch (tabName) {
                case 'planning':
                    renderPlanningTab();
                    break;
                case 'execution':
                    renderExecutionTab();
                    break;
                case 'analysis':
                    renderAnalysisTab();
                    break;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const initialTab = new URLSearchParams(window.location.search).get('tab') || 'planning';
            navigateToTab(initialTab);

            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    const tabName = tab.getAttribute('data-tab');
                    navigateToTab(tabName);
                });
            });
        });