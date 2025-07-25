<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Ticket::class);
        return view('tickets/index');
    }

    /**
     * Show the form for creating a new resource.
     */
     public function create()
     {
         $this->authorize('create', Ticket::class);

         return view('tickets/create')
             ->with('item', new Ticket);
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticketId) : RedirectResponse
    {
      $this->authorize('delete', Ticket::class);
      // Check if the Statuslabel exists
      if (is_null($ticket = Ticket::find($ticketId))) {
          return redirect()->route('tickets.index')->with('error', 'При удалении заявки возникла проблема. Пожалуйста попробуйте снова.');
      }

      // Check that there are no assets associated
      if ($ticket->delete()) {

          return redirect()->route('tickets.index')->with('success', 'Заявка успешно удалена!');
      }

      return redirect()->route('tickets.index')->with('error', 'Ошибка удаления, эта заявка связана с другими обьектами!');
    }
}
