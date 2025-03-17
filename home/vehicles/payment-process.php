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

$policy_details = getPolicyTypeDetails($vehiclePolicy['type']);
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

    // Validate Card Number (16 digits, must start with 4 or 5)
    if (empty($values['card-number'])) {
        $errors['card-number'] = 'Card number is required';
    } elseif (!preg_match('/^\d{16}$/', $values['card-number'])) {  // Changed from 14 to 16
        $errors['card-number'] = 'Card number must be exactly 16 digits';
    } else {
        if (str_starts_with($values['card-number'], '5')) {
            $cardType = 'MasterCard';
        } elseif (str_starts_with($values['card-number'], '4')) {
            $cardType = 'Visa';
        } else {
            $errors['card-number'] = 'Invalid card number. Must start with 4 (Visa) or 5 (MasterCard).';
        }
    }

    // Validate Card Expiry (MM/YY format)
    if (empty($values['card-expiry'])) {
        $errors['card-expiry'] = 'Card expiry is required';
    } elseif (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $values['card-expiry'])) {
        $errors['card-expiry'] = 'Invalid expiry format. Use MM/YY';
    }

    // Validate CVV (3 digits)
    if (empty($values['card-cvv'])) {
        $errors['card-cvv'] = 'Card CVV is required';
    } elseif (!preg_match('/^\d{3}$/', $values['card-cvv'])) {
        $errors['card-cvv'] = 'CVV must be exactly 3 digits';
    }

    if (empty(array_filter($errors))) {
        $data = [
            'vehicle_id' => $vehicle['id'],
            'policy_id' => $policy_id,
            'payment_date' => $currentDate,
            'end_date_payment' => $vehiclePolicy['end_date'],
            'amount_paid' => $totalPrice,
            'payment_method' => $cardType,
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
        exit;
    }
}
?>

<div style="margin-block: 7.5rem;" class="container">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="<?= SITE_URL ?>home/vehicles/view.php?id=<?= $vehicle['id'] ?>" class="btn btn-outline-primary btn-sm px-3 p-1 rounded-2">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h2 class="fs-2 text-dark text-center text-sm-start">Payment Process</h2>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm rounded-3">
                <div class="card-body p-1 rounded-3">
                    <img src="<?= getImagePath(image: $vehicle['image_path'], path: '/assets/uploads/vehicles/') ?>" class="img-responsive-1x2 mb-3 rounded-3 object-cover" width="100%" height="250px" alt="<?= $vehicle['make'] ?>">
                    <div class="px-3 pb-3">
                        <p class="fs-2 text-dark fw-bold">
                            <?= $vehicle['year'] ?> - <?= $vehicle['make'] ?> - <?= $vehicle['model'] ?>
                        </p>
                        <p class="fs-3">
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
            <div class="card shadow-sm p-3">
                <form action="" method="POST">
                    <p class="fw-bold">Payment Details</p>
                    <p class="dis mb-3">Complete your payment to activate your vehicle policy.</p>
                    
                    <div class="mb-3">
                        <p class="fw-bold">Email address</p>
                        <input class="form-control" type="email" disabled value="<?= $auth['email'] ?>">
                    </div>

                    <div class="mb-3">
                        <p class="fw-bold">Card Details</p>
                        <input type="text" class="form-control" name="card-number" placeholder="Card Number" value="<?= $values['card-number'] ?>">
                        <?= showErrors($errors['card-number']) ?>
                    </div>

                    <div class="mb-3">
    <label for="card-expiry" class="form-label">Expiry Date</label>
    <input type="text" 
           class="form-control" 
           id="card-expiry" 
           name="card-expiry" 
           placeholder="MM/YY" 
           maxlength="5" 
           pattern="(0[1-9]|1[0-2])\/\d{2}"
           title="Please use MM/YY format (e.g. 03/25)"
           autocomplete="cc-exp"
           oninput="formatExpiry(this)"
           value="<?= htmlspecialchars($values['card-expiry']) ?>">
    <div class="form-text">Enter expiry date in MM/YY format</div>
    <?= showErrors($errors['card-expiry']) ?>
</div>

<script>
function formatExpiry(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2);
    }
    input.value = value;
}
</script>


                    <div class="mb-3">
                        <input type="password" class="form-control" name="card-cvv" placeholder="CVV" maxlength="3" value="<?= $values['card-cvv'] ?>">
                        <?= showErrors($errors['card-cvv']) ?>
                    </div>

                    <button type="submit" name="submit-payment" class="btn btn-primary">Pay $<?= $totalPrice ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../config/front/footer.php'; ?>
