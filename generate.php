<?php
// Include necessary files
require_once('functions.php');

// Function to replace placeholders in template content with actual data
function replacePlaceholders($templateContent, $csvData, $agentData, $heroPhoto, $additionalPhotos, $qrCodeFile) {
    // Constructing the complete address
    $completeAddress = $csvData['Street Number Numeric'] ?? '';
    $completeAddress .= !empty($csvData['Street Dir Prefix']) ? ' ' . $csvData['Street Dir Prefix'] : '';
    $completeAddress .= !empty($csvData['Street Name']) ? ' ' . $csvData['Street Name'] : '';
    $completeAddress .= !empty($csvData['Street Suffix']) ? ' ' . $csvData['Street Suffix'] : '';
    $completeAddress .= !empty($csvData['Street Dir Suffix']) ? ' ' . $csvData['Street Dir Suffix'] : '';
    $completeAddress .= !empty($csvData['Street Number Extension']) ? ' ' . $csvData['Street Number Extension'] : '';
    $completeAddress .= ', ' . ($csvData['City'] ?? '');
    $completeAddress .= ', ' . ($csvData['State Or Province'] ?? '');
    $completeAddress .= ' ' . ($csvData['Zip Code'] ?? '');

    // Constructing the property details
    $propertyDetails = ($csvData['Bedrooms Total'] ?? '') . ' BD | ';
    $propertyDetails .= ($csvData['Bathrooms Total Integer'] ?? '') . ' BA | ';
    $propertyDetails .= ($csvData['Living Area'] ?? '') . ' SQFT | ';
    $propertyDetails .= ($csvData['Lot Size Square Feet'] ?? '') . ' SQFT LOT';

    // Replace placeholders with actual data and echo the data being used
    echo "Replacing placeholders with actual data:\n";
    echo "Property Address: $completeAddress\n";
    echo "Property Details: $propertyDetails\n";
    echo "Price: " . ($csvData['Current Price'] ?? '') . "\n";
    echo "Hero Image: $heroPhoto\n";
    echo "Property Description: " . ($csvData['Public Remarks'] ?? '') . "\n";
    echo "Agent Data: " . json_encode($agentData) . "\n"; // Convert agent data array to string
    echo "QR Code: $qrCodeFile\n";

    // Replace placeholders with actual data
    $templateContent = str_replace('{Property Address}', $completeAddress, $templateContent);
    $templateContent = str_replace('{Property Details}', $propertyDetails, $templateContent);
    $templateContent = str_replace('{Price}', $csvData['Current Price'] ?? '', $templateContent);
    $templateContent = str_replace('{Hero Image}', $heroPhoto ?? '', $templateContent);
    $templateContent = str_replace('{Property Description}', $csvData['Public Remarks'] ?? '', $templateContent);
    $templateContent = str_replace('{Agent Data}', json_encode($agentData), $templateContent); // Convert agent data array to string
    $templateContent = str_replace('{QR Code}', $qrCodeFile ?? '', $templateContent);

    // Process additional photos
    $galleryImages = '';
    foreach ($additionalPhotos as $photo) {
        $galleryImages .= "<img src='$photo' alt='Gallery Image'>";
    }
    $templateContent = str_replace('{Gallery Images}', $galleryImages, $templateContent);

    return $templateContent;
}

// Function to display template content after placeholders are replaced
function displayTemplateContent($templateContent) {
    echo '<pre>';
    echo 'Template Content with Replaced Data: <br>';
    echo htmlentities($templateContent);
    echo '</pre>';
}

// Handle form submission
function handleFormSubmission() {
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Read form data
        $templateName = $_POST['template'];
        $csvFile = $_FILES['csv']['tmp_name'];
        $heroPhoto = $_FILES['hero_photo']['tmp_name'];
        $additionalPhoto1 = $_FILES['additional_photo_1']['tmp_name'];
        $additionalPhoto2 = $_FILES['additional_photo_2']['tmp_name'];
        $additionalPhoto3 = $_FILES['additional_photo_3']['tmp_name'];
        $additionalPhoto4 = $_FILES['additional_photo_4']['tmp_name'];
        $agentName = $_POST['agent'];
        
        // Check if URL is set
        $url = isset($_POST['url']) ? $_POST['url'] : '';
        
        // Read template file
        $templateContent = readTemplate($templateName);

        if ($templateContent !== false) {
            // Parse CSV data
            $csvData = parseCSVData($csvFile);

            // Construct agent file path based on selected agent
            $agentFilePath = 'agents/' . $agentName . '.txt';

            // Parse agent data
            $agentData = parseAgentData($agentFilePath);

            if ($csvData !== false && $agentData !== false) {
                // Define logo path
                $logo = 'qr-logo.png'; // Replace 'path_to_your_logo.png' with the actual path to your logo image

                // Generate QR code file path
                $qrCodeFile = 'qr_code.png';

                // Activate debug section if needed
                $debug = true; // Change to true to activate debug

                if ($debug) {
                    echo "<pre>";
                    echo "POST data:\n";
                    print_r($_POST);
                    echo "\n";
                    echo "File data:\n";
                    print_r($_FILES);
                    echo "\n";
                    echo "CSV File Path: $csvFile\n"; // Display CSV file path
                    echo "CSV data:\n";
                    print_r($csvData);
                    echo "\n";
                    echo "Agent File Path: $agentFilePath\n"; // Display agent file path
                    echo "Agent data:\n";
                    print_r($agentData);
                    echo "</pre>";
                }

                // Generate QR code locally
                generateQRCode($url, $logo, $qrCodeFile);

                // Replace placeholders in template content with actual data
                $templateContent = replacePlaceholders($templateContent, $csvData, $agentData, $heroPhoto, [$additionalPhoto1, $additionalPhoto2, $additionalPhoto3, $additionalPhoto4], $qrCodeFile);
                
                // Debugging
                var_dump($templateContent); // Output the modified template content

                // Display template content with replaced data
                displayTemplateContent($templateContent);

                // Generate PDF using template content
                $pdfFile = 'output.pdf';
                generatePDF($templateContent, $csvData, $agentData, $heroPhoto, [$additionalPhoto1, $additionalPhoto2, $additionalPhoto3, $additionalPhoto4], $qrCodeFile, $pdfFile);

                // Download or display the generated PDF
                // Redirect to the generated PDF file
                header('Location: ' . $pdfFile);
                exit();
            } else {
                echo "Error: Unable to parse CSV data or agent data.";
            }
        } else {
            echo "Error: Template file not found.";
        }
    } else {
        // Display form
        include('form.php');
    }
}

// Call the function to handle form submission
handleFormSubmission();
?>