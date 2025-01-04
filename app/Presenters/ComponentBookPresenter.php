<?php

namespace App\Presenters;

/**
 * Class ComponentPresenter
 */
class ComponentBookPresenter extends Presenter
{
    /**
     * Json Column Layout for bootstrap table
     * @return string
     */
    public static function dataTableLayout()
    {
        $layout = [
            [
                'field' => 'id',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.id'),
                'visible' => false,
            ],
            [
                'field' => 'name',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.name'),
                'visible' => true,
            ], 
            [
                'field' => 'partnum',
                'searchable' => true,
                'sortable' => true,
                'title' => 'Партийный номер',
	        'visible' => true, 
	        ], 
        ];
     
        $layout[] = [
            'field' => 'actions',
            'searchable' => false,
            'sortable' => false,
            'switchable' => false,
            'title' => trans('table.actions'),
            'formatter' => 'componentbooksActionsFormatter',
        ];

        return json_encode($layout);
    }

    
}
