@extends('layouts.master')

@section('title')
    Product
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Product</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    @if (auth()->user()->level == 1)
                        <button onclick="createOne('{{ route('product.store') }}')" class="btn btn-success btn-sm btn-flat"><i class="fa fa-plus-circle"></i></button>
                        <button onclick="printBarcode('{{ route('product.print_barcode') }}')" class="btn btn-info btn-sm btn-flat">
                            <i class="fa fa-barcode"></i>
                        </button>
                    @endif
                </div>
                <div class="box-body table-responsive">
                    <form action="" method="post" class="form-product">
                        @csrf
                        <table class="table table-striped table-bordered">
                            <thead>
                            <th width="5%"><input type="checkbox" name="select_all" id="select_all"></th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Buy price</th>
                            <th>Sell price</th>
                            <th>Stock</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                            </thead>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @includeIf('product.form')
@endsection

@push('scripts')
    <script>
        let table;

        $(function () {
            table = $('.table').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('product.data') }}',
                },
                columns: [
                    {data: 'select_all', searchable: false, sortable: false},
                    {data: 'name'},
                    {data: 'category_name'},
                    {data: 'buy_price', searchable: false},
                    {data: 'sell_price', searchable: false},
                    {data: 'stock', searchable: false},
                    {data: 'action', searchable: false, sortable: false},
                ]
            });

            handleFormSubmit();
            selectAllCheckboxes('[name=select_all]', ':checkbox');
        });

        function createOne(url) {
            openModal('#modal-form', 'Create Product', '#modal-form form', url);
        }

        function updateOne(url) {
            openModal('#modal-form', 'Edit Product', '#modal-form form', url, 'put');

            $.get(url)
                .done((response) => {
                    $('#modal-form [name=name]').val(response.name);
                    $('#modal-form [name=category_id]').val(response.category_id);
                    $('#modal-form [name=buy_price]').val(response.buy_price);
                    $('#modal-form [name=sell_price]').val(response.sell_price);
                    $('#modal-form [name=stock]').val(response.stock);
                })
                .fail((errors) => {
                    showToast('error', errors?.responseText || 'Failed to load data');
                });
        }

        function deleteOne(url, name) {
            showConfirmToast(`Are you sure you want to delete "${name}"?`, url, 'Successfully deleted the data', 'Failed to delete data')
        }

        function updateStock(url) {
            openModal('#modal-form', 'Update Stock', '#modal-form form', url, 'put');

            $.get(url)
                .done((response) => {
                    $('#modal-form [name=name]').val(response.name).prop('readonly', true);
                    $('#modal-form [name=category_id]').val(response.category_id);
                    $('#modal-form [name=buy_price]').val(response.buy_price);
                    $('#modal-form [name=sell_price]').val(response.sell_price);

                    $('#modal-form [name=category_id]').closest('.form-group').hide();
                    $('#modal-form [name=buy_price]').closest('.form-group').hide();
                    $('#modal-form [name=sell_price]').closest('.form-group').hide();

                    $('#modal-form [name=stock]').val(response.stock);
                })
                .fail((errors) => {
                    showToast('error', errors?.responseText || 'Failed to load data');
                });
        }

        function printBarcode(url) {
            if ($('input:checked').length < 1) {
                showToast('info', 'Select the product to print');
            } else if ($('input:checked').length < 3) {
                showToast('info', 'Select at least 3 to print');
            } else {
                $('.form-product')
                    .attr('target', '_blank')
                    .attr('action', url)
                    .submit();
            }
        }
    </script>
@endpush
