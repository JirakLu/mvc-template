<?php

namespace App\Models\DB;

use Error;
use Nette\Neon\Exception;
use Nette\Neon\Neon;
use PDO;

class Db
{
    private PDO $connection;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (!file_exists(dirname(__DIR__, 3) . "/config/db.neon")) {
            throw new Error("db.neon config file does not exists in directory - " . dirname(__DIR__, 3) . "/config/db.neon");
        }
        $dbConfig = Neon::decodeFile(dirname(__DIR__, 3) . "/config/db.neon");

        $this->connection = new PDO(
            $dbConfig["db"]["dsn"],
            $dbConfig["db"]["username"],
            $dbConfig["db"]["password"],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_MULTI_STATEMENTS => false
            ]
        );
    }

    /**
     * Gets one row of the select from DB.
     * @param string $sql
     * @param class-string $className
     * @param DbParam[] $params
     * @return object|bool
     * @throws Exception
     */
    public function getOne(string $sql, string $className, array $params = []): object|bool
    {
        $stmt = $this->connection->prepare($sql);

        foreach ($params as $param) {
            $stmt->bindValue($param->name, $param->value, $param->type);
        }

        if (!$stmt->execute())
            throw new Exception("Dotaz {$sql} se neprovede");

        $stmt->setFetchMode(PDO::FETCH_CLASS, $className);
        return $stmt->fetch();
    }

    /**
     * Gets all rows of the select from DB.
     * @param string $sql
     * @param class-string $className
     * @param DbParam[] $params
     * @throws Exception
     * @return array<int, object> | bool
     */
    public function getAll(string $sql, string $className, array $params = []): array|bool
    {
        $stmt = $this->connection->prepare($sql);

        foreach ($params as $param) {
            $stmt->bindValue($param->name, $param->value, $param->type);
        }

        if (!$stmt->execute()) {
            throw new Exception("Dotaz {$sql} se neprovedl.");
        }

        $stmt->setFetchMode(PDO::FETCH_CLASS, $className);
        return $stmt->fetchAll();
    }

    /**
     * Gets one value from DB.
     * @param string $sql
     * @param DbParam[] $params
     * @return mixed
     * @throws Exception
     */
    public function getValue(string $sql, array $params = []): mixed
    {
        $stmt = $this->connection->prepare($sql);

        foreach ($params as $param) {
            $stmt->bindValue($param->name, $param->value, $param->type);
        }
        if (!$stmt->execute())
            throw new Exception("Dotaz {$sql} se neprovede");

        $stmt->setFetchMode(PDO::FETCH_NUM);
        return $stmt->fetch()[0];
    }

    /**
     * Executes SQL command. Useful for (UPDATE, INSERT, DROP,...)
     * @param string $sql
     * @param DbParam[] $params
     * @return int
     * @throws Exception
     */
    public function exec(string $sql, array $params = []): int
    {
        $stmt = $this->connection->prepare($sql);

        foreach ($params as $param) {
            $stmt->bindValue($param->name, $param->value, $param->type);
        }

        if (!$stmt->execute())
            throw new Exception("Dotaz {$sql} se neprovedl");

        return $stmt->rowCount();
    }

    public function lastInsertID(): string|bool
    {
        return $this->connection->lastInsertId();
    }


    /*
    PDO
        exec: int (non-Select, ideálně bez parametrů)
        query: PDOStatement/false   (select, ideálně bez parametrů)
        prepare: PDOStatement/false (dotazy s parametry, select i non-select)
        lastInsertId: int

    PDOStatement implements Traversable (= dá se používat ve foreach cyklu přímo)
        bindParam(parametr, &hodnota, typHodnoty)
        bindValue(parametr, hodnota, typHodnoty)
        execute(?paramsArray) :bool
        setFetchMode() definuje, v jaké formě se bude vracet jeden záznam
        fetch
        fetchAll

    FETCH_BOTH  pole s číselnými i stringovými klíči
    FETCH_NUM   pole pouze s číselnými klíči
    FETCH_ASSOC pole pouze se stringovými klíči, pro stejný název sloupců vrací jednu hodnotu
    FETCH_NAMED pole pouze se stringovými klíči, pro setjný název sloupců vrací kolekci hodnot
    FETCH_OBJ   objekt třídy StdClass, ve kterém vytvoří veřejné vlastnosti podle názvu sloupců
    FETCH_CLASS object specifikované třídy

    */
}