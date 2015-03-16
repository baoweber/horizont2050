<?php
namespace App\Models;

use Nette\Diagnostics\Debugger;

class Sources extends \DivineModel
{
    /** @var \DibiConnection */
    private $db;

    public function __construct(\DibiConnection $connection)
    {
        // runnging parent construct
        parent::__construct($connection);

        // assigning table name including :pref:
        $this->table = ':pref:sources';

        // assigning connection
        $this->db = $connection;

        // defining schema
        $this->schema = array(
            'type'         => '%i',
            'name'         => '%s',
            'author'       => '%s',
            'date'         => '%d',
            'visited'      => '%s',
            'pages'        => '%s',
            'in'           => '%s',
            'publisher'    => '%s',
            'ISBN'         => '%s',
            'url'          => '%s',
            'signals_id'   => '%i'
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
}
