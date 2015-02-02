<?php
namespace App\Models;

use Nette\Diagnostics\Debugger;

/**
 * Event type model.
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class EventTypes extends \DivineModel
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
        $this->table = ':pref:event_types';

        // assigning connection
        $this->db = $connection;

        // defining schema
        $this->schema = array(
            'label'  => '%s'
        );
    }

}
