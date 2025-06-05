<?php

namespace App\Presenters;

/**
 * Class ComponentPresenter
 */
class TicketPresenter extends Presenter
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
                'field' => 'asset_name',
                'searchable' => true,
                'sortable' => true,
                'title' => 'Наименование актива',
                'visible' => true,
                'formatter' => 'hardwareLinkObjFormatter'
            ],
            [
                'field' => 'asset_serial',
                'searchable' => true,
                'sortable' => true,
                'title' => 'Серийный номер актива',
                'visible' => true,
            ],
            [
                'field' => 'component',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.component'),
                'visible' => true,
                'formatter' => 'componentsLinkObjFormatter'
            ],
            [
                'field' => 'requester_email',
                'searchable' => true,
                'sortable' => true,
                'title' => 'Почта инженера',
	              'visible' => true,
	        ],
          [
              'field' => 'requester_name',
              'searchable' => true,
              'sortable' => true,
              'title' => 'Имя инженера',
              'visible' => true,
              'formatter' => 'usersLinkObjFormatter'
          ],
          [
              'field' => 'sd_ticket_id',
              'searchable' => true,
              'sortable' => true,
              'title' => 'Заявка(sd)',
              'visible' => true,
          ],
          [
              'field' => 'statuslabels',
              'searchable' => true,
              'sortable' => true,
              'title' => 'Статус',
              'visible' => true,
              'formatter' => 'statuslabelsLinkStatusObjFormatter',
          ],
          [
              'field' => 'created_at',
              'searchable' => false,
              'sortable' => true,
              'visible' => false,
              'title' => trans('general.created_at'),
              'formatter' => 'dateDisplayFormatter',
          ],
          [
              'field' => 'updated_at',
              'searchable' => false,
              'sortable' => true,
              'visible' => false,
              'title' => trans('general.updated_at'),
              'formatter' => 'dateDisplayFormatter',
          ],
        ];
        $layout[] = [
            'field' => 'checkincheckout',
            'searchable' => false,
            'sortable' => false,
            'switchable' => false,
            'title' => trans('general.checkout'),//trans('general.checkin').'/'.trans('general.checkout'),
            'visible' => true,
            'formatter' => 'ticketsInOutFormatter',
        ];
        $layout[] = [
            'field' => 'actions',
            'searchable' => false,
            'sortable' => false,
            'switchable' => false,
            'title' => trans('table.actions'),
            'formatter' => 'ticketsActionsFormatter',
        ];

        return json_encode($layout);
    }


}
