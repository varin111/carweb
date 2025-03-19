<?php

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/ssp.class.php';

$only_admins = $_GET['only_admins'] ?? 0;
$not_auth_user = auth()['id'];
$table = 'users';

$primaryKey = 'id';

$url_actions = [
    'edit' => SITE_URL . '/admin/users/user-action.php?action=edit&id=',
    'delete' => SITE_URL . '/admin/users/index.php?action=delete&id=',
];

if ($only_admins == 1) {
    $where = "users.is_admin = 1 AND users.id != $not_auth_user";
} else {
    $where = "users.is_admin = 0";
    $url_actions['delete'] = SITE_URL . '/admin/customers/index.php?action=delete&id=';
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
    array('db' => 'name',  'dt' => $only_admins ? 1 : 2),
    array('db' => 'username',   'dt' => $only_admins ? 2 : 3),
    array('db' => 'email',     'dt' => $only_admins ? 3 : 4),
    array('db' => 'phone',     'dt' => $only_admins ? 4 : 5),
    array('db' => 'address',     'dt' => $only_admins ? 5 : 6),
    array(
        'db' => 'date_of_birth',
        'dt' => $only_admins ? 6 : 7,
        'formatter' => function ($d, $row) {
            if ($d === null) {
                return 'N/A';
            }
            return date('Y-m-d', strtotime($d));
        },
    ),
    array(
        'db' => 'gender',
        'dt' => $only_admins ? 7 : 8,
    ),
    array(
        'db'        => 'created_at',
        'dt'        => $only_admins  ? 8 : 10,
        'formatter' => function ($d, $row) {
            return date('Y-m-d h:i:m A', strtotime($d));
        },
    ),
    array(
        'db'        => 'id',  // Use 'id' to identify the user for actions
        'dt'        => $only_admins  ? 9 : 11,
        'formatter' => function ($d, $row) use ($url_actions, $only_admins) {
            $html = '<div class="text-center d-flex gap-3">';
            if (data_get($url_actions, 'edit') !== null) {
                $html .= '
                        <a href="' . $url_actions['edit'] . $d . '" class="btn btn-sm btn-primary rounded px-3">
                            <i class="fas fa-edit me-1"></i>
                            Edit
                        </a>
                        ';
            } else {
                $html .= '<a href="' . SITE_URL . '/admin/customers/policies.php?user_id=' . $d . '" class="btn btn-sm btn-success rounded px-3">
                                <i class="fas fa-file-invoice-dollar"></i> Policies
                        </a>';
            }
            $html .= '<a href="' . $url_actions['delete'] . $d . '" class="btn btn-sm btn-danger rounded px-3"
                            onclick="return confirm(\'Are you sure you want to delete this ' . ($only_admins == 1 ? 'user' : 'customer') . '?\')">
                            <i class="fas fa-trash me-1"></i>
                            Delete
                        </a>';
            return $html . '</div>';
        }
    ),
);


if ($only_admins != 1) {
    $columns[] =  array(
        'db' => 'national_card_image',
        'dt' => 1,
        'formatter' => function ($d, $row) {
            $path = getImagePath($d);
            return '<a href="' . $path . '" target="_blank">
                <span class="avatar avatar-cover" 
                style="
                width: 100px;
                height: 50px;
                margin-top:1px;background-image: url(\'' . $path . '\');"></span>
            </a>';
        },
    );
    $columns[] =  array(
        'db' => 'id',
        'dt' => 9,
        'formatter' => function ($d, $row) {
            return count_query('vehicles', "user_id = $d");
        },
    );
}

echo json_encode(
    SSP::simple($_GET, [
        'host' => DB_SERVER,
        'user' => DB_USERNAME,
        'pass' => DB_PASSWORD,
        'db' => DB_NAME,
    ], $table, $primaryKey, $columns, $where)
);
