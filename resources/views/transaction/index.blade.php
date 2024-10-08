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
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-transaction">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Member Code</th>
                        <th>Total Item</th>
                        <th>Total Harga</th>
                        <th>Total Bayar</th>
                        <th>Kasir</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('transaction.detail')
@endsection

@push('scripts')
<script>
    let table, table1;

    $(function () {
        table = $('.table-transaction').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('transaction.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'date'},
                {data: 'member_code'},
                {data: 'total_item_quantity'},
                {data: 'total_price'},
                {data: 'money_received'},
                {data: 'user_name'},
                {data: 'action', searchable: false, sortable: false},
            ]
        });

        table1 = $('.table-detail').DataTable({
            processing: true,
            bSort: false,
            dom: 'Brt',
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'code'},
                {data: 'name'},
                {data: 'sell_price'},
                {data: 'quantity'},
                {data: 'price'},
            ]
        })
    });

    function showDetail(url) {
        $('#modal-detail').modal('show');

        table1.ajax.url(url);
        table1.ajax.reload();
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }
</script>
@endpush
