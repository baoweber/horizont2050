<?php
use Nette\Application\UI\Form;
use Nette\Utils\Html;

class VerticalForm extends Form
{

  protected function attached($parent)
  {
    parent::attached($parent); // VŽDYCKY VOLAT NEJDŘÍVE PARENT!!!

    if ($parent instanceof Nette\Application\UI\Presenter) {
      // tento kód se zavolá bezpodmínečně jen jednou

      $this->renderer->wrappers['pair']['container'] = Html::el('div')->class('form-pair-container large-5');
      $this->renderer->wrappers['controls']['container'] = NULL;
      $this->renderer->wrappers['control']['container'] = Html::el('div')->class('controls');
      $this->renderer->wrappers['control']['description'] = Html::el('div')->class('form-description');
      $this->renderer->wrappers['label']['container'] = '';
      $this->renderer->wrappers['label']['suffix'] = '';

      //$renderer->wrappers['form']['container'] = Html::el('div')->id('form');
      //$renderer->wrappers['form']['errors'] = FALSE;
      //$renderer->wrappers['group']['container'] = NULL;
      //$renderer->wrappers['group']['label'] = 'h3';
      //$renderer->wrappers['control']['requiredsuffix'] = " \xE2\x80\xA2";
      //$renderer->wrappers['control']['.odd'] = 'odd';
      //$renderer->wrappers['control']['errors'] = TRUE;

    }
  }

}

class VerticalForm8 extends Form
{

    protected function attached($parent)
    {
        parent::attached($parent); // VŽDYCKY VOLAT NEJDŘÍVE PARENT!!!

        if ($parent instanceof Nette\Application\UI\Presenter) {
            // tento kód se zavolá bezpodmínečně jen jednou

            $this->renderer->wrappers['pair']['container'] = Html::el('div')->class('form-pair-container large-8');
            $this->renderer->wrappers['controls']['container'] = NULL;
            $this->renderer->wrappers['control']['container'] = Html::el('div')->class('controls');
            $this->renderer->wrappers['control']['description'] = Html::el('div')->class('form-description');
            $this->renderer->wrappers['label']['container'] = '';
            $this->renderer->wrappers['label']['suffix'] = '';

            //$renderer->wrappers['form']['container'] = Html::el('div')->id('form');
            //$renderer->wrappers['form']['errors'] = FALSE;
            //$renderer->wrappers['group']['container'] = NULL;
            //$renderer->wrappers['group']['label'] = 'h3';
            //$renderer->wrappers['control']['requiredsuffix'] = " \xE2\x80\xA2";
            //$renderer->wrappers['control']['.odd'] = 'odd';
            //$renderer->wrappers['control']['errors'] = TRUE;

        }
    }

}


class VerticalForm12 extends Form
{

    protected function attached($parent)
    {
        parent::attached($parent); // VŽDYCKY VOLAT NEJDŘÍVE PARENT!!!

        if ($parent instanceof Nette\Application\UI\Presenter) {
            // tento kód se zavolá bezpodmínečně jen jednou

            $this->renderer->wrappers['pair']['container'] = Html::el('div')->class('form-pair-container large-12');
            $this->renderer->wrappers['controls']['container'] = NULL;
            $this->renderer->wrappers['control']['container'] = Html::el('div')->class('controls');
            $this->renderer->wrappers['control']['description'] = Html::el('div')->class('form-description');
            $this->renderer->wrappers['label']['container'] = '';
            $this->renderer->wrappers['label']['suffix'] = '';

            //$renderer->wrappers['form']['container'] = Html::el('div')->id('form');
            //$renderer->wrappers['form']['errors'] = FALSE;
            //$renderer->wrappers['group']['container'] = NULL;
            //$renderer->wrappers['group']['label'] = 'h3';
            //$renderer->wrappers['control']['requiredsuffix'] = " \xE2\x80\xA2";
            //$renderer->wrappers['control']['.odd'] = 'odd';
            //$renderer->wrappers['control']['errors'] = TRUE;

        }
    }

}

class InlineForm extends Form
{

  protected function attached($parent)
  {
    parent::attached($parent); // VŽDYCKY VOLAT NEJDŘÍVE PARENT!!!

    if ($parent instanceof Nette\Application\UI\Presenter) {
      // tento kód se zavolá bezpodmínečně jen jednou

      $this->renderer->wrappers['pair']['container'] = NULL;
      $this->renderer->wrappers['controls']['container'] = NULL;
      $this->renderer->wrappers['control']['container'] = NULL;
      $this->renderer->wrappers['control']['description'] = NULL;
      $this->renderer->wrappers['label']['container'] = NULL;
      $this->renderer->wrappers['label']['suffix'] = '';

      //$renderer->wrappers['form']['container'] = Html::el('div')->id('form');
      //$renderer->wrappers['form']['errors'] = FALSE;
      //$renderer->wrappers['group']['container'] = NULL;
      //$renderer->wrappers['group']['label'] = 'h3';
      //$renderer->wrappers['control']['requiredsuffix'] = " \xE2\x80\xA2";
      //$renderer->wrappers['control']['.odd'] = 'odd';
      //$renderer->wrappers['control']['errors'] = TRUE;

    }
  }

}
