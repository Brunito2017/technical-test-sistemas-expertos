<?php

require_once __DIR__ . '/../services/WareHouseService.php';

class WarehouseController
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
    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $warehouse = $this->warehouseService->createWarehouse($data);
            echo json_encode($warehouse);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    public function update(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de bodega requerido']);
            return;
        }
        try {
            $warehouse = $this->warehouseService->updateWarehouse($data['id'], $data);
            echo json_encode(['success' => true, 'data' => $warehouse]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function delete(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de bodega requerido']);
            return;
        }
        try {
            $this->warehouseService->deleteWarehouse($data['id']);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    public function show(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            return;
        }
        $warehouse = $this->warehouseService->getWarehouseById($id);
        if (!$warehouse) {
            http_response_code(404);
            echo json_encode(['error' => 'Bodega no encontrada']);
            return;
        }
        $managerIds = $this->warehouseService->getWarehouseManagerIds($id);
        $warehouseData = get_object_vars($warehouse);
        $warehouseData['manager_ids'] = $managerIds;
        echo json_encode(['data' => $warehouseData]);
    }
}
