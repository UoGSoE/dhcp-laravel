<div class="relative inline-block text-left ml-1"
    wire:click.outside="$set('active', false)"
>
    <div>
        <button
            wire:click="$toggle('active')"
            type="button"
            {{-- data-dropdown-toggle="table-dropdown-component-{{ $id }}" --}}
            class="flex items-center rounded-full text-gray-400 hover:text-gray-600 focus:outline-none" id="menu-button"
            aria-expanded="true" aria-haspopup="true">
            <span class="sr-only">Open options</span>
            <i class="fa-solid fa-ellipsis-vertical"></i>
        </button>
    </div>


    {{--
    <!--
    Dropdown menu, show/hide based on menu state.

    Entering: "transition ease-out duration-100"
      From: "transform opacity-0 scale-95"
      To: "transform opacity-100 scale-100"
    Leaving: "transition ease-in duration-75"
      From: "transform opacity-100 scale-100"
      To: "transform opacity-0 scale-95"
  --> --}}

    @if ($active)
        <div
            {{-- id="table-dropdown-component-{{ $id }}" --}}
            class="absolute right-0 z-10 mt-2 w-max origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
            role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
            <div class="py-1" role="none">
                <!-- Active: "bg-gray-100 text-gray-900", Not Active: "text-gray-700" -->
                {{-- <a href="#" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1"
                    id="menu-item-0">{{ $id }}</a> --}}
                <div class="px-4 py-2">
                    <a
                        href="mailto:{{ $value }}"
                        class="flex flex-row gap-2 text-gray-700 text-sm items-center" role="menuitem" tabindex="-1"
                        id="menu-item-0">
                            <i class="fa-solid fa-envelope"></i>
                            <span class="hover:underline">Send email</span>
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a
                        href="https://teams.microsoft.com/l/chat/0/0?users={{ $value }}"
                        class="flex flex-row gap-2 text-gray-700 text-sm items-center" role="menuitem" tabindex="-1"
                        id="menu-item-1">
                            <svg fill="#000000" class="inline-block" width="14px" height="14px" viewBox="0 0 24 24" role="img" xmlns="http://www.w3.org/2000/svg">
                                <title>Microsoft Teams icon</title>
                                <path
                                    d="M20.625 8.127q-.55 0-1.025-.205-.475-.205-.832-.563-.358-.357-.563-.832Q18 6.053 18 5.502q0-.54.205-1.02t.563-.837q.357-.358.832-.563.474-.205 1.025-.205.54 0 1.02.205t.837.563q.358.357.563.837.205.48.205 1.02 0 .55-.205 1.025-.205.475-.563.832-.357.358-.837.563-.48.205-1.02.205zm0-3.75q-.469 0-.797.328-.328.328-.328.797 0 .469.328.797.328.328.797.328.469 0 .797-.328.328-.328.328-.797 0-.469-.328-.797-.328-.328-.797-.328zM24 10.002v5.578q0 .774-.293 1.46-.293.685-.803 1.194-.51.51-1.195.803-.686.293-1.459.293-.445 0-.908-.105-.463-.106-.85-.329-.293.95-.855 1.729-.563.78-1.319 1.336-.756.557-1.67.861-.914.305-1.898.305-1.148 0-2.162-.398-1.014-.399-1.805-1.102-.79-.703-1.312-1.664t-.674-2.086h-5.8q-.411 0-.704-.293T0 16.881V6.873q0-.41.293-.703t.703-.293h8.59q-.34-.715-.34-1.5 0-.727.275-1.365.276-.639.75-1.114.475-.474 1.114-.75.638-.275 1.365-.275t1.365.275q.639.276 1.114.75.474.475.75 1.114.275.638.275 1.365t-.275 1.365q-.276.639-.75 1.113-.475.475-1.114.75-.638.276-1.365.276-.188 0-.375-.024-.188-.023-.375-.058v1.078h10.875q.469 0 .797.328.328.328.328.797zM12.75 2.373q-.41 0-.78.158-.368.158-.638.434-.27.275-.428.639-.158.363-.158.773 0 .41.158.78.159.368.428.638.27.27.639.428.369.158.779.158.41 0 .773-.158.364-.159.64-.428.274-.27.433-.639.158-.369.158-.779 0-.41-.158-.773-.159-.364-.434-.64-.275-.275-.639-.433-.363-.158-.773-.158zM6.937 9.814h2.25V7.94H2.814v1.875h2.25v6h1.875zm10.313 7.313v-6.75H12v6.504q0 .41-.293.703t-.703.293H8.309q.152.809.556 1.5.405.691.985 1.19.58.497 1.318.779.738.281 1.582.281.926 0 1.746-.352.82-.351 1.436-.966.615-.616.966-1.43.352-.815.352-1.752zm5.25-1.547v-5.203h-3.75v6.855q.305.305.691.452.387.146.809.146.469 0 .879-.176.41-.175.715-.48.304-.305.48-.715t.176-.879Z" />
                            </svg>
                            <span class="hover:underline">Chat in Teams</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

</div>
