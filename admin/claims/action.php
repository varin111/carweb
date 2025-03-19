<?php
ob_start();
require_once __DIR__ . '/../../config/admin/header.php';
$params = getUrlParams($_SERVER['REQUEST_URI']);
$claim_id = data_get($params, 'id', null);
$action = data_get($params, 'action', null);

if ($action === 'approved' || $action === 'rejected') {
    if ($claim_id === null || !is_numeric($claim_id)) {
        setSession('claim-action', [
            'type' => 'danger',
            'message' => 'Claim not found'
        ]);
        header("Location: " . SITE_URL . "/admin/claims/index.php");
        exit;
    }
    $claim = query_select('claims', '*', "id = $claim_id");
    if (!empty($claim)) {
        $status = $action === 'approved' ? 'approved' : 'rejected';
        if ($status === 'approved') {
            // increase the claim amount to the vehicle balance
            $vehicle_balance = query_select('vehicles', 'balance', "id = " . $claim['vehicle_id']);
            $new_balance = $vehicle_balance['balance'] - $claim['amount'];
            if ($new_balance < 0) {
                setSession('claim-action', [
                    'type' => 'danger',
                    'message' => 'Vehicle balance is not enough to approve this claim amount should be less than ' . $vehicle_balance['balance']
                ]);
                header("Location: " . SITE_URL . "/admin/claims/index.php");
                exit;
            }
            query_update('vehicles', ['balance' => $new_balance], "id = " . $claim['vehicle_id']);
        }
        $data = [
            'status' => $status,
        ];
        if ($status === 'approved') {
            $data['approved_at'] = date('Y-m-d H:i:s');
        } else {
            $data['rejected_at'] = date('Y-m-d H:i:s');
        }
        query_update('claims', $data, "id = $claim_id");
        setSession('claim-action', [
            'type' => 'success',
            'message' => 'Claim ' . $status . ' successfully'
        ]);
    }
    header("Location: " . SITE_URL . "/admin/claims/index.php");
} else {
    setSession('claim-action', [
        'type' => 'danger',
        'message' => 'Invalid action'
    ]);
    header("Location: " . SITE_URL . "/admin/claims/index.php");
}
