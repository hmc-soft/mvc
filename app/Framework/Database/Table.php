<?php
namespace HMC\Database;

/**
* Represents a Table in a Database.
*
* @author Ebben Feagan - ebben@hmc-soft.com
*/
class Table {

  /**
  * Specifies a field as being able to be NULL.
  */
  const NULLABLE = 'NULL';

  /**
  * Specifies a field as not being able to be NULL.
  */
  const NOT_NULL = 'NOT NULL';

  private $tblName = '';
  private $tblFields = null;
  private $db = null;
  private $pk = null;

  /**
  * Create a new Table
  * @param $name - the name of the table.
  * @param $fields (optional) - array describing the tables fields
  * @param $dbConnection (optional) - PDO database connection for creating / comparing table.
  *
  * @throws InvalidArgumentException if $name is empty or null
  */
  public function __construct($name, array $fields = null, $dbConnection = null) {
    if($name == null || $name == '') {
      throw new \InvalidArgumentException('You must provide a name for the table.');
    }
    $this->tblName = $name;
    if($fields) {
      $this->tblFields = $fields;
    }
    if($dbConnection) {
      $this->db = $dbConnection;
    }
  }

  /**
  * Allows getting field info like: $fieldInfo = $mytable->fieldName;
  * If the fieldName is the same as an existing function or property prefix with
  * a single underscore (_) and it will be removed automatically.
  */
  public function __get($name) {
    $fname = ltrim($name,'_');
    if($this->tblFields == null) return false;
    foreach($this->tblFields as $field) {
      if($field['name'] == $fname) {
        return array(
          'type' => $field['type'],
          'length' => $field['length'],
          'nullable' => $field['nullable'],
          'default' => $field['default'],
          'options' => $field['options'],
          'primary' => ($this->pk === $fname ? true : false)
        );
      }
    }
    return false;
  }

  /**
  * Allows setting field info like: $mytable->fieldName = $fieldInfo;
  * If the fieldName is the same as an existing function or property prefix with
  * a single underscore (_) and it will be removed automatically.
  */
  public function __set($name,$value) {
    $fname = ltrim($name,'_');
    if($this->tblFields == null) $this->tblFields = array();
    $foundIt = false;
    foreach($this->tblFields as $field) {
      if($field['name'] == $fname) {
        $foundIt = true;
        $field['type'] = $value['type'];
        $field['length'] = $value['length'] || null;
        $field['nullable'] = $value['nullable'] || NOT_NULL;
        $field['default'] = $value['default'] || null;
        $field['options'] = $value['options'] || null;
        if(isset($value['primary'])) {
          if($value['primary']) {
            $this->pk = $fname;
          }
        }
      }
    }
    if(!$foundIt) {
      $this->tblFields[] = array(
        'name' => $fname,
        'type' => $value['type'],
        'length' => $value['length'] || null,
        'nullable' => $value['nullable'] || NOT_NULL,
        'default' => $value['default'] || null,
        'options' => $value['options'] || null
      );
      if(isset($value['primary'])) {
        if($value['primary']) {
          $this->pk = $fname;
        }
      }
    }
  }

  /**
  * Retrieve or set the database connection used for creating / comparing.
  * @param $dbConnection (optional) - if set this will become the new connection
  * @return The connection that will be used from now on.
  */
  public function connection($dbConnection = null) {
    if($dbConnection) {
      $this->db = $dbConnection;
    }
    return $this->db;
  }

  /**
  * Retrieve an array of each field name.
  */
  public function fieldNames() {
    $out = array();
    foreach($this->tblFields as $field) {
      $out[] = $field['name'];
    }
    return $out;
  }

  /**
  * Retrieve all info about fields.
  */
  public function fieldDetails() {
    return $this->tblFields;
  }

  /**
  * Add a field to the table.
  * @param $fName - the name of the field.
  * @param $fType - the type of the field.
  * @param $fLen - the (optional) length of the type, i.e. varchar(255)
  * @param $fNullable (optional) - can the field be null, defaults to DbTable::NOT_NULL.
  * @param $fDefault (optional) - the default value of the field.
  * @param $isPrimary (optional) - is this the table's primary key, defaults to false
  * @param $options (optional) - field options, typically used for AUTO_INCREMENT, accepts null for none, a string or an array of options.
  *
  * @return $this for chaining.
  */
  public function addField($fName, $fType, $fLen = null, $fNullable = Table::NOT_NULL, $fDefault = null, $isPrimary = false, $options = null) {
    if($this->tblFields == null) $tblFields = array();

    $field = array(
      'name' => $fName,
      'type' => $fType,
      'length' => $fLen,
      'nullable' => $fNullable,
      'default' => $fDefault,
      'options' => $options
    );

    if($isPrimary) {
      $this->pk = $fName;
      $field['primary'] = true;
    }

    $this->tblFields[] = $field;

    return $this;
  }

