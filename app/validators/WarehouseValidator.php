<?php

/**
 * Validador para los datos de bodegas.
 */
class WarehouseValidator
{
    /**
     * Valida los datos de una bodega.
     * 
     * @param array $data Datos a validar
     * @param bool $requireAll Si es true, todos los campos son requeridos
     * @return array Array de errores de validación (vacío si no hay errores)
     */
    public static function validate(array $data, bool $requireAll = true): array
    {
        $errors = [];

        if ($requireAll || isset($data['id'])) {
            if (empty($data['id'])) {
                $errors['id'] = "Warehouse ID is required";
            } elseif (!preg_match('/^[a-zA-Z0-9]{1,5}$/', $data['id'])) {
                $errors['id'] = "Warehouse ID must be alphanumeric and up to 5 characters";
            }
        }

        if ($requireAll || isset($data['name'])) {
            if (empty($data['name'])) {
                $errors['name'] = "Warehouse name is required";
            } elseif (strlen($data['name']) > 100) {
                $errors['name'] = "Warehouse name must not exceed 100 characters";
            }
        }

        if ($requireAll || isset($data['address'])) {
            if (empty($data['address'])) {
                $errors['address'] = "Warehouse address is required";
            }
        }

        if ($requireAll || isset($data['endowment'])) {
            if (!isset($data['endowment']) || $data['endowment'] === '') {
                $errors['endowment'] = "Endowment is required";
            } elseif (!is_numeric($data['endowment'])) {
                $errors['endowment'] = "Endowment must be a number";
            } elseif ($data['endowment'] < 0) {
                $errors['endowment'] = "Endowment must be a positive number";
            }
        }

        return $errors;
    }

    /**
     * Valida los datos de una bodega y lanza una excepción si hay errores.
     * 
     * @param array $data Datos a validar
     * @param bool $requireAll Si es true, todos los campos son requeridos
     * @throws Exception Si la validación falla
     * @return void
     */
    public static function validateOrFail(array $data, bool $requireAll = true): void
    {
        $errors = self::validate($data, $requireAll);
        
        if (!empty($errors)) {
            $errorMessages = implode(', ', $errors);
            throw new Exception("Validation failed: " . $errorMessages);
        }
    }
}