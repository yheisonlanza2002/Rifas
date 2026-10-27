<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Administración</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a>
				</li>
				<li class="breadcrumb-item"><a href="<?= base_url('plantillas-email') ?>">Plantillas de Email</a></li>
				<li class="breadcrumb-item active" aria-current="page">Vista Previa</li>
			</ol>
		</nav>
	</div>
</div>

<div class="card">
	<div class="card-header">
		<div class="d-flex align-items-center">
			<div>
				<h6 class="mb-0">Vista Previa: <?= esc($template['name']) ?></h6>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="mb-3">
			<strong>Asunto:</strong> <?= esc($template['subject']) ?>
		</div>
		<div class="border rounded p-3 bg-light">
			<?= $template['body_html'] ?>
		</div>
	</div>
</div>

<div class="mt-3">
	<a href="<?= base_url('plantillas-email') ?>" class="btn btn-outline-secondary px-4">
		<i class="bx bx-arrow-back"></i> Volver a Plantillas
	</a>
</div>
<?= $this->endSection() ?>
