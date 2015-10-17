@extends('reveal_modal')

@section('form')
	{!! Form::open(['action' => 'OrdersController@destroy']) !!}
		{!! Form::hidden('_method', 'DELETE') !!}
		{!! Form::hidden('from', '') !!}
		{!! Form::hidden('to', '') !!}
		{!! Form::hidden('location', '') !!}
		{!! Form::submit('Confirm', ['class' => 'button success']) !!}
		{!! Html::link('#', 'Cancel', ['class' => 'button alert my-close-reveal-modal']) !!}
	{!! Form::close() !!}
@endsection