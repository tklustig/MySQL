<?php

function classAutoloader($class) {
    $path = "$class.php";
    if (file_exists($path)) {
        require $path;
    } else {
        print_r("Klasse exisitert nicht");
        die();
    }
}

spl_autoload_register('classAutoloader');
$DatabaseObject = new MySQLClass('root', '', 'mysql', 'localhost', 'yii2_KanatImmo');
$connect = $DatabaseObject->Verbinden();
if ($connect) {
    print_r("MySQL-Aufbau wurde soeben initialisiert");
} else
    print_r("MySQL-Aufbau ist gescheitert");

$sql = 'SELECT COUNT(id) FROM immobilien';
$query = $DatabaseObject->Abfragen($connect, $sql);
if (is_array($query) && !empty($query)) {
    ?>
    <font size='5' color='#FA5858'><p> Es wurden <?= $query[0]["COUNT(id)"] ?> Datensätze gefunden</p></font>
    <?php
} else {
    ?>
    <font size='5' color='#FA5858'><p> Es wurden keine Datensätze gefunden</p></font>
    <?php
}
?>
<?php
$sql = 'SELECT plz, stadt,strasse,geldbetrag FROM immobilien LEFT JOIN l_plz ON immobilien.l_plz_id=l_plz.id';
$query = $DatabaseObject->Abfragen($connect, $sql);
foreach ($query as $record) {
    ?>
    <ul>
        <li> <?= $record['plz'] . " " . $record['stadt'] . ", " . $record['strasse'] . ", Kosten:" . $record['geldbetrag'] . "€" ?></li>
    </ul>
    <?php
}
$query = $DatabaseObject->lastInsertedPK($connect);
if ($query == 0)
    print_r('<br><br>Es wurde kein neuer Record erkannt. Ändern Sie das Query!');
else
    print_r($query);
?>

