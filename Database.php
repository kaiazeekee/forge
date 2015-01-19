<?php
/******************************************************************************\
+------------------------------------------------------------------------------+
| Foonster Publishing Software                                                 |
| Copyright (c) 2002 Foonster Technology                                       |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
|                                                                              |
| OWNERSHIP. The Software and all modifications or enhancements to, or         |
| derivative works based on the Software, whether created by Foonster          |
| Technology or you, and all copyrights, patents, trade secrets, trademarks    |
| and other intellectual property rights protecting or pertaining to any       |
| aspect of the Software or any such modification, enhancement or derivative   |
| work are and shall remain the sole and exclusive property of Foonster        |
| Technology.                                                                  |
|                                                                              |
| LIMITED RIGHTS. Pursuant to this Agreement, you may: (a) use the Software    |
| on one website only, for purposes of running one website only. You must      |
| provide Foonster Technology with exact URL (Unique Resource Locator) of the  |
| website you install the Software to; (b) modify the Software and/or merge    |
| it into another program; c) transfer the Software and license to another     |
| party if the other party agrees to accept the terms and conditions of this   |
| Agreement.                                                                   |
|                                                                              |
| Except as expressly set forth in this Agreement, you have no right to use,   |
| make, sublicense, modify, transfer or copy either the original or any copies |
| of the Software or to permit anyone else to do so. You may not allow any     |
| third party to use or have access to the Software. It is illegal to copy the |
| Software and install that single program for simultaneous use on multiple    |
| machines.                                                                    |
|                                                                              |
| PROPRIETARY NOTICES. You may not remove, disable, modify, or tamper with     |
| any copyright, trademark or other proprietary notices and legends contained  |
| within the code of the Software.                                             |
|                                                                              |
| COPIES.  "CUSTOMER" will be entitled to make a reasonable number of          |
| machine-readable copies of the Software for backup or archival purposes.     |
|                                                                              |
| LICENSE RESTRICTIONS. "CUSTOMER" agrees that you will not itself, or through |
| any parent, subsidiary, affiliate, agent or other third party:               |
|(a) sell, lease, license or sub-license the Software or the Documentation;    |
|(b) decompile, disassemble, or reverse engineer the Software, the Database,   |
| in whole or in part; (c) write or develop any derivative software or any     |
| other software program based upon the Software or any Confidential           |
| Information, | except pursuant to authorized Use of Software, if any; (d) use|
| the Software to provide services on a 'service bureau' basis; or (e) provide,|
| disclose, | divulge or make available to, or permit use of the Software by   |
| any unauthorized third party without Foonster Technology's prior written     |
| consent.                                                                     |
|                                                                              |
+------------------------------------------------------------------------------+
\******************************************************************************/
namespace foonster\forge;

class Database
{
    private $dbh;
    private $database = '';
    private $dbUser = '';
    private $dbPass = '';
    private $dsn = 'mysql:host=localhost';
    private $lastCommandError = false;
    private $connectionError = false;
    private $errorMessage = false;
    private $databases = array();

    /**
     * [__construct]
     * @param string $database   [the database name]
     * @param string $user       [the user associated with the database connection]
     * @param string $pass       [the password associated with the database connection]
     * @param string $dsn 
     *                           [The Data Source Name, or DSN, contains the information required to connect to
     *                           the database. ]
     */
    public function __construct($database = null, $user = null , $pass = null, $dsn = null)
    {
        if (!empty($database)) {
            $this->database = $database;
            !empty($user) ? $this->dbUser = $user : false;
            !empty($pass) ? $this->dbPass = $pass : false;
            !empty($dsn) ? $this->dsn = $dsn : false;
            $this->connect();
        }
    }

    /**
     * [__destruct]
     */
    public function __destruct()
    {
        
    }

    /**
     * [buildQuery]
     * @param  string $type        [the table name to use as a template to build query]
     * @param  [type] $table       [what type of CRUD operation is occuring]
     * @param  array  $variables   [list of variables to include in the record update]
     * @param  array  $constraints [list of constraining variables]
     * @param  string $limit       [limit on returned or impacted records]
     * @return string              [A PDO acceptable query string]
     */
    public function buildQuery(
        $table, 
        $type = 'INSERT', 
        $variables = array(), 
        $constraints = array(), 
        $limit = '1')
    {

        $sql = '';
        $fields = $values = $updates = $columns = array();
        $type = strtoupper(trim($type));
        $sth = $this->_dbh->prepare('DESCRIBE ' . $table);

        $sth->execute();

        $limit > 0 ? $limit = " LIMIT $limit" : $limit = '';

        $tableinfo = $sth->fetchAll(\PDO::FETCH_ASSOC);
        // add field names by name
        foreach ($tableinfo as $key => $array) {
            strtoupper($array['Key']) == 'PRI' ? $primary = $array['Field'] : false;
            $columns[$array['Field']] = $array;
        }

        if ($type == 'SELECT') {
            foreach ($columns as $key => $array) {
                if (array_key_exists($array['Field'], $variables) && !empty($variables[ $array['Field'] ])) {
                    $updates[] = $array['Field'] . " = :$array[Field]";
                }
            }
            $sql = "SELECT FROM $table WHERE " . implode(' AND ', $updates) . "$limit";
        } elseif ($type == 'DELETE') {
            foreach ($columns as $key => $array) {
                if (array_key_exists($array['Field'], $variables) && !empty( $variables[ $array['Field'] ] )) {
                    $updates[] = $array['Field'] . " = :$array[Field]";
                }
            }
            $sql = "DELETE FROM $table WHERE " . implode(' AND ', $updates) . "$limit";
        } elseif ($type == 'UPDATE') {
            foreach ($columns as $key => $array) {
                if (array_key_exists($array['Field'], $variables) && $primary != $array['Field']) {
                    $updates[] = $array['Field'] . " = :$array[Field]";
                }
            }
            if (sizeof($constraints) > 0) {
                foreach ($constraints as $key => $array) {
                    $where[] = $key . " = :$key";
                }
                $sql = "UPDATE $table SET " . implode(',', $updates) . " WHERE " . implode(' AND ', $where) . "$limit";
            } else {
                $sql = "UPDATE $table SET " . implode(',', $updates) . " WHERE " . $primary . " = :" . $primary . "$limit";
            }
        } else {
            // check to see all null value no fields are accounted for.
            foreach ($columns as $key => $array) {
                if ($array['Field'] != $primary) {
                    if (!array_key_exists($array['Field'], $variables)) {
                        if ($array['Null'] == 'NO') {
                            if (!in_array($array['Field'], $fields)) {
                                $fields[] = $array['Field'];
                                $values[] = "'$array[Default]'";
                            }
                        }
                    } else {
                        $fields[] = $array['Field'];
                        $values[] = ":$array[Field]";
                    }
                }
            }
            $sql = "INSERT INTO $table ( " . implode(' , ', $fields) . ' ) VALUES ( ' . implode(' , ', $values) . " );";
        }
        
        return $sql;
    }

