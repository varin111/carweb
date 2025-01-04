<?php
require_once __DIR__ . '/../config/admin/header.php';
$users_count  = count_query('users', 'is_admin = 1');
$customers_count  = count_query('users', 'is_admin = 0');
$policy_count = count_query('policies');
$claims_count = count_query('claims');

$ten_latest_claims = query_select_with_join(
    table: 'claims',
    join: 'JOIN vehicles ON vehicles.id = claims.vehicle_id
        JOIN users ON users.id = vehicles.user_id
        JOIN policies ON policies.id = claims.policy_id',
    columns: 'claims.*, users.name as user_name, vehicles.license_plate as vehicle_license_plate, policies.coverage_type as policy_coverage_type',
    limit: 10
);

$sql = "SELECT users.id as user_id, users.name as user_name,
        SUM(payments.amount_paid) as total_payment_amount,
        MAX(payments.payment_date) as latest_payment_date
        FROM users
        JOIN vehicles ON vehicles.user_id = users.id
        JOIN payments ON payments.vehicle_id = vehicles.id
        WHERE users.is_admin = 0
        GROUP BY users.id, users.name
        ORDER BY latest_payment_date DESC
        LIMIT 10";

$customers = run_sql($sql);
$customersChartPayment = null;
if ($customers) {
    $labels = []; // For user names
    $data = [];   // For total payment amounts
    while ($row = $customers->fetch_assoc()) {
        $labels[] = $row['user_name']; // Add user name to labels
        $data[] = $row['total_payment_amount']; // Add total payment to data
    }
    // Call the chart function with the fetched data
    $customersChartPayment = chart('bar', 'Total Payment Amount', $labels, $data);
}

