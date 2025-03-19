<?php
ob_start();
require_once __DIR__ . '/../../config/admin/header.php';
$params = getUrlParams($_SERVER['REQUEST_URI']);
$policy_id = clear(data_get($params, 'id', null));
$errors = [
    'vehicle_ids' => '',
];
$values = [
    'vehicle_ids' => [],
];

if ($policy_id === null || !is_numeric($policy_id)) {
    setSession('policy-action', [
        'type' => 'error',
        'message' => 'Policy not found',
    ]);
    header("Location: " . SITE_URL . "/admin/policies/index.php");
    exit;
} else {
    $policy = query_select('policies', '*', "id = " . $policy_id);
    if ($policy == null) {
        setSession('policy-action', [
            'type' => 'error',
            'message' => 'Policy not found',
        ]);
        header("Location: " . SITE_URL . "/admin/policies/index.php");
        exit;
    }

    $values['vehicle_ids'] = array_map(
        fn($vehicle) => $vehicle['id'],
        selectAll('vehicle_policies', 'vehicle_id as id', "policy_id = $policy_id")
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $values['vehicle_ids'] = clearArray($_POST['vehicle_ids'] ?: []);

    if (!empty($values['vehicle_ids'])) {
        if (!is_array($values['vehicle_ids'])) {
            $errors['vehicle_ids'] = 'Vehicle must be an array';
        } elseif ((array_filter($values['vehicle_ids'], 'is_numeric') !== $values['vehicle_ids'])) {
            $errors['vehicle_ids'] = 'Vehicle is invalid';
        } else {
            $errors['vehicle_ids'] = '';
        }
    }

    if (empty(array_filter($errors))) {
        // Fetch existing vehicle IDs for the policy from the database
        $existingVehiclePolicies = selectAll(
            'vehicle_policies',
            'vehicle_id',
            "policy_id = {$policy_id}"
        );
        $existingVehicleIds = array_column($existingVehiclePolicies, 'vehicle_id'); // Extract only the IDs
        // Determine which vehicles to delete
        $vehiclesToDelete = array_diff($existingVehicleIds, $values['vehicle_ids']);
        // Determine which vehicles to add
        $vehiclesToAdd = array_diff($values['vehicle_ids'], $existingVehicleIds);

        // Delete vehicles no longer associated with the policy
        if (!empty($vehiclesToDelete) && count($vehiclesToDelete) > 0) {
            $vehiclesToDelete = implode(',', $vehiclesToDelete);
            query_delete('vehicle_policies', "policy_id = $policy_id AND vehicle_id IN ($vehiclesToDelete)");
        }

        // Add new vehicles to the policy
        foreach ($vehiclesToAdd as $vehicleId) {
            $vehicle = query_select('vehicles', '*', "id = $vehicleId");
            if ($vehicle != null) {
                query_insert('vehicle_policies', [
                    'policy_id' => $policy_id,
                    'user_id' => $vehicle['user_id'],
                    'vehicle_id' => $vehicle['id'],
                ]);
            }
        }
        setSession('policy-action', [
            'type' => 'success',
            'message' => 'Vehicle assigned successfully',
        ]);
        header("Location: " . SITE_URL . "/admin/policies/index.php");
    }
}
$selectedVehicles = [];
if (!empty($values['vehicle_ids'])) {
    $ids = implode(',', $values['vehicle_ids']) ?? '';
    if (empty($ids)) {
        $ids = 0;
    }
    $sql = "SELECT vehicles.id, vehicles.license_plate,vehicles.user_id, users.name as user_name
        FROM vehicles 
        LEFT JOIN users ON vehicles.user_id = users.id
        WHERE vehicles.id IN ($ids)";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $selectedVehicles[] = [
                'id' => $row['id'],
                'text' => $row['user_name'] . ' - ' . $row['license_plate'],
            ];
        }
    }
}
?>
<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="<?= SITE_URL ?>/admin/policies/index.php" class="btn btn-primary btn-sm px-3 p-1 rounded-3">
            <i class="fas fa-arrow-left me-1"></i>
            Back
        </a>
    </div>
    <div class="row row-cards">
        <div class="col-12">
            <form class="card"
                action="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?id=' . $policy_id) ?>"
                method="POST"
                enctype="multipart/form-data">
                <div class="card-body">
                    <h3 class="card-title size">
                        Assign Vehicle
                    </h3>
                    <div class="row row-cards">
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label d-flex align-items-center justify-content-between">
                                    <span>Vehicle</span>
                                    <div>
                                        <div class="btn btn-sm btn-danger rounded-2 p-1 px-2" onclick="$('#vehicle_ids').val(null).trigger('change')">
                                            <i class="fas fa-times"></i>
                                        </div>
                                    </div>
                                </label>
                                <select class="form-select"
                                    id="vehicle_ids"
                                    name="vehicle_ids[]"
                                    data-placeholder="Select Vehicle"
                                    data-selected='<?= json_encode($selectedVehicles) ?>'
                                    multiple>
                                </select>
                            </div>
                            <?= showErrors($errors['vehicle_ids']); ?>
                        </div>
                    </div>
                </div>
                <div class="text-end p-3 pt-0">
                    <button type="submit" class="btn btn-primary px-3 p-1 rounded-3"
                        name="submit">
                        <i class="fas fa-save me-1"></i>
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        let selectedValues = JSON.parse($('#vehicle_ids').attr('data-selected'));

        $('#vehicle_ids').select2({
            ajax: {
                url: '<?= SITE_URL ?>/api/select/vehicles.php',
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
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });
        if (selectedValues && selectedValues.length > 0) {
            selectedValues.forEach(value => {
                $('#vehicle_ids').append(new Option(value.text, value.id, true, true));
            });
        }
    });

    function formatRepo(repo) {
        if (repo.loading) {
            return repo.text;
        }

        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title fw-bold'></div>" +
            "<div class='select2-result-repository__description text-muted'></div>" +
            "</div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.text);
        $container.find(".select2-result-repository__description").text(repo.description || '');

        return $container;
    }

    function formatRepoSelection(repo) {
        return repo.text || repo.description;
    }
</script>
<?php
require_once __DIR__ . '/../../config/admin/footer.php';
