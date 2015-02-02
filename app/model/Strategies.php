<?php
namespace App\Models;

use Nette\Diagnostics\Debugger;

class Strategies extends \DivineModel
{
    /** @var \DibiConnection */
    private $db;

    public function __construct(\DibiConnection $connection)
    {
        // runnging parent construct
        parent::__construct($connection);

        // assigning table name including :pref:
        $this->table = ':pref:strategies';

        // assigning connection
        $this->db = $connection;

        // defining schema
        $this->schema = array(
            'name'         => '%s',
            'institution'  => '%s',
            'url'          => '%s',
            'perex'        => '%s',
            'valid_until'  => '%d'
        );
    }

    public function getAllSignalSources($signalsId, $params = null) {

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

        $where['signals_id%i'] = $signalsId;

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

    public function getAssignedStrategies($id)
    {

        $tables = array( $this->table => 'k', ':pref:signals_strategies' => 's_k');
        $fields = array(
            's_k.id' => 'id',
            'k.id' => 'strategies_id',
            'name',
            'url'
        );
        $where = array('s_k.signals_id' => $id, 's_k.strategies_id%sql' => '`k`.`id`', );

        $assigned = $this->db->fetchAll('
            SELECT %n
            FROM %n
            WHERE %and
            ',
            $fields,
            $tables,
            $where
        );


        return $assigned;
    }

    public function assignExisting($signals_id, $strategies_id)
    {

        $values = array(
            'signals_id%i' => $signals_id,
            'strategies_id%i' => $strategies_id
        );

        $this->db->query('INSERT INTO %n %v', ':pref:signals_strategies', $values);
    }

    public function unassign($id, $strategies_id)
    {

        $where = array(
            'signals_id%i' => $id,
            'strategies_id%i' => $strategies_id
        );

        $this->db->query('DELETE FROM %n WHERE %and', ':pref:signals_strategies', $where);

    }

}