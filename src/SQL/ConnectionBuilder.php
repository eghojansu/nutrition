<?php

namespace Nutrition\SQL;

use Base;
use DB\SQL;
use Exception;
use InvalidArgumentException;
use PDO;
use Prefab;
use RuntimeException;

/**
 * Build DB\SQL instance
 */
class ConnectionBuilder extends Prefab
{
    const E_CONNECT = 'Could not connect to database with current configuration!';
    const E_CONFIG = 'Invalid database configuration!';

    /** @var DB\SQL */
    public $conn;

    /** @var array */
    public $config;


    /**
     * class constructor, prepare configuration
     * @param array|null $config
     */
    public function __construct(array $config = null)
    {
        $this->config = $config ?? Base::instance()->get('DATABASE') ?? [];
    }

    /**
     * Get database configuration
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function getConfig($key = null, $default = null)
    {
        if ($key) {
            return array_key_exists($key, $this->config) ? $this->config[$key] : $default;
        }

        return $this->config;
    }

    /**
     * Set database configuration
     * @param  mixed $key
     * @param  mixed $default
     * @return $this
     */
    public function setConfig($key = null, $value = null)
    {
        if (is_array($key)) {
            $this->config = $key;
        } else {
            $this->config[$key] = $value;
        }

        return $this;
    }

    /**
     * Get DB\SQL instance
     * @return DB\SQL
     */
    public function getConnection()
    {
        if (null === $this->conn) {
            $this->checkConfiguration();

            try {
                $this->conn = new SQL(
                    $this->buildDsn(),
                    $this->config['username'],
                    $this->config['password']
                );
            } catch (Exception $e) {
                throw new RuntimeException(self::E_CONNECT);
            }
        }

        return $this->conn;
    }

    /**
     * Get PDO without database name
     * @return PDO
     */
    public function pdoWithoutDB()
    {
        $this->checkConfiguration();

        try {
            return new PDO(
                $this->buildDsn(false),
                $this->config['username'],
                $this->config['password']
            );
        } catch (Exception $e) {
            throw new RuntimeException(self::E_CONNECT);
        }
    }

    /**
     * Get table status percentage
     * @return float
     */
    public function getStatus()
    {
        $tables = count($this->getTables());
        $unhealth = count($this->getUnhealthyTable());

        if ($tables) {
            $healthy = $tables - $unhealth;

            return ($healthy / $tables) * 100;
        }

        return 100;
    }

    /**
     * Perform table checking
     * @param  array  $tables
     * @return array  CHECK TABLES result
     */
    public function checkTables(array $tables)
    {
        $check = implode(',', $tables);

        $result = $this->getConnection()->pdo()->query("CHECK TABLE $check");

        return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : $result;
    }

    /**
     * Perform table repair
     * @param  array  $tables
     * @return array  CHECK TABLES result
     */
    public function repairTables(array $tables)
    {
        $check = implode(',', $tables);

        $result = $this->getConnection()->pdo()->query("REPAIR TABLE $check");

        return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : $result;
    }

    /**
     * Get unhealthy table
     * @return array
     */
    public function getUnhealthyTable()
    {
        $app = Base::instance();
        $key = 'CACHE.db_unhealthy';
        if ($app->devoid($key)) {
            $tables = $this->getTables();
            $unhealthy = [];

            if ($tables) {
                foreach ($this->checkTables($tables) as $result) {
                    if (!in_array($result['Msg_text'], ['OK','Table is already up to date'])) {
                        $unhealthy[] = $result['Table'];
                    }
                }
            }

            $app->set($key, $unhealthy, 60);
        }

        return $app->get($key);
    }

    /**
     * Check if db need repair
     * @return bool
     */
    public function isHealthy()
    {
        return $this->getStatus() >= 100;
    }

    /**
     * Get database size (from information_schema)
     * @return float
     */
    public function getSize()
    {
        $app = Base::instance();
        $key = 'CACHE.db_size';
        if ($app->devoid($key)) {
            $sql = 'SELECT SUM(ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024 ), 2)) AS mb_size'.
                ' FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?';
            $query = $this->getConnection()->pdo()->prepare($sql);
            $query->execute([$this->getConfig('name')]);
            $result = $query->fetch(PDO::FETCH_ASSOC);

            $app->set($key, $result ? $result['mb_size'] : 0, 60);
        }

        return $app->get($key);
    }

    /**
     * Get tables
     * @return array
     */
    public function getTables()
    {
        $app = Base::instance();
        $key = 'CACHE.db_tables';
        if ($app->devoid($key)) {
            $result = $this->getConnection()->pdo()
                ->query('SHOW TABLES')->fetchAll(PDO::FETCH_ASSOC);
            $tables = [];
            foreach ($result as $table) {
                $tables[] = array_pop($table);
            }

            $app->set($key, $tables, 60);
        }

        return $app->get($key);
    }

    /**
     * Build dsn string based on config
     * @param  boolean $withDatabase
     * @return string
     */
    protected function buildDsn($withDatabase = true)
    {
        $dsn = 'mysql:host='.$this->config['host'];
        if ($withDatabase) {
            $dsn .= ';dbname='.$this->config['name'];
        }
        if (!empty($this->config['port'])) {
            $dsn .= ';port='.$this->config['port'];
        }

        return $dsn;
    }

    /**
     * Check configuration
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected function checkConfiguration()
    {
        if (
            empty($this->config['name'])
            || empty($this->config['username'])
            || empty($this->config['host'])
        ) {
            throw new InvalidArgumentException(self::E_CONFIG);
        }
    }
}
