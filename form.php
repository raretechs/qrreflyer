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
<style type="text/css">
    body {
        margin: 0;
        padding: 30px;
        background: #f4f4f4;
        font-family: Arial, Helvetica, sans-serif;
        color: #222;
    }

    .page-wrap {
        max-width: 900px;
        margin: 0 auto;
    }

    .title h1 {
        text-align: center;
        font-variant: small-caps;
        margin-bottom: 20px;
    }

    .instructions {
        background: #ffffff;
        border-left: 6px solid #111;
        padding: 18px 22px;
        margin-bottom: 20px;
        line-height: 1.5;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .instructions h2 {
        margin-top: 0;
        font-size: 20px;
    }

    .instructions ol {
        margin-bottom: 0;
        padding-left: 22px;
    }

    body form {
        background: #ffffff;
        padding: 25px;
        border: 1px solid #ddd;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    body form label {
        display: block;
        font-weight: bold;
        margin-top: 16px;
        margin-bottom: 6px;
    }

    input[type="file"],
    input[type="text"],
    select {
        width: 100%;
        max-width: 500px;
        padding: 8px;
        box-sizing: border-box;
    }

    button {
        margin-top: 22px;
        padding: 10px 18px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
    }
</style>

<div class="page-wrap">
    <div class="title">
        <h1>Castra Realty Property Flyer Maker</h1>
    </div>

    <div class="instructions">
        <h2>How to Create a Property Flyer</h2>
        <ol>
            <li>In CRMLS, search for the property you want to use.</li>
            <li>Click <strong>Export</strong>.</li>
            <li>For <strong>Export file format</strong>, choose <strong>Agent Full + Photos</strong>.</li>
            <li>Save the exported ZIP file to your computer.</li>
            <li>Unzip the file.</li>
            <li>Use this form to select the CSV file, property photos, agent name, and website URL.</li>
            <li>Click <strong>Generate PDF</strong>.</li>
        </ol>
        <p><strong>Note:</strong> You no longer need to process the CSV separately. The flyer generator handles the CRMLS CSV automatically.</p>
    </div>

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
</div>
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
