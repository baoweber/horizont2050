<?php
namespace App\FrontModule\Presenters;

use Nette\Diagnostics\Debugger;
use Nette\Application\UI\Form;
use Nette\Utils\Html;

/**
 * User administration presenter.
 *
 * @author     Martin Kryl
 * @package    Horizont2050
 */
class DisplayPresenter extends FrontPresenter
{

    /** @var \Pages */
    private $pages;


    protected function startup()
    {
        parent::startup();

        $this->pages = $this->context->pages;
    }

    /* ----- Renders ---------------------------------------------------------------- */

    public function renderDefault($id = NULL)
    {

        if(!$id) {
            $this->redirect('Default', $this->context->parameters['homepageSlug']);
        }

        $page = $this->pages->getSingleBySlug($id);

        $this->redirectCheck($page);

        // getting left menu
        $params = array(
            'orderby' => '`ord` ASC',
            'where' => array(
                'active' => 1,
                'show_in_menu' => 1
            )
        );

        if($page->parent) {
            $params['where']['parent'] = $page->parent;
        } else {
            $params['where']['parent'] = $page->id;
        }

        // set active page
        $this['topMenu']->setActive($page->slug);

        $this->template->left_menu  = $this->pages->getAll($params);

        $this->template->page       = $page;


    }

    private function redirectCheck($databaseEntry){

        if(isset($databaseEntry->redirect) && $databaseEntry->redirect) {
            $this->redirectUrl($databaseEntry->redirect);
        }

    }

}