  /**
  * Compile the table definition to SQL, typically used only internally.
  * Note: This does not return the complete statement, you must add
  * CREATE TABLE to the beginning to actually use this.
  * @return The proper SQL string to create the table or null if in error.
  * @throws Exception if a duplicate primary key is defined.
  */
  public function compile() {
    $sql = "{$this->tblName} (";

    if(!$this->tblFields) return null;

    foreach ($this->tblFields as $field) {
      $realType = $field['type'];
      if($field['length'] !== null) {
        if(is_int($field['length'])) {
          $realType .= '('.$field['length'].')';
        }
      }
      $sql .= "`{$field['name']}` {$realType} {$field['nullable']} ";
      if(isset($field['default'])) {
        if($field['default'] !== null) {
          $sql .= "DEFAULT {$field['default']} ";
        }
      }
      if(isset($field['options'])) {
        if($field['options'] !== null) {
          if(is_array($field['options'])) {
            foreach($field['options'] as $opt) {
              $sql .= $opt . ' ';
            }
          } else {
            $sql .= $field['options'];
          }
        }
      }
      $sql .= ', ';
      if(isset($field['primary'])) {
        if($field['primary']) {
          if($this->pk !== null && $this->pk !== $field['name']) {
            throw new Exception('Duplicate Primary keys defined: first was ('.$this->pk.') this one ('.$field['name'].')');
          } else {
            $this->pk = $field['name'];
          }
        }
      }
    }

    if ($this->pk !== null) {
        $sql .= "PRIMARY KEY (`{$this->pk}`)";
    }

    // Removing additional commas
    $sql = rtrim($sql, ', ') . ')';
    return $sql;
  }

  /**
  * Create the table in the database.
  * @param $onlyNew (optional) - only create the table if its not already there.
  * @return true if the table was created or already exists, false otherwise.
  */
  public function create($onlyNew = false) {
    $compiled = $this->compile();
    if($compiled === null) return false;

    $sql = "CREATE TABLE ";
    if($onlyNew) $sql .= "IF NOT EXISTS ";
    $sql .= $compiled . ';';

    if($this->db === null) return false;
    try {
      $this->db->exec($sql);
      $ret = $this->db->errorCode();
      if($ret == '00000') {
        return true;
      } else {
        $info = $this->db->errorInfo();
        \HMC\Logger::error('SQLSTATE['.$ret.'] ' . $info[0].$info[1].' '.$info[2] );
        return false;
      }
    }
    catch(\Exception $e) {
      \HMC\Logger::error($sql);
      \HMC\Logger::error(\HMC\Logger::buildExceptionMessage($e));
      return false;
    }
  }

  /**
  * Does a table with the same name exist in the database.
  * @return boolean
  * @throws Exception if the method is not implemented for the current db driver.
  */
  public function exists() {
    $retVal = false;
    if($this->db === null) return false;
    $dbtype = $this->db->getAttribute(\PDO::ATTR_DRIVER_NAME);
    switch($dbtype) {
      case 'sqlite':
        $sql  = "SELECT name FROM sqlite_master ";
        $sql .= "WHERE type='table' AND name='{$this->tblName}';";
        $res = $this->db->query($sql);
        if($res !== false) {
          $set = $res->fetchAll();
          if(count($set) > 0) $retVal = true;
          unset($set);
          $res->closeCursor();
        }
        break;

      case 'mysql':
        $sql = "SHOW TABLES LIKE '{$this->tblName}'";
        $res = $this->db->query($sql);
        if($res !== false) {
          $set = $res->fetchAll();
          if(count($set) > 0) $retVal = true;
          unset($set);
          $res->closeCursor();
        }
        break;

      default:
        throw new \Exception('Method (exists) not implemented for this driver: ' . $dbtype);
    }
    return $retVal;
  }

  /**
  * Build the Table object from the database table definition.
  * @param $name - the name of the table in the database.
  * @param $db - the PDO Database object.
  * @return Table or null
  * @throws Exception if method is not implemented for this db driver.
  */
  public static function fromDB($name,$db) {
    if($db === null) return null;
    $ret = new Table($name,null,$db);
    if($ret->exists()) {
      $dbtype = $db->getAttribute(\PDO::ATTR_DRIVER_NAME);
      switch($dbtype) {
        case 'mysql':
          $sql = "SHOW COLUMNS FROM {$name}";
          $resultSet = $db->query($sql);
          $results = $resultSet->fetchAll(\PDO::FETCH_ASSOC);
          foreach($results as $field) {
            $matches = array();
            $regresult = preg_match("/(\w+)\((\d+)\)/",$field['Type'],$matches);
            $realType = '';
            if($regresult == 0 || $regresult == false) {
              $realType = $field['Type'];
              $realLen = null;
            } else {
              $realType = $matches[1];
              $realLen = (int)$matches[2];
            }
            $ret->addField(
              $field['Field'],
              $realType,
              $realLen,
              ($field['Null'] == 'YES' ? Table::NULLABLE : Table::NOT_NULL),
              $field['Default'],
              ($field['Key'] == 'PRI' ? true : false),
              ($field['Key'] == 'UNI' ? 'UNIQUE '.strtoupper($field['Extra']) : ($field['Extra'] == '' ? null : strtoupper($field['Extra'])))
            );
          }
          break;

        default:
          throw new \Exception('Method (fromDB) not implemented for this driver: ' . $dbtype);
      }
      return $ret;
    }
    return null;
  }

  /**
  * Report differences between the passed table and this one.
  * @param $rhs Table to compare to
  * @return Array of fields that are different with the different values.
  */
  public function diff(Table $rhs) {
    $ourFields = $this->tblFields;
    $lenOF = count($ourFields);
    $theirFields = $rhs->tblFields;
    $lenTF = count($theirFields);
    $diff = array();
    foreach($theirFields as $field) {
      $name = $field['name'];

      $of = null;
      foreach($ourFields as $ofield) {
        if($ofield['name'] == $name)
          $of = $ofield;
      }
      if($of == null) {
        //field doesn't exist in our table.
        $diff[] = $field;
      } else {
        $needed = false;
        $outfield = array( 'name' => $name );

        foreach($field as $key => $value) {
          if($of[$key] !== $value) {
            $needed = true;
            $outfield[$key] = array('ours' => $of[$key], 'theirs' => $value);
          }
        }

        if($needed) {
          $diff[] = $outfield;
        }
      }
    }
    return $diff;
  }
}
