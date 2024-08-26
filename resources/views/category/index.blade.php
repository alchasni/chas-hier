@extends('layouts.master')

@section('title')
    Product Category
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Category</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <button onclick="createOne('{{ route('category.store') }}')" class="btn btn-success btn-sm btn-flat"><i class="fa fa-plus-circle"></i></button>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-stiped table-bordered">
                        <thead>
                        <th>Name</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @includeIf('category.form')
@endsection
@push('scripts')
    <script>
        let table;

        $(function () {
            table = $('.table').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('category.data') }}',
                },
                columns: [
                    {data: 'name'},
                    {data: 'action', searchable: false, sortable: false},
                ]
            });

            handleFormSubmit();
        });

        function createOne(url) {
            openModal('#modal-form', 'Create Category', '#modal-form form', url);
        }

        function updateOne(url) {
            openModal('#modal-form', 'Edit Category', '#modal-form form', url, 'put');

            $.get(url)
                .done((response) => {
                    $('#modal-form [name=name]').val(response.name);
                })
                .fail((errors) => {
                    showToast('error', errors?.responseText || 'Failed to load data');
                });
        }

        function deleteOne(url, name) {
            showConfirmToast(`Are you sure you want to delete "${name}"?`, url, 'Successfully deleted the data', 'Failed to delete data')
        }
    </script>
@endpush
