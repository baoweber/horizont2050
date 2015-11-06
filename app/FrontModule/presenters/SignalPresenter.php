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
class SignalPresenter extends FrontPresenter
{

    /** @var \App\Models\Signals */
    private $signals;

    /** @var \App\Models\Pages */
    private $pages;

    /** @var \App\Models\Spaces */
    private $spaces;

    /** @var \App\Models\EventTypes */
    private $eventTypes;

    /** @var \App\Models\Sources */
    private $sources;

    /** @var \App\Models\Keywords */
    private $keywords;

    /** @var \App\Models\Strategies */
    private $strategies;

    /** @var \App\Models\Challenges */
    private $challenges;

    private $impacts, $scales;

    private $sourcesIds, $sourcesTexts, $timeframe, $categories;

    protected function startup()
    {
        parent::startup();

        $this->signals = $this->context->getService('signals');
        $this->pages = $this->context->getService('pages');
        $this->spaces = $this->context->getService('spaces');
        $this->eventTypes = $this->context->getService('eventTypes');
        $this->keywords = $this->context->getService('keywords');
        $this->sources = $this->context->getService('sources');
        $this->challenges = $this->context->getService('challenges');
        $this->strategies = $this->context->getService('strategies');

        // set active page
        $this['topMenu']->setActive('database');

        $this->impacts = array(
            1 => 'Nežádoucí',
            2 => 'Žádoucí',
            3 => 'Ambivalentní',
            4 => 'Nejistý',
            5 => 'Nezobrazovat'
        );

        $this->categories = array(
            1 => 'Divoká karta',
            2 => 'Slabý signál',
            3 => 'Trend',
            4 => 'Megatrend',
            5 => 'Metatrend'
        );

        $this->scales = array(
            1 => 'Globální',
            2 => 'Evropský',
            3 => 'ČR',
            4 => 'Lokální'
        );

        $this->timeframe = array(
            1 => 'Krátkodobý (do 2020)',
            2 => 'Střednědobý (do 2030)',
            3 => 'Dlouhodobý (do 2050)',
            4 => 'Kdykoli'
        );

        $this->template->event_types = $this->eventTypes->getPairs('id', 'label');
        $this->template->spaces = $this->spaces->getPairs('id', 'label');
        $this->template->impacts = $this->impacts;
        $this->template->scales = $this->scales;
        $this->template->timeframe = $this->timeframe;
        $this->template->categories= $this->categories;

    }

    /* ----- Renders ---------------------------------------------------------------- */

    public function renderDefault($id = NULL)
    {

        $signal = $this->signals->getSingle($id);

        // adding image
        // chekcing file
        $path = $this->context->parameters['signalImgPath'] . '/' . $signal->image_path;

        if(is_file($path)) {
            $url  = $this->context->parameters['signalImgUrl'] . '/' . $signal->image_path;
            $signal->image = $url;
        } else {
            $signal->image = false;
        }

        //getting assigned keywords
        $assigned = $this->keywords->getAssignedKeywords($signal->id);

        //getting assigned strategies
        $strategies = $this->strategies->getAssignedStrategies($signal->id);

        //getting assigned challenges
        $challenges = $this->challenges->getAssignedChallenges($signal->id);

        // getting sources
        $sources = $this->sources->getAllSignalSources($signal->id);

        // get cources Arrags
        $this->getSourcesArrays($sources);

        $signal->relevance = $this->signals->getRelevanceStatus($signal);

        // processing texts for sources
        $signal->description        = $this->processSources($signal->description);
        $signal->impact             = $this->processSources($signal->impact);
        $signal->likelyhood         = $this->processSources($signal->likelyhood);
        $signal->drivers            = $this->processSources($signal->drivers);
        $signal->recomendations     = $this->processSources($signal->recomendations);

        // sdding template variables
        $this->template->assignedKeywords = $assigned;
        $this->template->sources = $sources;
        $this->template->strategies = $strategies;
        $this->template->challenges = $challenges;
        $this->template->signal = $signal;
        $this->template->relevanceArr = $this->context->parameters['relevance'];
        $this->template->acknowledgement = isset($signal->acknowledgements[0]) ? 'The image is a courtesy of ' . $signal->acknowledgements[0]->author . ' from the site ' . $signal->acknowledgements[0]->site .  '.' : '';


        // debug output
        Debugger::barDump($signal, 'SIGNAL');
        Debugger::barDump($sources, 'SOURCES');
        Debugger::barDump($challenges, 'CHALLENGES');

        //$this->template->left_menu = array();

    }

    /**
     * @param $id

     */
    private function processSources($text)
    {
        // getting the source matches
        preg_match_all('/~~[0-9]+~~/', $text, $matches);


        foreach ($matches[0] as $item) {
            $id = str_replace('~~', '', $item);
            $link = '<a href="#source' . $id . '" title="' . $this->sourcesTexts[$id] . '">[' . $this->sourcesIds[$id] . ']</a>';
            $text = str_replace($item, $link, $text);
        }

        return $text;
    }

    private function getSourcesArrays($sources){

        // parsing sources
        $this->sourcesIds     = array();
        $this->sourcesTexts   = array();
        $i = 1;
        foreach($sources as $source) {
            $this->sourcesIds[$source->id]    = $i;
            $this->sourcesTexts[$source->id]  = $source->name;
            $i++;
        }
    }

}
