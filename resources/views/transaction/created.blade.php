@extends('layouts.master')

@section('title')
    Transaction
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Transaction</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <div class="alert alert-success alert-dismissible">
                    <i class="fa fa-check icon"></i>
                    Transaction successfully created.
                </div>
            </div>
            <div class="box-footer">
                <button class="btn btn-warning btn-flat" onclick="printOrders('{{ route('transaction.print_orders') }}', 'Purchase Orders')">Print Purchase Orders</button>
                <button class="btn btn-warning btn-flat" onclick="printOrdersPDF('{{ route('transaction.print_orders_pdf') }}', 'Payment Note')">Print Payment Note</button>
                <a href="{{ route('transaction.new') }}" class="btn btn-primary btn-flat">New Transaction</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // tambahkan untuk delete cookie innerHeight terlebih dahulu
    document.cookie = "innerHeight=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

    function printOrders(url, title) {
        popupCenter(url, title, 625, 500);
    }

    function printOrdersPDF(url, title) {
        popupCenter(url, title, 900, 675);
    }

    function popupCenter(url, title, w, h) {
        const dualScreenLeft = window.screenLeft !==  undefined ? window.screenLeft : window.screenX;
        const dualScreenTop  = window.screenTop  !==  undefined ? window.screenTop  : window.screenY;

        const width  = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        const systemZoom = width / window.screen.availWidth;
        const left       = (width - w) / 2 / systemZoom + dualScreenLeft
        const top        = (height - h) / 2 / systemZoom + dualScreenTop
        const newWindow  = window.open(url, title,
        `
            scrollbars=yes,
            width  = ${w / systemZoom},
            height = ${h / systemZoom},
            top    = ${top},
            left   = ${left}
        `
        );

        if (window.focus) newWindow.focus();
    }
</script>
@endpush
