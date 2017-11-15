@extends('app')

@section('zendesk_config_name', 'Views')

@section('content')
{!! Session::get('zendesk_views_excel')!!}
@endsection

@section('javascript')
<script>

</script>
@endsection
