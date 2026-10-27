<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Componentes</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="bx bx-home-alt"></i></a>
				</li>
				<li class="breadcrumb-item active" aria-current="page">Alertas SweetAlert2</li>
			</ol>
		</nav>
	</div>
</div>
<!--end breadcrumb-->

<h6 class="mb-0 text-uppercase">Toasts (notificaciones flotantes)</h6>
<hr>
<div class="card">
	<div class="card-body">
		<p class="mb-3">Notificaciones en la esquina que se cierran solas. Usando <code>showToast()</code> del helper JS.</p>
		<div class="row row-cols-auto g-3">
			<div class="col">
				<button type="button" class="btn btn-success px-5" onclick="showToast('Operación exitosa', 'success')"><i class="bx bx-check-circle mr-1"></i> Success</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-danger px-5" onclick="showToast('Ocurrió un error', 'error')"><i class="bx bx-x-circle mr-1"></i> Error</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-warning px-5" onclick="showToast('Cuidado con esto', 'warning')"><i class="bx bx-error mr-1"></i> Warning</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-info px-5" onclick="showToast('Dato informativo', 'info')"><i class="bx bx-info-circle mr-1"></i> Info</button>
			</div>
		</div>
	</div>
</div>

<h6 class="mb-0 text-uppercase">Posiciones del Toast</h6>
<hr>
<div class="card">
	<div class="card-body">
		<div class="row row-cols-auto g-3">
			<div class="col">
				<button type="button" class="btn btn-dark px-4" onclick="toastPos('top-start')">Top Start</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-dark px-4" onclick="toastPos('top')">Top</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-dark px-4" onclick="toastPos('top-end')">Top End</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-dark px-4" onclick="toastPos('bottom-start')">Bottom Start</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-dark px-4" onclick="toastPos('bottom')">Bottom</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-dark px-4" onclick="toastPos('bottom-end')">Bottom End</button>
			</div>
		</div>
	</div>
</div>

<h6 class="mb-0 text-uppercase">Alertas Modales</h6>
<hr>
<div class="card">
	<div class="card-body">
		<div class="row row-cols-auto g-3">
			<div class="col">
				<button type="button" class="btn btn-success px-5" onclick="alertSuccess('¡Bien hecho!', 'El registro se guardó correctamente.')"> Success</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-danger px-5" onclick="alertError('Error', 'No se pudo completar la operación.')"> Error</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-warning px-5" onclick="showAlert({icon: 'warning', title: 'Advertencia', text: 'Revisa los datos antes de continuar.'})"> Warning</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-info px-5" onclick="showAlert({icon: 'info', title: 'Información', text: 'Nueva versión disponible.'})"> Info</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-secondary px-5" onclick="showAlert({icon: 'question', title: '¿Duda?', text: '¿Deseas continuar con el proceso?', showCancelButton: true})"> Question</button>
			</div>
		</div>
	</div>
</div>

<h6 class="mb-0 text-uppercase">Confirmaciones (data-confirm)</h6>
<hr>
<div class="card">
	<div class="card-body">
		<p class="mb-3">Solo agrega <code>data-confirm="mensaje"</code> a un enlace o formulario y el helper <code>swal_confirm_script()</code> hace el resto.</p>
		<div class="row row-cols-auto g-3 align-items-center">
			<div class="col">
				<a href="<?= base_url('componentes/alertas') ?>" class="btn btn-danger px-4" data-confirm="¿Eliminar esta rifa?" data-confirm-text="Esta acción no se puede deshacer" data-confirm-btn="Sí, eliminar">
					<i class="bx bx-trash"></i> Eliminar (enlace)
				</a>
			</div>
			<div class="col">
				<form method="post" action="<?= base_url('componentes/alertas') ?>" data-confirm="¿Guardar los cambios?" class="d-inline">
					<?= csrf_field() ?>
					<button type="submit" class="btn btn-primary px-4"><i class="bx bx-save"></i> Guardar (formulario)</button>
				</form>
			</div>
		</div>
	</div>
