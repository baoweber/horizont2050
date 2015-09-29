<?php
namespace App\Models;


use Nette\Diagnostics\Debugger;

/**
 * Keywords model
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class Keywords extends \DivineModel
{
    /** @var \DibiConnection */
    private $db;

    /**
     * Object constructor
     *
     * @param \DibiConnection $connection
     */
    public function __construct(\DibiConnection $connection)
    {
        // runnging parent construct
        parent::__construct($connection);

        // assigning table name including :pref:
        $this->table = ':pref:keywords';

        // assigning connection
        $this->db = $connection;

        // defining schema
        $this->schema = array(
            'label'  => '%s'
        );
    }

    /**
     * Get keywrods assigned to a specified signal
     *
     * @param int $id Signal ID
     * @return \DibiRow[]
     */
    public function getAssignedKeywords($id)
    {

        $tables = array( $this->table => 'k', ':pref:signals_keywords' => 's_k');
        $fields = array(
            's_k.id' => 'id',
            'k.id' => 'keywords_id',
            'label'
        );
        $where = array('s_k.signals_id' => $id, 's_k.keywords_id%sql' => '`k`.`id`', );

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

    /**
     * Assign an existing keyword to a signal
     *
     * @param int $signals_id
     * @param int $keywords_id
     */
    public function assignExisting($signals_id, $keywords_id)
    {

        $values = array(
            'signals_id%i' => $signals_id,
            'keywords_id%i' => $keywords_id
        );

        $this->db->query('INSERT INTO %n %v', ':pref:signals_keywords', $values);
    }

    /**
     * Insert new keyword and assign it to a signal
     *
     * @param int $signals_id
     * @param string $label
     * @param int $user
     */
    public function assignNew($signals_id, $label, $user)
    {

        $values = array(
            'label' => $label
        );

        $keywords_id = $this->insert($values, $user);

        $values = array(
            'signals_id%i' => $signals_id,
            'keywords_id%i' => $keywords_id
        );

        $this->db->query('INSERT INTO %n %v', ':pref:signals_keywords', $values);
    }

    /**
     * Unassign keyword from a signal
     *
     * @param int $id
     * @param int $keywords_id
     */
    public function unassign($id, $keywords_id)
    {

        $where = array(
            'signals_id%i' => $id,
            'keywords_id%i' => $keywords_id
        );

        $this->db->query('DELETE FROM %n WHERE %and', ':pref:signals_keywords', $where);

    }

    public function getAllCount($params = false)
    {
        $keywords  = $this->getAll($params);

        foreach($keywords as $item) {
            $where = array(
                'keywords_id%i' => $item->id
            );
            $item->count = $this->db->fetchSingle("SELECT COUNT(`id`) FROM [:pref:signals_keywords] WHERE %and", $where);
        }

        return $keywords;
    }

}
