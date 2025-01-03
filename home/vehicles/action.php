<?php
require_once __DIR__ . '/../../config/front/header.php';
if (empty(getSession('user_id')) && empty($_COOKIE['user_login'])) {
    header("Location: " . SITE_URL . "login.php");
    exit;
}
$params = getUrlParams($_SERVER['REQUEST_URI']);
$errors = [
    'license_plate' => '',
    'make' => '',
    'model' => '',
    'year' => '',
    'vin' => '',
    'color' => '',
    'mileage' => '',
    'image_path' => '',
];
$values = [
    'license_plate' => '',
    'make' => '',
    'model' => '',
    'year' => '',
    'vin' => '',
    'color' => '',
    'mileage' => '',
    'image_path' => '',
];
if (data_get($params, 'action') === null || (data_get($params, 'action') !== 'create' && data_get($params, 'action') !== 'update')) {
    header("Location: " . SITE_URL . "home/vehicles/action.php?action=create");
    exit;
} else {
    $action = data_get($params, 'action', 'create');
}
$oldImage = null;

if (data_get($params, 'action') === 'update' && (data_get($params, 'id') === null || !is_numeric(data_get($params, 'id')))) {
    header("Location: " . SITE_URL . "home/vehicles/action.php?action=create");
    exit;
} elseif (data_get($params, 'action') === 'update' && data_get($params, 'id') !== null) {
    $vehicle = query_select('vehicles', '*', "id = " . data_get($params, 'id'));
    if ($vehicle == null) {
        setSession('vehicle-action', [
            'type' => 'error',
            'message' => 'Vehicle not found',
        ]);
        header("Location: " . SITE_URL . "home/vehicles/index.php");
        exit;
    } else {
        $values['license_plate'] = $vehicle['license_plate'];
        $values['make'] = $vehicle['make'];
        $values['model'] = $vehicle['model'];
        $values['year'] = $vehicle['year'];
        $values['vin'] = $vehicle['vin'];
        $values['color'] = $vehicle['color'];
        $values['mileage'] = $vehicle['mileage'];
        $oldImage = $vehicle['image_path'];
    }
}

$title = data_get($params, 'action') === 'update' ? 'Edit Vehicle' : 'Add Vehicle';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $values['license_plate'] = clear($_POST['license_plate']);
    $values['make'] = clear($_POST['make']);
    $values['model'] = clear($_POST['model']);
    $values['year'] = clear($_POST['year']);
    $values['vin'] = clear($_POST['vin']);
    $values['color'] = clear($_POST['color']);
    $values['mileage'] = clear($_POST['mileage']);
    $values['image_path'] = $_FILES['image_path'];
    $old_image = clear($_POST['old_image']);

    if (empty($values['license_plate'])) {
        $errors['license_plate'] = 'License Plate is required';
    } elseif (validate_string($values['license_plate'], 3, 255) === false) {
        $errors['license_plate'] = 'License Plate must be a string and between 3 and 255 characters';
    } else 

    if (empty($values['make'])) {
        $errors['make'] = 'Make is required';
    } elseif (validate_string($values['make'], 3, 255) === false) {
        $errors['make'] = 'Make must be a string and between 3 and 255 characters';
    }

    if (empty($values['model'])) {
        $errors['model'] = 'Model is required';
    } elseif (validate_string($values['model'], 3, 255) === false) {
        $errors['model'] = 'Model must be a string and between 3 and 255 characters';
    }

    if (empty($values['year'])) {
        $errors['year'] = 'Year is required';
    } elseif (validate_string($values['year'], 4, 4) === false) {
        $errors['year'] = 'Year must be a validate year';
    }

    if (empty($values['vin'])) {
        $errors['vin'] = 'VIN is required';
    } elseif (validate_string(value: $values['vin'], min: 3, max: 255,
        another_preg :' \_\-,.!?') === false) {
        $errors['vin'] = 'VIN must be a string and between 3 and 255 characters';
    }

    if (empty($values['color'])) {
        $errors['color'] = 'Color is required';
    } elseif (validate_string($values['color'], 3, 255) === false) {
        $errors['color'] = 'Color must be a string and between 3 and 255 characters';
    }

    if (empty($values['mileage'])) {
        $errors['mileage'] = 'Mileage is required';
    } elseif (validate_string($values['mileage'], 1, 255) === false) {
        $errors['mileage'] = 'Mileage must be a string and between 1 and 255 characters';
    }


    if (!empty($values['image_path']['name'])) {
        if (checkImageSize($values['image_path']) === false) {
            $errors['image'] = 'Image size must be less than 10MB';
        } elseif (checkImageType($values['image_path']) === false) {
            $errors['image'] = 'Image type must be jpg, jpeg, png or gif';
        }
    }


    if (empty(array_filter($errors))) {
        if ($oldImage !== null && empty($old_image)) {
            removeOldImage($oldImage, __DIR__ . '/../../' . PATH_IMAGE_UPLOAD_VEHICLES);
        }
        if (!empty($values['image_path']['name'])) {
            $image_path = uploadImage($values['image_path'], __DIR__ . '/../../' . PATH_IMAGE_UPLOAD_VEHICLES);
        } else {
            $image_path = !empty($old_image) ?  $oldImage : null;
        }
        $data = [
            'license_plate' => $values['license_plate'],
            'make' => $values['make'],
            'model' => $values['model'],
            'year' => $values['year'],
            'vin' => $values['vin'],
            'color' => $values['color'],
            'mileage' => $values['mileage'],
            'image_path' => $image_path,
            'user_id' => $auth['id'],
        ];
        if ($action == 'create') {
            query_insert('vehicles', $data);
        } else {
            query_update('vehicles', $data, 'id = ' . data_get($params, 'id'));
        }
        setSession('vehicle-action', [
            'type' => 'success',
            'message' =>
            $action == 'create' ? 'Vehicle added successfully' : 'Vehicle updated successfully',
        ]);
        header("Location: " . SITE_URL . "home/vehicles/index.php");
    }
}

