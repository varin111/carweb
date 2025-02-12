<?php
require_once __DIR__ . '/../../config/front/header.php';
if (empty(getSession('user_id')) && empty($_COOKIE['user_login'])) {
    header("Location: " . SITE_URL . "login.php");
    exit;
}
$currentDate = date('Y-m-d H:i:s');
$params = getUrlParams($_SERVER['REQUEST_URI']);
$policy_id = data_get($params, 'policy_id', null);
$vehicle_policy_id = data_get($params, 'vehicle_policy_id', null);
$action = data_get($params, 'action', null);
$type = data_get($params, 'type', null);
$id = data_get($params, 'id', null);
$page = data_get($params, 'page', 1); // Get current page
$pageAllPolicies = data_get($params, 'page-all-policies', 1); // Get current page
$limit = 8; // Number of vehicles per page
$offset = ($page - 1) * $limit;
$offsetAllPolicies = ($pageAllPolicies - 1) * $limit;

if (
    !empty($type) &&
    !in_array($type, $policies_type_values)
) {
    $type = null;
}

$vehicle = query_select('vehicles', '*', "id = $id AND user_id = $auth[id]");
if (empty($vehicle)) {
    setSession('vehicle-action', ['message' => 'Vehicle not found.', 'type' => 'danger']);
    header("Location: " . SITE_URL . "home/vehicles/index.php");
    exit;
}

$where = "vehicle_policies.user_id = $auth[id]";
$totalVehiclePolicies = count_query('vehicle_policies', $where); // Count total vehicle policies
if (!empty($type)) {
    $where .= " AND policies.type = '$type'";
}

$where .= " AND vehicles.id = $id";
// $vehiclePolicies = query_select_with_join(
//     table: 'vehicle_policies',
//     join: 'JOIN users ON vehicle_policies.user_id = users.id JOIN vehicles ON vehicle_policies.vehicle_id = vehicles.id JOIN policies ON vehicle_policies.policy_id = policies.id LEFT JOIN payments ON vehicle_policies.policy_id = payments.policy_id AND vehicle_policies.vehicle_id = payments.vehicle_id',
//     columns: 'vehicle_policies.*, users.name as user_name, policies.start_date as start_date, policies.end_date as end_date, policies.status as status, policies.premium_amount as premium_amount, policies.coverage_type as coverage_type, payments.payment_status as payment_status',
//     where: $where,
//     limit: $limit,
//     offset: $offset
// );
$vehiclePolicies = query_select_with_join(
    table: 'vehicle_policies',
    join: 'JOIN users ON vehicle_policies.user_id = users.id 
        JOIN vehicles ON vehicle_policies.vehicle_id = vehicles.id 
        JOIN policies ON vehicle_policies.policy_id = policies.id 
        left JOIN payments ON vehicle_policies.policy_id = payments.policy_id 
        AND vehicle_policies.vehicle_id = payments.vehicle_id',
    columns: 'DISTINCT vehicle_policies.*, 
            users.name as user_name, 
            policies.start_date as start_date, 
            policies.end_date as end_date, 
            policies.status as status, 
            policies.premium_amount as premium_amount, 
            policies.coverage_type as coverage_type, 
            policies.type as type,
            Max(payments.id) as last_payment_id,
            MAX(payments.end_date_payment) as payment_end_date_payment',
    where: $where,
    limit: $limit,
    offset: $offset,
    group_by: 'vehicle_policies.id, payments.payment_status',
);


$where = "policies.end_date >= '$currentDate'";
if (!empty($type)) {
    $where .= " AND policies.type = '$type'";
}
$where .= " AND policies.id NOT IN (
    SELECT vehicle_policies.policy_id 
    FROM vehicle_policies 
    WHERE vehicle_policies.vehicle_id = $vehicle[id] 
    AND vehicle_policies.user_id = $auth[id]
)";
$totalPolicies = count_query('policies', $where); // Count total policies

$allPolicies = selectAll(
    table: 'policies',
    columns: 'policies.*',
    where: $where,
    limit: $limit,
    offset: $offsetAllPolicies
);

