<!doctype html>
<html lang="<?= service('request')->getLocale() ?? 'es' ?>">

<?= view('partials/head', ['title' => $title ?? null]) ?>

<body>
	<!--wrapper-->
	<div class="wrapper">
		<?= $this->renderSection('content') ?>
	</div>
	<!--end wrapper-->

	<?= view('partials/scripts') ?>
</body>

</html>
