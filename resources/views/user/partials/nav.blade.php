<div class="fixed pin-t pin-x z-40" id="topnav">
    <div class="bg-gradient-primary text-white h-1"></div>

    <nav class="flex items-center justify-between text-black bg-navbar shadow-xs h-16">
        <div class="flex items-center flex-no-shrink">
            <div class="box-switch switch">
                <i class="jswitch switch-checkbox fas fa-bars"></i>
            </div>
            <a href="{{ url('/') }}" class="flex items-center flex-no-shrink text-black margin-l">
                @include("rvsitebuilder/larecipe::user.partials.logo")

                <p class="inline-block font-semibold mx-1 text-grey-dark">
                    {{ config('app.name') }}
                </p>
            </a>
            {{--
            <div class="switch">
                <input type="checkbox" name="1" id="1" v-model="sidebar" class="switch-checkbox" />
                <label class="switch-label" for="1"></label>
            </div>
            --}}
        </div>

        <div class="block mx-4 flex items-center">
            @if(config('rvsitebuilder.larecipe.search.enabled'))
                <larecipe-button id="search-button"
                    :type="searchBox ? 'primary' : 'link'"
                    @click="searchBox = ! searchBox"
                    class="px-4">
                    <i class="fas fa-search" id="search-button-icon"></i>
                </larecipe-button>
            @endif

            <div class="hidden-mobile" up-keep>
                @include('rvsitebuilder/larecipe::user.partials.dropdown')
            </div>

        </div>
    </nav>
</div>