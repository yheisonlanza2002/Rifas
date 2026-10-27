<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="wrapper d-flex align-items-center justify-content-center" style="min-height: 100vh;">
 	<div class="container">
		<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
			<div class="col mx-auto">
				<div class="card">
					<div class="card-body">
						<div class="p-4">
							<div class="mb-4 text-center">
								<img src="<?= base_url('assets/images/logo-icon.png') ?>" width="60" alt="">
							</div>
							<div class="text-start mb-4">
								<h5 class="">Generar Nueva Contraseña</h5>
								<p class="mb-0">Recibimos tu solicitud. ¡Ingresa tu nueva contraseña!</p>
							</div>
							<form method="post" action="<?= base_url('auth/reset-password') ?>">
								<?= csrf_field() ?>
								<div class="mb-3 mt-4">
									<label class="form-label">Nueva Contraseña</label>
									<input type="password" name="password" class="form-control" placeholder="Ingresa la nueva contraseña" required>
								</div>
								<div class="mb-4">
									<label class="form-label">Confirmar Contraseña</label>
									<input type="password" name="password_confirm" class="form-control" placeholder="Confirma la contraseña" required>
								</div>
								<div class="d-grid gap-2">
									<button type="submit" class="btn btn-primary">Cambiar Contraseña</button> <a href="<?= base_url('auth/login') ?>" class="btn btn-light"><i class='bx bx-arrow-back mr-1'></i>Volver al Login</a>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
  	</div>
</div>
<!-- end wrapper -->
<?= $this->endSection() ?>
