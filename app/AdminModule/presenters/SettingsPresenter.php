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
class SettingsPresenter extends AdminPresenter
{

    /** @var \App\Models\Settings */
    private $settings;

    /**
     * Startup method of the presenter
     */
    protected function startup()
    {
        parent::startup();

        // setting selected menuitem
        $this->menuItem = $this->getName();

        // vytvoří instanci služby a uloží do vlastnosti presenteru
        $this->settings     = $this->context->getService('settings');

    }

    /* ----- Renders ---------------------------------------------------------------- */

    /**
     * Renders the list of settings
     */
    public function renderDefault()
    {
        // getting data
        $settings = $this->settings->getAll();

        // debug
        Debugger::barDump($settings, "Settings");

        $form = $this['settingsForm'];
        $form->setDefaults($settings);

        // assigning the variable to a template
        $this->template->settings  = $settings;
    }

    /* ----- Actions ---------------------------------------------------------------- */


    /* ----- Components ------------------------------------------------------------- */

    /**
     * Generates the update form
     *
     * @return \VerticalForm
     */
    protected function createComponentSettingsForm() {

        $form = new \VerticalForm;

        $form->addText('EUR', 'Počet položek na stránku.')
            ->getControlPrototype()
                ->class('smaller-width');

        $form->addSubmit('submit', 'uložit nastavení')
            ->getControlPrototype()
                ->class("small secondary");

        // callback method on success
        $form->onSuccess[] = callback($this, "processSettingsForm");

        // returining form
        return $form;

    }

    /**
     * Handles the output of the form component
     *
     * @param Form $form
     */
    public function processSettingsForm(Form $form) {

        // getting values
        $values = $form->form->getValues();

        // adjusting values

        foreach($values as $key =>  $value) {
            $this->settings->saveValue($key, $value);
        }

        // flash message
        $this->flashMessage('Setting values were updated.', 'success');

        // redirecting
        $this->redirect('default');
    }

}
