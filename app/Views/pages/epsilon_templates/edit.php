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
				<li class="breadcrumb-item active" aria-current="page">Editar</li>
			</ol>
		</nav>
	</div>
</div>

<div class="card">
	<div class="card-header">
		<div class="d-flex align-items-center">
			<div>
				<h6 class="mb-0">Editar Plantilla</h6>
			</div>
		</div>
	</div>
	<div class="card-body">
		<?= $this->include('pages/email_templates/_form') ?>
	</div>
</div>
<?= $this->endSection() ?>
