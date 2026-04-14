<?php

namespace App\Controllers;

require_once __DIR__ . '/../services/WareHouseService.php';

class WareHouseController
{
    private $warehouseService;

    public function __construct()
    {
        $this->warehouseService = new WareHouseService();
    }

    public function index(): void
    {
        $wareHouses = $this->warehouseService->getAllWarehouses();

        echo json_encode($wareHouses);
    }
}