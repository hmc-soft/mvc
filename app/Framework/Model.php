<?php
namespace HMC;

use HMC\Database\Connection;
use HMC\Database\Table;

/*
 * The Base class for Models.
 * Optionally acts like a simple ORM.
 *
 * @author Ebben Feagan - ebben@hmc-soft.com
 */

abstract class Model
{
    /**
     * hold the database connection
     * @var object
     */
    protected $db = null;

    private $name = '';
    private $fields = null;
    private $values = null;
    private $dirty = false;
    private $new = true;
    private $pk = null;
    private $table = null;

    /**
    * This function is meant to be called from the derived constructor
    * with the name of the Table in the database to setup the simple ORM.
    */
    protected function alias($name = null) {
      $this->table = Table::fromDb($name,$this->db);
      if($this->table == null) return;
      $this->fields = $this->table->fieldNames();
      foreach($this->table->fieldDetails() as $field) {
        if($this->pk == null){
          if(isset($field['primary'])) {
            if($field['primary']) {
              $this->pk = $field['name'];
            }
          }
        }
      }
      $this->name = $name;
    }

    /**
    * Allows access to the database fields via $model->fieldName.
    */
    public function __get($fname) {
      if($this->values == null) {
        throw new \Exception("No data to get, you must perform a query first.");
      }
      if(isset($this->values[$fname])) {
        return $this->values[$fname];
      } else {
        throw new \Exception("No field exists with the name: $fname");
      }
    }

    /**
    * Allows access to set field values using $model->fieldName = 'value';
    */
    public function __set($fname,$value) {
      if($this->values == null) {
        $this->values = array();
      }
      $this->values[$fname] = $value;
      $this->dirty = true;
    }

    /**
    * Simple method to retrieve a single record for the model.
    * Array Example:
    * `MyModel::get(array("parent" => 1, "type" => "menu"));`
    * Becomes:
    * `SELECT fields... FROM MyModel WHERE (parent = '1') AND (type = 'menu');`
    * @param $id- the value of the primary key of the record to retrieve or an array of key => values that will form the where statement.
    */
    public static function get($id) {
      $ret = new self();
      if($ret->pk == null) {
        throw new \Exception("Primary key not defined for " . $ret->name);
      }
      $passArray = false;
      if(is_array($id)) {
        $passArray = true;
        $where = '';
        foreach($id as $key => $value) {
          $where .= "({$key} = :{$key}) AND ";
        }
        $where = rtrim($where," AND ");
      } else {
        $where = "{$ret->pk} = :{$ret->pk}";
      }
      $ret->values = $ret->db->select(
        "SELECT " . join(",",$ret->fields) . " FROM {$ret->name} WHERE $where;",
        ($passArray ? $id : array($ret->pk => $id))
      );
      return $ret->values == null;
    }

    /**
    * Run a query and get all matching objects.
    * @param $query string containing the WHERE clause of a SQL statement (WHERE not needed), like key = :key AND key2 = :key2.
    * @param $vals array containing key/value pairs to bind to where statement.
    * @return array of Model objects with results.
    */
    public static function search($query,$vals) {
      $ret = new self();
      $results = $ret->db->select(
        "SELECT " . join(",",$ret->fields) . " FROM {$ret->name} WHERE {$query}",
        $vals
      );
      $retArray = array();
      foreach($results as $result) {
        $i = new self($ret->db,$result);
        $retArray[] = $i;
      }
      return $retArray;
    }

    /**
    * Updates or Creates a new record if any changes have been made.
    */
    public function save() {
      if(!$this->dirty) return null;
      if($this->pk == null) {
        throw new \Exception("Primary key not defined for " . $this->name);
      }
      $this->dirty = false;
      if($this->new) {
        $this->new = false;
        return $this->db->insert($this->name, $this->values);
      } else {
        return $this->db->update($this->name, $this->values, array($this->pk => $this->values[$this->pk]));
      }
    }

    /**
    * Deletes the current record.
    */
    public function delete() {
      if($this->values == null) return;
      if($this->pk == null) {
        throw new \Exception("Primary key not defined for " . $this->name);
      }
      $this->db->delete($this->name, array($this->pk => $this->values[$this->pk]));
      $this->dirty = false;
      unset($this->values);
      $this->values = null;
    }

    /**
     * create a new instance of the database helper
     */
    public function __construct($dbo = null,$valuesArray = null)
    {
      //connect to PDO here.
      if($dbo == null){
        $this->db = Connection::get();
      } else {
        $this->db = $dbo;
      }

      $this->alias(get_class($this));

      $this->values = $valuesArray;
    }
}
