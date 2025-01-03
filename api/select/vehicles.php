<?php

require_once __DIR__ . '/../../config/connection.php';
$params = getUrlParams($_SERVER['REQUEST_URI']);
$search = !empty($params['search']) ? $params['search'] : '';
$page = !empty($params['page']) ? $params['page'] : 1;

$sql = "SELECT vehicles.*, users.name as user_name FROM vehicles
        LEFT JOIN users ON vehicles.user_id = users.id
        WHERE license_plate LIKE '%$search%' OR make LIKE '%$search%'
        order by vehicles.id desc
        LIMIT 10 OFFSET " . ($page - 1) * 10;

$vehicles = mysqli_query($conn, $sql);

$vehiclesData = [];
if (mysqli_num_rows($vehicles) > 0) {
    while ($row = mysqli_fetch_assoc($vehicles)) {
        $data['id'] = $row['id'];
        $data['text'] = $row['user_name'] . ' - ' . $row['license_plate'];
        $data['description'] = $row['license_plate'] . ' - ' . $row['make'] . ' - ' . $row['model'] . ' - year:' . $row['year'] . ' - vin:' . $row['vin'];
        array_push($vehiclesData, $data);
    }
}

echo json_encode([
    'results' => $vehiclesData,
    'pagination' => ['more' => mysqli_num_rows($vehicles) == 10 ? true : false] // Adjust 'more' as needed for pagination
]);
