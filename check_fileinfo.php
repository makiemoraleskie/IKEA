<?php
// Quick check to see if fileinfo extension is enabled
echo "<h2>PHP Configuration Check</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Loaded php.ini file: " . php_ini_loaded_file() . "\n";
echo "Additional php.ini files scanned: " . (php_ini_scanned_files() ?: 'None') . "\n\n";

if (class_exists('finfo')) {
    echo "✓ fileinfo extension is ENABLED\n";
    echo "fileinfo functions available:\n";
    echo "  - finfo class: " . (class_exists('finfo') ? 'Yes' : 'No') . "\n";
    echo "  - mime_content_type(): " . (function_exists('mime_content_type') ? 'Yes' : 'No') . "\n";
} else {
    echo "✗ fileinfo extension is NOT ENABLED\n";
    echo "\nTo enable it:\n";
    echo "1. Open this file: " . php_ini_loaded_file() . "\n";
    echo "2. Search for 'fileinfo' (Ctrl+F)\n";
    echo "3. Find: ;extension=fileinfo\n";
    echo "4. Remove the semicolon: extension=fileinfo\n";
    echo "5. Save and restart Apache in XAMPP Control Panel\n";
}
echo "</pre>";
?>

