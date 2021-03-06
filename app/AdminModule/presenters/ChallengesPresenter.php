<?php
namespace  App\AdminModule\Presenters;
use Nette\Diagnostics\Debugger;
use Nette\Application\UI\Form;
use Nette\Utils\Html;

/**
 * User administration presenter.
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class ChallengesPresenter extends AdminPresenter
{

    /** @var \App\Models\Challenges */
    private $challenges;

    /**
     * Startup method of the presenter
     */
    protected function startup()
    {
        parent::startup();

        $this->menuItem = $this->getName();

        // vytvoří instanci služby a uloží do vlastnosti presenteru
        $this->challenges = $this->context->getService('challenges');

    }

    /* ----- Renders ---------------------------------------------------------------- */

    /**
     * Renders the list of challenges
     */
    public function renderDefault()
    {

        $challenges = $this->challenges->getall();

        // debug
        Debugger::barDump($challenges, "Strategies");

        // assigning the variable to a template
        $this->template->challenges       = $challenges;
    }

    /**
     * Deletes challenge
     *
     * @param int $id
     */
    public function actionDelete($id)
    {

        if($this->user->isAllowed($this->name, $this->action)) {

            if(isset($id) && $id) {

                $this->challenges->delete($id);
            }
        }

        $this->redirect('default');
    }

    /**
     * Updates challenge
     *
     * @param int $id
     */
    public function actionUpdate($id)
    {

        if($this->user->isAllowed($this->name, $this->action)) {

            if(isset($id) && $id) {

                // gettin value for the item
                $data = $this->challenges->getSingle($id);

                // assignig data and submit caption
                $form = $this['challengeForm'];
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
    protected function createComponentChallengeForm() {

        $form = new \VerticalForm;

        // adding input id
        $form->addHidden('id'); // (toto pridat)

        $form->addText('name', 'Název');
        $form->addText('desc', 'Popis');

        $form->addSubmit('submit', 'uložit');



        // callback method on success
        $form->onSuccess[] = callback($this, "processChallengeForm");

        // returining form
        return $form;
    }

    /**
     * Handles the output of the form component
     *
     * @param Form $form
     */
    public function processChallengeForm(Form $form) {

        // getting values
        $values = $form->form->getValues();

        // adjusting values

        // cheking if insert or update
        if (empty($values->id)) {
            // insert
            $this->challenges->insert($values, $this->user->getId());

            //flashMessage
            $this->flashMessage('Strategie byla vložena.', 'success');

        } else {
            // update
            $this->challenges->update($values->id, $values, $this->user->getId());

            // flash message
            $this->flashMessage('Úpravy uloženy.', 'success');
        }

        // redirecting
        $this->redirect('default');
    }

}