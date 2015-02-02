<?php
namespace  App\AdminModule\Presenters;
use Nette\Diagnostics\Debugger;
use Nette\Application\UI\Form;
use Nette\Utils\Html;

/**
 * Event type presenter
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class EventTypesPresenter extends AdminPresenter
{

    /** @var \App\Models\Spaces */
    private $eventTypes;

    /**
     * Startup method of the presenter
     */
    protected function startup()
    {
        parent::startup();

        $this->menuItem = 'Settings';

        // vytvoří instanci služby a uloží do vlastnosti presenteru
        $this->eventTypes = $this->context->eventTypes;

    }

    /* ----- Renders ---------------------------------------------------------------- */

    /**
     * Renders the list of event types
     */
    public function renderDefault()
    {

        $spaces = $this->eventTypes->getall();

        // debug
        Debugger::barDump($spaces, "Spaces");

        // assigning the variable to a template
        $this->template->users       = $spaces;
    }

    /**
     * Deletes an event type
     *
     * @param $id
     */
    public function actionDelete($id)
    {

        if($this->user->isAllowed($this->name, $this->action)) {

            if(isset($id) && $id) {

                $this->eventTypes->delete($id);
            }
        }

        $this->redirect('default');
    }

    /**
     * Updates an event type
     *
     * @param $id
     */
    public function actionUpdate($id)
    {

        if($this->user->isAllowed($this->name, $this->action)) {

            if(isset($id) && $id) {

                // gettin value for the item
                $data = $this->eventTypes->getSingle($id);

                // assignig data and submit caption
                $form = $this['eventTypeForm'];
                $form->setDefaults($data);
                $form['submit']->caption = 'upravit';

            } else {
                $this->flashMessage('Invalid data passed.','alert');
                $this->redirect('default');
            }
        } else {
            $this->flashMessage('You dont have access to this action.', 'alert');
            $this->redirect('default');
        }

        $this->setView('form');

    }

    /**
     * Render a form to insert an event form
     */
    public function actionInsert()
    {

        if(!$this->user->isAllowed($this->name, $this->action)) {
            $this->flashMessage('You dont have access to this action.', 'alert');
            $this->redirect('default');
        }

        $this->setView('form');

    }

    /**
     * Generates the create/update form
     *
     * @return \VerticalForm
     */
    protected function createComponentEventTypeForm() {

        $form = new \VerticalForm;

        // adding input id
        $form->addHidden('id'); // (toto pridat)

        $form->addText('label', 'Název');

        $form->addSubmit('submit', 'uložit')
            ->getControlPrototype()->class('small');



        // callback method on success
        $form->onSuccess[] = callback($this, "processEventTypeForm");

        // returining form
        return $form;

    }

    /**
     * Handles the output of the form component
     *
     * @param Form $form
     */
    public function processEventTypeForm(Form $form) {

        // getting values
        $values = $form->form->getValues();

        // adjusting values

        // cheking if insert or update
        if (empty($values->id)) {
            // insert
            $this->eventTypes->insert($values, $this->user->getId());

            //flashMessage
            $this->flashMessage('Typ událotsi vytvořen.', 'success');

        } else {
            // update
            $this->eventTypes->update($values->id, $values, $this->user->getId());

            // flash message
            $this->flashMessage('Úpravy uloženy.', 'success');
        }

        // redirecting
        $this->redirect('default');
    }

}