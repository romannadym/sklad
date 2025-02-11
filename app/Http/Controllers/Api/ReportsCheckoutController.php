<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Transformers\ActionlogsTransformer;
use App\Http\Transformers\ComponentsTransformer;
use App\Models\Component;
use App\Models\ComponentCheckout;
use Illuminate\Database\Query\Builder;
use App\Models\Asset;
use App\Models\Actionlog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportsCheckoutController extends Controller
{
    /**
     * Returns Activity Report JSON.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     */
    public function index(Request $request) : JsonResponse | array
    {
        $this->authorize('view', \App\Models\Asset::class);
        $offset = request('offset', 0);
        $limit = $request->input('limit', 50);
        $checkout = ComponentCheckout::join('assets', 'assets.id', '=', 'component_checkouts.asset_id')
        ->join('components', 'components.id', '=', 'component_checkouts.component_id')
        ->join('users', 'users.id', '=', 'component_checkouts.assigned_to_user_id')->select('component_checkouts.*');
        if ($request->filled('search') || $request->filled('filter')) {
           
            $checkout->where(function ($query) use ($request) {
                $search_str = '%' . $request->input('search') . '%';
               $query->where('assets.asset_tag', 'like', $search_str)
            ->orWhere('components.name', 'like', $search_str)
            ->orWhereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", [$search_str])
            ->orWhere('ticketnum', 'like', $search_str)
            ->orWhere('note', 'like', $search_str);
            })
            ->where(function ($query) use ($request) {
                if ($request->filled('filter')) {
                    if(!empty($request->input('filter')['assetName'])){
                        $query->where('assets.asset_tag', $request->input('filter')['assetName'] );
                    }
                    if(!empty($request->input('filter')['ticketnum'])){
                        $query->where('ticketnum', 'like', '%'.$request->input('filter')['ticketnum'].'%' );
                    }
                    if(!empty($request->input('filter')['component_name'])){
                        $query->where('components.name', $request->input('filter')['component_name'] );
                    }
                    if(!empty($request->input('filter')['dateStart']) && !empty($request->input('filter')['dateEnd'])){
                        
                        $query->whereBetween('component_checkouts.created_at', [date('Y-m-d H:i:s',strtotime($request->input('filter')['dateStart'])), date('Y-m-d 23:59:59',strtotime($request->input('filter')['dateEnd']))]);
                    }
                    if(!empty($request->input('filter')['dateStart']) && empty($request->input('filter')['dateEnd'])){
                        
                        $query->whereBetween('component_checkouts.created_at', [date('Y-m-d H:i:s',strtotime($request->input('filter')['dateStart'])), date('Y-m-d H:i:s')]);
                    }
                }
            })
            ->orderBy('component_checkouts.created_at', 'asc');
            $total = $checkout->count();
            if($total > 0) {
            $checkout = $checkout->skip($offset)->take($limit)->get();
            $ass[] = (new ComponentsTransformer)->transformCheckedout($checkout, $total);
            }
        } else {
            $checkout = $checkout->orderBy('component_checkouts.created_at', 'asc');
                $total = $checkout->count();
                $checkout = $checkout->skip($offset)->take($limit)->get();
                if($total > 0) {
                    $ass[] = (new ComponentsTransformer)->transformCheckedout($checkout, $total);
                }
           
        }
        $result = [
            'rows' => [],
            'total' => 0,
            
            
        ];
        if(isset($ass))
        {
            foreach($ass as $key => $asset)
            {
                foreach($asset['rows'] as $key => $row)
                {
                    $result['rows'][] = $row;
                }
                $result['total'] += $asset['total'];
            }
        }
        $result['test'] = $ass;
        return response()->json($result, 200, [], JSON_NUMERIC_CHECK);
       // return (new ComponentsTransformer)->transformCheckedoutComponents($assets, $total);
    }
}