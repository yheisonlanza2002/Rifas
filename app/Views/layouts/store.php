<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Tienda de Rifas - Participa y Gana') ?></title>
    <link rel="icon" href="<?= base_url('assets/images/favicon-32x32.png') ?>" type="image/png" />

    <!-- Bootstrap & Plugins CSS -->
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-extended.css') ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/icons.css') ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f7f8fa;
            font-family: 'Roboto', sans-serif;
            color: #333;
        }
        .store-navbar {
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .store-hero {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
            padding: 45px 0 35px 0;
            margin-bottom: 30px;
        }
        .badge-lottery {
            background: #ffecb3;
            color: #b78103;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
        }
        .card-raffle {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
            background: #fff;
        }
        .card-raffle:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        .store-footer {
            background: #19202e;
            color: #cbd5e1;
            padding: 40px 0 20px 0;
            margin-top: 60px;
        }
        .store-footer a {
            color: #94a3b8;
            text-decoration: none;
        }
        .store-footer a:hover {
            color: #fff;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- NAVBAR PÚBLICA -->
    <nav class="store-navbar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <a href="<?= base_url('tienda') ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="<?= base_url('assets/images/logo-icon.png') ?>" alt="Logo" width="38" height="38">
                    <span class="fs-4 fw-bold text-dark tracking-wide">Rifas</span>
                </a>

                <div class="d-flex align-items-center gap-3">
                    <a href="<?= base_url('tienda') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-none d-sm-inline-flex align-items-center gap-1">
                        <i class='bx bx-gift'></i> Rifas Disponibles
                    </a>
                    <a href="<?= base_url('tienda/consultar') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1">
                        <i class='bx bx-search'></i> Mis Boletas
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ALERTAS FLASH -->
    <div class="container mt-3">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class='bx bx-check-circle fs-4'></i>
                <div><?= session()->getFlashdata('success') ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class='bx bx-error-circle fs-4'></i>
                <div><?= session()->getFlashdata('error') ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- FOOTER -->
    <footer class="store-footer">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-md-5">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="<?= base_url('assets/images/logo-icon.png') ?>" alt="Logo" width="32">
                        <span class="fs-5 fw-bold text-white">Rifas</span>
                    </div>
                    <p class="small text-secondary mb-3">
                        Plataforma oficial de venta de boletas y sorteos transparentes. Participa de forma segura desde cualquier lugar con medios de pago digitales en línea y transferencias.
                    </p>
                    <div class="d-flex gap-2">
                        <span class="badge bg-dark border text-light"><i class='bx bx-shield-quarter me-1'></i> Pagos 100% Seguros</span>
                        <span class="badge bg-dark border text-light"><i class='bx bx-check-shield me-1'></i> Loterías Oficiales</span>
                    </div>
                </div>

                <div class="col-md-3 ms-auto">
                    <h6 class="text-white fw-bold mb-3">Enlaces Rápidos</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="<?= base_url('tienda') ?>"><i class='bx bx-chevron-right'></i> Ver Rifas Activas</a></li>
                        <li><a href="<?= base_url('tienda/consultar') ?>"><i class='bx bx-chevron-right'></i> Consultar Mis Boletas</a></li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <h6 class="text-white fw-bold mb-3">Métodos de Pago Aceptados</h6>
                    <p class="small text-secondary mb-2">Paga seguro con Wompi, PSE, tarjetas de crédito, débito, Nequi, Daviplata o transferencia bancaria.</p>
                    <div class="d-flex flex-wrap gap-2 pt-1">
                        <span class="badge bg-light text-dark p-2 fw-semibold"><i class='bx bx-credit-card me-1'></i> Wompi</span>
                        <span class="badge bg-light text-dark p-2 fw-semibold">PSE</span>
                        <span class="badge bg-light text-dark p-2 fw-semibold">Nequi</span>
                        <span class="badge bg-light text-dark p-2 fw-semibold">Bancolombia</span>
                    </div>
                </div>
            </div>

            <hr class="border-secondary opacity-25">

            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between small text-secondary pt-2">
                <span>&copy; <?= date('Y') ?> Rifas. Todos los derechos reservados.</span>
                <span>Juega con responsabilidad. Prohibida la venta a menores de edad.</span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>

