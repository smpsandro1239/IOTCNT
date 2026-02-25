<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="description" content="Gestão de agendamentos automáticos para o sistema IOTCNT">
    <meta name="robots" content="noindex, nofollow">
    <title>IOTCNT - Agendamentos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            color: #333;
            font-size: 16px;
        }

        .navbar {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .navbar-brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
        }

        .navbar-brand h1 {
            font-size: 1.25rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .navbar-brand span {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.9);
        }

        .navbar-nav {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .nav-link {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
            font-size: 0.875rem;
            min-height: 44px;
            display: flex;
            align-items: center;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .user-badge {
            background: #dc2626;
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .btn:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #1f2937;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 10;
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-success:hover {
            background: #15803d;
        }

        .btn-warning {
            background: #d97706;
            color: white;
        }

        .btn-warning:hover {
            background: #b45309;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            margin-bottom: 2rem;
        }

        .page-title h2 {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .page-title p {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .section {
            background: white;
            border-radius: 0.75rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .section h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .schedule-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1.5rem;
            transition: all 0.3s;
        }

        .schedule-card.active {
            border-color: #16a34a;
            background: #f0fdf4;
        }

        .schedule-card h4 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }

        .schedule-card p {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 16px;
            background: white;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
        }

        /* Responsive breakpoints */
        @media (min-width: 480px) {
            .navbar-content { padding: 0 1.5rem; }
            .navbar-brand h1 { font-size: 1.5rem; }
            .navbar-brand span { font-size: 1rem; }
            .nav-link {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }
            .main { padding: 1.5rem; }
            .page-title h2 { font-size: 1.75rem; }
            .page-title p { font-size: 1rem; }
            .schedule-card { padding: 1.25rem; }
            .btn { padding: 0.75rem 1.5rem; font-size: 1rem; }
        }

        @media (min-width: 768px) {
            .navbar { padding: 1rem 0; }
            .navbar-content {
                padding: 0 2rem;
                flex-wrap: nowrap;
            }
            .navbar-nav { flex-wrap: nowrap; }
            .main { padding: 2rem; }
            .page-title h2 { font-size: 2rem; }
            .schedule-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 2rem;
            }
            .schedule-card { padding: 1.5rem; }
            .section { padding: 1.5rem; }
            .user-info { justify-content: flex-end; }
        }

        @media (min-width: 1024px) {
            .schedule-grid {
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            }
            .schedule-card { padding: 1.75rem; }
        }

        @media (min-width: 1441px) {
            .navbar-content,
            .main {
                max-width: 1400px;
            }
            .schedule-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 2.5rem;
            }
            .schedule-card { padding: 2rem; }
        }

        /* Mobile Portrait */
        @media (max-width: 480px) {
            .navbar-content {
                flex-direction: column;
                gap: 1rem;
                padding: 0 1rem;
            }
            .navbar-brand h1 { font-size: 1.25rem; }
            .navbar-brand span { font-size: 0.75rem; }
            .user-info {
                flex-direction: column;
                gap: 0.5rem;
            }
            .user-badge {
                font-size: 0.625rem;
                padding: 0.375rem 0.75rem;
            }
            .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
                width: 100%;
            }
            .main { padding: 1rem; }
            .page-title h2 { font-size: 1.5rem; }
            .schedule-grid { grid-template-columns: 1fr; gap: 1rem; }
            .schedule-card { padding: 1rem; }
            .schedule-card h4 { font-size: 1.125rem; }
            .form-group input,
            .form-group select {
                padding: 0.875rem;
                font-size: 16px;
            }
        }

        /* Mobile Landscape */
        @media (min-width: 481px) and (max-width: 768px) {
            .navbar-content { padding: 0 1.5rem; }
            .main { padding: 1.5rem; }
            .page-title h2 { font-size: 1.75rem; }
            .schedule-grid { grid-template-columns: 1fr; gap: 1.5rem; }
            .btn { min-width: 120px; }
        }

        /* Tablet Portrait */
        @media (min-width: 769px) and (max-width: 1024px) {
            .navbar-content { padding: 0 2rem; }
            .main { padding: 2rem; }
            .schedule-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {
            .btn {
                min-height: 44px;
                min-width: 44px;
            }
            .form-group input,
            .form-group select {
                min-height: 44px;
            }
            .btn:hover::after {
                display: none;
            }
        }

        /* Landscape orientation on mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            .navbar { padding: 0.5rem 0; }
            .main { padding: 1rem; }
            .schedule-grid { gap: 1rem; }
        }

        /* Accessibility & Dark Mode */
        @media (prefers-reduced-motion: reduce) {
            .schedule-card,
            .btn {
                transition: none;
            }
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: #111827;
                color: #f3f4f6;
            }
            .navbar-brand span,
            .page-title h2,
            .section h3,
            .schedule-card h4 {
                color: #f3f4f6;
            }
            .page-title p,
            .schedule-card p {
                color: #d1d5db;
            }
            .section,
            .schedule-card {
                background: #1f2937;
                border: 1px solid #374151;
            }
            .form-group label {
                color: #f3f4f6;
            }
            .form-group input,
            .form-group select {
                background: #374151;
                border-color: #4b5563;
                color: white;
            }
            .form-group input:focus,
            .form-group select:focus {
                border-color: #60a5fa;
            }
            .btn:hover::after {
                background: #374151;
            }
        }

        @media (prefers-contrast: high) {
            .schedule-card,
            .section,
            .btn {
                border: 2px solid #000;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar" role="navigation" aria-label="Main navigation">
        <div class="navbar-content">
            <a href="/index-iotcnt.html" class="navbar-brand">
                <h1>IOTCNT</h1>
                <span>Agendamentos</span>
            </a>
            <div class="navbar-nav">
                <a href="/dashboard-admin.html" class="nav-link">Dashboard</a>
                <a href="/valve-control.html" class="nav-link">Válvulas</a>
                <a href="/scheduling.html" class="nav-link active" aria-current="page">Agendamentos</a>
                <a href="/monitoring-dashboard.html" class="nav-link">Monitorização</a>
                <a href="/system-settings.html" class="nav-link">Configurações</a>
                <div class="user-info">
                    <span class="user-badge">👨‍💼 Admin</span>
                    <a href="/login-iotcnt.html" class="btn btn-secondary" data-tooltip="Terminar sessão">Sair</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="main">
        <div class="page-title">
            <h2>Agendamentos Automáticos</h2>
            <p>Gestão de ciclos de prevenção de legionela programados</p>
        </div>

        <div class="section">
            <h3>📅 Agendamentos Activos</h3>
            <div class="schedule-grid" id="schedulesGrid"></div>
        </div>
    </main>

    <script>
        const schedules = [
            {id: 1, name: 'Ciclo Diário Manhã', time: '06:00', frequency: 'daily', valves: [1,2,3,5], active: true},
            {id: 2, name: 'Ciclo Semanal Profundo', time: '02:00', frequency: 'weekly', valves: [1,2,3,4,5], active: true},
            {id: 3, name: 'Manutenção Mensal', time: '00:00', frequency: 'monthly', valves: [4], active: false}
        ];

        function renderSchedules() {
            const grid = document.getElementById('schedulesGrid');
            grid.innerHTML = schedules.map(schedule => `
                <div class="schedule-card ${schedule.active ? 'active' : ''}">
                    <h4>${schedule.name}</h4>
                    <p>Horário: ${schedule.time}</p>
                    <p>Frequência: ${translateFrequency(schedule.frequency)}</p>
                    <p>Válvulas: ${schedule.valves.join(', ')}</p>
                    <button type="button" class="btn ${schedule.active ? 'btn-warning' : 'btn-success'}"
                            data-tooltip="${schedule.active ? 'Desactivar agendamento' : 'Activar agendamento'}"
                            onclick="toggleSchedule(${schedule.id})">
                        ${schedule.active ? 'Desactivar' : 'Activar'}
                    </button>
                </div>
            `).join('');
        }

        function translateFrequency(frequency) {
            const translations = {
                'daily': 'Diário',
                'weekly': 'Semanal',
                'monthly': 'Mensal'
            };
            return translations[frequency] || frequency;
        }

        function toggleSchedule(id) {
            const schedule = schedules.find(s => s.id === id);
            schedule.active = !schedule.active;
            renderSchedules();
            // Simular chamada API
            fetch(`/api.php?action=toggleSchedule&id=${id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ active: schedule.active })
            })
            .then(response => response.json())
            .then(data => console.log('API Response:', data))
            .catch(error => console.error('API Error:', error));
        }

        document.addEventListener('DOMContentLoaded', renderSchedules);
    </script>
</body>
</html>
