<!doctype html>
<html lang="<?= service('request')->getLocale() ?? 'es' ?>">

 <?= view('partials/head', ['title' => $title ?? null, 'plugins_css' => $plugins_css ?? []]) ?>
 <?= $this->renderSection('styles') ?>

<body>
	<!--wrapper-->
	<div class="wrapper">
		<!--sidebar wrapper -->
		<?= view('partials/sidebar') ?>
		<!--end sidebar wrapper -->

		<!--start header -->
		<?= view('partials/header') ?>
		<!--end header -->

		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<?= $this->renderSection('content') ?>
			</div>
		</div>
		<!--end page wrapper -->

		<!--start overlay-->
		<div class="overlay toggle-icon"></div>
		<!--end overlay-->

		<!--Start Back To Top Button-->
		<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
		<!--End Back To Top Button-->

		<?= view('partials/footer') ?>
	</div>
	<!--end wrapper-->

	<?= view('partials/search_modal') ?>

	<?= view('partials/switcher') ?>

	<?= view('partials/scripts', [
		'plugins_js' => $plugins_js ?? [],
		'page_js'    => $page_js ?? null,
	]) ?>
	<?= $this->renderSection('scripts') ?>
</body>

</html>
