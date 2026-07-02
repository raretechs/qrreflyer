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
            CREATE TABLE IF NOT EXISTS agents (
                id INTEGER PRIMARY KEY AUTOINCREMENT,

                slug TEXT NOT NULL UNIQUE,
                display_name TEXT NOT NULL,

                first_name TEXT,
                last_name TEXT,

                email TEXT,
                phone TEXT,
                mobile TEXT,

                dre_license TEXT,

                website TEXT,

                photo_path TEXT,

                bio TEXT,

                facebook TEXT,
                instagram TEXT,
                linkedin TEXT,
                youtube TEXT,

                active INTEGER NOT NULL DEFAULT 1,

                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }
}
