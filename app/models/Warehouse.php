<?php
class Warehouse
{
    public $id;
    public $name;
    public $address;
    public $endowment;
    public $created_at;
    public $updated_at;
    public $is_active;

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