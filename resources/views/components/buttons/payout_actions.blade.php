<div class="flex items-center space-x-2">
    <x-buttons.show :model="$model" />

    @if( $model->status == "review" )
    <x-buttons.deactivate :model="$model" />
    @hasanyrole("city-admin|admin")
    <x-buttons.activate :model="$model" />
    @endhasanyrole
    @endif
</div>
