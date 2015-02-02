<?php
namespace App\AdminModule\Presenters;

/**
 * Administration top menu factory
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class TopMenu extends \Nette\Application\UI\Control
{

    /**
     * Renders the output
     */
    public function render()
    {
        // assigning template variables
        $links = array(
            'Homepage' => array(
                'link' => $this->presenter->link('Homepage:'),
                'text' => 'Domů'
            ),
            'Pages' => array(
                'link' => $this->presenter->link('Pages:', NULL),
                'text' => 'Texty'
            ),
            'Signals' => array(
                'link' => $this->presenter->link('Signals:'),
                'text' => 'Signály'
            ),
        );


        $linksRight['Settings'] = array(
            'link' => $this->presenter->link('Settings:'),
            'text' => 'nastavení'
        );

        if($this->presenter->user->isAllowed('Admin:Users')) {
            $linksRight['Users'] = array(
                'link' => $this->presenter->link('Users:'),
                'text' => 'Uživatlé'
            );
        }
        $linksRight['Logout'] = array(
            'link' => $this->presenter->link('Sign:out'),
            'text' => 'Odhlásit'
        );

        // determining active class
        // key of teh array have to correspond to the presenter name
        if(isset($links[$this->presenter->menuItem])) {
            $links[$this->presenter->menuItem]['active'] = true;
        }

        if(isset($linksRight[$this->presenter->menuItem])) {
            $linksRight[$this->presenter->menuItem]['active'] = true;
        }

        // assigning template file
        $this->template->setFile(__DIR__ . '/topMenu.latte');

        // assigning links
        $this->template->links = $links;

        // assigning links
        $this->template->linksRight = $linksRight;

        // rendering component
        $this->template->render();
    }
}