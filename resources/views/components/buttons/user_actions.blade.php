<div class="flex items-center space-x-2">

    
    <x-buttons.show :model="$model" />
    @hasanyrole('admin')
        @if($model->hasAnyRole('city-admin'))
            <x-buttons.assign :model="$model" />
        @endif
    @endhasanyrole
    <x-buttons.edit :model="$model" />
    @if( $model->is_active )
        <x-buttons.deactivate :model="$model" />
    @else
        <x-buttons.activate :model="$model" />
    @endif

    <x-buttons.delete :model="$model" />

</div>
