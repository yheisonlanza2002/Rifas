<?php

/**
 * Alert Helper - SweetAlert2 para CodeIgniter 4
 *
 * Proporciona funciones globales para mostrar alertas con SweetAlert2.
 * Uso típico desde controladores:
 *
 *   session()->setFlashdata('success', 'Registro guardado correctamente');
 *   return redirect()->to('/dashboard');
 *
 * Luego en el layout se llama: <?= render_flash_alerts() ?>
 *
 * Uso directo en vistas:
 *   <?= toast_success('Operación exitosa') ?>
 */

if (!function_exists('alert_assets')) {
    /**
     * Devuelve las etiquetas para cargar los assets de SweetAlert2.
     * Colocar una sola vez en el <head> (css) o se usará en scripts.php.
     */
    function alert_assets(bool $cssOnly = false): string
    {
        $css = '<link href="' . base_url('assets/vendor/sweetalert2/sweetalert2.min.css') . '" rel="stylesheet">';
        $js  = '<script src="' . base_url('assets/vendor/sweetalert2/sweetalert2.min.js') . '"></script>';

        return $cssOnly ? $css : $css . PHP_EOL . $js;
    }
}

if (!function_exists('swal')) {
    /**
     * Genera un <script> con Swal.fire() personalizado.
     *
     * @param array $options Opciones de SweetAlert2 (title, text, icon, etc.)
     */
    function swal(array $options): string
    {
        $json = json_encode($options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return '<script>document.addEventListener("DOMContentLoaded",function(){Swal.fire(' . $json . ');});</script>';
    }
}

if (!function_exists('toast')) {
    /**
     * Genera un toast (notificación flotante sin botones).
     *
     * @param string $message Mensaje a mostrar
     * @param string $icon    success|error|warning|info|question
     * @param int    $timer   Milisegundos visible (default 3000)
     * @param string $position top-end|top|top-start|bottom-end...
     */
    function toast(string $message, string $icon = 'success', int $timer = 3000, string $position = 'top-end'): string
    {
        return swal([
            'toast'            => true,
            'position'         => $position,
            'icon'             => $icon,
            'title'            => $message,
            'showConfirmButton' => false,
            'timer'            => $timer,
            'timerProgressBar' => true,
            'customClass'      => [
                'timerProgressBar' => 'swal2-progress-' . $icon,
            ],
        ]);
    }
}

if (!function_exists('toast_success')) {
    function toast_success(string $message, int $timer = 3000): string
    {
        return toast($message, 'success', $timer);
    }
}

if (!function_exists('toast_error')) {
    function toast_error(string $message, int $timer = 4000): string
    {
        return toast($message, 'error', $timer);
    }
}

if (!function_exists('toast_warning')) {
    function toast_warning(string $message, int $timer = 3500): string
    {
        return toast($message, 'warning', $timer);
    }
}

if (!function_exists('toast_info')) {
    function toast_info(string $message, int $timer = 3000): string
    {
        return toast($message, 'info', $timer);
    }
}

if (!function_exists('alert_success')) {
    /** Modal de éxito con botón de confirmación */
    function alert_success(string $title, string $text = '', string $confirmText = 'Aceptar'): string
    {
        return swal([
            'icon'              => 'success',
            'title'             => $title,
            'text'              => $text,
            'confirmButtonText' => $confirmText,
            'confirmButtonColor' => '#0d6efd',
        ]);
    }
}

if (!function_exists('alert_error')) {
    /** Modal de error con botón de confirmación */
    function alert_error(string $title, string $text = '', string $confirmText = 'Aceptar'): string
    {
        return swal([
            'icon'              => 'error',
            'title'             => $title,
            'text'              => $text,
            'confirmButtonText' => $confirmText,
            'confirmButtonColor' => '#dc3545',
        ]);
    }
}

if (!function_exists('alert_info')) {
    /** Modal informativo */
    function alert_info(string $title, string $text = '', string $confirmText = 'Entendido'): string
    {
        return swal([
            'icon'              => 'info',
            'title'             => $title,
            'text'              => $text,
            'confirmButtonText' => $confirmText,
            'confirmButtonColor' => '#0dcaf0',
        ]);
    }
}

if (!function_exists('alert_warning')) {
    /** Modal de advertencia */
    function alert_warning(string $title, string $text = '', string $confirmText = 'Aceptar'): string
    {
        return swal([
            'icon'              => 'warning',
            'title'             => $title,
            'text'              => $text,
            'confirmButtonText' => $confirmText,
            'confirmButtonColor' => '#ffc107',
        ]);
    }
}

if (!function_exists('render_flash_alerts')) {
    /**
     * Renderiza automáticamente los mensajes flash de la sesión como toasts.
     * Compatible con:
     *   session()->setFlashdata('success', '...')
     *   session()->setFlashdata('error', '...')
     *   session()->setFlashdata('warning', '...')
     *   session()->setFlashdata('info', '...')
     *   ->with('success', '...') en redirects
     *
     * Soporta mensaje simple (string) o múltiples (array).
     */
    function render_flash_alerts(): string
    {
        $session  = session();
        $output   = '';
        $mapIcons = [
            'success' => 'success',
            'error'   => 'error',
            'danger'  => 'error',
            'warning' => 'warning',
            'info'    => 'info',
        ];

        foreach ($mapIcons as $key => $icon) {
            $message = $session->getFlashdata($key);
            if ($message === null) {
                continue;
            }

            $messages = is_array($message) ? $message : [$message];
            foreach ($messages as $msg) {
                $output .= toast($msg, $icon);
            }
        }

        return $output;
    }
}

if (!function_exists('swal_confirm_script')) {
    /**
     * Devuelve un bloque <script> que habilita confirmación SweetAlert2
     * en elementos con el atributo [data-confirm].
     *
     * Uso en HTML:
     *   <a href="/eliminar/5" data-confirm="¿Eliminar este registro?">Eliminar</a>
     *   <form data-confirm="¿Seguro?">...</form>
     */
    function swal_confirm_script(): string
    {
        return <<<'HTML'
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Confirmación en enlaces
    document.querySelectorAll('a[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const url = el.getAttribute('href');
            Swal.fire({
                title: el.dataset.confirm || '¿Estás seguro?',
                text: el.dataset.confirmText || 'Esta acción no se puede deshacer',
                icon: el.dataset.confirmIcon || 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: el.dataset.confirmBtn || 'Sí, continuar',
                cancelButtonText: el.dataset.cancelBtn || 'Cancelar'
            }).then(function (result) {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    // Confirmación en formularios
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: form.dataset.confirm || '¿Estás seguro?',
                text: form.dataset.confirmText || '',
                icon: form.dataset.confirmIcon || 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: form.dataset.confirmBtn || 'Sí, continuar',
                cancelButtonText: form.dataset.cancelBtn || 'Cancelar'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
HTML;
    }
}
