<?php

namespace App\Http\Transformers;

use App\Helpers\Helper;
use App\Models\ComponentBook;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
class ComponentBooksTransformer
{
    public function transformComponentBooks(Collection $components, $total)
    {
        $array = [];
        foreach ($components as $component) {
            $array[] = self::transformComponentBook($component);
        }

        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformComponentBook(ComponentBook $component)
    {

        $array = [
            'id' => (int) $component->id,
            'name' => e($component->name),
	          'partnum' => ($component->partnum) ? e($component->partnum) : null,
            'category' => ($component->category) ? [
                'id' => (int) $component->category->id,
                'name' => e($component->category->name),
            ] : null,
            'created_by' => ($component->adminuser) ? [
                'id' => (int) $component->adminuser->id,
                'name'=> e($component->adminuser->present()->fullName()),
            ] : null,
            'created_at' => Helper::getFormattedDateObject($component->created_at, 'datetime'),
            'updated_at' => Helper::getFormattedDateObject($component->updated_at, 'datetime'),
          'actions' => ['sadasdasd'],
        ];

        $permissions_array['available_actions'] = [
            'update' => Gate::allows('update', ComponentBook::class),
            'delete' => Gate::allows('delete', ComponentBook::class),
        ];
        $array += $permissions_array;

        return $array;
    }


}
