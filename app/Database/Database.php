<?php

require_once __DIR__ . '/../Config/Config.php';

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $databasePath = Config::databasePath();
        $databaseDirectory = dirname($databasePath);

        if (!is_dir($databaseDirectory)) {
            mkdir($databaseDirectory, 0750, true);
        }

        self::$connection = new PDO('sqlite:' . $databasePath);
        self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        self::migrate(self::$connection);

        return self::$connection;
    }

    private static function migrate(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration TEXT NOT NULL UNIQUE,
                executed_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $migrationDirectory = Config::basePath() . '/database/migrations';

        if (!is_dir($migrationDirectory)) {
            return;
        }

        $migrationFiles = glob($migrationDirectory . '/*.sql');
        sort($migrationFiles);

        foreach ($migrationFiles as $migrationFile) {
            $migrationName = basename($migrationFile);

            $stmt = $db->prepare("SELECT COUNT(*) FROM migrations WHERE migration = :migration");
            $stmt->execute(['migration' => $migrationName]);

            if ((int)$stmt->fetchColumn() > 0) {
                continue;
            }

            $sql = file_get_contents($migrationFile);

            $db->beginTransaction();

            try {
                $db->exec($sql);

                $insert = $db->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
                $insert->execute(['migration' => $migrationName]);

                $db->commit();
            } catch (Throwable $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }
}
