<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Supabase client setup
require_once 'path/to/supabase-php-sdk/autoload.php';

// Initialize Supabase client
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
    $url = $_POST['url'];
    $tags = $_POST['tags'];
    $authors = $_POST['authors'];
    $channel = $_POST['channel'];
    $name = $_POST['name'];
    $release = $_POST['release'];
    $createdAt = date('Y-m-d H:i:s');
    $archive = $_POST['archive']; // Use TEXT for URL
    $image = $_POST['image']; // Use TEXT for image URL

    $tagsString = '{' . implode(', ', array_map(function($tag) {
        return '"' . addslashes($tag) . '"';
    }, $tags)) . '}';
    $authorsString = '{' . implode(', ', array_map(function($author) {
        return '"' . addslashes($author) . '"';
    }, $authors)) . '}';
    $channelString = '{' . implode(', ', array_map(function($ch) {
        return '"' . addslashes($ch) . '"';
    }, $channel)) . '}';

    $sql = "INSERT INTO \"MainTable\" (\"URL\", \"Release\", \"CreatedAt\", \"Authors\", \"Channel\", \"Name\", \"Tags\", \"Archive\", \"Image\") 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$url, $release, $createdAt, $authorsString, $channelString, $name, $tagsString, $archive, $image]);

    if (isset($_FILES['cabinImage']) && $_FILES['cabinImage']['error'] == UPLOAD_ERR_OK) {
        $image = $_FILES['cabinImage'];
        $imageSize = $_FILES['cabinImage']['size'];
        $imageType = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

        if ($stmt->rowCount() > 0) {
            echo "Data inserted successfully into the MainTable.";
        } else {
            echo "Failed to insert data into the MainTable.";
        }       

        if ($imageSize > 5000000) {
            echo "Error: Image size must be under 5MB.";
        } else {
            if (in_array($imageType, ['jpg', 'jpeg', 'png', 'tiff', 'webp', 'svg', 'heif', 'heic'])) {
                $bucketName = 'Bookmarks';
                $folder = str_replace(' ', '_', strtolower($name));
                $newFileName = uniqid('cabin_', true) . '.' . $imageType;
                $filePath = $folder . '/' . $newFileName;

                // Supabase upload logic
                $response = $supabaseClient->storage()->getBucket($bucketName)->uploadFile($filePath, $image['tmp_name']);

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