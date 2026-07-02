<?php

class Config
{
    public static function basePath(): string
    {
        return realpath(__DIR__ . '/../..');
    }

    public static function storagePath(): string
    {
        return self::basePath() . '/storage';
    }

    public static function databasePath(): string
    {
        return self::storagePath() . '/data/app.db';
    }

    public static function version(): string
    {
        $versionFile = self::basePath() . '/VERSION';
        return file_exists($versionFile) ? trim(file_get_contents($versionFile)) : 'dev';
    }

    public static function appName(): string
    {
        return 'Castra Realty Marketing Studio';
    }
}
