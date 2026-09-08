@extends('lp.layouts.app')
@section('head')
<link rel="stylesheet" href="{{ asset('lp/css/validationEngine.jquery.css') }}">
@endsection
@section('content')

<section class="contact01">

    <div class="inner">

        <h2 class="mainTit">
            お問い合わせ
        </h2>

        @include('contact.form')

    </div>

</section>

@endsection