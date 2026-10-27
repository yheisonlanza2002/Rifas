	<!-- Bootstrap JS -->
	<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
	<!--plugins-->
	<script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
	<script src="<?= base_url('assets/plugins/simplebar/js/simplebar.min.js') ?>"></script>
	<script src="<?= base_url('assets/plugins/metismenu/js/metisMenu.min.js') ?>"></script>
	<script src="<?= base_url('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') ?>"></script>
	<?php if (!empty($plugins_js) && is_array($plugins_js)): ?>
		<?php foreach ($plugins_js as $js): ?>
			<script src="<?= base_url('assets/' . $js) ?>"></script>
		<?php endforeach; ?>
	<?php endif; ?>
	<!--app JS-->
	<script src="<?= base_url('assets/js/app.js') ?>"></script>
	<!-- SweetAlert2 JS local -->
	<script src="<?= base_url('assets/vendor/sweetalert2/sweetalert2.min.js') ?>"></script>
	<!-- Formato de números global -->
	<script src="<?= base_url('assets/js/format-number.js') ?>"></script>
	<script>
		// ============================================================
		// Helper global JS de alertas (SweetAlert2)
		// Uso: showToast('Mensaje', 'success') / showAlert({...}) / confirmAction(...)
		// ============================================================

		/** Toast notification (flotante, auto-cierra) — barra de progreso coloreada por tipo */
		function showToast(message, icon = 'success', timer = 3000) {
			Swal.fire({
				toast: true,
				position: 'top-end',
				icon: icon,
				title: message,
				showConfirmButton: false,
				timer: timer,
				timerProgressBar: true,
				customClass: {
					timerProgressBar: 'swal2-progress-' + icon
				}
			});
		}

		/** Alerta modal genérica */
		function showAlert(options) {
			return Swal.fire(Object.assign({
				confirmButtonColor: '#0d6efd'
			}, options));
		}

		/**
		 * Confirmación con callback.
		 * confirmAction('¿Eliminar?', 'No podrás revertir', function(){ ... });
		 */
		function confirmAction(title, text, onConfirm, options = {}) {
			Swal.fire(Object.assign({
				title: title,
				text: text,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#dc3545',
				cancelButtonColor: '#6c757d',
				confirmButtonText: 'Sí, continuar',
				cancelButtonText: 'Cancelar'
			}, options)).then((result) => {
				if (result.isConfirmed && typeof onConfirm === 'function') {
					onConfirm();
				}
			});
		}

		/** Alerta de éxito rápida */
		function alertSuccess(title, text = '') {
			return Swal.fire({ icon: 'success', title: title, text: text, confirmButtonColor: '#0d6efd' });
		}

		/** Alerta de error rápida */
		function alertError(title, text = '') {
			return Swal.fire({ icon: 'error', title: title, text: text, confirmButtonColor: '#dc3545' });
		}

		/** Loading con promesa fetch (AJAX) */
		function alertAjax(options) {
			return Swal.fire(Object.assign({
				title: 'Procesando...',
				showConfirmButton: false,
				allowOutsideClick: false,
				didOpen: () => { Swal.showLoading(); }
			}, options));
		}
	</script>
	<?= swal_confirm_script() ?>
	<?= render_flash_alerts() ?>
	<?php if (!empty($page_js)): ?>
		<script src="<?= base_url('assets/js/' . $page_js) ?>"></script>
	<?php endif; ?>
