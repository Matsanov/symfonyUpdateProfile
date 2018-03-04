$('document').ready(function () {

    // Events

    //When clicked getting image id and username and attaching them in the html
    $('.gallery_image').click(function () {
        var image_id = $(this).data('image-id');
        var username = $(this).data('username');
        $('#image_id_modal').attr('value', image_id);
        $('.modal-username').html(username);
    });

    //Clicking on picture
    $('.thumbnail').click(function () {


        $('.image-placeholder').empty();
        $('.modal-description').empty();

        //Getting the title and the description
        var title = $(this).parent('a').attr("title");
        var description = $(this).parent('a').attr("description");


        //Getting the image id
        var image_id = $(this).parent().parent().data('imageId');

        //Appending the comments for the picture
        appendComments(image_id);

        //Setting the title and the description in the modal picture
        $('.modal-title').html('Title: ' + '<br>' + title);
        $('.modal-description').html('Description: ' + description);
        $($(this).parents('div').html()).appendTo('.image-placeholder');
        $('#myModal').modal({show: true});
    });

    //Appending the comments for picture
    function appendComments(image_id) {

        //Get the contents
        $('#comentsAndUsers').html('');

        $.ajax({

            type: 'GET',
            url: '/comments/' + image_id,
            success: function (response) {
                response = JSON.parse(response);

                if (response.status == 'ok') {

                    //Appending the comments view from the controller
                    $('#comentsAndUsers').append(response.comments);

                    $('.commentDelete').click(function () {
                        var comment = $(this).parent().find('#comment-message textarea').val();
                        var image_id = $(this).parent().find('#image_id_modal').val();
                        var comment_id = $(this).data('commentId');

                        $.ajax({
                            type: 'POST',
                            url: '/codeigniter/admin/comment/' + comment_id + '/delete',

                            success: function (response) {
                                $('#comment_' + comment_id).remove()

                            }
                        });
                    });

                }

            }
        });
    }


    //When clicked getting the values of the comment and image id
    $('#commentSubmit').click(function () {
        var comment = $(this).parent().find('#comment-message textarea').val();
        var image_id = $(this).parent().find('#image_id_modal').val();

        $.ajax({
            type: 'POST',
            url:  '/comment/' + image_id,
            data: {'comment': comment},
            beforeSend: function () {
                $('#alerts').html('');
            },
            success: function (response) {
                var response = JSON.parse(response);

                //If status = true
                if (response.status) {

                    //Calling function appendComments to append the comments with image_id
                    appendComments(image_id);

                    //Clearing the textarea
                    document.getElementById('comment').value = "";

                    //If status errors
                } else {

                    //Appending the alerts
                    response.errors.forEach(function (error) {
                        var element = $('<div class="alert alert-warning" role="alert"></div>').append(error);
                        $('#alerts').append(element);
                    });

                    //Clearing the textarea
                    document.getElementById('comment').value = "";

                }

                //Setting the alert to fade and remove
                window.setTimeout(function () {
                    $(".alert").fadeTo(200, 0).slideUp(500, function () {
                        $(this).remove();
                    });
                }, 5000);


            }
        });
    });


    $('#myModal').on('shown.bs.modal', function () {
        //When clicked getting the values of the comment and image id
        $('.commentDelete').click(function () {
            var comment = $(this).parent().find('#comment-message textarea').val();
            var image_id = $(this).parent().find('#image_id_modal').val();
            var comment_id = $(this).data('commentId');

            $.ajax({
                type: 'POST',
                url: '/codeigniter/admin/comment/' + comment_id + '/delete',

                success: function (response) {

                    //Removing the comment
                    $('#comment_' + comment_id).remove();

                    var response = JSON.parse(response);

                }
            });
        });
    });


    //Loading the JS datatables
    $(document).ready(function () {
        $('#table_id').DataTable();
    });

});
