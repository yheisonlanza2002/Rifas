<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\SaleModel;
use App\Models\PaymentGatewayModel;

class Payments extends BaseController
{
    protected PaymentModel $paymentModel;
    protected SaleModel $saleModel;
    protected PaymentGatewayModel $gatewayModel;

    private const STATUSES = [
        'PENDING'  => 'Pendiente',
        'COMPLETED' => 'Completado',
        'FAILED'    => 'Fallido',
        'REFUNDED' => 'Reembolsado',
    ];

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->saleModel = new SaleModel();
        $this->gatewayModel = new PaymentGatewayModel();
    }

    public function index(): string
    {
        $companyId = (int) session()->get('company_id');
        $status = $this->request->getGet('status') ?? '';

        $builder = $this->paymentModel
            ->forCompany($companyId)
            ->orderBy('payments.created_at', 'DESC');

        if ($status !== '' && array_key_exists($status, self::STATUSES)) {
            $builder->where('payments.status', $status);
        }

        $payments = $builder->findAll();

        return view('pages/payments/index', [
            'title'    => 'Pagos - Rifas',
            'payments' => $payments,
            'statuses' => self::STATUSES,
            'filterStatus' => $status,
        ]);
    }

    public function create(): string
    {
        return view('pages/payments/create', [
            'title'    => 'Crear Pago - Rifas',
            'sales'    => $this->getSales(),
            'gateways' => $this->getGateways(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function store()
    {
        if (!$this->validateData($this->request->getPost(), 'paymentCreate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $this->paymentModel->insert([
            'uuid'                  => $this->generateUuid(),
            'company_id'            => session()->get('company_id'),
            'sale_id'               => $post['sale_id'],
            'gateway_id'            => !empty($post['gateway_id']) ? $post['gateway_id'] : null,
            'amount'                => $post['amount'],
            'fee_amount'            => $post['fee_amount'] ?? 0,
            'net_amount'            => $post['net_amount'] ?? $post['amount'],
            'payment_method'        => $post['payment_method'],
            'status'                => $post['status'],
            'transaction_reference' => $post['transaction_reference'] ?? null,
            'gateway'               => $post['gateway'] ?? null,
        ]);

        return redirect()->to(base_url('pagos'))
            ->with('success', 'Pago creado correctamente.');
    }

    public function edit(int $id)
    {
        $payment = $this->findPaymentOrFail($id);
        if (!is_array($payment)) {
            return $payment;
        }

        return view('pages/payments/edit', [
            'title'    => 'Editar Pago - Rifas',
            'payment'  => $payment,
            'sales'    => $this->getSales(),
            'gateways' => $this->getGateways(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(int $id)
    {
        $payment = $this->findPaymentOrFail($id);
        if (!is_array($payment)) {
            return $payment;
        }

        $data = $this->request->getPost();
        $data['id'] = $id;

        if (!$this->validateData($data, 'paymentUpdate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $updateData = [
            'sale_id'               => $post['sale_id'],
            'gateway_id'            => !empty($post['gateway_id']) ? $post['gateway_id'] : null,
            'amount'                => $post['amount'],
            'fee_amount'            => $post['fee_amount'] ?? 0,
            'net_amount'            => $post['net_amount'] ?? $post['amount'],
            'payment_method'        => $post['payment_method'],
            'status'                => $post['status'],
            'transaction_reference' => $post['transaction_reference'] ?? null,
            'gateway'               => $post['gateway'] ?? null,
        ];

        if ($post['status'] === 'COMPLETED' && $payment['status'] !== 'COMPLETED') {
            $updateData['paid_at'] = date('Y-m-d H:i:s');
        }

        $this->paymentModel->update($id, $updateData);

        return redirect()->to(base_url('pagos'))
            ->with('success', 'Pago actualizado correctamente.');
    }

    public function delete(int $id)
    {
        $payment = $this->findPaymentOrFail($id);
        if (!is_array($payment)) {
            return $payment;
        }

        if ($payment['status'] === 'COMPLETED') {
            return redirect()->to(base_url('pagos'))
                ->with('error', 'No se puede eliminar un pago completado.');
        }

        $this->paymentModel->delete($id);

        return redirect()->to(base_url('pagos'))
            ->with('success', 'Pago eliminado correctamente.');
    }

    private function getSales(): array
    {
        $companyId = (int) session()->get('company_id');

        return $this->saleModel
            ->forCompany($companyId)
            ->orderBy('sales.created_at', 'DESC')
            ->findAll();
    }

    private function getGateways(): array
    {
        $companyId = (int) session()->get('company_id');

        return $this->gatewayModel
            ->forCompany($companyId)
            ->where('status', 'ACTIVE')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    private function findPaymentOrFail(int $id)
    {
        $companyId = (int) session()->get('company_id');

        $payment = $this->paymentModel
            ->forCompany($companyId)
            ->find($id);

        if (!$payment) {
            return redirect()->to(base_url('pagos'))
                ->with('error', 'El pago no existe.');
        }

        return $payment;
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
