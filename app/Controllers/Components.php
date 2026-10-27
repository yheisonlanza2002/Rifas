<?php

namespace App\Controllers;

class Components extends BaseController
{
    public function alerts(): string
    {
        return view('pages/components/alerts', [
            'title' => 'Alertas SweetAlert2 - Rifas',
        ]);
    }

    /**
     * Recibe el POST del formulario de demo con data-confirm.
     */
    public function alertsPost()
    {
        session()->setFlashdata('success', 'Formulario enviado correctamente (demo).');
        return redirect()->to('componentes/alertas');
    }

    /**
     * Ruta de demo: establece un flash message y redirige a la página de alertas.
     * Uso: /componentes/alertas/demo/success|error|warning|info
     */
    public function alertsDemo(string $type)
    {
        $messages = [
            'success' => 'Registro guardado correctamente.',
            'error'   => 'Ocurrió un error al procesar la solicitud.',
            'warning' => 'Atención: revisa los datos ingresados.',
            'info'    => 'Tienes nuevas notificaciones pendientes.',
        ];

        if (!isset($messages[$type])) {
            return redirect()->to('componentes/alertas');
        }

        session()->setFlashdata($type, $messages[$type]);
        return redirect()->to('componentes/alertas');
    }
}
