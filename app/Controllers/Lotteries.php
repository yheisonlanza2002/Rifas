<?php

namespace App\Controllers;

use App\Models\LotteryModel;

class Lotteries extends BaseController
{
    protected LotteryModel $lotteryModel;

    public function __construct()
    {
        $this->lotteryModel = new LotteryModel();
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }

        $nombre = $this->request->getJSON()->name ?? '';
        $codigo = $this->request->getJSON()->code ?? '';
        $companyId = (int) session()->get('company_id');

        if (empty($nombre)) {
            return $this->response->setJSON(['success' => false, 'message' => 'El nombre es obligatorio']);
        }

        $existente = $this->lotteryModel->findByName($companyId, $nombre);
        if ($existente) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ya existe una lotería con ese nombre']);
        }

        $id = $this->lotteryModel->insert([
            'uuid'       => sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0x8000, 0xbfff), mt_rand(0x8000, 0xbfff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)),
            'company_id' => $companyId,
            'name'       => $nombre,
            'code'       => $codigo ?: null,
            'status'     => 'ACTIVE',
        ]);

        return $this->response->setJSON([
            'success'  => true,
            'lottery'  => [
                'id'   => $id,
                'name' => $nombre,
            ],
        ]);
    }
}
