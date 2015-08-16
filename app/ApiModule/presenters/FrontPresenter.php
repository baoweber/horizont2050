<?php
namespace App\ApiModule\Presenters;

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
class ApiPresenter extends \BasePresenter
{
    protected function startup()
    {
        parent::startup();
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
        $str .= ' at ';
        $str .= implode(', ', $sites);
        $str .= '.';

        $this->template->acknowledgement = $str;
    }
}