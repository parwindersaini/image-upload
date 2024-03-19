jQuery(document).ready(function($) {
    $('#image-form').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData($(this)[0]);

console.log(formData);

        // $.ajax({
        //     url: ajax_image_upload.ajaxurl,
        //     type: 'POST',
        //     data: formData,
        //     async: false,
        //     cache: false,
        //     contentType: false,
        //     processData: false,
        //     success: function(response) {
        //         if (response.success) {
        //             // Do something with the uploaded image URL
        //             console.log('Image uploaded successfully:', response.image_url);
        //         } else {
        //             console.error('Error:', response.message);
        //         }
        //     }
        // });
    });
});
