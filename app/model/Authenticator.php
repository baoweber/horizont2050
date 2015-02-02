<?php
namespace App\Models;

use Nette\Security as NS;
use Nette\Diagnostics\Debugger;

/**
 * Users authenticator.
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class Authenticator extends \Nette\Object implements NS\IAuthenticator
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
        $this->db = $connection;
    }


    /**
     * Performs an authentication
     *
     * @param  array
     * @return NS\Identity
     * @throws NS\AuthenticationException
     */
    public function authenticate(array $credentials)
    {


        // debug
        Debugger::barDump($credentials, "PROJECTS");

        // breaking up the credentials
        list($email, $password) = $credentials;


        $data = array(
            'email%s'  => $email,
            'status%i' => 1
        );
        $row  = $this->db->query("SELECT * FROM [:pref:users] WHERE %and", $data)->fetch();

        if (!$row) {
            throw new NS\AuthenticationException("User '$email' not found.", self::IDENTITY_NOT_FOUND);
        }

        if ($row->password !== $this->calculateHash($password)) {
            throw new NS\AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
        }


        unset($row->password);

        return new NS\Identity($row->id, $row->role, $row->toArray());
    }


    /**
     * Computes salted password hash
     *
     * @param  string
     * @return string
     */
    public function calculateHash($password)
    {
        //return md5($password . str_repeat('fg5*', 10));
        return md5($password);
    }

}
