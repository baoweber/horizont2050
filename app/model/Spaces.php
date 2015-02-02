<?php
namespace App\Models;

use Nette\Diagnostics\Debugger;

class Spaces extends \DivineModel
{
    /** @var \DibiConnection */
    private $db;

    public function __construct(\DibiConnection $connection)
    {
        // runnging parent construct
        parent::__construct($connection);

        // assigning table name including :pref:
        $this->table = ':pref:spaces';

        // assigning connection
        $this->db = $connection;

        // defining schema
        $this->schema = array(
            'label'  => '%s'
        );
    }

}
