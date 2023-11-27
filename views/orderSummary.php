<?php

/* FRONTEND AZ összegzéshez (sima táblázatba írd ki, miket vett, 
ha van kedved kísérletezni, rendezd ABC sorrendbe) */



/* Ha van kedved, a jQuery table segít hogy ne neked kelljen kódolni */

/* Utána legyen egy vissza és egy tovább gomb */

/* Majd csinlálj egy order_data.php fájlt. És ott kérd el a címet */

/* Legyen duplázva h céges vagy nem céges */

/* Csinálj neki SQL táblát belátásod szerint */
/* Order tábla és Order_Product tábla. */

/* előbbibe Számlázási cím, ha nem ugyanaz, akkor Szállítási cím, megrendelő neve, telefonszáma, email címe, sumTotal  */
/* Jövőre mutatva, ha regisztrált felhasználó, akkor az ID, ez legyen az utolsó
amit csinálsz de legyen a táblába,lehet NULL */

/* Utóbbiba, order_id, product_id, qty, price */





require '../layouts/header.php';
require '../controllers/database.php';





session_name("Lion2023-WebApp");
session_start();



?>


<p class="text-start">
    <a href="./index.php" type="button" class="link-success link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Back to shopping</a>
</p>
<div class="row mt-4 mb-2">
    <div class="container">
        <div class="card">
            <table class="table">
                <div class="card-body text-center"><h4 class="card-title">Summary</h4></div>
                
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $total = 0;
                    if (isset($_SESSION['cart'])) { /* Ha van a sessionben kosár */

                        foreach ($_SESSION['cart'] as $key => $product) { /* Akkor végigmegyek az összes elemén és megcsinálom a táblázat sorait */
                            $total += $product['qty'] * $product['price'];
                            echo '<tr>
                <td scope="row">' . $product['id'] . '</td>
                <td>' . $product['name'] . '</td>
                <td><input disabled type="number" value="' . $product['qty'] . '" class="form-control" name="cartQty" onchange="changeHiddenQty(this, ' . $key . ')"></td>
                <td>' . $product['qty'] * $product['price'] . '</td>
                </tr>';
                        }
                    }

                    ?>
                    <tr>
                        <td colspan="3"> Össszesen: </td>
                        <td>
                            <?php echo " ($" . $total * 0.73 . " + VAT)<br> <b>$" . $total . " </b>" ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mb-3"></div>
<p class="text-end">
    <a href="../controllers/order_data.php" type="button" class="link-success link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Continue to paying</a>
</p>

<?php require '../layouts/footer.php'; ?>