<?php
$errors = [
    'name' => '',
    'username' => '',
    'email' => '',
    'phone' => '',
    'date_of_birth' => '',
    'gender' => '',
    'address' => '',
    'current_password' => '',
    'new_password' => '',
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
    'current_password' => '',
    'new_password' => '',
    'confirm_password' => '',
    'image' => '',
];
$oldImage = null;
if (isset($user)) {
    $values['name'] = $user['name'];
    $values['username'] = $user['username'];
    $values['email'] = $user['email'];
    $values['phone'] = $user['phone'];
    $values['date_of_birth'] = $user['date_of_birth'];
    $values['gender'] = $user['gender'];
    $values['address'] = $user['address'];
    $oldImage = $user['image_path'];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $values['name'] = clear($_POST['name']);
    $values['username'] = clear($_POST['username']);
    $values['email'] = clear($_POST['email']);
    $values['phone'] = clear($_POST['phone']);
    $values['date_of_birth'] = clear($_POST['date_of_birth']);
    $values['gender'] = clear($_POST['gender']);
    $values['address'] = clear($_POST['address']);

    if (empty($values['name'])) {
        $errors['name'] = 'Name is required';
    } elseif (validate_string($values['name'], 3, 255) === false) {
        $errors['name'] = 'Name must be a string and between 3 and 255 characters';
    } else {
        $errors['name'] = '';
    }

    if (empty($values['username'])) {
        $errors['username'] = 'Username is required';
    } elseif (validate_string(value: $values['username'], min: 3, max: 255, another_preg: ".,-_") === false) {
        $errors['username'] = 'Username must be a string and between 3 and 255 characters';
    } else {
        $errors['username'] = '';
    }

    if (empty($values['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (validate_email($values['email']) === false) {
        $errors['email'] = 'Email must be a valid email address';
    } else {
        $errors['email'] = '';
    }

    if (empty($values['phone'])) {
        $errors['phone'] = 'Phone is required';
    } elseif (validate_phone($values['phone']) === false) {
        $errors['phone'] = 'Phone must be a valid phone number';
    } else {
        $errors['phone'] = '';
    }

    if (empty($values['date_of_birth'])) {
        $errors['date_of_birth'] = 'Date of Birth is required';
    } elseif (validate_date($values['date_of_birth']) === false) {
        $errors['date_of_birth'] = 'Date of Birth must be a valid date';
    } else {
        $errors['date_of_birth'] = '';
    }

    if (empty($values['gender'])) {
        $errors['gender'] = "Please Select Gender";
    } elseif ($values['gender'] !== 'Male' && $values['gender'] !== 'Female') {
        $errors['gender'] = "Please Select correct gender";
    } else {
        $errors['gender'] = '';
    }

    if (empty($values['address'])) {
        $errors['address'] = 'Address is required';
    } elseif (validate_string(value: $values['address'], min: 3, max: 255, another_preg: ".,-_") === false) {
        $errors['address'] = 'Address must be a string and between 3 and 255 characters';
    } else {
        $errors['address'] = '';
    }

    if (empty(array_filter($errors))) {
        $data = [
            'name' => $values['name'],
            'username' => $values['username'],
            'email' => $values['email'],
            'phone' => $values['phone'],
            'date_of_birth' => $values['date_of_birth'],
            'gender' => $values['gender'],
            'address' => $values['address'],
        ];
        query_update('users', $data, 'id = ' . $user['id']);
        setSession('profile-action', [
            'type' => 'success',
            'message' => 'Profile updated successfully',
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $values['current_password'] = clear($_POST['current_password']);
    $values['new_password'] = clear($_POST['new_password']);
    $values['confirm_password'] = clear($_POST['confirm_password']);

    if (empty($values['current_password'])) {
        $errors['current_password'] = 'Current Password is required';
    } elseif (validate_string($values['current_password'], 6, 255) === false) {
        $errors['current_password'] = 'Current Password must be a string and between 6 and 255 characters';
    } else {
        $errors['current_password'] = '';
    }

    if (empty($values['new_password'])) {
        $errors['new_password'] = 'New Password is required';
    } elseif (validate_string($values['new_password'], 6, 255) === false) {
        $errors['new_password'] = 'New Password must be a string and between 6 and 255 characters';
    } else {
        $errors['new_password'] = '';
    }

    if (empty($values['confirm_password'])) {
        $errors['confirm_password'] = 'Confirm Password is required';
    } elseif ($values['new_password'] !== $values['confirm_password']) {
        $errors['confirm_password'] = 'Confirm Password must be same as New Password';
    } else {
        $errors['confirm_password'] = '';
    }

    if (empty(array_filter($errors))) {
        $user = query_select('users', '*', 'id = ' . $user['id']);
        if (password_verify($values['current_password'], $user['password'])) {
            $data = [
                'password' => password_hash($values['new_password'], PASSWORD_DEFAULT),
            ];
            query_update('users', $data, 'id = ' . $user['id']);
            setSession('profile-action', [
                'type' => 'success',
                'message' => 'Password updated successfully',
            ]);
        } else {
            $errors['current_password'] = 'Current Password is incorrect';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete-image') {
    $imagePath = $user['image_path'];
    if ($imagePath) {
        removeOldImage($imagePath);
        query_update('users', ['image_path' => null], 'id = ' . $user['id']);
        $user['image_path'] = '';
        setSession('profile-action', [
            'type' => 'success',
            'message' => 'Image deleted successfully',
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_image'])) {
    $values['image'] = $_FILES['image'];
    if (!empty($values['image']['name'])) {
        if (checkImageSize($values['image']) === false) {
            $errors['image'] = 'Image size must be less than 10MB';
        } elseif (checkImageType($values['image']) === false) {
            $errors['image'] = 'Image type must be jpg, jpeg, png or gif';
        } else {
            $errors['image'] = '';
        }
    }

    if (empty(array_filter($errors))) {
        if (!empty($values['image']['name'])) {
            if ($oldImage !== null) {
                removeOldImage($oldImage);
            }
            $image_path = uploadImage($values['image']);
        } else {
            $image_path = $oldImage;
        }

        query_update('users', ['image_path' => $image_path], 'id = ' . $user['id']);
        $user['image_path'] = $image_path;
        setSession('profile-action', [
            'type' => 'success',
            'message' => 'Image uploaded successfully',
        ]);
        header('Location: ' . htmlspecialchars($_SERVER['PHP_SELF']));
    }
}
?>
<div class="row my-0 my-md-4">
    <div class="col-12">
        <?= showSessionMessage('profile-action') ?>
        <div class="card">
            <div class="card-header flex-column align-items-start gy-2">
                <h2 class="d-flex flex-column">
                    <span class="text-capitalize ">
                        <?= $user['name']; ?>
                    </span>
                    <small class="text-muted fs-4">
                        <?= $user['email']; ?>
                    </small>
                </h2>
                <h3 class="mb-0">Profile Details</h3>
                <p class="mb-0">You have full control to manage your own account setting.</p>
            </div>
            <div class="card-body">
                <div class="d-lg-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center mb-4 mb-lg-0">
                        <img src="<?= getImagePath($user['image_path']) ?>" id="img-uploaded" class="avatar rounded-circle object-cover" alt="avatar">
                        <div class="ms-3">
                            <h4 class="mb-0">Your avatar</h4>
                            <p class="mb-0">jpg, jpeg, png, gif and not bigger than 10MB.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1" x-data>
                        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                            method="post"
                            enctype="multipart/form-data"
                            x-ref="form">
                            <input type="file" id="file-upload" class="d-none" name="image" accept="image/*"
                                x-on:change="$refs.submitButton.click()">
                            <button
                                class=" d-none"
                                x-ref="submitButton"
                                name="update_image">
                                upload image
                            </button>
                            <button type="button" class="btn btn-primary btn-sm rounded-2"
                                x-on:click="document.getElementById('file-upload').click();">Upload</button>
                        </form>
                        <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>?action=delete-image" class="btn btn-outline-danger btn-sm rounded-2">Delete</a>
                    </div>
                </div>
                <hr class="my-1 mt-3">
                <div class="mt-2">
                    <h3 class="mb-0">Personal Details</h3>
                    <p class="mb-2 text-muted">Edit your personal information and address.</p>
                    <form class="row gx-2 gy-2 needs-validation"
                        novalidate=""
                        action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                        method="POST">
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
                        <div class="col-12 mt-2">
                            <button class="btn btn-primary" type="submit" name="update_profile">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <h3 class="mb-0">Change Password</h3>
                    <p class="mb-0 text-muted">Change your password to keep your account secure.</p>
                </div>
                <div>
                    <form class="row gx-2 gy-2 needs-validation"
                        novalidate=""
                        action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                        method="POST">
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">Current Password</label>
                                <div class="input-group input-group-flat border rounded"
                                    x-data="{ show: false }">
                                    <input type="password" class="form-control border-0" placeholder="Your password" autocomplete="off"
                                        name="current_password" value="<?= $values['current_password'] ?>"
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
                            <?= showErrors($errors['current_password']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">New Password</label>
                                <div class="input-group input-group-flat border rounded"
                                    x-data="{ show: false }">
                                    <input type="password" class="form-control border-0" placeholder="New password" autocomplete="off"
                                        name="new_password" value="<?= $values['new_password'] ?>"
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
                            <?= showErrors($errors['new_password']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">Confirm Password</label>
                                <div class="input-group input-group-flat border rounded"
                                    x-data="{ show: false }">
                                    <input type="password" class="form-control border-0" placeholder="Confirm password" autocomplete="off"
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
                        <div class="col-12 mt-2">
                            <button class="btn btn-primary" type="submit"
                                name="update_password">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>