</div>

<h6 class="mb-0 text-uppercase">Confirmación con Callback JS</h6>
<hr>
<div class="card">
	<div class="card-body">
		<div class="row row-cols-auto g-3">
			<div class="col">
				<button type="button" class="btn btn-danger px-5" onclick="demoConfirmCallback()"> Eliminar con callback</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-primary px-5" onclick="demoAjax()"> Simular AJAX (loading)</button>
			</div>
			<div class="col">
				<button type="button" class="btn btn-info px-5" onclick="demoInput()"> Con input</button>
			</div>
		</div>
	</div>
</div>

<h6 class="mb-0 text-uppercase">Flashdata desde el servidor (PHP)</h6>
<hr>
<div class="card">
	<div class="card-body">
		<p class="mb-3">Botones que llaman a rutas que usan <code>session()->setFlashdata()</code> y redirigen aquí. El layout las muestra como toasts con <code>render_flash_alerts()</code>.</p>
		<div class="row row-cols-auto g-3">
			<div class="col">
				<a href="<?= base_url('componentes/alertas/demo/success') ?>" class="btn btn-success px-4">Flash Success</a>
			</div>
			<div class="col">
				<a href="<?= base_url('componentes/alertas/demo/error') ?>" class="btn btn-danger px-4">Flash Error</a>
			</div>
			<div class="col">
				<a href="<?= base_url('componentes/alertas/demo/warning') ?>" class="btn btn-warning px-4">Flash Warning</a>
			</div>
			<div class="col">
				<a href="<?= base_url('componentes/alertas/demo/info') ?>" class="btn btn-info px-4">Flash Info</a>
			</div>
		</div>
	</div>
</div>

<h6 class="mb-0 text-uppercase">Referencia rápida PHP (alert_helper)</h6>
<hr>
<div class="card">
	<div class="card-body">
		<pre class="bg-light p-3 rounded mb-0"><code>// En el controlador:
session()->setFlashdata('success', 'Guardado correctamente');
return redirect()->to('/ruta');

// En cualquier vista (echo directo):
&lt;?= toast_success('Hola') ?&gt;
&lt;?= toast_error('Falló') ?&gt;
&lt;?= alert_success('Título', 'Texto') ?&gt;
&lt;?= alert_error('Error', 'Detalle') ?&gt;
&lt;?= swal(['icon' => 'info', 'title' => 'Custom']) ?&gt;

// En JS (ya disponible globalmente):
showToast('Mensaje', 'success');
alertSuccess('Guardado');
confirmAction('¿Eliminar?', 'texto', () => { /* acción */ });</code></pre>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
	function toastPos(position) {
		Swal.fire({
			toast: true,
			position: position,
			icon: 'success',
			title: 'Posición: ' + position,
			showConfirmButton: false,
			timer: 2500,
			timerProgressBar: true
		});
	}

	function demoConfirmCallback() {
		confirmAction('¿Eliminar registro?', 'No podrás revertir esto.', function () {
			alertSuccess('Eliminado', 'El registro fue eliminado.');
		});
	}

	function demoAjax() {
		alertAjax({ title: 'Procesando pago...', text: 'Por favor espera' });
		// Simula una petición
		setTimeout(function () {
			Swal.fire({
				icon: 'success',
				title: '¡Pago completado!',
				text: 'La transacción se procesó correctamente.',
				confirmButtonColor: '#0d6efd'
			});
		}, 2000);
	}

	function demoInput() {
		Swal.fire({
			title: '¿Cuántos boletos?',
			input: 'number',
			inputLabel: 'Cantidad',
			inputPlaceholder: 'Ingresa la cantidad',
			inputAttributes: { min: 1, max: 100 },
			showCancelButton: true,
			confirmButtonText: 'Comprar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#0d6efd'
		}).then((result) => {
			if (result.isConfirmed) {
				showToast('Boletos: ' + result.value, 'success');
			}
		});
	}
</script>
<?= $this->endSection() ?>
