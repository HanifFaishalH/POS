@if(isset($barang))
<form action="{{ url('/barang/' . $barang->barang_id . '/delete_ajax') }}" method="POST" id="form-delete">
    @csrf
    @method('DELETE')
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Penghapusan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <h5><i class="icon fas fa-ban"></i> Konfirmasi !!!</h5>
                    Apakah Anda yakin ingin menghapus data barang berikut?
                </div>
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $barang->kategori->kategori_nama }}</td>
                    </tr>
                    <tr>
                        <th>Kode Barang</th>
                        <td>{{ $barang->barang_kode }}</td>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <td>{{ $barang->barang_nama }}</td>
                    </tr>
                    <tr>
                        <th>Harga Beli</th>
                        <td>{{ $barang->harga_beli }}</td>
                    </tr>
                    <tr>
                        <th>Harga Jual</th>
                        <td>{{ $barang->harga_jual }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $("#form-delete").validate({
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            dataBarang.ajax.reload(); // Reload tabel barang
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Terjadi kesalahan saat mengirim data. Silakan coba lagi.'
                        });
                    }
                });
                return false;
            }
        });
    });
</script>
@else
<div class="alert alert-danger">
    Data barang tidak ditemukan.
</div>
@endif