<?php
namespace  App\AdminModule\Presenters;

use Nette\Diagnostics\Debugger;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Nette\Utils\Html;

/**
 * Strategries presenter
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class StrategiesPresenter extends AdminPresenter
{

    /** @var \App\Models\Strategies */
    private $strategies;

    /**
     * Startup method of the presenter
     */
    protected function startup()
    {
        parent::startup();

        $this->menuItem = $this->getName();

        // vytvoří instanci služby a uloží do vlastnosti presenteru
        $this->strategies = $this->context->getService('strategies');

    }

    /* ----- Renders ---------------------------------------------------------------- */

    /**
     * Renders the list of challenges
     */
    public function renderDefault()
    {

        $strategies = $this->strategies->getall();

        // debug
        Debugger::barDump($strategies, "Strategies");

        // assigning the variable to a template
        $this->template->strategies       = $strategies;
    }

    /**
     * Deletes strategy
     *
     * @param int $id
     */
    public function actionDelete($id)
    {

        if($this->user->isAllowed($this->name, $this->action)) {

            if(isset($id) && $id) {

                $this->strategies->delete($id);
            }
        }

        $this->redirect('default');
    }

    /**
     * Updates strategy
     *
     * @param int $id
     */
    public function actionUpdate($id)
    {

        if($this->user->isAllowed($this->name, $this->action)) {

            if(isset($id) && $id) {

                // gettin value for the item
                $data = $this->strategies->getSingle($id);

                // assignig data and submit caption
                $form = $this['strategiesForm'];
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
     * Render form to insert a challenge
     */
    public function actionInsert($id)
    {

        if(!$this->user->isAllowed($this->name, $this->action)) {
            $this->flashMessage('You dont have access to this action.', 'alert');
            $this->redirect('default');
        }

        $this->setView('form');

    }

    /**
     * Generates the create/update form component factory
     *
     * @return \VerticalForm
     */
    protected function createComponentStrategiesForm() {

        $form = new \VerticalForm;

        // adding input id
        $form->addHidden('id'); // (toto pridat)

        $form->addText('name', 'Název');
        $form->addText('institution', 'Instituce');
        $form->addText('valid_until', 'Platné do');
        $form->addText('url', 'Odkaz');
        $form->addTextArea('prerex', 'Anotace:');

        $form->addSubmit('submit', 'uložit');

        // callback method on success
        $form->onSuccess[] = callback($this, "processStrategiesForm");

        // returining form
        return $form;

    }

    /**
     * Handles the output of the form component
     *
     * @param Form $form
     */
    public function processStrategiesForm(Form $form) {

        // getting values
        $values = $form->form->getValues();

        // adjusting values
        $values['valid_until'] = new DateTime($values['valid_until']);

        // cheking if insert or update
        if (empty($values->id)) {
            // insert
            $this->strategies->insert($values, $this->user->getId());

            //flashMessage
            $this->flashMessage('Strategie byla vložena.', 'success');

        } else {
            // update
            $this->strategies->update($values->id, $values, $this->user->getId());

            // flash message
            $this->flashMessage('Úpravy uloženy.', 'success');
        }

        // redirecting
        $this->redirect('default');
    }

}
