<?php
ob_start();
require_once __DIR__ . '/../../config/admin/header.php';
$params = getUrlParams($_SERVER['REQUEST_URI']);
$errors = [
    'name' => '',
    'username' => '',
    'email' => '',
    'phone' => '',
    'date_of_birth' => '',
    'gender' => '',
    'address' => '',
    'password' => '',
    'confirm_password' => '',
    'image' => '',
];
$values = [
    'name' => '',
    'username' => '',
    'email' => '',
    'phone' => '',
    'date_of_birth' => '',
    'gender' => '',
    'address' => '',
    'password' => '',
    'confirm_password' => '',
    'image' => '',
];
$oldImage = null;

if (data_get($params, 'action') === null || (data_get($params, 'action') !== 'add' && data_get($params, 'action') !== 'edit')) {
    header("Location: " . SITE_URL . "/admin/users/user-action.php?action=add");
    exit;
} else {
    $action = data_get($params, 'action', 'add');
}

if (data_get($params, 'action') === 'edit' && (data_get($params, 'id') === null || !is_numeric(data_get($params, 'id')))) {
    header("Location: " . SITE_URL . "/admin/users/user-action.php?action=add");
    exit;
} elseif (data_get($params, 'action') === 'edit' && data_get($params, 'id') !== null) {
    $user = query_select('users', '*', "id = " . data_get($params, 'id'));
    if ($user == null) {
        setSession('user-action', [
            'type' => 'error',
            'message' => 'User not found',
        ]);
        header("Location: " . SITE_URL . "/admin/users/index.php");
        exit;
    } else {
        $values['name'] = $user['name'];
        $values['username'] = $user['username'];
        $values['email'] = $user['email'];
        $values['phone'] = $user['phone'];
        $values['date_of_birth'] = $user['date_of_birth'];
        $values['gender'] = $user['gender'];
        $values['address'] = $user['address'];
        $oldImage = $user['image_path'];
    }
}

