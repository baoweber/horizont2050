<?php
namespace App\Models;

use Nette\Diagnostics\Debugger;

/**
 * User model
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class Users extends \DivineModel
{
    /** @var \DibiConnection */
    private $db;

    /**
     * Array to allowed user roles to perform check against
     *
     * @var array
     */
    private $allowedRoles = array(
        'editor',
        'publisher',
        'admin'
    );

    /**
     * Array of roles
     *
     * @var array
     */
    private $roles = array(
        'editor'    => 'editor',
        'publisher' => 'publisher',
        'admin'     => 'admin'
    );

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
        $this->table = ':pref:users';

        // assigning connection
        $this->db = $connection;
    }

    /**
     * Set user password
     *
     * @param int $id
     * @param string $password
     * @return \DibiResult|bool
     */
    public function setPassword($id, $password)
    {
        $values               = array();
        $values['password%s'] = md5($password);
        $result               = $this->db->query("UPDATE [:pref:users] SET", $values, "WHERE `id` = %i", $id);

        return $result;
    }

    /**
     * Inser new user
     *
     * @param array $values
     * @param string $role
     * @return int|void
     */
    public function insert($values, $role = 'user')
    {
        // parse data

        $data = array(
            'email%s'     => $values['email'],
            'name%s' => $values['name'],
            'surname%s' => $values['surname'],
            'role'        => $values['role'],
            'password%s'  => md5($values['password']),
            'status'      => 0,
            'updated%sql' => 'NOW()',
            'created%sql' => 'NOW()'
        );

        // executing query
        $this->db->query("INSERT INTO [:pref:users]", $data);

    }

    /**
     * Update user
     *
     * @param array $values
     * @param int $userid
     */
    public function edit($values, $userid)
    {
        // parse data
        $data = array(
            'email%s'     => $values['email'],
            'name%s'      => $values['name'],
            'surname%s'   => $values['surname'],
            'role'        => $values['role'],
            'updated%sql' => 'NOW()'
        );

        // checking whether to update password or not
        if($values['password'] != '') {
            $data['password%s'] = md5($values['password']);
        }

        // executing query
        $this->db->query("
            UPDATE [:pref:users]
            SET %a
            WHERE `id` = %i", $data, $values['id']
        );
    }

    /**
     * Activate given user
     *
     * @param int $id
     */
    public function activate($id)
    {
        // executing query
        $result = $this->db->query("UPDATE [:pref:users] SET `status` = 1 WHERE `id` = %i", $id);
    }

    /**
     * Deactivate given user
     *
     * @param int $id
     */
    public function deactivate($id)
    {
        // executing query
        $result = $this->db->query("UPDATE [:pref:users] SET `status` = 0 WHERE `id` = %i", $id);
    }

    /**
     * Return array of roles
     *
     * @return array
     */
    public function getRoles()
    {

        return $this->roles;

    }

    /**
     * Return array of allowed roles roles
     *
     * @return array
     */
    public function getAllowedRoles()
    {

        return $this->allowedRoles;

    }

}
