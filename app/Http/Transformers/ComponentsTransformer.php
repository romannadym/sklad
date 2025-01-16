<?php

namespace App\Http\Transformers;

use App\Helpers\Helper;
use App\Models\Component;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
class ComponentsTransformer
{
    public function transformComponents(Collection $components, $total)
    {
        $array = [];
        foreach ($components as $component) {
            $array[] = self::transformComponent($component);
        }

        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformComponent(Component $component)
    {
        if(is_array(json_decode($component->image)) && !empty($component->image)){
            $images = [];
            foreach( json_decode($component->image) as $image) {
                $images[] = Storage::disk('public')->url('components/'.e($image));
            }
        }
        $array = [
            'id' => (int) $component->id,
            'name' => e($component->name),
            'image' =>   (isset($images) && is_array($images) && !empty($images)) ? "<a href='#' class='images' data-images='".json_encode($images)."'>Фото</a>" : null,
            'serial' => ($component->serial) ? e($component->serial) : null,
	    'partnum' => ($component->partnum) ? e($component->partnum) : null,
	    'status' => ($component->status) ? e($component->status) : null,
	    'customer' => ($component->customer) ? e($component->customer) : null,
	    'location' => ($component->location) ? [
                'id' => (int) $component->location->id,
                'name' => e($component->location->name),
            ] : null,
            'qty' => ($component->qty != '') ? (int) $component->qty : null,
            'min_amt' => ($component->min_amt != '') ? (int) $component->min_amt : null,
            'category' => ($component->category) ? [
                'id' => (int) $component->category->id,
                'name' => e($component->category->name),
            ] : null,
            'supplier' => ($component->supplier) ? ['id' => $component->supplier->id, 'name'=> e($component->supplier->name)] : null,
            'order_number'  => e($component->order_number),
            'purchase_date' =>  Helper::getFormattedDateObject($component->purchase_date, 'date'),
            'purchase_cost' => Helper::formatCurrencyOutput($component->purchase_cost),
            'remaining'  => (int) $component->numRemaining(),
            'company'   => ($component->company) ? [
                'id' => (int) $component->company->id,
                'name' => e($component->company->name),
            ] : null,
            'notes' => ($component->notes) ? Helper::parseEscapedMarkedownInline($component->notes) : null,
            'created_by' => ($component->adminuser) ? [
                'id' => (int) $component->adminuser->id,
                'name'=> e($component->adminuser->present()->fullName()),
            ] : null,
            'created_at' => Helper::getFormattedDateObject($component->created_at, 'datetime'),
            'updated_at' => Helper::getFormattedDateObject($component->updated_at, 'datetime'),
            'user_can_checkout' =>  ($component->numRemaining() > 0) ? 1 : 0,
        ];

        $permissions_array['available_actions'] = [
            'checkout' => Gate::allows('checkout', Component::class),
            'checkin' => Gate::allows('checkin', Component::class),
            'update' => Gate::allows('update', Component::class),
            'delete' => Gate::allows('delete', Component::class),
        ];
        $array += $permissions_array;

        return $array;
    }

    public function transformCheckedoutComponents(Collection $components_assets, $total)
    {
        $array = [];
        foreach ($components_assets as $asset) {
           $user = User::find($asset->pivot->assigned_to_user_id);
           $component  = Component::find($asset->pivot->component_id);
            $array[] = [
                'assigned_pivot_id' => $asset->pivot->id,
                'id' => (int) $asset->id,
                'name' => /* e($asset->model->present()->name).' '.*/ e($asset->present()->name),
                'qty' => $asset->pivot->assigned_qty,
                'note' => $asset->pivot->note,
                'ticketnum' => $asset->pivot->ticketnum,
                'assigned_to_username' => "<a href='/users/{$user->id}'>". e($user->last_name.' '.e($user->first_name))."</a>" , // Получаем имя пользователя,
                'assigned_to_username2' => e($user->last_name).' '.e($user->first_name), // Получаем имя пользователя,
                'type' => 'asset',
                'created_at' => Helper::getFormattedDateObject($asset->pivot->created_at, 'datetime'),
                'available_actions' => ['checkin' => true],
                'component_name' => "<a href='/components/{$component->id}'>". e($component->name)."</a>",
                'component_name2' => e($component->name),
            ];
        }

        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }
}
