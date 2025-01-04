<?php

namespace App\Models;

use App\Models\Traits\Searchable;
use App\Presenters\Presentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Watson\Validating\ValidatingTrait;

class ComponentBook extends SnipeModel
{
    use HasFactory;

    protected $presenter = \App\Presenters\ComponentBookPresenter::class;
    use Presentable;
    protected $table = 'component_books';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'partnum',
    ];

    public $rules = [
       

    ];
    use Searchable;

    /**
     * The attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableAttributes = ['name',  'partnum'];
}
