<?php

namespace Clases;

class Conexion
{
    public static function con()
    {
        $conexion = mysqli_connect($_ENV["DB_HOST"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);
        mysqli_set_charset($conexion, 'utf8');
        return $conexion;
    }

    public static function conPDO()
    {
        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $dsn = "mysql:host=" . $_ENV["DB_HOST"] . ";dbname=" .  $_ENV["DB_NAME"] . ";charset=utf8";
        try {
            $pdo = new \PDO($dsn, $_ENV["DB_USER"], $_ENV["DB_PASS"], $options);
            return $pdo;
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function sql($query)
    {
        self::con()->query($query);
        self::con()->close();
    }

    public static function sqlReturn($query)
    {
        $dato = self::con()->query($query);
        self::con()->close();
        return $dato;
    }
}
