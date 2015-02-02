<?php
namespace  App\AdminModule\Presenters;
/**
 * Homepage presenter.
 *
 * @author     Martin Kryl, COŽP UK
 * @package    Horizont2050
 */
class HomepagePresenter extends AdminPresenter
{

    /**
     * Startup method of the presenter
     */
    protected function startup()
    {
        parent::startup();

        $this->menuItem = $this->getName();

    }

    /**
     * Renders administration homepage
     */
	public function renderDefault()
	{

    }
}