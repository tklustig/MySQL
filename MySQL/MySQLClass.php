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

    public function Abfragen($connection, $sql) {
        $pConn = $connection->prepare($sql);
        $GiveBackBoolean = $pConn->execute();
        $result = array();
        while ($row = $pConn->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row);
        }
        if (!empty($result) && is_array($result))
            return $result;
        else
            return $GiveBackBoolean;
    }

    public function lastInsertedPK($connection) {
        /* Im Zusammenhang mit Transaktionen funktioniert diese Methode nicht (lt. der PHP Manuals:http://de2.php.net/manual/en/pdo.lastinsertid.php */
        return $connection->lastInsertId();
    }

    public function Transaction($connection) {
        return $connection->beginTransaction();
    }

    public function Commit($connection) {
        try {
            $connection->commit();
            return true;
        } catch (Exception $e) {
            print_r($e->getMessage());
            return false;
        }
    }

    public function Rollback($connection) {
        try {
            $connection->rollback();
            return true;
        } catch (Exception $e) {
            print_r($e->getMessage());
            return false;
        }
    }

}