?>
<div class="px-1 px-md-3">
    <div class="row g-2">
        <div class="col-12">
            <div class="w-full page-header d-print-none mb-2 rounded-2">
                <div class="page-pretitle">
                    Overview
                </div>
                <h2 class="">
                    Dashboard
                </h2>
            </div>
        </div>
        <div class="col-12">
            <div class="row row-cards">
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-bitbucket text-white avatar avatar-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18.6161 20H19.1063C20.2561 20 21.1707 19.4761 21.9919 18.7436C24.078 16.8826 19.1741 15 17.5 15M15.5 5.06877C15.7271 5.02373 15.9629 5 16.2048 5C18.0247 5 19.5 6.34315 19.5 8C19.5 9.65685 18.0247 11 16.2048 11C15.9629 11 15.7271 10.9763 15.5 10.9312" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            <path d="M4.48131 16.1112C3.30234 16.743 0.211137 18.0331 2.09388 19.6474C3.01359 20.436 4.03791 21 5.32572 21H12.6743C13.9621 21 14.9864 20.436 15.9061 19.6474C17.7889 18.0331 14.6977 16.743 13.5187 16.1112C10.754 14.6296 7.24599 14.6296 4.48131 16.1112Z" stroke="currentColor" stroke-width="1.5" />
                                            <path d="M13 7.5C13 9.70914 11.2091 11.5 9 11.5C6.79086 11.5 5 9.70914 5 7.5C5 5.29086 6.79086 3.5 9 3.5C11.2091 3.5 13 5.29086 13 7.5Z" stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium text-muted mb-1"
                                        style="font-size: 25px;">
                                        Users
                                    </div>
                                    <div class="font-weight-medium"
                                        style="font-size: 20px;">
                                        <?= $users_count; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-black text-white avatar avatar-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20.7739 18C21.5232 18 22.1192 17.5285 22.6543 16.8691C23.7498 15.5194 21.9512 14.4408 21.2652 13.9126C20.5679 13.3756 19.7893 13.0714 18.9999 13M17.9999 11C19.3806 11 20.4999 9.88071 20.4999 8.5C20.4999 7.11929 19.3806 6 17.9999 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            <path d="M3.2259 18C2.47659 18 1.88061 17.5285 1.34548 16.8691C0.250028 15.5194 2.04861 14.4408 2.73458 13.9126C3.43191 13.3756 4.21052 13.0714 4.99994 13M5.49994 11C4.11923 11 2.99994 9.88071 2.99994 8.5C2.99994 7.11929 4.11923 6 5.49994 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            <path d="M8.08368 15.1112C7.0619 15.743 4.38286 17.0331 6.01458 18.6474C6.81166 19.436 7.6994 20 8.8155 20H15.1843C16.3004 20 17.1881 19.436 17.9852 18.6474C19.6169 17.0331 16.9379 15.743 15.9161 15.1112C13.52 13.6296 10.4797 13.6296 8.08368 15.1112Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M15.4999 7.5C15.4999 9.433 13.9329 11 11.9999 11C10.0669 11 8.49988 9.433 8.49988 7.5C8.49988 5.567 10.0669 4 11.9999 4C13.9329 4 15.4999 5.567 15.4999 7.5Z" stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium text-muted mb-1"
                                        style="font-size: 25px;">
                                        Customers
                                    </div>
                                    <div class="font-weight-medium"
                                        style="font-size: 20px;">
                                        <?= $customers_count; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-cyan-lt text-blue avatar avatar-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4.26759 4.32782C5.95399 3.02741 8.57337 2 12 2C15.4266 2 18.046 3.02741 19.7324 4.32782C19.9693 4.51048 20.0877 4.60181 20.1849 4.76366C20.2665 4.89952 20.3252 5.10558 20.3275 5.26404C20.3302 5.4528 20.2672 5.62069 20.1413 5.95648C19.8305 6.78539 19.6751 7.19984 19.6122 7.61031C19.533 8.12803 19.5322 8.25474 19.6053 8.77338C19.6632 9.18457 19.9795 10.0598 20.6121 11.8103C20.844 12.452 21 13.1792 21 14C21 17 18.5 19.375 16 20C13.8082 20.548 12.6667 21.3333 12 22C11.3333 21.3333 10.1918 20.548 8 20C5.5 19.375 3 17 3 14C3 13.1792 3.15595 12.452 3.38785 11.8103C4.0205 10.0598 4.33682 9.18457 4.39473 8.77338C4.46777 8.25474 4.46702 8.12803 4.38777 7.61031C4.32494 7.19984 4.16952 6.78539 3.85868 5.95648C3.73276 5.62069 3.6698 5.4528 3.67252 5.26404C3.6748 5.10558 3.73351 4.89952 3.81509 4.76366C3.91227 4.60181 4.03071 4.51048 4.26759 4.32782Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M12.6911 7.57767L13.395 8.99715C13.491 9.19475 13.7469 9.38428 13.9629 9.42057L15.2388 9.6343C16.0547 9.77141 16.2467 10.3682 15.6587 10.957L14.6668 11.9571C14.4989 12.1265 14.4069 12.4531 14.4589 12.687L14.7428 13.925C14.9668 14.9049 14.4509 15.284 13.591 14.7718L12.3951 14.0581C12.1791 13.929 11.8232 13.929 11.6032 14.0581L10.4073 14.7718C9.5514 15.284 9.03146 14.9009 9.25543 13.925L9.5394 12.687C9.5914 12.4531 9.49941 12.1265 9.33143 11.9571L8.33954 10.957C7.7556 10.3682 7.94358 9.77141 8.75949 9.6343L10.0353 9.42057C10.2473 9.38428 10.5033 9.19475 10.5993 8.99715L11.3032 7.57767C11.6872 6.80744 12.3111 6.80744 12.6911 7.57767Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium text-muted mb-1"
                                        style="font-size: 25px;">
                                        Policies
                                    </div>
                                    <div class="font-weight-medium"
                                        style="font-size: 20px;">
                                        <?= $policy_count; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-info-lt text-blue avatar avatar-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M7.5 4.94531H16C16.8284 4.94531 17.5 5.61688 17.5 6.44531V7.94531" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M15 12.9453H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M12 16.9453H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M18.497 2L6.30767 2.00002C5.81071 2.00002 5.30241 2.07294 4.9007 2.36782C3.62698 3.30279 2.64539 5.38801 4.62764 7.2706C5.18421 7.7992 5.96217 7.99082 6.72692 7.99082H18.2835C19.077 7.99082 20.5 8.10439 20.5 10.5273V17.9812C20.5 20.2007 18.7103 22 16.5026 22H7.47246C5.26886 22 3.66619 20.4426 3.53959 18.0713L3.5061 5.16638" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium text-muted mb-1"
                                        style="font-size: 25px;">
                                        Claims
                                    </div>
                                    <div class="font-weight-medium"
                                        style="font-size: 20px;">
                                        <?= $claims_count; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 my-2 mb-3">
            <div class="w-full page-header d-print-none mb-2 rounded-2">
                <div class="page-pretitle">
                    Recent Claims
                </div>
                <h2 class="">
                    10 Latest Claims
                </h2>
            </div>
            <div class="table-responsive border mt-1 rounded-2">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Claim ID</th>
                            <th>User</th>
                            <th>License Plate</th>
                            <th>Policy</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ten_latest_claims as $claim): ?>
                            <tr>
                                <td><?= md5($claim['id']) ?></td>
                                <td><?= $claim['user_name'] ?></td>
                                <td><?= $claim['vehicle_license_plate'] ?></td>
                                <td><?= $claim['policy_coverage_type'] ?></td>
                                <td><?= strlen($claim['description']) > 50 ? substr($claim['description'], 0, 50) . '...' : $claim['description'] ?></td>
                                <td><?= format_currency($claim['amount']) ?>$</td>
                                <td>
                                    <span class="badge bg-<?= $claim['status'] == 'approved' ? 'success' : ($claim['status'] == 'rejected' ? 'danger' : 'warning') ?>">
                                        <?= ucfirst($claim['status']) ?>
                                    </span>
                                </td>
                                <td><?= $claim['created_at'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="<?= $customersChartPayment == null ? 'd-none' : 'col-12' ?> border rounded-3 px-2 px-md-4 bg-white shadow-sm d-flex flex-column gap-2">
            <div class="w-full page-header d-print-none mb-2 rounded-2">
                <div class="page-pretitle">
                    Customers
                </div>
                <h2 class="">
                    10 Customers Total Payment Chart
                </h2>
            </div>
            <div>
                <canvas
                    id="customers-chart-payment"
                    style="display: block; width: 100%; height:650px;"></canvas>
            </div>
        </div>
    </div>
</div>
<?php if ($customersChartPayment != null): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('customers-chart-payment').getContext('2d');
        new Chart(ctx, <?= $customersChartPayment->toJson(); ?>);
    </script>
<?php endif; ?>
<?php
require_once __DIR__ . '/../config/admin/footer.php';
