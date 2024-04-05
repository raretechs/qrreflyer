<?php
// Check if required directories exist
$requiredDirectories = ['agents', 'templates', 'tcpdf', 'phpqrcode'];
$missingDirectories = [];
foreach ($requiredDirectories as $dir) {
    if (!is_dir($dir)) {
        $missingDirectories[] = $dir;
    }
}

// Check if required files exist
$requiredFiles = ['qr-logo.png', 'functions.php', 'index.php'];
$missingFiles = [];
foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        $missingFiles[] = $file;
    }
}

// Disable form submission if any required elements are missing
$disableSubmit = !empty($missingDirectories) || !empty($missingFiles);
?>

<form action="generate.php" method="post" enctype="multipart/form-data">
    <label for="template">Select Template:</label>
    <select name="template" id="template">
        <?php
        $templates = getHTMLTemplateList();
        foreach ($templates as $template) {
            echo "<option value='$template'>$template</option>";
        }
        ?>
    </select><br>
    <label for="csv">CSV File:</label>
    <input type="file" name="csv" accept=".csv"><br>
    <label for="hero_photo">Hero Photo:</label>
    <input type="file" name="hero_photo" accept="image/*"><br>
<!-- Additional photo inputs -->
    <label for="additional_photo_1">Additional Photo 1:</label>
    <input type="file" name="additional_photo_1" accept="image/*"><br>
    
    <label for="additional_photo_2">Additional Photo 2:</label>
    <input type="file" name="additional_photo_2" accept="image/*"><br>
    
    <label for="additional_photo_3">Additional Photo 3:</label>
    <input type="file" name="additional_photo_3" accept="image/*"><br>
    
    <label for="additional_photo_4">Additional Photo 4:</label>
    <input type="file" name="additional_photo_4" accept="image/*"><br>

    <!-- End of additional photo inputs -->
    <label for="agent">Select Agent:</label>
    <select name="agent" id="agent">
        <?php
        $agents = getAgentList();
        foreach ($agents as $agent) {
            echo "<option value='$agent'>$agent</option>";
        }
        ?>
    </select><br>
    <!-- Add the URL input field here -->
    <label for="url">URL:</label>
    <input type="text" name="url" id="url"><br>
    <!-- End of URL input field -->
    <button type="submit" <?php echo $disableSubmit ? 'disabled' : ''; ?>>Generate PDF</button>
</form>

<?php
// Function to get list of HTML templates
function getHTMLTemplateList() {
    $templates = [];
    $templateDirectory = 'templates';
    if (is_dir($templateDirectory)) {
        $templateFiles = scandir($templateDirectory);
        foreach ($templateFiles as $file) {
            // Check if the file is an HTML file
            if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'html') {
                $templates[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }
    }
    return $templates;
}
?>
