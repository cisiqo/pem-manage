<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Layout\Content;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function store()
    {
        return $this->form()->store();
    }

    public function edit($id, Content $content)
    {
        return $content->body($this->form()->edit($id));
    }

    public function update($id)
    {
        return $this->form()->update($id);
    }

    public function destroy($id) {
        return $this->form()->destroy($id);
    }
}
