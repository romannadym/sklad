<?php

namespace App\Models;

use App\Models\Traits\Searchable;
use App\Presenters\Presentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Traits\UniqueUndeletedTrait;
use Watson\Validating\ValidatingTrait;

class ComponentBook extends SnipeModel
{
    use HasFactory;
    use UniqueUndeletedTrait;
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
        'category_id'
    ];

    public $rules = [
        'partnum'         => ['nullable', 'min:1', 'max:255', 'unique_undeleted:component_books,partnum', 'not_array'],

    ];
    use Searchable;

    /**
     * The attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableAttributes = ['name',  'partnum'];

    public static function rules()
    {
        return [
            // Определите правила валидации
            // Пример:
          //  'category_id' => 'required|exists:categories,id',
            // другие поля...
        ];
    }
    // Добавьте отношение к категории
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
