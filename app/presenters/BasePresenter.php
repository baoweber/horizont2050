<?php

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    protected function createTemplate($class = NULL)
    {
        $template = parent::createTemplate($class);

        $helpers = new \ThumberHelper($this->context);
        $template->addFilter('thumber', $helpers->thumber);

        return $template;
    }

}
