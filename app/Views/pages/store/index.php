<?= $this->extend('layouts/store') ?>

<?= $this->section('content') ?>

<!-- HERO BANNER -->
<section class="store-hero text-center">
    <div class="container">
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold mb-3">
            <i class='bx bx-party me-1'></i> ¡Sorteos Transparentes y Verificados!
        </span>
        <h1 class="display-5 fw-bold mb-3">Elige tu número de la suerte y gana</h1>
        <p class="lead mb-0 text-light opacity-75 max-w-700 mx-auto">
            Participa en nuestras rifas oficiales jugando con las principales loterías del país. Compra fácil, rápido y seguro.
        </p>
    </div>
</section>

<!-- GRID DE RIFAS ACTIVAS -->
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class='bx bx-gift text-primary me-2'></i>Rifas Disponibles</h4>
            <p class="text-muted mb-0 small">Selecciona una rifa para ver los premios y escoger tus boletas</p>
        </div>
        <a href="<?= base_url('tienda/consultar') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class='bx bx-search me-1'></i> Consultar mis boletas
        </a>
    </div>

    <?php if (empty($raffles)): ?>
        <div class="card card-raffle text-center py-5">
            <div class="card-body">
                <div class="mb-3">
                    <i class='bx bx-info-circle text-muted' style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-dark">No hay rifas activas en este momento</h5>
                <p class="text-secondary mb-4">Pronto publicaremos nuevos sorteos con increíbles premios. ¡Vuelve a visitarnos!</p>
                <a href="<?= base_url('tienda/consultar') ?>" class="btn btn-primary rounded-pill px-4">
                    <i class='bx bx-search me-1'></i> Consultar compras anteriores
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($raffles as $raffle): ?>
                <?php
                $prize = $raffle['main_prize'] ?? null;
                $stats = $raffle['stats'] ?? ['total' => 0, 'sold' => 0, 'available' => 0, 'percent' => 0];
                $imgFile = !empty($prize['prize_image']) ? $prize['prize_image'] : (!empty($prize['image']) ? $prize['image'] : null);
                $imgUrl = $imgFile ? base_url('uploads/prizes/' . $imgFile) : null;
                ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card card-raffle h-100 d-flex flex-column">
                        <!-- IMAGEN DEL PREMIO -->
                        <div class="position-relative bg-light text-center" style="height: 220px; overflow: hidden;">
                            <?php if ($imgUrl): ?>
                                <img src="<?= esc($imgUrl) ?>" alt="<?= esc($raffle['name']) ?>"
                                     class="w-100 h-100" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted bg-gradient-cosmic text-white">
                                    <i class='bx bx-trophy' style="font-size: 4.5rem;"></i>
                                    <span class="small mt-2 fw-semibold">Gran Premio</span>
                                </div>
                            <?php endif; ?>

                            <!-- BADGE DE PRECIO -->
                            <div class="position-absolute bottom-0 start-0 m-3">
                                <span class="badge bg-dark bg-opacity-75 text-white fs-6 px-3 py-2 rounded-pill shadow-sm">
                                    $ <?= number_format((float) $raffle['ticket_price'], 0, ',', '.') ?> <?= esc($raffle['currency']) ?>
                                </span>
                            </div>

                            <!-- BADGE LOTERÍA -->
                            <?php if (!empty($raffle['lottery'])): ?>
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-warning text-dark fw-bold shadow-sm">
                                        <i class='bx bx-dice-5 me-1'></i> <?= esc($raffle['lottery']['name']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- CUERPO DE LA TARJETA -->
                        <div class="card-body d-flex flex-column flex-grow-1 p-4">
                            <h5 class="fw-bold text-dark mb-2"><?= esc($raffle['name']) ?></h5>

                            <?php if ($prize): ?>
                                <p class="text-primary fw-semibold small mb-2">
                                    <i class='bx bx-award me-1'></i> Premio: <?= esc($prize['name'] ?? $prize['title'] ?? '') ?>
                                    <?php if (!empty($prize['value'])): ?>
                                        <span class="text-muted">(Valor: $ <?= number_format((float) $prize['value'], 0, ',', '.') ?>)</span>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($raffle['description'])): ?>
                                <p class="text-secondary small mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= esc($raffle['description']) ?>
                                </p>
                            <?php endif; ?>

                            <!-- FECHA DEL SORTEO -->
                            <div class="bg-light rounded p-2 mb-3 text-center small text-muted">
                                <i class='bx bx-calendar me-1'></i>
                                Juega: <strong><?= !empty($raffle['end_at']) ? date('d/m/Y', strtotime($raffle['end_at'])) : 'Fecha por definir' ?></strong>
                            </div>

                            <!-- PROGRESO DE BOLETAS -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Progreso de venta</span>
                                    <span class="fw-semibold"><?= $stats['percent'] ?>%</span>
                                </div>
                                <div class="progress" style="height: 7px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $stats['percent'] ?>%"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted mt-1 font-12">
                                    <span><?= $stats['available'] ?> disponibles</span>
                                    <span><?= $stats['sold'] ?> vendidas</span>
                                </div>
                            </div>

                            <!-- BOTÓN DE ACCIÓN -->
                            <a href="<?= base_url('tienda/rifa/' . esc($raffle['code'])) ?>" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                                <i class='bx bx-cart-add me-1'></i> Comprar Boletas
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

