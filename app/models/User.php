<?php

/**
 * Modelo de usuario que representa a los encargados de bodegas.
 */
class User
{
    public $id;
    public $run;
    public $first_name;
    public $last_name;
    public $second_last_name;
    public $address;
    public $phone;
    public $role;
    public $created_at;
    public $updated_at;

    /**
     * Constructor del modelo User.
     * 
     * @param array $data Datos del usuario para inicializar el modelo
     */
    public function __construct($data = [])
    {
        $this->id                = $data['id'] ?? null;
        $this->run               = $data['run'] ?? '';
        $this->first_name        = $data['first_name'] ?? '';
        $this->last_name         = $data['last_name'] ?? '';
        $this->second_last_name  = $data['second_last_name'] ?? '';
        $this->address           = $data['address'] ?? '';
        $this->phone             = $data['phone'] ?? '';
        $this->role              = $data['role'] ?? 'manager';
        $this->created_at        = $data['created_at'] ?? null;
        $this->updated_at        = $data['updated_at'] ?? null;
    }
}