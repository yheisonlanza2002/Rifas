<?php

namespace App\Controllers;

use App\Models\PaymentGatewayModel;

class PaymentGateways extends BaseController
{
    protected PaymentGatewayModel $gatewayModel;

    private const STATUSES = [
        'ACTIVE'   => 'Activo',
        'INACTIVE' => 'Inactivo',
    ];

    private const DRIVERS = [
        'wompi'        => 'Wompi (Colombia)',
        'mercado_pago' => 'Mercado Pago',
        'payu'         => 'PayU',
        'manual'       => 'Manual',
    ];

    public function __construct()
    {
        $this->gatewayModel = new PaymentGatewayModel();
    }

    public function index(): string
    {
        $companyId = (int) session()->get('company_id');

        $gateways = $this->gatewayModel
            ->forCompany($companyId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        // Decodificar config para mostrar en la tabla
        foreach ($gateways as &$gw) {
            $gw['config_decoded'] = !empty($gw['config']) ? json_decode($gw['config'], true) : [];
        }
        unset($gw);

        return view('pages/payment_gateways/index', [
            'title'    => 'Pasarelas de Pago - Rifas',
            'gateways' => $gateways,
            'statuses' => self::STATUSES,
            'drivers'  => self::DRIVERS,
        ]);
    }

    public function create(): string
    {
        return view('pages/payment_gateways/create', [
            'title'    => 'Crear Pasarela - Rifas',
            'statuses' => self::STATUSES,
        ]);
    }

    public function store()
    {
        if (!$this->validateData($this->request->getPost(), 'gatewayCreate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post   = $this->request->getPost();
        $config = $this->extractConfig($post['driver'] ?? '', $post['config'] ?? []);

        $this->gatewayModel->insert([
            'uuid'              => $this->generateUuid(),
            'company_id'        => session()->get('company_id'),
            'name'              => $post['name'],
            'slug'              => $post['slug'],
            'driver'            => $post['driver'],
            'config'            => json_encode($config),
            'supported_methods' => null,
            'test_mode'         => $post['test_mode'],
            'status'            => $post['status'],
            'sort_order'        => $post['sort_order'] ?? 0,
        ]);

        return redirect()->to(base_url('pasarelas'))
            ->with('success', 'Pasarela creada correctamente.');
    }

    public function edit(int $id)
    {
        $gateway = $this->findGatewayOrFail($id);
        if (!is_array($gateway)) {
            return $gateway;
        }

        return view('pages/payment_gateways/edit', [
            'title'    => 'Editar Pasarela - Rifas',
            'gateway'  => $gateway,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(int $id)
    {
        $gateway = $this->findGatewayOrFail($id);
        if (!is_array($gateway)) {
            return $gateway;
        }

        $data       = $this->request->getPost();
        $data['id'] = $id;

        if (!$this->validateData($data, 'gatewayUpdate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post   = $this->request->getPost();
        $config = $this->extractConfig($post['driver'] ?? '', $post['config'] ?? []);

        $this->gatewayModel->update($id, [
            'name'       => $post['name'],
            'slug'       => $post['slug'],
            'driver'     => $post['driver'],
            'config'     => json_encode($config),
            'test_mode'  => $post['test_mode'],
            'status'     => $post['status'],
            'sort_order' => $post['sort_order'] ?? 0,
        ]);

        return redirect()->to(base_url('pasarelas'))
            ->with('success', 'Pasarela actualizada correctamente.');
    }

    public function delete(int $id)
    {
        $gateway = $this->findGatewayOrFail($id);
        if (!is_array($gateway)) {
            return $gateway;
        }

        $this->gatewayModel->delete($id);

        return redirect()->to(base_url('pasarelas'))
            ->with('success', 'Pasarela eliminada correctamente.');
    }

    // ──────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Extrae y limpia los campos de configuración según el driver seleccionado.
     * Solo guarda los campos relevantes para ese driver.
     */
    private function extractConfig(string $driver, array $rawConfig): array
    {
        return match ($driver) {
            'wompi' => [
                'pub_key'    => trim($rawConfig['pub_key']    ?? ''),
                'prv_key'    => trim($rawConfig['prv_key']    ?? ''),
                'events_key' => trim($rawConfig['events_key'] ?? ''),
            ],
            'mercado_pago' => [
                'mp_public_key'   => trim($rawConfig['mp_public_key']   ?? ''),
                'mp_access_token' => trim($rawConfig['mp_access_token'] ?? ''),
            ],
            'payu' => [
                'payu_merchant_id' => trim($rawConfig['payu_merchant_id'] ?? ''),
                'payu_account_id'  => trim($rawConfig['payu_account_id']  ?? ''),
                'payu_api_key'     => trim($rawConfig['payu_api_key']     ?? ''),
            ],
            'manual' => [
                'banco'          => trim($rawConfig['banco']          ?? ''),
                'tipo_cuenta'    => trim($rawConfig['tipo_cuenta']    ?? 'ahorros'),
                'numero_cuenta'  => trim($rawConfig['numero_cuenta']  ?? ''),
                'titular'        => trim($rawConfig['titular']        ?? ''),
                'instrucciones'  => trim($rawConfig['instrucciones']  ?? ''),
            ],
            default => [],
        };
    }

    private function findGatewayOrFail(int $id)
    {
        $companyId = (int) session()->get('company_id');

        $gateway = $this->gatewayModel
            ->forCompany($companyId)
            ->withDeleted()
            ->find($id);

        if (!$gateway || $gateway['deleted_at'] !== null) {
            return redirect()->to(base_url('pasarelas'))
                ->with('error', 'La pasarela no existe.');
        }

        return $gateway;
    }

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0x8000, 0xbfff), mt_rand(0x8000, 0xbfff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
