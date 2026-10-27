<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    /**
     * Grupos de reglas de validación centralizados para formularios.
     * Deben ser propiedades `public array` (CI4 las resuelve por nombre).
     *
     * Uso en controladores:
     *   if (!$this->validateData($this->request->getPost(), 'userCreate')) { ... }
     */

    // ========================================================================
    // AUTH
    // ========================================================================

    /** Login de usuarios */
    public array $userLogin = [
        'email' => [
            'rules'  => 'required|valid_email',
            'errors' => [
                'required'    => 'El email es obligatorio.',
                'valid_email' => 'Ingresa un email válido.',
            ],
        ],
        'password' => [
            'rules'  => 'required|min_length[6]',
            'errors' => [
                'required'   => 'La contraseña es obligatoria.',
                'min_length' => 'La contraseña debe tener al menos 6 caracteres.',
            ],
        ],
    ];

    // ========================================================================
    // MÓDULO 1: USUARIOS Y ROLES
    // ========================================================================

    /** Crear usuario */
    public array $userCreate = [
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[100]',
            'errors' => [
                'required'   => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 100 caracteres.',
            ],
        ],
        'last_name' => [
            'rules'  => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'El apellido no puede exceder 100 caracteres.',
            ],
        ],
        'email' => [
            'rules'  => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'errors' => [
                'required'    => 'El email es obligatorio.',
                'valid_email' => 'Ingresa un email válido.',
                'max_length'  => 'El email no puede exceder 150 caracteres.',
                'is_unique'   => 'Este email ya está registrado.',
            ],
        ],
        'phone' => [
            'rules'  => 'permit_empty|min_length[7]|max_length[30]',
            'errors' => [
                'min_length' => 'El teléfono debe tener al menos 7 dígitos.',
                'max_length' => 'El teléfono no puede exceder 30 caracteres.',
            ],
        ],
        'password' => [
            'rules'  => 'required|min_length[6]',
            'errors' => [
                'required'   => 'La contraseña es obligatoria.',
                'min_length' => 'La contraseña debe tener al menos 6 caracteres.',
            ],
        ],
        'password_confirm' => [
            'rules'  => 'required|matches[password]',
            'errors' => [
                'required' => 'Confirma la contraseña.',
                'matches'  => 'Las contraseñas no coinciden.',
            ],
        ],
        'roles' => [
            'rules'  => 'required',
            'errors' => [
                'required' => 'Debes asignar al menos un rol.',
            ],
        ],
    ];

    /** Actualizar usuario (contraseña opcional) */
    public array $userUpdate = [
        'id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required' => 'ID de usuario no proporcionado.',
            ],
        ],
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[100]',
            'errors' => [
                'required'   => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 100 caracteres.',
            ],
        ],
        'last_name' => [
            'rules'  => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'El apellido no puede exceder 100 caracteres.',
            ],
        ],
        'email' => [
            'rules'  => 'required|valid_email|max_length[150]|is_unique[users.email,id,{id}]',
            'errors' => [
                'required'    => 'El email es obligatorio.',
                'valid_email' => 'Ingresa un email válido.',
                'max_length'  => 'El email no puede exceder 150 caracteres.',
                'is_unique'   => 'Este email ya está en uso por otro usuario.',
            ],
        ],
        'phone' => [
            'rules'  => 'permit_empty|min_length[7]|max_length[30]',
            'errors' => [
                'min_length' => 'El teléfono debe tener al menos 7 dígitos.',
                'max_length' => 'El teléfono no puede exceder 30 caracteres.',
            ],
        ],
        'password' => [
            'rules'  => 'permit_empty|min_length[6]',
            'errors' => [
                'min_length' => 'La contraseña debe tener al menos 6 caracteres.',
            ],
        ],
        'password_confirm' => [
            'rules'  => 'permit_empty|matches[password]',
            'errors' => [
                'matches' => 'Las contraseñas no coinciden.',
            ],
        ],
        'roles' => [
            'rules'  => 'required',
            'errors' => [
                'required' => 'Debes asignar al menos un rol.',
            ],
        ],
    ];

    // ========================================================================
    // MÓDULO 2: PARTICIPANTES
    // ========================================================================

    /** Crear participante */
    public array $participantCreate = [
        'document_type' => [
            'rules'  => 'required|in_list[CC,NIT,CE,TI,PAP,PA]',
            'errors' => [
                'required' => 'El tipo de documento es obligatorio.',
                'in_list'  => 'Tipo de documento no válido.',
            ],
        ],
        'document_number' => [
            'rules'  => 'required|min_length[5]|max_length[50]|is_unique[participants.document_number]',
            'errors' => [
                'required'   => 'El número de documento es obligatorio.',
                'min_length' => 'El documento debe tener al menos 5 caracteres.',
                'max_length' => 'El documento no puede exceder 50 caracteres.',
                'is_unique'  => 'Ya existe un participante con este documento.',
            ],
        ],
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[100]',
            'errors' => [
                'required'   => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 100 caracteres.',
            ],
        ],
        'last_name' => [
            'rules'  => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'El apellido no puede exceder 100 caracteres.',
            ],
        ],
        'email' => [
            'rules'  => 'permit_empty|valid_email|max_length[150]',
            'errors' => [
                'valid_email' => 'Ingresa un email válido.',
                'max_length'  => 'El email no puede exceder 150 caracteres.',
            ],
        ],
        'phone' => [
            'rules'  => 'required|min_length[7]|max_length[30]',
            'errors' => [
                'required'   => 'El teléfono es obligatorio.',
                'min_length' => 'El teléfono debe tener al menos 7 dígitos.',
                'max_length' => 'El teléfono no puede exceder 30 caracteres.',
            ],
        ],
        'address' => [
            'rules'  => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'La dirección no puede exceder 255 caracteres.',
            ],
        ],
        'city' => [
            'rules'  => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'La ciudad no puede exceder 100 caracteres.',
            ],
        ],
        'department' => [
            'rules'  => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'El departamento no puede exceder 100 caracteres.',
            ],
        ],
        'birth_date' => [
            'rules'  => 'permit_empty|valid_date[Y-m-d]',
            'errors' => [
                'valid_date' => 'La fecha de nacimiento no es válida (formato AAAA-MM-DD).',
            ],
        ],
        'gender' => [
            'rules'  => 'permit_empty|in_list[M,F,O]',
            'errors' => [
                'in_list' => 'Género no válido.',
            ],
        ],
        'notes' => [
            'rules'  => 'permit_empty|max_length[1000]',
            'errors' => [
                'max_length' => 'Las notas no pueden exceder 1000 caracteres.',
            ],
        ],
    ];

    /** Actualizar participante */
    public array $participantUpdate = [
        'id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required' => 'ID de participante no proporcionado.',
            ],
        ],
        'document_type' => [
            'rules'  => 'required|in_list[CC,NIT,CE,TI,PAP,PA]',
            'errors' => [
                'required' => 'El tipo de documento es obligatorio.',
                'in_list'  => 'Tipo de documento no válido.',
            ],
        ],
        'document_number' => [
            'rules'  => 'required|min_length[5]|max_length[50]|is_unique[participants.document_number,id,{id}]',
            'errors' => [
                'required'   => 'El número de documento es obligatorio.',
                'min_length' => 'El documento debe tener al menos 5 caracteres.',
                'max_length' => 'El documento no puede exceder 50 caracteres.',
                'is_unique'  => 'Ya existe otro participante con este documento.',
            ],
        ],
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[100]',
            'errors' => [
                'required'   => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 100 caracteres.',
            ],
        ],
        'last_name' => [
            'rules'  => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'El apellido no puede exceder 100 caracteres.',
            ],
        ],
        'email' => [
            'rules'  => 'permit_empty|valid_email|max_length[150]',
            'errors' => [
                'valid_email' => 'Ingresa un email válido.',
                'max_length'  => 'El email no puede exceder 150 caracteres.',
            ],
        ],
        'phone' => [
            'rules'  => 'required|min_length[7]|max_length[30]',
            'errors' => [
                'required'   => 'El teléfono es obligatorio.',
                'min_length' => 'El teléfono debe tener al menos 7 dígitos.',
                'max_length' => 'El teléfono no puede exceder 30 caracteres.',
            ],
        ],
        'address' => [
            'rules'  => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'La dirección no puede exceder 255 caracteres.',
            ],
        ],
        'city' => [
            'rules'  => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'La ciudad no puede exceder 100 caracteres.',
            ],
        ],
        'department' => [
            'rules'  => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'El departamento no puede exceder 100 caracteres.',
            ],
        ],
        'birth_date' => [
            'rules'  => 'permit_empty|valid_date[Y-m-d]',
            'errors' => [
                'valid_date' => 'La fecha de nacimiento no es válida (formato AAAA-MM-DD).',
            ],
        ],
        'gender' => [
            'rules'  => 'permit_empty|in_list[M,F,O]',
            'errors' => [
                'in_list' => 'Género no válido.',
            ],
        ],
        'notes' => [
            'rules'  => 'permit_empty|max_length[1000]',
            'errors' => [
                'max_length' => 'Las notas no pueden exceder 1000 caracteres.',
            ],
        ],
    ];

    // ========================================================================
    // MÓDULO 3: RIFAS
    // ========================================================================

    /** Crear rifa */
    public array $raffleCreate = [
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[150]',
            'errors' => [
                'required'   => 'El nombre de la rifa es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 150 caracteres.',
            ],
        ],
        'code' => [
            'rules'  => 'permit_empty|max_length[80]',
            'errors' => [
                'max_length' => 'El código no puede exceder 80 caracteres.',
            ],
        ],
        'description' => [
            'rules'  => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'La descripción no puede exceder 500 caracteres.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[DRAFT,ACTIVE,CLOSED]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
        'ticket_quantity' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'            => 'La cantidad de boletas es obligatoria.',
                'is_natural_no_zero'  => 'La cantidad debe ser un número entero positivo.',
            ],
        ],
        'numbers_per_ticket' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'Los números por boleta son obligatorios.',
                'is_natural_no_zero' => 'Debe ser un número entero positivo.',
            ],
        ],
        'number_start' => [
            'rules'  => 'required|max_length[30]',
            'errors' => [
                'required'   => 'El número inicial es obligatorio.',
                'max_length' => 'El número inicial no puede exceder 30 caracteres.',
            ],
        ],
        'number_end' => [
            'rules'  => 'required|max_length[30]',
            'errors' => [
                'required'   => 'El número final es obligatorio.',
                'max_length' => 'El número final no puede exceder 30 caracteres.',
            ],
        ],
        'number_length' => [
            'rules'  => 'permit_empty|is_natural_no_zero',
            'errors' => [
                'is_natural_no_zero' => 'La longitud debe ser un número entero positivo.',
            ],
        ],
        'tipo_generacion' => [
            'rules'  => 'required|in_list[SEQUENTIAL,RANDOM]',
            'errors' => [
                'required' => 'El tipo de generación es obligatorio.',
                'in_list'  => 'Tipo de generación no válido.',
            ],
        ],
        'ticket_price' => [
            'rules'  => 'required|decimal|greater_than[0]',
            'errors' => [
                'required'      => 'El precio es obligatorio.',
                'decimal'       => 'El precio debe ser un número decimal válido.',
                'greater_than'  => 'El precio debe ser mayor que cero.',
            ],
        ],
        'currency' => [
            'rules'  => 'required|in_list[COP,USD,EUR]',
            'errors' => [
                'required' => 'La moneda es obligatoria.',
                'in_list'  => 'Moneda no válida.',
            ],
        ],
        'commission_rate' => [
            'rules'  => 'permit_empty|decimal|less_than_equal_to[100]',
            'errors' => [
                'decimal'              => 'La comisión debe ser un número decimal válido.',
                'less_than_equal_to'   => 'La comisión no puede exceder 100%.',
            ],
        ],
        'reservation_minutes' => [
            'rules'  => 'permit_empty|is_natural_no_zero',
            'errors' => [
                'is_natural_no_zero' => 'Los minutos de reserva deben ser un número entero positivo.',
            ],
        ],
        'terms_conditions' => [
            'rules'  => 'permit_empty|max_length[2000]',
            'errors' => [
                'max_length' => 'Los términos no pueden exceder 2000 caracteres.',
            ],
        ],
        'start_at' => [
            'rules'  => 'permit_empty|valid_date[Y-m-d\TH:i]',
            'errors' => [
                'valid_date' => 'La fecha de inicio no es válida.',
            ],
        ],
        'end_at' => [
            'rules'  => 'permit_empty|valid_date[Y-m-d\TH:i]',
            'errors' => [
                'valid_date' => 'La fecha de fin no es válida.',
            ],
        ],
    ];

    /** Actualizar rifa */
    public array $raffleUpdate = [
        'id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required' => 'ID de rifa no proporcionado.',
            ],
        ],
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[150]',
            'errors' => [
                'required'   => 'El nombre de la rifa es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 150 caracteres.',
            ],
        ],
        'code' => [
            'rules'  => 'required|min_length[3]|max_length[80]|is_unique[raffles.code,id,{id}]',
            'errors' => [
                'required'   => 'El código es obligatorio.',
                'min_length' => 'El código debe tener al menos 3 caracteres.',
                'max_length' => 'El código no puede exceder 80 caracteres.',
                'is_unique'  => 'Este código ya está en uso por otra rifa.',
            ],
        ],
        'description' => [
            'rules'  => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'La descripción no puede exceder 500 caracteres.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[DRAFT,ACTIVE,CLOSED]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
        'ticket_quantity' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'            => 'La cantidad de boletas es obligatoria.',
                'is_natural_no_zero'  => 'La cantidad debe ser un número entero positivo.',
            ],
        ],
        'numbers_per_ticket' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'Los números por boleta son obligatorios.',
                'is_natural_no_zero' => 'Debe ser un número entero positivo.',
            ],
        ],
        'number_start' => [
            'rules'  => 'required|max_length[30]',
            'errors' => [
                'required'   => 'El número inicial es obligatorio.',
                'max_length' => 'El número inicial no puede exceder 30 caracteres.',
            ],
        ],
        'number_end' => [
            'rules'  => 'required|max_length[30]',
            'errors' => [
                'required'   => 'El número final es obligatorio.',
                'max_length' => 'El número final no puede exceder 30 caracteres.',
            ],
        ],
        'number_length' => [
            'rules'  => 'permit_empty|is_natural_no_zero',
            'errors' => [
                'is_natural_no_zero' => 'La longitud debe ser un número entero positivo.',
            ],
        ],
        'number_assignment_mode' => [
            'rules'  => 'required|in_list[SEQUENTIAL,RANDOM,MANUAL]',
            'errors' => [
                'required' => 'El modo de asignación es obligatorio.',
                'in_list'  => 'Modo de asignación no válido.',
            ],
        ],
        'number_selection_mode' => [
            'rules'  => 'required|in_list[BOTH,SELECTION,ASSIGNMENT]',
            'errors' => [
                'required' => 'El modo de selección es obligatorio.',
                'in_list'  => 'Modo de selección no válido.',
            ],
        ],
        'ticket_price' => [
            'rules'  => 'required|decimal|greater_than[0]',
            'errors' => [
                'required'      => 'El precio es obligatorio.',
                'decimal'       => 'El precio debe ser un número decimal válido.',
                'greater_than'  => 'El precio debe ser mayor que cero.',
            ],
        ],
        'currency' => [
            'rules'  => 'required|in_list[COP,USD,EUR]',
            'errors' => [
                'required' => 'La moneda es obligatoria.',
                'in_list'  => 'Moneda no válida.',
            ],
        ],
        'commission_rate' => [
            'rules'  => 'permit_empty|decimal|less_than_equal_to[100]',
            'errors' => [
                'decimal'              => 'La comisión debe ser un número decimal válido.',
                'less_than_equal_to'   => 'La comisión no puede exceder 100%.',
            ],
        ],
        'reservation_minutes' => [
            'rules'  => 'permit_empty|is_natural_no_zero',
            'errors' => [
                'is_natural_no_zero' => 'Los minutos de reserva deben ser un número entero positivo.',
            ],
        ],
        'terms_conditions' => [
            'rules'  => 'permit_empty|max_length[2000]',
            'errors' => [
                'max_length' => 'Los términos no pueden exceder 2000 caracteres.',
            ],
        ],
        'start_at' => [
            'rules'  => 'permit_empty|valid_date[Y-m-d H:i:s]',
            'errors' => [
                'valid_date' => 'La fecha de inicio no es válida.',
            ],
        ],
        'end_at' => [
            'rules'  => 'permit_empty|valid_date[Y-m-d H:i:s]',
            'errors' => [
                'valid_date' => 'La fecha de fin no es válida.',
            ],
        ],
    ];

    // ========================================================================
    // MÓDULO 4: PREMIOS
    // ========================================================================

    /** Crear premio */
    public array $prizeCreate = [
        'raffle_id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La rifa es obligatoria.',
                'is_natural_no_zero' => 'Debe seleccionar una rifa válida.',
            ],
        ],
        'lottery_id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La lotería es obligatoria.',
                'is_natural_no_zero' => 'Debe seleccionar una lotería válida.',
            ],
        ],
        'prize_type' => [
            'rules'  => 'required|in_list[PRIZE,MONEY]',
            'errors' => [
                'required' => 'El tipo de premio es obligatorio.',
                'in_list'  => 'Tipo de premio no válido.',
            ],
        ],
        'recurrence_days' => [
            'rules'  => 'permit_empty|is_natural_no_zero',
            'errors' => [
                'is_natural_no_zero' => 'La recurrencia debe ser un número entero positivo.',
            ],
        ],
        'draw_date' => [
            'rules'  => 'permit_empty|valid_date[Y-m-d]',
            'errors' => [
                'valid_date' => 'La fecha del sorteo no es válida.',
            ],
        ],
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[150]',
            'errors' => [
                'required'   => 'El nombre del premio es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 150 caracteres.',
            ],
        ],
        'description' => [
            'rules'  => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'La descripción no puede exceder 500 caracteres.',
            ],
        ],
        'position' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La posición del sorteo es obligatoria.',
                'is_natural_no_zero' => 'La posición debe ser un número entero positivo.',
            ],
        ],
        'quantity' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La cantidad es obligatoria.',
                'is_natural_no_zero' => 'La cantidad debe ser un número entero positivo.',
            ],
        ],
        'value' => [
            'rules'  => 'required|decimal|greater_than[0]',
            'errors' => [
                'required'      => 'El valor es obligatorio.',
                'decimal'       => 'El valor debe ser un número decimal válido.',
                'greater_than'  => 'El valor debe ser mayor que cero.',
            ],
        ],
        'currency' => [
            'rules'  => 'required|in_list[COP,USD,EUR]',
            'errors' => [
                'required' => 'La moneda es obligatoria.',
                'in_list'  => 'Moneda no válida.',
            ],
        ],
        'image' => [
            'rules'  => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'La ruta de la imagen no puede exceder 255 caracteres.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[ACTIVE,INACTIVE]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
    ];

    /** Actualizar premio */
    public array $prizeUpdate = [
        'id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required' => 'ID de premio no proporcionado.',
            ],
        ],
        'raffle_id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La rifa es obligatoria.',
                'is_natural_no_zero' => 'Debe seleccionar una rifa válida.',
            ],
        ],
        'lottery_id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La lotería es obligatoria.',
                'is_natural_no_zero' => 'Debe seleccionar una lotería válida.',
            ],
        ],
        'prize_type' => [
            'rules'  => 'required|in_list[PRIZE,MONEY]',
            'errors' => [
                'required' => 'El tipo de premio es obligatorio.',
                'in_list'  => 'Tipo de premio no válido.',
            ],
        ],
        'recurrence_days' => [
            'rules'  => 'permit_empty|is_natural_no_zero',
            'errors' => [
                'is_natural_no_zero' => 'La recurrencia debe ser un número entero positivo.',
            ],
        ],
        'draw_date' => [
            'rules'  => 'permit_empty|valid_date[Y-m-d]',
            'errors' => [
                'valid_date' => 'La fecha del sorteo no es válida.',
            ],
        ],
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[150]',
            'errors' => [
                'required'   => 'El nombre del premio es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 150 caracteres.',
            ],
        ],
        'description' => [
            'rules'  => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'La descripción no puede exceder 500 caracteres.',
            ],
        ],
        'position' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La posición del sorteo es obligatoria.',
                'is_natural_no_zero' => 'La posición debe ser un número entero positivo.',
            ],
        ],
        'quantity' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La cantidad es obligatoria.',
                'is_natural_no_zero' => 'La cantidad debe ser un número entero positivo.',
            ],
        ],
        'value' => [
            'rules'  => 'required|decimal|greater_than[0]',
            'errors' => [
                'required'      => 'El valor es obligatorio.',
                'decimal'       => 'El valor debe ser un número decimal válido.',
                'greater_than'  => 'El valor debe ser mayor que cero.',
            ],
        ],
        'currency' => [
            'rules'  => 'required|in_list[COP,USD,EUR]',
            'errors' => [
                'required' => 'La moneda es obligatoria.',
                'in_list'  => 'Moneda no válida.',
            ],
        ],
        'image' => [
            'rules'  => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'La ruta de la imagen no puede exceder 255 caracteres.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[ACTIVE,INACTIVE]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
    ];

    // ========================================================================
    // MÓDULO 5: BOLETAS/TICKETS
    // ========================================================================

    /** Crear ticket */
    public array $ticketCreate = [
        'raffle_id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La rifa es obligatoria.',
                'is_natural_no_zero' => 'Debe seleccionar una rifa válida.',
            ],
        ],
        'code' => [
            'rules'  => 'required|min_length[3]|max_length[80]|is_unique[tickets.code]',
            'errors' => [
                'required'   => 'El código es obligatorio.',
                'min_length' => 'El código debe tener al menos 3 caracteres.',
                'max_length' => 'El código no puede exceder 80 caracteres.',
                'is_unique'  => 'Este código ya está registrado.',
            ],
        ],
        'serial_prefix' => [
            'rules'  => 'permit_empty|max_length[20]',
            'errors' => [
                'max_length' => 'El prefijo no puede exceder 20 caracteres.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[AVAILABLE,RESERVED,SOLD,CANCELLED]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
        'price' => [
            'rules'  => 'required|decimal|greater_than[0]',
            'errors' => [
                'required'      => 'El precio es obligatorio.',
                'decimal'       => 'El precio debe ser un número decimal válido.',
                'greater_than'  => 'El precio debe ser mayor que cero.',
            ],
        ],
        'number_quantity' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La cantidad de números es obligatoria.',
                'is_natural_no_zero' => 'Debe ser un número entero positivo.',
            ],
        ],
        'bundle_quantity' => [
            'rules'  => 'permit_empty|is_natural_no_zero',
            'errors' => [
                'is_natural_no_zero' => 'La cantidad del paquete debe ser un número entero positivo.',
            ],
        ],
    ];

    /** Actualizar ticket */
    public array $ticketUpdate = [
        'id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required' => 'ID de ticket no proporcionado.',
            ],
        ],
        'raffle_id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La rifa es obligatoria.',
                'is_natural_no_zero' => 'Debe seleccionar una rifa válida.',
            ],
        ],
        'code' => [
            'rules'  => 'required|min_length[3]|max_length[80]|is_unique[tickets.code,id,{id}]',
            'errors' => [
                'required'   => 'El código es obligatorio.',
                'min_length' => 'El código debe tener al menos 3 caracteres.',
                'max_length' => 'El código no puede exceder 80 caracteres.',
                'is_unique'  => 'Este código ya está en uso por otro ticket.',
            ],
        ],
        'serial_prefix' => [
            'rules'  => 'permit_empty|max_length[20]',
            'errors' => [
                'max_length' => 'El prefijo no puede exceder 20 caracteres.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[AVAILABLE,RESERVED,SOLD,CANCELLED]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
        'price' => [
            'rules'  => 'required|decimal|greater_than[0]',
            'errors' => [
                'required'      => 'El precio es obligatorio.',
                'decimal'       => 'El precio debe ser un número decimal válido.',
                'greater_than'  => 'El precio debe ser mayor que cero.',
            ],
        ],
        'number_quantity' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La cantidad de números es obligatoria.',
                'is_natural_no_zero' => 'Debe ser un número entero positivo.',
            ],
        ],
        'bundle_quantity' => [
            'rules'  => 'permit_empty|is_natural_no_zero',
            'errors' => [
                'is_natural_no_zero' => 'La cantidad del paquete debe ser un número entero positivo.',
            ],
        ],
    ];

    // ========================================================================
    // MÓDULO 6: VENTAS
    // ========================================================================

    /** Crear venta */
    public array $saleCreate = [
        'participant_id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'El participante es obligatorio.',
                'is_natural_no_zero' => 'Debe seleccionar un participante válido.',
            ],
        ],
        'sale_code' => [
            'rules'  => 'required|min_length[3]|max_length[80]|is_unique[sales.sale_code]',
            'errors' => [
                'required'   => 'El código de venta es obligatorio.',
                'min_length' => 'El código debe tener al menos 3 caracteres.',
                'max_length' => 'El código no puede exceder 80 caracteres.',
                'is_unique'  => 'Este código ya está registrado.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[PENDING,PAID,CANCELLED]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
        'quantity' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La cantidad es obligatoria.',
                'is_natural_no_zero' => 'La cantidad debe ser un número entero positivo.',
            ],
        ],
        'subtotal' => [
            'rules'  => 'required|decimal|greater_than_equal_to[0]',
            'errors' => [
                'required'                => 'El subtotal es obligatorio.',
                'decimal'                 => 'El subtotal debe ser un número decimal válido.',
                'greater_than_equal_to'   => 'El subtotal no puede ser negativo.',
            ],
        ],
        'discount' => [
            'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal'                => 'El descuento debe ser un número decimal válido.',
                'greater_than_equal_to'  => 'El descuento no puede ser negativo.',
            ],
        ],
        'tax_amount' => [
            'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal'                => 'El impuesto debe ser un número decimal válido.',
                'greater_than_equal_to'  => 'El impuesto no puede ser negativo.',
            ],
        ],
        'commission_amount' => [
            'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal'                => 'La comisión debe ser un número decimal válido.',
                'greater_than_equal_to'  => 'La comisión no puede ser negativo.',
            ],
        ],
        'total' => [
            'rules'  => 'required|decimal|greater_than[0]',
            'errors' => [
                'required'      => 'El total es obligatorio.',
                'decimal'       => 'El total debe ser un número decimal válido.',
                'greater_than'  => 'El total debe ser mayor que cero.',
            ],
        ],
        'paid_amount' => [
            'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal'                => 'El monto pagado debe ser un número decimal válido.',
                'greater_than_equal_to'  => 'El monto pagado no puede ser negativo.',
            ],
        ],
        'currency' => [
            'rules'  => 'required|in_list[COP,USD,EUR]',
            'errors' => [
                'required' => 'La moneda es obligatoria.',
                'in_list'  => 'Moneda no válida.',
            ],
        ],
        'notes' => [
            'rules'  => 'permit_empty|max_length[1000]',
            'errors' => [
                'max_length' => 'Las notas no pueden exceder 1000 caracteres.',
            ],
        ],
    ];

    /** Actualizar venta */
    public array $saleUpdate = [
        'id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required' => 'ID de venta no proporcionado.',
            ],
        ],
        'participant_id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'El participante es obligatorio.',
                'is_natural_no_zero' => 'Debe seleccionar un participante válido.',
            ],
        ],
        'sale_code' => [
            'rules'  => 'required|min_length[3]|max_length[80]|is_unique[sales.sale_code,id,{id}]',
            'errors' => [
                'required'   => 'El código de venta es obligatorio.',
                'min_length' => 'El código debe tener al menos 3 caracteres.',
                'max_length' => 'El código no puede exceder 80 caracteres.',
                'is_unique'  => 'Este código ya está en uso por otra venta.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[PENDING,PAID,CANCELLED]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
        'quantity' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La cantidad es obligatoria.',
                'is_natural_no_zero' => 'La cantidad debe ser un número entero positivo.',
            ],
        ],
        'subtotal' => [
            'rules'  => 'required|decimal|greater_than_equal_to[0]',
            'errors' => [
                'required'                => 'El subtotal es obligatorio.',
                'decimal'                 => 'El subtotal debe ser un número decimal válido.',
                'greater_than_equal_to'   => 'El subtotal no puede ser negativo.',
            ],
        ],
        'discount' => [
            'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal'                => 'El descuento debe ser un número decimal válido.',
                'greater_than_equal_to'  => 'El descuento no puede ser negativo.',
            ],
        ],
        'tax_amount' => [
            'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal'                => 'El impuesto debe ser un número decimal válido.',
                'greater_than_equal_to'  => 'El impuesto no puede ser negativo.',
            ],
        ],
        'commission_amount' => [
            'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal'                => 'La comisión debe ser un número decimal válido.',
                'greater_than_equal_to'  => 'La comisión no puede ser negativo.',
            ],
        ],
        'total' => [
            'rules'  => 'required|decimal|greater_than[0]',
            'errors' => [
                'required'      => 'El total es obligatorio.',
                'decimal'       => 'El total debe ser un número decimal válido.',
                'greater_than'  => 'El total debe ser mayor que cero.',
            ],
        ],
        'paid_amount' => [
            'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal'                => 'El monto pagado debe ser un número decimal válido.',
                'greater_than_equal_to'  => 'El monto pagado no puede ser negativo.',
            ],
        ],
        'currency' => [
            'rules'  => 'required|in_list[COP,USD,EUR]',
            'errors' => [
                'required' => 'La moneda es obligatoria.',
                'in_list'  => 'Moneda no válida.',
            ],
        ],
        'notes' => [
            'rules'  => 'permit_empty|max_length[1000]',
            'errors' => [
                'max_length' => 'Las notas no pueden exceder 1000 caracteres.',
            ],
        ],
    ];

    // ========================================================================
    // MÓDULO 7: PAGOS Y PASARRELAS
    // ========================================================================

    /** Crear pago */
    public array $paymentCreate = [
        'sale_id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La venta es obligatoria.',
                'is_natural_no_zero' => 'Debe seleccionar una venta válida.',
            ],
        ],
        'gateway_id' => [
            'rules'  => 'permit_empty|is_natural_no_zero',
            'errors' => [
                'is_natural_no_zero' => 'Debe seleccionar una pasarela válida.',
            ],
        ],
        'amount' => [
            'rules'  => 'required|decimal|greater_than[0]',
            'errors' => [
                'required'      => 'El monto es obligatorio.',
                'decimal'       => 'El monto debe ser un número decimal válido.',
                'greater_than'  => 'El monto debe ser mayor que cero.',
            ],
        ],
        'fee_amount' => [
            'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal'                => 'La comisión debe ser un número decimal válido.',
                'greater_than_equal_to'  => 'La comisión no puede ser negativa.',
            ],
        ],
        'net_amount' => [
            'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal'                => 'El monto neto debe ser un número decimal válido.',
                'greater_than_equal_to'  => 'El monto neto no puede ser negativo.',
            ],
        ],
        'payment_method' => [
            'rules'  => 'required|max_length[40]',
            'errors' => [
                'required'   => 'El método de pago es obligatorio.',
                'max_length' => 'El método no puede exceder 40 caracteres.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[PENDING,COMPLETED,FAILED,REFUNDED]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
        'transaction_reference' => [
            'rules'  => 'permit_empty|max_length[150]',
            'errors' => [
                'max_length' => 'La referencia no puede exceder 150 caracteres.',
            ],
        ],
        'gateway' => [
            'rules'  => 'permit_empty|max_length[80]',
            'errors' => [
                'max_length' => 'La pasarela no puede exceder 80 caracteres.',
            ],
        ],
    ];

    /** Actualizar pago */
    public array $paymentUpdate = [
        'id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required' => 'ID de pago no proporcionado.',
            ],
        ],
        'sale_id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La venta es obligatoria.',
                'is_natural_no_zero' => 'Debe seleccionar una venta válida.',
            ],
        ],
        'gateway_id' => [
            'rules'  => 'permit_empty|is_natural_no_zero',
            'errors' => [
                'is_natural_no_zero' => 'Debe seleccionar una pasarela válida.',
            ],
        ],
        'amount' => [
            'rules'  => 'required|decimal|greater_than[0]',
            'errors' => [
                'required'      => 'El monto es obligatorio.',
                'decimal'       => 'El monto debe ser un número decimal válido.',
                'greater_than'  => 'El monto debe ser mayor que cero.',
            ],
        ],
        'fee_amount' => [
            'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal'                => 'La comisión debe ser un número decimal válido.',
                'greater_than_equal_to'  => 'La comisión no puede ser negativa.',
            ],
        ],
        'net_amount' => [
            'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal'                => 'El monto neto debe ser un número decimal válido.',
                'greater_than_equal_to'  => 'El monto neto no puede ser negativo.',
            ],
        ],
        'payment_method' => [
            'rules'  => 'required|max_length[40]',
            'errors' => [
                'required'   => 'El método de pago es obligatorio.',
                'max_length' => 'El método no puede exceder 40 caracteres.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[PENDING,COMPLETED,FAILED,REFUNDED]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
        'transaction_reference' => [
            'rules'  => 'permit_empty|max_length[150]',
            'errors' => [
                'max_length' => 'La referencia no puede exceder 150 caracteres.',
            ],
        ],
        'gateway' => [
            'rules'  => 'permit_empty|max_length[80]',
            'errors' => [
                'max_length' => 'La pasarela no puede exceder 80 caracteres.',
            ],
        ],
    ];

    /** Crear pasarela de pago */
    public array $gatewayCreate = [
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[80]',
            'errors' => [
                'required'   => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 80 caracteres.',
            ],
        ],
        'slug' => [
            'rules'  => 'required|min_length[3]|max_length[80]|is_unique[payment_gateways.slug]',
            'errors' => [
                'required'   => 'El slug es obligatorio.',
                'min_length' => 'El slug debe tener al menos 3 caracteres.',
                'max_length' => 'El slug no puede exceder 80 caracteres.',
                'is_unique'  => 'Este slug ya está registrado.',
            ],
        ],
        'driver' => [
            'rules'  => 'required|max_length[50]',
            'errors' => [
                'required'   => 'El driver es obligatorio.',
                'max_length' => 'El driver no puede exceder 50 caracteres.',
            ],
        ],
        'test_mode' => [
            'rules'  => 'required|in_list[0,1]',
            'errors' => [
                'required' => 'El modo de prueba es obligatorio.',
                'in_list'  => 'Modo de prueba no válido.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[ACTIVE,INACTIVE]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
        'sort_order' => [
            'rules'  => 'permit_empty|is_natural',
            'errors' => [
                'is_natural' => 'El orden debe ser un número entero.',
            ],
        ],
    ];

    /** Actualizar pasarela de pago */
    public array $gatewayUpdate = [
        'id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required' => 'ID de pasarela no proporcionado.',
            ],
        ],
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[80]',
            'errors' => [
                'required'   => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 80 caracteres.',
            ],
        ],
        'slug' => [
            'rules'  => 'required|min_length[3]|max_length[80]|is_unique[payment_gateways.slug,id,{id}]',
            'errors' => [
                'required'   => 'El slug es obligatorio.',
                'min_length' => 'El slug debe tener al menos 3 caracteres.',
                'max_length' => 'El slug no puede exceder 80 caracteres.',
                'is_unique'  => 'Este slug ya está en uso por otra pasarela.',
            ],
        ],
        'driver' => [
            'rules'  => 'required|max_length[50]',
            'errors' => [
                'required'   => 'El driver es obligatorio.',
                'max_length' => 'El driver no puede exceder 50 caracteres.',
            ],
        ],
        'test_mode' => [
            'rules'  => 'required|in_list[0,1]',
            'errors' => [
                'required' => 'El modo de prueba es obligatorio.',
                'in_list'  => 'Modo de prueba no válido.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[ACTIVE,INACTIVE]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
        'sort_order' => [
            'rules'  => 'permit_empty|is_natural',
            'errors' => [
                'is_natural' => 'El orden debe ser un número entero.',
            ],
        ],
    ];

    // ========================================================================
    // MÓDULO 8: SORTEOS Y GANADORES
    // ========================================================================

    /** Crear sorteo */
    public array $drawCreate = [
        'raffle_id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La rifa es obligatoria.',
                'is_natural_no_zero' => 'Debe seleccionar una rifa válida.',
            ],
        ],
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[150]',
            'errors' => [
                'required'   => 'El nombre del sorteo es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 150 caracteres.',
            ],
        ],
        'scheduled_at' => [
            'rules'  => 'required|valid_date[Y-m-d H:i:s]',
            'errors' => [
                'required'   => 'La fecha programada es obligatoria.',
                'valid_date' => 'La fecha no es válida (formato AAAA-MM-DD HH:MM:SS).',
            ],
        ],
        'method' => [
            'rules'  => 'required|in_list[MANUAL,RANDOM,EXTERNAL_API,BLOCKCHAIN]',
            'errors' => [
                'required' => 'El método es obligatorio.',
                'in_list'  => 'Método no válido.',
            ],
        ],
        'seed' => [
            'rules'  => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'La semilla no puede exceder 100 caracteres.',
            ],
        ],
        'external_reference' => [
            'rules'  => 'permit_empty|max_length[150]',
            'errors' => [
                'max_length' => 'La referencia externa no puede exceder 150 caracteres.',
            ],
        ],
    ];

    /** Actualizar sorteo */
    public array $drawUpdate = [
        'id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required' => 'ID de sorteo no proporcionado.',
            ],
        ],
        'raffle_id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'La rifa es obligatoria.',
                'is_natural_no_zero' => 'Debe seleccionar una rifa válida.',
            ],
        ],
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[150]',
            'errors' => [
                'required'   => 'El nombre del sorteo es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 150 caracteres.',
            ],
        ],
        'scheduled_at' => [
            'rules'  => 'required|valid_date[Y-m-d H:i:s]',
            'errors' => [
                'required'   => 'La fecha programada es obligatoria.',
                'valid_date' => 'La fecha no es válida (formato AAAA-MM-DD HH:MM:SS).',
            ],
        ],
        'method' => [
            'rules'  => 'required|in_list[MANUAL,RANDOM,EXTERNAL_API,BLOCKCHAIN]',
            'errors' => [
                'required' => 'El método es obligatorio.',
                'in_list'  => 'Método no válido.',
            ],
        ],
        'seed' => [
            'rules'  => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'La semilla no puede exceder 100 caracteres.',
            ],
        ],
        'external_reference' => [
            'rules'  => 'permit_empty|max_length[150]',
            'errors' => [
                'max_length' => 'La referencia externa no puede exceder 150 caracteres.',
            ],
        ],
    ];

    // ========================================================================
    // MÓDULO 9: NOTIFICACIONES Y EMAILS
    // ========================================================================

    /** Crear plantilla de email */
    public array $emailTemplateCreate = [
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[100]',
            'errors' => [
                'required'   => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 100 caracteres.',
            ],
        ],
        'slug' => [
            'rules'  => 'required|min_length[3]|max_length[100]|is_unique[email_templates.slug]',
            'errors' => [
                'required'   => 'El slug es obligatorio.',
                'min_length' => 'El slug debe tener al menos 3 caracteres.',
                'max_length' => 'El slug no puede exceder 100 caracteres.',
                'is_unique'  => 'Este slug ya está registrado.',
            ],
        ],
        'subject' => [
            'rules'  => 'required|min_length[3]|max_length[255]',
            'errors' => [
                'required'   => 'El asunto es obligatorio.',
                'min_length' => 'El asunto debe tener al menos 3 caracteres.',
                'max_length' => 'El asunto no puede exceder 255 caracteres.',
            ],
        ],
        'body_html' => [
            'rules'  => 'required',
            'errors' => [
                'required' => 'El cuerpo HTML es obligatorio.',
            ],
        ],
        'body_text' => [
            'rules'  => 'permit_empty',
            'errors' => [],
        ],
        'variables' => [
            'rules'  => 'permit_empty|max_length[1000]',
            'errors' => [
                'max_length' => 'Las variables no pueden exceder 1000 caracteres.',
            ],
        ],
        'is_global' => [
            'rules'  => 'required|in_list[0,1]',
            'errors' => [
                'required' => 'El tipo es obligatorio.',
                'in_list'  => 'Tipo no válido.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[ACTIVE,INACTIVE]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
    ];

    /** Actualizar plantilla de email */
    public array $emailTemplateUpdate = [
        'id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required' => 'ID de plantilla no proporcionado.',
            ],
        ],
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[100]',
            'errors' => [
                'required'   => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 100 caracteres.',
            ],
        ],
        'slug' => [
            'rules'  => 'required|min_length[3]|max_length[100]|is_unique[email_templates.slug,id,{id}]',
            'errors' => [
                'required'   => 'El slug es obligatorio.',
                'min_length' => 'El slug debe tener al menos 3 caracteres.',
                'max_length' => 'El slug no puede exceder 100 caracteres.',
                'is_unique'  => 'Este slug ya está en uso por otra plantilla.',
            ],
        ],
        'subject' => [
            'rules'  => 'required|min_length[3]|max_length[255]',
            'errors' => [
                'required'   => 'El asunto es obligatorio.',
                'min_length' => 'El asunto debe tener al menos 3 caracteres.',
                'max_length' => 'El asunto no puede exceder 255 caracteres.',
            ],
        ],
        'body_html' => [
            'rules'  => 'required',
            'errors' => [
                'required' => 'El cuerpo HTML es obligatorio.',
            ],
        ],
        'body_text' => [
            'rules'  => 'permit_empty',
            'errors' => [],
        ],
        'variables' => [
            'rules'  => 'permit_empty|max_length[1000]',
            'errors' => [
                'max_length' => 'Las variables no pueden exceder 1000 caracteres.',
            ],
        ],
        'is_global' => [
            'rules'  => 'required|in_list[0,1]',
            'errors' => [
                'required' => 'El tipo es obligatorio.',
                'in_list'  => 'Tipo no válido.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[ACTIVE,INACTIVE]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
    ];

    // ========================================================================
    // MÓDULO: ROLES Y PERMISOS
    // ========================================================================

    /** Crear rol */
    public array $roleCreate = [
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[80]|is_unique[roles.name]',
            'errors' => [
                'required'   => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 80 caracteres.',
                'is_unique'  => 'Este nombre ya está registrado.',
            ],
        ],
        'description' => [
            'rules'  => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'La descripción no puede exceder 255 caracteres.',
            ],
        ],
        'scope' => [
            'rules'  => 'required|in_list[COMPANY,SYSTEM]',
            'errors' => [
                'required' => 'El alcance es obligatorio.',
                'in_list'  => 'Alcance no válido.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[ACTIVE,INACTIVE]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
    ];

    /** Actualizar rol */
    public array $roleUpdate = [
        'id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required' => 'ID de rol no proporcionado.',
            ],
        ],
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[80]|is_unique[roles.name,id,{id}]',
            'errors' => [
                'required'   => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 80 caracteres.',
                'is_unique'  => 'Este nombre ya está en uso por otro rol.',
            ],
        ],
        'description' => [
            'rules'  => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'La descripción no puede exceder 255 caracteres.',
            ],
        ],
        'scope' => [
            'rules'  => 'required|in_list[COMPANY,SYSTEM]',
            'errors' => [
                'required' => 'El alcance es obligatorio.',
                'in_list'  => 'Alcance no válido.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[ACTIVE,INACTIVE]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
    ];

    /** Crear permiso */
    public array $permissionCreate = [
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[120]|is_unique[permissions.name]',
            'errors' => [
                'required'   => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 120 caracteres.',
                'is_unique'  => 'Este nombre ya está registrado.',
            ],
        ],
        'module' => [
            'rules'  => 'required|min_length[2]|max_length[80]',
            'errors' => [
                'required'   => 'El módulo es obligatorio.',
                'min_length' => 'El módulo debe tener al menos 2 caracteres.',
                'max_length' => 'El módulo no puede exceder 80 caracteres.',
            ],
        ],
        'action' => [
            'rules'  => 'required|min_length[2]|max_length[80]',
            'errors' => [
                'required'   => 'La acción es obligatoria.',
                'min_length' => 'La acción debe tener al menos 2 caracteres.',
                'max_length' => 'La acción no puede exceder 80 caracteres.',
            ],
        ],
        'description' => [
            'rules'  => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'La descripción no puede exceder 255 caracteres.',
            ],
        ],
        'guard_name' => [
            'rules'  => 'required|max_length[20]',
            'errors' => [
                'required'   => 'El guard es obligatorio.',
                'max_length' => 'El guard no puede exceder 20 caracteres.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[ACTIVE,INACTIVE]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
    ];

    /** Actualizar permiso */
    public array $permissionUpdate = [
        'id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required' => 'ID de permiso no proporcionado.',
            ],
        ],
        'name' => [
            'rules'  => 'required|min_length[3]|max_length[120]|is_unique[permissions.name,id,{id}]',
            'errors' => [
                'required'   => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede exceder 120 caracteres.',
                'is_unique'  => 'Este nombre ya está en uso por otro permiso.',
            ],
        ],
        'module' => [
            'rules'  => 'required|min_length[2]|max_length[80]',
            'errors' => [
                'required'   => 'El módulo es obligatorio.',
                'min_length' => 'El módulo debe tener al menos 2 caracteres.',
                'max_length' => 'El módulo no puede exceder 80 caracteres.',
            ],
        ],
        'action' => [
            'rules'  => 'required|min_length[2]|max_length[80]',
            'errors' => [
                'required'   => 'La acción es obligatoria.',
                'min_length' => 'La acción debe tener al menos 2 caracteres.',
                'max_length' => 'La acción no puede exceder 80 caracteres.',
            ],
        ],
        'description' => [
            'rules'  => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'La descripción no puede exceder 255 caracteres.',
            ],
        ],
        'guard_name' => [
            'rules'  => 'required|max_length[20]',
            'errors' => [
                'required'   => 'El guard es obligatorio.',
                'max_length' => 'El guard no puede exceder 20 caracteres.',
            ],
        ],
        'status' => [
            'rules'  => 'required|in_list[ACTIVE,INACTIVE]',
            'errors' => [
                'required' => 'El estado es obligatorio.',
                'in_list'  => 'Estado no válido.',
            ],
        ],
    ];
}
