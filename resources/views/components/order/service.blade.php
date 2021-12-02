<table class="w-full border rounded">
    <thead>
        <tr class="font-medium bg-gray-200 ">
            <th class="p-2">{{__('Service')}}</th>
            <th class="p-2">{{__('Price')}}</th>
            <th class="p-2">{{__('Hours')}}</th>
        </tr>
    </thead>
    <tbody>

        @if (!empty($order))
            <tr class="font-light border-b ">
                <td class="p-2">{{ $order->order_service->service->name ?? '' }}</td>
                <td class="p-2">{{ setting('currency', '$') }} {{ $order->order_service->price ?? '' }}</td>
                <td class="p-2">{{ $order->order_service->hours ?? '' }}cm</td>
            </tr>
        @endif

    </tbody>
</table>
