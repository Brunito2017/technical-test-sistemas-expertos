<?php

/**
 * Clase para gestionar la conexión a la base de datos PostgreSQL.
 */
class Database
{
	private static $host = 'localhost';
	private static $port = '5432';
	private static $dbname = 'houseware_db'; 
	private static $user = 'admin';    
	private static $password = 'xa31_S'; 
	private static $pdo = null;

	/**
	 * Obtiene la conexión a la base de datos (singleton).
	 * 
	 * @return \PDO Instancia de la conexión PDO
	 */
	public static function getConnection(): \PDO
	{
		if (self::$pdo === null) {
			$dsn = sprintf(
				'pgsql:host=%s;port=%s;dbname=%s',
				self::$host,
				self::$port,
				self::$dbname
			);
			try {
				self::$pdo = new \PDO($dsn, self::$user, self::$password, [
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
					\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
				]);
			} catch (\PDOException $e) {
				die('Error de conexión a la base de datos: ' . $e->getMessage());
			}
		}
		return self::$pdo;
	}
}