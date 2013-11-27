@extends('layouts.admin')

@section('content')

    <div class='row'>
        <div class="col-sm-7">
            <h3>Manage your users</h3>
        </div>
        <div class="col-sm-5 text-right">
            @if($current_user->hasAccess('admin.user.create'))
                <a href='' class='btn btn-primary pull-right margin-left' data-toggle="modal" data-target="#addUser"><i class='fa fa-plus'></i> Add</a>
            @endif
        </div>
    </div>

    <div class="col-sm-12">

        <br/>

        @if($error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endif

        @foreach($users as $user)
            <?php
                $user_group = '1';
                foreach($user->getGroups() as $group){
                    $user_group = $group->id;
                    break;
                }
            ?>

            <div class="panel dataset dataset-link button-row panel-default"
                        data-id='{{ $user->id }}'
                        data-name='{{ $user->email }}'
                        data-group='{{ $user_group }}'>
                <div class="panel-body">
                    <div class='icon'>
                        <i class='fa fa-male'></i>
                    </div>
                    <div>
                        <div class='row'>
                            <div class='col-sm-6'>
                                <h4 class='dataset-title'>
                                    @if($current_user->email == $user->email)
                                        <i class='fa fa-angle-right'></i>
                                    @endif
                                    <a href='{{ URL::to('api/admin/users/edit/' . $user->id) }}'>{{ $user->email }}</a>
                                </h4>
                            </div>
                            <div class='col-sm-2'>
                                <strong>Group:</strong>
                                @foreach($user->getGroups() as $group)
                                    {{ $group->name }}
                                @endforeach
                            </div>
                            <div class='col-sm-4 text-right'>
                                @if($current_user->hasAccess('admin.user.update') && $user->id != 1)
                                    <a href='#' class='btn edit-user' title='Edit this user'><i class='fa fa-edit'></i> Edit</a>
                                @endif
                                @if($current_user->hasAccess('admin.user.delete') && $user->id > 2)
                                    <a href='{{ URL::to('api/admin/users/delete/'. $user->id) }}' class='btn delete' title='Delete this user'><i class='fa fa-times icon-only'></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach

    </div>

    <div id='addUser' class="modal fade">
        <div class="modal-dialog">
            <form action='{{ URL::to('api/admin/users/create') }}' method='post' class="form-horizontal" role="form">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Add a user</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputName" name='name' placeholder="Username" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword" class="col-sm-2 control-label">Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="inputPassword" name='password' placeholder="Password"autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputGroup" class="col-sm-2 control-label">Group</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="inputGroup" name='group'>
                                    @foreach($groups as $group)
                                        <option value='{{ $group->id }}'>{{ $group->name }}</option>
                                    @endforeach
                                </select>
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
            <form action='{{ URL::to('api/admin/users/update') }}' method='post' class="form-horizontal" role="form">

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