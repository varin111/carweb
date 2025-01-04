<?php

require_once __DIR__ . '/../../config/connection.php';
$params = getUrlParams($_SERVER['REQUEST_URI']);
$search = !empty($params['search']) ? $params['search'] : '';
$page = !empty($params['page']) ? $params['page'] : 1;

$sql = "SELECT * FROM users
        WHERE is_admin = 0 AND name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%'
        order by users.id desc
        LIMIT 10 OFFSET " . ($page - 1) * 10;

$users = mysqli_query($conn, $sql);

$usersData = [];
if (mysqli_num_rows($users) > 0) {
    while ($row = mysqli_fetch_assoc($users)) {
        $data['id'] = $row['id'];
        $data['text'] = $row['name'] . ' - ' . $row['email'];
        array_push($usersData, $data);
    }
}

echo json_encode([
    'results' => $usersData,
    'pagination' => ['more' => mysqli_num_rows($users) == 10 ? true : false] // Adjust 'more' as needed for pagination
]);
