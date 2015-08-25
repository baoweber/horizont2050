<?php

use Nette\Diagnostics\Debugger;

class DivineModel extends \Nette\Object
{
  /** @var \DibiConnection */
  private $db;

  /**
   * Table serviced by the model
   */
  protected $table;

  /**
   * An array of table columns and dibi modifiers (associative array)
   */
  protected $schema;

  /**
   * Object constructor
   *
   * @param DibiConnection $connection
   */
  public function __construct(\DibiConnection $connection)
  {
    $this->db = $connection;
  }

  /**
   * Begins DB transaction
   */
  public function begin()
  {
    // executing query
    $result = $this->db->query("BEGIN");
  }

  /**
   * Commits DB transaction
   */
  public function commit()
  {
    // executing query
    $result = $this->db->query("COMMIT");
  }


  /**
   * This method gets all element of the linekd table
   *
   * The params array can contain arguments that affect the query that include:
   * limit, orderby, start
   *
   * @param array|bool $params
   * @return \DibiRow[]
   */
  public function getAll($params = false)
  {
    if(isset($params['orderby'])) {
      $orderString = $params['orderby'];
    } else {
      $orderString = '`id` DESC';
    }

    if(isset($params['where'])) {
      $where = $params['where'];
    } else {
      $where = false;
    }

    $query[] = "SELECT * FROM %n";
    $query[] = $this->table;

    if(is_array($where)) {
      $query[] = "WHERE %and";
      $query[] = $where;
    }

    $query[] = 'ORDER BY %sql ';
    $query[] = $orderString;

    // getting all entries
    return $this->db->fetchAll($query);
  }

  /**
   * Returns all rows that match the search
   *
   * @param string $needle
   * @param array $fields
   * @param array|bool $params
   * @return \DibiRow[]
   */
  public function Search($needle, $fields, $params = false)
  {

    $search = false;

    if(isset($params['orderby'])) {
      $orderString = $params['orderby'];
    } else {
      $orderString = '`id` DESC';
    }

    if($needle && is_array($fields)) {
      foreach($fields as $value) {
        $key = $value . '%~like~';
        $search[$key] = $needle;
      }
    }


    $query[] = "SELECT * FROM %n";
    $query[] = $this->table;

    if(is_array($search)) {
      $query[] = "WHERE %or";
      $query[] = $search;
    }

    $query[] = 'ORDER BY %sql ';
    $query[] = $orderString;

    // getting all entries
    return $this->db->fetchAll($query);
  }

  /**
   * Gets single row from the DB table
   *
   * @param int $id
   * @return \DibiRow
   */
  public function getSingle($id)
  {
    // getting all entries
    return $this->db->fetch(
        "SELECT *
            FROM %n
            WHERE `id` =  %i",
        $this->table, $id
    );
  }

  /**
   * Get associative array of results based on columns $key and $value
   *
   * @param string $key
   * @param string $value
   * @return array
   */
  public function getPairs($key, $value)
  {
    $fields = array($key, $value);

    return $this->db->query(
        "SELECT %n
            FROM %n",
        $fields, $this->table
    )->fetchPairs($key, $value);
  }

  /**
   * Delete from the DB table
   *
   * @param int $id
   */
  public function delete($id)
  {
    // removing the item
    $this->db->query(
        "DELETE FROM %n
                WHERE `id` = %i",
        $this->table, $id
    );
  }

  /**
   * Insert into the DB table
   *
   * @param array $data
   * @param int $user
   * @return int
   */
  public function insert($data, $user)
  {
    // parsing predefined schema
    foreach($this->schema as $key => $item) {
      if(isset($data->$key)) {
        $field = $key . $item;
        $values[$field] = $data[$key];
      } elseif (isset($data[$key])) {
        $field = $key . $item;
        $values[$field] = $data[$key];
      }
    }

    // adding editedby and edited
    $values['created%sql'] = 'NOW()';
    $values['createdby%i'] = $user;
    $values['updated%sql'] = 'NOW()';
    $values['updatedby%i'] = $user;

    // removing the item
    $this->db->query(
        "INSERT INTO %n %v",
        $this->table, $values
    );

    $id = $this->db->insertId;

    return $id;

  }

  /**
   * Updates a row in the database table
   *
   * @param int $id
   * @param array $data
   * @param int $user
   */
  public function update($id, $data, $user)
  {

    // parsing predefined schema
    foreach($this->schema as $key => $item) {
      if(isset($data->$key)) {
        $field = $key . $item;
        $values[$field] = $data[$key];
      } elseif(isset($data[$key])) {
        $field = $key . $item;
        $values[$field] = $data[$key];
      }
    }

    // adding editedby and edited
    $values['updated%sql'] = 'NOW()';
    $values['updatedby%i'] = $user;

    // removing the item
    $this->db->query(
        "UPDATE %n
            SET %a
            WHERE `id` = %i",
        $this->table, $values, $id
    );
  }

  /**
   * Gets a single field from a single row from the DB table
   *
   * @param int $id
   * @param string $field
   * @return mixed
   */
  public function getField($id, $field)
  {

    // getting all entries
    return $this->db->fetchSingle(
        "SELECT %n
            FROM %n
            WHERE `id` =  %i",
        $field, $this->table, $id
    );

  }

}
