<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Transformers\TicketsTransformer;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class TicketsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : JsonResponse | array
    {
      $this->authorize('view', Ticket::class);
      $allowed_columns =
          [
              'id',
              'asset_name',
          ];
        $tickets = Ticket::select('tickets.*'); //
        if ($request->filled('search')) {
            $tickets = $tickets->TextSearch($request->input('search'));
        }

        if ($request->filled('name')) {
            $tickets->where('asset_name', '=', $request->input('name'));
        }


        // Make sure the offset and limit are actually integers and do not exceed system limits
        $offset = ($request->input('offset') > $tickets->count()) ? $tickets->count() : app('api_offset_value');
        $limit = app('api_limit_value');

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $sort_override =  $request->input('sort');
        $column_sort = in_array($sort_override, $allowed_columns) ? $sort_override : 'created_at';

        switch ($sort_override) {
            case 'created_by':
                $tickets = $tickets->OrderByCreatedBy($order);
                break;
            default:
                $tickets = $tickets->orderBy($column_sort, $order);
                break;
        }

        $total = $tickets->count();

        $tickets = $tickets->skip($offset)->take($limit)->get();
       // Log::error('Значение переменной:', ['variable' =>$components]);
        return (new TicketsTransformer)->transformTickets($tickets, $total);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) : JsonResponse
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id) : array
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) : JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) : JsonResponse
    {
        //
    }
}
