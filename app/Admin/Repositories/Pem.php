<?php

namespace App\Admin\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;
use App\Models\Pem as PemModel;

class Pem extends EloquentRepository
{
    protected $eloquentClass = PemModel::class;

    public function getGridColumns(){
        return ['id', 'group', 'env', 'discription', 'email', 'path', 'date', 'created_at', 'updated_at'];
    }
}