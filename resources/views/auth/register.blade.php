<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Register Pengguna</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
</head>

<body class="hold-transition register-page">
  <div class="register-box">
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="{{ url('/') }}" class="h1"><b>Admin</b>LTE</a>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Daftar Akun Baru</p>
        <form id="form-register" action="{{ url('/register') }}" method="POST">
            @csrf
        
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
        
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" name="nama" id="nama" class="form-control" required>
            </div>
        
            <div class="mb-3">
                <label for="level_id" class="form-label">Level</label>
                <select name="level_id" id="level_id" class="form-control" required>
                    <option value="">-- Pilih Level --</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->level_id }}">{{ $level->level_nama }}</option>
                    @endforeach
                </select>
            </div>
        
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
        
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>
        
            <button type="submit" class="btn btn-primary">Register</button>
        </form>        
      </div>
    </div>
  </div>

  <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
  <script>
    $(document).ready(function() {
    $("#form-register").validate({
        rules: {
            username: { required: true, minlength: 4, maxlength: 20 },
            nama: { required: true, minlength: 3 },
            password: { required: true, minlength: 6 },
            password_confirmation: { required: true, equalTo: '[name="password"]' }
        },
        submitHandler: function(form) {
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registrasi Berhasil',
                            text: response.message,
                        }).then(function() {
                            window.location.href = "{{ url('/login') }}"; // Redirect ke halaman login
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registrasi Gagal',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors;
                    let errorMessage = "Terjadi kesalahan saat registrasi.";
                    if (errors) {
                        errorMessage = Object.values(errors).join("\n");
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Registrasi Gagal',
                        text: errorMessage
                    });
                }
            });
            return false;
        }
    });
});
</script>
</body>
</html>
