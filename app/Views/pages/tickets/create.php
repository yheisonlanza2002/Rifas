<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Administración</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a>
				</li>
				<li class="breadcrumb-item"><a href="<?= base_url('boletas') ?>">Boletas</a></li>
				<li class="breadcrumb-item active" aria-current="page">Crear</li>
			</ol>
		</nav>
	</div>
</div>

<div class="card">
	<div class="card-header">
		<div class="d-flex align-items-center">
			<div>
				<h6 class="mb-0">Crear Nueva Boleta</h6>
			</div>
		</div>
	</div>
	<div class="card-body">
		<?= view('pages/tickets/_form', [
			'action'   => base_url('boletas'),
			'ticket'   => null,
			'raffles'  => $raffles,
			'statuses' => $statuses,
			'errors'   => $errors ?? null,
			'isEdit'   => false,
		]) ?>
	</div>
</div>
<?= $this->endSection() ?>
