
@inject('larecipe', 'Rvsitebuilder\Larecipe\Http\Controllers\Admin\LarecipeController')

<div class="uk-column-1-2 uk-column-divider">
    <label>{{ __('rvsitebuilder/larecipe::default.index.github') }} : </label>
    <div class="">
        <input type="text" class="uk-input uk-form-width-large" name="github" placeholder="https://github.com/rvsitebuilder/developer-docs" value="{{ config('rvsitebuilder/larecipe.github') }}">
    </div>

    <label>{{ __('rvsitebuilder/larecipe::default.index.show-github') }}: </label>
    <span class="rv-tooltip" data-uk-dropdown="{mode:'hover'}" aria-haspopup="true" aria-expanded="false"  style="position:absolute;margin-top:3px; margin-left:3px;">
        <a href="javascript:void(0)"><i class="uk-icon uk-icon-question-circle"></i></a>
        <div class="uk-dropdown uk-panel-box" aria-hidden="true" tabindex="" style="top: 15px; left: 0px;">
            {{ __('rvsitebuilder/larecipe::default.index.github-info-start') }}
            {{ __('rvsitebuilder/larecipe::default.index.document') }}
        </div>
    </span>
    <div class="uk-grid uk-grid-small">
        <div class="uk-width-medium-1-1">
            <label>
                    <input class="uk-radio" type="radio" name='github_show' value="false"
                    {{ config('rvsitebuilder/larecipe.github_show') === false ? 'checked':null }}>
                    {{ __('rvsitebuilder/larecipe::default.index.hidden') }}
            </label>
            <label><input class="uk-radio" type="radio" name='github_show'
                value="true" {{ config('rvsitebuilder/larecipe.github_show') === true ? 'checked':null }}> {{ __('rvsitebuilder/larecipe::default.index.show') }}</label>

        </div>
    </div>

</div>
<hr>
<div class="uk-width-medium-1-2 uk-column-divider">
    <label>{{ __('rvsitebuilder/larecipe::default.index.disqus') }}  : </label>
    <span class="rv-tooltip" data-uk-dropdown="{mode:'hover'}" aria-haspopup="true" aria-expanded="false"  style="position:absolute;margin-top:3px; margin-left:3px;">
        <a href="javascript:void(0)"><i class="uk-icon uk-icon-question-circle"></i></a>
        <div class="uk-dropdown uk-panel-box" aria-hidden="true" tabindex="" style="top: 15px; left: 0px;">
            {{ __('rvsitebuilder/larecipe::default.index.disqus-info-start') }}
            <a href="https://disqus.com/" target="_blank" class="uk-text-warning">{{ __('rvsitebuilder/larecipe::default.index.disqus') }}</a>
            {{ __('rvsitebuilder/larecipe::default.index.disqus-info-end') }}
        </div>
    </span>
    <div class="">
        <input type="text"
            class="uk-input uk-form-width-medium"
            name="forum[services.disqus.site_name]"
            placeholder=""
            value="{{ config('rvsitebuilder/larecipe.forum.services.disqus.site_name') }}">
    </div>
</div>
<hr>
<div class="uk-column-1-2 uk-column-divider">
    <label>{{ __('rvsitebuilder/larecipe::default.index.docs-default-versions') }}</label>
    <div class="">
        <input type="text"
            class="uk-input uk-form-width-medium"
            name="versions[default]"
            placeholder="master"
            value="{{ config('rvsitebuilder/larecipe.versions.default') }}">
    </div>

    <label>{{ __('rvsitebuilder/larecipe::default.index.docs-all-versions') }}</label>
    <div class="">
    <input type="text"
            class="uk-input uk-form-width-medium"
            name="versions[published]"
            placeholder="master"
            value="{{ $larecipe->getConfig()['versions'] }}">
    </div>
</div>
<hr>
<div class="uk-column-1-2 uk-column-divider">
    <label>{{ __('rvsitebuilder/larecipe::default.index.docs-default-lang') }} </label>
    <div class="">
            <input type="text" class="uk-input uk-form-width-medium"  name="languages[default]" placeholder="en" value="{{ config('rvsitebuilder/larecipe.languages.default') }}">
    </div>

    <label>{{ __('rvsitebuilder/larecipe::default.index.docs-all-lang') }}</label>
    <div class="">
        <input type="text"
            class="uk-input uk-form-width-medium"
            name="languages[published]"
            placeholder="en,th,ja"
            value="{{ $larecipe->getConfig()['languages'] }}">
    </div>
</div>