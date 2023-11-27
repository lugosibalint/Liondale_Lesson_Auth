<?php

if (isset($_POST['submit'])) {

    /* Adatbázis linkelése */
    require 'database.php';
    /* SQL INJECTION ELLENI VÉDELEM */
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);
    $passwordConfirm = mysqli_real_escape_string($connection, $_POST['passwordConfirm']);
    $country = mysqli_real_escape_string($connection, $_POST['country']);
    echo $country;
    $state = mysqli_real_escape_string($connection, $_POST['state']);
    $postal = mysqli_real_escape_string($connection, $_POST['postal']);
    $street = mysqli_real_escape_string($connection, $_POST['street']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    $address_id = 0;

    /* E-mail cím és username ellenőrzése */
    $email_Query = "SELECT * FROM users WHERE email = '$email'";
    $emailResult = mysqli_query($connection, $email_Query);
    if (mysqli_num_rows($emailResult) != 0) {
        header("Location: ../index.php?error=emailTaken");
        exit();
    }

    $user_Query = "SELECT * FROM users WHERE username = '$username'";
    $userResult = mysqli_query($connection, $user_Query);
    if (mysqli_num_rows($userResult) != 0) {
        header("Location: ../index.php?error=usernameTaken");
        exit();
    }

    /* VALIDÁLÁS */

    /* Jelszó erőssége */
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    /* Miket hagyott üresen a user */
    $errors = array(); /* Tárolom, hogy miket hagyott üresen a user */
    foreach ($_POST as $key => $value) { /* végigmegyek az átküldött adatokon */
        if ($value == "") { /* Van-e értéke a POST-ban szereplő dolgoknak? */
            if ($key != "submit") { /* Nem submit nevű gomb (ami tovább küldött ide) */
                array_push($errors, $key); /* Feltöltjük a hibás a dolgokat */
            }
        }
    }

    if (count($errors) != 0) {
        echo "Valamit üresen hagyott a user";
        foreach ($errors as $key => $value) {
            echo $value . "<br>";
        }
    } else if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        echo 'Email jaj';
    } else if (
        /* Password strength & confirm validate */
        !$uppercase ||
        !$lowercase ||
        !$number ||
        !$specialChars ||
        strlen($password) >= 8 ||
        $password !== $passwordConfirm
    ) {
        date_default_timezone_set('Europe/Budapest');
        $date = date("Y-m-d h:i:s");

        /* Address validation */

        /* 1 lekérés a streeten és ha van eredmény */
        /* 2 ha van eredmény, akkor megnézed, egyezik-e a postal code */
        /* 3 ha egyezés van, akkor megnézed hogy egyezik-e a country id */
        /* Ha bármelyikre is NEM a válasz (nincs egyezés (Else) akkor mehetsz feltöltésre) */
        /* HA VAN EGYEZÉS ÉS TELJES - akkor LEKÉRED CSAK AZ ID-t és átugrod az uploadot. */

        $addressExists = false; //Létezik-e a címtáramban az új felhasználó címe. Az a feltételezésem, hogy nem.
        //megvizsgálom.
        $addressValidateQuery = "SELECT * FROM addresses WHERE street = '$street'"; //Megpróbálod lekérni a címet
        $result = mysqli_query($connection, $addressValidateQuery);
        if (mysqli_num_rows($result) != 0) { //Van eredmény
            $adatok = mysqli_fetch_row($result); //Akkor elmented az eredményt egy változóba
            if ($adatok[2] == $postal && $adatok[1] == $country) { //Megnézed, hogy az irsz és az ország is egyezik-e.
                /* Az imént feltöltött address ID-jének a lekérése következik. */
                $address_id = $adatok[0]; //Akkor az átkérésnél megcsinált address id-t (ami nulla) átírod a megtalált, már elmentett cím ID-jére.
                var_dump($adatok);
                $addressExists = true; //Találtam az adatbázisban címet.
            }
        }

        if (!$addressExists) { //Ha találtam az adatbázisban címet, kihagyom ezt a lépést.

            /* Address upload */
            $address_Query = "INSERT INTO `addresses`(`country_id`, `postal`, `street`, `created_at`) 
            VALUES (?,?,?,?)";
            $addressStatement = mysqli_stmt_init($connection);
            if (mysqli_stmt_prepare($addressStatement, $address_Query)) {
                mysqli_stmt_bind_param($addressStatement, 'isss', $country, $postal, $street, $date);
                mysqli_stmt_execute($addressStatement);
            } else {
                echo 'address_stmt_error';
            }

            /* Az imént feltöltött address ID-jének a lekérése következik. */
            $idQuery = "SELECT MAX(id) FROM addresses";
            $result = mysqli_query($connection, $idQuery);

            $address_id = mysqli_fetch_row($result)[0]; // Az imént feltöltött ID elmentése fentre.

        }
        /* User regisztrálható */


        /* Password hashelni */
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        /*1. Sql query */
        $sql_query = "INSERT INTO `users`( `username`, `password`, `email`, `phone`,
         `address_id`, `created_at`) 
        VALUES (?,?,?,?,?,?)";
        /* 2. Statement */
        $statement = mysqli_stmt_init($connection);
        /* 3. Prepare the statement */
        if (mysqli_stmt_prepare($statement, $sql_query)) {
            /* 4. Parameter binding to statement */
            mysqli_stmt_bind_param(
                $statement,
                'ssssis',
                $username,
                $hashedPassword,
                $email,
                $phone,
                $address_id,
                $date
            );
            /* 5. Futtatjuk a szerveren a statementet.*/
            mysqli_stmt_execute($statement);
        } else {
            echo 'Prepare statement error. Preparing not works. Check SQL syntax or connection.';
        }

    } else {
        echo "password hiba";
    }

} else {
    echo "Nem lett megnyomva a gomb";
}