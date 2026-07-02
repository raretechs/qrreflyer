<?php

require_once __DIR__ . '/../Database/Database.php';

class Agent
{
    public static function all(): array
    {
        $db = Database::getConnection();

        $stmt = $db->query("
            SELECT *
            FROM agents
            ORDER BY active DESC, display_name ASC
        ");

        return $stmt->fetchAll();
    }
}
