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
class NewsPresenter extends FrontPresenter
{

    /** @var \App\Models\News */
    private $news;


    protected function startup()
    {
        parent::startup();

        $this->news = $this->context->getService('news');
    }

    /* ----- Renders ---------------------------------------------------------------- */

    public function renderDefault($id = NULL)
    {
        if(!$id || $id == $this->context->parameters['homepageSlug']) {
            $this->redirect('Homepage:');
        }
        if($id == 'tym') {
            $this->redirect('Contacts:');
        }

        $news_item = $this->news->getSingleBySlug($id);

        $this->redirectCheck($news_item);

        $this->template->page       = $news_item;
    }

    private function redirectCheck($databaseEntry){

        if(isset($databaseEntry->redirect) && $databaseEntry->redirect) {
            $this->redirectUrl($databaseEntry->redirect);
        }

    }

}
