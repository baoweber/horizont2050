<?php
namespace App\FrontModule\Presenters;

use Nette\Diagnostics\Debugger;
use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Nette;
use Latte;
use Latte\Macros;


/**
 * User administration presenter.
 *
 * @author     Martin Kryl
 * @package    Horizont2050
 */
class FrontPresenter extends \BasePresenter
{

    /** @var \Pages */
    private $pages;

    protected function startup()
    {
        parent::startup();

        $this->pages = $this->context->pages;


        $latte = new Latte\Engine;
        // vytvoříme si sadu
        $set = new Macros\MacroSet($latte->getCompiler());

        // do sady přidáme párové makro {try} ... {/try}
        $set->addMacro(
            'aaa', // název makra
            'Ahoj {',  // PHP kód nahrazující otevírací značku
            'KonecAhoj' // kód nahrazující uzavírací značku
        );

    }

    protected function createComponentTopMenu()
    {
        $model = $this->pages;
        $menu = new TopMenu($model);

        // na tomto místě by se od komponenty vkládala data
        return $menu;

    }

    public function getAcknowledgement($signals)
    {

        $authors = array();
        $sites = array();

        foreach($signals as $signal) {
            if(is_array($signal->acknowledgements) && count($signal->acknowledgements)) {
                $site = $signal->acknowledgements[0]['site'];
                $author = $signal->acknowledgements[0]['author'];

                if (!in_array($author, $authors)) {
                    $authors[] = $author;
                }

                if (!in_array($site, $sites)) {
                    $sites[] = $site;
                }
            }
        }

        $str = 'Images are the courtesy of ';
        $str .= implode(', ', $authors);
        $str .= ' from the sites ';
        $str .= implode(', ', $sites);
        $str .= '.';

        $this->template->acknowledgement = $str;

    }

}