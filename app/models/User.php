<?php

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