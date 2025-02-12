<?php

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/ssp.class.php';

$table = 'policies';
$primaryKey = 'id';

$url_actions = [
    'edit' => SITE_URL . 'admin/policies/user-action.php?action=edit&id=',
    'delete' => SITE_URL . 'admin/policies/index.php?action=delete&id=',
];

$columns = array(
    array(
        'db' => 'id',
        'dt' => 0,
        'formatter' => function ($d, $row) {
            return md5($d + 1);
        },
    ),
    array('db' => 'coverage_type', 'dt' => 1),
    array(
        'db' => 'start_date',
        'dt' => 2,
        'formatter' => function ($d, $row) {
            return date('Y-m-d', strtotime($d));
        }
    ),
    array(
        'db' => 'end_date',
        'dt' => 3,
        'formatter' => function ($d, $row) {
            return date('Y-m-d', strtotime($d));
        }
    ),
    array(
        'db' => 'end_date',
        'dt' => 4,
        'formatter' => function ($d, $row) {
            $expired = strtotime($d) < strtotime(date('Y-m-d'));
            return $expired ? '<span class="badge bg-danger">Expired</span>' : '<span class="badge bg-success">Active</span>';
        }
    ),
    array(
        'db' => 'premium_amount',
        'dt' => 5,
        'formatter' => function ($d, $row) {
            return format_currency($d) . ' $';
        }
    ),
    array(
        'db' => 'status',
        'dt' => 6,
        'formatter' => function ($d, $row) {
            return $d == 'enable' ? '<span class="badge bg-success">Enable</span>' : '<span class="badge bg-danger">Disable</span>';
        }
    ),
    array(
        'db' => 'type',
        'dt' => 7,
        'formatter' => function ($d, $row) {
            $html = match ($d) {
                'Standard' => '<span class="badge bg-primary">Standard</span>',
                'Gold' => '<span class="badge bg-warning">Gold</span>',
                'Platinum' => '<span class="badge bg-info">Platinum</span>',
                'Premium' => '<span class="badge bg-success">Premium</span>',
                default => '<span class="badge bg-secondary">Unknown</span>',
            };
            return $html;
        }
    ),
    array(
        'db' => 'created_at',
        'dt' => 8,
        'formatter' => function ($d, $row) {
            return date('Y-m-d h:i:m A', strtotime($d));
        },
    ),
    array(
        'db' => 'id',
        'dt' => 9,
        'formatter' => function ($d, $row) {
            return '
            <div class="d-flex align-items-center justify-content-center gap-1 text-center">
                <label class="form-check form-switch"
                    onclick="return confirm(\'Are you sure you want to change the status of this policy?\')"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="Change status"
                    data-bs-original-title="Change status">
                    <input class="form-check-input" type="checkbox" id="' . md5($d) . '" 
                    ' . ($row['status'] == 'enable' ? 'checked' : '') . '
                    onchange="window.location.href = \'' . SITE_URL . 'admin/policies/policy-action.php?action=toggle-status&id=' . $d . '\'" />
                </label>
                <a href="' . SITE_URL . 'admin/policies/assign-vehicle.php?id=' . $d . '" class="btn btn-sm btn-primary me-1">
                    <i class="fas fa-car me-1"></i>
                    Assign Vehicle
                </a>
                <a href="' . SITE_URL . 'admin/policies/policy-action.php?action=edit&id=' . $d . '" class="btn btn-sm btn-primary me-1">
                    <i class="fas fa-edit me-1"></i>
                    Edit
                </a>
                <a href="' . SITE_URL . 'admin/policies/index.php?action=delete&id=' . $d . '" class="btn btn-sm btn-danger"
                    onclick="return confirm(\'Are you sure you want to delete this policy?\')">
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
