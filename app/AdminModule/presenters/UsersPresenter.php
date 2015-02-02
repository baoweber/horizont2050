<?php
namespace  App\AdminModule\Presenters;

use Nette\Diagnostics\Debugger;
use Nette\Application\UI\Form;
use Nette\Utils\Html;

/**
 * Users administration presenter
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class UsersPresenter extends AdminPresenter
{

    /** @var \App\Models\Users */
    private $users;

    /** @persistent  */
    public $role = 'user';

    /**
     * Startup method of the presenter
     */
    protected function startup()
    {
        parent::startup();

        // setting selected menuitem
        $this->menuItem = 'Users';

        // assign services
        $this->users = $this->context->users;

        if(!$this->user->isAllowed($this->name)) {
            die("Insufficient priviledges.");
        }

    }

    /* ----- Renders ---------------------------------------------------------------- */

    /**
     * Renders the list of users
     */
    public function renderDefault()
    {
        $params = array(
            'orderby' => 'surname'
        );

        // getting data
        $users = $this->users->getAll($params);

        // debug
        Debugger::barDump($users, "All Users");

        // assigning the variable to a template
        $this->template->users  = $users;
    }

    /**
     * Render user edit
     *
     * @param int $id
     */
    public function renderEdit($id)
    {
        // getting stamp values
        $data = $this->users->getSingle($id);

        // debug
        Debugger::barDump($data, "DATA");

        // assigning data for the form
        $this['userEditForm']->setDefaults($data);
    }

    /**
     * Render user insert
     */
    public function renderInsert($id)
    {

    }


    /* ----- Actions ---------------------------------------------------------------- */

    /**
     * Activate ot deactivate the user
     *
     * @param int $id User ID
     * @param bool $value Activate if TRUE, deactivate if FALSE
     */
    public function actionActivate($id, $value)
    {
        // getting id to delete
        $id    = intval($id);
        $value = intval($value);

        if ($value) {
            $this->users->activate($id);
            // setting flash message
            $this->flashMessage('Uživatel/ka aktivován/a.', 'alert-success');
        } else {
            $this->users->deactivate($id);
            // setting flash message
            $this->flashMessage('Uživatel/ka deaktivován/a.', 'alert-success');
        }

        $this->redirect('default');
    }

    /* ----- Form components --------------------------------------------------------- */
    /**
     * Generates the update form component factory
     *
     * @return \VerticalForm
     */
    protected function createComponentUserEditForm()
    {

        // creating form
        $form = new \VerticalForm;

        $form->addHidden('id');

        $form->addSelect('role', 'role', $this->users->getRoles())
            ->setRequired('Fill the user role field please.')
            ->setPrompt('Vyberte uživatelskou roli.');

        $form->addText('email', 'Email')
            ->setRequired('Fill the email field please.')
            ->addRule(Form::EMAIL, 'The format of the email is not valid..');

        $form->addText('name', 'Jméno')
            ->setRequired('Fill the name field please.');

        $form->addText('surname', 'Příjmení')
            ->setRequired('Fill the surname field please.');

        $form->addPassword('password', 'Heslo');

        $form->addPassword('password2', 'Zopakujte heslo')
            ->addRule($form::EQUAL, 'Passowrd missmatch.', $form['password']);

        $form->addSubmit('save', 'uložit');

        // callback method on success
        $form->onSuccess[] = callback($this, "processUserEditForm");

        // returining form
        return $form;
    }

    /**
     * Handles the output of the update form component
     *
     * @param Form $form
     */
    public function processUserEditForm(Form $form)
    {
        $data = $form->getValues();

        $this->users->edit($data, $this->user->id);

        $this->flashMessage('User has been updated.', 'alert-success');
        $this->redirect('default');
    }

    /**
     * Generates the insert form component factory
     *
     * @return \VerticalForm
     */
    protected function createComponentUserInsertForm()
    {
        // creating form
        $form = new \VerticalForm;

        $form->addSelect('role', 'role', $this->users->getRoles())
            ->setRequired('Fill the user role field please.')
            ->setPrompt('Vyberte uživatelskou roli.');

        $form->addText('email', 'Email')
            ->setRequired('Fill the email field please.')
            ->addRule(Form::EMAIL, 'Formát emailu je neplatný.');

        $form->addText('name', 'jméno')
            ->setRequired('Fill the name field please.');

        $form->addText('surname', 'Příjmení')
            ->setRequired('Fill the surname field please.');

        $form->addPassword('password', 'Heslo')
            ->setRequired('Fill the password field please.')
            ->addRule($form::MIN_LENGTH, 'Minimální délka hesla je %d znaků', 6);

        $form->addPassword('password2', 'Zopakujte heslo')
            ->setRequired('Repeat the password please.')
            ->addRule($form::MIN_LENGTH, 'The passowrd has to be at least %d letters long.', 6)
            ->addRule($form::EQUAL, 'Passowrd missmatch.', $form['password']);

        $form->addSubmit('save', 'vytvořit');

        // callback method on success
        $form->onSuccess[] = callback($this, "processUserInsertForm");

        // returining form
        return $form;
    }

    /**
     * Handles the output of the insert form component
     *
     * @param Form $form
     */
    public function processUserInsertForm(Form $form)
    {
        $data = $form->getValues();

        // check insterted role
        if(!in_array($data->role, $this->users->getAllowedRoles() )) {
            die('Neplatná role.');  // TODO předělat do nějaké vyjímky
        }

        $this->users->insert($data, $data->role);
        $this->flashMessage('Uživatel/ka vložen/a.', 'alert-success');
        $this->redirect('default');
    }

}
