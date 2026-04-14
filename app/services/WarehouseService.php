<?php

use Database;
use Warehouse;
use WarehouseValidator;

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Warehouse.php';
require_once __DIR__ . '/../validators/WarehouseValidator.php';

class WareHouseService

{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }
    public function getAllWarehouses(?string $status = null): array
    {
        $query = "SELECT w.*, 
                     CONCAT(u.first_name, ' ', u.last_name, ' ', u.second_last_name) as manager_name
              FROM warehouses w
              LEFT JOIN warehouse_user wu ON w.id = wu.warehouse_id
              LEFT JOIN users u ON wu.user_id = u.id";

        if ($status === 'active') {
            $query .= " WHERE w.is_active = true";
        } elseif ($status === 'inactive') {
            $query .= " WHERE w.is_active = false";
        }

        $query .= " ORDER BY w.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $warehouses = [];
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $warehouses[] = new Warehouse($row);
        }

        return $warehouses;
    }

    public function createWarehouse(array $data): Warehouse
    {
        WarehouseValidator::validateOrFail($data, true);

        if ($this->getWarehouseById($data['id'])) {
            throw new Exception("Warehouse with ID '{$data['id']}' already exists");
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                "INSERT INTO warehouses (id, name, address, endowment, is_active, created_at, updated_at)
                 VALUES (:id, :name, :address, :endowment, :is_active, NOW(), NOW())"
            );

            $stmt->execute([
                'id' => $data['id'],
                'name' => $data['name'],
                'address' => $data['address'],
                'endowment' => $data['endowment'],
                'is_active' => $data['is_active'] ?? true
            ]);

            if (!empty($data['manager_ids'])) {
                $this->associateManagers($data['id'], $data['manager_ids']);
            }

            $this->db->commit();

            return $this->getWarehouseById($data['id']);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to create warehouse: " . $e->getMessage());
        }
    }
    public function updateWarehouse(string $id, array $data): Warehouse
    {
        $wareHouse = $this->getWarehouseById($id);

        if (!$wareHouse) {
            throw new Exception("Warehouse with ID '{$id}' not found");
        }

        WarehouseValidator::validateOrFail($data, false);

        try {
            $this->db->beginTransaction();

            $query =                 "UPDATE warehouses 
             SET name = :name, address = :address, endowment = :endowment, 
                 is_active = :is_active, updated_at = NOW() 
             WHERE id = :id";

            $stmt = $this->db->prepare($query);

            $stmt->execute([
                'id' => $id,
                'name' => $data['name'],
                'address' => $data['address'],
                'endowment' => $data['endowment'],
                'is_active' => $data['is_active'] ?? true
            ]);

            if (!empty($data['manager_ids'])) {
                $this->db->prepare("DELETE FROM warehouse_user WHERE warehouse_id = :id")
                    ->execute(['id' => $id]);
                $this->associateManagers($id, $data['manager_ids']);
            }


            $this->db->commit();

            return $this->getWarehouseById($id);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to update warehouse: " . $e->getMessage());
        }
    }
    public function deleteWarehouse(string $id): void
    {
        $wareHouse = $this->getWarehouseById($id);

        if (!$wareHouse) {
            throw new Exception("Warehouse with ID '{$id}' not found");
        }

        try {
            $this->db->beginTransaction();

            $this->db->prepare("DELETE FROM warehouse_user WHERE warehouse_id = :id")
                ->execute(['id' => $id]);

            $this->db->prepare("DELETE FROM warehouses WHERE id = :id")
                ->execute(['id' => $id]);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to delete warehouse: " . $e->getMessage());
        }
    }

    public function getWarehouseById(string $id): ?Warehouse
    {
        $query = "SELECT * FROM warehouses WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Warehouse($data) : null;
    }

    private function associateManagers(string $warehouseId, array $managerIds): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO warehouse_user (warehouse_id, user_id) VALUES (:warehouse_id, :user_id)"
        );
        foreach ($managerIds as $managerId) {
            $stmt->execute([
                'warehouse_id' => $warehouseId,
                'user_id' => $managerId
            ]);
        }
    }
}
