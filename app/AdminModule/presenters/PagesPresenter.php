<?php
namespace  App\AdminModule\Presenters;
use Nette\Diagnostics\Debugger;
use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Nette\Utils\Strings;

/**
 * Pages presenter
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class PagesPresenter extends AdminPresenter
{

    /** @var \App\Models\Pages */
    private $pages;

    /** @persistent */
    public $parent = 0;

    /**
     * Startup method of the presenter
     */
    protected function startup()
    {
        parent::startup();

        $this->menuItem = $this->getName();

        // vytvoří instanci služby a uloží do vlastnosti presenteru
        $this->pages = $this->context->getService('pages');
    }

    /* ----- Renders ---------------------------------------------------------------- */

    /**
     * Renders list of available pages for category specified in $parent
     *
     * @param int $parent
     */
    public function renderDefault($parent = NULL)
    {

        $params = array(
            'orderby' => '`ord` ASC'
        );

        if ($this->parent) {
            $params['where'] = array('parent%iN' => $this->parent);
            $parentCategory = $this->pages->getSingle($this->parent);
        } else {
            $params['where'] = array('parent%iN' => NULL);
            $parentCategory  = false;
        }

        $pages = $this->pages->getAll($params);

        // getting breadcrumbs
        $docId = $parent;
        $breadcrumbs = array();
        while($docId != NULL) {
            $breadcrumbs[$docId] = $this->pages->getField($docId, 'menutitle');
            $docId =$this->pages->getParent($docId);
        }

        $breadcrumbs[0] = 'HOME';
        $breadcrumbs = array_reverse($breadcrumbs);


        // debug
        Debugger::barDump($pages, "All pages");

        // assigning the variable to a template
        $this->template->pages          = $pages;
        $this->template->parentCategory = $parentCategory;
        $this->template->breadcrumbs    = $breadcrumbs;
    }

    /**
     * Renders a view of specified document
     *
     * @param int $id
     */
    public function renderView($id)
    {

        $signal = $this->signals->getSingle($id);

        // debug
        Debugger::barDump($signal, "Signals");

        // assigning id
        $this['keywordsForm']->setDefaults(array('signals_id' => $signal->id));
        $this['sourcesForm']->setDefaults(array('signals_id' => $signal->id));
        $this['strategiesForm']->setDefaults(array('signals_id' => $signal->id));
        $this['challengesForm']->setDefaults(array('signals_id' => $signal->id));

        //getting assigned keywords
        $assigned = $this->keywords->getAssignedKeywords($signal->id);

        //getting assigned strategies
        $strategies = $this->strategies->getAssignedStrategies($signal->id);

        //getting assigned challenges
        $challenges = $this->challenges->getAssignedChallenges($signal->id);

        // getting sources
        $sources = $this->sources->getAllSignalSources($signal->id);

        // assigning the variable to a template
        $this->template->signal = $signal;
        $this->template->assignedKeywords = $assigned;
        $this->template->sources = $sources;
        $this->template->strategies = $strategies;
        $this->template->challenges = $challenges;
    }

    /**
     * Moves page up by on in it's category order
     *
     * @param int $id
     */
    public function actionMoveUp($id)
    {

        if (isset($id) && $id) {
            $this->pages->moveUp($id);
        }

        $this->redirect('default');
    }

    /**
     * Moves page down by on in it's category order
     *
     * @param int $id
     */
    public function actionMoveDown($id)
    {

        if (isset($id) && $id) {
            $this->pages->moveDown($id);
        }

        $this->redirect('default');
    }

    /**
     * Deletes specified page
     *
     * @param int $id
     */
    public function actionDelete($id)
    {

        if ($this->user->isAllowed($this->name, $this->action)) {

            if (isset($id) && $id) {

                $this->pages->delete($id);
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
                $data = $this->pages->getSingle($id);

                // assignig data and submit caption
                $form = $this['pageForm'];
                $form->setDefaults($data);
                $form['submit']->caption = 'upravit';

            } else {
                $this->flashMessage('Invalid data passed.', 'alert');
                $this->redirect('default');
            }
        } else {
            $this->flashMessage('You dont have access to this action.', 'alert');
            $this->redirect('default');
        }

        $this->setView('form');

    }

    /**
     * Renders insert form
     *
     * @param int $id
     */
    public function actionInsert($id)
    {

        if (!$this->user->isAllowed($this->name, $this->action)) {
            $this->flashMessage('You dont have access to this action.', 'alert');
            $this->redirect('default');
        }

        $this->setView('form');

    }


    /**
     * Generates the create/update form
     *
     * @return \VerticalForm12
     */
    protected function createComponentPageForm()
    {

        $form = new \VerticalForm12;
        $form->getElementPrototype()->class('custom');

        // adding input id
        $form->addHidden('id'); // (toto pridat)

        $form->addHidden('parent', $this->parent);

        $form->addText('title', 'Titulek');

        $form->addText('menutitle', 'Krátký název');

        $form->addText('slug', 'URL dokumentu');

        $form->addCheckbox('show_in_menu', 'Zobrazit v menu');

        $form->addCheckbox('active', 'Aktivní');

        $form->addText('redirect', 'Přesměrování na jinou adresu');

        $form->addTextarea('perex', 'Shrnutí')
            ->getControlPrototype()
            ->class('perex-text');;

        $form->addTextArea('text', 'text stránky')
            ->getControlPrototype()
            ->class('main-text');

        $form->addSubmit('submit', 'vložit');

        // callback method on success
        $form->onSuccess[] = callback($this, "processPageForm");

        // returining form
        return $form;

    }

    /**
     * Handles the output of the form component
     *
     * @param Form $form
     */
    public function processPageForm(Form $form)
    {

        // getting values
        $values = $form->form->getValues();

        if ($values['parent'] == 0) {
            $values['parent'] = NULL;
        }

        // parsing slug
        if($values['slug'] == '') {
            $values['slug'] = Strings::webalize($values['menutitle']);
        }

        // cheking if insert or update
        if (empty($values->id)) {
            // insert
            $this->pages->insert($values, $this->user->getId());

            //flashMessage
            $this->flashMessage('Text byl vytvořen.', 'success');

        } else {
            // update
            $this->pages->update($values->id, $values, $this->user->getId());

            // flash message
            $this->flashMessage('Text byl upraven.', 'success');
        }

        // redirecting
        $this->redirect('default');
    }


}
