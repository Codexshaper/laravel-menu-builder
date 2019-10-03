@extends('menu::layouts.app')

@section('content')
        @include('menu::layouts.sidebar')
        <div class="cx-main-pannel">
              <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
                <div class="container-fluid">
                  <div class="navbar-wrapper">
                    <a class="navbar-brand" href="#pablo">Laravel Menu Builder</a>
                  </div>
                  <button class="navbar-toggler show-sidebar" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="navbar-toggler-icon icon-bar"></span>
                    <span class="navbar-toggler-icon icon-bar"></span>
                    <span class="navbar-toggler-icon icon-bar"></span>
                  </button>
                </div>
            </nav>
              <!-- End Navbar -->
             <div class="cx-main-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <draggable-menu :menu="{{ $menu }}" prefix="{{ menu_prefix() }}"></draggable-menu>
                        </div>
                    </div>
                 </div>
            </div>

       </div>

@endsection
