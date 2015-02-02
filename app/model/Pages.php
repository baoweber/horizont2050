<?php
namespace App\Models;


use Nette\Diagnostics\Debugger;

class Pages extends \DivineModel
{
    /** @var \DibiConnection */
    private $db;

    public function __construct(\DibiConnection $connection)
    {
        // runnging parent construct
        parent::__construct($connection);

        // assigning table name including :pref:
        $this->table = ':pref:pages';

        // assigning connection
        $this->db = $connection;

        // defining schema
        $this->schema = array(
            'title'             => '%s',
            'menutitle'         => '%s',
            'perex'             => '%s',
            'slug'              => '%s',
            'text'              => '%s',
            'parent'            => '%i',
            'show_in_menu'      => '%i',
            'active'            => '%i',
            'redirect'          => '%s'
        );
    }
    public function getParent($id) {

        return $this->getField($id, 'parent');

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

    public function getMenuTitle($id) {

        return $this->getField($id, 'menutitle');

    }

    public function moveUp($id) {

        $parent = $this->getParent($id);

        $this->resort($parent, $id, 1);

    }

    public function moveDown($id) {

        $parent = $this->getParent($id);

        $this->resort($parent, $id, -1);

    }

    private function resort($cat, $id, $dir) {

        //selecting array
        if ($cat) {
            $where = " WHERE `parent` = {$cat}";
        } else {
            $where = " WHERE `parent` IS NULL";
        }

        //write lock
        $this->db->query("LOCK TABLES %n WRITE", $this->table);

        // retrieving the list of categories
        $result = $this->db->query("SELECT `id` FROM %n  %sql ORDER BY ord",$this->table,  $where);

        $i = 0;
        $categories = array();
        foreach($result as $n => $row) {
            $i++;
            $categories[$i] = $row['id'];
        }

        // ===== RESORTING
        $debug['before'] = $categories;

        $categories = $this->resortArray($categories, $id, $dir);
        $debug['after'] = $categories;
        Debugger::barDump($debug, 'SORT');

        foreach ($categories as $key => $item) {
            $this->db->query("UPDATE %n SET `ord` = %i WHERE id = %i", $this->table, $key, $item);
        }

        //droping write lock
        $this->db->query("UNLOCK TABLES");

    }

    private function resortArray($array, $token, $dir) {

        //variables
        $i = 0;
        $result = array();

        foreach($array as $key => $item) {
            $i++;
            if(isset($array[$i+$dir]) && $array[$i+$dir] == $token) {
                $result[$i] = $array[$i+$dir];
            } elseif ($array[$i] == $token){
                $result[$i] = isset($array[$i-$dir]) ? $array[$i-$dir]: NULL;
            } else {
                $result[$i] = $array[$i];
            }
        }

        return $result;

    }

}
