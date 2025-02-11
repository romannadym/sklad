<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Presenters\Presentable;
class ComponentCheckout extends SnipeModel
{
    use HasFactory;
    protected $presenter = \App\Presenters\ComponentCheckoutPresenter::class;
    use Presentable;
    protected $table = 'component_checkouts';
    protected $fillable = [
        'asset_id',
        'component_id',
        'assigned_qty',
        'note',
        'ticketnum',
        'assigned_to_user_id',
    ];
}
 