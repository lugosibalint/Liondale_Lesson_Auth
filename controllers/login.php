<?php

/* Adatbázis behívása */
require 'database.php';

/* Elnevezem a böngészőben a sessiont és elindítom */
session_name("Lion2023-WebApp");
session_start();

/* Dev User
    Rick / Rick1234!
*/

echo 'Attemting to login...';

if (isset($_POST['submit'])) {
    
    /* Login Form inputjainak átkérése és SQL INJ védelem. */
     $username = mysqli_real_escape_string($connection, $_POST['username']);
     $password = mysqli_real_escape_string($connection, $_POST['password']);

     echo "<br> - Attemting to log in $username";
     echo "<br> - His password is: $password <br>";

     /* Felhasználó összes adatának lekérése */
     $query = "SELECT * FROM users WHERE username = '$username'";
     $result = mysqli_query($connection, $query);

     var_dump($result);

     /* Felhasználó ellenőrzése (van, nincs, túl sok van) */
    if (mysqli_num_rows($result) === 1) {
        
        /* User összes adatának lekérése */
        $user = mysqli_fetch_assoc($result);

        echo '<br> - A lekért user adatai: <br>';
        var_dump($user);

        /* User hashelt jelszavának lekérése */
        $hash = $user['password'];

        /* User beírt és az adatbázisban hashelt jelszavának összehasonlítása */
        $verify = password_verify($password, $hash);

        if ($verify === true) {
            
            /* Bejelentkeztetés a SESSION használatával */
            $_SESSION["user"] = $user['username'];
            $_SESSION["email"] = $user['email'];

            header("Location: ../views/index.php?login=success");
            die("<br> User is logged in.");

        }
        else {
            die("A jelszavak nem egyeznek");
        }

    }
    else if(mysqli_num_rows($result) === 0 ){
        die('Nincs ilyen felhasználó');
    }
    else {
        die('Critical Failure - Több felhasználó ugyanazon néven');
    }
    
} else {
    die('A gomb nem lett megnyomva.');
}

/* Leeellenőrizni, hogy van -e ilyen user (kapunk eredményt a querytől?) */
/* A jelszó hitelesítése (megnézzük hogy a hashelt és a beírt jelszó ugyanez) */
/* Bejelentkezés (lásd később) */