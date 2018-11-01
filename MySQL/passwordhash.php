<!Doctype html> <!-- Definition des doctype-Modus -->
<!-- Definition des Stammverzeichnises -->
<head> <!-- Definition des Kopfbereiches -->
    <meta charset="utf-8"> <!-- charset[utf-8:]  definiert den deutschen Zeichensatz -->
    <title>Login check</title>
    <!-- Das neueste kompilierte und minimierte CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <!-- Online JQuery Bibliotheken. Werden zwar nicht benötigt, können aber auch nicht schaden... -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.17.0/jquery.validate.js" type="text/javascript" charset="utf-8"></script>
</head>
<?php
error_reporting(E_ALL ^ E_NOTICE);

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
$DatabaseObject = new MySQLClass('root', '', 'mysql', 'localhost', 'userScript');
$connect = $DatabaseObject->Verbinden();
if ($connect) {
    print_r("MySQL-Aufbau wurde soeben initialisiert");
} else {
    print_r("MySQL-Aufbau ist gescheitert");
    die();
}
?>
<?php
//Submittbutton gedrückt und kein Radiobutton gewählt?
$message = '';
if ($_POST['typ'] == null) {
    $message = 'Please, choose an option about radion buttons';
}
//Submittbutton gedrückt und Registration
if ($_POST['typ'] == 'insert' && isset($_POST['login']) && (empty($_POST['username']) || empty($_POST['password']))) {
    $message = 'Please, fill in all data to get registrated';
} else if ($_POST['typ'] == 'insert' && isset($_POST['login']) && (!empty($_POST['username']) && !empty($_POST['password']))) {
    $password = $_POST['password'];
    $gehashtes_passwort = password_hash($password, PASSWORD_DEFAULT);
    $sql1 = "INSERT INTO benutzer (username,password)
                        VALUES ('" . ($_POST['username']) . "',
		'" . ($gehashtes_passwort) . "')";
    try {
        //$DatabaseObject->Transaction($connect);
        $query1 = $DatabaseObject->Abfragen($connect, $sql1);
        //$DatabaseObject->Commit($connect);
        if (is_bool($query1)) {
            ?>
            <font size='5' color='#FA5858'><p> Die Eingaben wurden in die Datenbank eingefügt. Die Id des Datensatzes ist <?= $DatabaseObject->lastInsertedPK($connect) ?></p></font>
            <?php
        } else {
            ?>
            <font size='5' color='#FA5858'><p> Es konnten keine Datensätze eingefügt werden.</p></font>
            <?php
        }
    } catch (Exception $e) {
        $DatabaseObject->Rollback($connect);
        print_r($e->getMessage());
    }
}
//Submittbutton gedrückt und Login
if ($_POST['typ'] == 'login' && isset($_POST['login']) && (empty($_POST['username']) || empty($_POST['password']))) {
    $message = 'Please, fill in all data to get entered';
} else if ($_POST['typ'] == 'login' && isset($_POST['login']) && (!empty($_POST['username']) && !empty($_POST['password']))) {
    $sql2 = 'SELECT username,password FROM benutzer';
    $query2 = $DatabaseObject->Abfragen($connect, $sql2);
    if ($query2 && !is_array($query2)) {
        ?>
        <font size='5' color='#FA5858'><p> Da keine Benutzer vorhanden sind, können sie sich nicht einloggen. Legen Sie welche an!</p></font>
        <?php
    } else {
        foreach ($query2 as $record) {
            if ($_POST['username'] == $record['username'] && password_verify($_POST['password'], $record['password'])) {
                header("Location: http://localhost/MySQL/MySQL/correct_passsword.php");
            } else {
                $message = 'Wrong username or password';
            }
        }
    }
}
//Submittbutton gedrückt und letzten Benutzer löschen
if ($_POST['typ'] == 'delete') {
    $sql4 = 'SELECT MAX(id) FROM benutzer';
    $query4 = $DatabaseObject->Abfragen($connect, $sql4);
    try {
        if (is_array($query4)) {
            foreach ($query4 as $record) {
                $Id2Delete = $record['MAX(id)'];
            }
            $DatabaseObject->Transaction($connect);
            if ($Id2Delete != null) {
                $sql5 = "DELETE FROM benutzer WHERE id=$Id2Delete";
                $query5 = $DatabaseObject->Abfragen($connect, $sql5);
                $DatabaseObject->Commit($connect);
                if ($query5) {
                    ?>
                    <font size='5' color='#FA5858'><p> Der Benutzer mit der Id  <?= $Id2Delete ?> wurde soeben entfernt.</p></font>
                    <?php
                }
            } else {
                ?>
                <font size='5' color='#FA5858'><p> Da keine Benutzer vorhanden sind, können auch keine gelöscht werden.</p></font>
                <?php
            }
        }
    } catch (Exception $e) {
        $DatabaseObject->Rollback($connect);
        print_r($e->getMessage());
    }
}
?>
<body>
<center>
    <div class="page-header">
        <h3>Login bzw. Registrationsscript <small>in Verbindung mit PHP baut diese WebSite eine Verbindung zu einer Datenbank auf und überprüft ggf.Ihre Eingaben</small></h3>
    </div>
</center>
<div class = "container form-signin">
    <div class = "container">
        <div id="signup">
            <form name="formular" action = "<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "post">
                <div class="row">
                    <div class="col-md-12">
                        <p><?= "<font color='#FE2E9A'>" . $message . "</font>"; ?></p>
                    </div>
                    <h3 class="alert alert-success">Enter Username and Password</h3>
                    <div class="col-md-3">
                        <input type = "text" name = "username" placeholder = "username" autofocus>
                    </div>
                    <div class="col-md-3">
                        <input type = "password" class = "form-control"
                               name = "password"  id="password" placeholder = "password">
                    </div>
                    <div class="col-md-3">
                        <button class = "btn btn-info" type = "submit" name = "login">Submit</button>
                    </div>
                    <div class="col-md-3">
                        <input type="radio" name="typ" value="insert"> Registration<br>
                        <input type="radio" name="typ" value="login"> Login<br>
                        <input type="radio" name="typ" value="show"> Benutzer anzeigen<br>
                        <input  type="radio" name="typ" value="delete">letzten Benutzer löschen<br>
                    </div>
                </div>
            </form>
            <div class="col-md-12">
                <?php
// Submittbutton gedrückt und Benutzer anzeigen
                if ($_POST['typ'] == 'show') {
                    ?>
                    <p>Folgende Benutzer sind in der Datenbank vorhanden:</p>
                    <?php
                    $sql3 = 'SELECT id,username,password FROM benutzer';
                    $query3 = $DatabaseObject->Abfragen($connect, $sql3);
                    if (is_array($query3)) {
                        foreach ($query3 as $record) {
                            ?>
                            <ul>
                                <li><?= $record['username'] . '(Id:' . $record['id'] . ')' . ', Hash:' . $record['password'] ?></li>
                            </ul>
                            <?php
                        }
                    } else if (is_bool($query3)) {
                        ?>
                        <font size='5' color='#FA5858'><p> Es konnten keine User gefunden werden.</p></font>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
</body>





