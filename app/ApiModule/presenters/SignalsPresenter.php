<?php
namespace App\ApiModule\Presenters;

use Tracy\Debugger;
use Nette\Application\UI\Form;
use Nette\Utils\Html;

/**
 * User administration presenter.
 *
 * @author     Martin Kryl
 * @package    Horizont2050
 */
class SignalsPresenter extends ApiPresenter
{

    /** @var \Signals */
    private $signals;

    protected function startup()
    {
        parent::startup();

        $this->signals = $this->context->signals;
    }

    /* ----- Renders ---------------------------------------------------------------- */

    public function renderDefault()
    {
        $fields = array('id', 'name', 'title', 'user_update', 'user_create');
        $signals = $this->signals->getAllSelectiveFields($fields);

        $this->payload->count = count($signals);
        $this->payload->results = $signals;
        $this->sendPayload();

        /*
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
        */
    }
}
