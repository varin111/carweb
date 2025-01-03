<?php
require_once __DIR__ . '/../../config/front/header.php';
if (empty(getSession('user_id')) && empty($_COOKIE['user_login'])) {
    header("Location: " . SITE_URL . "login.php");
    exit;
}
$params = getUrlParams($_SERVER['REQUEST_URI']);
$search = !empty($params['search']) ? clear($params['search']) : '';
$action = data_get($params, 'action', null);
$id = data_get($params, 'id', null);
$page = data_get($params, 'page', 1); // Get current page
$limit = 12; // Number of vehicles per page
$offset = ($page - 1) * $limit;

if ($action === 'delete') {
    if ($id !== null && is_numeric($id)) {
        $vehicle = query_select('vehicles', '*', "id = $id AND user_id = $auth[id]");
        if (!empty($vehicle)) {
            $imagePath = $vehicle['image_path'];
            if ($imagePath) {
                removeOldImage($imagePath, __DIR__ . '/../../assets/uploads/vehicles/');
            }
            query_delete('vehicles', "id = $id AND user_id = $auth[id]");
            setSession('vehicle-action', [
                'type' => 'success',
                'message' => 'Vehicle deleted successfully'
            ]);
        }
    }
}

$where = "user_id = $auth[id]";
if ($id) {
    $where .= " AND vehicles.id != $id";
}
$search = mysqli_real_escape_string($conn, $search);
if (!empty($search)) $where .= " AND (vehicles.year LIKE '%$search%' OR vehicles.make LIKE '%$search%' OR vehicles.model LIKE '%$search%' OR vehicles.license_plate LIKE '%$search%' OR vehicles.color LIKE '%$search%' OR vehicles.mileage LIKE '%$search%' OR vehicles.vin LIKE '%$search%')";
$totalVehicles = count_query('vehicles', $where);
$vehicles = query_select_with_join(
    table: 'vehicles',
    join: 'JOIN users ON vehicles.user_id = users.id',
    columns: 'vehicles.*, users.name as user_name',
    where: $where,
    limit: $limit,
    offset: $offset
);
// Calculate total pages
$totalPages = ceil($totalVehicles / $limit);
?>
<div style="margin-block: 7.5rem;" class="container">
    <?= showSessionMessage('vehicle-action') ?>
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="text-center">Vehicles</h1>
        <a href="<?= SITE_URL ?>/home/vehicles/action.php?action=create" class="btn btn-primary">Add Vehicle</a>
    </div>
    <div>
        <form class="d-flex align-items-center justify-content-between mt-3">
            <input type="search" class="form-control" name="search" placeholder="Search" value="<?= $search ?>">
            <button type="submit" class="btn btn-primary ms-2">Search</button>
        </form>
    </div>
    <?php if (empty($vehicles)) : ?>
        <div class="alert alert-info mt-3">
            No vehicles found. <a href="<?= SITE_URL ?>/home/vehicles/action.php?action=create">Add Vehicle</a>
        </div>
    <?php endif; ?>
    <div class="row mt-1 gy-3">
        <?php foreach ($vehicles as $vehicle) : ?>
            <div class="col-md-6 col-lg-4 ">
                <div class="card shadow-sm rounded-3">
                    <div class="card-body p-1 rounded-3">
                        <img src="<?= getImagePath(image: $vehicle['image_path'], path: '/assets/uploads/vehicles/') ?>" class="img-responsive-1x2 mb-3 rounded-3 object-cover" width="100%" height="250px" alt="<?= $vehicle['make'] ?>">
                        <div class="px-3 pb-3 text-truncate">
                            <a
                                href="<?= SITE_URL ?>home/vehicles/view.php?id=<?= $vehicle['id'] ?>"
                                class="fs-2 text-dark text-decoration-none fw-bold">
                                <?= $vehicle['year'] ?> - <?= $vehicle['make'] ?> - <?= $vehicle['model'] ?>
                            </a>
                            <p class="card-text fs-3 mt-2">
                                <strong>License Plate:</strong> <?= $vehicle['license_plate'] ?><br>
                                <strong>Color:</strong> <?= $vehicle['color'] ?><br>
                                <strong>Mileage:</strong> <?= $vehicle['mileage'] ?> miles<br>
                                <strong>VIN:</strong> <?= $vehicle['vin'] ?><br>
                                <strong>Owner:</strong> <?= $vehicle['user_name'] ?><br>
                                <strong>Balance:</strong> <?= format_currency($vehicle['balance']) ?>$<br>
                            </p>
                            <hr class="mb-1 mt-0">
                            <p>
                                <strong>Created at: <?= format_date($vehicle['created_at']) ?></strong>
                            </p>
                            <?php if ($vehicle['balance'] != null && $vehicle['balance'] > 0) : ?>
                                <a href="<?= SITE_URL ?>home/claims/index.php?vehicle_id=<?= $vehicle['id'] ?>" class="btn btn-primary btn-sm p-1 px-3 rounded-2 mb-2 w-full">
                                    <i class="fas fa-clipboard-list me-1"></i>
                                    Claims
                                </a>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between gap-1">
                                <a href="<?= SITE_URL ?>home/vehicles/view.php?id=<?= $vehicle['id'] ?>" class="btn btn-primary btn-sm p-1 px-3 rounded-2 flex-grow-1">
                                    <i class="fas fa-eye me-1"></i>
                                    View
                                </a>
                                <a href="<?= SITE_URL ?>home/vehicles/action.php?action=update&id=<?= $vehicle['id'] ?>" class="btn btn-primary btn-sm p-1 px-3 rounded-2 flex-grow-1">
                                    <i class="fas fa-edit me-1"></i>
                                    Edit
                                </a>
                                <a href="<?= $_SERVER['PHP_SELF'] ?>?action=delete&id=<?= $vehicle['id'] ?>" class="btn btn-danger btn-sm p-1 px-3 rounded-2 flex-grow-1"
                                    onclick="return confirm('Are you sure you want to delete this vehicle?')">
                                    <i class="fas fa-trash me-1"></i>
                                    Delete
                                </a>
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
                        echo '<li class="page-item"><a class="page-link" href="' . $_SERVER['PHP_SELF'] . '?page=1&search=' . $search . '">1</a></li>';
                        if ($startPage > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    for ($i = $startPage; $i <= $endPage; $i++) {
                        echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">';
                        echo '<a class="page-link" href="' . $_SERVER['PHP_SELF'] . '?page=' . $i . '&search=' . $search . '">' . $i . '</a>';
                        echo '</li>';
                    }
                    if ($endPage < $totalPages) {
                        if ($endPage < $totalPages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="' . $_SERVER['PHP_SELF'] . '?page=' . $totalPages . '&search=' . $search . '">' . $totalPages . '</a></li>';
                    }
                    ?>
                    <!-- Next Button -->
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $_SERVER['PHP_SELF'] ?>?page=<?= $page + 1 ?>&search=' . $search . '" aria-label="Next">
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
