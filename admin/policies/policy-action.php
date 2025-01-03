<?php
ob_start();
require_once __DIR__ . '/../../config/admin/header.php';
$params = getUrlParams($_SERVER['REQUEST_URI']);
$errors = [
    'coverage_type' => '',
    'start_date' => '',
    'end_date' => '',
    'premium_amount' => '',
    'status' => '',
];
$values = [
    'coverage_type' => '',
    'start_date' => '',
    'end_date' => '',
    'premium_amount' => '',
    'status' => '',
];
if (data_get($params, 'action') !== null && data_get($params, 'action') === 'toggle-status' && data_get($params, 'id') !== null && is_numeric(data_get($params, 'id'))) {
    $id = clear(data_get($params, 'id'));
    $policy = query_select('policies', '*', "id = " . $id);
    if ($policy !== null) {
        $status = $policy['status'] === 'enable' ? 'disable' : 'enable';
        query_update('policies', ['status' => $status], 'id = ' . $id);
        setSession('policy-action', [
            'type' => 'success',
            'message' => 'Policy status updated successfully',
        ]);
        header("Location: " . SITE_URL . "admin/policies/index.php");
        exit;
    }
}

if (data_get($params, 'action') === null || (data_get($params, 'action') !== 'add' && data_get($params, 'action') !== 'edit')) {
    header("Location: " . SITE_URL . "admin/policies/policy-action.php?action=add");
    exit;
} else {
    $action = data_get($params, 'action', 'add');
}

if (data_get($params, 'action') === 'edit' && (data_get($params, 'id') === null || !is_numeric(data_get($params, 'id')))) {
    header("Location: " . SITE_URL . "admin/policies/policy-action.php?action=add");
    exit;
} elseif (data_get($params, 'action') === 'edit' && data_get($params, 'id') !== null) {
    $policy = query_select('policies', '*', "id = " . data_get($params, 'id'));
    if ($policy == null) {
        setSession('policy-action', [
            'type' => 'error',
            'message' => 'Policy not found',
        ]);
        header("Location: " . SITE_URL . "admin/policies/index.php");
        exit;
    } else {
        $values['coverage_type'] = $policy['coverage_type'];
        $values['start_date'] = $policy['start_date'];
        $values['end_date'] = $policy['end_date'];
        $values['premium_amount'] = $policy['premium_amount'];
        $values['status'] = $policy['status'];
    }
}

$title = data_get($params, 'action') === 'edit' ? 'Edit Policy' : 'Add Policy';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $values['coverage_type'] = clear($_POST['coverage_type']);
    $values['start_date'] = clear($_POST['start_date']);
    $values['end_date'] = clear($_POST['end_date']);
    $values['premium_amount'] = clear($_POST['premium_amount']);
    $values['status'] = clear($_POST['status']);
    // change format of date to Y-m-d H:i:s
    $values['start_date'] = !empty($values['start_date']) ? date('Y-m-d H:i:s', strtotime($values['start_date'])) : '';
    $values['end_date'] = !empty($values['end_date']) ? date('Y-m-d H:i:s', strtotime($values['end_date'])) : '';
    var_dump($values['start_date']);

    if (empty($values['coverage_type'])) {
        $errors['coverage_type'] = 'Coverage Type is required';
    } elseif (validate_string(
        value: $values['coverage_type'],
        min: 3,
        max: 255,
        another_preg: ' \_\-,.!?'
    ) === false) {
        $errors['coverage_type'] = 'Coverage Type must be a string and between 3 and 255 characters';
    } else {
        $errors['coverage_type'] = '';
    }

    if (empty($values['start_date'])) {
        $errors['start_date'] = 'Start Date is required';
    } elseif (validate_date($values['start_date'], 'Y-m-d H:i:s') === false) {
        $errors['start_date'] = 'Start Date must be a valid date';
    } else {
        $errors['start_date'] = '';
    }

    if (empty($values['end_date'])) {
        $errors['end_date'] = 'End Date is required';
    } elseif (validate_date($values['end_date'], 'Y-m-d H:i:s') === false) {
        $errors['end_date'] = 'End Date must be a valid date';
    } elseif (strtotime($values['end_date']) <= strtotime($values['start_date'])) {
        $errors['end_date'] = 'End Date must be greater than Start Date';
    } else {
        $errors['end_date'] = '';
    }

    if (empty($values['premium_amount'])) {
        $errors['premium_amount'] = 'Premium Amount is required';
    } elseif (is_numeric($values['premium_amount']) === false) {
        $errors['premium_amount'] = 'Premium Amount must be a number';
    } else {
        $errors['premium_amount'] = '';
    }

    if (empty($values['status'])) {
        $errors['status'] = 'Status is required';
    } elseif ($values['status'] !== 'enable' && $values['status'] !== 'disable') {
        $errors['status'] = 'Status must be enable or disable';
    } else {
        $errors['status'] = '';
    }

    if (empty(array_filter($errors))) {
        $data = [
            'coverage_type' => $values['coverage_type'],
            'start_date' => $values['start_date'],
            'end_date' => $values['end_date'],
            'premium_amount' => $values['premium_amount'],
            'status' => $values['status'],
        ];
        if ($action == 'add') {
            query_insert('policies', $data);
        } else {
            query_update('policies', $data, 'id = ' . data_get($params, 'id'));
        }
        setSession('policy-action', [
            'type' => 'success',
            'message' =>
            $action == 'add' ? 'Policy added successfully' : 'Policy updated successfully',
        ]);
        header("Location: " . SITE_URL . "admin/policies/index.php");
    }
}

