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

        return view('pages/payment_gateways/index', [
            'title'    => 'Pasarelas de Pago - Rifas',
            'gateways' => $gateways,
            'statuses' => self::STATUSES,
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

        $post = $this->request->getPost();

        $this->gatewayModel->insert([
            'uuid'              => $this->generateUuid(),
            'company_id'        => session()->get('company_id'),
            'name'              => $post['name'],
            'slug'              => $post['slug'],
            'driver'            => $post['driver'],
            'config'            => json_encode([]),
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

        $data = $this->request->getPost();
        $data['id'] = $id;

        if (!$this->validateData($data, 'gatewayUpdate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $this->gatewayModel->update($id, [
            'name'       => $post['name'],
            'slug'       => $post['slug'],
            'driver'     => $post['driver'],
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
