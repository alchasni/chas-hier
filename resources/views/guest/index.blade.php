@extends('layouts.master')

@section('title')
    Member
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Member</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <button onclick="createOne('{{ route('guest.store') }}')" class="btn btn-success btn-sm btn-flat"><i class="fa fa-plus-circle"></i></button>
                </div>
                <div class="box-body table-responsive">
                    <form action="" method="post" class="form-guest">
                        @csrf
                        <table class="table table-striped table-bordered">
                            <thead>
                            <th width="5%">
                                <input type="checkbox" name="select_all" id="select_all">
                            </th>
                            <th>Member Code</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th><i class="fa fa-cog"></i></th>
                            </thead>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @includeIf('guest.form')
@endsection

@push('scripts')
    <script>
        let table;

        $(function () {
            table = $('.table').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('guest.data') }}',
                },
                columns: [
                    {data: 'select_all', searchable: false, sortable: false},
                    {data: 'member_code'},
                    {data: 'name'},
                    {data: 'phone_number'},
                    {data: 'address'},
                    {data: 'action', searchable: false, sortable: false},
                ]
            });

            handleFormSubmit();
            selectAllCheckboxes('[name=select_all]', ':checkbox');
        });

        function createOne(url) {
            openModal('#modal-form', 'Create Member', '#modal-form form', url);
        }

        function updateOne(url) {
            openModal('#modal-form', 'Edit Member', '#modal-form form', url, 'put');

            $.get(url)
                .done((response) => {
                    $('#modal-form [name=name]').val(response.name);
                    $('#modal-form [name=phone_number]').val(response.phone_number);
                    $('#modal-form [name=address]').val(response.address);
                })
                .fail(() => {
                    showToast('error', errors.responseText || 'Failed to load data');
                });
        }

        function deleteOne(url, name) {
            showConfirmToast(`Are you sure you want to delete "${name}"?`, url, 'Successfully deleted the data', 'Failed to delete data')
        }
    </script>
@endpush
