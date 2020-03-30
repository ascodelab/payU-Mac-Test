@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.css"/>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if (\Session::has('success'))
                <div class="alert alert-success">
                    <ul>
                        <li>{!! \Session::get('success') !!}</li>
                    </ul>
                </div>
            @endif
            @if (\Session::has('error'))
                <div class="alert alert-warning">
                    <ul>
                        <li>{!! \Session::get('error') !!}</li>
                    </ul>
                </div>
            @endif
            <ul class="nav nav-tabs">
              <li class=" active col-md-6 text-center"><a data-toggle="tab" href="#create-code">Create Code</a></li>
              <li class="col-md-6 text-center"><a data-toggle="tab" href="#search-code">Search Code</a></li>
            </ul>

            <div class="tab-content">
                <div id="create-code" class="tab-pane fade in active">
                    <div class="col-md-12">
                        <label>
                            <img src="{{ asset('images/plus--v2.png') }}" class="create-class">
                            <h4 class="create-class-text">Create Code</h4>
                        </label> 
                    </div>                       
                    <div class="col-md-12 create-code">
                        <div class="col-md-7">
                            <form method="post" id="createCodeForm" action="{{ route('createCode') }}">
                                <input type="hidden" name="_token" value="{{csrf_token()}}">
                                <div class="form-group">
                                    <select class="form-control" id="state" name="state_id">
                                        <option value="null">State *</option>
                                        @foreach($stateList as $key => $value )
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <div class="state_id error-message"></div>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" id="a_type" name="a_type">
                                        <option value="null">A Type(C/S)*</option>
                                        <option value="C">C</option>
                                        <option value="S">S</option>
                                    </select>
                                    <div class="a_type error-message"></div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="refNo" placeholder="Ref No." name="ref_no">
                                    <div class="ref_no error-message"></div>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" id="d_type" name="d_type">
                                        <option value="null">D Type(G/P/U)*</option>
                                        <option value="G">G</option>
                                        <option value="P">P</option>
                                        <option value="U">U</option>
                                    </select>
                                    <div class="d_type error-message"></div>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary createCode">Submit</button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-secondary cancel">Cancel</button>
                                </div>
                            </form>
                            <div class="success_message"></div>    
                        </div>
                        <div class="col-md-5">
                            
                        </div>
                    </div>
                </div>
                <div id="search-code" class="tab-pane fade">
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="fromDate" placeholder="From Date" name="fromDate" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="toDate" placeholder="To Date" name="toDate" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">    
                                <button type="button" class="btn btn-primary searchCode">Search</button>
                            </div>
                            <div class="col-md-3 export" style="color: blue;cursor: pointer;text-decoration: underline;"></div>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-bordered dataTable">
                                <thead>
                                    <tr>
                                        <th>State</th>
                                        <th>A Type</th>
                                        <th>D Type</th>
                                        <th>Code</th>
                                        <th>Ref No.</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($CodeData as $value )
                                        <tr class="data-row">
                                            <td>{{ $value->stateName->state_name }}</td>
                                            <td>{{ $value->a_type }}</td>
                                            <td>{{ $value->d_type }}</td>
                                            <td>{{ $value->user_code }}</td>
                                            <td class="ref_no">{{ $value->ref_no }}</td>
                                            <td>{{ $value->created_at }}</td>
                                            <td><span style="cursor: pointer;color: blue;text-decoration: underline;" id="edit-item" data-item-id="{{ $value->id }}">Edit</span>/<a href="{{ route('deleteCode', ['id' => $value->id]) }}">Delete</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="black_overlay">
        <img class="logo_loader" src="{{ asset('images/images.png') }}">
    </div>
</div>


<!-- Attachment Modal -->
<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content col-md-6">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-modal-label">Edit Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="attachment-body-content">
                <form id="edit-form" class="form-horizontal" method="POST" action="{{ route('editCode') }}">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <div class="card text-white">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="col-form-label" for="modal-input-ref_no">Ref No</label>
                                <input type="text" name="ref_no" class="form-control" id="modal-input-ref_no" required autofocus>
                            </div>
                            <input type="hidden" name="code_id" id="codeId" />
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Done</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </form>
            </div>
            
                
            
        </div>
    </div>
</div>

@endsection