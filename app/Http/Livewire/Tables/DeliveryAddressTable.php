<?php

namespace App\Http\Livewire\Tables;

use App\Models\DeliveryAddress;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Kdion4891\LaravelLivewireTables\Column;

class DeliveryAddressTable extends BaseTableComponent
{

    public $model = DeliveryAddress::class;
    public $header_view = 'components.buttons.new';
    public function query()
    {
        $user = User::find(Auth::id());
        if ($user->hasRole('city-admin')) {
            return DeliveryAddress::with('user')->whereHas("user", function ($query) {
                return $query->where('creator_id', Auth::id());
            });
        } else {
            return DeliveryAddress::with('user');
        }
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->searchable(),
            Column::make(__('User'),'user.name')->searchable(),
            Column::make(__('Name'),'name')->searchable(),
            Column::make(__('Address'),'address')->searchable(),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->view('components.buttons.edit'),
            Column::make('')->view('components.buttons.delete'),
        ];
    }
}
