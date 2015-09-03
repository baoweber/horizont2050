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
class HomepagePresenter extends FrontPresenter
{

    /** @var \App\Models\Pages */
    private $pages;

    /** @var \App\Models\Signals */
    private $signals;

    protected function startup()
    {
        parent::startup();


        // getting signal
        $this->pages = $this->context->getService('pages');
        $this->signals = $this->context->getService('signals');
    }

    /* ----- Renders ---------------------------------------------------------------- */

    public function renderDefault($id = NULL)
    {
        $page = $this->pages->getSingleBySlug('uvodni-strana');

        //getting signal and image path
        $signal = $this->signals->getSingle(26);
        $path = $this->context->parameters['signalImgPath'] . '/' . $signal->image_path;
        if(is_file($path)) {
            $signal->image = $this->context->parameters['signalImgUrl'] . '/' . $signal->image_path;
        } else {
            $signal->image = false;
        }

        // set active page
        $this['topMenu']->setActive('uvodni-strana');

        $this->template->page       = $page;
        $this->template->signal     = $signal;
    }
}
