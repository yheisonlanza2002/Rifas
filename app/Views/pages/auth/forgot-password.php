<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="wrapper d-flex align-items-center justify-content-center" style="min-height: 100vh;">
	<div class="card forgot-box">
		<div class="card-body">
			<div class="p-3">
				<div class="text-center">
					<img src="<?= base_url('assets/images/icons/forgot-2.png') ?>" width="100" alt="">
				</div>
				<h4 class="mt-5 font-weight-bold">¿Olvidaste tu contraseña?</h4>
				<p class="text-muted">Ingresa tu email registrado para restablecer la contraseña</p>
				<form method="post" action="<?= base_url('auth/forgot-password') ?>">
					<?= csrf_field() ?>
					<div class="my-4">
						<label class="form-label">Email</label>
						<input type="email" name="email" class="form-control" placeholder="example@user.com" required>
					</div>
					<div class="d-grid gap-2">
						<button type="submit" class="btn btn-primary">Enviar</button>
						<a href="<?= base_url('auth/login') ?>" class="btn btn-light"><i class='bx bx-arrow-back me-1'></i>Volver al Login</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- end wrapper -->
<?= $this->endSection() ?>
