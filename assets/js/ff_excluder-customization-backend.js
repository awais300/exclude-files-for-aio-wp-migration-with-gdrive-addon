jQuery(document).ready(function($) {

    /*JS for GDrive upload*/
    $(document).on('click', '#upload_btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var aio_upload_url = $(this).attr('data-url');
        generate_url(aio_upload_url);
    });

    function generate_url(aio_upload_url) {
        let gdrive_folder_id = $('#gdrive_folder_id').val();
        let backup_file_name = '';

        if (!gdrive_folder_id) {
            alert('Pleas input GDrive folder ID.');
            return;
        }

        if ($('input#check-custom-path').is(':checked')) {
            backup_file_name = $('#custom-path').val();
            //partial_query_string = '&custom_path=' + backup_file_name;
            partial_query_string = '&custom_path=';
            //partial_query_string = '';
            if (!backup_file_name) {
                alert('Please input file path.');
                return;
            }
            backup_file_name = LOCAL.custom_file_path + '/' + backup_file_name;
        } else {
            partial_query_string = '';
            backup_file_name = $('#backup_file_name').val();
            if (!backup_file_name) {
                alert('Please select file.');
                return;
            }
            backup_file_name = LOCAL.aio_file_path + '/' + backup_file_name;
        }

        if (gdrive_folder_id && backup_file_name) {
            //alert(backup_file_name);
            new_url = aio_upload_url + '&gdrive_folder_id=' + gdrive_folder_id + '&backup_file_name=' + backup_file_name + partial_query_string;
            window.location = new_url;
        } else {
            alert('GDrive folder ID OR Backup file is missing');
        }
    }


    $(document).on('change', '#check-custom-path', function(e) {
        if (this.checked) {
            $('#backup_file_name').prop('disabled', 'disabled');
            $('#backup_file_name').val('');
            $('#custom-row').show();
        } else {
            $('#custom-row').hide();
            $('#backup_file_name').prop('disabled', '');
        }
    });



    /*Ajax uploading*/
    $(document).on('click', '#mybtn', function(e) {
        run_ajax(0, 0, 0);
    });


    function run_ajax(first_run = 0, status, file_seek) {
        $.ajax({
            url: LOCAL.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                '_ajax_nonce': LOCAL._ajax_nonce,
                'action': 'init_upload',
                'gdrive_folder_id': $('#gdrive_folder_id').val(),
                'backup_file_name': $('#backup_file_name').val(),
                'first_run': first_run,
                'file_seek': file_seek,
                'status': status
            },
            success: function($data) {
                console.log('success');
                console.log($data);

                if ($data.error == true) {
                    console.log('e1');
                    alert($data.message);
                }

                if ($data.error == false) {
                    console.log('t1');
                    if ($data.status == 0) {
                        console.log('t2');
                        run_ajax(1, $data.status, $data.file_seek);
                    } else {
                        console.log('t3');
                        alert('File Uploaded!');
                    }
                }

            },
            error: function(error) {
                console.log('error');
            }
        }); //ajax end
    }

    // Get the element with id="default-open" and click on it
    document.getElementById("default-open").click();

});

function openTab(evt, tabName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}