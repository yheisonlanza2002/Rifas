<?php

namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\ParticipantModel;

class Sales extends BaseController
{
    protected SaleModel $saleModel;
    protected ParticipantModel $participantModel;

    private const STATUSES = [
        'PENDING'  => 'Pendiente',
        'PAID'     => 'Pagada',
        'CANCELLED' => 'Cancelada',
    ];

    private const CURRENCIES = [
        'COP' => 'Peso Colombiano (COP)',
        'USD' => 'Dólar Estadounidense (USD)',
        'EUR' => 'Euro (EUR)',
    ];

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->participantModel = new ParticipantModel();
    }

    public function index(): string
    {
        $companyId = (int) session()->get('company_id');
        $status = $this->request->getGet('status') ?? '';

        $builder = $this->saleModel
            ->forCompany($companyId)
            ->orderBy('sales.created_at', 'DESC');

        if ($status !== '' && array_key_exists($status, self::STATUSES)) {
            $builder->where('sales.status', $status);
        }

        $sales = $builder->findAll();

        return view('pages/sales/index', [
            'title'    => 'Ventas - Rifas',
            'sales'    => $sales,
            'statuses' => self::STATUSES,
            'filterStatus' => $status,
        ]);
    }

    public function create(): string
    {
        return view('pages/sales/create', [
            'title'       => 'Crear Venta - Rifas',
            'participants' => $this->getParticipants(),
            'statuses'    => self::STATUSES,
            'currencies'  => self::CURRENCIES,
        ]);
    }

    public function store()
    {
        if (!$this->validateData($this->request->getPost(), 'saleCreate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $this->saleModel->insert([
            'uuid'              => $this->generateUuid(),
            'company_id'        => session()->get('company_id'),
            'participant_id'    => $post['participant_id'],
            'sale_code'         => $post['sale_code'],
            'status'            => $post['status'],
            'quantity'          => $post['quantity'],
            'subtotal'          => $post['subtotal'],
            'discount'          => $post['discount'] ?? 0,
            'tax_amount'        => $post['tax_amount'] ?? 0,
            'commission_amount' => $post['commission_amount'] ?? 0,
            'total'             => $post['total'],
            'paid_amount'       => $post['paid_amount'] ?? 0,
            'pending_amount'    => $post['total'] - ($post['paid_amount'] ?? 0),
            'currency'          => $post['currency'],
            'notes'             => $post['notes'] ?? null,
        ]);

        return redirect()->to(base_url('ventas'))
            ->with('success', 'Venta creada correctamente.');
    }

    public function edit(int $id)
    {
        $sale = $this->findSaleOrFail($id);
        if (!is_array($sale)) {
            return $sale;
        }

        return view('pages/sales/edit', [
            'title'       => 'Editar Venta - Rifas',
            'sale'        => $sale,
            'participants' => $this->getParticipants(),
            'statuses'    => self::STATUSES,
            'currencies'  => self::CURRENCIES,
        ]);
    }

    public function update(int $id)
    {
        $sale = $this->findSaleOrFail($id);
        if (!is_array($sale)) {
            return $sale;
        }

        $data = $this->request->getPost();
        $data['id'] = $id;

        if (!$this->validateData($data, 'saleUpdate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $updateData = [
            'participant_id'    => $post['participant_id'],
            'sale_code'         => $post['sale_code'],
            'status'            => $post['status'],
            'quantity'          => $post['quantity'],
            'subtotal'          => $post['subtotal'],
            'discount'          => $post['discount'] ?? 0,
            'tax_amount'        => $post['tax_amount'] ?? 0,
            'commission_amount' => $post['commission_amount'] ?? 0,
            'total'             => $post['total'],
            'paid_amount'       => $post['paid_amount'] ?? 0,
            'pending_amount'    => $post['total'] - ($post['paid_amount'] ?? 0),
            'currency'          => $post['currency'],
            'notes'             => $post['notes'] ?? null,
        ];

        if ($post['status'] === 'PAID' && $sale['status'] !== 'PAID') {
            $updateData['sold_at'] = date('Y-m-d H:i:s');
        } elseif ($post['status'] === 'CANCELLED' && $sale['status'] !== 'CANCELLED') {
            $updateData['cancelled_at'] = date('Y-m-d H:i:s');
        }

        $this->saleModel->update($id, $updateData);

        return redirect()->to(base_url('ventas'))
            ->with('success', 'Venta actualizada correctamente.');
    }

    public function delete(int $id)
    {
        $sale = $this->findSaleOrFail($id);
        if (!is_array($sale)) {
            return $sale;
        }

        if ($sale['status'] === 'PAID') {
            return redirect()->to(base_url('ventas'))
                ->with('error', 'No se puede eliminar una venta pagada.');
        }

        $this->saleModel->delete($id);

        return redirect()->to(base_url('ventas'))
            ->with('success', 'Venta eliminada correctamente.');
    }

    private function getParticipants(): array
    {
        $companyId = (int) session()->get('company_id');

        return $this->participantModel
            ->forCompany($companyId)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    private function findSaleOrFail(int $id)
    {
        $companyId = (int) session()->get('company_id');

        $sale = $this->saleModel
            ->forCompany($companyId)
            ->find($id);

        if (!$sale) {
            return redirect()->to(base_url('ventas'))
                ->with('error', 'La venta no existe.');
        }

        return $sale;
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
