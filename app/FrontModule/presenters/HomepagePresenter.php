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

    /** @var \App\Models\News */
    private $news;

    protected function startup()
    {
        parent::startup();


        // getting signal
        $this->pages = $this->context->getService('pages');
        $this->signals = $this->context->getService('signals');
        $this->news = $this->context->getService('news');

    }

    /* ----- Renders ---------------------------------------------------------------- */

    public function renderDefault($id = NULL)
    {
        $page = $this->pages->getSingleBySlug('uvodni-strana');

        $signals = $this->signals->getAll([
            'limit' => 4,
            'orderby' => 'RAND()',
            'where' => [
                'public' => 1
            ],
            'with-acknowledgements' => true
        ]);

        foreach($signals as $signal) {
            $path = $this->context->parameters['signalImgPath'] . '/' . $signal->image_path;
            if(is_file($path)) {
                $signal->image = $this->context->parameters['signalImgUrl'] . '/' . $signal->image_path;
            } else {
                $signal->image = false;
            }
        }

        // set active page
        $this['topMenu']->setActive('uvodni-strana');
        $news = $this->news->getHotNews(5);

        $this->template->page       = $page;
        $this->template->signals    = $signals;
        $this->template->news       = $news;
    }
}
