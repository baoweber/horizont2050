<?php
namespace  App\AdminModule\Presenters;

use Nette\Application\UI, Nette\Security as NS, Nette\Application\UI\Form;;


/**
 * Sign in/out presenters.
 *
 * @author     Martin Kryl <martin.kryl@czp.cuni.cz>
 * @package    Horizont2050
 * @copyright  COŽP UK 2014-2015
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 */
class SignPresenter extends \BasePresenter
{


    /**
     * Sign in form component factory.
     *
     * @return \VerticalForm
     */
    protected function createComponentSignInForm()
    {
        // creating form
        $form = new \VerticalForm;

        $form->getElementPrototype()->class('form-horizontal');

        $form->addText('username', 'email:')
            ->setRequired('Please provide a username.');

        $form->addPassword('password', 'heslo:')
            ->setRequired('Please provide a password.');

        //$form->addCheckbox('remember', 'zapamatovat přihášení');

        $form->addSubmit('send', 'přihlásit')
            ->setAttribute('class', 'button');

        $form->onSuccess[] = callback($this, 'signInFormSubmitted');
        return $form;
    }

    /**
     * Process sign in form component
     *
     * @param Form $form
     */
    public function signInFormSubmitted(Form $form)
    {
        try {
            $values = $form->getValues();
            /*
            if ($values->remember) {
              $this->getUser()->setExpiration('+ 14 days', FALSE);
            } else {
              $this->getUser()->setExpiration('+ 1 day', TRUE);
            }
            */
            $this->getUser()->setExpiration('+ 1 day', TRUE);

            $this->getUser()->login($values->username, $values->password);
            $this->redirect('Homepage:');

        } catch (NS\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

    /**
     * Logout Action
     */
    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('You have been signed out.');
        $this->redirect('in');
    }

}
