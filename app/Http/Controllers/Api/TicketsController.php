<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Transformers\TicketsTransformer;
use App\Models\Ticket;
use App\Models\User;
use App\Http\Requests\ImageUploadRequest;
use App\Models\Asset;//Актив
use App\Models\Category;
use App\Models\ComponentCheckout;
use App\Models\Component;
use App\Models\Company;
use App\Models\Manufacturer; //Производитель
use App\Models\AssetModel;//Модель
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
              'status_id'
          ];
        $tickets = Ticket::select('tickets.*'); //
        if ($request->filled('search')) {
            $tickets = $tickets->TextSearch($request->input('search'));
        }

        if ($request->filled('name')) {
            $tickets->where('asset_name', '=', $request->input('name'));
        }
        if ($request->filled('sd_ticket_id')) {
            $tickets->where('sd_ticket_id', '=', $request->input('sd_ticket_id'));
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
      if(empty($request->get('component_id'))){
        return response()->json(['message' => 'Отсутсвует id компонента'], 422);
      }

      $asset = Asset::with('company')->with('model.category')->where('asset_tag', $request->get('asset_name'))->first();

      if(!$asset){
        $category = Category::where('category_type', 'asset')->first();
        $model = AssetModel::where('name', $request->get('asset_model'))->first();
        $company = Company::where('name', $request->get('company_name'))->first();
        $brand = Manufacturer::where('name', $request->get('asset_brand'))->first();

        $asset = new Asset();
        $asset->asset_tag = $request->get('asset_brand').' '.$request->get('asset_model').' (S/n: '.$request->get('asset_serial').')';
        //$asset->name = 'HP EliteBook 850';
        $asset->model_id = $model->id; // ID модели
        $asset->company_id = $company->id; // ID компании
        //  $asset->location_id = 3; // ID локации
        $asset->status_id = 2; // например, "В эксплуатации"
        $asset->serial = $request->get('asset_serial');
        //  $asset->supplier_id = 4; // ID поставщика
        $asset->order_number = $request->get('asset_contract');
        // Дополнительно при необходимости
        $asset->notes = 'Создан через API из сервисдеска';

        if (!$asset->save()) {
            return response()->json(['error' => $asset->getErrors()], 422);
        }


      }

      $user = User::where('email', $request->get('engineer_email'))->first();
      if(!$user){
        $user = new User;
        //Username, email, and password need to be handled specially because the need to respect config values on an edit.
        $user->email = trim($request->input('engineer_email'));
        $user->username = trim($request->input('engineer_email'));
        $user->password = bcrypt('Gfhjkm123!');
        $user->first_name = $request->input('engineer_name');
        $user->last_name = $request->input('engineer_name');
        $user->activated = $request->input('activated', 0);
        $user->notes = 'Создан через API из сервисдеска';
        $user->address = $request->input('address', null);
        $user->city = $request->input('city', null);
        $user->state = $request->input('state', null);
        $user->country = $request->input('country', null);
        $user->zip = $request->input('zip', null);
        $user->remote = $request->input('remote', 0);
        $user->website = $request->input('website', null);
        $user->created_by = auth()->id();
        $user->start_date = $request->input('start_date', null);
        $user->end_date = $request->input('end_date', null);
        $user->autoassign_licenses = $request->input('autoassign_licenses', 0);
        app(ImageUploadRequest::class)->handleImages($user, 600, 'avatar', 'avatars', 'avatar');
        if (!$user->save()) {
            return response()->json(['error' => $user->getErrors()], 422);
        }
      }
      $ticket = new Ticket();

      $ticket->asset_name = $request->get('asset_model');
      $ticket->asset_serial = $request->get('asset_serial');
      $ticket->asset_id = $asset->id;
      $ticket->user_id = $user->id;
      $ticket->requester_email =  $request->get('engineer_email');
      $ticket->requester_name =  $request->get('engineer_name');
      $ticket->component_id =  $request->get('component_id');
      $ticket->sd_ticket_id = $request->get('sd_ticket_id');

      if (!$ticket->save()) {
          return response()->json(['error' => $asset->getErrors()], 422);
      }
      return response()->json([
          'message' => 'created',
          'data' => $ticket,
      ], 200);

    }

    /**
     * Display the specified resource.
     */
    public function show($id) : array
    {
        return response()->json(['show' => $id], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) : JsonResponse
    {
      $ticket = Ticket::find($id);
      $status_id = $request->get('status_id');
      if (!$ticket) {
          return response()->json(['error' => 'Ticket not found'], 404);
      }
      $component = Component::find($ticket->component_id);
      if (!$component) {
          return response()->json(['error' => 'Ticket not found'], 404);
      }
      if($status_id == 11)
      {
        $component->assets()->wherePivot('asset_id', $ticket->asset_id)->update([
            'note' => 'Установлено через API из сервисдеска',
            'ticketnum' => $ticket->id
        ]);

        $checkout = ComponentCheckout::create([
            'component_id' => $ticket->component_id,
            'assigned_qty' => 1,
            'asset_id' => $ticket->asset_id,
            'note' => 'Установлено через API из сервисдеска',
            'ticketnum' => $id,
            'assigned_to_user_id' => $ticket->user_id,
        ]);
      }

        $ticket->status_id = $status_id;
        $ticket->save();
      return response()->json([
      'success' => true,
      'ticket' => $ticket,
      'checkout_id' => $checkout->id ?? null,
      ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) : JsonResponse
    {
        //
    }
}
