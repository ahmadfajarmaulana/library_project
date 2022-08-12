@extends('layouts.admin')

@section('header', 'Transaction')

@section('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
@endsection

@section('content')

<div id="controller">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-7">
                    <a href="{{url('transaction/create')}}" class="btn btn-sm btn-primary pull-right"><i
                            class="fas fa-plus fa-sm text-white-50"></i> Create New Author</a>
                </div>
                <div class="col-md-2">
                    <select id="status" class="form-control" name="status">
                        <option value="">Filter Status</option>
                        <option value="1">Sudah Kembali</option>
                        <option value="2">Belum Kembali</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group date">
                        <input type="text" class="form-control" id="datepicker" name="date_start"
                            placeholder="Filter tanggal">
                        <div class="input-group-append">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive-lg">
                    <table id="datatable" class="table table-bordered table-striped table-responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Nama Peminjam</th>
                                <th>Lama Pinjam (hari)</th>
                                <th>Total Buku</th>
                                <th>Total bayar</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>

<script type="text/javascript">
    var actionUrl = '{{ url('transaction') }}';
    var apiUrl = '{{ url('api/transaction') }}';
    var columns = [
        {data: 'DT_RowIndex', class: 'text-center', orderable: true},
        {data: 'date_start', class: 'text-center', orderable: true},
        {data: 'date_end', class: 'text-center', orderable: true},
        {data: 'member.name', class: 'text-center', orderable: true},
        {data: 'limit', class: 'text-center', orderable: true},
        {data: 'details.length', class: 'text-center', orderable: false},
        {data: 'purches', class: 'text-center', orderable: true},
        {data: 'status', class: 'text-center', orderable: true},
        // {data: 'date', class: 'text-center', orderable: true},//format DD-MM-YY hh:mm
        {render: function(index, row, data, meta){
            return `
                <a href="${actionUrl}/${data.id}" class="btn btn-info btn-sm" onclick="controller.editData(event,${meta.row})">
                    <i class="fa fa-eye"></i>
                </a>
                <a href="${actionUrl}/${data.id}/edit" class="btn btn-warning btn-sm" onclick="controller.editData(event,${meta.row})">
                    <i class="fa fa-pencil-alt"></i>
                </a>
                <a href="#" class="btn btn-danger btn-sm" onclick="controller.deleteData(event,${data.id})">
                    <i class="fa fa-trash"></i>
                </a>`;
        },orderable: false, width:"100px", class:"text-center"},
    ];

    
    var controller = new Vue({
        el: '#controller',
        data: {
            datas: [], 
            data: {},   
            actionUrl,
            apiUrl, //mengambil variable berisi Api
            
        },
        mounted: function() {
            this.datatable();
        },
        methods: {
            datatable() {
                const _this = this;
                _this.table = $('#datatable').DataTable({
                    ajax: {
                        url: _this.apiUrl,
                        type: 'GET',
                    },
                    columns: columns
                }).on('xhr', function(){
                    _this.datas = _this.table.ajax.json().data;
                });
            },
            addData() {
                this.data = {};
                this.editStatus= false;
                $('#modal-default').modal();
            },
            editData(event, row) {
                this.data = this.datas[row];
                this.editStatus= true;
                $('#modal-default').modal();
            },
            deleteData(event, id) {
                
                if (confirm("Are You Sure ??")){
                    $(event.target).parents('tr').remove();
                    axios.post(this.actionUrl+'/'+id, {_method: 'Delete'}).then(response =>{
                        alert('Data Has been removed');
                    })
                }
            },
            submitForm(event, id) {
                event.preventDefault();
                const _this = this;
                var actionUrl = ! this.editStatus ? this.actionUrl : this.actionUrl+'/'+id;
                axios.post(actionUrl, new FormData($(event.target)[0])).then(response => {
                    $('#modal-default').modal('hide');
                    _this.table.ajax.reload();
                });
            }
        }
    });
</script>
<script type="text/javascript">
    $( "#status" ).on('change', function(){
            status = $('#status').val();
            return  controller.table.ajax.url(apiUrl+'?status='+status).load();
        });

</script>

<script type="text/javascript">
    $( function() {
        $( "#datepicker" ).datepicker({dateFormat : 'yy-mm-dd'}).on('change', function(){
            filter = $('#datepicker').val();
            return  controller.table.ajax.url(apiUrl+'?filter='+filter).load();
        });
    } );
</script>

@endsection