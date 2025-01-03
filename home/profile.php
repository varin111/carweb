<?php
require_once __DIR__ . '/../config/front/header.php';
$user = auth();
?>
<div style="margin-top: 8.5rem;" class="container">
    <?php require_once __DIR__ . '/../config/user_profile.php'; ?>
</div>

<?php
require_once __DIR__ . '/../config/front/footer.php';
