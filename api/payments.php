<?php
require_once __DIR__ . '/../config/connection.php';

$user_id = data_get($_GET, 'user_id', null);
$policy_id = data_get($_POST, 'policy_id', null);

if ($user_id === null || !is_numeric($user_id)) {
    echo json_encode(['data' => []]);
    exit;
}

// Set default values for DataTables parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Build the WHERE clause using prepared statement parameters
$where_conditions = ["v.user_id = ?"];
$params = [$user_id];
$param_types = "i"; // integer for user_id

if (!empty($policy_id)) {
    $where_conditions[] = "p.policy_id = ?";
    $params[] = $policy_id;
    $param_types .= "i"; // integer for policy_id
}

if (!empty($search)) {
    $where_conditions[] = "(
        pol.coverage_type LIKE ? OR 
        v.make LIKE ? OR 
        v.model LIKE ? OR 
        p.payment_method LIKE ? OR 
        p.payment_status LIKE ?
    )";
    $search_param = "%$search%";
    for ($i = 0; $i < 5; $i++) {
        $params[] = $search_param;
        $param_types .= "s"; // string for search terms
    }
}

$where_clause = implode(" AND ", $where_conditions);

// Count total records using prepared statement
$count_sql = "SELECT COUNT(*) as total FROM payments p 
    JOIN vehicles v ON p.vehicle_id = v.id 
    JOIN policies pol ON p.policy_id = pol.id 
    WHERE " . $where_clause;

$stmt = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($stmt, $param_types, ...$params);
mysqli_stmt_execute($stmt);
$count_result = mysqli_stmt_get_result($stmt);
$total = mysqli_fetch_assoc($count_result)['total'];
mysqli_stmt_close($stmt);

// Main query for data using prepared statement
$main_sql = "SELECT 
    p.*,
    pol.coverage_type as policy_name,
    pol.type as policy_type,
    CONCAT(v.make, ' ', v.model, ' (', v.year, ')') as vehicle
FROM payments p 
JOIN vehicles v ON p.vehicle_id = v.id 
JOIN policies pol ON p.policy_id = pol.id 
WHERE " . $where_clause . "
ORDER BY p.payment_date DESC 
LIMIT ?, ?";

$stmt = mysqli_prepare($conn, $main_sql);
$params[] = $start;
$params[] = $length;
$param_types .= "ii"; // two integers for LIMIT parameters

mysqli_stmt_bind_param($stmt, $param_types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$payments = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$data = [];
foreach ($payments as $payment) {
    $data[] = [
        'payment_id' => $payment['id'],
        'policy_name' => $payment['policy_name'] . ' (' . $payment['policy_type'] . ')',
        'vehicle' => $payment['vehicle'],
        'amount' => $payment['amount_paid'],
        'payment_date' => date('Y-m-d H:i:s', strtotime($payment['payment_date'])),
        'end_date' => $payment['end_date_payment'] ? date('Y-m-d', strtotime($payment['end_date_payment'])) : '-',
        'payment_method' => $payment['payment_method'],
        'status' => $payment['payment_status']
    ];
}

echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $total,
    'recordsFiltered' => $total,
    'data' => $data
]);