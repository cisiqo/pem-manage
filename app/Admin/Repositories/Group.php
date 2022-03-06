<?php

namespace App\Admin\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;
use App\Models\Group as GroupModel;

class Group extends EloquentRepository
{
    protected $eloquentClass = GroupModel::class;

    public function getGridColumns(){
        return ['id', 'name', 'created_at', 'updated_at'];
    }
}