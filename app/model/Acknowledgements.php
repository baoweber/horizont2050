<?php
namespace App\Models;

use Nette\Diagnostics\Debugger;

class Acknowledgements extends \DivineModel
{
    /** @var \DibiConnection */
    private $db;

    public function __construct(\DibiConnection $connection)
    {
        // runnging parent construct
        parent::__construct($connection);

        // assigning table name including :pref:
        $this->table = ':pref:acknowledgements';

        // assigning connection
        $this->db = $connection;

        // defining schema
        $this->schema = array(
            'site'         => '%s',
            'author'       => '%s',
            'year'         => '%i',
            'signals_id'   => '%i'
        );
    }

    public function getAllBySignal($signalsId) {

        $output = $this->getAll(array('where' => array('signals_id%i' => $signalsId)));

        return $output;
    }
}
