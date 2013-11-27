@extends('layouts.admin')

@section('content')

    <div class='row'>
        <div class="col-sm-7">
            <h3>Manage your groups <small>and permissions</small></h3>
        </div>
        <div class="col-sm-5 text-right">
            @if($current_user->hasAccess('admin.group.create'))
                <a href='' class='btn btn-primary pull-right margin-left' data-toggle="modal" data-target="#addGroup"><i class='fa fa-plus'></i> Add</a>
            @endif
        </div>
    </div>

    <div class="col-sm-12">

        <br/>

        @if($error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endif

        @foreach($groups as $group)

            <div class="panel dataset dataset-link button-row panel-default">
                <div class="panel-body">
                    <div class='icon'>
                        <i class='fa fa-group'></i>
                    </div>
                    <div>
                        <div class='row'>
                            <div class='col-sm-6'>
                                <h4 class='dataset-title'>
                                    <a href='{{ URL::to('api/admin/groups/edit/' . $group->id) }}'>{{ $group->name }}</a>
                                </h4>
                            </div>
                            <div class='col-sm-2'>
                                <strong>Users:</strong>
                                @foreach($users as $user)
                                    @if($user->inGroup($group))
                                        {{ $user->email }}
                                    @endif
                                @endforeach
                            </div>
                            <div class='col-sm-4 text-right'>
                                @if($current_user->hasAccess('admin.group.update') && $group->id > 2)
                                    <a href='#' class='btn edit-user' title='Edit this user'><i class='fa fa-edit'></i> Edit</a>
                                @endif
                                @if($current_user->hasAccess('admin.group.delete') && $group->id > 2)
                                    <a href='{{ URL::to('api/admin/groups/delete/'. $group->id) }}' class='btn delete' title='Delete this user'><i class='fa fa-times icon-only'></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach

        <br/>

        <a href='#' class='hover-help pull-right' data-toggle="tooltip" data-placement='left' title='User have a username and a password used to authenticate on the DataTank, the permissions they have are determined by the group they are in.'>
            <i class='fa fa-lg fa-question-circle'></i>
        </a>
    </div>


    <div id='addGroup' class="modal fade">
        <div class="modal-dialog">
            <form action='{{ URL::to('api/admin/groups/create') }}' method='post' class="form-horizontal" role="form">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Add a group</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputName" name='name' placeholder="Groupname" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id='editUser' class="modal fade">
        <div class="modal-dialog">
            <form action='{{ URL::to('api/admin/groups/update') }}' method='post' class="form-horizontal" role="form">

                <input type="hidden" class="form-control" id="inputEditId" name='id'>

                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Edit a user</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="inputEditName" class="col-sm-2 control-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputEditName" name='name' placeholder="Username" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEditPassword" class="col-sm-2 control-label">Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="inputEditPassword" name='password' placeholder="Password" autocomplete="off">
                                <span class='help-block'>Leave blank if you want to keep the current password</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEditGroup" class="col-sm-2 control-label">Group</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="inputEditGroup" name='group'>
                                    @foreach($groups as $group)
                                        <option value='{{ $group->id }}'>{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@stop