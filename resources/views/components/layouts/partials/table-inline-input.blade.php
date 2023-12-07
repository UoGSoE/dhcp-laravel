@if (array_key_exists($dhcpEntry->id, $editedEntries))
    <input
        wire:model.live="editedEntries.{{ $dhcpEntry->id }}.{{ $fieldName }}"
        type="text"
        name="{{ $fieldName }}"
        id="{{ $fieldName }}"
        class="block w-full rounded-md border-0 py-1.5 pr-14 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-base sm:leading-6"
    />

        @if ( array_key_exists($fieldName, $errors) )
            <div class='error-message text-red-700 mt-3 flex items-center'>
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span class='px-2 text-base'>
                    {{ $errors[$fieldName][0] }}
                </span>
            </div>
        @endif

    @else
        @if ($dhcpEntry[$fieldName])
            {{ $dhcpEntry[$fieldName] }}
        @endif
@endif