    /**
     * [changeDatabase]
     * 
     * @param  string - $database [name of database to change to.]
     * 
     * @return none
     */
    public function changeDatabase($database) 
    {
        $this->setDatabase($database);
        $this->connect();
    }

    /**
     * [connect] - connect to Database
     * 
     * @return none
     */
    public function connect()
    {
        try {
            $this->dbh = new \PDO($this->dsn, 
                $this->dbUser, 
                $this->dbPass);  
            $this->dbh->setAttribute( \PDO::MYSQL_ATTR_FOUND_ROWS, true);   
            $this->dbh->setAttribute (\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
            $this->dbh->setAttribute (\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
        } catch (\PDOException $e) {
            $this->connectionError = true;
            $this->errorMessage = "Error!: " . $e->getMessage() . "<br/>";
        } catch (\Exception $e) {
            $this->connectionError = true;
            $this->errorMessage = "Error!: " . $e->getMessage() . "<br/>";
        }
    }

    /**
     * [connectionError]
     * 
     * @return string [description]
     * 
     */
    public function connectionError() 
    {
        return $this->connectionError;
    }

    /**
     * [connectionInfo]
     * 
     * @return [string] a json string containing all information re
     */
    public function connectionInfo($return = 'array')
    {
        return json_encode(array(
            'connection' => $this->dbConn,
            'database' => $this->dbName,
            'user' => $this->dbUser,
            'password' => $this->dbPass
            ));
    }

    /**
     * [describeTable]
     * 
     * @param  string $table [what table obtain information about.]
     * 
     * @return array         [array attributes]
     */
    public function describeTable($table)
    {
        $columns = array();
        $sth = $this->dbh->prepare('DESCRIBE ' . $table);
        $sth->execute();
        $tableinfo = $sth->fetchAll(\PDO::FETCH_ASSOC);
        // add field names by name
        foreach ($tableinfo as $key => $array) {            
            $columns[$array['Field']] = $array;
        }
        return $columns;
    }

    /**
     * [errorInfo description]
     * 
     * @return [type] [description]
     */
    public function errorInfo()
    {
        return $this->dbh->errorInfo();
    }

    /**
     * [errorMessage]
     * 
     * @return string 
     */
    public function errorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * [lastInsertId]
     * 
     * @return integer number id from the last insert command with auto-increment.
     */
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * [isError]
     * 
     * @return boolean 
     */
    public function isError()
    {
        return $this->error;
    }

    /**
     * [setDsn] - se the DSN variable.
     * 
     * @param string $dsn [The Data Source Name, or DSN, contains the information required 
     *                    to connect to the database. ]
     */
    public function setDsn($dsn)
    {
        $this->dsn = $dsn;
        return $this;
    }
    
    /**
     * [setCredentials] - set the user/pw combonation to be used by the 
     *                    database connection.
     *                    
     * @param string $user [the user associated with the database connection]
     * @param string $pw   [the password associated with the database connection]
     * 
     */
    public function setCredentials($user = '', $pw = '')
    {
        $this->dbUser = $user;
        $this->dbPass = $pw;
        return $this;
    }

    /**
     * [setDatabase] - set the database variable.
     * 
     * @param none
     * 
     */
    public function setDatabase($database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * [showDatabases] - return a list of databases the current user is able to 
     *                   view in the currently seelction connection.
     *                   
     * @return [array] [list of databases]
     * 
     */
    public function showDatabases()
    {
        $array = array();
        $sth = $this->dbh->prepare('SHOW databases');        
        $sth->execute();        
        $tableinfo = $sth->fetchAll(\PDO::FETCH_ASSOC);        
        foreach ($tableinfo as $key => $value) {            
            $array[] = $value['Database'];
        }        
        return $array;
    }

    /**
     * [showTables] - return the list of databases for the selected database.
     *                            
     * @return [array] [list of tables]
     * 
     */
    public function showTables()
    {
        $array = array();
        $sth = $this->dbh->prepare('SHOW tables');        
        $sth->execute();        
        $tableinfo = $sth->fetchAll(\PDO::FETCH_ASSOC);        
        foreach ($tableinfo as $key => $value) {            
            $array[] = $value['Tables_in_' . $this->dbName];
        }        
        return $array;
    }

}
