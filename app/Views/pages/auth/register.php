<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
	<div class="container">
		<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
			<div class="col mx-auto">
				<div class="card">
					<div class="card-body">
						<div class="border p-4 rounded">
							<div class="text-center">
								<h3 class="">Registro</h3>
								<p>¿Ya tienes cuenta? <a href="<?= base_url('auth/login') ?>">Inicia sesión aquí</a>
								</p>
							</div>
							<div class="d-grid">
								<a class="btn my-4 shadow-sm btn-white" href="javascript:;"> <span class="d-flex justify-content-center align-items-center">
										<img class="me-2" src="<?= base_url('assets/images/icons/search.svg') ?>" width="16" alt="Image Description">
										<span>Registrarse con Google</span>
									</span>
								</a> <a href="javascript:;" class="btn btn-facebook"><i class="bx bxl-facebook"></i>Registrarse con Facebook</a>
							</div>
							<div class="login-separater text-center mb-4"> <span>O REGÍSTRATE CON EMAIL</span>
								<hr>
							</div>
							<div class="form-body">
								<form class="row g-3" method="post" action="<?= base_url('auth/register') ?>">
									<?= csrf_field() ?>
									<div class="col-sm-6">
										<label for="inputFirstName" class="form-label">Nombre</label>
										<input type="text" class="form-control" id="inputFirstName" name="first_name" placeholder="Jhon">
									</div>
									<div class="col-sm-6">
										<label for="inputLastName" class="form-label">Apellido</label>
										<input type="text" class="form-control" id="inputLastName" name="last_name" placeholder="Deo">
									</div>
									<div class="col-12">
										<label for="inputEmailAddress" class="form-label">Email</label>
										<input type="email" class="form-control" id="inputEmailAddress" name="email" placeholder="example@user.com">
									</div>
									<div class="col-12">
										<label for="inputChoosePassword" class="form-label">Contraseña</label>
										<div class="input-group" id="show_hide_password">
											<input type="password" class="form-control border-end-0" id="inputChoosePassword" name="password" placeholder="Ingresa tu contraseña"> <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
										</div>
									</div>
									<div class="col-12">
										<div class="form-check form-switch">
											<input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked">
											<label class="form-check-label" for="flexSwitchCheckChecked">He leído y acepto los Términos y Condiciones</label>
										</div>
									</div>
									<div class="col-12">
										<div class="d-grid">
											<button type="submit" class="btn btn-primary"><i class='bx bx-user'></i>Registrarse</button>
										</div>
									</div>
								</form>
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
				$('#show_hide_password i').addClass("bx-hide").removeClass("bx-show");
			} else if ($('#show_hide_password input').attr("type") == "password") {
				$('#show_hide_password input').attr('type', 'text');
				$('#show_hide_password i').removeClass("bx-hide").addClass("bx-show");
			}
		});
	});
</script>
<?= $this->endSection() ?>
