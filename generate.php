<?php
// Include necessary files
require_once('functions.php');

// Start output buffering
ob_start();

// Function to replace placeholders in template content with actual data
function replacePlaceholders($templateContent, $csvData, $agentData, $heroPhoto, $additionalPhotos, $qrCodeFile, $agentName) {
    // Loop through each row of CSV data
    foreach ($csvData as $rowData) {
        // Constructing the complete address
        $completeAddress = isset($rowData['Street Number Numeric']) ? $rowData['Street Number Numeric'] : '';
        $completeAddress .= !empty($rowData['Street Dir Prefix']) ? ' ' . $rowData['Street Dir Prefix'] : '';
        $completeAddress .= !empty($rowData['Street Name']) ? ' ' . $rowData['Street Name'] : '';
        $completeAddress .= !empty($rowData['Street Suffix']) ? ' ' . $rowData['Street Suffix'] : '';
        $completeAddress .= !empty($rowData['Street Dir Suffix']) ? ' ' . $rowData['Street Dir Suffix'] : '';
        $completeAddress .= !empty($rowData['Street Number Extension']) ? ' ' . $rowData['Street Number Extension'] : '';
        $completeAddress .= ', ' . (isset($rowData['City']) ? $rowData['City'] : '');
        $completeAddress .= ', ' . (isset($rowData['State Or Province']) ? $rowData['State Or Province'] : '');
        $completeAddress .= ' ' . (isset($rowData['Zip Code']) ? $rowData['Zip Code'] : '');

        // Constructing the property details
        $propertyDetails = ($rowData['Bedrooms Total'] ?? '') . ' BD | ';
        $propertyDetails .= ($rowData['Bathrooms Total Integer'] ?? '') . ' BA | ';
        $propertyDetails .= ($rowData['Living Area'] ?? '') . ' SQFT | ';
        $propertyDetails .= ($rowData['Lot Size Square Feet'] ?? '') . ' SQFT LOT';

        // Replace placeholders with actual CSV data
        $templateContent = str_replace('{Property Address}', htmlentities($completeAddress, ENT_QUOTES, 'utf-8'), $templateContent);
        $templateContent = str_replace('{Property Details}', htmlentities($propertyDetails, ENT_QUOTES, 'UTF-8'), $templateContent);
        $templateContent = str_replace('{Current Price}', htmlentities($rowData['Current Price'] ?? '', ENT_QUOTES, 'UTF-8'), $templateContent);
        $templateContent = str_replace('{Property Description}', htmlentities($rowData['Public Remarks'] ?? '', ENT_QUOTES, 'UTF-8'), $templateContent);
    }

    // Construct relative paths for the images
    $heroPhotoRelativePath = 'tmp/' . basename($heroPhoto);
    $additionalPhotosRelativePaths = [];
    foreach ($additionalPhotos as $photo) {
        $additionalPhotosRelativePaths[] = 'tmp/' . basename($photo);
    }

    // Replace placeholders with actual images
    $templateContent = str_replace('{Hero Image}', htmlentities($heroPhotoRelativePath, ENT_QUOTES, 'UTF-8'), $templateContent);
	    
	// eplace placeholders with actual gallery images
    $galleryImages = '';
    foreach ($additionalPhotosRelativePaths as $photo) {
        $galleryImages .= "<img src='$photo' alt='Gallery Image'>";
    }

	    // Replace placeholders with actual agent info
    $templateContent = str_replace('{Agent Photo}', htmlentities($agentData['Agent Photo'] ?? getDefaultAgentData($agentName)['Agent Photo'], ENT_QUOTES, 'UTF-8'), $templateContent);
    $templateContent = str_replace('{Agent Name}', htmlentities($agentData['agentname'] ?? getDefaultAgentData($agentName)['Agent Name'], ENT_QUOTES, 'UTF-8'), $templateContent);
    $templateContent = str_replace('{Agent Title}', htmlentities($agentData['agenttitle'] ?? getDefaultAgentData($agentName)['Agent Title'], ENT_QUOTES, 'UTF-8'), $templateContent);
    $templateContent = str_replace('{Agent License Number}', htmlentities($agentData['agentdrenumber'] ?? getDefaultAgentData($agentName)['Agent License Number'], ENT_QUOTES, 'UTF-8'), $templateContent);
    $templateContent = str_replace('{Agent Mobile}', htmlentities($agentData['agentphonenumber'] ?? getDefaultAgentData($agentName)['Agent Mobile'], ENT_QUOTES, 'UTF-8'), $templateContent);
    $templateContent = str_replace('{Agent Office}', htmlentities($agentData['agentofficenumber'] ?? getDefaultAgentData($agentName)['Agent Office'], ENT_QUOTES, 'UTF-8'), $templateContent);
    $templateContent = str_replace('{Agent Email}', htmlentities($agentData['agentemail'] ?? getDefaultAgentData($agentName)['Agent Email'], ENT_QUOTES, 'UTF-8'), $templateContent);
    $templateContent = str_replace('{Agent Website}', htmlentities($agentData['agentwebsite'] ?? getDefaultAgentData($agentName)['Agent Website'], ENT_QUOTES, 'UTF-8'), $templateContent);
    $templateContent = str_replace('{QR Code}', htmlentities($qrCodeFile ?? '', ENT_QUOTES, 'UTF-8'), $templateContent);

    $templateContent = str_replace('{Gallery Images}', $galleryImages, $templateContent);

    return $templateContent;
}

