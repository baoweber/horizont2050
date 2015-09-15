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
class NewsPresenter extends AdminPresenter
{

    /** @var \App\Models\News */
    private $news;

    /**
     * Startup method of the presenter
     */
    protected function startup()
    {
        parent::startup();

        $this->menuItem = $this->getName();

        // vytvoří instanci služby a uloží do vlastnosti presenteru
        $this->news = $this->context->getService('news');
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
            'orderby' => '`created` DESC'
        );

        $news = $this->news->getAll($params);

        // assigning the variable to a template
        $this->template->news          = $news;
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

                $this->news->delete($id);
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
                $data = $this->news->getSingle($id);

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

        $form->addCheckbox('active', 'Aktivní');
        $form->addCheckbox('linked', 'Proklik na delší text');

        $form->addText('redirect', 'Přesměrování na jinou adresu');

        $form->addTextarea('perex', 'Shrnutí')
            ->getControlPrototype()
            ->class('perex-text');

        $form->addTextArea('text', 'text stránky')
            ->getControlPrototype()
            ->class('main-text');

        $form->addText('date', 'Datum');

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
            $this->news->insert($values, $this->user->getId());

            //flashMessage
            $this->flashMessage('Text byl vytvořen.', 'success');

        } else {
            // update
            $this->news->update($values->id, $values, $this->user->getId());

            // flash message
            $this->flashMessage('Text byl upraven.', 'success');
        }

        // redirecting
        $this->redirect('default');
    }


}
