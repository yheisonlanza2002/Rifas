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
				<li class="breadcrumb-item"><a href="<?= base_url('usuarios') ?>">Usuarios</a></li>
				<li class="breadcrumb-item active" aria-current="page">Editar</li>
			</ol>
		</nav>
	</div>
</div>
<!--end breadcrumb-->

<div class="row">
	<div class="col-xl-10 mx-auto">
		<h6 class="mb-0 text-uppercase">Editar Usuario: <?= esc($user['name'] . ' ' . ($user['last_name'] ?? '')) ?></h6>
		<hr>
		<div class="card">
			<div class="card-body">
				<?= view('pages/users/_form', [
					'action'    => base_url('usuarios/' . $user['id']),
					'user'      => $user,
					'roles'     => $roles,
					'userRoles' => $userRoles,
					'errors'    => session()->getFlashdata('errors'),
					'isEdit'    => true,
				]) ?>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
	$(document).ready(function () {
		$("#show_hide_password a").on('click', function (event) {
			event.preventDefault();
			if ($('#show_hide_password input').attr("type") == "text") {
				$('#show_hide_password input').attr('type', 'password');
				$('#show_hide_password i').addClass("bx-hide").removeClass("bx-show");
			} else {
				$('#show_hide_password input').attr('type', 'text');
				$('#show_hide_password i').removeClass("bx-hide").addClass("bx-show");
			}
		});
	});
</script>
<?= $this->endSection() ?>
