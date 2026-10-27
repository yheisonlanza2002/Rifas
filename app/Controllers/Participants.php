<?php

namespace App\Controllers;

use App\Models\ParticipantModel;

class Participants extends BaseController
{
    protected ParticipantModel $participantModel;

    private const DOCUMENT_TYPES = [
        'CC'  => 'CÃ©dula de CiudadanÃ­a',
        'NIT' => 'NIT',
        'CE'  => 'CÃ©dula de ExtranjerÃ­a',
        'TI'  => 'Tarjeta de Identidad',
        'PAP' => 'Pasaporte',
        'PA'  => 'Permiso de Permanencia',
    ];

    public function __construct()
    {
        $this->participantModel = new ParticipantModel();
    }

    /**
     * GET /participantes â€” Listado
     */
    public function index(): string
    {
        $companyId = (int) session()->get('company_id');

        $participants = $this->participantModel
            ->forCompany($companyId)
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('pages/participants/index', [
            'title'         => 'Participantes - Rifas',
            'participants'  => $participants,
            'documentTypes' => self::DOCUMENT_TYPES,
        ]);
    }

    /**
     * GET /participantes/crear â€” Formulario de creaciÃ³n
     */
    public function create(): string
    {
        return view('pages/participants/create', [
            'title'         => 'Crear Participante - Rifas',
            'documentTypes' => self::DOCUMENT_TYPES,
        ]);
    }

    /**
     * POST /participantes â€” Guardar nuevo participante
     */
    public function store()
    {
        if (!$this->validateData($this->request->getPost(), 'participantCreate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $this->participantModel->insert([
            'uuid'            => $this->generateUuid(),
            'company_id'      => session()->get('company_id'),
            'document_type'   => $post['document_type'],
            'document_number' => $post['document_number'],
            'name'            => $post['name'],
            'last_name'       => $post['last_name'] ?? null,
            'email'           => !empty($post['email']) ? $post['email'] : null,
            'phone'           => $post['phone'],
            'address'         => $post['address'] ?? null,
            'city'            => $post['city'] ?? null,
            'department'      => $post['department'] ?? null,
            'birth_date'      => !empty($post['birth_date']) ? $post['birth_date'] : null,
            'gender'          => !empty($post['gender']) ? $post['gender'] : null,
            'notes'           => $post['notes'] ?? null,
            'source'          => 'WEB',
            'status'          => 'ACTIVE',
        ]);

        return redirect()->to(base_url('participantes'))
            ->with('success', 'Participante creado correctamente.');
    }

    /**
     * GET /participantes/{id}/editar â€” Formulario de ediciÃ³n
     */
    public function edit(int $id)
    {
        $participant = $this->findParticipantOrFail($id);
        if (!is_array($participant)) {
            return $participant; // redirect
        }

        return view('pages/participants/edit', [
            'title'         => 'Editar Participante - Rifas',
            'participant'   => $participant,
            'documentTypes' => self::DOCUMENT_TYPES,
        ]);
    }

    /**
     * POST /participantes/{id} â€” Actualizar participante
     */
    public function update(int $id)
    {
        $participant = $this->findParticipantOrFail($id);
        if (!is_array($participant)) {
            return $participant; // redirect
        }

        $data = $this->request->getPost();
        $data['id'] = $id;

        if (!$this->validateData($data, 'participantUpdate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $this->participantModel->update($id, [
            'document_type'   => $post['document_type'],
            'document_number' => $post['document_number'],
            'name'            => $post['name'],
            'last_name'       => $post['last_name'] ?? null,
            'email'           => !empty($post['email']) ? $post['email'] : null,
            'phone'           => $post['phone'],
            'address'         => $post['address'] ?? null,
            'city'            => $post['city'] ?? null,
            'department'      => $post['department'] ?? null,
            'birth_date'      => !empty($post['birth_date']) ? $post['birth_date'] : null,
            'gender'          => !empty($post['gender']) ? $post['gender'] : null,
            'notes'           => $post['notes'] ?? null,
        ]);

        return redirect()->to(base_url('participantes'))
            ->with('success', 'Participante actualizado correctamente.');
    }

    /**
     * GET /participantes/{id}/eliminar â€” Soft delete
     */
    public function delete(int $id)
    {
        $participant = $this->findParticipantOrFail($id);
        if (!is_array($participant)) {
            return $participant; // redirect
        }

        $this->participantModel->delete($id);

        return redirect()->to(base_url('participantes'))
            ->with('success', 'Participante eliminado correctamente.');
    }

    // --------------------------------------------------------------------
    // Helpers privados
    // --------------------------------------------------------------------

    /**
     * Busca un participante verificando empresa propietaria y no eliminado.
     * Devuelve el array del participante o un redirect de error.
     */
    private function findParticipantOrFail(int $id)
    {
        $companyId = (int) session()->get('company_id');

        $participant = $this->participantModel
            ->forCompany($companyId)
            ->withDeleted() // necesario para consultar soft-deleted integrity; se filtra abajo
            ->find($id);

        if (!$participant || $participant['deleted_at'] !== null) {
            return redirect()->to(base_url('participantes'))
                ->with('error', 'El participante no existe.');
        }

        return $participant;
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
