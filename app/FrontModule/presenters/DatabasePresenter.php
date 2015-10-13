<?php
namespace App\FrontModule\Presenters;

use Tracy\Debugger;
use Nette\Application\UI\Form;
use Nette\Utils\Html;

/**
 * User administration presenter.
 *
 * @author     Martin Kryl
 * @package    Horizont2050
 */
class DatabasePresenter extends FrontPresenter
{

    /** @var \App\Models\Signals */
    private $signals;

    /** @var \App\Models\Pages */
    private $pages;

    /** @var \App\Models\Keywords */
    private $keywords;

    protected function startup()
    {
        parent::startup();

        $this->signals = $this->context->getService('signals');
        $this->pages = $this->context->getService('pages');
        $this->keywords = $this->context->getService('keywords');

        // set active page
        $this['topMenu']->setActive('database');

    }

    /* ----- Renders ---------------------------------------------------------------- */

    public function renderDefault()
    {
        $this->getSignals();
    }

    public function renderKeywords($selected = '')
    {
        $this->getSignals();
        $keywords = $this->keywords->getPairs('id', 'label');
        $this->template->keywords = $keywords;
        $this->template->selected = $selected;
    }

    private function getSignals()
    {
        $params = array(
            'with-acknowledgements' => true,
            'where' => array(
                'public%i' => 1
            )
        );
        $signals = $this->signals->getAll($params);

        foreach($signals as $signal) {

            // chekcing file
            $path = $this->context->parameters['signalImgPath'] . '/' . $signal->image_path;

            if(is_file($path)) {
                $url  = $this->context->parameters['signalImgUrl'] . '/' . $signal->image_path;
                $signal->image = $url;
            } else {
                $signal->image = false;
            }
        }

        Debugger::barDump($signals, 'SIGNALS');
        $this->template->signals = $signals;

        $id = 20;

        $params = array();
        if(isset($page->parent) && $page->parent) {
            $params['where']['parent'] = $page->parent;
        } else {
            $params['where']['parent'] = $id;
        }
        $this->template->left_menu  = $this->pages->getAll($params);

        // getting the proper acknowledgement string for the left menu
        $this->getAcknowledgement($signals);
    }
}