?>
<div style="margin-top: 1.5rem;" class="container">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="<?= SITE_URL ?>home/vehicles/index.php" class="btn btn-outline-primary btn-sm px-3 p-1 rounded-2">
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
                action="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?action=' . $action . ($action === 'update' ? '&id=' . data_get($params, 'id') : '')); ?>"
                method="POST"
                enctype="multipart/form-data">
                <div class="card-body">
                    <h3 class="size fs-1 mb-2">
                        <?= $title; ?>
                    </h3>
                    <div class="row row-cards">
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">
                                    License Plate
                                </label>
                                <input type="text" class="form-control" placeholder="Enter License Plate"
                                    name="license_plate"
                                    value="<?= $values['license_plate']; ?>">
                            </div>
                            <?= showErrors($errors['license_plate']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">
                                    Make
                                </label>
                                <input type="text" class="form-control" placeholder="Enter Make"
                                    name="make"
                                    value="<?= $values['make']; ?>">
                            </div>
                            <?= showErrors($errors['make']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">
                                    Model
                                </label>
                                <input type="text" class="form-control" placeholder="Enter Model"
                                    name="model"
                                    value="<?= $values['model']; ?>">
                            </div>
                            <?= showErrors($errors['model']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">
                                    Year
                                </label>
                                <input type="text" class="form-control" placeholder="Enter Year"
                                    name="year"
                                    value="<?= $values['year']; ?>">
                            </div>
                            <?= showErrors($errors['year']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">
                                    VIN
                                </label>
                                <input type="text" class="form-control" placeholder="Enter VIN"
                                    name="vin"
                                    value="<?= $values['vin']; ?>">
                            </div>
                            <?= showErrors($errors['vin']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">
                                    Color
                                </label>
                                <input type="text" class="form-control" placeholder="Enter Color"
                                    name="color"
                                    value="<?= $values['color']; ?>">
                            </div>
                            <?= showErrors($errors['color']); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">
                                    Mileage
                                </label>
                                <input type="text" class="form-control" placeholder="Enter Mileage"
                                    name="mileage"
                                    value="<?= $values['mileage']; ?>">
                            </div>
                            <?= showErrors($errors['mileage']); ?>
                        </div>
                        <div class="col-md-12"
                            x-data="{ image: null, imagePreview: null }"
                            x-init="() => {
                                if ('<?= $oldImage; ?>' !== '') {
                                    imagePreview = '<?= SITE_URL . PATH_IMAGE_UPLOAD_VEHICLES . $oldImage; ?>';
                                }
                            }">
                            <div class="mb-1">
                                <label class="form-label">image</label>
                                <input type="file" class="form-control" placeholder="Enter image"
                                    name="image_path"
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
                                <input type="hidden" name="old_image"
                                    x-ref="old_image"
                                    value="<?= $oldImage; ?>">
                            </div>
                            <div class="mt-2 position-relative" x-show="imagePreview" x-cloak>
                                <img x-bind:src="imagePreview" class="img-fluid img-thumbnail rounded"
                                    width="300"
                                    height="100">
                                <button type="button" class="btn-close position-absolute top-0 end-0"
                                    aria-label="Close"
                                    x-on:click="
                                        image = null;
                                        imagePreview = null;
                                        $refs.image.value = ''
                                        $refs.old_image.value = ''
                                        ;
                                    "></button>
                            </div>
                            <?= showErrors($errors['image_path']); ?>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary"
                            name="submit">
                            <?= $title; ?>
                        </button>
                    </div>
            </form>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . '/../../config/front/footer.php';
