@extends('reveal_modal')

@section('form')
	{!! Form::open(['action' => 'OrdersController@store']) !!}
		{!! Form::hidden('from', '') !!}
		{!! Form::hidden('to', '') !!}
		{!! Form::submit('Confirm', ['class' => 'button success']) !!}
		{!! Html::link('#', 'Cancel', ['class' => 'button alert my-close-reveal-modal']) !!}
	{!! Form::close() !!}
@endsection