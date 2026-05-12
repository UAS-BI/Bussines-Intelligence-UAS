<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactHousing extends Model
{
    protected $table = 'fact_housing';

    protected $primaryKey = 'fact_id';

    public $timestamps = false;
}