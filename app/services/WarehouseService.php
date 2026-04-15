<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Warehouse.php';
require_once __DIR__ . '/../validators/WarehouseValidator.php';

/**
 * Servicio que gestiona la lógica de negocio de las bodegas.
 */
class WareHouseService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Obtiene todas las bodegas con sus encargados.
     * 
     * @param string|null $status Filtra por estado: 'active', 'inactive' o null para todas
     * @return array Lista de bodegas
     */
    public function getAllWarehouses(?string $status = null): array
    {
        $query = "SELECT w.*, 
    string_agg(CONCAT(u.first_name, ' ', u.last_name, ' ', u.second_last_name), ', ') as manager_name
FROM warehouses w
LEFT JOIN warehouse_user wu ON w.id = wu.warehouse_id
LEFT JOIN users u ON wu.user_id = u.id";

        if ($status === 'active') {
            $query .= " WHERE w.is_active = true";
        } elseif ($status === 'inactive') {
            $query .= " WHERE w.is_active = false";
        }

        $query .= " GROUP BY w.id ORDER BY w.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $warehouses = [];
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $warehouses[] = $row; 
        }

        return $warehouses;
    }

    /**
     * Crea una nueva bodega en el sistema.
     * 
     * @param array $data Datos de la bodega a crear
     * @return Warehouse Bodega creada
     * @throws Exception Si hay errores de validación o la bodega ya existe
     */
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

            $isActive = $this->toBool($data['is_active'] ?? true);
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_STR);
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':address', $data['address'], PDO::PARAM_STR);
            $stmt->bindValue(':endowment', (int)$data['endowment'], PDO::PARAM_INT);
            $stmt->bindValue(':is_active', $isActive, PDO::PARAM_BOOL);
            $stmt->execute();

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

    /**
     * Actualiza una bodega existente.
     * 
     * @param string $id ID de la bodega a actualizar
     * @param array $data Nuevos datos de la bodega
     * @return Warehouse Bodega actualizada
     * @throws Exception Si la bodega no existe o hay errores de validación
     */
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

            $isActive = $this->toBool($data['is_active'] ?? true);
            $stmt->bindValue(':id', $id, PDO::PARAM_STR);
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':address', $data['address'], PDO::PARAM_STR);
            $stmt->bindValue(':endowment', (int)$data['endowment'], PDO::PARAM_INT);
            $stmt->bindValue(':is_active', $isActive, PDO::PARAM_BOOL);
            $stmt->execute();

            if (isset($data['manager_ids'])) {
                $this->db->prepare("DELETE FROM warehouse_user WHERE warehouse_id = :id")
                    ->execute(['id' => $id]);
                if (!empty($data['manager_ids'])) {
                    $this->associateManagers($id, $data['manager_ids']);
                }
            }


            $this->db->commit();

            return $this->getWarehouseById($id);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to update warehouse: " . $e->getMessage());
        }
    }

    /**
     * Elimina una bodega del sistema.
     * 
     * @param string $id ID de la bodega a eliminar
     * @return void
     * @throws Exception Si la bodega no existe o hay errores al eliminar
     */
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

    /**
     * Obtiene los encargados de una bodega específica.
     * 
     * @param string $warehouseId ID de la bodega
     * @return array Lista de encargados
     */
    public function getWarehouseManagers(string $warehouseId): array
    {
        $query = "SELECT u.* FROM users u
                  JOIN warehouse_user wu ON u.id = wu.user_id
                  WHERE wu.warehouse_id = :warehouse_id";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['warehouse_id' => $warehouseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los IDs de los encargados de una bodega.
     * 
     * @param string $warehouseId ID de la bodega
     * @return array Lista de IDs de encargados
     */
    public function getWarehouseManagerIds(string $warehouseId): array
    {
        $query = "SELECT user_id FROM warehouse_user WHERE warehouse_id = :warehouse_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['warehouse_id' => $warehouseId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'user_id');
    }

    /**
     * Obtiene una bodega por su ID.
     * 
     * @param string $id ID de la bodega
     * @return Warehouse|null Bodega encontrada o null si no existe
     */
    public function getWarehouseById(string $id): ?Warehouse
    {
        $query = "SELECT * FROM warehouses WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Warehouse($data) : null;
    }

    /**
     * Asocia encargados a una bodega en la tabla intermedia.
     * 
     * @param string $warehouseId ID de la bodega
     * @param array $managerIds IDs de los encargados a asociar
     * @return void
     */
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

    /**
     * Convierte un valor a tipo booleano.
     * 
     * @param mixed $value Valor a convertir
     * @return bool Valor booleano resultante
     */
    private function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if ($value === 'true' || $value === '1' || $value === 1) {
            return true;
        }
        if ($value === 'false' || $value === '0' || $value === 0 || $value === '' || $value === null) {
            return false;
        }
        return (bool)$value;
    }
}
