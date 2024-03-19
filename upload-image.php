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

// Handle form submission
function handle_image_upload() {
    if ( isset( $_FILES['image'] ) ) {
        $file = $_FILES['image'];

        // Check for errors
        if ( $file['error'] === UPLOAD_ERR_OK ) {
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['path'] . '/' . basename( $file['name'] );

            // Move the uploaded file to the WordPress uploads directory
            move_uploaded_file( $file['tmp_name'], $upload_path );

            // Generate shortcode for the uploaded image
            $shortcode = '[image_shortcode url="' . $upload_dir['url'] . '/' . basename( $file['name'] ) . '"]';

            // Output the shortcode
            echo '<p>Shortcode for the uploaded image: ' . $shortcode . '</p>';
        } else {
            echo '<p>Error uploading image. Please try again.</p>';
        }
    }
}
add_action( 'init', 'handle_image_upload' );
