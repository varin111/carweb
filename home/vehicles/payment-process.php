<?php
require_once __DIR__ . '/../../config/front/header.php';
if (empty(getSession('user_id')) && empty($_COOKIE['user_login'])) {
    header("Location: " . SITE_URL . "login.php");
    exit;
}
$currentDate = date('Y-m-d H:i:s');
$params = getUrlParams($_SERVER['REQUEST_URI']);
$token = data_get($params, 'token', random_string(32));
$policy_id = data_get($params, 'policy_id', 0);
$vehicle_id = data_get($params, 'vehicle_id', 0);
$vehicle = query_select('vehicles', '*', "id = $vehicle_id AND user_id = $auth[id]");
if (empty($vehicle)) {
    setSession(
        'vehicle-policy-action',
        [
            'type' => 'danger',
            'message' => 'Vehicle not found.'
        ]
    );
    header("Location: " . SITE_URL . "home/vehicles/view.php?id=$vehicle_id");
    exit;
}
$id = data_get($params, 'id', 0);
$vehiclePolicy = query_select('policies', '*', "id = $policy_id AND status = 'enable' AND start_date <= '$currentDate' AND end_date >= '$currentDate'");
if (empty($vehiclePolicy)) {
    setSession(
        'vehicle-policy-action',
        [
            'type' => 'danger',
            'message' => 'Vehicle policy not found.'
        ]
    );
    header("Location: " . SITE_URL . "home/vehicles/view.php?id={$vehicle['id']}");
    exit;
}
$totalPrice = $vehiclePolicy['premium_amount'];
$payment = query_select('payments', '*', "policy_id = $policy_id AND vehicle_id = {$vehicle['id']}");
if (isset($payment['status']) && $payment['status'] == 'success') {
    setSession(
        'vehicle-policy-action',
        [
            'type' => 'success',
            'message' => 'Payment has been successfully processed.'
        ]
    );
    header("Location: " . SITE_URL . "home/vehicles/view.php?id=$vehicle_id");
    exit;
}

$errors = [
    'card-number' => '',
    'card-expiry' => '',
    'card-cvv' => ''
];
$values = [
    'card-number' => '',
    'card-expiry' => '',
    'card-cvv' => ''
];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-payment'])) {
    $values['card-number'] = clear($_POST['card-number']);
    $values['card-expiry'] = clear($_POST['card-expiry']);
    $values['card-cvv'] = clear($_POST['card-cvv']);

    if (empty($values['card-number'])) {
        $errors['card-number'] = 'Card number is required';
    } elseif (validate_string($values['card-number'], 1, 255) === false) {
        $errors['card-number'] = 'Card number must be a string and between 1 and 255 characters';
    }

    if (empty($values['card-expiry'])) {
        $errors['card-expiry'] = 'Card expiry is required';
    }

    if (empty($values['card-cvv'])) {
        $errors['card-cvv'] = 'Card cvv is required';
    } elseif (validate_string($values['card-cvv'], 1, 3) === false) {
        $errors['card-cvv'] = 'Card cvv must be between 1 and 3 numbers';
    }

    if (empty(array_filter($errors))) {
        $data = [
            'vehicle_id' => $vehicle['id'],
            'policy_id' => $policy_id,
            'payment_date' => $currentDate,
            'amount_paid' => $totalPrice,
            'payment_method' => 'card',
            'payment_status' => 'success',
        ];
        $VehicleBalance = $vehicle['balance'] + $totalPrice;
        query_insert('payments', $data);
        query_update('vehicles', ['balance' => $VehicleBalance], "id = {$vehicle['id']}");
        setSession('vehicle-policy-action', [
            'type' => 'success',
            'message' => 'Payment has been successfully processed.'
        ]);
        header("Location: " . SITE_URL . "home/vehicles/view.php?id=$vehicle_id");
    }
}

