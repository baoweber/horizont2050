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

    /** @var \App\Models\Signals */
    private $signals;

    /** @var \App\Models\Challenges */
    private $challenges;

    protected function startup()
    {
        parent::startup();

        $this->signals = $this->context->getService('signals');
        $this->challenges = $this->context->getService('challenges');
    }

    /* ----- Renders ---------------------------------------------------------------- */

    public function renderDefault()
    {
        $thumber = new \ThumberHelper($this->context);

        $fields = array('id', 'name', 'title', 'user_update', 'user_create', 'image_path', 'categories_id');
        $signals = $this->signals->getAllSelectiveFields($fields, true, true);

        foreach($signals as $signal) {

            // chekcing file
            $path = $this->context->parameters['signalImgPath'] . '/' . $signal->image_path;

            if(is_file($path)) {
                $url  = $this->context->parameters['signalImgUrl'] . '/' . $signal->image_path;
                $signal->image = $url;

                // creating creating thumb
                $signal->thumb_path = $thumber->thumber($signal->image, 200, 200, 'crop', '', true);
            } else {
                $signal->image = false;
                $signal->thumb_path = false;
            }

            //assigning challenges
            $challenges = $this->challenges->getAssignedChallenges($signal->id);
            foreach($challenges as $challenge) {
                $signal->challenges[] = array(
                    'id' => $challenge->challenges_id,
                    'name' => $challenge->name
                );
            }
        }

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
