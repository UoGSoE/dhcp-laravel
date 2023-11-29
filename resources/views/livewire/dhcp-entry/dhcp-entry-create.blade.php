@extends('components.forms.dhcp-form-base')

@section('form-title')
    Create DHCP Entry
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
        @if (!$ipAddress or $ipAddress=='' )
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
        value="false"
        />
@endsection

@section('status-input')
    <input
        wire:model.live="isActive"
        type="checkbox"
        id="isActive"
        name="isActive"
        value="true"
        checked
        />
@endsection


@section('note-section')
    <label for="note" class="block text-base font-medium leading-6 text-gray-900 sm:pt-1.5">Notes</label>
    <div class="mt-2 sm:col-span-2 sm:mt-0">
        <textarea
            wire:model.live="note"
            placeholder="Add note here..."
            id="note"
            name="note"
            rows="3"
            class="block w-full max-w-2xl rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6">
        </textarea>
    </div>
@endsection

@section('save-cancel-buttons')
    <button type="button" class="text-base font-semibold leading-6 text-gray-900">
            <a href="{{ route('dhcp-entries')}}">
                Cancel
            </a>
    </button>
    <button wire:click.prevent="createDhcpEntry" type="submit"
        @if (count($errors) > 0)
            aria-disabled
            disabled
        @endif
        class="{{ (count($errors) > 0) ? 'disabled:opacity-75 disabled
        aria-disabled ' : '' }} inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-base
        font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2
        focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        Save
    </button>
@endsection
