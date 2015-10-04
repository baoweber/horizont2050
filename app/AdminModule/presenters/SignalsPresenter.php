<?php
namespace  App\AdminModule\Presenters;

use Nette\Diagnostics\Debugger;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Nette\Utils\Html;

/**
 * User administration presenter.
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class SignalsPresenter extends AdminPresenter
{

    /** @var \App\Models\Signals */
    private $signals;

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

    /** @var \App\Models\Acknowledgements */
    private $acknow;

    /** @persistent */
    public $nameFilter = '';

    /** @persistent */
    public $activeTab = 1;

    private $signalId;

    private $impacts, $scales, $timeframe;

    /**
     * Startup method of the presenter
     */
    protected function startup()
    {
        parent::startup();

        $this->menuItem = $this->getName();

        // vytvoří instanci služby a uloží do vlastnosti presenteru
        $this->signals = $this->context->getService('signals');
        $this->spaces = $this->context->getService('spaces');
        $this->eventTypes = $this->context->getService('eventTypes');
        $this->keywords = $this->context->getService('keywords');
        $this->sources = $this->context->getService('sources');
        $this->challenges = $this->context->getService('challenges');
        $this->strategies = $this->context->getService('strategies');
        $this->acknow = $this->context->getService('acknowledgements');

        $this->impacts = array(
            1 => 'Nežádoucí',
            2 => 'Žádoucí',
            3 => 'Ambivalentní',
            4 => 'Nejistý',
            5 => 'Nezobrazovat'
        );

        $this->scales = array(
            1 => 'Globální',
            2 => 'Evropský',
            3 => 'ČR',
            4 => 'Lokální'
        );

        $this->timeframe = array(
            1 => 'krátkodobý (do 2020)',
            2 => 'střednědobý (do 2030)',
            3 => 'dlouhodobý (do 2050)',
            4 => 'kdykoli (divoké karty) '
        );

        $this->template->event_types = $this->eventTypes->getPairs('id', 'label');
        $this->template->spaces = $this->spaces->getPairs('id', 'label');
        $this->template->impacts = $this->impacts;
        $this->template->scales = $this->scales;

        $this->template->active = $this->activeTab;
    }

    /* ----- Renders ---------------------------------------------------------------- */

    /**
     * Renders the list of signals
     */
    public function renderDefault()
    {

        $params = array(
            'orderby' => 'name',
            'with-acknowledgements' => true
        );

        if ($this->nameFilter != '') {
            $signals = $this->signals->search($this->nameFilter, array('name'), $params);
        } else {
            $signals = $this->signals->getAll($params);
        }

        // debug
        Debugger::barDump($signals, "All Signals");

        // assigning the variable to a template
        $this->template->signals = $signals;
        $this->template->nameFilter = $this->nameFilter;
    }

    /**
     * Renders view (main edit page) for a specified signal
     *
     * @param int $id
     * @param null $acknowledgementId
     */
    public function renderView($id, $acknowledgementId = null, $sourceId = null)
    {

        // getting the signal from the DB
        $data = $this->signals->getSingle($id);

        // timeframe - DIRTY FIX
        if(!in_array($data->timeframe, array(1,2,3,4))) {
            unset($data->timeframe);
        }

        // adjusting dates
        $data->user_create = date('Y-m-d', strtotime($data->user_create));
        $data->user_update = date('Y-m-d', strtotime($data->user_update));

        // assigning data to the main form
        $form = $this['signalForm'];
        $form->setDefaults($data);
        $form['submit']->caption = 'upravit';

        // assigning data to the text form
        $form2 = $this['textsForm'];
        $form2->setDefaults($data);
        $form2['submit']->caption = 'upravit';

        // assigning id
        $this['keywordsForm']->setDefaults(array('signals_id' => $data->id));
        $this['strategiesForm']->setDefaults(array('signals_id' => $data->id));
        $this['challengesForm']->setDefaults(array('signals_id' => $data->id));

        // acknowledgements
        if($sourceId) {
            $this->activeTab = 4;
            $ackData = $this->sources->getSingle($sourceId);
            $this['sourcesForm']->setDefaults($ackData);
        } else {
            $this['sourcesForm']->setDefaults(array('signals_id' => $data->id));
        }

        // acknowledgements
        if($acknowledgementId) {
            $this->activeTab = 6;
            $ackData = $this->acknow->getSingle($acknowledgementId);
            $this['acknowForm']->setDefaults($ackData);
        } else {
            $this['acknowForm']->setDefaults(array('signals_id' => $data->id));
        }


        //getting assigned keywords
        $assigned = $this->keywords->getAssignedKeywords($data->id);

        //getting assigned strategies
        $strategies = $this->strategies->getAssignedStrategies($data->id);

        //getting assigned challenges
        $challenges = $this->challenges->getAssignedChallenges($data->id);

        // getting sources
        $sources = $this->sources->getAllSignalSources($data->id);

        // ---------- IMAGE
        // assigning data to image form
        $this['imageForm']->setDefaults(array('signals_id' => $id));

        // processing image
        $path = $this->context->parameters['signalImgPath'] . '/' .$data->image_path;

        if(is_file($path)) {
            $url  = $this->context->parameters['signalImgUrl'] . '/' . $data->image_path;
            $this->template->imagePath = $url;
        } else {
            $this->template->imagePath = false;
        }

        // ---------- ASSIGNING VARIABLES TO THE TEMPLATE
        $this->template->signal = $data;
        $this->template->assignedKeywords = $assigned;
        $this->template->sources = $sources;
        $this->template->strategies = $strategies;
        $this->template->challenges = $challenges;
        $this->template->active = $this->activeTab;

        // output the signal data
        Debugger::barDump($data, "Signals");
    }

    /* ----- Actions ---------------------------------------------------------------- */

    /**
     * Deletes signal
     *
     * @param int $id
     */
    public function actionDelete($id)
    {

        if ($this->user->isAllowed($this->name, $this->action)) {

            if (isset($id) && $id) {

                $this->signals->delete($id);
            }
        }

        $this->redirect('default');
    }

    /**
     * Renders update form
     *
     * @param int $id
     */
    public function actionUpdate($id)
    {

        if ($this->user->isAllowed($this->name, $this->action)) {

            if (isset($id) && $id) {

                // gettin value for the item
                $data = $this->signals->getSingle($id);

                // assignig data and submit caption
                $form = $this['signalForm'];
                $form->setDefaults($data);
                $form['submit']->caption = 'upravit';

                $this['imageForm']->setDefaults(array('signals_id' => $id));

                // chekcing file
                $path = $this->context->parameters['signalImgPath'] . '/' .$data->image_path;

                if(is_file($path)) {
                    $url  = $this->context->parameters['signalImgUrl'] . '/' . $data->image_path;
                    $this->template->imagePath = $url;
                } else {
                    $this->template->imagePath = false;
                }

            } else {
                $this->flashMessage('Invalid data passed.', 'alert');
                $this->redirect('default');
            }
        } else {
            $this->flashMessage('You dont have access to this action.', 'alert');
            $this->redirect('default');
        }

	    $this->template->action = 'update';

        $this->setView('form');

    }

    /**
     * Renders insert form
     */
    public function actionInsert()
    {

        if (!$this->user->isAllowed($this->name, $this->action)) {
            $this->flashMessage('You dont have access to this action.', 'alert');
            $this->redirect('default');
        }

        $this->template->imagePath = false;
        $this->template->action = 'insert';

        $this->setView('form');

    }

    /**
     * Removes given keyword from a signal
     *
     * @param int $id
     * @param int $keywords_id
     */
    public function actionRemoveKeyword($id, $keywords_id)
    {

        $this->keywords->unassign($id, $keywords_id);

        $this->setView('view');

    }

    /**
     * Removes given strategy from a signal
     *
     * @param int $id
     * @param int $strategies_id
     */
    public function actionRemoveStrategy($id, $strategies_id)
    {

        $this->strategies->unassign($id, $strategies_id);

        $this->activeTab = 3;

        $this->setView('view');

    }

    /**
     * Removes given challenge from a signal
     *
     * @param $id
     * @param $challenges_id
     */
    public function actionRemoveChallenge($id, $challenges_id)
    {

        $this->challenges->unassign($id, $challenges_id);

        $this->activeTab = 5;

        $this->setView('view');

    }

    /**
     * Removes given source from a signal
     *
     * @param int $id
     * @param int $sources_id
     */
    public function actionRemoveSource($id, $sources_id)
    {

        $this->sources->delete($sources_id);

        $this->activeTab = 4;

        $this->setView('view');
    }

    /**
     * Removes given source from a signal
     *
     * @param int $id
     * @param int $sources_id
     */
    public function actionRemoveAcknowledgement($id, $acknow_id)
    {

        $this->acknow->delete($acknow_id);

        $this->activeTab = 6;

        $this->setView('view');
    }

    /**
     * Activates given signal
     *
     * @param $id
     */
    public function actionActivate ($id)
    {
        $this->signals->activate($id);

        $this->redirect('default');
    }

    /**
     * Deactivates signal
     *
     * @param $id
     */
    public function actionDeactivate ($id)
    {
        $this->signals->deactivate($id);

        $this->redirect('default');
    }

    /* ----- Components --------------------------------------------------------------- */

    /**
     * Generates the update form for basic parameters of the signal
     *
     * @return \VerticalForm12
     */
    protected function createComponentSignalForm()
    {

        $form = new \VerticalForm12;
        $form->getElementPrototype()->class('custom');

        // adding input id
        $form->addHidden('id'); // (toto pridat)

        $form->addSelect('categories_id', 'Kategorie',
            array(
                1 => 'divoká karta',
                2 => 'slabý signál',
                3 => 'trend',
                4 => 'megatrend',
                5 => 'metatrend'
            )
        )->setRequired('Vyplňte prosím kategorii.')
            ->setPrompt('vyberte prosím kategorii');

        /*
        $form->addSelect('spaces_id', 'Tematická oblast', $this->spaces->getPairs('id', 'label'))
            ->setRequired('Vyplňte prosím tematickou oblast.')
            ->setPrompt('vyberte prosím tematickou oblast');
        */

        $form->addSelect('event_types_id', 'Typ události', $this->eventTypes->getPairs('id', 'label'))
            ->setRequired('Vyplňte prosím typ události.')
            ->setPrompt('vyberte prosím typ události');

        $form->addText('name', 'Krátký název');

        $form->addText('title', 'Titulek');

        $form->addTextArea('perex', 'Shrnutí');

        $form->addText('relevance', 'Závažnost');

        $form->addText('probability', 'Pravděpodobnost');

        $impact = $this->impacts;

        $form->addSelect('impacts_id', 'Dopad signálu', $impact)
            ->setRequired('Vyplňte prosím klasifikaci události.')
            ->setPrompt('vyberte prosím klasifikaci události');

        $scale = array(
            1 => 'Globální',
            2 => 'Evropský',
            3 => 'ČR',
            4 => 'Lokální'
        );

        $form->addSelect('scales_id', 'Rozsah', $scale)
            ->setRequired('Vyplňte prosím rozsah události.')
            ->setPrompt('vyberte prosím rozsah události');

        $form->addSelect('timeframe', 'Časový horizont', $this->timeframe)
            ->setRequired('Vyplňte prosím časový horizont.')
            ->setPrompt('vyberte prosím časový horizont události');

        $form->addText('ptree', 'PearlTree odkaz');

        $form->addText('user_create', 'Datum vytvoření');

        $form->addText('user_update', 'Datum úpravy');

        $form->addSubmit('submit', 'vložit')
            ->getControlPrototype()
                ->setClass('button');

        // callback method on success
        $form->onSuccess[] = callback($this, "processSignalForm");

        // returining form
        return $form;

    }

    /**
     * Handles the output of the SignalForm component
     *
     * @param Form $form
     */
    public function processSignalForm(Form $form)
    {
        $this->activeTab = 1;
        // getting values
        $values = $form->form->getValues();

        // adjusting values
        if(isset($values['update_create']) && $values['update_create'] == '') {
            $values['user_update'] = new DateTime();
        }

        // adjusting values
        if(isset($values['update_date']) && $values['update_date'] == '') {
            $values['user_update'] = new DateTime();
        }

        // cheking if insert or update
        if ($values->id) {

            // update
            $this->signals->update($values->id, $values, $this->user->getId());

            // flash message
            $this->flashMessage('Signál byl upraven.', 'success');
        }

        // redirecting
        $this->redirect('view', $values->id);
    }

    /**
     * Generates the update form component for texts of the signal
     *
     * @return \VerticalForm12
     */
    protected function createComponentTextsForm()
    {
        $form = new \VerticalForm12;
        $form->getElementPrototype()->class('custom');

        // adding input id
        $form->addHidden('id'); // (toto pridat)

        $form->addTextArea('description', 'Popis signálu');

        $form->addTextArea('drivers', 'Hnací síly');

        $form->addTextArea('impact', 'Dopady');

        $form->addTextArea('likelyhood', 'Pravděpodonost');

        $form->addTextArea('recomendations', 'Doporučená opatření');

        $form->addSubmit('submit', 'vložit')
            ->getControlPrototype()
            ->setClass('button');

        // callback method on success
        $form->onSuccess[] = callback($this, "processTextsForm");

        // returining form
        return $form;

    }

    /**
     * Handles the output of the TextsForm component
     *
     * @param Form $form
     */
    public function processTextsForm(Form $form)
    {

        // getting values
        $values = $form->form->getValues();

        // adjusting values

        // cheking if insert or update
        if ($values->id) {
            // update
            $this->signals->update($values->id, $values, $this->user->getId());

            // flash message
            $this->flashMessage('Signál byl upraven.', 'success');
        }

        $this->activeTab = 2;

        // redirecting
        $this->redirect('view', $values->id);
    }

    /**
     * Generates a create form component
     *
     * @return \VerticalForm12
     */
    protected function createComponentCreateSignalForm()
    {

        $form = new \VerticalForm12;
        $form->getElementPrototype()->class('custom');

        // adding input id
        $form->addHidden('id'); // (toto pridat)

        $form->addSelect('categories_id', 'Kategorie',
            array(
                1 => 'divoká karta',
                2 => 'slabý signál',
                3 => 'trend',
                4 => 'megatrend',
                5 => 'metatrend'
            )
        )->setRequired('Vyplňte prosím kategorii.')
            ->setPrompt('vyberte prosím kategorii');


        $form->addText('name', 'Krátký název');

        $form->addText('title', 'Titulek');

        $form->addSubmit('submit', 'vložit')
            ->getControlPrototype()
            ->setClass('button');

        // callback method on success
        $form->onSuccess[] = callback($this, "processCreateSignalForm");

        // returining form
        return $form;

    }

    /**
     * Handles the output of the CreateSignal component
     *
     * @param Form $form
     */
    public function processCreateSignalForm(Form $form)
    {
        // getting values
        $values = $form->form->getValues();

        // insert
        $id = $this->signals->insert($values, $this->user->getId());

        //flashMessage
        $this->flashMessage('Signál byl vytvořen.', 'success');

        // redirecting
        $this->redirect('view', $id);
    }

    /**
     * Generates the upload image component
     *
     * @return \VerticalForm12
     */
    public function createComponentImageForm()
    {
        $form = new \InlineForm();

        // adding input id
        $form->addHidden('signals_id');

        $form->addUpload('image', '')
        ->addRule(Form::IMAGE, 'Avatar musí být JPEG, PNG nebo GIF.')
        ->addRule(Form::MAX_FILE_SIZE, 'Maximální velikost souboru je 2 MB.', 2 * 1024 * 1024 /* v bytech */);

        $form->addSubmit('submit', 'nahrát obrázek')
            ->getControlPrototype()
            ->class('button small secondary');


        // callback method on success
        $form->onSuccess[] = callback($this, "processImageForm");

        // returning form
        return $form;
    }

    /**
     * Handles the output of the ImageForm component
     *
     * @param Form $form
     */
    public function processImageForm(Form $form, $values)
    {
        $this->activeTab = 1;
        $file = $values['image'];

        if($file->isOk() AND $file->isImage()) {
            $ext        = strtolower(pathinfo($file->getName(), PATHINFO_EXTENSION));
            $filename   = 'signal_' . $values->signals_id .  "." . $ext;
            $file->move($this->context->parameters['signalImgPath'] . '/' . $filename);

            // saving values
            $this->signals->update($values['signals_id'], array('image_path' =>  $filename), $this->user->id);
        }

        $this->redirect('view', $values->signals_id);
    }

    /**
     * Generates the attach keywords component
     *
     * @return \VerticalForm12
     */
    public function createComponentKeywordsForm($id)
    {
        // todo MASIVNE PRDELAT

        $keywords = $this->keywords->getPairs('id', 'label');
        $assigned = $this->keywords->getAssignedKeywords($this->getParam('id'));

        $remove = array();
        foreach ($assigned as $key => $value) {
            $remove[] = $value->keywords_id;
        }

        foreach ($keywords as $key => $value) {
            if (in_array($key, $remove)) {
                unset($keywords[$key]);
            }
        }


        $form = new \VerticalForm12;
        $form->getElementPrototype()->class('custom');

        // adding input id
        $form->addHidden('signals_id');

        $form->addSelect('keywords_id', 'Vyberte existující klíčové slovo:', $keywords)
            ->setPrompt('vyberte klíčové slovo');

        $form->addText('label', 'Nebo zadejte nové:');

        $form->addSubmit('submit', 'vložit');

        // callback method on success
        $form->onSuccess[] = callback($this, "processKeywordsForm");

        // returining form
        return $form;
    }

    /**
     * Handles the output of the Keywords component
     *
     * @param Form $form
     */
    public function processKeywordsForm(Form $form)
    {
        // getting values
        $values = $form->form->getValues();
        $this->activeTab = 1;

        // adjusting values


        // cheking if insert or update
        if ($values->label == '') {

            // insering existing
            $this->keywords->assignExisting($values->signals_id, $values->keywords_id);

        } else {

            // assign new
            $this->keywords->assignNew($values->signals_id, $values->label, $this->user->id);

        }

        // redirecting
        $this->redirect('view', $values->signals_id);
    }

    /**
     * Generates the attach sources component
     *
     * @return \VerticalForm12
     */
    public function createComponentSourcesForm()
    {

        $form = new \VerticalForm12;

        // adding input id
        $form->addHidden('signals_id');

        $form->addHidden('id');

        $form->addSelect('type', 'Typ zdroje', $this->context->parameters['sources'])
            ->setPrompt('Vyberte typ zdroje')
            ->setRequired('Uveďte prosím typ zdroje.');

        $form['type']
            ->addCondition($form::EQUAL, 1)
                ->toggle('study');

        $form->addText('name', 'Název')
            ->setRequired('Uveďte prosím název zdroje.');

        $form->addText('author', 'Autor');

        $form->addText('date', 'Datum vydání')
            ->getControlPrototype()
            ->class('datepicker');

        $form->addText('pages', 'Strany');

        $form->addText('in', 'Sborník / časopis');

        $form->addText('publisher', 'Nakladatel');

        $form->addText('ISBN', 'ISBN / DOI');

        $form->addText('url', 'Odkaz (pokud existuje)');

        $form->addSubmit('submit', 'vložit')
            ->getControlPrototype()
            ->class('button small');

        // callback method on success
        $form->onSuccess[] = callback($this, "processSourcesForm");

        // returning form
        return $form;
    }

    /**
     * Handles the output of the SourcesForm component
     *
     * @param Form $form
     */
    public function processSourcesForm(Form $form)
    {
        // getting values
        $values = $form->form->getValues();

        $this->activeTab = 4;

        if($values->id) {
            $this->sources->update($values->id, $values, $this->user->getId());
        } else {
            $this->sources->insert($values, $this->user->getId());
        }

        // redirecting
        $this->redirect('view', $values->signals_id);
    }

    /**
     * Generates the attach strategies component
     *
     * @return \VerticalForm12
     */
    public function createComponentStrategiesForm()
    {

        $strategies = $this->strategies->getPairs('id', 'name');
        $assigned = $this->strategies->getAssignedStrategies($this->getParam('id'));

        if(count($assigned)) {
            foreach($assigned as $item) {

                if(isset($strategies[$item->strategies_id])) {
                    unset($strategies[$item->strategies_id]);
                }
            }
        }

        $form = new \InlineForm();

        // adding input id
        $form->addHidden('signals_id');

        $form->addSelect('strategies_id', 'Připojit existující', $strategies)
            ->setPrompt('Vyberte strategii');

        $form->addSubmit('submit', 'připojit')
            ->getControlPrototype()
            ->class('button small');


        // callback method on success
        $form->onSuccess[] = callback($this, "processStrategiesForm");

        // returning form
        return $form;
    }

    /**
     * Handles the output of the Strategies component
     *
     * @param Form $form
     */
    public function processStrategiesForm(Form $form)
    {
        // getting values
        $values = $form->form->getValues();

        $this->activeTab = 3;

        // insering existing
        $this->strategies->assignExisting($values->signals_id, $values->strategies_id);

        // redirecting
        $this->redirect('view', $values->signals_id);
    }

    /**
     * Generates the attach challenges component
     *
     * @return \VerticalForm12
     */
    public function createComponentChallengesForm()
    {

        $challenges = $this->challenges->getPairs('id', 'name');
        $assigned = $this->challenges->getAssignedChallenges($this->getParam('id'));

        if(count($assigned)) {
            foreach($assigned as $item) {
                if(isset($challenges[$item->challenges_id])) {
                    unset($challenges[$item->challenges_id]);
                }
            }
        }

        $form = new \InlineForm();

        // adding input id
        $form->addHidden('signals_id');

        $form->addSelect('challenges_id', 'Připojit existující', $challenges)
            ->setPrompt('Vyberte grand challenge');

        $form->addSubmit('submit', 'připojit')
            ->getControlPrototype()
            ->class('button small secondary');


        // callback method on success
        $form->onSuccess[] = callback($this, "processChallengesForm");

        // returning form
        return $form;
    }

    /**
     * Handles the output of the ChallengesForm component
     *
     * @param Form $form
     */
    public function processChallengesForm(Form $form)
    {
        // getting values
        $values = $form->form->getValues();

        $this->activeTab = 5;

        // insering existing
        $this->challenges->assignExisting($values->signals_id, $values->challenges_id);

        // redirecting
        $this->redirect('view', $values->signals_id);
    }

    /**
     * Generates the attach challenges component
     *
     * @return \VerticalForm12
     */
    public function createComponentAcknowForm()
    {
        $form = new \VerticalForm12;

        // adding input id
        $form->addHidden('signals_id');

        $form->addHidden('id');

        $form->addText('site', 'Webový portál');

        $form->addText('author', 'Autor');

        $form->addText('year', 'Rok');

        $form->addSubmit('submit', 'uložit')
            ->getControlPrototype()
            ->class('button small secondary');


        // callback method on success
        $form->onSuccess[] = callback($this, "processAcknowForm");

        // returning form
        return $form;
    }

    /**
     * Handles the output of the ChallengesForm component
     *
     * @param Form $form
     */
    public function processAcknowForm(Form $form)
    {
        // getting values
        $values = $form->form->getValues();

        $this->activeTab = 6;

        if($values->id) {
            // insering existing
            $this->acknow->update($values->id, $values, $this->user->id);
        } else {
            // insering existing
            $this->acknow->insert($values, $this->user->id);
        }

        // redirecting
        $this->redirect('view', $values->signals_id);
    }

    /*
    public function createComponentChallengesGridForm()
    {

        $challenges = $this->challenges->getPairs('id', 'name');
        $assigned   = $this->challenges->getAssignedChallenges($this->getParam('id'));

        $form = new \InlineForm();

        // adding input id
        $form->addHidden('signals_id');

        foreach($assigned as $challenge) {
            $name = 'challenge_' .$challenge->challenges_id;

            $form->addRadioList($name, $challenge->name, array(
                1 => 'topíš se',
                2 => 'samá voda',
                3 => 'přihořívá',
                4 => 'přihořívá',
                5 => 'hoří'
            ))
                ->getControlPrototype()
                ->class('button small secondary');
        }

        $form->addSubmit('submit', 'upravit')
            ->getControlPrototype()
            ->class('button small secondary');


        // callback method on success
        $form->onSuccess[] = callback($this, "processChallengesGridForm");

        // returning form
        return $form;
    }

    public function processChallengesGridForm(Form $form)
    {

        // getting values
        $values = $form->form->getValues();

        $this->activeTab = 2;

        dump($values);
        die();


        // insering existing
        $this->challenges->assignExisting($values->signals_id, $values->challenges_id);

        // redirecting
        $this->redirect('view', $values->signals_id);

    }
    */

}
