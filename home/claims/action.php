<?php
require_once __DIR__ . '/../../config/front/header.php';
if (empty(getSession('user_id')) && empty($_COOKIE['user_login'])) {
    header("Location: " . SITE_URL . "login.php");
    exit;
}
$currentDate = date('Y-m-d H:i:s');
$params = getUrlParams($_SERVER['REQUEST_URI']);
$vehicle_id = data_get($params, 'vehicle_id', null);

if ($vehicle_id === null) {
    setSession(
        'vehicle-action',
        [
            'type' => 'danger',
            'message' => 'Vehicle not found.'
        ]
    );
    header("Location: " . SITE_URL . "home/vehicles/index.php");
    exit;
}
$vehicle = query_select('vehicles', '*', "id = $vehicle_id AND user_id = $auth[id]");
if (empty($vehicle)) {
    setSession(
        'vehicle-action',
        [
            'type' => 'danger',
            'message' => 'Vehicle not found.'
        ]
    );
    header("Location: " . SITE_URL . "home/vehicles/index.php");
    exit;
} elseif ($vehicle['balance'] == 0 || $vehicle['balance'] == null) {
    setSession(
        'vehicle-action',
        [
            'type' => 'danger',
            'message' => 'Vehicle balance is 0. Please pay the policy to get the balance and then try to claim.'
        ]
    );
    header("Location: " . SITE_URL . "home/vehicles/index.php");
    exit;
}

$errors = [
    'description' => '',
    'amount' => '',
    'policy_id' => '',
];
$values = [
    'description' => '',
    'amount' => '',
    'policy_id' => '',
];
if (data_get($params, 'action') === null || data_get($params, 'action') !== 'create') {
    header("Location: " . SITE_URL . "home/claims/action.php?action=create");
    exit;
} else {
    $action = data_get($params, 'action', 'create');
}

$title = ucfirst($action) . ' Claim';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $values['description'] = clear($_POST['description']);
    $values['amount'] = clear($_POST['amount']);
    $values['policy_id'] = clear($_POST['policy_id'] ?? '');

    if (empty($values['description'])) {
        $errors['description'] = 'Description is required';
    } elseif (validate_string($values['description'], 3, 1000) === false) {
        $errors['description'] = 'Description must be a string and between 3 and 1000 characters';
    }

    if (empty($values['policy_id'])) {
        $errors['policy_id'] = 'Policy is required';
    } elseif (data_exists(
        table: 'vehicle_policies',
        columns: 'vehicle_policies.id',
        join: 'LEFT JOIN policies ON vehicle_policies.policy_id = policies.id
        LEFT JOIN vehicles ON vehicle_policies.vehicle_id = vehicles.id',
        where: "policies.id = {$values['policy_id']} AND vehicle_policies.vehicle_id = $vehicle_id AND vehicle_policies.user_id = $auth[id]"
    ) === false) {
        $errors['policy_id'] = 'Policy not found';
    }

    if (empty($values['amount'])) {
        $errors['amount'] = 'Amount is required';
    } elseif (is_numeric($values['amount']) == false) {
        $errors['amount'] = 'Amount must be a number';
    } elseif ($values['amount'] > $vehicle['balance']) {
        $errors['amount'] = 'Amount must be less than or equal to vehicle balance (' . $vehicle['balance'] . '$)';
    }


    if (empty(array_filter($errors))) {
        $data = [
            'vehicle_id' => $vehicle_id,
            'policy_id' => $values['policy_id'],
            'description' => $values['description'],
            'amount' => $values['amount'],
        ];
        query_insert('claims', $data);
        setSession('claim-action', [
            'type' => 'success',
            'message' => 'Claim added successfully and waiting for approval by the admin.'
        ]);
        header("Location: " . SITE_URL . "home/claims/index.php?vehicle_id=$vehicle_id");
        exit;
    }
}

$selectPolicy = query_select_with_join(
    table: 'vehicle_policies',
    join: 'LEFT JOIN policies ON vehicle_policies.policy_id = policies.id
        LEFT JOIN vehicles ON vehicle_policies.vehicle_id = vehicles.id',
    columns: 'vehicle_policies.*, policies.coverage_type as coverage_type',
    where: "vehicle_policies.vehicle_id = $vehicle_id 
            AND policies.status = 'enable' 
            AND policies.start_date <= '$currentDate' 
            AND policies.end_date >= '$currentDate' 
            AND vehicle_policies.policy_id = " . ($values['policy_id'] ?: '0')
);
$selectedPolicy = null;
if (!empty($selectPolicy) && count($selectPolicy) > 0) {
    $selectedPolicy = [
        'id' => $selectPolicy[0]['policy_id'],
        'text' => $selectPolicy[0]['coverage_type']
    ];
}
$dataSelected = $selectedPolicy ? json_encode($selectedPolicy) : '{}';

?>
<div style="margin-block: 7.5rem;" class="container">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="<?= SITE_URL ?>home/claims/index.php?vehicle_id=<?= $vehicle_id; ?>" class="btn btn-outline-primary btn-sm px-3 p-1 rounded-2">
            <i class="fas fa-arrow-left me-1"></i>
            Back
        </a>
        <h1 class="text-center">
            <?= $title; ?>
        </h1>
    </div>
    <div class="row row-cards">
        <div class="col-12">
            <form class="card"
                action="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?action=create&vehicle_id=' . $vehicle_id); ?>"
                method="POST">
                <div class="card-body">
                    <h3 class="size fs-1 mb-2">
                        <?= $title; ?>
                    </h3>
                    <div class="row row-cards">
                        <div class="col-12">
                            <p class="fs-4 fw-bold mb-1">
                                Vehicle license plate: <?= $vehicle['license_plate']; ?>
                            </p>
                            <p class="fs-4 fw-bold">
                                Your vehicle balance is: <?= $vehicle['balance']; ?>$
                            </p>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label">
                                    Description
                                </label>
                                <textarea class="form-control" placeholder="Enter Description"
                                    rows="10"
                                    name="description"><?= $values['description']; ?></textarea>
                            </div>
                            <?= showErrors($errors['description']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">
                                    Amount
                                </label>
                                <input type="text" class="form-control" placeholder="Enter Amount"
                                    name="amount"
                                    max="<?= $vehicle['balance']; ?>"
                                    value="<?= $values['amount']; ?>">
                            </div>
                            <?= showErrors($errors['amount']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">
                                    Policy
                                </label>
                                <select class="form-select"
                                    id="policy_id"
                                    name="policy_id"
                                    data-selected='<?= $dataSelected; ?>'
                                    data-placeholder="Select Policy">
                                </select>
                            </div>
                            <?= showErrors($errors['policy_id']); ?>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary"
                            name="submit">
                            <?= $title; ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        let selectedValues = $('#policy_id').attr('data-selected');
        selectedValues = selectedValues ? JSON.parse(selectedValues) : null;

        $('#policy_id').select2({
            ajax: {
                url: '<?= SITE_URL ?>api/select/policies-claims.php?vehicle_id=<?= $vehicle_id; ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            placeholder: 'Select Vehicle',
            minimumInputLength: 1,
        });

        if (selectedValues) {
            $('#policy_id').append(new Option(selectedValues.text, selectedValues.id, true, true));
        }
    });
</script>
<?php
require_once __DIR__ . '/../../config/front/footer.php';