?>
<div style="margin-block: 1.5rem;" class="container">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="<?= SITE_URL ?>home/vehicles/view.php?id=<?= $vehicle['id'] ?>" class="btn btn-outline-primary btn-sm px-3 p-1 rounded-2">
            <i class="fas fa-arrow-left me-1"></i>
            Back
        </a>
        <h2 class="fs-2 text-dark text-center text-sm-start">Payment Process</h2>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm rounded-3">
                <div class="card-body p-1 rounded-3">
                    <img src="<?= getImagePath(image: $vehicle['image_path'], path: '/assets/uploads/vehicles/') ?>" class="img-responsive-1x2 mb-3 rounded-3 object-cover" width="100%" height="250px" alt="<?= $vehicle['make'] ?>">
                    <div class="px-3 pb-3 text-truncate">
                        <p class="fs-2 text-dark text-decoration-none fw-bold">
                            <?= $vehicle['year'] ?> - <?= $vehicle['make'] ?> - <?= $vehicle['model'] ?>
                        </p>
                        <p class="card-text fs-3 mt-2">
                            <strong>License Plate:</strong> <?= $vehicle['license_plate'] ?><br>
                            <strong>Color:</strong> <?= $vehicle['color'] ?><br>
                            <strong>Mileage:</strong> <?= $vehicle['mileage'] ?> miles<br>
                            <strong>VIN:</strong> <?= $vehicle['vin'] ?><br>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm p-3 box-inner-2">
                <div>
                    <p class="fw-bold">Payment Details</p>
                    <p class="dis mb-3">Complete your payment to activate your vehicle policy.</p>
                    <p class="dis mb-3">Policy : <?= $vehiclePolicy['coverage_type'] ?></p>
                </div>
                <form
                    action="<?= SITE_URL ?>home/vehicles/payment-process.php?token=<?= $token ?>&policy_id=<?= $policy_id ?>&vehicle_id=<?= $vehicle_id ?>&id=<?= $id ?>"
                    method="POST">
                    <div class="mb-3">
                        <p class="dis fw-bold mb-2">Email address</p>
                        <input class="form-control" type="email" disabled value="<?= $auth['email'] ?>">
                    </div>
                    <div>
                        <p class="dis fw-bold mb-2">Card details</p>
                        <div class="d-flex align-items-center justify-content-between card-atm border rounded">
                            <div class="fab fa-cc-visa ps-3"></div>
                            <input type="text" class="form-control border-0 " placeholder="Card Details"
                                name="card-number"
                                id="card-number"
                                value="<?= $values['card-number'] ?>">
                            <div class="d-flex w-50">
                                <div>
                                    <input type="text" class="form-control px-0" placeholder="MM/YY"
                                        name="card-expiry"
                                        value="<?= $values['card-expiry'] ?>">
                                    <?= showErrors($errors['card-expiry']) ?>
                                </div>
                                <div>
                                    <input type="password" maxlength=3 class="form-control px-0" placeholder="CVV"
                                        name="card-cvv"
                                        value="<?= $values['card-cvv'] ?>">
                                    <?= showErrors($errors['card-cvv']) ?>
                                </div>
                            </div>
                        </div>
                        <?= showErrors($errors['card-number']) ?>
                        <div class="mt-3">
                            <div class="d-flex flex-column dis">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <p>Subtotal</p>
                                    <p>
                                        <span class="fas fa-dollar-sign"></span>
                                        <?= $totalPrice ?>
                                    </p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <p class="fw-bold">Total</p>
                                    <p class="fw-bold">
                                        <span class="fas fa-dollar-sign"></span>
                                        <?= $totalPrice ?>
                                    </p>
                                </div>
                                <button
                                    type="submit"
                                    name="submit-payment"
                                    class="btn btn-primary mt-2">
                                    Pay
                                    <span class="fas fa-dollar-sign px-1"></span>
                                    <?= $totalPrice ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . '/../../config/front/footer.php';
