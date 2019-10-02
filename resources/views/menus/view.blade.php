@extends('menu::layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        You are logged in!
                    </div>

{{--                    {{ var_dump( $menus )  }}--}}



{{--                    <draggable-menu :lists="{{ $menus  }}"></draggable-menu>--}}

                    <div class="dd" id="nestable">
                        <ol class="dd-list">
                            <li class="dd-item" data-id="1">
                                <div class="dd-handle">Item 1</div>
                            </li>
                            <li class="dd-item" data-id="2">
                                <div class="dd-handle">Item 2</div>
                                <ol class="dd-list">
                                    <li class="dd-item" data-id="3"><div class="dd-handle">Item 3</div></li>
                                    <li class="dd-item" data-id="4"><div class="dd-handle">Item 4</div></li>
                                    <li class="dd-item" data-id="5">
                                        <div class="dd-handle">Item 5</div>
                                        <ol class="dd-list">
                                            <li class="dd-item" data-id="6"><div class="dd-handle">Item 6</div></li>
                                            <li class="dd-item" data-id="7"><div class="dd-handle">Item 7</div></li>
                                            <li class="dd-item" data-id="8"><div class="dd-handle">Item 8</div></li>
                                        </ol>
                                    </li>
                                    <li class="dd-item" data-id="9"><div class="dd-handle">Item 9</div></li>
                                    <li class="dd-item" data-id="10"><div class="dd-handle">Item 10</div></li>
                                </ol>
                            </li>
                            <li class="dd-item" data-id="11">
                                <div class="dd-handle">Item 11</div>
                            </li>
                            <li class="dd-item" data-id="12">
                                <div class="dd-handle">Item 12</div>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
