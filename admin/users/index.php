<?php
ob_start();
require_once __DIR__ . '/../../config/admin/header.php';
$params = getUrlParams($_SERVER['REQUEST_URI']);
$action = data_get($params, 'action', null);
if ($action === 'delete') {
    $id = data_get($params, 'id', null);
    if ($id !== null && is_numeric($id)) {
        $user = query_select('users', '*', "id = $id AND is_admin = 1");
        if (!empty($user)) {
            $imagePath = $user['image_path'];
            if ($imagePath) {
                removeOldImage($imagePath);
            }
            query_delete('users', "id = $id AND is_admin = 1");
            setSession('user-action', [
                'type' => 'success',
                'message' => 'User deleted successfully'
            ]);
        }
    }
}
?>
<div class="page-header d-print-none mt-2 bg-white p-2 p-lg-3 p-md-2 p-sm-1 rounded-2 border mb-1">
    <div class="d-flex align-items-center justify-content-between">
        <h2 class="">
            Users List
        </h2>
        <div>
            <a href="<?= SITE_URL ?>admin/users/user-action.php?action=add" class="btn btn-primary">Add User</a>
        </div>
    </div>
</div>
<?= showSessionMessage('user-action') ?>
<div id="users-table" class="table-responsive bg-white p-2 p-lg-3 p-md-2 p-sm-1 rounded-2">
    <table class="table" id="users-data-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Date of Birth</th>
                <th>Gender</th>
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
        new DataTable("#users-data-table", {
            'processing': true,
            'serverSide': true,
            'scrollX': true,
            'language': {
                'emptyTable': '<div class="text-center fs-3 fw-bolder">No data available</div>'
            },
            'ajax': '<?= SITE_URL ?>api/users.php?only_admins=1',
        });
    });
</script>

<?php
require_once __DIR__ . '/../../config/admin/footer.php';
