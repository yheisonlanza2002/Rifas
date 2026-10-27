<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Administración</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a>
				</li>
				<li class="breadcrumb-item"><a href="<?= base_url('participantes') ?>">Participantes</a></li>
				<li class="breadcrumb-item active" aria-current="page">Editar</li>
			</ol>
		</nav>
	</div>
</div>
<!--end breadcrumb-->

<div class="row">
	<div class="col-xl-10 mx-auto">
		<h6 class="mb-0 text-uppercase">Editar Participante: <?= esc(trim($participant['name'] . ' ' . ($participant['last_name'] ?? ''))) ?></h6>
		<hr>
		<div class="card">
			<div class="card-body">
				<?= view('pages/participants/_form', [
					'action'        => base_url('participantes/' . $participant['id']),
					'participant'   => $participant,
					'documentTypes' => $documentTypes,
					'errors'        => session()->getFlashdata('errors'),
					'isEdit'        => true,
				]) ?>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
