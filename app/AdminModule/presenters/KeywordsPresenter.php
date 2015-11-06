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
class KeywordsPresenter extends AdminPresenter
{

    /** @var \App\Models\Keywords */
    private $keywords;

    /**
     * Startup method of the presenter
     */
    protected function startup()
    {
        parent::startup();

        // setting selected menuitem
        $this->menuItem = $this->getName();

        // vytvoří instanci služby a uloží do vlastnosti presenteru
        $this->keywords     = $this->context->getService('keywords');

    }

    /* ----- Renders ---------------------------------------------------------------- */

    /**
     * Renders the list of settings
     */
    public function renderDefault()
    {
        // getting data
        $keywords = $this->keywords->getAll();

        // debug
        Debugger::barDump($keywords, "Keywords");

        // $form = $this['keywordsForm'];
        // $form->setDefaults($keywords);

        // assigning the variable to a template
        $this->template->keywords  = $keywords;
    }

    /**
     * Deletes an event type
     *
     * @param $id
     */
    public function actionDelete($id)
    {

        //if($this->user->isAllowed($this->name, $this->action)) {

            if(isset($id) && $id) {

                $this->keywords->delete($id);
            }
        //}

        $this->redirect('default');
    }

    /**
     * Renders update form
     *
     * @param int $id
     */
    public function actionUpdate($id)
    {

        //if ($this->user->isAllowed($this->name, $this->action)) {

            if (isset($id) && $id) {

                // gettin value for the item
                $data = $this->keywords->getSingle($id);


                // assignig data and submit caption
                $form = $this['editForm'];
                $form->setDefaults($data);
                $form['submit']->caption = 'upravit';

            } else {
                $this->flashMessage('Invalid data passed.', 'alert');
                $this->redirect('default');
            }
        //} else {
        //    $this->flashMessage('You dont have access to this action.', 'alert');
        //    $this->redirect('default');
        //}

        $this->setView('form');

    }

    /* ----- Actions ---------------------------------------------------------------- */


    /* ----- Components ------------------------------------------------------------- */

    /**
     * Generates the update form
     *
     * @return \VerticalForm
     */
    protected function createComponentEditForm() {

        $form = new \VerticalForm;

        $form->addText('label', 'Klíčové slovo');

        $form->addHidden('id');

        $form->addSubmit('submit', 'uložit úpravu')
            ->getControlPrototype()
                ->class("small secondary");

        // callback method on success
        $form->onSuccess[] = callback($this, "processEditForm");

        // returining form
        return $form;

    }


    /**
     * Handles the output of the form component
     *
     * @param Form $form
     */
    public function processEditForm(Form $form) {

        // getting values
        $values = $form->form->getValues();

        $this->keywords->update($values->id, $values, $this->user->id);

        // flash message
        $this->flashMessage('Klíčové slovo upraveno.', 'success');

        // redirecting
        $this->redirect('default');
    }
}
