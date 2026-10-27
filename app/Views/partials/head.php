<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--favicon-->
	<link rel="icon" href="<?= base_url('assets/images/favicon-32x32.png') ?>" type="image/png">
	<!--plugins-->
	<?php if (!empty($plugins_css) && is_array($plugins_css)): ?>
		<?php foreach ($plugins_css as $css): ?>
			<link href="<?= base_url('assets/' . $css) ?>" rel="stylesheet">
		<?php endforeach; ?>
	<?php endif; ?>
	<link href="<?= base_url('assets/plugins/simplebar/css/simplebar.css') ?>" rel="stylesheet">
	<link href="<?= base_url('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') ?>" rel="stylesheet">
	<link href="<?= base_url('assets/plugins/metismenu/css/metisMenu.min.css') ?>" rel="stylesheet">
	<!-- loader-->
	<link href="<?= base_url('assets/css/pace.min.css') ?>" rel="stylesheet">
	<script src="<?= base_url('assets/js/pace.min.js') ?>"></script>
	<!-- Bootstrap CSS -->
	<link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
	<link href="<?= base_url('assets/css/bootstrap-extended.css') ?>" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	<link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
	<link href="<?= base_url('assets/css/icons.css') ?>" rel="stylesheet">
	<!-- Theme Style CSS -->
	<link rel="stylesheet" href="<?= base_url('assets/css/dark-theme.css') ?>">
	<link rel="stylesheet" href="<?= base_url('assets/css/semi-dark.css') ?>">
	<link rel="stylesheet" href="<?= base_url('assets/css/header-colors.css') ?>">
	<!-- SweetAlert2 CSS -->
	<link href="<?= base_url('assets/vendor/sweetalert2/sweetalert2.min.css') ?>" rel="stylesheet">
	<link href="<?= base_url('assets/css/swal2-extra.css') ?>" rel="stylesheet">
	<title><?= esc($title ?? 'Rifas - Panel de Administración') ?></title>
</head>
