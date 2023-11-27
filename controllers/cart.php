<?php

session_name("Lion2023-WebApp");
session_start();

require_once "database.php";

/* Ha van a sessionben Cart lekérem, ha nincs, újat csinálok */
if (isset($_SESSION["cart"])) {
    $cart = $_SESSION["cart"];
} else {
    $cart = array();
}

/* Ha a kosár gombot megnyomva jöttem ide */
if (isset($_POST["submit"])) {
    /* Injection védelem */
    $productId = mysqli_real_escape_string($connection, $_POST["productId"]);
    $qty = mysqli_real_escape_string($connection, $_POST["qty"]);

    /* Lekérés és a talált termék elmentése */
    $query = "SELECT * FROM products WHERE id = $productId";
    $result = mysqli_query($connection, $query);
    $product = mysqli_fetch_assoc($result);

    /* A termék benne van-e a kosárban már? Ha igen mennyiséget adok hozzá, ha nem beleteszem */
    $inCart = false;
    foreach ($cart as $key => $cartProduct) {
        if ($cartProduct["id"] == $product["id"]) {
            $cart[$key]["qty"] += $qty;
            $inCart = true;
            break;
        }
    }
    if (!$inCart) {
        //Mennyiség hozzáadása a lekért termékről szóló tömbhöz
        $product["qty"] = $qty;
        //Termék tömb hozzáadása a kosár tömbhöz.
        array_push($cart, $product);
    }

    /* A helyi kosár hozzáadása a sessionhöz */
    $_SESSION['cart'] = $cart;

    /* Vissza a vásárlós oldalra */
    header('Location: ../views/index.php?msg=cartSuccess');
}