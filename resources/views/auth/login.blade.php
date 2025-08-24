<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - A101 Stok Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .logo {
            font-size: 2.5rem;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="card login-card border-0">
                        <div class="card-body p-5">
                            <!-- Logo ve Başlık -->
                            <div class="text-center mb-4">
                                <div class="logo mb-3">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <h3 class="fw-bold text-dark">A101 Stok Sistemi</h3>
                                <p class="text-muted">Mağaza girişi yapın</p>
                            </div>

                            <!-- Login Form -->
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                
                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope"></i> E-posta Adresi
                                    </label>
                                    <input type="email" name="email" id="email" 
                                           class="form-control @if(isset($errors) && $errors->has('email')) is-invalid @endif" 
                                           value="{{ old('email') }}" 
                                           placeholder="mağaza@a101.com" 
                                           required 
                                           autofocus>
                                    @if(isset($errors) && $errors->has('email'))
                                        <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>

                                <!-- Şifre -->
                                <div class="mb-4">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock"></i> Şifre
                                    </label>
                                    <input type="password" name="password" id="password" 
                                           class="form-control @if(isset($errors) && $errors->has('password')) is-invalid @endif" 
                                           placeholder="••••••••" 
                                           required>
                                    @if(isset($errors) && $errors->has('password'))
                                        <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>

                                <!-- Giriş Butonu -->
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-box-arrow-in-right"></i> Giriş Yap
                                    </button>
                                </div>

                                <!-- Demo Bilgileri -->
                                <div class="text-center">
                                    <small class="text-muted">
                                        <strong>Demo Giriş:</strong><br>
                                        Kayseri Ana Mağaza: kayseri@a101.com / 123456<br>
                                        Hürriyet Şubesi: hurriyet@a101.com / 123456
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
