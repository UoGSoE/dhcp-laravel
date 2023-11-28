@if ($currentEditedEntry['id'] == $dhcpEntry->id && $currentEditedEntry['field'] == $fieldName)
    <input
        wire:model.live="editedEntries.{{ $dhcpEntry->id }}.{{ $fieldName }}"
        type="text"
        name="{{ $fieldName }}"
        id="{{ $fieldName }}"
        class="block w-full rounded-md border-0 py-1.5 pr-14 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
    />

        @if ( $errors->has('editedEntries.' . $dhcpEntry->id . '.' . $fieldName) )
            <div class='error-message text-red-700 mt-3 flex items-center'>
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span class='px-2 text-sm'>
                    {{ $errors->first('editedEntries.' . $dhcpEntry->id . '.' . $fieldName) }}
                </span>
            </div>
        @endif

        <div class="mt-3 flex flex-row gap-2">
            <span wire:click.prevent="updateField({{$dhcpEntry}}, '{{ $fieldName }}')"
                class="text-indigo-600 hover:text-indigo-900 hover:cursor-pointer hover:underline">
                Save
            </span>
            <span wire:click.prevent="cancelEditField('{{ $dhcpEntry->id }}')"
                class="text-indigo-600 hover:text-indigo-900 hover:cursor-pointer hover:underline">
                Cancel
            </span>
        </div>

    @else
        <span wire:click.prevent="editField({{$dhcpEntry}}, '{{ $fieldName }}')" class="hover:cursor-pointer">
            @if ($dhcpEntry[$fieldName])
                {{ $dhcpEntry[$fieldName] }}
            @else
                <i class="fa-solid fa-pencil opacity-30"></i>
            @endif
        </span>
@endif
