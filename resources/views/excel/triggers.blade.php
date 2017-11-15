@extends('app')

@section('zendesk_config_name', 'Triggers')

@section('content')
   Triggers

   @if (\Session::has('triggers'))
      {{ \Session::get('triggers') }}
   @endif
@endsection
