<?php

namespace BT\DataTables;

use BT\Modules\Quotes\Models\Quote;
use BT\Support\Statuses\QuoteStatuses;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Column;

class QuotesDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $statuses = QuoteStatuses::listsAllFlat() + ['overdue' => trans('bt.overdue')];

        return datatables()->eloquent($query)->addColumn('action', 'quotes._actions')
            ->editColumn('id', function (Quote $quote) {
                return '<input type="checkbox" class="bulk-record" data-id="' . $quote->id . '">';
            })
            ->editColumn('number', function (Quote $quote) {
                return '<a href="/quotes/' . $quote->id . '/edit">' . $quote->number . '</a>';
            })
            ->editColumn('quote_status_id', function (Quote $quote) use ($statuses) {
                $ret = '<td class="hidden-sm hidden-xs">
                <span class="badge badge-' . strtolower($statuses[$quote->status_text]) . '">
                    ' . trans('bt.' . strtolower($statuses[$quote->status_text])) . '</span>';
                if ($quote->viewed)
                    $ret .= '<span class="badge badge-success">' . trans('bt.viewed') . '</span>';
                else
                    $ret .= '<span class="badge badge-secondary">' . trans('bt.not_viewed') . '</span>';
                $ret .= '</td>';

                return $ret;
            })
            ->editColumn('invoice_id', function (Quote $quote) {
                $ret = '<td class="hidden-xs">';
                if ($quote->invoice_id)
                    $ret .= '<a href="' . route('invoices.edit', [$quote->invoice_id]) . '">' . trans('bt.invoice') . '</a>';
                elseif ($quote->workorder_id)
                    $ret .= '<a href="' . route('workorders.edit', [$quote->workorder_id]) . '">' . trans('bt.workorder') . '</a>';
                else
                    $ret .= trans('bt.no');
                $ret .= '</td>';

                return $ret;
            })
            ->editColumn('client.name', function (Quote $quote) {
                return '<a href="/clients/' . $quote->client->id . '">' . $quote->client->name . '</a>';
            })
            ->orderColumn('formatted_quote_date', 'quote_date $1')
            ->orderColumn('formatted_expires_at', 'expires_at $1')
            ->rawColumns(['client.name', 'invoice_id', 'quote_status_id', 'number', 'action', 'id']);
    }


    /**
     * Get query source of dataTable.
     *
     * @param Quote $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Quote $model)
    {
        $models = $model->with(['client', 'activities', 'amount.quote.currency'])->select('quotes.*')
            ->status(request('status'))
            ->clientId(request('client'))
            ->companyProfileId(request('company_profile'));

        return $models;

    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(3, 'desc');
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('id')
                ->orderable(false)
                ->searchable(false)
                ->printable(false)
                ->exportable(false)
                ->className('bulk-record')
            ,
            Column::make('quote_status_id')
                ->title(trans('bt.status')),
            Column::make('number')
                ->title(trans('bt.quote'))
                ->data('number'),
            Column::make('quote_date')
                ->title(trans('bt.date'))
                ->data('formatted_quote_date')
                ->searchable(false),
            Column::make('expires_at')
                ->title(trans('bt.due'))
                ->data('formatted_expires_at')
                ->searchable(false),
            Column::make('client_name')
                ->name('client.name')
                ->title(trans('bt.client'))
                ->data('client.name'),
            Column::make('summary')
                ->title(trans('bt.summary'))
                ->data('formatted_summary'),
            Column::make('amount')
                ->name('amount.total')
                ->title(trans('bt.total'))
                ->data('amount.formatted_total')
                ->orderable(true)
                ->searchable(false),
            Column::make('invoice_id')
                ->title(trans('bt.converted'))
                ->data('invoice_id')
                ->orderable(false)
                ->searchable(false),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(80)
                ->addClass('text-center'),

        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Quotes_' . date('YmdHis');
    }
}
