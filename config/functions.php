<?php
session_start();
ob_start();

function showErrors(string $message): string
{
    return isset($message)
        && $message !== ""
        ? "<span class='text-danger d-block fs-5 fw-bold'>$message</span>"
        : "";
}

function clear(string $value): string
{
    if (empty($value) || !isset($value)) {
        return "";
    }
    $value = trim($value); // Remove white spaces
    $value = stripslashes($value); // Remove slashes
    $value = htmlspecialchars($value); // Convert special characters to HTML entities
    return $value;
}

function clearArray(array $values): array
{
    return  is_array($values) ? array_map('clear', $values) : [];
}

function validate_string(
    $value,
    $min = 2,
    $max = 50,
    $allowed_numbers = true,
    $another_preg = ' ,.!?'
): bool {
    $preg = "/^[a-zA-Z {$another_preg}]{{$min},{$max}}$/";
    if ($allowed_numbers) {
        $preg = "/^[a-zA-Z {$another_preg} 0-9]{{$min},{$max}}$/";
    }
    return preg_match($preg, $value);
}

function validate_email($field)
{
    return filter_var($field, FILTER_VALIDATE_EMAIL); // Return true
}

function validate_phone($field, $length = 15)
{
    return preg_match("/^[0-9]{{$length}}$/", $field);
}

function validate_password($value): bool|int
{
    // accept at least one lowercase letter (a-z)
    // at least one uppercase letter (A-Z)
    // at least one digit (0-9)
    // at least one special character (@$!%*?&)
    // at least 8 characters
    return preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $value);
}

function validate_confirm_password($password, $confirm_password)
{
    return $password === $confirm_password;
}

// validate_date
function validate_date($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function setSession($key, $values)
{
    $_SESSION[$key] = $values;
}

function getSession($key)
{
    return isset($_SESSION[$key]) ? $_SESSION[$key] : '';
}

function isActivePages(...$pages)
{
    $currentPage = trim($_SERVER['SCRIPT_NAME'], '/');
    return in_array($currentPage, (array)$pages);
}

function getUrlParams(string $url): array
{
    $urlParts = parse_url($url);

    if (!isset($urlParts['query'])) {
        return [];
    }

    parse_str($urlParts['query'], $params);
    return $params;
}

function data_get($data, $key, $default = null)
{
    return $data[$key] ?? $default;
}

// 10MB
function checkImageSize($file, $size = 10000000)
{
    return $file['size'] < $size; // 10MB
}

function checkImageType($file, $allowed = ['jpg', 'jpeg', 'png', 'gif'])
{
    $file_name = $file['name'];
    $file_ext = explode('.', $file_name);
    $file_ext = strtolower(end($file_ext));
    return in_array($file_ext, $allowed);
}

function uploadImage($file, $path = __DIR__ . '/../' . PATH_IMAGE_UPLOAD): string
{
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];

    $file_ext = explode('.', $file_name);
    $file_actual_ext = strtolower(end($file_ext)); // Get the last element of the array like jpg, png, gif

    $file_name_new = uniqid('', true) . "." . $file_actual_ext;
    $file_destination = $path . $file_name_new;
    move_uploaded_file($file_tmp, $file_destination);
    return $file_name_new;
}

function removeOldImage($image, $path = __DIR__ . '/../' . PATH_IMAGE_UPLOAD)
{
    if ($image) {
        $path = $path . $image;
        if (file_exists($path)) {
            unlink($path);
        }
    }
}

function showSessionMessage($key, $type = 'success')
{
    if (!isset($_SESSION[$key])) {
        return;
    }
    $type = data_get($_SESSION[$key], 'type', $type);
    if (isset($_SESSION[$key]['message'])) {
        echo "
        <div class='alert alert-{$type} alert-dismissible'>
                <div class='d-flex'>
                {$_SESSION[$key]['message']}
                </div>
            <a class='btn-close' data-bs-dismiss='alert' aria-label='close'></a>
        </div>
        
        ";
    }
    unset($_SESSION[$key]);
}

function getImagePath($image, $path = PATH_IMAGE_UPLOAD)
{
    return SITE_URL . ($image ? $path . $image : 'assets/images/not_available_image.png');
}


function getCssLinks(): void
{
    echo ' 
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.semanticui.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/fontawesome.min.css" integrity="sha512-v8QQ0YQ3H4K6Ic3PJkym91KoeNT5S3PnDKvqnwqFD1oiqIl653crGZplPdU5KKtHjO0QKcQ2aUlQZYjHczkmGw==" crossorigin="anonymous" referrerpolicy="no-referrer" />    <link href="' . SITE_URL . 'assets/css/tabler-flags.min.css" rel="stylesheet" />
    <link href="' . SITE_URL . 'assets/css/tabler.min.css" rel="stylesheet" />
    <link href="' . SITE_URL . 'assets/css/tabler-flags.min.css" rel="stylesheet" />
    <link href="' . SITE_URL . 'assets/css/tabler-payments.min.css" rel="stylesheet" />
    <link href="' . SITE_URL . 'assets/css/tabler-vendors.min.css" rel="stylesheet" />
    <link href="' . SITE_URL . 'assets/css/demo.min.css" rel="stylesheet" />
    <link href="' . SITE_URL . 'assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="' . SITE_URL . 'assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="' . SITE_URL . 'assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="' . SITE_URL . 'assets/css/main.css" rel="stylesheet" />
    ';
}

function getJsLinks(): void
{
    echo '
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script defer src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script defer src="https://cdn.datatables.net/2.1.8/js/dataTables.semanticui.js"></script>
    <script defer src="' . SITE_URL . 'assets/js/tabler.min.js"></script>
    <script defer src="' . SITE_URL . 'assets/js/demo.min.js"></script>
    <script defer src="' . SITE_URL . 'assets/js/list.min.js"></script>
    <script defer src="' . SITE_URL . 'assets/vendor/aos/aos.js"></script>
    <script defer src="' . SITE_URL . 'assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script defer src="' . SITE_URL . 'assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script defer src="' . SITE_URL . 'assets/vendor/waypoints/noframework.waypoints.js"></script>
    <script defer src="' . SITE_URL . 'assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script defer src="' . SITE_URL . 'assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script defer src="' . SITE_URL . 'assets/js/main.js"></script>
    ';
}

function format_date($date, $format = 'Y-m-d h:i:s A')
{
    return date($format, strtotime($date));
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

function sendMail($data)
{
    // Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'kurdcarinsurance@gmail.com';                     //SMTP username
        $mail->Password   = 'udob gjqm uuqq jvun';                               //SMTP password
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($data['email'], $data['name']);               //Name is optional
        $mail->addAddress('kurdcarinsurance@gmail.com', 'Kurd Car Insurance');
        // $mail->addAddress('ellen@example.com');               //Name is optional
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $data['subject'];
        $mail->Body    = "<h1>Name : {$data['name']}</h1><p>{$data['message']}</p>";
        $mail->AltBody =  "{$data['name']} {$data['message']}";

        $mail->send();
    } catch (Exception $e) {
    }
}


// random_string
function random_string($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// format_currency
function format_currency($value)
{
    return number_format($value ?: 0, 2);
}

