<?php
namespace App\Models;

use Nette\Diagnostics\Debugger;

class Signals extends \DivineModel
{
    /** @var \DibiConnection */
    private $db;

    public function __construct(\DibiConnection $connection)
    {
        // runnging parent construct
        parent::__construct($connection);

        // assigning table name including :pref:
        $this->table = ':pref:signals';

        // assigning connection
        $this->db = $connection;

        // defining schema
        $this->schema = array(
            'name'              => '%s',
            'title'             => '%s',
            'perex'             => '%s',
            'description'       => '%s',
            'impact'            => '%s',
            'likelyhood'        => '%s',
            'drivers'           => '%s',
            'recomendations'    => '%s',
            'categories_id'     => '%i',
            'event_types_id'    => '%i',
            'spaces_id'         => '%i',
            'relevance'         => '%i',
            'impacts_id'        => '%i',
            'scales_id'         => '%i',
            'image_path'        => '%s',
            'timeframe'         => '%s',
            'ptree'             => '%s',
            'user_update'       => '%d',
            'user_create'       => '%d'
        );
    }
}
