@extends('layouts.app')
@section('content')
<!-- The Modal -->
<div class="modal" id="newAccountModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Add account</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form class="user" action="/accounts/new" method="POST" autocomplete="none" aria-autocomplete="none">
                    {!! csrf_field() !!}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input name="email" autocomplete="none" placeholder="Email" class="form-control form-control-user" type="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <input autocomplete="new-password" name="password" class="form-control form-control-user" placeholder="Password" type="password">
                        </div>
                        <div class="col-md-6 mb-5">
                            <select style="height:50px; padding:0; padding-left:10px;" name="platform" placeholder="Platform" class="form-control form-control-user">
                                <option value="" disabled selected>Platform</option>
                                <option value="xbox">Xbox</option>
                                <option value="ps">Playstation</option>
                                <option value="pc">PC</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-5">
                            <input name="backup_codes" class="form-control form-control-user" placeholder="Backup Codes" type="text">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 mt-3">
                            <button class="btn btn-primary btn-block text-white btn-user" type="submit">Add</button>
                        </div>
                    </div>
                </form>  
            </div>
        </div>
    </div>
</div>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h3 class="text-dark mb-0">Accounts</h3>
            <button data-toggle="modal" data-target="#newAccountModal" class="btn btn-primary btn-icon-split" type="button">
                <span class="text-white-50 icon"><i class="far fa-plus-square"></i></span>
                <span class="text-white text">Add account</span>
            </button>
        </div>
        <div class="row">
            @if(count($accounts) == 0)
            <div class="col">
                <p>No accounts in the database.</p>
            </div>
            @else
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-3">
                                    <p class="text-primary font-weight-bold"><i class="fa fa-envelope"></i> Email</p>
                                </div>
                                <div class="col-3">
                                    <p class="text-primary font-weight-bold"><i class="fa fa-desktop"></i> Platform</p>
                                </div>
                                <div class="col-3">
                                    <p class="text-primary font-weight-bold"><i class="fa fa-info-circle"></i> Status</p>
                                </div>
                                <div class="col-3">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pb-0 pt-1">
                            @foreach($accounts as $account)
                            <div class="row mt-3">
                                <div class="col-3">
                                    <p class="ml-2">{{$account->email}}</p>
                                </div>
                                <div class="col-3">
                                     <p>{{$account->platform}}</p>
                                </div>
                                <div class="col-3">
                                    @if($account->status == 0)
                                        <p><span class="badge badge-pill badge-warning">Stopped</span></p>
                                    @elseif($account->status == 1)
                                        <p><span class="badge badge-pill badge-primary">Sniping</span></p>
                                    @elseif($account->status == 2)
                                        <p><span class="badge badge-pill badge-success">Ready</span></p>
                                    @elseif($account->status == 3)
                                        <p><span class="badge badge-pill badge-secondary">Cooldown</span></p>
                                    @else
                                        <p><span class="badge badge-pill badge-danger">Error</span></p>
                                    @endif
                                </div>
                                <div class="col-3">
                                    <a href="/accounts/{{$account->id}}/refresh" class="btn btn-sm btn-primary text-white mb-3"> Refresh session</a> &nbsp;
                                    <a href="/accounts/{{$account->id}}/stop" class="btn btn-sm btn-danger text-white mb-3"> Stop</a>
                                </div>
                            </div>
                            <hr style="background-color:#ECEDF0; height:1px; border:0; width:100%;" class="mt-0">
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div> 
@endsection