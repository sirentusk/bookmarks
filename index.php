<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

Supabase client setup (assuming you have already set this up)
require_once 'path/to/supabase-php-sdk/autoload.php';
$supabaseClient = new Supabase\SupabaseClient('SUPABASE_URL', 'SUPABASE_KEY');

// Database connection details
$supabaseUrl = getenv('SUPABASE_URL');
$supabaseKey = getenv('SUPABASE_KEY');
$password = getenv('DB_PASSWORD');
$dbParams = parse_url($supabaseUrl);

$host = 'db.tpogqybedqrbsgjoawxe.supabase.co';
$dbname = 'postgres';
$user = 'postgres';
$port = '5432';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password;sslmode=require";
$pdo = new PDO($dsn);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cabinType = $_POST['cabinType'];
    $description = $_POST['description'];
    $pricePerNight = $_POST['pricePerNight'];
    $pricePerWeek = $_POST['pricePerWeek'];
    $inclusions = $_POST['inclusions'];

    $inclusionsString = '{' . implode(', ', array_map(function($inclusion) {
        return '"' . addslashes($inclusion) . '"';
    }, $inclusions)) . '}';

    $sql = "INSERT INTO \"Cabins\" (\"cabinType\", \"Description\", \"pricePerNight\", \"pricePerWeek\", \"Inclusions\") VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cabinType, $description, $pricePerNight, $pricePerWeek, $inclusionsString]);

    if (isset($_FILES['cabinImage']) && $_FILES['cabinImage']['error'] == UPLOAD_ERR_OK) {
        $image = $_FILES['cabinImage'];
        $imageSize = $_FILES['cabinImage']['size'];
        $imageType = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

        if ($imageSize > 5000000) {
            echo "Error: Image size must be under 5MB.";
        } else {
            if (in_array($imageType, ['jpg', 'jpeg', 'png', 'tiff', 'webp', 'svg', 'heif', 'heic'])) {
                $bucketName = 'cabins';
                $folder = str_replace(' ', '_', strtolower($cabinType));
                $newFileName = uniqid('cabin_', true) . '.' . $imageType;
                $filePath = $folder . '/' . $newFileName;

                // Supabase upload logic
                $response = $supabaseClient->storage()->getBucket(cabins)->uploadFile($filePath, $image['tmp_name']);

                if ($response->isSuccess()) {
                    echo "Image uploaded successfully to Supabase.";
                } else {
                    echo "Failed to upload image to Supabase: " . $response->getMessage();
                }
            } else {
                echo "Invalid file type, please upload a jpg, jpeg, png, tiff, webp, svg, heif, or heic file type.";
            }
        }
    }
}

?>
