<?php
ob_start();
require_once __DIR__ . '/../../config/admin/header.php';
$params = getUrlParams($_SERVER['REQUEST_URI']);
$action = data_get($params, 'action', null);
$id = data_get($params, 'id', null);
if ($action == 'delete') {
    if ($id !== null && is_numeric($id)) {
        $policy = query_select('policies', '*', "id = $id");
        if (!empty($policy)) {
            query_delete('policies', "id = $id");
            setSession('policy-action', [
                'type' => 'success',
                'message' => 'Policy deleted successfully',
            ]);
        }
    }
}

?>
<div class="page-header d-print-none mt-2 bg-white p-2 p-lg-3 p-md-2 p-sm-1 rounded-2 border mb-1">
    <div class="d-flex align-items-center justify-content-between">
        <h2 class="page-title">
            Policies List
        </h2>
        <div>
            <a href="<?= SITE_URL ?>admin/policies/policy-action.php?action=add" class="btn btn-primary">Add Policy</a>
        </div>
    </div>
</div>
<?= showSessionMessage('policy-action') ?>
<div id="policies-table" class="table-responsive bg-white p-2 p-lg-3 p-md-2 p-sm-1 rounded-2">
    <table class="table" id="policies-data-table">
        <thead>
            <tr>
                <th>#Id</th>
                <th>Coverage Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Premium Amount</th>
                <th>Status</th>
                <th>Created At</th>
                <th class="text-center">actions</th>
            </tr>
        </thead>
        <tbody class="table-tbody">
        </tbody>
    </table>
</div>
<script>
    window.addEventListener('DOMContentLoaded', () => {
        new DataTable("#policies-data-table", {
            'processing': true,
            'serverSide': true,
            'scrollX': true,
            'language': {
                'emptyTable': '<div class="text-center fs-3 fw-bolder">No data available</div>'
            },
            'ajax': '<?= SITE_URL ?>api/policies.php',
        });
    });
</script>

<?php
require_once __DIR__ . '/../../config/admin/footer.php';
