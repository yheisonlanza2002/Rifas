<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
	<div class="container">
		<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
			<div class="col mx-auto">
				<div class="card mb-0">
					<div class="card-body">
						<div class="p-4">
							<div class="mb-3 text-center">
								<img src="<?= base_url('assets/images/logo-icon.png') ?>" width="60" alt="">
							</div>
							<div class="text-center mb-4">
								<h5 class="">Rifas Admin</h5>
								<p class="mb-0">Inicia sesión en tu cuenta</p>
							</div>
							<div class="form-body">
								<form class="row g-3" method="post" action="<?= base_url('auth/login') ?>">
									<?= csrf_field() ?>
									<div class="col-12">
										<label for="inputEmailAddress" class="form-label">Email</label>
										<input type="email" class="form-control" id="inputEmailAddress" name="email" placeholder="jhon@example.com" value="<?= old('email') ?>">
									</div>
									<div class="col-12">
										<label for="inputChoosePassword" class="form-label">Contraseña</label>
										<div class="input-group" id="show_hide_password">
											<input type="password" class="form-control border-end-0" id="inputChoosePassword" name="password" placeholder="Tu contraseña"> <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-check form-switch">
											<input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="remember">
											<label class="form-check-label" for="flexSwitchCheckChecked">Recuérdame</label>
										</div>
									</div>
									<div class="col-md-6 text-end">	<a href="<?= base_url('auth/forgot-password') ?>">¿Olvidaste tu contraseña?</a>
									</div>
									<div class="col-12">
										<div class="d-grid">
											<button type="submit" class="btn btn-primary">Iniciar sesión</button>
										</div>
									</div>
									<div class="col-12">
										<div class="text-center ">
											<p class="mb-0">¿No tienes cuenta? <a href="<?= base_url('auth/register') ?>">Regístrate aquí</a>
											</p>
										</div>
									</div>
								</form>
							</div>
							<div class="login-separater text-center mb-5"> <span>O INICIA SESIÓN CON</span>
								<hr>
							</div>
							<div class="list-inline contacts-social text-center">
								<a href="javascript:;" class="list-inline-item bg-facebook text-white border-0 rounded-3"><i class="bx bxl-facebook"></i></a>
								<a href="javascript:;" class="list-inline-item bg-twitter text-white border-0 rounded-3"><i class="bx bxl-twitter"></i></a>
								<a href="javascript:;" class="list-inline-item bg-google text-white border-0 rounded-3"><i class="bx bxl-google"></i></a>
								<a href="javascript:;" class="list-inline-item bg-linkedin text-white border-0 rounded-3"><i class="bx bxl-linkedin"></i></a>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end row-->
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
				$('#show_hide_password i').addClass("bx-hide");
				$('#show_hide_password i').removeClass("bx-show");
			} else if ($('#show_hide_password input').attr("type") == "password") {
				$('#show_hide_password input').attr('type', 'text');
				$('#show_hide_password i').removeClass("bx-hide");
				$('#show_hide_password i').addClass("bx-show");
			}
		});
	});
</script>
<?= $this->endSection() ?>
