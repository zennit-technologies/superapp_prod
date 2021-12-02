{{ setting('currency', '$') }}{{ $value ?? $model->price ?? $model[$column->attribute] ??  '' }}
