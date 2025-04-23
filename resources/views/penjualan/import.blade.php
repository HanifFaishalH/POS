<form action="{{ url('/penjualan/import_ajax') }}" method="POST" id="form-import-penjualan" enctype="multipart/form-data">
    @csrf
    <div id="modal-import" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Data Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info-circle"></i> Petunjuk Format File</h5>
                    <ol>
                        <li>Kolom A: Kode Penjualan (unik, contoh: PJ001)</li>
                        <li>Kolom B: Tanggal Penjualan (format: YYYY-MM-DD)</li>
                        <li>Kolom C: Nama Pembeli</li>
                        <li>Kolom D: ID Barang (harus sesuai dengan data barang)</li>
                        <li>Kolom E: Jumlah Barang (angka bulat positif)</li>
                        <li>Kolom F: Harga Satuan (angka, tanpa tanda pemisah ribuan)</li>
                    </ol>                    
                </div>

                <div class="form-group">
                    <label>Download Template</label>
                    <a href="{{ asset('template_penjualan.xlsx') }}" class="btn btn-info btn-sm" download>
                        <i class="fas fa-file-excel"></i> Download Template
                    </a>
                    <small id="error-file_penjualan" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Pilih File Excel</label>
                    <input type="file" name="file_penjualan" id="file_penjualan" class="form-control" required>
                    <small class="text-muted">Format file harus .xlsx atau .xls (Max 2MB)</small>
                    <small id="error-file_penjualan" class="error-text form-text text-danger"></small>
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
    $("#form-import-penjualan").validate({
        rules: {
            file_penjualan: {
                required: true,
                extension: "xlsx|xls",
                filesize: 2 // dalam MB
            }
        },
        messages: {
            file_penjualan: {
                required: "File harus dipilih",
                extension: "Hanya file Excel (.xlsx, .xls) yang diperbolehkan",
                filesize: "Ukuran file maksimal 2MB"
            }
        },
        submitHandler: function(form) {
            var formData = new FormData(form);
            var submitBtn = $('#submitBtn');

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
            $('.error-text').text('');

            $.ajax({
                url: form.action,
                type: form.method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        $('#modal-import').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            if (typeof dataPenjualan !== 'undefined') {
                                dataPenjualan.ajax.reload(null, false);
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
        }
    });

    // custom validator untuk file size
    $.validator.addMethod('filesize', function(value, element, param) {
        return this.optional(element) || (element.files[0].size <= param * 1024 * 1024);
    }, 'Ukuran file melebihi batas maksimal');
});

</script>