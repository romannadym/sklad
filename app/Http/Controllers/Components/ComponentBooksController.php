<?php

namespace App\Http\Controllers\Components;

use App\Http\Controllers\Controller;
use App\Models\ComponentBook;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
/**
 * This class controls all actions related to Components for
 * the Snipe-IT Asset Management application.
 *
 * @version    v1.0
 */
class ComponentBooksController extends Controller
{
    /**
     * Returns a view that invokes the ajax tables which actually contains
     * the content for the components listing, which is generated in getDatatable.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ComponentsController::getDatatable() method that generates the JSON response
     * @since [v3.0]
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('view', ComponentBook::class);

        return view('componentbooks/index');
    }

      /**
     * Returns a form to create a new component.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ComponentsController::postCreate() method that stores the data
     * @since [v3.0]
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', ComponentBook::class);

        return view('componentbooks/create')
            ->with('item', new ComponentBook);
    }

    public function store(Request $request)
    {
        $this->authorize('create', ComponentBook::class);
        $component = new ComponentBook();
        $component->name                   = $request->get('name');
        $component->partnum                  = $request->get('partnum');
        $component->category_id            = $request->get('category_id');
      //  session()->put(['redirect_option' => $request->get('redirect_option')]);
        $duplicate = ComponentBook::where(['partnum' => $request->get('partnum')])->first();
        if(isset($duplicate->partnum))
        {
            $duplicate = new \stdClass();
            $duplicate->duplicate = 1;
            return redirect()->back()->withInput()->withErrors($duplicate);
        }

        if ($component->save()) {
           // return redirect()->to(Helper::getRedirectOption($request, $component->id, 'Components'))->with('success', trans('admin/components/message.create.success'));
            return redirect()->route('componentbooks.index')->with('success', 'Component '.$component->id.' Book created successfully!');
        }

        return redirect()->back()->withInput()->withErrors($component->getErrors());
    }

      /**
     * Return a view to edit a component.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ComponentsController::postEdit() method that stores the data.
     * @since [v3.0]
     * @param int $componentId
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($componentId = null)
    {
        if ($item = ComponentBook::find($componentId)) {
            $this->authorize('update', $item);
            $item->image = json_decode($item->image);
            return view('componentbooks/edit', compact('item'));
        }

        return redirect()->route('componentbooks.index')->with('error', trans('admin/components/message.does_not_exist'));
    }

     /**
     * Return a view to edit a component.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ComponentsController::getEdit() method presents the form.
     * @param ImageComponentUploadRequest $request
     * @param int $componentId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @since [v3.0]
     */
    public function update(Request $request, $componentId = null)
    {
        if (is_null($component = ComponentBook::find($componentId))) {
            return redirect()->route('componentbooks.index')->with('error', trans('admin/components/message.does_not_exist'));
        }

        $this->authorize('update', $component);
        $duplicate = ComponentBook::where(['partnum' => $request->input('partnum')])->where('id','!=',$componentId)->first();
        if(isset($duplicate->partnum))
        {
            $duplicate = new \stdClass();
            $duplicate->duplicate = 1;
            return redirect()->back()->withInput()->withErrors($duplicate);
        }
        // Update the component data
        $component->name                   = $request->input('name');
        $component->partnum                  = $request->input('partnum');
        $component->category_id            = $request->input('category_id');

        if ($component->save()) {
            return redirect()->route('componentbooks.index')->with('success', 'Component '.$component->id.' Book edited successfully!');
        }

        return redirect()->back()->withInput()->withErrors($component->getErrors());
    }

    /**
     * Delete a component.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v3.0]
     * @param int $componentId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($componentId)
    {
        if (is_null($component = ComponentBook::find($componentId))) {
            return redirect()->route('componentbooks.index')->with('error', trans('admin/components/message.does_not_exist'));
        }

        $this->authorize('delete', $component);


        $component->delete();

        return redirect()->route('componentbooks.index')->with('success', trans('admin/components/message.delete.success'));
    }

}
