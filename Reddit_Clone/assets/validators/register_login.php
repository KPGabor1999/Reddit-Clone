<?php

//Regisztrációért és bejelentkezésért felelős komponens:

//*Ne felejtsd el inicializálni az adatbázist.*

//Szükséges segédosztályok beimportálása
//require_once('../utils/Storage.php');      //A komponensekben csak include_once legyen
//require_once('../utils/Auth.php');

//Felhasználói adatbázis létrehozása: regisztráció és bejelentkezés validálásához is ezt használjuk.
$user_storage = new Storage(new JsonIO('assets/json_databases/users.json'));
/*$user_storage->add([                                                                   //Adatbázis egyszeri inicializálása
    'username' => 'realkrazyxl',
    'password' => password_hash('RXL', PASSWORD_DEFAULT)
]);
$user_storage->add([
    'username' => 'H6N8XS',
    'password' => password_hash('Hallgato', PASSWORD_DEFAULT)
]);
$user_storage->add([
    'username' => 'Korom Pál Gábor',
    'password' => password_hash('me', PASSWORD_DEFAULT)
]);
$user_storage->add([
    'username' => 'VIBIN',
    'password' => password_hash('aesthetics', PASSWORD_DEFAULT)
]);
$user_storage->add([
    'username' => 'ArizonaGreenTea',
    'password' => password_hash('WithGinsengAndHoney', PASSWORD_DEFAULT)
]);*/
$user_database_handler = new Auth($user_storage);

//session elindítása
session_start();

//Regisztráció validálása: Ha még nincs ilyen nevű felhasználó, felvesszük az adatbázisba.
if(isset($_POST['username_register']) && isset($_POST['password_register'])){
    if(
        trim($_POST['username_register']) !== '' &&
        trim($_POST['password_register']) !== ''
    ){
        $new_user_data = [
            'username' => $_POST['username_register'],
            'password' => $_POST['password_register']
        ];
        if(!$user_database_handler->user_exists($new_user_data['username'])){
            $user_database_handler->register($new_user_data);
            echo "<small class='success'>Sikeres regisztráció:" .  $_POST['username_register'] . "</small><br>";
        } else {
            $errors['invalid_register'] = 'Ilyen nevű felhasználó már létezik.';
        }
    } else {
        $errors['empty_username_or_password'] = 'Hiba: Üres felhasználónév vagy jelszó.';
    }
}

//Bejelentkezés validálása: Ha már létezik ilyen nevű felhasználó a megadott jelszóval, beállítjuk a felhasználónevet a $_SESSION-be.
if(isset($_GET['username_login']) && isset($_GET['password_login'])){
    //Ne lehessen üres felhasználónévvel vagy jelszóval bejlentkezni
    $username = $_GET['username_login'];
    $password = $_GET['password_login'];
    if($user_database_handler->user_exists($username) && $user_database_handler->authenticate($username, $password)){
        $user_database_handler->login($username);
        echo "<small class='success'>Sikeres bejelentkezés: " . $_SESSION['user'] . "</small><br>";
    } else {
        $errors['invalid_login'] = 'Érvénytelen felhasználónév vagy jelszó!';
    }
}

//session leállítása debuggolási célból (valamiért csak második elküldésre működik)
if(isset($_GET['username_login']) && $_GET['username_login'] === 'session_unset()') session_unset();    //destroy helyett unset