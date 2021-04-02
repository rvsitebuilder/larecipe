@extends('rvsitebuilder/larecipe::admin.layouts.app')
@section('content')

    <h2> {{ __('rvsitebuilder/larecipe::default.app.name') }}</h2>

    <div class="uk-margin-left">
        <p>{{ __('rvsitebuilder/larecipe::default.app.description') }}</p>
        <div>

            <div class="row rv-larecipe">

                <div class="col-lg-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <blockquote class="blockquote">
                                <h2>{{ __('rvsitebuilder/larecipe::default.index.update-docs') }}</h2>
                                <footer class="blockquote-footer">{{ route('admin.larecipe.docs.install') }}</footer>
                            </blockquote>
                            <div class="update-area">
                                @if(!empty(config('rvsitebuilder.larecipe.github')))
                                    <button type="button"
                                        class="btn btn-primary update-doc">{{ __('rvsitebuilder/larecipe::default.index.btn-update') }}</button>
                                @else
                                    <button type="button"
                                        class="btn btn-primary update-doc disable-larecipe">{{ __('rvsitebuilder/larecipe::default.index.btn-update') }}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <blockquote class="blockquote">
                                <h2>{{ __('rvsitebuilder/larecipe::default.index.docs') }}</h2>
                                <footer class="blockquote-footer">
                                    @if(!empty(config('rvsitebuilder.larecipe.github'))) {{ route('larecipe.index') }}
                                    @endif
                                </footer>
                            </blockquote>
                            @if($openDocs === true && $warning === false)
                                <a href="{{ route('larecipe.index') }}" class="btn btn-primary"
                                    target="_blank">{{ __('rvsitebuilder/larecipe::default.index.btn-docs') }}</a>
                            @else
                                <a href="{{ route('larecipe.index') }}" class="btn btn-primary disable-larecipe"
                                    target="_blank">{{ __('rvsitebuilder/larecipe::default.index.btn-docs') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


        @endsection

        @push('package-scripts')
            {!! script('vendor/rvsitebuilder/weather/js/admin/settingapikey.js') !!}
            <script>
                @if($warning === true && $openDocs === true && !empty(config('rvsitebuilder.larecipe.github')))
                    console.pop.error({
                        title: 'Error',
                        text: `{{ __('rvsitebuilder/larecipe::default.index.warning-file-index') }}`
                    });

                @endif

                @empty(config('rvsitebuilder.larecipe.github'))
                    console.pop.notice({
                        title: 'Warning',
                        text: `{{ __('rvsitebuilder/larecipe::default.index.warning-config') }}`
                    });
                @endempty

                $('.update-doc').click(oThis => {

                    oThis.currentTarget.classList.add('disabled')
                    var iconLoading =
                        `<img class="loading" alt="" src="${wex.url.WYS_IMG_URL}/images/loading08.gif" width="20" height="20" border="0" align="absmiddle" />`
                    $('.update-area').append(iconLoading);

                    $.ajax({
                        type: "POST",
                        url: parent.wex.url.baseUrl + '/admin/larecipe/docs/install',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(responseText) {
                            oThis.currentTarget.classList.remove('disabled')
                            console.pop.success({
                                text: responseText.message
                            });

                            $('.loading').remove();
                            location.reload(true);
                        },
                        error: function(xhr, thrownError) {
                            console.pop.error({
                                text: xhr.responseJSON.message
                            });
                            oThis.currentTarget.classList.remove('disabled')
                            $('.loading').remove();

                        },
                    });
                });

            </script>
        @endpush

        @push('package-styles')
            <style>
                .disable-larecipe {
                    pointer-events: none;
                    opacity: 0.3;
                }

            </style>

        @endpush
