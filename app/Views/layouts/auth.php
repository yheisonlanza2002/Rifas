<!doctype html>
<html lang="<?= service('request')->getLocale() ?? 'es' ?>">

<?= view('partials/head', ['title' => $title ?? null]) ?>

<body class="">
	<!--wrapper-->
	<div class="wrapper">
		<?= $this->renderSection('content') ?>
	</div>
	<!--end wrapper-->

	<?= view('partials/scripts', [
		'plugins_js' => $plugins_js ?? [],
		'page_js'    => $page_js ?? null,
	]) ?>
	<?= $this->renderSection('scripts') ?>
</body>

</html>
