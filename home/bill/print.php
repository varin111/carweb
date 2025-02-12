<?php
require_once __DIR__ . '/../../config/front/header.php';
if (empty(getSession('user_id')) && empty($_COOKIE['user_login'])) {
    header("Location: " . SITE_URL . "login.php");
    exit;
}
$currentDate = date('Y-m-d H:i:s');
$params = getUrlParams($_SERVER['REQUEST_URI']);
$token = data_get($params, 'token', random_string(32));
$payment_id = data_get($params, 'payment_id', 0);

$payment =
    query_select_with_join(
        table: 'payments',
        join: ' JOIN vehicles ON payments.vehicle_id = vehicles.id 
        JOIN policies ON payments.policy_id = policies.id 
        JOIN users ON vehicles.user_id = users.id',
        columns: 'DISTINCT payments.*, 
            vehicles.license_plate as vehicle_license_plate,
            vehicles.make as vehicle_make,
            vehicles.model as vehicle_model,
            vehicles.vin as vehicle_vin,
            vehicles.color as vehicle_color,
            policies.coverage_type as policy_coverage_type,
            policies.type as policy_type,
            users.name as user_name,
            users.email as user_email',
        where: "payments.id = $payment_id",
    );

if (empty($payment) || count($payment) === 0) {
    setSession(
        'vehicle-policy-action',
        [
            'type' => 'danger',
            'message' => 'Payment not found',
        ]
    );
    header("Location: " . SITE_URL . "home/vehicles/view.php?id=$vehicle_id");
    exit;
}
$payment = $payment[0];
?>
<div class="p-3 container mx-auto" style="height: 100vh;"
    style="background: #f1f1f1; padding: 20px; border: 1px solid #000; width: 80%; margin: 0 auto;"
    x-data x-init="window.print();
    setTimeout(() => {
        window.location.href = '<?= SITE_URL; ?>home/vehicles/view.php?id=<?= $payment['vehicle_id']; ?>';
    }, 1000);
    ">
    <div class="d-flex align-items-center justify-content-between flex-wrap">
        <h1>Payment Receipt</h1>
        <h2 class="p-2 px-4 border">
            <?= $payment['id']; ?><br>
        </h2>
    </div>
    <div class="w-full lh-sm d-flex align-items-center justify-content-between gap-1">
        <div class="d-flex  flex-column gap-2">
            <span class="fs-3">Printed on <?= $currentDate; ?></span>
            <span class="fs-3">Printed To <?= $payment['user_name']; ?></span>
        </div>
        <div class="d-flex align-items-end flex-column gap-2 mt-2">
            <span class="fw-bolder fs-3">Contact Info:</span>
            <span class="text-muted"><?= SITE_EMAIL ?></span>
            <span class="text-muted"><?= SITE_PHONE ?></span>
        </div>
    </div>
    <table class="table mt-3">
        <thead>
            <tr>
                <th class="bg-black text-white fs-3 p-2">Description</th>
                <th class="bg-black text-white fs-3 p-2 text-center">Price</th>
                <th class="bg-black text-white fs-3 p-2 text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <ul>
                        <li><strong>Date :</strong> <?= $currentDate; ?></li>
                        <li><strong>Payment ID :</strong> <?= $payment['id']; ?></li>
                        <li><strong>Customer Email :</strong> <?= $payment['user_email']; ?></li>
                        <li><strong>Policy Type :</strong> <?= $payment['policy_type']; ?></li>
                    </ul>
                </td>
                <td class="text-center fs-3">$<?= $payment['amount_paid']; ?></td>
                <td class="text-end fs-3">$<?= $payment['amount_paid']; ?></td>
            </tr>
            <tr>
                <td>
                    <h1 class="text-center bg-black text-white fs-3 p-2">
                        Vehicle Information
                    </h1>
                    <ul>
                        <li><strong>Model Year :</strong> <?= $payment['vehicle_model']; ?></li>
                        <li><strong>License Plate :</strong> <?= $payment['vehicle_license_plate']; ?></li>
                        <li><strong>Make :</strong> <?= $payment['vehicle_make']; ?></li>
                        <li><strong>Model :</strong> <?= $payment['vehicle_model']; ?></li>
                        <li><strong>VIN :</strong> <?= $payment['vehicle_vin']; ?></li>
                        <li><strong>Color :</strong> <?= $payment['vehicle_color']; ?></li>
                    </ul>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <p>
                        <strong>Payment Method :</strong> <?= $payment['payment_method']; ?>
                        (<?= $payment['payment_status']; ?>)
                    </p>
                </td>
                <td>Subtotal</td>
                <td>
                    <strong>$<?= $payment['amount_paid']; ?></strong>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>Total</td>
                <td>
                    <strong>$<?= $payment['amount_paid']; ?></strong>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="mt-2">
        <p class="fw-bold">
            Payment Info:
        </p>
        <p>
            <strong>Payment ID :</strong> <?= $payment['id']; ?>
        </p>
        <p>
            <strong>Payment Date :</strong> <?= $payment['payment_date']; ?>
        </p>
        <h3 class="fw-bolder text-end text-black">
            Thank you for choosing <?= SITE_NAME; ?>. Your policy is now active.✅
        </h3>
    </div>
</div>

<?php
