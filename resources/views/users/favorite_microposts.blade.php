
@extends('layouts.app')

@section('content')
<ul class="media-list">
    @foreach ($microposts as $micropost)
        <?php $user = $micropost->user; ?>
        <li class="media">
            <div class="media-left">
                <img class="media-object img-rounded" src="{{ Gravatar::src($user->email, 50) }}" alt="">
            </div>
            <div class="media-body">
                <div>
                    {!! link_to_route('users.show', $user->name, ['id' => $user->id]) !!} <span class="text-muted">posted at {{ $micropost->created_at }}</span>
                </div>
                <div>
                    <p>{!! nl2br(e($micropost->content)) !!}</p>
                </div>
                <div>
                    @if (Auth::user()->liked($micropost->id))
                    {!! Form::open(['route' => ['micropost.unlike', $micropost->id], 'method' => 'delete']) !!}
                        {!! Form::submit('unlike', ['class' => 'btn btn-warning btn-xs']) !!}
                    {!! Form::close() !!}
                    @else
                    {!! Form::open(['route' => ['micropost.like', $micropost->id]]) !!}
                        {!! Form::submit('like', ['class' => 'btn btn-primary btn-xs']) !!}
                    {!! Form::close() !!}
                    
                    @endif
                </div>
            </div>
        </li>
    @endforeach
</ul>

{!! $microposts->render() !!}
@endsection