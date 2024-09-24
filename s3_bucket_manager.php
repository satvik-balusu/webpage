<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Create an S3 client
function createS3Client($region, $accessKey, $secretKey) {
    return new S3Client([
        'region' => $region,
        'version' => 'latest',
        'credentials' => [
            'key' => $accessKey,
            'secret' => $secretKey,
        ],
    ]);
}

// Function to create a bucket
function createBucket($bucketName, $region, $accessKey, $secretKey) {
    $s3Client = createS3Client($region, $accessKey, $secretKey);
    try {
        $result = $s3Client->createBucket([
            'Bucket' => $bucketName,
            'ACL' => 'public-read', // Optional: Set ACL as needed
        ]);
        return "Bucket '$bucketName' created successfully.";
    } catch (AwsException $e) {
        return "Error creating bucket: " . $e->getMessage();
    }
}

// Function to delete a bucket
function deleteBucket($bucketName, $region, $accessKey, $secretKey) {
    $s3Client = createS3Client($region, $accessKey, $secretKey);
    try {
        // First, delete all objects in the bucket
        $objects = $s3Client->listObjects(['Bucket' => $bucketName]);
        if ($objects['Contents']) {
            foreach ($objects['Contents'] as $object) {
                $s3Client->deleteObject([
                    'Bucket' => $bucketName,
                    'Key' => $object['Key'],
                ]);
            }
        }
        // Now delete the bucket itself
        $s3Client->deleteBucket(['Bucket' => $bucketName]);
        return "Bucket '$bucketName' deleted successfully.";
    } catch (AwsException $e) {
        return "Error deleting bucket: " . $e->getMessage();
    }
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $installationName = $_POST['installation_name'];
    $region = $_POST['aws_region'];
    $accessKey = $_POST['aws_access_key'];
    $secretKey = $_POST['aws_secret_key'];
    $accountId = $_POST['aws_account_id'];

    // Example usage: Create a bucket with the installation name as its name
    echo createBucket($installationName, $region, $accessKey, $secretKey);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S3 Bucket Manager</title>
</head>
<body>
    <!-- The output of the PHP script will be displayed here -->
</body>
</html>