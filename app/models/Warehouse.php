<?php
class Warehouse
{
    public int $id;
    public string $name;
    public string $address;
    public int $endowment;
    public ?DateTime $created_at;
    public ?DateTime $updated_at;
    public bool $is_active;

    public function __construct($data = [])
    {
        $this->id        = $data['id'] ?? null;
        $this->name      = $data['name'] ?? '';
        $this->address   = $data['address'] ?? '';
        $this->endowment = $data['endowment'] ?? 0;
        $this->created_at = isset($data['created_at']) ? new DateTime($data['created_at']) : null;
        $this->updated_at = isset($data['updated_at']) ? new DateTime($data['updated_at']) : null;
        $this->is_active = $data['is_active'] ?? true;
    }
}