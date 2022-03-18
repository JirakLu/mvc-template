<?php

class Db
{

    private PDO $connection;

    public function __construct()
    {
        $this->connection = new PDO(
            "mysql:host=localhost;dbname=test;charset=utf8mb4",
            "root",
            "",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_MULTI_STATEMENTS => false
            ]
        );
    }

    /**
     * @param DbParam[] $params
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
     * @param DbParam[] $params
     * @throws Exception
     * @return array<string, object> | bool
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
     * @param DbParam[] $params
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
     * @param DbParam[] $params
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

    public function lastInsertID(): int
    {
        return 1;
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