<?php

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/ssp.class.php';

$only_admins = $_GET['only_admins'] ?? 0;

$table = 'users';

$primaryKey = 'id';

$url_actions = [
    'edit' => SITE_URL . 'admin/users/user-action.php?action=edit&id=',
    'delete' => SITE_URL . 'admin/users/index.php?action=delete&id=',
];
if ($only_admins == 1) {
    $where = "users.is_admin = 1";
} else {
    $where = "users.is_admin = 0";
    $url_actions['delete'] = SITE_URL . 'admin/customers/index.php?action=delete&id=';
    unset($url_actions['edit']);
}

$columns = array(
    array(
        'db' => 'image_path',
        'dt' => 0,
        'formatter' => function ($d, $row) {
            return '<span class="avatar avatar-sm avatar-rounded avatar-cover" style="margin-top:1px;background-image: url(\'' . getImagePath($d) . '\');"></span>';
        }
    ),
    array('db' => 'name',  'dt' => 1),
    array('db' => 'username',   'dt' => 2),
    array('db' => 'email',     'dt' => 3),
    array('db' => 'phone',     'dt' => 4),
    array('db' => 'address',     'dt' => 5),
    array(
        'db' => 'date_of_birth',
        'dt' => 6,
        'formatter' => function ($d, $row) {
            if ($d === null) {
                return 'N/A';
            }
            return date('Y-m-d', strtotime($d));
        },
    ),
    array(
        'db' => 'gender',
        'dt' => 7,
    ),
    array(
        'db' => 'id',
        'dt' => 8,
        'formatter' => function ($d, $row) {
            return count_query('vehicles', "user_id = $d");
        },
    ),
    array(
        'db'        => 'created_at',
        'dt'        => 9,
        'formatter' => function ($d, $row) {
            return date('Y-m-d h:m:i A', strtotime($d));
        },
    ),
    array(
        'db'        => 'id',  // Use 'id' to identify the user for actions
        'dt'        => 10,
        'formatter' => function ($d, $row) use ($url_actions, $only_admins) {
            if (data_get($url_actions, 'edit') !== null) {
                return '<div class="text-center">
                        <a href="' . $url_actions['edit'] . $d . '" class="btn btn-sm btn-primary rounded px-3">
                            <i class="fas fa-edit me-1"></i>
                            Edit
                        </a>
                        <a href="' . $url_actions['delete'] . $d . '" class="btn btn-sm btn-danger rounded px-3"
                            onclick="return confirm(\'Are you sure you want to delete this ' . ($only_admins == 1 ? 'user' : 'customer') . '?\')">
                            <i class="fas fa-trash me-1"></i>
                            Delete
                        </a>
                        </div>';
            }
            return '
            <div class="text-center">
                <a href="' . $url_actions['delete'] . $d . '" class="btn btn-sm btn-danger rounded px-3"
                    onclick="return confirm(\'Are you sure you want to delete this ' . ($only_admins == 1 ? 'user' : 'customer') . '?\')">
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
    ], $table, $primaryKey, $columns, $where)
);
