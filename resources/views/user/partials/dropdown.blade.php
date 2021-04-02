            {{-- Language dropdown --}}
            <larecipe-dropdown class="marl"> {{-- TODO: .marl only on mobile --}}
                <larecipe-button type="primary" class="flex ml-2 capitalize" id="currentLang" style="background:#5ab9b1;">{{-- TODO: .ml-2 only on desktop --}}
                @if(Lang::has('menus.language-picker.img.'.$currentLang))
                    @lang('menus.language-picker.img.'.$currentLang)
                    &nbsp; <small> @lang('menus.language-picker.langs.'.$currentLang) </small>
                    <i class="mx-1 fa fa-angle-down"></i>
                @else
                    @lang('menus.language-picker.img.default')
                    &nbsp; <small> {{ $currentLang }} </small>
                    <i class="mx-1 fa fa-angle-down"></i>
                @endif
                </larecipe-button>

                <template slot="list">
                    <ul class="list-reset"  >
                        @foreach(config('rvsitebuilder.larecipe.languages.published') as $lang)
                            <li class="py-2 hover:bg-grey-lightest">
                                <a href="{{ route('larecipe.show', ['version' => $currentVersion, 'page' => $lang.$page]) }}"
                                    up-lang="{{ $lang }}"
                                    class="dropdown-item px-6 text-grey-darkest">
                                    @if(Lang::has('menus.language-picker.img.'.$lang))
                                        @lang('menus.language-picker.img.'.$lang)
                                        &nbsp; <small> @lang('menus.language-picker.langs.'.$lang) </small>
                                    @else
                                        @lang('menus.language-picker.img.default')
                                        &nbsp; <small> {{ $lang }} </small>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </template>
            </larecipe-dropdown>

            {{-- Github button --}}
            @if( config('rvsitebuilder.larecipe.github_show') === true)
            <larecipe-button tag="a" href="{{ config('rvsitebuilder.larecipe.github') }}" target="__blank" type="black" class="mx-2 px-4 mar-icon">{{-- TODO: .mar-icon only on desktop --}}
                <i class="fab fa-github"></i>
            </larecipe-button>
            @endif

            {{-- versions dropdown --}}
            <larecipe-dropdown  class="marl">
                <larecipe-button type="primary" class="flex ml-2 capitalize" id="currentVersion"> {{-- TODO: .ml-2 only on desktop --}}
                    {{ $currentVersion }} <i class="mx-1 fa fa-angle-down"></i>
                </larecipe-button>

                <template slot="list">
                    <ul class="list-reset">
                        @foreach ($versions as $version)
                            <li class="py-2 hover:bg-grey-lightest">
                                <a href="{{ route('larecipe.show', ['version' => $version, 'page' => $currentSection]) }}"
                                up-version="{{ $version }}"
                                class="px-6 text-grey-darkest">
                                {{ ucfirst($version) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </template>
            </larecipe-dropdown>

            @auth
                {{-- account --}}
                <larecipe-dropdown>
                    <larecipe-button type="white" class="ml-2">
                        {{ auth()->user()->name ?? 'Account' }} <i class="fa fa-angle-down"></i>
                    </larecipe-button>

                    <template slot="list">
                        <a href="{{ route('user.auth.logout') }}">
                        <button type="submit" class="py-2 px-4 text-white bg-danger inline-flex"><i class="fa fa-power-off mr-2"></i> Logout</button>
                        </a>
                    </template>
                </larecipe-dropdown>
                {{-- /account --}}
            @endauth