<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Transformers\ActionlogsTransformer;
use App\Http\Transformers\ComponentsTransformer;
use App\Models\Component;
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
        
        $component = Component::all();
        
        $offset = request('offset', 0);
        $limit = $request->input('limit', 50);
        
        if ($request->filled('search') || $request->filled('filter')) {
            foreach( $component as $key => $comp) {
                $assets = $comp->assets()
                ->where(function ($query) use ($request) {
                    $search_str = '%' . $request->input('search') . '%';
                   $query->where('name', 'like', $search_str)
                            ->orWhereIn('model_id', function (Builder $query) use ($request) {
                                $search_str = '%' . $request->input('search') . '%';
                                $query->selectRaw('id')->from('models')->where('name', 'like', $search_str);
                            })
                            ->orWhere('asset_tag', 'like', $search_str)
                            ->orWhereIn('component_id', function (Builder $query) use ($request) {
                                $search_str = '%' . $request->input('search') . '%';
                                $query->select('id')
                                ->from('components')
                                ->where('name', 'like', $search_str);
                            })
                            ->orWhereIn('assigned_to_user_id', function (Builder $query) use ($request) {
                                $search_str = '%' . $request->input('search') . '%';
                                $query->select('id')
                                ->from('users')
                                ->where('first_name', 'like', $search_str)
                                ->orWhere('last_name', 'like', $search_str)
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$search_str]);
                            })->orWhere('ticketnum', 'like', $search_str);
                         })->where(function ($query) use ($request) {
                            if ($request->filled('filter')) {
                                if(!empty($request->input('filter')['assetName'])){
                                    $query->where('asset_tag', $request->input('filter')['assetName'] );
                                }
                                if(!empty($request->input('filter')['ticketnum'])){
                                    $query->where('ticketnum', 'like', '%'.$request->input('filter')['ticketnum'].'%' );
                                }
                                if(!empty($request->input('filter')['component_name'])){
                                    $query->whereIn('component_id', function (Builder $query) use ($request) {
                                        $query->select('id') 
                                        ->from('components')
                                        ->where('name', $request->input('filter')['component_name']);
                                    });
                                }
                                if(!empty($request->input('filter')['dateStart']) && !empty($request->input('filter')['dateEnd'])){
                                    
                                    $query->whereBetween('components_assets.created_at', [date('Y-m-d H:i:s',strtotime($request->input('filter')['dateStart'])), date('Y-m-d H:i:s',strtotime($request->input('filter')['dateEnd']))]);
                                }
                                if(!empty($request->input('filter')['dateStart']) && empty($request->input('filter')['dateEnd'])){
                                    
                                    $query->whereBetween('components_assets.created_at', [date('Y-m-d H:i:s',strtotime($request->input('filter')['dateStart'])), date('Y-m-d H:i:s')]);
                                }
                            }
                         })
                            
                         ->get();
                $total = $assets->count();
                if($total > 0) {
                    $ass[] = (new ComponentsTransformer)->transformCheckedoutComponents($assets, $total);
                }
            }
          
        } else {
            foreach( $component as $key => $comp) {
                $assets = $comp->assets();
                $total = $assets->count();
                $assets = $assets->skip($offset)->take($limit)->get();
                if($total > 0) {
                    $ass[] = (new ComponentsTransformer)->transformCheckedoutComponents($assets, $total);
                }
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
        $result['test'] = 1;
        return response()->json($result, 200, [], JSON_NUMERIC_CHECK);
       // return (new ComponentsTransformer)->transformCheckedoutComponents($assets, $total);
    }
}