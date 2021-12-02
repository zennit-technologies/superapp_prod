<div class="flex items-center space-x-2">


    @hasanyrole('admin|city-admin')
        <x-buttons.assign :model="$model" />
    @endhasanyrole
    <x-buttons.time :model="$model" />
    <x-buttons.show :model="$model" />
    <x-buttons.edit :model="$model" />
    
    @hasanyrole('city-admin|admin')
        @if( $model->is_active )
            <x-buttons.deactivate :model="$model" />
        @else
            <x-buttons.activate :model="$model" />
        @endif

        <x-buttons.delete :model="$model" />
    @endhasanyrole

    @role('manager')
        @if( $model->is_active )
            <x-buttons.deactivate :model="$model" />
        @endif
    @endrole



</div>
