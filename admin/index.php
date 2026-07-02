<?php
require_once __DIR__ . '/../app/Config/Config.php';

$appName = Config::appName();
$version = Config::version();

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo e($appName); ?></title>
    <style>
        body {
            margin: 0;
            padding: 40px;
            background: #f4f4f4;
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
        }

        .wrap {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            background: #111;
            color: #fff;
            padding: 28px;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .header h1 {
            margin: 0 0 6px 0;
            font-size: 32px;
        }

        .header p {
            margin: 0;
            color: #ccc;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .card {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #222;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
        }

        .card h2 {
            margin: 0 0 8px 0;
            font-size: 22px;
        }

        .card p {
            margin: 0;
            color: #666;
            line-height: 1.4;
        }

        .disabled {
            opacity: .55;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <h1><?php echo e($appName); ?></h1>
            <p>Version <?php echo e($version); ?></p>
        </div>

        <div class="cards">
            <a class="card" href="agents.php">
                <h2>👥 Agent Manager</h2>
                <p>Add, edit, deactivate, and manage agent profiles.</p>
            </a>

            <a class="card" href="../form.php">
                <h2>🏡 Flyer Generator</h2>
                <p>Create property flyers from CRMLS exports.</p>
            </a>

            <a class="card disabled" href="#">
                <h2>📝 Templates</h2>
                <p>Manage flyer templates. Coming soon.</p>
            </a>

            <a class="card disabled" href="#">
                <h2>⚙ Settings</h2>
                <p>Configure Studio options. Coming soon.</p>
            </a>
        </div>
    </div>
</body>
</html>
