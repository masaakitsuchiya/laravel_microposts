@extends('layouts.app')

@section('content')
    @if(Auth::check())
        <div class="row">
            <aside class="col-md-4">
                {!! Form::open(['route'=> 'microposts.store']) !!}
                <div class="form-group">
                    {!! Form::textarea('content', old('content'), ['class' => 'form-control', 'rows' => '5']) !!}
                </div>
                {!! Form::submit('Post', ['class' => 'btn btn-primary btn-block']) !!}
                {!! Form::close() !!}
                <p>
                {!! link_to_route('favorite_microposts', 'Liked Posts', null, ['class' => 'btn btn-info btn-block']) !!}
                </p>
            </aside>
            <div class="col-md-8">
                @if (count($microposts) > 0)
                    @include('microposts.microposts', ['microposts' => $microposts])
                @endif
            </div>
        </div>
    @else
        <div class="center jumbotron">
            <div class="text-center">
                <h1>Welcome to the Microposts</h1>
                {!! link_to_route('signup.get', 'Sign up now!', null, ['class' => 'btn btn-lg btn-primary']) !!}
            </div>
        </div>
    @endif
@endsection