// Function to display template content after placeholders are replaced
function displayTemplateContent($templateContent) {
    //echo '<pre>';
    //echo 'Template Content with Replaced Data: <br>';
    //echo htmlentities($templateContent);
    echo $templateContent;
    //echo '</pre>';
}

// Handle form submission
function handleFormSubmission() {
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Directory where uploaded files will be stored within your project directory
        $uploadDirectory = __DIR__ . '/tmp';

        // Generate unique identifiers for filenames
        $uniqueId = uniqid();

        // Move uploaded files to the upload directory with unique filenames
        move_uploaded_file($_FILES['csv']['tmp_name'], $uploadDirectory . '/' . $uniqueId . '_' . $_FILES['csv']['name']);
        move_uploaded_file($_FILES['hero_photo']['tmp_name'], $uploadDirectory . '/' . $uniqueId . '_' . $_FILES['hero_photo']['name']);
        move_uploaded_file($_FILES['additional_photo_1']['tmp_name'], $uploadDirectory . '/' . $uniqueId . '_' . $_FILES['additional_photo_1']['name']);
        move_uploaded_file($_FILES['additional_photo_2']['tmp_name'], $uploadDirectory . '/' . $uniqueId . '_' . $_FILES['additional_photo_2']['name']);
        move_uploaded_file($_FILES['additional_photo_3']['tmp_name'], $uploadDirectory . '/' . $uniqueId . '_' . $_FILES['additional_photo_3']['name']);
        move_uploaded_file($_FILES['additional_photo_4']['tmp_name'], $uploadDirectory . '/' . $uniqueId . '_' . $_FILES['additional_photo_4']['name']);

        // Read form data
        $templateName = $_POST['template'];
        $csvFile = $uploadDirectory . '/' . $uniqueId . '_' . $_FILES['csv']['name'];
        $heroPhoto = $uploadDirectory . '/' . $uniqueId . '_' . $_FILES['hero_photo']['name'];
        $additionalPhoto1 = $uploadDirectory . '/' . $uniqueId . '_' . $_FILES['additional_photo_1']['name'];
        $additionalPhoto2 = $uploadDirectory . '/' . $uniqueId . '_' . $_FILES['additional_photo_2']['name'];
        $additionalPhoto3 = $uploadDirectory . '/' . $uniqueId . '_' . $_FILES['additional_photo_3']['name'];
        $additionalPhoto4 = $uploadDirectory . '/' . $uniqueId . '_' . $_FILES['additional_photo_4']['name'];
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
				$qrCodeFileName = 'qr_code.png';
                $qrCodeFile = './tmp/' . $uniqueId . '_' . $qrCodeFileName;

                // Generate QR code locally
                generateQRCode($url, $logo, './tmp/' . $uniqueId . '_' . $qrCodeFileName);

                // Replace placeholders in template content with actual data
                $templateContent = replacePlaceholders($templateContent, $csvData, $agentData, $heroPhoto, [$additionalPhoto1, $additionalPhoto2, $additionalPhoto3, $additionalPhoto4], $qrCodeFile, $agentName);

                // Display template content with replaced data
                displayTemplateContent($templateContent);
                
                // Commenting out the redirection to PDF for viewing the HTML content
                // Generate PDF using template content
                // $pdfFile = 'output.pdf';
                // generatePDF($templateContent, $csvData, $agentData, $heroPhoto, [$additionalPhoto1, $additionalPhoto2, $additionalPhoto3, $additionalPhoto4], $qrCodeFile, $pdfFile);

                // Redirect to the generated PDF file
                // header('Location: ' . $pdfFile);
                // exit();
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

// Flush output buffer
ob_end_flush();
?>
