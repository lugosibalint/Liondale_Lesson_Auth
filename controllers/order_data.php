<?php 




require '../layouts/header.php';
require '../controllers/database.php';




session_name("Lion2023-WebApp");
session_start();



if (isset($_SESSION["user"])) {
    # code...
}

?>

<div class="row mt-4 mb-2">
    <div class="container">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title">Order datas</h4>
                <!-- Form kezdés -->
                <form action="../controllers/order_data.php" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="firstname" id="firstname"
                            placeholder="First Name" data-parsley-minlength="3" required>
                        <label for="firstname">First name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="lastname" id="lastname"
                            placeholder="Last Name" data-parsley-minlength="3" required>
                        <label for="lastname">Last name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com"
                            data-parsley-type="email" required>
                        <label for="email">Email address</label>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" placeholder="" data-mask="(99) 999-9999" class="form-control" name="phone">
                        <span class="font-13 text-muted">(999) 999-9999</span>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>





<?php require '../layouts/footer.php'; ?>