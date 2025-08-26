<?php

namespace App\Http\Controllers\Components;

use App\Events\CheckoutableCheckedOut;
use App\Events\ComponentCheckedOut;
use App\Helpers\Helper;
use App\Models\Ticket;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Component;
use App\Models\ComponentCheckout;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class ComponentCheckoutController extends Controller
{
    /**
     * Returns a view that allows the checkout of a component to an asset.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ComponentCheckoutController::store() method that stores the data.
     * @since [v3.0]
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $assetId = request()->input('asset_id'); // или request('asset_id')
        $userId = request()->input('user_id');
        $ticketId = request()->input('ticket_id');
        $note = request()->input('note');
        if ($component = Component::find($id)) {

            $this->authorize('checkout', $component);

            // Make sure the category is valid
            if ($component->category) {

                // Make sure there is at least one available to checkout
                if ($component->numRemaining() <= 0){
                    return redirect()->route('components.index')
                        ->with('error', trans('admin/components/message.checkout.unavailable'));
                }

                // Return the checkout view
                $component->remaining = (int)$component->numRemaining();

                return view('components/checkout', compact('component','assetId','userId','ticketId','note'));
            }

            // Invalid category
            return redirect()->route('components.edit', ['component' => $component->id])
                ->with('error', trans('general.invalid_item_category_single', ['type' => trans('general.component')]));
        }

        // Not found
        return redirect()->route('components.index')->with('error', trans('admin/components/message.not_found'));

    }

    /**
     * Validate and store checkout data.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ComponentCheckoutController::create() method that returns the form.
     * @since [v3.0]
     * @param Request $request
     * @param int $componentId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request, $componentId)
    { 
        // Check if the component exists
        if (!$component = Component::find($componentId)) {
            // Redirect to the component management page with error
            return redirect()->route('components.index')->with('error', trans('admin/components/message.not_found'));
        }

        $this->authorize('checkout', $component);
        //Проверка на дубли по серийным номерам
        foreach ($request->get('serial') as $key => $serial) {
            $duplicate = Component::where('serial', '=', $serial)->first();
            if(isset($duplicate->id) && $duplicate->id == $componentId){
                continue;
            }
            if($duplicate){
                $duplicate_serials[$key] = $serial;
            }
        }
        if(isset($duplicate_serials)){
            return redirect()->route('components.checkout.show', $componentId)
                ->with('error', trans('admin/components/message.checkout.duplicate_error'))
                ->withInput()
                ->with('duplicate_serials', $duplicate_serials);
        }

        $max_to_checkout = $component->numRemaining();

        // Make sure there are at least the requested number of components available to checkout
        if ($max_to_checkout < $request->get('assigned_qty')) {
            return redirect()->back()->withInput()
            ->with('error', trans('admin/components/message.checkout.unavailable', ['remaining' => $max_to_checkout, 'requested' => $request->get('assigned_qty')]));
        }

        $validator = Validator::make($request->all(), [
            'asset_id'          => 'required|exists:assets,id',
            'assigned_qty'      => "required|numeric|min:1|digits_between:1,$max_to_checkout",
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if the asset exists
        $asset = Asset::find($request->input('asset_id'));

        if ((Setting::getSettings()->full_multiple_companies_support) && $component->company_id !== $asset->company_id) {
            return redirect()->route('components.checkout.show', $componentId)
            ->with('error', trans('general.error_user_company'));
        }


        if($request->get('assigned_qty') == $component->qty && $component->qty == 1)
        {
            foreach($request->get('serial') as $key => $serial){
                $component->serial = $serial;
                $component->save();
            }

            // Update the component data

            $component->asset_id = $request->input('asset_id');
            $component->assets()->attach($component->id, [
                'component_id' => $component->id,
                'created_by' => auth()->user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'assigned_qty' => 1,
                'asset_id' => $request->input('asset_id'),
                'note' => $request->input('note'),
                'ticketnum' => $request->input('ticketnum'),
                'assigned_to_user_id' => $request->input('assigned_to_user_id'),
            ]);
            ComponentCheckout::create([
                'component_id' => $component->id,
                'assigned_qty' => 1,
                'asset_id' => $request->input('asset_id'),
                'note' => $request->input('note'),
                'ticketnum' => $request->input('ticketnum'),
                'assigned_to_user_id' => $request->input('assigned_to_user_id'),
            ]);
            event(new CheckoutableCheckedOut($component, $asset, auth()->user(), $request->input('note')));
            unset($component->asset_id);
        }

        else if ($request->get('assigned_qty') == $component->qty && $component->qty > 1)
        {
            $i = 1;
            foreach($request->get('serial') as $key => $serial)
            {
                if($i == 1)
                {
                    $component->serial = $serial;
                    $component->save();

                    // Update the component data

                    $component->asset_id = $request->input('asset_id');
                    $component->assets()->attach($component->id, [
                        'component_id' => $component->id,
                        'created_by' => auth()->user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'assigned_qty' => 1,
                        'asset_id' => $request->input('asset_id'),
                        'note' => $request->input('note'),
                        'ticketnum' => $request->input('ticketnum'),
                        'assigned_to_user_id' => $request->input('assigned_to_user_id'),
                    ]);
                    ComponentCheckout::create([
                        'component_id' => $component->id,
                        'assigned_qty' => 1,
                        'asset_id' => $request->input('asset_id'),
                        'note' => $request->input('note'),
                        'ticketnum' => $request->input('ticketnum'),
                        'assigned_to_user_id' => $request->input('assigned_to_user_id'),
                    ]);
                    event(new CheckoutableCheckedOut($component, $asset, auth()->user(), $request->input('note')));
                    unset($component->asset_id);
                }
                else
                {
                $componentNew = new Component();
                $componentNew->name                   = $component->name;
                $componentNew->category_id            = $component->category_id;
                $componentNew->supplier_id            = $component->supplier_id;
                $componentNew->location_id            = $component->location_id;
                $componentNew->company_id             = $component->company_id;
                $componentNew->serial                 = $serial;
                $componentNew->qty                    = 1;
                $componentNew->created_by             = auth()->id();
                $componentNew->notes                  = $component->notes;
                $componentNew->partnum                = $component->partnum;
                $componentNew->status                 = $component->status;
                $componentNew->customer               = $component->customer;
                $componentNew->save();
                if($componentNew->id)
                    {
                        $component->qty = $component->qty - 1;
                        $component->save();

                        // Update the component data

                        $componentNew->asset_id = $request->input('asset_id');
                        $componentNew->assets()->attach($componentNew->id, [
                            'component_id' => $componentNew->id,
                            'created_by' => auth()->user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'assigned_qty' => 1,
                            'asset_id' => $request->input('asset_id'),
                            'note' => $request->input('note'),
                            'ticketnum' => $request->input('ticketnum'),
                            'assigned_to_user_id' => $request->input('assigned_to_user_id'),
                        ]);
                        ComponentCheckout::create([
                            'component_id' => $componentNew->id,
                            'assigned_qty' => 1,
                            'asset_id' => $request->input('asset_id'),
                            'note' => $request->input('note'),
                            'ticketnum' => $request->input('ticketnum'),
                            'assigned_to_user_id' => $request->input('assigned_to_user_id'),
                        ]);
                        event(new CheckoutableCheckedOut($componentNew, $asset, auth()->user(), $request->input('note')));
                        unset($componentNew->asset_id);
                    }
                }


                $i++;
            }
        }
        //если запрос пришел из заявки на компонент
        else if($request->get('assigned_qty') != $component->qty && $request->has('ticket_id'))
        {
            foreach($request->get('serial') as $key => $serial){
                $componentNew = new Component();
                $componentNew->name                   = $component->name;
                $componentNew->category_id            = $component->category_id;
                $componentNew->supplier_id            = $component->supplier_id;
                $componentNew->location_id            = $component->location_id;
                $componentNew->company_id             = $component->company_id;
                $componentNew->serial                 = $serial;
                $componentNew->qty                    = 1;
                $componentNew->created_by             = auth()->id();
                $componentNew->notes                  = $component->notes;
                $componentNew->partnum                = $component->partnum;
                $componentNew->status                 = $component->status;
                $componentNew->customer               = $component->customer;
                $componentNew->save();
                if($componentNew->id)
                {
                    $component->qty = $component->qty - 1;
                    $component->save();

                    // Update the component data

                    $componentNew->asset_id = $request->input('asset_id');
                    $componentNew->assets()->attach($componentNew->id, [
                        'component_id' => $componentNew->id,
                        'created_by' => auth()->user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'assigned_qty' => 1,
                        'asset_id' => $request->input('asset_id'),
                        'note' => $request->input('note'),
                        'ticketnum' => $request->input('ticketnum'),
                        'assigned_to_user_id' => $request->input('assigned_to_user_id'),
                    ]);
                    ComponentCheckout::create([
                        'component_id' => $componentNew->id,
                        'assigned_qty' => 1,
                        'asset_id' => $request->input('asset_id'),
                        'note' => $request->input('note'),
                        'ticketnum' => $request->input('ticketnum'),
                        'assigned_to_user_id' => $request->input('assigned_to_user_id'),
                    ]);
                    event(new CheckoutableCheckedOut($componentNew, $asset, auth()->user(), $request->input('note')));
                    unset($componentNew->asset_id);
                }
            }
            $ticket_id = $request->input('ticket_id');
            $ticket = Ticket::where('id', $ticket_id)->first();
            if($ticket)
            {
              $ticket->component_id = $componentNew->id;
              $ticket->save();
            }
        }
        else if($request->get('assigned_qty') != $component->qty)
        {
            foreach($request->get('serial') as $key => $serial){
                $componentNew = new Component();
                $componentNew->name                   = $component->name;
                $componentNew->category_id            = $component->category_id;
                $componentNew->supplier_id            = $component->supplier_id;
                $componentNew->location_id            = $component->location_id;
                $componentNew->company_id             = $component->company_id;
                $componentNew->serial                 = $serial;
                $componentNew->qty                    = 1;
                $componentNew->created_by             = auth()->id();
                $componentNew->notes                  = $component->notes;
                $componentNew->partnum                = $component->partnum;
                $componentNew->status                 = $component->status;
                $componentNew->customer               = $component->customer;
                $componentNew->save();
                if($componentNew->id)
                {
                    $component->qty = $component->qty - 1;
                    $component->save();

                    // Update the component data

                    $componentNew->asset_id = $request->input('asset_id');
                    $componentNew->assets()->attach($componentNew->id, [
                        'component_id' => $componentNew->id,
                        'created_by' => auth()->user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'assigned_qty' => 1,
                        'asset_id' => $request->input('asset_id'),
                        'note' => $request->input('note'),
                        'ticketnum' => $request->input('ticketnum'),
                        'assigned_to_user_id' => $request->input('assigned_to_user_id'),
                    ]);
                    ComponentCheckout::create([
                        'component_id' => $componentNew->id,
                        'assigned_qty' => 1,
                        'asset_id' => $request->input('asset_id'),
                        'note' => $request->input('note'),
                        'ticketnum' => $request->input('ticketnum'),
                        'assigned_to_user_id' => $request->input('assigned_to_user_id'),
                    ]);
                    event(new CheckoutableCheckedOut($componentNew, $asset, auth()->user(), $request->input('note')));
                    unset($componentNew->asset_id);
                }
            }
        }
        $ticket_id = $request->input('ticket_id');
        $ticket = Ticket::where('id', $ticket_id)->first();
        if($ticket)
        {
          $ticket->status_id = 10;
          $ticket->save();
        }


        $request->request->add(['checkout_to_type' => 'asset']);
        $request->request->add(['assigned_asset' => $asset->id]);

        session()->put(['redirect_option' => $request->get('redirect_option'), 'checkout_to_type' => $request->get('checkout_to_type')]);

        return redirect()->to(Helper::getRedirectOption($request, $component->id, 'Components'))->with('success', trans('admin/components/message.checkout.success'));
    }
}
