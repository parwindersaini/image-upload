<?php
/*
Plugin Name: AJAX Image Upload
Description: It allows upload resize image and use short code: [image_upload].
Version: 3.3
Author: Parwinder Singh
*/
// 1. Set up the plugin structure
add_action('init', 'wg_image_upload_init');

function wg_image_upload_init()
{
    // 2. Create the shortcode for the form
    add_shortcode('image_upload', 'wg_image_upload_form');

    // 3. Implement AJAX image upload functionality
    add_action('wp_ajax_image_upload', 'wg_handle_image_upload');
    add_action('wp_ajax_nopriv_image_upload', 'wg_handle_image_upload');
}

function wg_image_upload_form()
{
    ob_start();
?>
    <form id="image_upload_form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
        <input type="file" name="image" id="image">
        <input type="submit" value="Upload Image">
        <?php //wp_nonce_field('image_upload_nonce', 'image_upload_nonce'); 
        ?>
    </form>
    <div id="image_upload_message"></div>
    <script>
        jQuery(document).ready(function($) {
            $('#image_upload_form').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                formData.append('action', 'image_upload');
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#image_upload_message').html(response);
                    }
                });
            });
        });
    </script>
<?php
    return ob_get_clean();
}

// Function to resize image
function resizeImg($sourceImage, $targetWidth, $targetHeight, $resized_upload_path, $file_type)
{
    // Get the dimensions of the original image
    list($sourceWidth, $sourceHeight) = getimagesize($sourceImage);

    // Create a new image with the target dimensions
    $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
    $upload_dir = wp_upload_dir();
    if ($file_type == 'image/jpeg') {
        // Load the original image
        $sourceImage = imagecreatefromjpeg($sourceImage); // Change this based on your image type

        // Resize the image
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);
        imagejpeg($targetImage, $resized_upload_path); // Change the file name and extension based on your needs
    }
    if ($file_type == 'image/png') {
        // Load the original image
        $sourceImage = imagecreatefrompng($sourceImage); // Change this based on your image type

        // Resize the image
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);
        imagepng($targetImage, $resized_upload_path); // Change the file name and extension based on your needs
    }
    // Free up memory
    imagedestroy($targetImage);
}

// 4. Handle the uploaded image on the server-side
function wg_handle_image_upload()
{
    // $nonce = $_POST['image_upload_nonce'];
    // if (!wp_verify_nonce($nonce, 'image_upload_nonce')) {
    //     die('Security check');
    // }

    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];
        $file_type = $file['type'];
        if ($file_type == 'image/jpeg' || $file_type == 'image/png') {

            if ($file['error'] === UPLOAD_ERR_OK) {
                $file_name = time() . '.jpg';
                $sourceImage = $file['tmp_name']; // Path to the original image
                $targetWidth = 300; // Desired width
                $targetHeight = 200; // Desired height
                $upload_dir = wp_upload_dir();
                $upload_path = $upload_dir['path'] . '/' . $file_name;
                $resized_upload_path = $upload_dir['path'] . '/resized' . $file_name;
                $file_type = $file['type'];
                resizeImg($sourceImage, $targetWidth, $targetHeight, $resized_upload_path, $file_type);

                // Move the uploaded file to the WordPress uploads directory
                move_uploaded_file($file['tmp_name'], $upload_path);

                // Generate shortcode for the uploaded image
                $shortcode = '[image_shortcode url="' . $upload_dir['url'] . '/' . $file_name . '"]';

                // Output the shortcode
                echo '<img src="' . $upload_dir['url'] . '/resized' . $file_name . '" ><br><img src="' . $upload_dir['url'] . '/' . $file_name . '" >';
            } else {
                echo '<p>Error uploading image. Please try again.</p>';
            }
        } else {
            echo '<p>Please select jpeg and png file and less then 1mb size</p>';
        }
        // Check for errors
    }
    wp_die();
}
// 5. Enqueue necessary scripts and styles (optional)
function enqueue_scripts_and_styles()
{
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'enqueue_scripts_and_styles');
