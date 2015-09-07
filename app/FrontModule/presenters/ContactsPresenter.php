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
class ContactsPresenter extends FrontPresenter
{

    /** @var \App\Models\Pages */
    private $pages;

    protected function startup()
    {
        parent::startup();

        // getting signal
        $this->pages = $this->context->getService('pages');
    }

    /* ----- Renders ---------------------------------------------------------------- */

    public function renderDefault($id = NULL)
    {
        $page = $this->pages->getSingleBySlug('uvodni-strana');

        if($page->parent) {
            $params['where']['parent'] = $page->parent;
        } else {
            $params['where']['parent'] = $page->id;
        }

        // set active page
        $this['topMenu']->setActive('tym');

        $this->template->left_menu  = $this->pages->getAll($params);

    }
}