?>
<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="<?= SITE_URL ?>admin/policies/index.php" class="btn btn-primary btn-sm px-3 p-1 rounded-3">
            <i class="fas fa-arrow-left me-1"></i>
            Back
        </a>
    </div>
    <div class="row row-cards">
        <div class="col-12">
            <form class="card"
                action="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?action=' . $action . ($action === 'edit' ? '&id=' . data_get($params, 'id') : '')); ?>"
                method="POST"
                enctype="multipart/form-data">
                <div class="card-body">
                    <h3 class="card-title size">
                        <?= $title; ?>
                    </h3>
                    <div class="row row-cards">
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label">
                                    Coverage Type
                                </label>
                                <input type="text" class="form-control" placeholder="Enter Coverage Type"
                                    name="coverage_type"
                                    value="<?= $values['coverage_type']; ?>">
                            </div>
                            <?= showErrors($errors['coverage_type']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">
                                    Start Date
                                </label>
                                <input type="datetime-local" class="form-control" placeholder="Enter Start Date"
                                    name="start_date"
                                    value="<?= $values['start_date']; ?>">
                            </div>
                            <?= showErrors($errors['start_date']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">
                                    End Date
                                </label>
                                <input type="datetime-local" class="form-control" placeholder="Enter End Date"
                                    name="end_date"
                                    value="<?= $values['end_date']; ?>">
                            </div>
                            <?= showErrors($errors['end_date']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">
                                    Premium Amount
                                </label>
                                <input type="text" class="form-control" placeholder="Enter Premium Amount"
                                    name="premium_amount"
                                    value="<?= $values['premium_amount']; ?>">
                            </div>
                            <?= showErrors($errors['premium_amount']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">
                                    Status
                                </label>
                                <select class="form-select" name="status">
                                    <option value="">Select Status</option>
                                    <option value="enable" <?= $values['status'] === 'enable' ? 'selected' : ''; ?>>Enable</option>
                                    <option value="disable" <?= $values['status'] === 'disable' ? 'selected' : ''; ?>>Disable</option>
                                </select>
                            </div>
                            <?= showErrors($errors['status']); ?>
                        </div>
                    </div>
                </div>
                <div class="text-end p-3">
                    <button type="submit" class="btn btn-primary px-3 p-1 rounded-3"
                        name="submit">
                        <?= $title; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php
require_once __DIR__ . '/../../config/admin/footer.php';
