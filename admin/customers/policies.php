<?php
ob_start();
require_once __DIR__ . '/../../config/admin/header.php';

$user_id = data_get($_GET, 'user_id', null);
if ($user_id === null || !is_numeric($user_id)) {
    header('Location: index.php');
    exit;
}

$user = query_select('users', '*', "id = $user_id AND is_admin = 0");
if (empty($user)) {
    header('Location: index.php');
    exit;
}
?>

<div class="container-fluid py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= SITE_URL?>admin/customers/index.php" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i>
        </a>
        <h1 class="mb-0">Customer Details</h1>
    </div>
    <!-- Customer Info Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 text-center">
                    <img src="<?= getImagePath($user['image_path']); ?>"
                        class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                </div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-6">
                            <h1 class="mb-3"><?= htmlspecialchars($user['name']) ?></h1>
                            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
                            <p><strong>Date of Birth:</strong> <?= htmlspecialchars($user['date_of_birth']) ?></p>
                            <p><strong>Gender:</strong> <?= htmlspecialchars($user['gender']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Policies Table -->
    <!-- <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Insurance Policies</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="policies-table">
                    <thead>
                        <tr>
                            <th>Policy ID</th>
                            <th>Vehicle</th>
                            <th>Policy Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Premium Amount</th>
                            <th>Status</th>
                            <th>Payment Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div> -->

    <!-- Payments Table -->
    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h4 class="mb-0">Payment History</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="payments-table">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Policy</th>
                            <th>Vehicle</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        // Existing policies table initialization
        // const policiesTable = new DataTable("#policies-table", {
        //     'processing': true,
        //     'serverSide': true,
        //     'scrollX': true,
        //     'language': {
        //         'emptyTable': '<div class="text-center fs-3 fw-bolder">No policies found</div>'
        //     },
        //     'ajax': `<?= SITE_URL ?>api/policies.php?user_id=<?= $user_id ?>`,
        // });

        // Initialize payments table
        const paymentsTable = new DataTable("#payments-table", {
            'processing': true,
            'serverSide': true,
            'scrollX': true,
            'language': {
                'emptyTable': '<div class="text-center fs-3 fw-bolder">No payments found</div>'
            },
            'ajax': {
                'url': `<?= SITE_URL ?>api/payments.php`,
                'data': function(d) {
                    d.user_id = <?= $user_id ?>;
                }
            },
            'columnDefs': [{
                'targets': [0, 1, 2, 3, 4, 5, 6],
                'render': function(data, type, row, meta) {
                    switch (meta.col) {
                        case 0:
                            return row.payment_id;
                        case 1:
                            return row.policy_name;
                        case 2:
                            return row.vehicle;
                        case 3:
                            return '$' + parseFloat(row.amount).toFixed(2);
                        case 4:
                            return row.payment_date;
                        case 5:
                            return row.payment_method;
                        case 6:
                            const statusClasses = {
                                'completed': 'success',
                                'pending': 'warning',
                                'failed': 'danger'
                            };
                            return `<span class="badge bg-${statusClasses[row.status.toLowerCase()]}">${row.status}</span>`;
                        default:
                            return data;
                    }
                }
            }]
        });
    });
</script>

<?php
require_once __DIR__ . '/../../config/admin/footer.php';
?>