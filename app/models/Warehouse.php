<?php

/**
 * Modelo de bodega que representa las bodegas del sistema.
 */
class Warehouse
{
    public $id;
    public $name;
    public $address;
    public $endowment;
    public $created_at;
    public $updated_at;
    public $is_active;

    /**
     * Constructor del modelo Warehouse.
     * 
     * @param array $data Datos de la bodega para inicializar el modelo
     */
    public function __construct($data = [])
    {
        $this->id        = $data['id'] ?? null;
        $this->name      = $data['name'] ?? '';
        $this->address   = $data['address'] ?? '';
        $this->endowment = $data['endowment'] ?? 0;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        $this->is_active = $data['is_active'] ?? true;
    }
}