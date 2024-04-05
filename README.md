# qrreflyer

This project is a real estate flyer generator that allows users to create custom flyers based on templates, CSV data, and agent information. It generates PDF flyers with property details, images, QR codes, and agent information.

## Features

- Upload CSV data containing property information
- Select a template for the flyer
- Upload images for the flyer
- Customize agent information
- Generate QR code with a custom URL
- Debug mode for troubleshooting
- Automatically generates PDF flyers

## Installation

1. Clone the repository:
git clone https://github.com/raretechs/qrreflyer.git


2. Install dependencies:
composer install

3. Set up a web server (Apache, Nginx, etc.) and point it to the project directory.

4. Make sure the `agents` directory is writable by the web server for storing agent data.

5. Ensure that the `tcpdf` and `phpqrcode` libraries are properly configured and accessible.

## Usage

1. Access the application through your web browser.

2. Fill out the form with the required details:
   - Choose a template
   - Upload CSV data
   - Upload property images
   - Enter agent information
   - Optionally, enter a custom URL for the QR code

3. Submit the form.

4. View the generated PDF flyer or download it.

## Contributing

Contributions are welcome! Feel free to submit bug reports, feature requests, or pull requests.

1. Fork the repository.
2. Create your feature branch (`git checkout -b feature/NewFeature`).
3. Commit your changes (`git commit -am 'Add some feature'`).
4. Push to the branch (`git push origin feature/NewFeature`).
5. Create a new Pull Request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
