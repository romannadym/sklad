<?php

namespace App\Presenters;

/**
 * Class ComponentPresenter
 */
class ComponentCheckoutPresenter extends Presenter
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
                'title' => Заказчик,
                'visible' => true,
            ], 
            [
                'field' => 'component_name',
                'searchable' => true,
                'sortable' => true,
                'title' => 'Компонент',
	        'visible' => true, 
	        ], 
            [
                'field' => 'qty',
                'searchable' => true,
                'sortable' => true,
                'title' => 'Кол-во',
	        'visible' => true, 
	        ], 
            [
                'field' => 'note',
                'searchable' => true,
                'sortable' => true,
                'title' => 'Примечание',
	        'visible' => true, 
	        ], 
            [
                'field' => 'ticketnum',
                'searchable' => true,
                'sortable' => true,
                'title' => 'Номер заявки',
	        'visible' => true, 
	        ],
            [
                'field' => 'assigned_to_username',
                'searchable' => true,
                'sortable' => true,
                'title' => 'Инженер',
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
