@extends('layouts.master')

@section('title')
    Dashboard
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body text-center">
                <br><br>
                <a href="{{ route('transaction.new') }}" class="btn btn-success btn-lg">New Transaction</a>
                <br><br><br>
            </div>
        </div>
    </div>
</div>
@endsection
