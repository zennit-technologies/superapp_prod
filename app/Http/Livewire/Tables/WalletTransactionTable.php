<?php

namespace App\Http\Livewire\Tables;


use App\Models\WalletTransaction;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;


class WalletTransactionTable extends BaseDataTableComponent
{

    public $model = WalletTransaction::class;
    public function query()
    {
        $query = WalletTransaction::with('wallet.user', 'payment_method');
        return $query->when($this->getFilter('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($this->getFilter('start_date'), fn ($query, $sDate) => $query->whereDate('created_at', ">=", $sDate))
            ->when($this->getFilter('end_date'), fn ($query, $eDate) => $query->whereDate('created_at', "<=", $eDate))->orderBy('created_at', 'DESC');
    }

    public function filters(): array
    {
        return [
            'status' => Filter::make(__("Payment Status"))
                ->select([
                    '' => __('Any'),
                    'pending' => 'Pending',
                    'successful' => 'Successful',
                    'failed' => 'Failed',
                ]),
            'start_date' => Filter::make(__('Start Date'))
                ->date([
                    'min' => now()->subYear()->format('Y-m-d'), // Optional
                    'max' => now()->format('Y-m-d') // Optional
                ]),
            'end_date' => Filter::make(__('End Date'))
                ->date([
                    'min' => now()->subYear()->format('Y-m-d'), // Optional
                    'max' => now()->format('Y-m-d') // Optional
                ])
        ];
    }

    public function columns(): array
    {
        return [
            Column::make(__('ID'), "id"),
            Column::make(__('Ref No.'))->format(function ($value, $column, $row) {
                return view('components.table.custom', $data = [
                    "value" => "" . $row->ref . " ",
                ]);
            }),
            Column::make(__('Amount'), 'amount')->format(function ($value, $column, $row) {
                return view('components.table.price', $data = [
                    "model" => $row,
                    "value" => $value,
                ]);
            })->searchable()->sortable(),
            Column::make(__('User'), 'wallet.user.name')->searchable(),
            Column::make(__('Status'))->searchable()->sortable(),
            Column::make(__('Method'))->format(function ($value, $column, $row) {
                $localTransfer = substr($row->ref, 0, strlen("lt_")) === "lt_";
                return view('components.table.payment_method', $data = [
                    "model" => $row,
                    "value" => $localTransfer ? __("Transfer") : ($row->payment_method != null ? $row->payment_method->name : ''),
                ]);
            }),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->format(function ($value, $column, $row) {
                return view('components.buttons.transaction_actions', $data = [
                    "model" => $row,
                ]);
            }),
        ];
    }


    public function activateModel()
    {

        try {
            \DB::beginTransaction();
            $this->selectedModel->status = "successful";
            $this->selectedModel->save();
            //update wallet balance
            $this->selectedModel->wallet->balance += $this->selectedModel->amount;
            $this->selectedModel->wallet->save();
            \DB::commit();
            $this->showSuccessAlert("Activated");
        } catch (Exception $error) {
            \DB::rollback();
            $this->showErrorAlert("Failed");
        }
    }


    public function deactivateModel()
    {

        try {
            \DB::beginTransaction();
            $this->selectedModel->status = "failed";
            $this->selectedModel->save();
            \DB::commit();
            $this->showSuccessAlert("Deactivated");
        } catch (Exception $error) {
            $this->showErrorAlert("Failed");
        }
    }
}
