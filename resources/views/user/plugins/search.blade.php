@if(config('rvsitebuilder/larecipe.search.enabled'))
    @if(config('rvsitebuilder/larecipe.search.default') == 'algolia')
        <algolia-search-box
            v-if="searchBox"
            @close="searchBox = false"
            algolia-key="{{ config('rvsitebuilder/larecipe.search.engines.algolia.key') }}"
            algolia-index="{{ config('rvsitebuilder/larecipe.search.engines.algolia.index') }}"
            version="{{ $currentVersion }}"
        ></algolia-search-box>
    @elseif(config('rvsitebuilder/larecipe.search.default') == 'internal')
        <internal-search-box
            v-if="searchBox"
            @close="searchBox = false"
            version-url=""
            search-url="{{ route('larecipe.search') }}"
            ></internal-search-box>
    @endif 
@endif