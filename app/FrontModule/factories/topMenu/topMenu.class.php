<?php
namespace App\FrontModule\Presenters;

use Tracy\Debugger;

class TopMenu extends \Nette\Application\UI\Control
{

    /** @var \Pages */
    protected $model;

    protected $active = '';

    public function __construct($model) {
        parent::__construct();
        $this->model = $model;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function render()
    {

        // getting top menu items
        $params = array(
            'orderby' => '`ord` DESC',
            'where' => array(
                'active' => 1,
                'show_in_menu' => 1,
                'parent%iN' => NULL
            )
        );

        $menuItems = $this->model->getAll($params);

        // determining the active item
        foreach($menuItems as $item) {
            if($item->slug == $this->active) {
                $item->displayAsActive = true;
            } else {
                $item->displayAsActive = false;
            }
        }

        Debugger::barDump($menuItems, 'TOP MENU');

        // assigning template file
        $this->template->setFile(__DIR__ . '/topMenu.latte');

        // assigning links
        $this->template->menuItems = $menuItems;

        // rendering component
        $this->template->render();
    }
}