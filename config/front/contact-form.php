<?php

require_once __DIR__ . '/../connection.php';
$errors = [
    'name' => '',
    'email' => '',
    'subject' => '',
    'message' => '',
];
$values = [
    'name' => '',
    'email' => '',
    'subject' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $values = [
        'name' => clear($_POST['name']),
        'email' => clear($_POST['email']),
        'subject' => clear($_POST['subject']),
        'message' => clear($_POST['message']),
    ];
    if (empty($values['name'])) {
        $errors['name'] = 'Name is required';
    }
    if (empty($values['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (!validate_email($values['email'])) {
        $errors['email'] = 'Invalid email format';
    }
    if (empty($values['subject'])) {
        $errors['subject'] = 'Subject is required';
    }

    if (empty($values['message'])) {
        $errors['message'] = 'Message is required';
    }elseif(validate_string($values['message'], 10, 500) === false){
        $errors['message'] = 'Message must be between 10 and 500 characters';
    }

    if (empty(array_filter($errors))) {
        sendMail([
            'email' => $values['email'],
            'name' => $values['name'],
            'subject' => $values['subject'],
            'message' => $values['message'],
        ]);
        $values = [
            'name' => '',
            'email' => '',
            'subject' => '',
            'message' => '',
        ];
        setSession('contact-message-success', [
            'type' => 'success',
            'message' => 'Message sent successfully',
        ]);
    }
}


?>
<div>
    <?= showSessionMessage('contact-message-success') ?>
</div>
<form
    action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>#contact"
    class="php-email-form"
    method="post" data-aos="fade-up" data-aos-delay="200">
    <div class="row gy-2">
        <div class="col-md-6">
            <label for="name-field" class="pb-2">Your Name</label>
            <input type="text" name="name" id="name-field" class="form-control mb-1"
                value="<?= $values['name'] ?>">
            <?= showErrors($errors['name']) ?>
        </div>
        <div class="col-md-6">
            <label for="email-field" class="pb-2">Your Email</label>
            <input type="email" class="form-control mb-1" name="email" id="email-field"
                value="<?= $values['email'] ?>">
            <?= showErrors($errors['email']) ?>
        </div>
        <div class="col-md-12">
            <label for="subject-field" class="pb-2">Subject</label>
            <input type="text" class="form-control mb-1" name="subject" id="subject-field"
                value="<?= $values['subject'] ?>">
            <?= showErrors($errors['subject']) ?>
        </div>
        <div class="col-md-12">
            <label for="message-field" class="pb-2">Message</label>
            <textarea class="form-control mb-1" name="message" rows="10" id="message-field"><?= $values['message'] ?></textarea>
            <?= showErrors($errors['message']) ?>
        </div>
        <div class="col-md-12 mt-3">
            <button type="submit"
                class="btn btn-primary" name="send_message">Send Message</button>
        </div>
    </div>
</form>