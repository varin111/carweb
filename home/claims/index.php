<?php
require_once __DIR__ . '/../../config/front/header.php';
if (empty(getSession('user_id')) && empty($_COOKIE['user_login'])) {
    header("Location: " . SITE_URL . "login.php");
    exit;
}

$params = getUrlParams($_SERVER['REQUEST_URI']);
$vehicle_id = data_get($params, 'vehicle_id', null);
$action = data_get($params, 'action', null);
$id = data_get($params, 'id', null);
$page = data_get($params, 'page', 1); // Get current page
$limit = 12; // Number of vehicles per page
$offset = ($page - 1) * $limit;

$vehicle = query_select('vehicles', '*', "id = $vehicle_id AND user_id = $auth[id]");
if (empty($vehicle)) {
    setSession(
        'vehicle-action',
        [
            'type' => 'danger',
            'message' => 'Vehicle not found.'
        ]
    );
    header("Location: " . SITE_URL . "home/vehicles/index.php");
    exit;
} elseif ($vehicle['balance'] == 0 || $vehicle['balance'] == null) {
    setSession(
        'vehicle-action',
        [
            'type' => 'danger',
            'message' => 'Vehicle balance is 0. Please pay the policy to get the balance and then try to claim.'
        ]
    );
    header("Location: " . SITE_URL . "home/vehicles/index.php");
    exit;
}

if ($action === 'delete') {
    if ($id !== null && is_numeric($id)) {
        $claim = query_select('claims', '*', "id = $id AND vehicle_id = $vehicle_id");
        if (!empty($claim)) {
            query_delete('claims', "id = $id AND vehicle_id = $vehicle_id");
            setSession('claim-action', [
                'type' => 'success',
                'message' => 'Vehicle deleted successfully'
            ]);
        }
    }
}
$where = "vehicle_id = $vehicle_id";
if ($id) {
    $where .= " AND claims.id != $id";
}
$totalClaims = count_query('claims', $where); // Count total vehicles
$claims = query_select_with_join(
    table: 'claims',
    join: 'JOIN vehicles ON claims.vehicle_id = vehicles.id JOIN users ON vehicles.user_id = users.id
        JOIN policies ON claims.policy_id = policies.id',
    columns: 'claims.*,vehicles.license_plate as vehicle_license_plate, policies.coverage_type as policy_coverage_type',
    where: $where,
    limit: $limit,
    offset: $offset
);
// Calculate total pages
$totalPages = ceil($totalClaims / $limit);
?>
<div style="margin-block: 7.5rem;" class="container">
    <?= showSessionMessage('claim-action') ?>
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-1">
            <a href="<?= SITE_URL ?>home/vehicles/index.php" class="btn btn-outline-primary btn-sm px-3 p-1 rounded-2">
                <i class="fas fa-arrow-left me-1"></i>
                Back
            </a>
            <h1 class="text-center">
                Claims
            </h1>
        </div>
        <a href="<?= SITE_URL ?>/home/claims/action.php?action=create&vehicle_id=<?= $vehicle_id ?>" class="btn btn-primary">
            New Claim
        </a>
    </div>
    <?php if (empty($claims)) : ?>
        <div class="alert alert-info mt-3">
            No claims found. <a href="<?= SITE_URL ?>/home/claims/action.php?action=create&vehicle_id=<?= $vehicle_id ?>">Add Claim</a>
        </div>
    <?php endif; ?>
    <div class="row mt-1 gy-3">
        <?php foreach ($claims as $claim) : ?>
            <div class="col-md-6 col-lg-4 col-xl-3"
                x-data="{ expandDescription: false }">
                <div class="card shadow-sm rounded-3">
                    <div class="card-body p-1 rounded-3">
                        <div class="px-3 py-3 ">
                            <p class="card-text fs-3 mt-2"
                                x-on:click="expandDescription = !expandDescription"
                                :class="{ 'text-truncate': !expandDescription }">
                                <?= $claim['description'] ?>
                            </p>
                            <p class="card-text fs-3 mt-2 text-truncate"
                                style="line-height: 1.9rem;">
                                <strong>Vehicle:</strong> <?= $claim['vehicle_license_plate'] ?><br>
                                <strong>Policy:</strong> <?= $claim['policy_coverage_type'] ?><br>
                                <strong>Amount:</strong> <?= format_currency($claim['amount']) ?>$<br>
                            </p>
                            <hr class="mb-1 mt-0">
                            <p>
                                <strong>Created at: <?= format_date($claim['created_at']) ?></strong>
                            </p>
                            <div class="w-full">
                                <span class="badge bg-<?= $claim['status'] == 'approved' ? 'success' : ($claim['status'] == 'rejected' ? 'danger' : 'warning') ?> w-full py-2">
                                    <?= ucfirst($claim['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if ($totalPages > 1) : ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center gap-2 flex-wrap">
                    <!-- Previous Button -->
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $_SERVER['PHP_SELF'] ?>?page=<?= $page - 1 ?>" aria-label="Previous">
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
                        <a class="page-link" href="<?= $_SERVER['PHP_SELF'] ?>?page=<?= $page + 1 ?>" aria-label="Next">
                            Next
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>
<?php
require_once __DIR__ . '/../../config/front/footer.php';
