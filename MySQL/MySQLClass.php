<?php

class MySQLClass {

    private $user;
    private $password;
    private $databaseTyp;
    private $host;
    private $databaseName;

    //Konstruktor
    public function __construct($user, $password, $databaseTyp, $host, $name) {
        $this->user = $user;
        $this->password = $password;
        $this->databaseTyp = $databaseTyp;
        $this->host = $host;
        $this->databaseName = $name;
    }

    public function Verbinden() {
        try {
            // DB-Aufbau über die PDO-Klasse
            $connection = new PDO("$this->databaseTyp:host=$this->host;dbname=$this->databaseName;charset=utf8", $this->user, $this->password);
            $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
        } catch (PDOException $e) {
            print_r("Error!: " . $e->getMessage() . "<br>");
            die();
        }
    }

    public function Abfragen($ConnectObject, $sql) {
        $stmt = $ConnectObject->prepare($sql);
        $GiveBackBoolean = $stmt->execute();
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row);
        }
        if (!empty($result))
            return $result;
        else
            return $GiveBackBoolean;
    }

    public function lastInsertedPK($ConnectObject) {
        return $ConnectObject->lastInsertId();
    }

}
