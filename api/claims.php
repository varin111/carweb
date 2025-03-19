<?php

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/ssp.class.php';

$only_admins = $_GET['only_admins'] ?? 0;

$table = 'claims';

$primaryKey = 'id';

$url_actions = [
    'approved' => SITE_URL . '/admin/claims/action.php?action=approved&id=',
    'rejected' => SITE_URL . '/admin/claims/action.php?action=rejected&id=',
    'delete' => SITE_URL . '/admin/claims/index.php?action=delete&id=',
];

$columns = array(
    array(
        'db' => 'vehicle_id',
        'dt' => 0,
        'formatter' => function ($d, $row) {
            $vehicle = query_select('vehicles', 'license_plate', "id = $d");
            return data_get($vehicle, 'license_plate', 'N/A');
        }
    ),
    array(
        'db' => 'policy_id',
        'dt' => 1,
        'formatter' => function ($d, $row) {
            $policy = query_select('policies', 'coverage_type', "id = $d");
            return data_get($policy, 'coverage_type', 'N/A');
        }
    ),
    array('db' => 'description',  'dt' => 2, 'formatter' => function ($d, $row) {
        return strlen($d) > 50 ? substr($d, 0, 50) . '...' : $d;
    }),
    array('db' => 'amount',   'dt' => 3),
    array(
        'db' => 'status',
        'dt' => 4,
        'formatter' => function ($d, $row) {
            return '<span class="badge bg-' . ($d == 'approved' ? 'success' : ($d == 'rejected' ? 'danger' : 'warning')) . '">' . ucfirst($d) . '</span>';
        }
    ),
    array(
        'db' => 'created_at',
        'dt' => 5,
        'formatter' => function ($d, $row) {
            return date('Y-m-d h:i:m A', strtotime($d));
        }
    ),
    array(
        'db' => 'approved_at',
        'dt' => 6,
        'formatter' => function ($d, $row) {
            return $d ? date('Y-m-d h:i:m A', strtotime($d)) : 'N/A';
        }
    ),
    array(
        'db' => 'rejected_at',
        'dt' => 7,
        'formatter' => function ($d, $row) {
            return $d ? date('Y-m-d h:i:m A', strtotime($d)) : 'N/A';
        }
    ),
    array(
        'db' => 'id',
        'dt' => 8,
        'formatter' => function ($d, $row) use ($url_actions) {
            $buttons = data_get($row, 'approved_at') !== null || data_get($row, 'rejected_at') !== null ? '' :
                ' <a href="' . $url_actions['approved'] . $d . '" class="btn btn-sm btn-success rounded px-3 me-1"
                    onclick="return confirm(\'Are you sure you want to approve this claim?\')">
                    <i class="fas fa-check me-1"></i>
                    Approve
                </a>
                <a href="' . $url_actions['rejected'] . $d . '" class="btn btn-sm btn-danger rounded px-3 me-1"
                    onclick="return confirm(\'Are you sure you want to reject this claim?\')">
                    <i class="fas fa-times me-1"></i>
                    Reject
                </a>
                ';
            return '<div class="text-center">
                ' . $buttons . '
                <a href="' . $url_actions['delete'] . $d . '" class="btn btn-sm btn-danger rounded px-3"
                    onclick="return confirm(\'Are you sure you want to delete this claim?\')">
                    <i class="fas fa-trash me-1"></i>
                    Delete
                </a>
            </div>';
        }
    ),
);

echo json_encode(
    SSP::simple($_GET, [
        'host' => DB_SERVER,
        'user' => DB_USERNAME,
        'pass' => DB_PASSWORD,
        'db' => DB_NAME,
    ], $table, $primaryKey, $columns)
);
