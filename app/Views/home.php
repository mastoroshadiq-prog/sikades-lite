<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: white;
        }
        
        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            animation: fadeInUp 0.8s ease;
        }
        
        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.95;
            animation: fadeInUp 1s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 15px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            animation: fadeIn 1.2s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items:center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        
        .btn-hero {
            background: white;
            color: #667eea;
            padding: 15px 40px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            animation: fadeInUp 1.2s ease;
        }
        
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            color: #764ba2;
        }
        
        .stats-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-top: 50px;
        }
        
        .stat-item {
            text-align: center;
            padding: 20px;
        }
        
        .stat-item h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <div class="mb-4">
                            <i class="fas fa-landmark fa-3x floating"></i>
                        </div>
                        <h1>SiKaDes</h1>
                        <p>Sistem Informasi Kawal Desa Berbasis Web yang Modern, Transparan, dan Akuntabel</p>
                        <a href="<?= base_url('/login') ?>" class="btn btn-hero">
                            <i class="fas fa-sign-in-alt me-2"></i>Login ke Sistem
                        </a>
                        
                        <div class="stats-section">
                            <div class="row">
                                <div class="col-4 stat-item">
                                    <h3><i class="fas fa-check-circle"></i></h3>
                                    <p class="mb-0">Compliant</p>
                                    <small>Permendagri 20/2018</small>
                                </div>
                                <div class="col-4 stat-item">
                                    <h3><i class="fas fa-shield-alt"></i></h3>
                                    <p class="mb-0">Secure</p>
                                    <small>Role-Based Access</small>
                                </div>
                                <div class="col-4 stat-item">
                                    <h3><i class="fas fa-bolt"></i></h3>
                                    <p class="mb-0">Fast</p>
                                    <small>Modern Technology</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Penganggaran</h5>
                                <p class="text-muted mb-0">Kelola APBDes dengan mudah dan terstruktur sesuai standar</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card" style="animation-delay: 0.2s;">
                                <div class="feature-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Buku Kas Umum</h5>
                                <p class="text-muted mb-0">Catat transaksi keuangan real-time dengan akurat</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card" style="animation-delay: 0.4s;">
                                <div class="feature-icon">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Manajemen SPP</h5>
                                <p class="text-muted mb-0">Proses permintaan pembayaran dengan workflow approval</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card" style="animation-delay: 0.6s;">
                                <div class="feature-icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Pelaporan</h5>
                                <p class="text-muted mb-0">Generate laporan keuangan lengkap dalam format PDF</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
