@extends('components.forms.dhcp-form-base')

@props([
    'action' => 'edit',
])

@section('form-title')
    Edit DHCP Entry
@endsection

@section('mac-address-input')
    <input
        wire:model.live="macAddress"
        type="text"
        name="macAddress"
        id="macAddress"
        autocomplete=""
        class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-base sm:leading-6"
        placeholder="MAC address" />
@endsection

@section('owner-input')
    <input
        wire:model.live="owner"
        type="text"
        name="owner"
        id="owner"
        autocomplete=""
        class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-base sm:leading-6"
        placeholder="Owner (email address)" />
@endsection

@section('ip-address-input')
    <input
        wire:model.live="ipAddress"
        type="text"
        name="ipAddress"
        id="ipAddress"
        autocomplete=""
        class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-base sm:leading-6"
        placeholder="IP address (leave blank for pool)" />
@endsection

@section('hostname-input')
    <input
        @if (!$ipAddress or $ipAddress == '')
            disabled
        @endif
        wire:model.live="hostname"
        type="text"
        name="hostName"
        id="hostName"
        autocomplete=""
        class="{{ (!$ipAddress or $ipAddress == '') ? 'disabled:bg-gray-100 disabled:text-slate-500 ' : '' }} block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-base sm:leading-6"
        placeholder="Hostname (only for fixed IP machines)" />
@endsection

@section('ssd-input')
    <input
        wire:model.live="isSsd"
        type="checkbox"
        id="isSsd"
        name="isSsd"
        value="true"
        @if (boolval($dhcpEntry->ssd) == true)
            checked
        @endif />
@endsection

@section('status-input')
    <input
        wire:model.live="isActive"
        type="checkbox"
        id="isActive"
        name="isActive"
        @if (boolval($dhcpEntry->is_active) == true)
            checked
        @endif />
@endsection

@section('note-section')
    <label for="note" class="block text-base font-medium leading-6 text-gray-900 sm:pt-1.5">Notes</label>

    <ul class="flex flex-col gap-y-4 mt-2 sm:col-span-2 sm:mt-0">
        @if ($notes)
            @foreach ($notes->sortBy('updated_at') as $note)
                <li wire:key="{{ $note->id }}"
                    class="flex flex-col sm:max-w-md [&:nth-last-child(n+3)]:border-b-2">

                    {{-- Note user and dates --}}
                    <div class="flex flex-col gap-y-1">
                        {{-- Note user, edit/delete --}}
                        <div class="flex items-center gap-1 gap-x-3 justify-between">
                            <p class="text-base font-semibold leading-6 text-gray-900">
                                {{ $note->created_by }}
                            </p>

                            {{-- Note edit/delete functionality currently not needed --}}
                            {{-- TODO - if functionality included, fix wire.confirm --}}
                            {{-- <div class="flex flex-row gap-x-6 text-gray-600">
                                <i wire:click.prevent="editNote({{ $note }})"
                                    class="fa-solid fa-pencil w-1 hover:cursor-pointer"></i>
                                <i wire:click="deleteNote('{{ $note->id }}')" wire:confirm="Are you sure you want to delete this note?"
                                    class="fa-solid fa-trash w-1 hover:cursor-pointer"></i>
                            </div> --}}

                        </div>

                        {{-- Note created + edit dates --}}
                        <div class="flex gap-x-4 flex-row line-clamp-1 text-base text-gray-600">
                            <p class="flex-none">
                                {{ $note->created_at->format('d-m-Y H:i') }}
                            </p>
                            @if ($note->updated_at != $note->created_at)
                            <p class="flex-none italic">
                                Edited: {{ $note->updated_at->format('d-m-Y H:i') }}
                            </p>
                            @endif
                        </div>
                    </div>

                    {{-- Note text --}}
                    <p class="mt-4 pb-4 text-md leading-6 text-gray-600"> {{ $note->note }}</p>
                </li>
            @endforeach
        @endif

        <li class="sm:max-w-md">
            <textarea
                wire:model.live="note"
                placeholder="Add note here..."
                id="note"
                name="note"
                rows="3"
                class="block w-full max-w-2xl rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-base sm:leading-6">
            </textarea>
        </li>
    </ul>
@endsection

@section('save-cancel-buttons')
    <button type="button" class="text-base font-semibold leading-6 text-gray-900">
        <a href="{{ route('dhcp-entries')}}">
            Cancel
        </a>
    </button>
    <button
        wire:click.prevent="saveDhcpEntry"
        type="submit"
        @if (count($errors)> 0)
            aria-disabled
            disabled
        @endif
        class="{{ (count($errors) > 0) ? 'disabled:opacity-75 disabled
        aria-disabled ' : '' }} inline-flex justify-center rounded-md bg-primary px-3 py-2 text-base
        font-semibold text-white shadow-sm hover:bg-primary focus-visible:outline focus-visible:outline-2
        focus-visible:outline-offset-2 focus-visible:outline-primary">
        Save
    </button>
@endsection
