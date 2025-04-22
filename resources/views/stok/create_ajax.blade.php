<form action="{{ url('/stok/store_ajax') }}" method="POST" id="form-tambah-stok">
    @csrf
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Stok Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label>Barang</label>
                    <select name="barang_id" class="form-control" required>
                        <option value="">- Pilih Barang -</option>
                        @foreach($barang as $item)
                            <option value="{{ $item->barang_id }}">{{ $item->barang_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-barang_id" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Supplier</label>
                    <select name="supplier_id" class="form-control" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($supplier as $s)
                            <option value="{{ $s->supplier_id }}">{{ $s->supplier_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-supplier_id" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>User</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">- Pilih User -</option>
                        @foreach($user as $item)
                            <option value="{{ $item->user_id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-user_id" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Tanggal Stok <small class="text-muted">(otomatis jika dikosongkan)</small></label>
                    <input type="datetime-local" name="stok_tanggal" id="stok_tanggal" class="form-control" placeholder="Kosongkan untuk isi otomatis">
                    <small id="error-stok_tanggal" class="error-text form-text text-danger"></small>
                </div>                

                <div class="form-group">
                    <label>Jumlah Stok</label>
                    <input type="number" name="stok_jumlah" class="form-control" min="1" required>
                    <small id="error-stok_jumlah" class="error-text form-text text-danger"></small>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
<script>

function getDateTimeLocalNow() {
    const now = new Date();
    now.setSeconds(0, 0); // hapus detik & milidetik biar sesuai input
    return now.toISOString().slice(0, 16); // ambil "YYYY-MM-DDTHH:MM"
}

    $(document).ready(function () {
        $("#form-tambah-stok").validate({
            rules: {
                barang_id: { required: true },
                supplier_id: { required: true },
                user_id: { required: true },
                stok_tanggal: { required: false, date: true },
                stok_jumlah: { required: true, min: 1 }
            },
            messages: {
                barang_id: { required: "Barang harus dipilih." },
                supplier_id: { required: "Supplier harus dipilih." },
                user_id: { required: "User harus dipilih." },
                stok_jumlah: {
                    required: "Jumlah stok harus diisi.",
                    min: "Jumlah stok minimal 1."
                }
            },
            
            submitHandler: function (form) {
                const tanggalInput = $('#stok_tanggal');
                if (!tanggalInput.val()) {
                    tanggalInput.val(getDateTimeLocalNow());
                }
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function (response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            dataStok.ajax.reload(); // Reload datatable
                            form.reset();
                            $('.error-text').text('');
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function (prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengirim data.'
                        });
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
