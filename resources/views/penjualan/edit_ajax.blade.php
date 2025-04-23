@if(isset($penjualan))
<form id="form-edit-penjualan" action="{{ url('/penjualan/' . $penjualan->penjualan_id . '/update_ajax') }}" method="POST">
  @csrf
  @method('PUT')
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Edit Penjualan #{{ $penjualan->penjualan_kode }}</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      
      <div class="modal-body">
        {{-- Header Penjualan --}}
        <div class="form-row mb-3">
          <div class="col-md-4">
            <label>Tanggal</label>
            <input type="datetime-local" name="penjualan_tanggal" class="form-control" 
                   value="{{ $penjualan->penjualan_tanggal->format('Y-m-d\TH:i') }}" required>
            <small class="error-text text-danger" id="error-penjualan_tanggal"></small>
          </div>
          <div class="col-md-4">
            <label>Pembeli</label>
            <input type="text" name="pembeli" class="form-control" maxlength="50"
                   value="{{ $penjualan->pembeli }}" required>
            <small class="error-text text-danger" id="error-pembeli"></small>
          </div>
          <div class="col-md-4">
            <label>Kasir</label>
            <select name="user_id" class="form-control" required>
              <option value="">- Pilih Kasir -</option>
              @foreach($user as $u)
                <option value="{{ $u->user_id }}"
                  {{ $u->user_id == $penjualan->user_id ? 'selected' : '' }}>
                  {{ $u->nama }}
                </option>
              @endforeach
            </select>
            <small class="error-text text-danger" id="error-user_id"></small>
          </div>
        </div>
        
        {{-- Item Penjualan --}}
        <div class="card mb-3">
          <div class="card-header bg-light">
            <h6 class="mb-0">Detail Item</h6>
          </div>
          <div class="card-body">
            <div id="items-container">
              @foreach($penjualan->details as $i => $detail)
              <div class="item-row row mb-2">
                <div class="col-md-5">
                  <select name="items[{{ $i }}][barang_id]" class="form-control select-barang" required>
                    <option value="">- Pilih Barang -</option>
                    @foreach($barang as $item)
                      <option value="{{ $item->barang_id }}"
                        data-harga="{{ $item->harga_jual }}"
                        {{ $item->barang_id == $detail->barang_id ? 'selected' : '' }}>
                        {{ $item->barang_nama }} (Rp {{ number_format($item->harga_jual, 0, ',', '.') }})
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2">
                  <input type="number" name="items[{{ $i }}][harga]" class="form-control harga"
                         value="{{ $detail->harga }}" min="1" required>
                </div>
                <div class="col-md-2">
                  <input type="number" name="items[{{ $i }}][jumlah]" class="form-control jumlah"
                         value="{{ $detail->jumlah }}" min="1" required>
                </div>
                <div class="col-md-2">
                  <input type="text" class="form-control subtotal" readonly
                         value="Rp {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}">
                </div>
                <div class="col-md-1">
                  <button type="button" class="btn btn-danger btn-remove-item" {{ $i==0 ? 'disabled' : '' }}>
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              @endforeach
            </div>
            <button type="button" id="btn-add-item" class="btn btn-sm btn-primary mt-2">
              <i class="fas fa-plus"></i> Tambah Item
            </button>
          </div>
        </div>
        
        {{-- Total --}}
        <div class="form-group row">
          <label class="col-md-3 col-form-label font-weight-bold">Total</label>
          <div class="col-md-9">
            <input type="text" id="total-penjualan" class="form-control-plaintext font-weight-bold text-primary"
                   value="Rp 0" readonly style="font-size:1.2rem;">
          </div>
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </div>
  </div>
</form>

<script>
$(function () {
  function initItemRow(row) {
    row.find('.select-barang').change(function () {
      var harga = $(this).find(':selected').data('harga') || 0;
      row.find('.harga').val(harga).trigger('input');
    });
    row.find('.harga, .jumlah').on('input', function () {
      var h = parseFloat(row.find('.harga').val()) || 0;
      var q = parseInt(row.find('.jumlah').val()) || 0;
      var sub = h * q;
      row.find('.subtotal').val('Rp ' + sub.toLocaleString('id-ID'));
      calcTotal();
    });
    row.find('.btn-remove-item').click(function () {
      if ($('#items-container .item-row').length > 1) {
        row.remove();
        calcTotal();
      }
    });
  }

  function calcTotal() {
    var tot = 0;
    $('#items-container .item-row').each(function () {
      var v = $(this).find('.subtotal').val().replace(/\D/g, '');
      tot += parseInt(v) || 0;
    });
    $('#total-penjualan').val('Rp ' + tot.toLocaleString('id-ID'));
  }

  $('#items-container .item-row').each(function () {
    initItemRow($(this));
  });
  calcTotal();

  $('#btn-add-item').click(function () {
    var cnt = $('#items-container .item-row').length;
    var clone = $('#items-container .item-row').first().clone();
    clone.find('select').val('');
    clone.find('.harga,.jumlah').val('');
    clone.find('.subtotal').val('Rp 0');
    clone.find('.btn-remove-item').prop('disabled', false);
    clone.find('[name]').each(function () {
      var name = $(this).attr('name').replace(/\[\d+\]/, '[' + cnt + ']');
      $(this).attr('name', name);
    });
    $('#items-container').append(clone);
    initItemRow(clone);
  });

  $('#form-edit-penjualan').validate({
    rules: {
      penjualan_tanggal: { required: true },
      pembeli: { required: true, maxlength: 50 },
      user_id: { required: true },
    },
    submitHandler: function (form) {
      var formData = new FormData(form);
      var items = [];
      $('#items-container .item-row').each(function () {
        var b = $(this).find('[name$="[barang_id]"]').val();
        var h = $(this).find('[name$="[harga]"]').val();
        var j = $(this).find('[name$="[jumlah]"]').val();
        if (b) items.push({ barang_id: b, harga: h, jumlah: j });
      });
      if (items.length === 0) {
        Swal.fire('Gagal', 'Minimal 1 item harus diisi', 'warning');
        return false;
      }
      formData.set('items', JSON.stringify(items));

      $.ajax({
        url: form.action,
        type: form.method,
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
          if (res.status) {
            $('#main-modal').modal('hide');
            Swal.fire('Berhasil', res.message, 'success');
            if (typeof tablePenjualan !== 'undefined') tablePenjualan.ajax.reload(null, false);
          } else {
            $.each(res.errors || res.msgField || {}, function (k, v) {
              $('#error-' + k).text(v[0] || v);
            });
            Swal.fire('Gagal', 'Validasi gagal', 'error');
          }
        },
        error: function () {
          Swal.fire('Error', 'Terjadi kesalahan', 'error');
        }
      });
      return false;
    }
  });
});
</script>
@else
<div class="alert alert-danger">Data penjualan tidak ditemukan.</div>
@endif
