<?php
namespace  App\AdminModule\Presenters;

use Nette\Diagnostics\Debugger;
use Nette\Application\UI\Form;
use Nette\Utils\Html;

/**
 * Spaces presenter
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class SpacesPresenter extends AdminPresenter
{

    /** @var \Spaces */
    private $spaces;

    /**
     * Startup method of the presenter
     */
    protected function startup()
    {
        parent::startup();

        $this->menuItem = 'Settings';

        // vytvoří instanci služby a uloží do vlastnosti presenteru
        $this->spaces = $this->context->spaces;

    }

    /* ----- Renders ---------------------------------------------------------------- */

    /**
     * Renders the list of spaces
     */
    public function renderDefault()
    {

        $spaces = $this->spaces->getall();

        // debug
        Debugger::barDump($spaces, "Spaces");

        // assigning the variable to a template
        $this->template->users       = $spaces;
    }

    /**
     * Deletes space
     *
     * @param int $id
     */
    public function actionDelete($id)
    {

        if($this->user->isAllowed($this->name, $this->action)) {

            if(isset($id) && $id) {

                $this->spaces->delete($id);
            }
        }

        $this->redirect('default');
    }

    /**
     * Renders update form space
     *
     * @param int $id
     */
    public function actionUpdate($id)
    {

        if($this->user->isAllowed($this->name, $this->action)) {

            if(isset($id) && $id) {

                // gettin value for the item
                $data = $this->spaces->getSingle($id);

                // assignig data and submit caption
                $form = $this['spaceForm'];
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
     * Renders insert form space
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
    protected function createComponentSpaceForm() {

        $form = new \VerticalForm;

        // adding input id
        $form->addHidden('id'); // (toto pridat)

        $form->addText('label', 'Název');

        $form->addSubmit('submit', 'uložit')
            ->getControlPrototype()->class('small');



        // callback method on success
        $form->onSuccess[] = callback($this, "processSpacesForm");

        // returining form
        return $form;

    }

    /**
     * Handles the output of the form component
     *
     * @param Form $form
     */
    public function processSpacesForm(Form $form) {

        // getting values
        $values = $form->form->getValues();

        // adjusting values

        // cheking if insert or update
        if (empty($values->id)) {
            // insert
            $this->spaces->insert($values, $this->user->getId());

            //flashMessage
            $this->flashMessage('Oblast vytvořena.', 'success');

        } else {
            // update
            $this->spaces->update($values->id, $values, $this->user->getId());

            // flash message
            $this->flashMessage('Úpravy uloženy.', 'success');
        }

        // redirecting
        $this->redirect('default');
    }

}
