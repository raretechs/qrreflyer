<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
require_once('functions.php');
require_once('form.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>PDF Generator</title>
</head>
<body>
    <?php if (!empty($missingDirectories) || !empty($missingFiles)) : ?>
        <p style="color: red; font-weight: bold;">Warning: Required directories or files are missing!</p>
        <p>Please contact the administrator.</p>
    <?php else: ?>
        <!-- Display the form -->
        <?php include_once('form.php'); ?>
    <?php endif; ?>
</body>
</html>
