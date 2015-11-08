<?php
namespace HMC;

use HMC\Database\Connection;
use HMC\Database\Table;

/*
 * model - the base model
 *
 * @author David Carr - dave@simplemvcframework.HMC
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
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

    /**
    * This function is meant to be called from the derived constructor
    * with the name of the Table in the database to setup the simple ORM.
    */
    protected function alias($name = null) {
      $table = Table::fromDb($name,$this->db);
      if($table == null) return;
      $fields = $table->fields();
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
    * @param $filter - the SQL WHERE statement to get the record, like "id = '1'"
    */
    public function get($filter) {
      $this->values = $this->db->select(
        "SELECT " . join(",",$this->fields) . " FROM {$this->name} WHERE $filter;"
      );
      return $this->values == null;
    }

    /**
    * Updates or Creates a new record if any changes have been made.
    * @param $idFieldName - which field is the unique identifier, defaults to 'id'
    */
    public function save($idFieldName = 'id') {
      if(!$this->dirty) return null;
      $this->dirty = false;
      if($this->new) {
        return $this->db->insert($this->name, $this->values);
      } else {
        return $this->db->update($this->name, $this->values, array($idFieldName => $this->values[$idFieldName]));
      }
    }

    /**
     * create a new instance of the database helper
     */
    public function __construct()
    {
        //connect to PDO here.
        $this->db = Connection::get();

    }
}
