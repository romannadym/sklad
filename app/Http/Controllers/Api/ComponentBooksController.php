<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Transformers\ComponentBooksTransformer;
use App\Models\ComponentBook;
use Illuminate\Http\Request;
use App\Http\Requests\ImageUploadRequest;
use App\Events\CheckoutableCheckedIn;
use App\Models\Asset;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComponentBooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     *
     */
    public function index(Request $request) : JsonResponse | array
    {
        $this->authorize('view', ComponentBook::class);

        // This array is what determines which fields should be allowed to be sorted on ON the table itself, no relations
        // Relations will be handled in query scopes a little further down.
        $allowed_columns = 
            [
                'id',
                'name',
                'partnum',
            ];

        $components = ComponentBook::select('component_books.*'); // 

        if ($request->filled('search')) {
            $components = $components->TextSearch($request->input('search'));
        }

        if ($request->filled('name')) {
            $components->where('name', '=', $request->input('name'));
        }


        // Make sure the offset and limit are actually integers and do not exceed system limits
        $offset = ($request->input('offset') > $components->count()) ? $components->count() : app('api_offset_value');
        $limit = app('api_limit_value');

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $sort_override =  $request->input('sort');
        $column_sort = in_array($sort_override, $allowed_columns) ? $sort_override : 'created_at';

        switch ($sort_override) {
            case 'created_by':
                $components = $components->OrderByCreatedBy($order);
                break;
            default:
                $components = $components->orderBy($column_sort, $order);
                break;
        }

        $total = $components->count();
        
        $components = $components->skip($offset)->take($limit)->get();
       // Log::error('Значение переменной:', ['variable' =>$components]);
        return (new ComponentBooksTransformer)->transformComponentBooks($components, $total);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  \App\Http\Requests\ImageUploadRequest  $request
     */
    public function store(Request $request) : JsonResponse
    {
        $this->authorize('create', ComponentBook::class);
        $component = new ComponentBook;
        $component->fill($request->all());

        if ($component->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', $component, trans('admin/components/message.create.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, $component->getErrors()));
    }

    /**
     * Display the specified resource.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param  int  $id
     */
    public function show($id) : array
    {
        $this->authorize('view', ComponentBook::class);
        $component = ComponentBook::findOrFail($id);

        if ($component) {
            return (new ComponentBooksTransformer)->transformComponentBook($component);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param   \App\Http\Requests\ImageUploadRequest  $request
     * @param  int  $id
     */
    public function update(Request $request, $id) : JsonResponse
    {
        $this->authorize('update', ComponentBook::class);
        $component = ComponentBook::findOrFail($id);
        $component->fill($request->all());
        

        if ($component->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', $component, trans('admin/components/message.update.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, $component->getErrors()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  int  $id
     */
    public function destroy($id) : JsonResponse
    {
        $this->authorize('delete', ComponentBook::class);
        $component = ComponentBook::findOrFail($id);
        $this->authorize('delete', $component);
        $component->delete();

        return response()->json(Helper::formatStandardApiResponse('success', null, trans('admin/components/message.delete.success')));
    }

    
    public function search(Request $request)
    {
        $this->authorize('view', ComponentBook::class);
        $query = $request->input('query'); // Получение параметра запроса
        $filter = $request->input('filter'); // Получение параметра фильтрации
        $components = [];
        if($filter == 'all')
        {
            $components = ComponentBook::withTrashed()->where('name', 'LIKE', "%{$query}%")
            ->orWhere('partnum', 'LIKE', "%{$query}%")
           // ->limit(10)
            ->get(['id', 'name','partnum']); // Поиск по имени
        }
        if($filter == 'name')
        {
            $components = ComponentBook::withTrashed()->where('name', 'LIKE', "%{$query}%")
           // ->limit(10)
           ->get(['id', 'name','partnum']); // Поиск по имени
        }
        if($filter == 'partnum')
        {
            $components = ComponentBook::withTrashed()->where('partnum', 'LIKE', "%{$query}%")
           // ->limit(10)
           ->get(['id', 'name','partnum']); // Поиск по имени
        }
        

        return response()->json($components); // Возврат данных в формате JSON
    }

}
