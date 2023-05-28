@extends('layouts.admin')
@section('content')
    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        Dashboard
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="row">

                            @foreach ($panels as $panel)
                                <div class="col-lg-3 col-6">
                                    <!-- small box -->
                                    <div class="small-box {{ $panel['theme'] }}">
                                        <div class="inner">
                                            <h3>{{ $panel['value'] }}</h3>

                                            <p>{{ $panel['label'] }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="{{ $panel['icon'] }}"></i>
                                        </div>
                                        <a href="{{ $panel['url'] }}" class="small-box-footer">More info <i
                                                class="fas fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
