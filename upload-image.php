<?php
/*
Plugin Name: Upload Image   
Description: Upload image in resize it
Version: 3.3
Author: Parwinder Singh
 */






// Shortcode function
function image_upload_shortcode_handler( $atts ) {
    // Handle shortcode attributes if needed
    // For simplicity, we are not using any attributes here

    // Return HTML for the form
    ob_start();
    ?>
    <form id="image-upload-form" method="post" enctype="multipart/form-data">
        <input type="file" name="image" id="image">
        <input type="submit" value="Upload Image">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'image_upload_form', 'image_upload_shortcode_handler' );
// Function to resize image
function resizeImage($sourceImage, $targetWidth, $targetHeight,$resized_upload_path,$file_type) {
    // Get the dimensions of the original image
    list($sourceWidth, $sourceHeight) = getimagesize($sourceImage);
    
    // Create a new image with the target dimensions
    $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
    $upload_dir = wp_upload_dir();
    if($file_type=='image/jpeg'){
        // Load the original image
        $sourceImage = imagecreatefromjpeg($sourceImage); // Change this based on your image type
        
        // Resize the image
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);
        imagejpeg($targetImage, $resized_upload_path); // Change the file name and extension based on your needs
     }
    if($file_type=='image/png'){
        // Load the original image
        $sourceImage = imagecreatefrompng($sourceImage); // Change this based on your image type
        
        // Resize the image
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);
        imagepng($targetImage, $resized_upload_path); // Change the file name and extension based on your needs
    }
   
    // Save the resized image to a file
    
    // Free up memory
    imagedestroy($targetImage);
}
// Handle form submission
function handle_image_upload() {
    if ( isset( $_FILES['image'] ) ) {
        $file = $_FILES['image'];
        $file_type= $file['type'];
        if($file_type=='image/jpeg' || $file_type=='image/png'){

            if ( $file['error'] === UPLOAD_ERR_OK ) {
                $file_name=time().'.jpg';
                $sourceImage = $file['tmp_name']; // Path to the original image
                $targetWidth = 300; // Desired width
                $targetHeight = 200; // Desired height
                $upload_dir = wp_upload_dir();
                $upload_path = $upload_dir['path'] . '/' . $file_name;
                $resized_upload_path = $upload_dir['path'] . '/resized' . $file_name;
                $file_type= $file['type'];
                resizeImage($sourceImage, $targetWidth, $targetHeight,$resized_upload_path,$file_type);

            

                // Move the uploaded file to the WordPress uploads directory
                move_uploaded_file( $file['tmp_name'], $upload_path );

                // Generate shortcode for the uploaded image
                $shortcode = '[image_shortcode url="' . $upload_dir['url'] . '/' . $file_name . '"]';

                // Output the shortcode
                echo '<img src="' . $upload_dir['url'] . '/resized' . $file_name . '" ><br><img src="' . $upload_dir['url'] . '/' . $file_name . '" >';
                
            } else {
                echo '<p>Error uploading image. Please try again.</p>';
            }

         }else{
            echo '<p>Please select jpeg and png file and less then 1mb size</p>';
         }
        // Check for errors
    }
}
add_action( 'init', 'handle_image_upload' );
