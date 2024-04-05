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

// Function to parse CSV data
function parseCSVData($csvFile) {
    // Parse CSV file and return data as associative array
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        // Read the headers
        $headers = fgetcsv($handle, 1000, ",");

        // Initialize the data array
        $data = [];

        // Read each row of data
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // If the number of columns doesn't match the number of headers, skip the row
            if (count($row) !== count($headers)) {
                echo "Error: Number of columns in a row doesn't match the number of headers. Skipping row.<br>";
                echo "Headers: " . implode(", ", $headers) . "<br>";
                echo "Row: " . implode(", ", $row) . "<br>";
                continue;
            }

            // Combine headers with row data to create associative array
            $rowData = array_combine($headers, $row);

            // Replace empty values represented by double quotes with null
            $rowData = array_map(function($value) {
                return $value === "" ? null : $value;
            }, $rowData);

            // Add row data to the result array
            $data[] = $rowData;
        }

        fclose($handle);
        return $data;
    } else {
        return false;
    }
}

// Function to parse agent data from agent.txt in agent's subdirectory
function parseAgentData($agentName) {
    $agentDirectory = __DIR__ . '/agents/' . $agentName; // Path to agent's directory
    $agentFile = $agentDirectory . '/' . $agentName . '.txt'; // Path to agent's txt file

    // Debugging: Display the paths
    //echo "Agent File Path: $agentFile<br>";

    if (file_exists($agentFile)) {
        $agentData = file($agentFile, FILE_IGNORE_NEW_LINES); // Read file into an array, ignoring newlines
        
        // Initialize an empty array to store parsed data
        $parsedData = [];

        // Loop through each line in the agent file
        foreach ($agentData as $line) {
            // Split the line into label and value using ':'
            $parts = explode(':', $line, 2);
            if (count($parts) == 2) {
                $label = trim($parts[0]); // Trim any leading/trailing whitespace
                $value = trim($parts[1]); // Trim any leading/trailing whitespace
                $parsedData[$label] = $value; // Store label-value pair in parsed data array
            }
        }

        // Return the parsed agent data with default values if any field is missing
        return array(
            'Agent Photo' => isset($parsedData['Agent Photo']) ? trim($parsedData['Agent Photo']) : 'agent_photo.jpg',
            'Agent Name' => isset($parsedData['Agent Name']) ? trim($parsedData['Agent Name']) : $agentName,
            'Agent Title' => isset($parsedData['Agent Title']) ? trim($parsedData['Agent Title']) : 'Real Estate Agent',
            'Agent License Number' => isset($parsedData['Agent DRE Number']) ? trim($parsedData['Agent DRE Number']) : '123456',
            'Agent Mobile' => isset($parsedData['Agent Phone Number']) ? trim($parsedData['Agent Phone Number']) : '(999) 888-1234',
            'Agent Office' => isset($parsedData['Agent Office']) ? trim($parsedData['Agent Office']) : '(999) 777-4321',
            'Agent Email' => isset($parsedData['Agent Email']) ? trim($parsedData['Agent Email']) : 'office@domain.com',
            'Agent Website' => isset($parsedData['Agent Website']) ? trim($parsedData['Agent Website']) : 'www.domain.com'
        );
    }

    // Default agent data if file not found
    return array(
        'Agent Photo' => 'agent_photo.jpg',
        'Agent Name' => $agentName,
        'Agent Title' => 'Real Estate Agent',
        'Agent License Number' => '123456',
        'Agent Mobile' => '(999) 888-1234',
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

    // Add whitespace padding around the logo
    $margin = 10; // Adjust padding as needed
    $logoWidth = imagesx($logoImg);
    $logoHeight = imagesy($logoImg);
    $paddedLogoWidth = $logoWidth + 2 * $margin;
    $paddedLogoHeight = $logoHeight + 2 * $margin;
    $paddedLogo = imagecreatetruecolor($paddedLogoWidth, $paddedLogoHeight);
    $whiteColor = imagecolorallocate($paddedLogo, 255, 255, 255);
    imagefill($paddedLogo, 0, 0, $whiteColor);
    imagecopy($paddedLogo, $logoImg, $margin, $margin, 0, 0, $logoWidth, $logoHeight);

    // Merge padded logo onto the QR code
    $qrCodeImg = imagecreatefrompng($qrCodeFile);
    $qrCodeWidth = imagesx($qrCodeImg);
    $qrCodeHeight = imagesy($qrCodeImg);
    $logoX = ($qrCodeWidth - $paddedLogoWidth) / 2;
    $logoY = ($qrCodeHeight - $paddedLogoHeight) / 2;
    imagecopy($qrCodeImg, $paddedLogo, $logoX, $logoY, 0, 0, $paddedLogoWidth, $paddedLogoHeight);

    // Save the merged QR code with logo
    imagepng($qrCodeImg, $qrCodeFile);

    // Free up memory
    imagedestroy($logoImg);
    imagedestroy($paddedLogo);
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