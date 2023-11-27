<?php

require '../layouts/header.php';
require '../controllers/database.php';
/* Lekérni az összes terméket, az összes adattal az adatbázisból és eltárolni egy result változóban. */

/* Lekérdezés, vonatkozó inner joinnal ha van. */
$query = "SELECT * FROM products";

/* Where feltételek az első szűrés */
if (isset($_GET["cat"]) && $_GET["cat"] != '#') {
  $query = $query . " WHERE type = '" . $_GET["cat"] . "'";
}

/* Order by szerinti szűrés, mindig a végén */
if (isset($_GET["filter"])) {
  $query = $query . " ORDER BY price " . $_GET["filter"];
}

$result = mysqli_query($connection, $query) or die(mysqli_error($connection));

?>

<div class="landing-video">
  <video src="../assets/videos/landing.mp4" loop muted autoplay>
  </video>
</div>

<div class="row">
  <form action="#" method="GET">
    <div class="row">
      <div class="col-4">
        <select name="filter" id="filter" class="form-select">
          <option value="" selected disabled>Kérlek válassz</option>
          <option value="asc">Ár szerint növekvő</option>
          <option value="desc">Ár szerint csökkenő</option>
        </select>
      </div>
      <div class="col-2">
        <button type="submit" name="submit" class="btn btn-warning">Szűrés</button>
        <input type="hidden" name="cat" value="<?php echo isset($_GET['cat']) ? $_GET['cat'] : "#" ?>">
      </div>
    </div>
  </form>
</div>

<div class="row">
  <!-- A PHP kód beillesztése, ami egy while-lal amíg van sor a resultben, végigmegy és kiechozza a div class col-3at és tartalmát  -->

  <?php

  while ($row = mysqli_fetch_assoc($result)) {
    echo '<div class="col-lg-3 col-md-4 col-sm-6">
    <div class="card border-primary m-3">
      <img class="card-img-top img-fluid" src="../assets/images/' . $row["picture"] . '" alt="Placeholder">
      <div class="card-body">
        <h4 class="card-title text-truncate">' . $row["name"] . '</h4>
        <p class="card-text text-truncate">' . $row["description"] . '</p>
          <p class="mt-1 text-center text-success">
            Price: $' . $row["price"] . '
          </p>
      </div>
      <div class="mt-2 mb-2 text-center">
        <form action="../controllers/cart.php" method="POST">
          <input class="form-control" type="number" name="qty" id="qty" value="1">
          <button class="mt-2 cartIcon" name="submit" id="submit" type="submit"><i class="fa-solid fa-cart-shopping"></i></button>
          <input type="hidden" name="productId" id="productId" value="' . $row["id"] . '">
        </form>
      </div>
    </div>
  </div>';
  }
  ?>

  <!--  -->
</div>

<div class="row">
  <div class="col-12">
    <div class="table-responsive">
      <table class="table table-striped
      table-hover	
      table-borderless
      table-warning
      align-middle">
        <thead class="table-light">
          <tr>
            <th>Id</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody class="table-group-divider">ű

          <!-- Sor eleje -->
          <?php
          $total = 0;
          if (isset($_SESSION['cart'])) { /* Ha van a sessionben kosár */
            foreach ($_SESSION['cart'] as $key => $product) { /* Akkor végigmegyek az összes elemén és megcsinálom a táblázat sorait */
              $total += $product['qty'] * $product['price'];
              echo '<tr>
            <td scope="row">' . $product['id'] . '</td>
            <td>' . $product['name'] . '</td>
            <td><input type="number" value="' . $product['qty'] . '" class="form-control" name="cartQty" onchange="changeHiddenQty(this, ' . $key . ')"></td>
            <td>' . $product['qty'] * $product['price'] . '</td>
            <td>
              <div class="row">
                <div class="col-2">
                  <form action="../controllers/updateCart.php" method="POST">
                    <button class="actionButton"><i class="fa-solid fa-arrows-rotate me-3"></i></button>
                    <input type="hidden" name="productId" id="productId" value="' . $key . '">
                    <input type="hidden" name="hiddenQty" id="hiddenQty_' . $key . '" value="' . $product['qty'] . '">
                  </form>
                </div>
                <div class="col-2">
                  <form action="../controllers/deleteFromCart.php" method="POST">
                    <button class="actionButton"><i class="fa-solid fa-trash"></i></button>
                    <input type="hidden" name="productId" id="productId" value="' . $key . '">
                  </form>
                </div>
              </div>
            </td>
          </tr>';
            }
          }
          ?>
          <!-- Sor vége  -->

          <!-- Összesen -->
          <tr>
            <td colspan="4"> Össszesen: </td>
            <td>
              <?php echo "$" . $total . " - ($" . $total * 0.73 . " + VAT)" ?>
            </td>
          </tr>
          <tr>
            <td colspan="4"></td>
            <td>
              <a href="orderSummary.php" class="btn btn-outline-info">Rendelés véglegesítése</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  function changeHiddenQty(input, key) {
    let hiddenQty = document.getElementById("hiddenQty_" + key);
    hiddenQty.value = input.value;
  }</script>

<?php require '../layouts/footer.php'; ?>