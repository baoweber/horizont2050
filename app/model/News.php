<?php
namespace App\Models;


use Nette\Diagnostics\Debugger;

class News extends \DivineModel
{
    /** @var \DibiConnection */
    private $db;

    public function __construct(\DibiConnection $connection)
    {
        // runnging parent construct
        parent::__construct($connection);

        // assigning table name including :pref:
        $this->table = ':pref:news';

        // assigning connection
        $this->db = $connection;

        // defining schema
        $this->schema = array(
            'title'             => '%s',
            'menutitle'         => '%s',
            'perex'             => '%s',
            'date'              => '%s',
            'slug'              => '%s',
            'text'              => '%s',
            'active'            => '%i',
            'redirect'          => '%s'
        );
    }

    public function getSingleBySlug($slug)
    {
        // getting all entries
        return $this->db->fetch(
            "SELECT *
            FROM %n
            WHERE `slug` =  %s",
            $this->table, $slug
        );
    }

    public function getHotNews($limit = 3)
    {
        return $this->getAll([
            'limit' => $limit,
            'orderby' => '`date` DESC'
        ]);
    }

    public function getMenuTitle($id) {

        return $this->getField($id, 'menutitle');

    }

}
