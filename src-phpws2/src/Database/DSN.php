<?php

namespace phpws2\Database;
use \Canopy\Translation;

/**
 * The DSN object stores information used to create a database connection.
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @package phpws2
 * @subpackage DB
 * @license http://opensource.org/licenses/lgpl-3.0.html
 */
class DSN extends \Canopy\Data {

    /**
     * Type of database engine
     * @var string
     */
    protected $database_type;

    /**
     * Name of the database
     * @var Variable\StringVar
     */
    protected $database_name;

    /**
     * Username that has access to the database
     * @var Variable\StringVar
     */
    protected $username;

    /**
     * Password used to give user name access
     * @var Variable\StringVar
     */
    protected $password;

    /**
     * Host database lives on
     * @var Variable\StringVar
     */
    protected $host;

    /**
     * Port of access for database
     * @var Variable\Integer
     */
    protected $port;

    /**
     * Construction of object initializes a connection resource to the link
     * variable
     * @param string $dsn
     */
    public function __construct($database_type, $username, $password = null, $database_name = null, $host = null, $port = null)
    {
        $this->database_type = \phpws2\Variable::factory('StringVar', $database_type,
                        'database_type');
        $this->database_type->setLimit(12);
        $this->database_type->setLabel(Translation::t('Database type'));
        $this->database_type->wordCharactersOnly();

        $this->database_name = \phpws2\Variable::factory('StringVar', $database_name,
                        'database_name');
        $this->database_name->setLimit(58);
        $this->database_name->setLabel(Translation::t('Database name'));
        $this->database_name->wordCharactersOnly();

        $this->username = \phpws2\Variable::factory('StringVar', $username, 'username');
        $this->username->setLimit(255);
        $this->username->setLabel(Translation::t('Database user name'));
        $this->username->wordCharactersOnly();

        $this->password = \phpws2\Variable::factory('StringVar', $password, 'password');
        $this->password->setLimit(255);
        $this->password->setLabel(Translation::t('Database password'));
        $this->password->setInputType('password');

        $this->host = \phpws2\Variable::factory('StringVar', null, 'host');
        $this->host->setLimit(255);
        $this->host->setLabel('Database host');
        $this->host->allowNull(true);
        $this->host->set($host);

        $this->port = \phpws2\Variable::factory('integer', null, 'port');
        $this->port->setLabel('Database port');
        $this->port->setRange('1', '65535');
        $this->port->allowNull(true);
        if (!empty($port)) {
            $this->port->set($port);
        }
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getDatabaseType()
    {
        return $this->database_type;
    }

    public function getDatabaseName()
    {
        return (string) $this->database_name;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    /**
     * Returns a string formatted for a database connection
     *
     * @return string The DSN format needed by the PDO object
     */
    public function getPDOString()
    {
        $dbtype = $this->database_type->get();
        if ($dbtype == 'mysqli') {
            $dbtype = 'mysql';
        }
        $pdo_string[] = $dbtype . ':';
        if (!$this->host->isEmpty()) {
            $pdo_string[] = 'host=' . $this->host . ';';
        }

        if (!$this->port->isEmpty()) {
            $pdo_string[] = 'port=' . $this->port . ';';
        }

        if (!$this->database_name->isEmpty()) {
            $pdo_string[] = 'dbname=' . $this->database_name;
        }

        return implode('', $pdo_string);
    }

    public function setUsername($username)
    {
        $this->username->set($username);
    }

    public function setPassword($password)
    {
        $this->password->set($password);
    }

    public function setDatabaseName($database_name)
    {
        $this->database_name->set($database_name);
    }

    public function setDatabaseType($database_type)
    {
        $this->database_type->set($database_type);
    }

    public function setHost($host)
    {
        $this->host->set($host);
    }

    public function setPort($port)
    {
        $this->port->set($port);
    }

}
