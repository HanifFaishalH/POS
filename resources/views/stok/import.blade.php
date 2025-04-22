<form action="{{ url('/stok/import_ajax') }}" method="POST" id="form-import-stok" enctype="multipart/form-data">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Import Data Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Download Template</label>
                    <a href="{{ asset('template_stok.xlsx') }}" class="btn btn-info btn-sm" download>
                        <i class="fas fa-file-excel"></i> Download Template
                    </a>
                    <small id="error-kategori_id" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Pilih File Excel</label>
                    <input type="file" name="file_stok" id="file_stok" class="form-control" required>
                    <small class="text-muted">Format file harus .xlsx atau .xls (Max 2MB)</small>
                    <small id="error-file_stok" class="error-text form-text text-danger"></small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-upload"></i> Upload
                </button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    $("#form-import-stok").validate({
        rules: {
            file_stok: {
                required: true,
                extension: "xlsx|xls",
                filesize: 2048 // 2MB
            }
        },
        messages: {
            file_stok: {
                required: "File harus dipilih",
                extension: "Hanya file Excel (.xlsx, .xls) yang diperbolehkan",
                filesize: "Ukuran file maksimal 2MB"
            }
        },
        submitHandler: function(form) {
            var formData = new FormData(form);
            var submitBtn = $('#submitBtn');
            
            // Disable button and show loading
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
            $('.error-text').text('');

            $.ajax({
                url: form.action,
                type: form.method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.status) {
                        $('#modal-master').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            if (typeof dataStok !== 'undefined') {
                                dataStok.ajax.reload(null, false);
                            }
                        });
                    } else {
                        $('.error-text').text('');
                        if (response.errors) {
                            $.each(response.errors, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Import',
                            html: response.message + 
                                 (response.warning ? '<br><br>' + response.warning : '') +
                                 (response.error_details ? '<br><br>' + response.error_details.join('<br>') : '')
                        });
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Terjadi kesalahan saat mengupload file';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $(`#error-${key}`).text(value[0]);
                        });
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-upload"></i> Upload');
                }
            });
            return false;
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });

    // Add custom validator for file size
    $.validator.addMethod('filesize', function(value, element, param) {
        return this.optional(element) || (element.files[0].size <= param * 1024 * 1024);
    }, 'Ukuran file melebihi batas maksimal');
});
</script>