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
}