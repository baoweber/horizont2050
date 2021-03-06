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

    /** @var \App\Models\Keywords */
    private $keywords;

    protected function startup()
    {
        parent::startup();

        $this->signals = $this->context->getService('signals');
        $this->challenges = $this->context->getService('challenges');
        $this->keywords = $this->context->getService('keywords');
    }

    /* ----- Renders ---------------------------------------------------------------- */

    public function renderDefault()
    {
        $thumber = new \ThumberHelper($this->context);

        $fields = array('id', 'name', 'title', 'user_update', 'user_create', 'image_path', 'categories_id', 'relevance', 'probability');
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

            // sssigning keywords
            $keywords = $this->keywords->getAssignedKeywords($signal->id);
            if($keywords) {
                foreach($keywords as $item) {
                    $signal->keywords[] = array(
                        'id' => $item->keywords_id,
                        'name' => $item->label
                    );
                }
            }

            // thumb acknowledgement
            $signal->thumb_acknowledgement = isset($signal->acknowledgements[0]) ? 'The image is a Courtesy of ' . $signal->acknowledgements[0]->author . ' from the site ' . $signal->acknowledgements[0]->site .  '.' : '';
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
