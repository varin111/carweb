<?php

require_once __DIR__ . '/../../config/connection.php';
$params = getUrlParams($_SERVER['REQUEST_URI']);
$search = !empty($params['search']) ? $params['search'] : '';
$page = !empty($params['page']) ? $params['page'] : 1;
$vehicle_id = !empty($params['vehicle_id']) ? $params['vehicle_id'] : null;
$currentDate = date('Y-m-d H:i:s');
if (empty($vehicle_id)) {
    echo json_encode([
        'results' => [],
        'pagination' => ['more' => false] // Adjust 'more' as needed for pagination
    ]);
    exit;
}

$sql = "SELECT vehicle_policies.*,policies.coverage_type as coverage_type, payments.end_date_payment as end_date_payment FROM vehicle_policies
        JOIN policies ON vehicle_policies.policy_id = policies.id
        JOIN vehicles ON vehicle_policies.vehicle_id = vehicles.id
        JOIN payments ON vehicle_policies.policy_id = payments.policy_id AND vehicle_policies.vehicle_id = payments.vehicle_id
        WHERE vehicle_policies.vehicle_id = $vehicle_id AND payments.end_date_payment >= '$currentDate'
        AND policies.coverage_type LIKE '%$search%'
        order by policies.id desc
        LIMIT 10 OFFSET " . ($page - 1) * 10;

$policies = mysqli_query($conn, $sql);

$policiesData = [];
if (mysqli_num_rows($policies) > 0) {
    while ($row = mysqli_fetch_assoc($policies)) {
        $data['id'] = $row['policy_id'];
        $data['text'] = $row['coverage_type'];
        array_push($policiesData, $data);
    }
}

echo json_encode([
    'results' => $policiesData,
    'pagination' => ['more' => mysqli_num_rows($policies) == 10 ? true : false] // Adjust 'more' as needed for pagination
]);
