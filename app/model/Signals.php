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
            'public'            => '%i',
            'ptree'             => '%s',
            'user_update'       => '%d',
            'user_create'       => '%d'
        );
    }

    /**
     * Activates given item
     *
     * @param $id
     */
    public function activate($id)
    {
        $this->setValue($id, 'public', 1);
    }

    /**
     * Deactivates given item
     *
     * @param $id
     */
    public function deactivate($id)
    {
        $this->setValue($id, 'public', 0);
    }

    /**
     * Update a single field of a given resource
     *
     * @todo move to divine presenter and bind to general update function
     * @param $id
     * @param $field
     * @param $value
     * @throws \Exception
     */
    public function setValue($id, $field, $value )
    {

        if(isset($this->schema[$field])) {

            $key = $field . $this->schema[$field];
            $values = array(
                $key => $value
            );
            $this->db->query("UPDATE %n SET %a WHERE `id` = %i ", $this->table, $values, $id);

        } else {
            throw new \Exception("Field `" . $field . "` is not in the model definition.");
        }

    }
}