// Calculate total pages
$totalPages = ceil($totalVehiclePolicies / $limit);
$totalPagesPolicy = ceil($totalPolicies / $limit);

if ($action == 'assign-policy' && !empty($policy_id)) {
    $CheckPolicy = query_select('policies', '*', "id = $policy_id AND start_date <= '$currentDate' AND end_date >= '$currentDate' AND status = 'enable'");
    if (empty($CheckPolicy)) {
        setSession('vehicle-policy-action', ['message' => 'Policy is not available.', 'type' => 'danger']);
        header("Location: " . SITE_URL . "home/vehicles/view.php?id=$id#allPolicies");
        exit;
    }
    $vehiclePolicy = query_select('vehicle_policies', '*', "vehicle_id = $id AND policy_id = $policy_id AND user_id = $auth[id]");
    if (empty($vehiclePolicy)) {
        $data = [
            'user_id' => $auth['id'],
            'vehicle_id' => $id,
            'policy_id' => $policy_id,
        ];
        query_insert('vehicle_policies', $data);
        setSession('vehicle-policy-action', ['message' => 'Vehicle policy assigned successfully.', 'type' => 'success']);
        header("Location: " . SITE_URL . "home/vehicles/view.php?id=$id#allPolicies");
        exit;
    } else {
        setSession('vehicle-policy-action', ['message' => 'Vehicle policy already assigned.', 'type' => 'danger']);
        header("Location: " . SITE_URL . "home/vehicles/view.php?id=$id#allPolicies");
        exit;
    }
}


