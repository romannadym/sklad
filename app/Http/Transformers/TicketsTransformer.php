<?php

namespace App\Http\Transformers;

use App\Helpers\Helper;
use App\Models\Ticket;
use App\Models\Statuslabel;
use App\Models\Component;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
class TicketsTransformer
{
    public function transformTickets(Collection $tickets, $total)
    {
        $array = [];
        foreach ($tickets as $ticket) {
            $array[] = self::transformTicket($ticket);
        }

        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformTicket(Ticket $ticket)
    {
        $component = Component::find($ticket->component_id);
        $asset = Asset::find($ticket->asset_id);
        $statusLabel = Statuslabel::find($ticket->status_id);

        $array = [
            'id' => (int) $ticket->id,
            'name' => e($ticket->asset_name) . '(S/n: ' . e($ticket->asset_serial) . ')',
            'asset_name' => ($asset->id) ? [
                'id' => (int) $asset->id,
                'name' => e($ticket->asset_name),
            ] : null,
            'asset_serial' => e($ticket->asset_serial),
            'requester_email' => e($ticket->requester_email),
            'requester_name' => ($ticket->id) ? [
              'id' => e($ticket->user_id),
              'name' => e($ticket->requester_name),
            ] : null,
            'sd_ticket_id' => e($ticket->sd_ticket_id),
            'component' => ($component && $component->id) ? [
                'id' => (int) $component->id,
                'name' => e($component->name) . ' (P/n ' . e($component->partnum) . ')',
                'checkin_id' => $component->assets()->where('asset_id', $ticket->asset_id)->first()->pivot->id ?? null
            ] : [
                'id' => (int) 0,
                'name' => 'Компонент был удален из системы',
                'checkin_id' => 0
            ],
            'serial' => ($component && $component->serial) ? e($component->serial) : null,
            'location' => ($component && $component->location && $component->assets->isEmpty()) ? [
                      'id' => (int) $component->location->id,
                      'name' => e($component->location->name),
                  ] : null,
            'statuslabels' => ($statusLabel->id) ? [
              'id' => (int) $statusLabel->id,
              'name' => e($statusLabel->name),
              'color' => e($statusLabel->color)
            ] : null,
            'created_by' => ($ticket->adminuser) ? [
                'id' => (int) $ticket->adminuser->id,
                'name'=> e($ticket->adminuser->present()->fullName()),
            ] : null,
            'created_at' => Helper::getFormattedDateObject($ticket->created_at, 'datetime'),
            'updated_at' => Helper::getFormattedDateObject($ticket->updated_at, 'datetime'),
            'user_can_checkout' =>  ($component && $component->numRemaining() > 0) ? 1 : 0,
        ];

        $permissions_array['available_actions'] = [
            $ticket->status_id == 13 ? '' : 'checkout' => Gate::allows('checkout', $component),
            'checkin' => Gate::allows('checkin', $component),
        //    'update' => Gate::allows('update', Ticket::class),
            'delete' => Gate::allows('delete', Ticket::class),
        ];
        $array += $permissions_array;

        return $array;
    }


}
