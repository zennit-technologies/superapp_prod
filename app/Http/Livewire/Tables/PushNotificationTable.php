<?php

namespace App\Http\Livewire\Tables;

use App\Models\Option;
use App\Models\PushNotification;
use Kdion4891\LaravelLivewireTables\Column;
use Illuminate\Support\Facades\Auth;

class PushNotificationTable extends BaseTableComponent
{

    public $model = PushNotification::class;
    public $header_view = 'components.buttons.new';




    public function query()
    {
        return PushNotification::with('user');
    }

    public function columns()
    {

        $columns = [
            Column::make(__('ID'), "id"),
            Column::make(__('Title'), 'title')->searchable()->sortable(),
            Column::make(__('Body'), 'body')->searchable()->sortable(),
            Column::make(__('Target'), 'role')->searchable()->sortable(),
            Column::make(__('Image'),'photo')->view('components.table.image_sm'),
            Column::make(__('Sender'),'user.name'),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->view('components.buttons.delete')
        ];

        return $columns;
    }
}
