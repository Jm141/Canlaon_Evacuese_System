<?php
/**
 * Barcode Generator Class
 * Generates Code 128 barcodes for ID cards
 */

class BarcodeGenerator {
    private $width;
    private $height;
    private $fontSize;
    private $barWidth;
    private $barHeight;
    
    public function __construct($width = 300, $height = 100) {
        $this->width = $width;
        $this->height = $height;
        $this->fontSize = 2; // Smaller font for better fit
        $this->barWidth = 3; // Thicker bars for better visibility
        $this->barHeight = 50; // Taller bars
    }
    
    /**
     * Generate Code 128 barcode
     */
    public function generateCode128($data) {
        // Code 128 character set
        $code128 = [
            ' ' => '11011001100', '!' => '11001101100', '"' => '11001100110',
            '#' => '10010011000', '$' => '10010001100', '%' => '10001001100',
            '&' => '10011001000', "'" => '10011000100', '(' => '10001100100',
            ')' => '11001001000', '*' => '11001000100', '+' => '11000100100',
            ',' => '10110011100', '-' => '10011011100', '.' => '10011001110',
            '/' => '10111001100', '0' => '10011101100', '1' => '10011100110',
            '2' => '11001110010', '3' => '11001011100', '4' => '11001001110',
            '5' => '11011100100', '6' => '11001110100', '7' => '11101101110',
            '8' => '11101001100', '9' => '11100101100', ':' => '11100100110',
            ';' => '11101100100', '<' => '11100110100', '=' => '11100110010',
            '>' => '11011011000', '?' => '11011000110', '@' => '11000110110',
            'A' => '10100011000', 'B' => '10001011000', 'C' => '10001000110',
            'D' => '10110001000', 'E' => '10001101000', 'F' => '10001100010',
            'G' => '11010001000', 'H' => '11000101000', 'I' => '11000100010',
            'J' => '10110111000', 'K' => '10110001110', 'L' => '10001101110',
            'M' => '10111011000', 'N' => '10111000110', 'O' => '10001110110',
            'P' => '11101110110', 'Q' => '11010001110', 'R' => '11000101110',
            'S' => '11011101000', 'T' => '11011100010', 'U' => '11011101110',
            'V' => '11101011000', 'W' => '11101000110', 'X' => '11100010110',
            'Y' => '11101101000', 'Z' => '11101100010'
        ];
        
        // Start character (Code 128A)
        $barcode = '11010000100';
        
        // Add data
        for ($i = 0; $i < strlen($data); $i++) {
            $char = $data[$i];
            if (isset($code128[$char])) {
                $barcode .= $code128[$char];
            }
        }
        
        // Calculate checksum
        $checksum = 103; // Start character value
        for ($i = 0; $i < strlen($data); $i++) {
            $char = $data[$i];
            $value = ord($char) - 32;
            $checksum += ($value * ($i + 1));
        }
        $checksum = $checksum % 103;
        
        // Add checksum character
        $checksumChar = chr($checksum + 32);
        if (isset($code128[$checksumChar])) {
            $barcode .= $code128[$checksumChar];
        }
        
        // Stop character
        $barcode .= '1100011101011';
        
        return $barcode;
    }
    
    /**
     * Create barcode image
     */
    public function createBarcodeImage($data) {
        // Generate barcode pattern
        $barcode = $this->generateCode128($data);
        
        // Calculate image dimensions
        $imageWidth = (int)(strlen($barcode) * $this->barWidth + 60);
        $imageHeight = (int)$this->height;
        
        // Create image
        $image = imagecreate($imageWidth, $imageHeight);
        
        // Define colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        
        // Fill background
        imagefill($image, 0, 0, $white);
        
        // Draw barcode
        $x = 30;
        for ($i = 0; $i < strlen($barcode); $i++) {
            if ($barcode[$i] == '1') {
                imagefilledrectangle($image, $x, 10, $x + $this->barWidth - 1, 10 + $this->barHeight, $black);
            }
            $x += $this->barWidth;
        }
        
        // Add text below barcode
        $textWidth = strlen($data) * imagefontwidth($this->fontSize);
        $textX = (int)(($imageWidth - $textWidth) / 2);
        $textY = (int)($imageHeight - 20);
        imagestring($image, $this->fontSize, $textX, $textY, $data, $black);
        
        // Output image
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        
        imagedestroy($image);
        
        return 'data:image/png;base64,' . base64_encode($imageData);
    }
    
    /**
     * Generate QR Code (simplified version)
     */
    public function generateQRCode($data) {
        // This is a simplified QR code generator
        // In production, you should use a proper QR library like phpqrcode
        
        $size = 200;
        $image = imagecreate((int)$size, (int)$size);
        
        // Define colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        
        // Fill background
        imagefill($image, 0, 0, $white);
        
        // Create a simple pattern (this is just for demonstration)
        // In real implementation, use proper QR code generation
        for ($i = 0; $i < $size; $i += 10) {
            for ($j = 0; $j < $size; $j += 10) {
                if (rand(0, 1)) {
                    imagefilledrectangle($image, $i, $j, $i + 8, $j + 8, $black);
                }
            }
        }
        
        // Output image
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        
        imagedestroy($image);
        
        return 'data:image/png;base64,' . base64_encode($imageData);
    }
}
?> 