<?php

namespace App\Admin\Controllers;

use App\Admin\Controllers\AdminController;
use Dcat\Admin\Grid;
use Dcat\Admin\Form;
use Dcat\Admin\Layout\Content;
use App\Admin\Repositories\Group;

class GroupController extends AdminController
{
    public function index(Content $content)
    {
        return $content
            ->header('项目组')
            ->body($this->grid());
    }

    protected function grid()
    {
        return new Grid(new Group(), function (Grid $grid) {
            $grid->number();
            $grid->column('name', "项目组");
            $grid->column('created_at');
            $grid->column('updated_at');
            $grid->disableViewButton();
        });
    }

    public function create(Content $content)
    {
        return $content
            ->body($this->form());
    }

    protected function form()
    {
        $form = new Form(new Group());
        $form->text('name', '项目组')->required();
        $form->disableViewButton();
        return $form;
    }

}
