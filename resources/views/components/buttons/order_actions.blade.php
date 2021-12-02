<div class="flex items-center space-x-2">
    <x-buttons.show :model="$model" />
    @if (!in_array($model->status, ['failed', 'delivered', 'cancelled']) && (!in_array($model->payment_status, ['review']) || empty($model->payment)))
    <x-buttons.edit :model="$model" />
    @endif

    @role('admin')
    @if (in_array($model->payment_status, ['review']) && !empty($model->payment))
    <x-buttons.review :model="$model" />
    @endif
    @endrole
</div>
