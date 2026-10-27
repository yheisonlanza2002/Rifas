<?php

namespace App\Controllers;

use App\Models\EmailTemplateModel;

class EmailTemplates extends BaseController
{
    protected EmailTemplateModel $emailTemplateModel;

    private const STATUSES = [
        'ACTIVE'   => 'Activo',
        'INACTIVE' => 'Inactivo',
    ];

    public function __construct()
    {
        $this->emailTemplateModel = new EmailTemplateModel();
    }

    public function index(): string
    {
        $companyId = (int) session()->get('company_id');

        $templates = $this->emailTemplateModel
            ->forCompany($companyId)
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('pages/email_templates/index', [
            'title'    => 'Plantillas de Email - Rifas',
            'templates' => $templates,
            'statuses' => self::STATUSES,
        ]);
    }

    public function create(): string
    {
        return view('pages/email_templates/create', [
            'title'    => 'Crear Plantilla - Rifas',
            'statuses' => self::STATUSES,
        ]);
    }

    public function store()
    {
        if (!$this->validateData($this->request->getPost(), 'emailTemplateCreate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();
        $companyId = (int) session()->get('company_id');

        $this->emailTemplateModel->insert([
            'uuid'       => $this->generateUuid(),
            'company_id' => $post['is_global'] ? null : $companyId,
            'name'       => $post['name'],
            'slug'       => $post['slug'],
            'subject'    => $post['subject'],
            'body_html'  => $post['body_html'],
            'body_text'  => $post['body_text'] ?? null,
            'variables'  => $post['variables'] ?? null,
            'is_global'  => $post['is_global'],
            'status'     => $post['status'],
        ]);

        return redirect()->to(base_url('plantillas-email'))
            ->with('success', 'Plantilla creada correctamente.');
    }

    public function edit(int $id)
    {
        $template = $this->findTemplateOrFail($id);
        if (!is_array($template)) {
            return $template;
        }

        return view('pages/email_templates/edit', [
            'title'    => 'Editar Plantilla - Rifas',
            'template' => $template,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(int $id)
    {
        $template = $this->findTemplateOrFail($id);
        if (!is_array($template)) {
            return $template;
        }

        $data = $this->request->getPost();
        $data['id'] = $id;

        if (!$this->validateData($data, 'emailTemplateUpdate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $this->emailTemplateModel->update($id, [
            'name'       => $post['name'],
            'slug'       => $post['slug'],
            'subject'    => $post['subject'],
            'body_html'  => $post['body_html'],
            'body_text'  => $post['body_text'] ?? null,
            'variables'  => $post['variables'] ?? null,
            'is_global'  => $post['is_global'],
            'status'     => $post['status'],
        ]);

        return redirect()->to(base_url('plantillas-email'))
            ->with('success', 'Plantilla actualizada correctamente.');
    }

    public function delete(int $id)
    {
        $template = $this->findTemplateOrFail($id);
        if (!is_array($template)) {
            return $template;
        }

        $this->emailTemplateModel->delete($id);

        return redirect()->to(base_url('plantillas-email'))
            ->with('success', 'Plantilla eliminada correctamente.');
    }

    public function preview(int $id): string
    {
        $template = $this->findTemplateOrFail($id);
        if (!is_array($template)) {
            return redirect()->to(base_url('plantillas-email'))
                ->with('error', 'La plantilla no existe.');
        }

        return view('pages/email_templates/preview', [
            'title'    => 'Vista Previa - Rifas',
            'template' => $template,
        ]);
    }

    private function findTemplateOrFail(int $id)
    {
        $companyId = (int) session()->get('company_id');

        $template = $this->emailTemplateModel
            ->forCompany($companyId)
            ->withDeleted()
            ->find($id);

        if (!$template || $template['deleted_at'] !== null) {
            return redirect()->to(base_url('plantillas-email'))
                ->with('error', 'La plantilla no existe.');
        }

        return $template;
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
