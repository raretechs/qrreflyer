<?php
// Include necessary libraries
require_once('tcpdf/tcpdf.php');
require_once('phpqrcode/qrlib.php');

// Function to read template content
function readTemplate($templateName) {
    // Construct path to the template file
    $templateFile = "templates/$templateName.html";

    // Check if the template file exists
    if (file_exists($templateFile)) {
        // Read template content from file
        $templateContent = file_get_contents($templateFile);
        return $templateContent;
    } else {
        // Provide more detailed error message
        echo "Error: Template file '$templateFile' not found.";
        return false;
    }
}

// Function to parse CSV data and return an array of arrays
function parseCSVData($csvFile) {
    $csvData = [];
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        // Read the headers
        $headers = fgetcsv($handle, 1000, ",");
        
        // Read each row of data
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Combine headers with row data to create associative array
            $rowData = array_combine($headers, $row);
            // Add row data to the csvData array
            $csvData[] = $rowData;
        }
        
        fclose($handle);
        
        // Return the array of arrays representing the CSV data
        return $csvData;
    } else {
        return false;
    }
}

// Function to parse agent data from agent.txt in agent's subdirectory
function parseAgentData($agentName) {
    // Remove the .txt extension from the agent name
    $agentNameWithoutExtension = basename($agentName, '.txt');
    //echo "Agent Name without Extension: $agentNameWithoutExtension<br>";

    // Construct the agent directory path
    $agentDirectory = __DIR__ . '/agents/' . $agentNameWithoutExtension;
    //echo "Agent Directory: $agentDirectory<br>";

    // Initialize agent photo path
    $agentPhoto = '';

    // Check if the agent directory exists
    if (file_exists($agentDirectory) && is_dir($agentDirectory)) {
        // Generate agent photo path
        $agentPhoto = "agents/$agentNameWithoutExtension/agent_photo.jpg";
    } else {
        // Use a default agent photo path if the directory does not exist
        $agentPhoto = "agents/default/agent_photo.jpg";
    }

    // Check if the agent photo path is not empty
    if (!empty($agentPhoto)) {
        // Read agent data from agent.txt file
        $agentFilePath = "$agentDirectory/agent.txt";
        //echo "Agent Text File Full Path and Name: $agentFilePath<br>";
        if (file_exists($agentFilePath)) {
            // Read agent data from file
            $agentData = file($agentFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $parsedData = [];
            foreach ($agentData as $line) {
                // Split the line into key and value
                list($key, $value) = explode(': ', $line, 2);
                // Convert key to lowercase and remove spaces
                $key = strtolower(str_replace(' ', '', $key));
                // Map the data to the parsedData array
                $parsedData[$key] = $value;
            }
            // Add agent photo path to the parsed data
            $parsedData['Agent Photo'] = $agentPhoto;
            
			// Print parsed data
            //echo "Parsed Data: <pre>";
            //print_r($parsedData);
            //echo "</pre>";
            
			// Return parsed agent data
            return $parsedData;
        }
    }

    // Return false if agent data cannot be parsed
    return false;
}

// Function to get default agent data
function getDefaultAgentData($agentNameWithoutExtension) {
    // Default agent data if file not found
    return array(
        'Agent Photo' => "$agentNameWithoutExtension/agent_photo.jpg",
        'Agent Name' => $agentNameWithoutExtension,
        'Agent Title' => 'Real Estate Agent',
        'Agent License Number' => '123456',
        'Agent Mobile' => '(969) 888-1234',
        'Agent Office' => '(999) 777-4321',
        'Agent Email' => 'office@domain.com',
        'Agent Website' => 'www.domain.com'
    );
}

// Function to generate QR code with logo
function generateQRCode($url, $logo, $qrCodeFile) {
    // Generate QR code without logo
    QRcode::png($url, $qrCodeFile, QR_ECLEVEL_H, 6, 2);

    // Load logo image
    $logoImg = imagecreatefrompng($logo);

    // Load QR code image
    $qrCodeImg = imagecreatefrompng($qrCodeFile);

    // Get QR code image dimensions
    $qrCodeWidth = imagesx($qrCodeImg);
    $qrCodeHeight = imagesy($qrCodeImg);

    // Set maximum logo size (percentage of QR code area)
    $maxLogoSizePercent = 0.3; // Adjust as needed

    // Calculate maximum allowable logo dimensions
    $maxLogoWidth = intval($qrCodeWidth * $maxLogoSizePercent);
    $maxLogoHeight = intval($qrCodeHeight * $maxLogoSizePercent);

    // Get logo image dimensions
    $logoWidth = imagesx($logoImg);
    $logoHeight = imagesy($logoImg);

    // Scale logo if it exceeds maximum dimensions
    if ($logoWidth > $maxLogoWidth || $logoHeight > $maxLogoHeight) {
        // Calculate scaling factor
        $scale = min($maxLogoWidth / $logoWidth, $maxLogoHeight / $logoHeight);

        // Calculate scaled dimensions
        $scaledWidth = $logoWidth * $scale;
        $scaledHeight = $logoHeight * $scale;

        // Create a new image with scaled dimensions
        $scaledLogo = imagescale($logoImg, $scaledWidth, $scaledHeight);

        // Free up memory from original logo
        imagedestroy($logoImg);

        // Assign scaled logo to original logo image
        $logoImg = $scaledLogo;
    }

    // Calculate logo position (centered within QR code)
    $logoX = intval(($qrCodeWidth - imagesx($logoImg)) / 2);
    $logoY = intval(($qrCodeHeight - imagesy($logoImg)) / 2);

    // Merge logo onto the QR code
    imagecopy($qrCodeImg, $logoImg, $logoX, $logoY, 0, 0, imagesx($logoImg), imagesy($logoImg));

    // Save the QR code with logo
    imagepng($qrCodeImg, $qrCodeFile);

    // Free up memory
    imagedestroy($logoImg);
    imagedestroy($qrCodeImg);
}

// Function to generate PDF
function generatePDF($templateContent, $csvData, $agentData, $heroPhoto, $additionalPhotos, $qrCodeFile, $pdfFileName) {
    // Start output buffering
    ob_start();

    // Create TCPDF object
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Lorem Ipsum, Inc.');
    $pdf->SetTitle('Marketing Flyer');
    $pdf->SetSubject('Marketing Flyer');
    $pdf->SetKeywords('Lorem Ipsum, Marketing, Generated');

    // Set default header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // Set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Set font
    $pdf->SetFont('dejavusans', '', 10);

    // Add a page
    $pdf->AddPage();

    // Set content
    $pdf->writeHTML($templateContent);

    // End output buffering and store PDF content
    $pdfContent = ob_get_clean();

    // Output PDF to browser
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $pdfFileName . '"');
    echo $pdfContent;
}

// Function to get list of templates
function getTemplateList() {
    $templates = [];
    $templateDirectory = 'templates';
    if (is_dir($templateDirectory)) {
        $templateFiles = scandir($templateDirectory);
        foreach ($templateFiles as $file) {
            if ($file != '.' && $file != '..' && is_file("$templateDirectory/$file")) {
                $templates[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }
    }
    return $templates;
}

// Function to get list of agents
function getAgentList() {
    $agents = [];
    $agentDirectory = 'agents';
    if (is_dir($agentDirectory)) {
        $agentDirectories = scandir($agentDirectory);
        foreach ($agentDirectories as $directory) {
            if ($directory != '.' && $directory != '..' && is_dir("$agentDirectory/$directory")) {
                $agents[] = $directory;
            }
        }
    }
    return $agents;
}
?>
