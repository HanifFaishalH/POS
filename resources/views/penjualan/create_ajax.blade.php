<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Tambah Penjualan Baru</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <!-- Hapus form yang nested dan gunakan id yang benar -->
            <form id="form-tambah-penjualan" action="{{ url('penjualan/store_ajax') }}" method="POST">
                @csrf
                
                <!-- Tambahkan input hidden untuk penjualan_kode -->
                <input type="hidden" name="penjualan_kode" value="TRX-{{ date('YmdHis') }}">
                
                <div class="form-group">
                    <label>Tanggal Penjualan</label>
                    <input type="datetime-local" name="penjualan_tanggal" class="form-control" 
                    value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" required>
                    <small id="error-penjualan_tanggal" class="error-text text-danger"></small>
                </div>
        
                <div class="form-group">
                    <label>Pembeli</label>
                    <input type="text" name="pembeli" class="form-control" maxlength="50" required>
                    <small id="error-pembeli" class="error-text text-danger"></small>
                </div>
        
                <div class="form-group">
                    <label>Petugas</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">- Pilih Petugas -</option>
                        @foreach($user as $u)
                            <option value="{{ $u->user_id }}">{{ $u->nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-user_id" class="error-text text-danger"></small>
                </div>
        
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Item Penjualan</h6>
                    </div>
                    <div class="card-body">
                        <div id="items-container">
                            <div class="item-row row mb-2">
                                <div class="col-md-5">
                                    <select name="items[0][barang_id]" class="form-control select-barang" required>
                                        <option value="">- Pilih Barang -</option>
                                        @foreach($barang as $item)
                                            <option value="{{ $item->barang_id }}" data-harga="{{ $item->harga_jual }}">
                                                {{ $item->barang_nama }} (Rp {{ number_format($item->harga_jual, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="items[0][harga]" class="form-control harga" placeholder="Harga" min="1" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="items[0][jumlah]" class="form-control jumlah" placeholder="Qty" min="1" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-remove-item" disabled>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="btn-add-item" class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-plus"></i> Tambah Item
                        </button>
                    </div>
                </div>
        
                <div class="form-group row">
                    <label class="col-md-3 col-form-label font-weight-bold">Total Penjualan</label>
                    <div class="col-md-9">
                        <input type="text" id="total-penjualan" class="form-control-plaintext font-weight-bold text-primary" 
                               value="Rp 0" readonly style="font-size: 1.2rem;">
                    </div>
                </div>
        
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Penjualan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
    // Inisialisasi item pertama
    initItemRow($('.item-row').first());
    
    // Tambah item baru
    $('#btn-add-item').click(function() {
        const itemCount = $('.item-row').length;
        const newRow = $('.item-row').first().clone();
        
        // Update nama atribut untuk array
        newRow.find('[name]').each(function() {
            const name = $(this).attr('name').replace('[0]', `[${itemCount}]`);
            $(this).attr('name', name).val('');
        });
        
        newRow.find('.subtotal').val('Rp 0');
        newRow.find('.btn-remove-item').prop('disabled', false);
        $('#items-container').append(newRow);
        initItemRow(newRow);
    });
    
    // Fungsi inisialisasi item row
    function initItemRow(row) {
        row.find('.select-barang').change(function() {
            const selectedOption = $(this).find('option:selected');
            const harga = selectedOption.data('harga') || 0;
            row.find('.harga').val(harga).trigger('input');
        });
        
        row.find('.jumlah, .harga').on('input', function() {
            const harga = parseFloat(row.find('.harga').val()) || 0;
            const jumlah = parseInt(row.find('.jumlah').val()) || 0;
            const subtotal = harga * jumlah;
            row.find('.subtotal').val('Rp ' + subtotal.toLocaleString('id-ID'));
            calculateTotal();
        });
        
        row.find('.btn-remove-item').click(function() {
            if ($('.item-row').length > 1) {
                $(this).closest('.item-row').remove();
                calculateTotal();
            }
        });
    }
    
    // Hitung total
    function calculateTotal() {
        let total = 0;
        $('.item-row').each(function() {
            const subtotalText = $(this).find('.subtotal').val().replace(/[^\d]/g, '');
            total += parseInt(subtotalText) || 0;
        });
        $('#total-penjualan').val('Rp ' + total.toLocaleString('id-ID'));
    }
    
    // Submit form
    $('#form-tambah-penjualan').submit(function(e) {
    e.preventDefault();
    const form = $(this);

    // Validasi minimal 1 item dengan barang terpilih
    let validItems = 0;
    $('.select-barang').each(function() {
        if ($(this).val()) validItems++;
    });

    if (validItems === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Minimal harus ada 1 item penjualan'
        });
        return false;
    }

    // Ambil barang_id dari item pertama (jika ada)
    const firstBarangId = $('.item-row').first().find('.select-barang').val();
    if (firstBarangId) {
        $('input[name="penjualan_kode"]').val('TRX-' + firstBarangId);
    }

    // Kumpulkan data form
    const formData = new FormData(form[0]);
    const items = [];

    $('.item-row').each(function(index) {
        const barangId = $(this).find('[name="items['+index+'][barang_id]"]').val();
        if (barangId) {
            items.push({
                barang_id: barangId,
                harga: $(this).find('[name="items['+index+'][harga]"]').val(),
                jumlah: $(this).find('[name="items['+index+'][jumlah]"]').val()
            });
        }
    });

    formData.append('items', JSON.stringify(items));
        
        // Kirim data via AJAX
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#ajaxModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 1500
                    });
                    if (typeof tablePenjualan !== 'undefined') {
                        tablePenjualan.ajax.reload(null, false);
                    }
                } else {
                    // Tampilkan error validasi
                    $('.error-text').text('');
                    $('.is-invalid').removeClass('is-invalid');
                    
                    if (response.errors) {
                        $.each(response.errors, function(prefix, val) {
                            const errorElement = $('[name="' + prefix + '"]');
                            if (errorElement.length) {
                                errorElement.addClass('is-invalid');
                                errorElement.closest('.form-group').find('.error-text').text(val[0]);
                            } else if (prefix.startsWith('items.')) {
                                // Handle error untuk items array
                                const parts = prefix.split('.');
                                const index = parts[1];
                                const fieldName = parts[2];
                                
                                const row = $('.item-row').eq(index);
                                if (row.length) {
                                    const input = row.find('[name="items['+index+']['+fieldName+']"]');
                                    input.addClass('is-invalid');
                                    input.closest('.form-group').append(
                                        '<small class="error-text text-danger">' + val[0] + '</small>'
                                    );
                                }
                            }
                        });
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });
});
</script>