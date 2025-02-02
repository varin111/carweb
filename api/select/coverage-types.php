<?php

require_once __DIR__ . '/../../config/connection.php';
$params = getUrlParams($_SERVER['REQUEST_URI']);
$search = !empty($params['search']) ? $params['search'] : '';
$page = !empty($params['page']) ? $params['page'] : 1;

$sql = "SELECT DISTINCT * FROM policies
        WHERE coverage_type LIKE '%$search%'
        ORDER BY id DESC
        LIMIT 10 OFFSET " . ($page - 1) * 10;


$policies = mysqli_query($conn, $sql);

$policiesData = [];
if (mysqli_num_rows($policies) > 0) {
    while ($row = mysqli_fetch_assoc($policies)) {
        $data['id'] = $row['id'];
        $data['text'] = $row['coverage_type'];
        array_push($policiesData, $data);
    }
}

echo json_encode([
    'results' => $policiesData,
    'pagination' => ['more' => mysqli_num_rows($policies) == 10 ? true : false] // Adjust 'more' as needed for pagination
]);
