<?php

namespace App\Models;

use App\Models\Traits\Searchable;
use App\Presenters\Presentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Traits\UniqueUndeletedTrait;
use Watson\Validating\ValidatingTrait;

class Ticket extends SnipeModel
{
  use HasFactory;
  use UniqueUndeletedTrait;
  protected $presenter = \App\Presenters\TicketsPresenter::class;
  use Presentable;
  protected $table = 'tickets';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'asset_name',
  ];

  public $rules = [
  //    'partnum'         => ['nullable', 'min:1', 'max:255', 'unique_undeleted:component_books,partnum', 'not_array'],

  ];
  use Searchable;

  /**
   * The attributes that should be included when searching the model.
   *
   * @var array
   */
  protected $searchableAttributes = ['asset_name'];

}
