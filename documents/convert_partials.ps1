# Script de conversión: extrae secciones de rocker-1/index.html a partials PHP
# Ejecutar una sola vez. Uso: powershell -File documents/convert_partials.ps1

$source = Get-Content -Path "rocker-1/index.html" -Raw -Encoding UTF8

function Extract-Section {
    param(
        [string]$Content,
        [string]$StartMarker,
        [string]$EndMarker
    )
    $startIdx = $Content.IndexOf($StartMarker)
    if ($startIdx -lt 0) { throw "No se encontró: $StartMarker" }
    $endIdx = $Content.IndexOf($EndMarker, $startIdx)
    if ($endIdx -lt 0) { throw "No se encontró: $EndMarker" }
    $endIdx += $EndMarker.Length
    return $Content.Substring($startIdx, $endIdx - $startIdx)
}

function Convert-Paths {
    param([string]$Html)
    # Convierte href="assets/... y src="assets/... a base_url()
    $Html = $Html -replace 'href="assets/', 'href="<?= base_url(''assets/'
    $Html = $Html -replace 'src="assets/', 'src="<?= base_url(''assets/'
    # Cierra comillas: .css" -> .css') ?>" etc.
    $Html = $Html -replace '(<\?= base_url\(''assets/[^"'']+\.(css|js|png|jpg|jpeg|gif|svg|ico|woff2?|ttf|eot))"', "`$1') ?>`""
    return $Html
}

# 1. SIDEBAR
$sidebar = Extract-Section -Content $source -StartMarker '<div class="sidebar-wrapper"' -EndMarker '<!--end sidebar wrapper -->'
$sidebar = Convert-Paths -Html $sidebar
Set-Content -Path "app/Views/partials/sidebar.php" -Value $sidebar -Encoding UTF8
Write-Output "OK: partials/sidebar.php ($($sidebar.Length) chars)"

# 2. HEADER
$header = Extract-Section -Content $source -StartMarker '<!--start header -->' -EndMarker '<!--end header -->'
$header = Convert-Paths -Html $header
Set-Content -Path "app/Views/partials/header.php" -Value $header -Encoding UTF8
Write-Output "OK: partials/header.php ($($header.Length) chars)"

# 3. FOOTER (con año dinámico y copyright corregido)
$footer = Extract-Section -Content $source -StartMarker '<footer class="page-footer">' -EndMarker '</footer>'
$footer = $footer -replace 'Copyright . \d{4}\. All right reserved\.', 'Copyright &copy; <?= date(''Y'') ?>. All right reserved.'
Set-Content -Path "app/Views/partials/footer.php" -Value $footer -Encoding UTF8
Write-Output "OK: partials/footer.php ($($footer.Length) chars)"

# 4. SEARCH MODAL (opcional, parte del header UX)
$searchModal = Extract-Section -Content $source -StartMarker '<!-- search modal -->' -EndMarker '<!-- end search modal -->'
Set-Content -Path "app/Views/partials/search_modal.php" -Value $searchModal -Encoding UTF8
Write-Output "OK: partials/search_modal.php ($($searchModal.Length) chars)"

# 5. THEME SWITCHER
$switcher = Extract-Section -Content $source -StartMarker '<!--start switcher-->' -EndMarker '<!--end switcher-->'
Set-Content -Path "app/Views/partials/switcher.php" -Value $switcher -Encoding UTF8
Write-Output "OK: partials/switcher.php ($($switcher.Length) chars)"

# 6. DASHBOARD (index2.html - contenido interno de page-content)
$source2 = Get-Content -Path "rocker-1/index2.html" -Raw -Encoding UTF8
$dashContent = Extract-Section -Content $source2 -StartMarker '<div class="page-content">' -EndMarker '<!--end page wrapper -->'
# Quita el tag de apertura <div class="page-content"> (ya existe en el layout)
$dashContent = $dashContent -replace '^\s*<div class="page-content">', ''
# Quita los cierres </div> de page-content y page-wrapper + el comentario final
$dashContent = $dashContent -replace '</div>\s*</div>\s*<!--end page wrapper -->', ''
$dashContent = Convert-Paths -Html $dashContent
$dashPhp = @"
<?= `$this->extend('layouts/main') ?>

<?= `$this->section('content') ?>
$dashContent
<?= `$this->endSection() ?>
"@
Set-Content -Path "app/Views/pages/dashboard/index.php" -Value $dashPhp -Encoding UTF8
Write-Output "OK: pages/dashboard/index.php ($($dashPhp.Length) chars)"

Write-Output "Conversión completada."
