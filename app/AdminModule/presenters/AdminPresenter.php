<?php
namespace App\AdminModule\Presenters;

/**
 * Challenges presenter
 *
 * @author     Martin Kryl, COŽP UK
 * @package    Horizont2050
 */
class AdminPresenter extends \BasePresenter
{

    public $menuItem;

    /**
     * Startup method of the presenter
     */
    protected function startup()
    {
        parent::startup();

        // user authentication
        if (!$this->user->isLoggedIn()) {
            if ($this->user->logoutReason === \Nette\Http\UserStorage::INACTIVITY) {
                $this->flashMessage('You have been signed out due to inactivity. Please sign in again.');
            }
            $this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
        }

        $this->template->tsep = ' ';
        $this->template->decimal = '.';

    }

    /**
     * Initiation of the top menu component fot the use in templates
     *
     * @return array TopMenu
     */
    protected function createComponentTopMenu()
    {
        $menu = new TopMenu();

        // na tomto místě by se od komponenty vkládala data
        return $menu;
    }

}