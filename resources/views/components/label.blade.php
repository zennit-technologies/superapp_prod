<label class="block mt-4 text-sm {{ setting('localeCode') == 'ar' ? 'text-right':'text-left' }}">
    <p class="mb-1 text-gray-700" dir="rtl">{{ $title ?? '' }}</p>
    {{ $slot ?? '' }}
  </label>
