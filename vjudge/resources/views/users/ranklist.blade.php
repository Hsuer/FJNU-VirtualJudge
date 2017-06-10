@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-bordered table-hover" id="users-table" width="100%">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Name</th>
                                <th width="15%">Nick</th>
                                <th width="50%">Description</th>
                                <th width="10%">Solved</th>
                                <th width="10%">AC Ratio</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    var table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 50,
        order: [[ 4, 'desc' ]],
        ajax: '{!! route('user.list') !!}',
        fnDrawCallback: function(){
            this.api().column(0).nodes().each(function(cell, i) {
                cell.innerHTML =  i + 1;
            });
        },
        columns: [
            { 
                data: 'id',
                orderable: false,
                searchable: false,
            },
            { 
                data: 'name',
                orderable: false,
                render: function ( data, type, row ) {
                    return '<a href="{{url('user')}}/' + row['id'] + '">' + data + '</a>';
                }
            },
            { 
                data: 'nick',
                orderable: false
            },
            { 
                data: 'description',
                orderable: false
            },
            { data: 'solve' },
            { 
                data: 'submit',
                orderable: false,
                render: function ( data, type, row ) {
                    return row['ac'] + '/' + data;
                }
            }
        ]
    });
});

</script>

@endsection
