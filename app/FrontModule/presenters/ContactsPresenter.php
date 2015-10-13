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
        $page = $this->pages->getSingleBySlug('tym');

        // set active page
        $this['topMenu']->setActive('tym');

        $staff['mirek'] = $this->pages->getSingleBySlug('miroslav-havranek');
        $staff['mirek']['photo'] = 'havranek.jpg';

        $staff['dana'] = $this->pages->getSingleBySlug('dana-kapitulcinova');
        $staff['dana']['photo'] = 'kapitulcinova.png';

        $staff['zuzana'] = $this->pages->getSingleBySlug('zuzana-martinkova');
        $staff['zuzana']['photo'] = 'martinkova.jpg';

        $staff['tereza'] = $this->pages->getSingleBySlug('tereza-ponocna');
        $staff['tereza']['photo'] = 'ponocna.jpg';

        $this->template->staff = $staff;
        $this->template->page = $page;

    }
}
