<div>
    Hello World


    <div class="mt-12">
        {{-- @dump($selected) --}}
        @foreach ($selected as $item)
            {{-- @dump($item) --}}
            @dump($item->notes->all())
        @endforeach
    </div>
</div>