if ($action == 'delete-policy' && !empty($vehicle_policy_id)) {
    $vehiclePolicy = query_select('vehicle_policies', '*', "vehicle_id = $id AND id = $vehicle_policy_id AND user_id = $auth[id]");
    if (!empty($vehiclePolicy)) {
        query_update(
            'payments',
            [
                'end_date_payment' => null,
            ],
            'policy_id = ' . $vehiclePolicy['policy_id'] . ' AND vehicle_id = ' . $vehiclePolicy['vehicle_id']
        );
        query_delete('vehicle_policies', "id = $vehiclePolicy[id]");
        setSession('vehicle-policy-action', ['message' => 'Vehicle policy deleted successfully.', 'type' => 'success']);
        header("Location: " . SITE_URL . "home/vehicles/view.php?id=$id#vehicle_policies");
        exit;
    } else {
        setSession('vehicle-policy-action', ['message' => 'Vehicle policy not found.', 'type' => 'danger']);
        header("Location: " . SITE_URL . "home/vehicles/view.php?id=$id#vehicle_policies");
        exit;
    }
}
?>
<div style="margin-block: 7.5rem;" class="container">
    <div class="modal fade" id="ViewVehicleModel" tabindex="-1" aria-labelledby="ViewVehicleModelLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ViewVehicleModelLabel">
                        View Vehicle
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card border-0">
                        <div class="card-body p-1 rounded-3">
                            <img src="<?= getImagePath(image: $vehicle['image_path'], path: '/assets/uploads/vehicles/') ?>"
                                class="img-responsive-1x2 mb-3 rounded-3 object-cover" width="100%" height="250px"
                                alt="<?= $vehicle['make'] ?>">
                            <div class="px-3 pb-3 text-truncate">
                                <a href="<?= SITE_URL ?>home/vehicles/view.php?id=<?= $vehicle['id'] ?>"
                                    class="fs-2 text-dark text-decoration-none fw-bold">
                                    <?= $vehicle['year'] ?> - <?= $vehicle['make'] ?> - <?= $vehicle['model'] ?>
                                </a>
                                <p class="card-text fs-3 mt-2">
                                    <strong>License Plate:</strong> <?= $vehicle['license_plate'] ?><br>
                                    <strong>Color:</strong> <?= $vehicle['color'] ?><br>
                                    <strong>Mileage:</strong> <?= $vehicle['mileage'] ?> miles<br>
                                    <strong>VIN:</strong> <?= $vehicle['vin'] ?><br>
                                </p>
                                <hr class="mb-1 mt-0">
                                <p>
                                    <strong>Created at: <?= format_date($vehicle['created_at']) ?></strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2 mb-3 justify-content-between">
        <a href="<?= SITE_URL ?>home/vehicles/index.php" class="btn btn-outline-primary btn-sm px-3 p-1 rounded-2">
            <i class="fas fa-arrow-left me-1"></i>
            Back
        </a>
        <button class="fs-4 btn btn-outline-primary btn-sm px-3 p-1 rounded-2" type="button" data-bs-toggle="modal"
            data-bs-target="#ViewVehicleModel">
            <strong>License Plate:</strong> <?= $vehicle['license_plate'] ?>
        </button>
    </div>
    <div class="d-flex align-items-center gap-2 mb-2">
        <h1 class="text-center">
            <?= $vehicle['year'] ?> - <?= $vehicle['make'] ?> - <?= $vehicle['model'] ?>
        </h1>
    </div>
    <?= showSessionMessage('vehicle-policy-action') ?>
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="<?= SITE_URL ?>home/vehicles/view.php?id=<?= $vehicle['id'] ?>"
            class="<?= empty($type) ? 'badge bg-primary' : 'badge bg-secondary' ?> btn-sm px-3 p-2 rounded-2">
            All
        </a>
        <?php foreach ($policies_type_values as $policy_type): ?>
            <?php
            $class = match ($policy_type) {
                'Standard' => 'badge bg-primary',
                'Gold' => 'badge bg-warning',
                'Platinum' => 'badge bg-info',
                'Premium' => 'badge bg-success',
                default => 'badge bg-secondary',
            };
            ?>
            <a href="<?= SITE_URL ?>home/vehicles/view.php?id=<?= $vehicle['id'] ?>&type=<?= $policy_type ?>"
                class="<?= $class ?> btn-sm px-3 p-2 rounded-2">
                <?= $policy_type ?>
            </a>
        <?php endforeach; ?>
    </div>
    <div x-data="{
        activeTab: 'vehicle_policies',
        changeTab(tab) {
            this.activeTab = tab;
            history.replaceState(null, null, `#${tab}`);
        },
        init() {
            const hash = window.location.hash.replace('#', '');
            if (hash) {
                this.activeTab = hash;
            }
        }
    }" x-init="init()">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link" :class="{ 'active': activeTab === 'vehicle_policies' }"
                    @click="changeTab('vehicle_policies')" id="vehicle_policies-tab" type="button" role="tab"
                    aria-controls="vehicle_policies" :aria-selected="activeTab === 'vehicle_policies'">
                    Vehicle Policies
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" :class="{ 'active': activeTab === 'allPolicies' }"
                    @click="changeTab('allPolicies')" id="allPolicies-tab" type="button" role="tab"
                    aria-controls="allPolicies" :aria-selected="activeTab === 'allPolicies'">
                    Policies (<?= $type ?? 'All' ?>)
                </button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'vehicle_policies' }"
                id="vehicle_policies" role="tabpanel" aria-labelledby="vehicle_policies-tab">
                <div class="d-flex align-items-center gap-2 mt-2">
                    <h1 class="text-center">
                        Vehicle Policies
                    </h1>
                </div>
                <?php if (empty($vehiclePolicies)): ?>
                    <div class="alert alert-info-wrap mt-3">
                        No policies found. please add a policy.
                    </div>
                <?php endif; ?>
                <div class="row mt-1 gy-3">
                    <?php foreach ($vehiclePolicies as $vehiclePolicy): ?>
                        <?php
                        $isAvailablePolicy = ($vehiclePolicy['end_date'] >= $currentDate && $vehiclePolicy['start_date'] <= $currentDate) && $vehiclePolicy['status'] == 'enable';
                        $isTimeToRenewPayment = $vehiclePolicy['payment_end_date_payment'] != null ? $vehiclePolicy['payment_end_date_payment'] <= $currentDate : null;
                        $vehicle_policy_details = getPolicyTypeDetails($vehiclePolicy['type']);
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card shadow-sm rounded-3 position-relative">
                                <div class="card-body rounded-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="fs-3 text-center text-truncate"><?= $vehiclePolicy['coverage_type'] ?></p>
                                        <div class="d-flex gap-3 align-items-center">
                                            <a href="<?= SITE_URL ?>home/vehicles/view.php?id=<?= $vehicle['id'] ?>&action=delete-policy&policy_id=<?= $vehiclePolicy['policy_id'] ?>&vehicle_policy_id=<?= $vehiclePolicy['id'] ?>#vehicle_policies"
                                                class="btn btn-sm px-3 p-2 rounded-2 bg-danger text-white">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <?php if ($isTimeToRenewPayment || $vehiclePolicy['payment_end_date_payment'] != null): ?>
                                                <a href="<?= SITE_URL ?>home/bill/print.php?token=<?= random_string(50) ?>&payment_id=<?= $vehiclePolicy['last_payment_id'] ?>"
                                                    class="btn btn-sm px-3 p-2 rounded-2 bg-primary text-white"
                                                    target="_blank" rel="noopener noreferrer">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="card-text">
                                            <strong>Start Date:</strong> <?= $vehiclePolicy['start_date'] ?>
                                        </p>
                                        <p class="card-text">
                                            <strong>End Date:</strong> <?= $vehiclePolicy['end_date'] ?>
                                            <?php if ($vehiclePolicy['end_date'] <= $currentDate): ?>
                                                <span class="badge bg-danger rounded-pill">Expired</span>
                                            <?php endif; ?>
                                        </p>
                                        <p class="card-text">
                                            <strong>Premium amount (USD):</strong> <?= $vehiclePolicy['premium_amount'] ?> $
                                        </p>
                                        <p class="card-text">
                                            <strong>Policy Status:</strong> <span
                                                class="badge <?= $isAvailablePolicy ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $isAvailablePolicy ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </p>
                                        <p class="card-text">
                                            <strong>Policy Type:</strong>
                                            <?php
                                            $html = match ($vehiclePolicy['type']) {
                                                'Standard' => '<span class="badge bg-primary">Standard</span>',
                                                'Gold' => '<span class="badge bg-warning">Gold</span>',
                                                'Platinum' => '<span class="badge bg-info">Platinum</span>',
                                                'Premium' => '<span class="badge bg-success">Premium</span>',
                                                default => '<span class="badge bg-secondary">Unknown</span>',
                                            };
                                            ?>
                                            <?= $html ?>
                                            </span>
                                        </p>
                                        <?php if ($vehiclePolicy['payment_end_date_payment'] != null): ?>
                                            <p class="card-text">
                                                <strong>Payment End Date:</strong>
                                                <?= $vehiclePolicy['payment_end_date_payment'] ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div x-data="{open:false}" class="mt-3">
                                        <a href="#" x-on:click="$event.preventDefault(); open = !open"
                                            class="w-full rounded-2 flex-grow-1 d-flex gap-2 align-items-center">
                                            <span
                                                x-text="open ? 'Hide Details' : 'Show Details'">
                                                Show Details
                                            </span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                :style="open ? 'transform: rotate(180deg);' : ''">
                                                <path d="M12 5v14" />
                                                <path d="m19 12-7 7-7-7" />
                                            </svg>
                                        </a>
                                        <div
                                            x-cloak
                                            x-show="open" class="mt-3">
                                            <?php foreach ($vehicle_policy_details as $detail): ?>
                                                <p class="card-text flex align-items-center gap-2">
                                                    <?php if ($detail['status']): ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check">
                                                            <path d="M20 6 9 17l-5-5" />
                                                        </svg>
                                                    <?php else: ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x">
                                                            <path d="M6 6 18 18M6 18 18 6" />
                                                        </svg>
                                                    <?php endif; ?>
                                                    <?= $detail['title'] ?>
                                                </p>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center mt-3">
                                        <?php if ($isAvailablePolicy): ?>
                                            <?php if ($isTimeToRenewPayment || $vehiclePolicy['payment_end_date_payment'] == null): ?>
                                                <a href="<?= SITE_URL ?>home/vehicles/payment-process.php?token=<?= random_string(50) ?>&user_id=<?= $auth['id'] ?>&vehicle_id=<?= $vehicle['id'] ?>&policy_id=<?= $vehiclePolicy['policy_id'] ?>&id=<?= $vehiclePolicy['id'] ?>#vehicle_policies"
                                                    class="btn btn-primary btn-sm px-3 p-2 rounded-2 flex-grow-1">
                                                    <?php if ($isTimeToRenewPayment != null): ?>
                                                        Renew Payment
                                                    <?php else: ?>
                                                        Pay Now
                                                    <?php endif; ?>
                                                </a>
                                            <?php else: ?>
                                                <button type="button"
                                                    class="btn btn-primary btn-sm px-3 p-2 rounded-2 flex-grow-1 disabled">
                                                    Payment is successfully processed
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <a href="#" class="btn btn-primary disabled w-full">
                                                Policy Is Not Available
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center gap-2 flex-wrap">
                                <!-- Previous Button -->
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= $_SERVER['PHP_SELF'] ?>?page=<?= $page - 1 ?>"
                                        aria-label="Previous">
                                        Previous
                                    </a>
                                </li>
                                <!-- Pagination Links -->
                                <?php
                                $visiblePages = 3; // Number of pages to show around the current page
                                $startPage = max(1, $page - $visiblePages);
                                $endPage = min($totalPages, $page + $visiblePages);
                                if ($startPage > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="' . $_SERVER['PHP_SELF'] . '?page=1">1</a></li>';
                                    if ($startPage > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">';
                                    echo '<a class="page-link" href="' . $_SERVER['PHP_SELF'] . '?page=' . $i . '">' . $i . '</a>';
                                    echo '</li>';
                                }
                                if ($endPage < $totalPages) {
                                    if ($endPage < $totalPages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="' . $_SERVER['PHP_SELF'] . '?page=' . $totalPages . '">' . $totalPages . '</a></li>';
                                }
                                ?>
                                <!-- Next Button -->
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= $_SERVER['PHP_SELF'] ?>?page=<?= $page + 1 ?>"
                                        aria-label="Next">
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'allPolicies' }" id="allPolicies"
                role="tabpanel" aria-labelledby="allPolicies-tab">
                <div class="d-flex align-items-center gap-2 mt-2">
                    <h1 class="text-center">
                        Policies (<?= $type ?? 'All' ?>)
                    </h1>
                </div>
                <?php if (empty($allPolicies)): ?>
                    <div class="alert alert-info-wrap mt-3">
                        No policies found. please contact the admin to add a policy.
                    </div>
                <?php endif; ?>
                <div class="row mt-1 gy-3">
                    <?php foreach ($allPolicies as $policy): ?>
                        <?php
                        $policy_details = getPolicyTypeDetails($policy['type']);
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card shadow-sm rounded-3">
                                <div class="card-body rounded-3">
                                    <div class="card-title  text-truncate">
                                        <h2><?= $policy['coverage_type'] ?></h2>
                                    </div>
                                    <div>
                                        <p class="card-text">
                                            <strong>Start Date:</strong> <?= $policy['start_date'] ?>
                                        </p>
                                        <p class="card-text">
                                            <strong>End Date:</strong> <?= $policy['end_date'] ?>
                                        </p>
                                        <p class="card-text">
                                            <strong>Premium amount (USD):</strong> <?= $policy['premium_amount'] ?> $
                                        </p>
                                        <p class="card-text">
                                            <strong>Policy Status:</strong> <span
                                                class="badge <?= $policy['status'] == 'enable' ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $policy['status'] == 'enable' ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </p>
                                        <p class="card-text">
                                            <strong>Policy Type:</strong>
                                            <?php
                                            $html = match ($policy['type']) {
                                                'Standard' => '<span class="badge bg-primary">Standard</span>',
                                                'Gold' => '<span class="badge bg-warning">Gold</span>',
                                                'Platinum' => '<span class="badge bg-info">Platinum</span>',
                                                'Premium' => '<span class="badge bg-success">Premium</span>',
                                                default => '<span class="badge bg-secondary">Unknown</span>',
                                            };
                                            ?>
                                            <?= $html ?>
                                        </p>
                                    </div>
                                    <div x-data="{open:false}" class="mt-3">
                                        <a href="#" x-on:click="$event.preventDefault(); open = !open"
                                            class="w-full rounded-2 flex-grow-1 d-flex gap-2 align-items-center">
                                            <span
                                                x-text="open ? 'Hide Details' : 'Show Details'">
                                                Show Details
                                            </span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                :style="open ? 'transform: rotate(180deg);' : ''">
                                                <path d="M12 5v14" />
                                                <path d="m19 12-7 7-7-7" />
                                            </svg>
                                        </a>
                                        <div
                                            x-cloak
                                            x-show="open" class="mt-3">
                                            <?php foreach ($policy_details as $detail): ?>
                                                <p class="card-text flex align-items-center gap-2">
                                                    <?php if ($detail['status']): ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check">
                                                            <path d="M20 6 9 17l-5-5" />
                                                        </svg>
                                                    <?php else: ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x">
                                                            <path d="M6 6 18 18M6 18 18 6" />
                                                        </svg>
                                                    <?php endif; ?>
                                                    <?= $detail['title'] ?>
                                                </p>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center mt-3">
                                        <?php if (($policy['status'] == 'enable' && $policy['end_date'] >= $currentDate && $policy['start_date'] <= $currentDate)): ?>
                                            <a href="<?= $_SERVER['PHP_SELF'] ?>?id=<?= $vehicle['id'] ?>&action=assign-policy&policy_id=<?= $policy['id'] ?>#allPolicies"
                                                class=" btn btn-primary btn-sm px-3 p-2 rounded-2 flex-grow-1">
                                                Assign Policy
                                            </a>
                                        <?php else: ?>
                                            <button type="button" name="assign_policy-<?= md5($policy['id']) ?>" href="#"
                                                class="btn btn-primary btn-sm px-3 p-2 rounded-2 flex-grow-1 disabled">
                                                Policy Is Not Available
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($totalPagesPolicy > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center gap-2 flex-wrap">
                                <li class="page-item <?= $pageAllPolicies <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= $_SERVER['PHP_SELF'] ?>?page=<?= $pageAllPolicies - 1 ?>"
                                        aria-label="Previous">
                                        Previous
                                    </a>
                                </li>
                                <?php
                                $visiblePages = 3; // Number of pages to show around the current page
                                $startPage = max(1, $pageAllPolicies - $visiblePages);
                                $endPage = min($totalPagesPolicy, $pageAllPolicies + $visiblePages);
                                if ($startPage > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="' . $_SERVER['PHP_SELF'] . '?page-all-policies=1">1</a></li>';
                                    if ($startPage > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    echo '<li class="page-item ' . ($i == $pageAllPolicies ? 'active' : '') . '">';
                                    echo '<a class="page-link" href="' . $_SERVER['PHP_SELF'] . '?page-all-policies=' . $i . '">' . $i . '</a>';
                                    echo '</li>';
                                }
                                if ($endPage < $totalPagesPolicy) {
                                    if ($endPage < $totalPagesPolicy - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="' . $_SERVER['PHP_SELF'] . '?page-all-policies=' . $totalPagesPolicy . '#allPolicies">' . $totalPagesPolicy . '</a></li>';
                                }
                                ?>
                                <li class="page-item <?= $pageAllPolicies >= $totalPagesPolicy ? 'disabled' : '' ?>">
                                    <a class="page-link"
                                        href="<?= $_SERVER['PHP_SELF'] ?>?page-all-policies=<?= $pageAllPolicies + 1 ?>"
                                        aria-label="Next">
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . '/../../config/front/footer.php';
