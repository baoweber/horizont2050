<?php
namespace App\Models;

use Nette\Diagnostics\Debugger;

/**
 * Challenges model
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class Challenges extends \DivineModel
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
        $this->table = ':pref:challenges';

        // assigning connection
        $this->db = $connection;

        // defining schema
        $this->schema = array(
            'name'         => '%s',
            'desc'         => '%s'
        );
    }

    /**
     * Returns challenges for given signal
     *
     * @param int $signalsId
     * @param array $params
     *
     * @return \DibiRow[]
     */
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

    /**
     * Returns challenges for given signal
     *
     * @param int $id
     * @return \DibiRow[]
     */
    public function getAssignedChallenges($id)
    {

        $tables = array( $this->table => 'k', ':pref:signals_challenges' => 's_k');
        $fields = array(
            's_k.id' => 'id',
            'k.id' => 'challenges_id',
            'name'
        );
        $where = array('s_k.signals_id' => $id, 's_k.challenges_id%sql' => '`k`.`id`', );

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
     * Assign existing challenge
     *
     * @param int $signals_id
     * @param int $challenges_id
     */
    public function assignExisting($signals_id, $challenges_id)
    {

        $values = array(
            'signals_id%i' => $signals_id,
            'challenges_id%i' => $challenges_id
        );

        $this->db->query('INSERT INTO %n %v', ':pref:signals_challenges', $values);
    }

    /**
     * Create a new challenge and asisgn it
     *
     * @param int $id
     * @param int $challenges_id
     */
    public function unassign($id, $challenges_id)
    {

        $where = array(
            'signals_id%i' => $id,
            'challenges_id%i' => $challenges_id
        );

        $this->db->query('DELETE FROM %n WHERE %and', ':pref:signals_challenges', $where);

    }

}
