{{ ($model->taxi_order != null && $model->taxi_order->currency != null ) ? $model->taxi_order->currency->symbol : setting('currency', '$') }}{{ $model->total }}