$title = data_get($params, 'action') === 'edit' ? 'Edit User' : 'Add User';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $values['name'] = clear($_POST['name']);
    $values['username'] = clear($_POST['username']);
    $values['email'] = clear($_POST['email']);
    $values['phone'] = clear($_POST['phone']);
    $values['date_of_birth'] = clear($_POST['date_of_birth']);
    $values['gender'] = clear($_POST['gender']);
    $values['address'] = clear($_POST['address']);
    if ($action === 'add') {
        $values['password'] = clear($_POST['password']);
        $values['confirm_password'] = clear($_POST['confirm_password']);
    }
    $values['image'] = $_FILES['image'];
    $old_image = clear($_POST['old_image']);

    if (empty($values['name'])) {
        $errors['name'] = 'Name is required';
    } elseif (validate_string($values['name'], 3, 255) === false) {
        $errors['name'] = 'Name must be a string and between 3 and 255 characters';
    }

    if (empty($values['username'])) {
        $errors['username'] = 'Username is required';
    } elseif (validate_string(value: $values['username'], min: 3, max: 255, another_preg: ".,-_") === false) {
        $errors['username'] = 'Username must be a string and between 3 and 255 characters';
    }

    if (empty($values['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (validate_email($values['email']) === false) {
        $errors['email'] = 'Email must be a valid email address';
    }

    if (empty($values['phone'])) {
        $errors['phone'] = 'Phone is required';
    } elseif (validate_phone($values['phone']) === false) {
        $errors['phone'] = 'Phone must be a valid phone number';
    }

    if (empty($values['date_of_birth'])) {
        $errors['date_of_birth'] = 'Date of Birth is required';
    } elseif (validate_date($values['date_of_birth']) === false) {
        $errors['date_of_birth'] = 'Date of Birth must be a valid date';
    }

    if (empty($values['gender'])) {
        $errors['gender'] = "Please Select Gender";
    } elseif ($values['gender'] !== 'Male' && $values['gender'] !== 'Female') {
        $errors['gender'] = "Please Select correct gender";
    }

    if (empty($values['address'])) {
        $errors['address'] = 'Address is required';
    } elseif (validate_string(value: $values['address'], min: 3, max: 255, another_preg: ".,-_") === false) {
        $errors['address'] = 'Address must be a string and between 3 and 255 characters';
    }

    if ($action === 'add') {
        if (empty($values['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (validate_string($values['password'], 8, 255) === false) {
            $errors['password'] = 'Password must be a string and between 8 and 255 characters';
        } elseif (validate_password($values['password']) === false) {
            $errors['password'] = 'Password must contain at least one uppercase letter, one digit, one special character and at least 8 characters';
        } elseif (validate_confirm_password($values['password'], $values['confirm_password']) === false) {
            $errors['confirm_password'] = 'Password and Confirm Password must be same';
        }

        if (empty($values['confirm_password'])) {
            $errors['confirm_password'] = 'Confirm Password is required';
        }
    }

    if (!empty($values['image']['name'])) {
        if (checkImageSize($values['image']) === false) {
            $errors['image'] = 'Image size must be less than 10MB';
        } elseif (checkImageType($values['image']) === false) {
            $errors['image'] = 'Image type must be jpg, jpeg, png or gif';
        }
    }

    if (empty(array_filter($errors))) {
        if ($oldImage !== null && empty($old_image)) {
            removeOldImage($oldImage);
        }
        if (!empty($values['image']['name'])) {
            $image_path = uploadImage($values['image']);
        } else {
            $image_path = !empty($old_image) ?  $oldImage : null;
        }
        $data = [
            'name' => $values['name'],
            'username' => $values['username'],
            'email' => $values['email'],
            'phone' => $values['phone'],
            'date_of_birth' => $values['date_of_birth'],
            'gender' => $values['gender'],
            'address' => $values['address'],
            'image_path' => $image_path,
            'is_admin' => 1,
        ];
        if ($action == 'add') {
            $data['password'] = password_hash($values['password'], PASSWORD_DEFAULT);
            query_insert('users', $data);
        } else {
            query_update('users', $data, 'id = ' . data_get($params, 'id'));
        }
        setSession('user-action', [
            'type' => 'success',
            'message' =>
            $action == 'add' ? 'User added successfully' : 'User updated successfully',
        ]);
        header("Location: " . SITE_URL . "/admin/users/index.php");
    }
}

?>
<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="<?= SITE_URL ?>/admin/users/index.php" class="btn btn-primary btn-sm px-3 p-1 rounded-3">
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
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" placeholder="Enter Name"
                                    name="name"
                                    value="<?= $values['name']; ?>">
                            </div>
                            <?= showErrors($errors['name']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" placeholder="Enter Username"
                                    name="username"
                                    value="<?= $values['username']; ?>">
                            </div>
                            <?= showErrors($errors['username']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">Email address</label>
                                <input type="email" class="form-control" placeholder="Enter Email Address"
                                    name="email"
                                    value="<?= $values['email']; ?>">
                            </div>
                            <?= showErrors($errors['email']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" placeholder="Enter Phone Number"
                                    name="phone"
                                    value="<?= $values['phone']; ?>">
                            </div>
                            <?= showErrors($errors['phone']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" placeholder="Enter Date of Birth"
                                    name="date_of_birth"
                                    value="<?= $values['date_of_birth']; ?>">
                            </div>
                            <?= showErrors($errors['date_of_birth']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">Gender</label>
                                <select class="form-select" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?= $values['gender'] === 'Male' ? 'selected' : ''; ?>>Male </option>
                                    <option value="Female" <?= $values['gender'] === 'Female' ? 'selected' : ''; ?>>Female </option>
                                </select>
                            </div>
                            <?= showErrors($errors['gender']); ?>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-1">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" placeholder="Enter Address"
                                    name="address"><?= $values['address']; ?></textarea>
                            </div>
                            <?= showErrors($errors['address']); ?>
                        </div>
                        <div class="col-md-12"
                            x-data="{ image: null, imagePreview: null }"
                            x-init="() => {
                                if ('<?= $oldImage; ?>' !== '') {
                                    imagePreview = '<?= SITE_URL . 'assets/uploads/' . $oldImage; ?>';
                                }
                            }">
                            <div class="mb-1">
                                <label class="form-label">image</label>
                                <input type="file" class="form-control" placeholder="Enter image"
                                    name="image"
                                    accept="image/*"
                                    x-on:change="
                                        image = $event.target.files[0];
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            imagePreview = e.target.result;
                                        };
                                        reader.readAsDataURL(image);
                                    "
                                    x-ref="image">
                                <input type="hidden" x-ref="old_image" name="old_image" value="<?= $oldImage; ?>">
                            </div>
                            <div class="mt-2 position-relative" x-show="imagePreview" x-cloak>
                                <img x-bind:src="imagePreview" class="img-fluid img-thumbnail rounded"
                                    width="100"
                                    height="100">
                                <button type="button" class="btn-close position-absolute top-0 end-0"
                                    aria-label="Close"
                                    x-on:click="
                                        image = null;
                                        imagePreview = null;
                                        $refs.image.value = '';
                                        $refs.old_image.value = '';
                                    "></button>
                            </div>
                            <?= showErrors($errors['image']); ?>
                        </div>
                        <?php if ($action === 'add') : ?>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label class="form-label">Password</label>
                                    <div class="input-group input-group-flat border rounded"
                                        x-data="{ show: false }">
                                        <input type="password" class="form-control border-0" placeholder="Your password" autocomplete="off"
                                            name="password" value="<?= $values['password'] ?>"
                                            x-bind:type="show ? 'text' : 'password'">
                                        <span class="input-group-text border-0">
                                            <a href="#"
                                                x-on:click="show = !show"
                                                class="link-secondary border-0" data-bs-toggle="tooltip" aria-label="Show password" data-bs-original-title="Show password">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                                                </svg>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                <?= showErrors($errors['password']); ?>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label class="form-label">Confirm Password</label>
                                    <div class="input-group input-group-flat border rounded"
                                        x-data="{ show: false }">
                                        <input type="password" class="form-control border-0" placeholder="Your Confirm password" autocomplete="off"
                                            name="confirm_password" value="<?= $values['confirm_password'] ?>"
                                            x-bind:type="show ? 'text' : 'password'">
                                        <span class="input-group-text border-0">
                                            <a href="#"
                                                x-on:click="show = !show"
                                                class="link-secondary border-0" data-bs-toggle="tooltip" aria-label="Show password" data-bs-original-title="Show password">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                                                </svg>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                <?= showErrors($errors['confirm_password']); ?>
                            </div>
                        <?php endif ?>
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
