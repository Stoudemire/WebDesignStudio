
<?php

class Database {
    private $pdo;
    private $config;
    private static $instance = null;

    public function __construct($configFile = null) {
        if ($configFile && file_exists($configFile)) {
            $this->config = include $configFile;
            if (!isset($this->config['database'])) {
                throw new Exception('Database configuration section not found in config file');
            }
            $dbConfig = $this->config['database'];
        } else {
            // Default configuration for MySQL with optimizations
            $dbConfig = [
                'host' => 'localhost',
                'dbname' => 'reino_habbo',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                'options' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => true, // Enable persistent connections
                    PDO::ATTR_TIMEOUT => 30,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            ];
        }

        $this->connect($dbConfig);
    }

    // Singleton pattern for better connection management
    public static function getInstance($configFile = null) {
        if (self::$instance === null) {
            self::$instance = new self($configFile);
        }
        return self::$instance;
    }

    private function connect($dbConfig) {
        // Build DSN for MySQL with optimizations
        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=%s;port=3306",
            $dbConfig['host'],
            $dbConfig['dbname'],
            $dbConfig['charset']
        );

        try {
            $this->pdo = new PDO(
                $dsn,
                $dbConfig['username'],
                $dbConfig['password'],
                $dbConfig['options']
            );

            // Optimize MySQL settings for better performance
            $this->pdo->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
            $this->pdo->exec("SET SESSION time_zone = '+00:00'");
            $this->pdo->exec("SET SESSION autocommit = 1");
            
            // Enable query cache if available (ignore errors if not supported)
            try {
                $this->pdo->exec("SET SESSION query_cache_type = ON");
            } catch (PDOException $e) {
                // Ignore query cache errors as it's not critical
            }
            
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            
            // More specific error messages
            if (strpos($e->getMessage(), 'Access denied') !== false) {
                throw new Exception('Acceso denegado a la base de datos. Verifica las credenciales.');
            } elseif (strpos($e->getMessage(), 'Connection refused') !== false) {
                throw new Exception('No se puede conectar al servidor MySQL. Verifica que esté ejecutándose.');
            } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
                throw new Exception('Base de datos "reino_habbo" no encontrada. Debes importar el schema SQL primero.');
            } else {
                throw new Exception('Error de conexión a la base de datos: ' . $e->getMessage());
            }
        }
    }

    public function getConnection() {
        // Check if connection is still alive
        try {
            $this->pdo->query('SELECT 1');
        } catch (PDOException $e) {
            // Reconnect if connection lost
            $this->connect($this->config['database'] ?? []);
        }
        
        return $this->pdo;
    }

    public function testConnection() {
        try {
            $stmt = $this->pdo->query('SELECT 1 as test');
            return $stmt->fetchColumn() === 1;
        } catch (PDOException $e) {
            error_log('Database test failed: ' . $e->getMessage());
            return false;
        }
    }

    // Prepared statement cache for better performance
    private $statementCache = [];

    public function prepare($sql) {
        $hash = md5($sql);
        
        if (!isset($this->statementCache[$hash])) {
            $this->statementCache[$hash] = $this->pdo->prepare($sql);
        }
        
        return $this->statementCache[$hash];
    }

    // Transaction helpers
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollback() {
        return $this->pdo->rollback();
    }

    // Optimized query methods
    public function query($sql, $params = []) {
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Database query failed: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw $e;
        }
    }

    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function fetchColumn($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn();
    }

    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    // Clean up resources
    public function __destruct() {
        $this->statementCache = [];
        $this->pdo = null;
    }
}
?>
