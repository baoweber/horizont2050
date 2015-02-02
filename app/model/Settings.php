<?php
namespace App\Models;

use Nette\Diagnostics\Debugger;

class Settings extends \DivineModel
{
    /** @var \DibiConnection */
    private $db;

    public function __construct(\DibiConnection $connection)
    {
        // runnging parent construct
        parent::__construct($connection);

        // assigning table name including :pref:
        $this->table = ':pref:settings';

        // assigning connection
        $this->db = $connection;

        // defining schema
        $this->schema = array(
            'EUR'          => '%f',
        );
    }

    public function getAll($params = false)
    {

        $result = $this->db->query("
            SELECT `key`, `value`
            FROM %n
          ", $this->table
        )->fetchPairs('key', 'value');

        return $result;

    }

    public function saveValue($key, $value)
    {

        $temp = 'value' . $this->schema[$key];
        $values = array($temp => $value);

        $result = $this->db->query("
            UPDATE %n
            SET %a
            WHERE `key` = %s
          ", $this->table, $values, $key
        );
    }

    public function getValue($key)
    {

        $result = $this->db->fetchSingle("
            SELECT `value`
            FROM %n
            WHERE `key` = %s
          ", $this->table, $key
        );

        return $result;

    }


}
