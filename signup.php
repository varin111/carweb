<?php
ob_start();
require_once __DIR__ . '/config/front/header.php';
if (!empty(getSession('user_id') || !empty($_COOKIE['user_login']))) {
    if (auth()['is_admin'] === '1') {
        header("Location: " . SITE_URL . "/admin/index.php");
    } else {
        header("Location: " . SITE_URL . "/index.php");
    }
}

$errors = [
    'name' => '',
    'username' => '',
    'email' => '',
    'phone' => '',
    'date_of_birth' => '',
    'gender' => '',
    'address' => '',
    'national_card_image' => '',
    'password' => '',
    'confirm_password' => '',
];
$values = [
    'name' => '',
    'username' => '',
    'email' => '',
    'phone' => '',
    'date_of_birth' => '',
    'gender' => '',
    'address' => '',
    'national_card_image' => '',
    'password' => '',
    'confirm_password' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $values['name'] = clear($_POST['name']);
    $values['username'] = clear($_POST['username']);
    $values['email'] = clear($_POST['email']);
    $values['phone'] = clear($_POST['phone']);
    $values['date_of_birth'] = clear($_POST['date_of_birth']);
    $values['gender'] = clear($_POST['gender']);
    $values['address'] = clear($_POST['address']);
    $values['national_card_image'] = $_FILES['national_card_image'];
    $values['password'] = clear($_POST['password']);
    $values['confirm_password'] = clear($_POST['confirm_password']);

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
    } elseif (validate_string($values['address'], 3, 255) === false) {
        $errors['address'] = 'Address must be a string and between 3 and 255 characters';
    } else {
        $errors['address'] = '';
    }

    if (empty($values['national_card_image']['name'])) {
        $errors['national_card_image'] = 'National Card Image is required';
    } elseif (checkImageSize($values['national_card_image']) === false && !empty($values['national_card_image']['name'])) {
        $errors['national_card_image'] = 'National Card Image size must be less than 10MB';
    } elseif (checkImageType($values['national_card_image']) === false && !empty($values['national_card_image']['name'])) {
        $errors['national_card_image'] = 'National Card Image type must be jpg, jpeg, png or gif';
    } else {
        $errors['national_card_image'] = '';
    }

    if (empty($values['password'])) {
        $errors['password'] = 'Password is required';
    } elseif (validate_string($values['password'], 8, 255) === false) {
        $errors['password'] = 'Password must be a string and between 8 and 255 characters';
    } elseif (validate_password($values['password']) === false) {
        $errors['password'] = 'Password must contain at least one uppercase letter, one digit, one special character and at least 8 characters';
    } elseif (validate_confirm_password($values['password'], $values['confirm_password']) === false) {
        $errors['confirm_password'] = 'Password and Confirm Password must be same';
    } else {
        $errors['password'] = '';
    }

    if (empty($values['confirm_password'])) {
        $errors['confirm_password'] = 'Confirm Password is required';
    }


    if (empty(array_filter($errors))) {
        if (!empty($values['national_card_image']['name'])) {
            $national_card_image = uploadImage($values['national_card_image']);
        } else {
            $national_card_image = null;
        }
        // create new user
        $data = [
            'name' => $values['name'],
            'username' => $values['username'],
            'email' => $values['email'],
            'phone' => $values['phone'],
            'date_of_birth' => $values['date_of_birth'],
            'gender' => $values['gender'],
            'address' => $values['address'],
            'national_card_image' => $national_card_image,
            'password' => password_hash($values['password'], PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'is_admin' => 0,
        ];
        query_insert('users', $data);

        setSession(
            'account-created-successfully',
            [
                'type' => 'success',
                'message' => 'Account created successfully. Please login to continue',
            ]
        );
        header("Location: " . SITE_URL . "/login.php");
    }
}

?>
<div class="page page-center d-flex align-items-center">
    <div class="container-narrow">
        <div class="card">
            <div class="text-center mt-4">
                <a href="<?= SITE_URL ?>" class="navbar-brand">
                    <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Tabler"
                        width="60" height="60">
                    <h2 class="fs-1">
                        <?= SITE_NAME ?>
                    </h2>
                </a>
            </div>
            <div class="card-body py-4">
                <h2 class="fs-1 text-center mb-4">
                    Create an account to get started
                </h2>
                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                    <div class="row row-cards">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" placeholder="Your full name"
                                name="name" value="<?= $values['name'] ?>">
                            <?= showErrors($errors['name']); ?>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" placeholder="Your username"
                                name="username" value="<?= $values['username'] ?>">
                            <?= showErrors($errors['username']); ?>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" placeholder="Your phone number"
                                name="phone" value="<?= $values['phone'] ?>">
                            <?= showErrors($errors['phone']); ?>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Email address</label>
                            <input type="email" class="form-control" placeholder="your@email.com"
                                name="email" value="<?= $values['email'] ?>">
                            <?= showErrors($errors['email']); ?>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" placeholder="Your date of birth"
                                name="date_of_birth" value="<?= $values['date_of_birth'] ?>">
                            <?= showErrors($errors['date_of_birth']); ?>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="gender">
                                <option value="">Select Gender</option>
                                <option value="Male" <?= $values['gender'] === 'Male' ? 'selected' : ''; ?>>Male </option>
                                <option value="Female" <?= $values['gender'] === 'Female' ? 'selected' : ''; ?>>Female </option>
                            </select>
                            <?= showErrors($errors['gender']); ?>
                        </div>
                        <div class="mb-3 col-12">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" placeholder="Your address"
                                name="address" value="<?= $values['address'] ?>">
                            <?= showErrors($errors['address']); ?>
                        </div>
                        <div class="col-md-12"
                            x-data="{ image: null, imagePreview: null }">
                            <div class="mb-1">
                                <label class="form-label">
                                    National Card Image
                                </label>
                                <input type="file" class="form-control" placeholder="Enter your national card image"
                                    name="national_card_image"
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
                            </div>
                            <div class="mt-2 position-relative" x-show="imagePreview" x-cloak>
                                <img x-bind:src="imagePreview" class="img-fluid img-thumbnail rounded"
                                    width="300"
                                    height="400">
                                <button type="button" class="btn-close position-absolute top-0 end-0"
                                    aria-label="Close"
                                    x-on:click="
                                        image = null;
                                        imagePreview = null;
                                        $refs.image.value = '';
                                        $refs.old_image.value = '';
                                    "></button>
                            </div>
                            <?= showErrors($errors['national_card_image']); ?>
                        </div>
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
                        <div class="form-footer">
                            <button type="submit" name="signup" class="btn btn-primary w-100">
                                Sign Up
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center text-secondary my-3">
            Already have an account?<a href="<?= SITE_URL . 'login.php' ?>" tabindex="-1">
                Login
            </a>
        </div>
    </div>
</div>
<?php
require_once  __DIR__ . '/config/front/footer.php';
ob_end_flush();
