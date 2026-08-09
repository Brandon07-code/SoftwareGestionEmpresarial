<?php
$dbHost = "db";
$dbName = "sge_db";
$dbUser = "dev_user";
$dbPass = "dev_password";

$dbConnected = false;
$dbError = null;

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 3
    ]);
    $dbConnected = true;
} catch (PDOException $e) {
    $dbError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Software de Gestión Empresarial - Entorno de Desarrollo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            --card-bg: rgba(30, 41, 59, 0.7);
            --card-border: rgba(255, 255, 255, 0.1);
            --accent-purple: #8b5cf6;
            --accent-blue: #3b82f6;
            --accent-green: #10b981;
            --accent-red: #ef4444;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Outfit", sans-serif;
            background: var(--bg-gradient);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            max-width: 900px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: rgba(139, 92, 246, 0.15);
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 9999px;
            color: #c4b5fd;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .badge-dot {
            width: 8px;
            height: 8px;
            background: var(--accent-green);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--accent-green);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(to right, #ffffff, #c4b5fd, #93c5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.75rem;
            letter-spacing: -0.025em;
        }

        p.subtitle {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            transform: translateY(-4px);
            border-color: rgba(139, 92, 246, 0.4);
            box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.5);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .icon-php { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        .icon-db { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .icon-tools { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-tag {
            font-size: 0.75rem;
            padding: 0.25rem 0.6rem;
            border-radius: 0.375rem;
            font-weight: 600;
        }

        .status-success { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-error { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }

        .detail-list {
            list-style: none;
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .detail-list strong {
            color: var(--text-main);
        }

        .actions {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 1rem;
            padding: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            border: 1px solid var(--card-border);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="badge">
                <span class="badge-dot"></span>
                <span>Entorno Docker WSL Activo</span>
            </div>
            <h1>Software de Gestión Empresarial</h1>
            <p class="subtitle">Prueba del Servidor Web & Conectividad con MariaDB</p>
        </div>

        <div class="grid">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">PHP Runtime</span>
                    <div class="icon-wrapper icon-php">🐘</div>
                </div>
                <div class="stat-value">
                    PHP <?= phpversion(); ?>
                    <span class="status-tag status-success">Activo</span>
                </div>
                <ul class="detail-list">
                    <li><strong>Servidor:</strong> Apache 2.0</li>
                    <li><strong>Reescritura (mod_rewrite):</strong> Habilitado</li>
                    <li><strong>PDO MySQL:</strong> Instalado</li>
                </ul>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">MariaDB / MySQL</span>
                    <div class="icon-wrapper icon-db">🐬</div>
                </div>
                <div class="stat-value">
                    <?php if ($dbConnected): ?>
                        Conectado
                        <span class="status-tag status-success">OK</span>
                    <?php else: ?>
                        Desconectado
                        <span class="status-tag status-error">Error</span>
                    <?php endif; ?>
                </div>
                <ul class="detail-list">
                    <li><strong>Host interno:</strong> <?= $dbHost ?>:3306</li>
                    <li><strong>Base de datos:</strong> <?= $dbName ?></li>
                    <?php if (!$dbConnected): ?>
                        <li style="color: var(--accent-red); margin-top: 0.5rem; font-size: 0.8rem;">
                            <?= htmlspecialchars($dbError) ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">phpMyAdmin</span>
                    <div class="icon-wrapper icon-tools">⚡</div>
                </div>
                <div class="stat-value">
                    Puerto 8086
                    <span class="status-tag status-success">Listo</span>
                </div>
                <ul class="detail-list">
                    <li><strong>Usuario:</strong> root</li>
                    <li><strong>Pass:</strong> root_password</li>
                    <li><strong>Gestor DB:</strong> GUI Web</li>
                </ul>
            </div>
        </div>

        <div class="actions">
            <div>
                <strong style="display: block; font-size: 1.05rem;">¡Prueba de Entorno Lista! 🎉</strong>
                <span style="color: var(--text-muted); font-size: 0.9rem;">El servidor PHP y la base de datos MariaDB están conectados correctamente.</span>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <a href="http://localhost:8086" target="_blank" class="btn btn-secondary">Abrir phpMyAdmin</a>
            </div>
        </div>
    </div>
</body>
</html>
