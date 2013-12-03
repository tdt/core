@extends('layouts.admin')

@section('content')

    <div class='row'>
        <div class="col-sm-7">
            <h3>Manage your groups <small>and permissions</small></h3>
        </div>
        <div class="col-sm-5 text-right">
            @if(tdt\core\auth\Auth::hasAccess('admin.group.create'))
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

            <?php
                $group_permissions = $group->getPermissions();
                $group_permissions = array_keys($group_permissions);

                $users_string = '';
                foreach($users as $user){
                    if($user->inGroup($group)){
                        $users_string .= $user->email . ', ';
                    }
                }
                $users_string = rtrim($users_string, ', ');
            ?>

            <div class="panel dataset dataset-link button-row panel-default"
                        data-id='{{ $group->id }}'
                        data-name='{{ $group->name }}'>
                <div class="panel-body">
                    <div class='icon'>
                        <i class='fa fa-group'></i>
                    </div>
                    <div>
                        <div class='row'>
                            <div class='col-sm-4'>
                                <h4 class='dataset-title'>
                                    <a href='{{ URL::to('api/admin/groups/edit/' . $group->id) }}'>{{ $group->name }}</a>
                                </h4>
                            </div>
                            <div class='col-sm-4'>
                                @if(!empty($users_string))
                                    <strong>Users:</strong>
                                    {{ $users_string }}
                                @endif
                            </div>
                            <div class='col-sm-4 text-right'>
                                @if(tdt\core\auth\Auth::hasAccess('admin.group.update') && $group->id > 2)
                                    <a href='#' class='btn edit-group' title='Rename this group'><i class='fa fa-edit'></i> Rename</a>
                                @endif
                                @if(tdt\core\auth\Auth::hasAccess('admin.group.delete') && $group->id > 2)
                                    <a href='{{ URL::to('api/admin/groups/delete/'. $group->id) }}' class='btn delete' title='Delete this group'><i class='fa fa-times icon-only'></i></a>
                                @endif
                            </div>
                        </div>
                        <hr/>
                        <div class='row edit-permissions'>
                            <div class='col-sm-4'>
                                <a class='btn'>
                                    <i class='fa fa-lock'></i>
                                    @if($group->id > 2 && tdt\core\auth\Auth::hasAccess('admin.group.update'))
                                        Edit
                                    @else
                                        View
                                    @endif
                                    permissions</a>
                            </div>
                            <div class='col-sm-8'>
                                @if($group->id != 2)
                                    <strong>{{ count($group_permissions) }}</strong>
                                @else
                                    <strong>All</strong>
                                @endif
                                permissions
                            </div>
                        </div>
                        <div class='row permissions'>
                            <hr/>
                            <div class='col-sm-4'>
                            </div>
                            <div class='col-sm-8'>
                                <form action='{{ URL::to('api/admin/groups/update') }}' method='post'>

                                    <input type="hidden" class="form-control" name='id' value='{{ $group->id }}'>

                                    @foreach($permission_groups as $perm_group => $permissions)
                                        <div>
                                            <strong>{{ $perm_group }}</strong>
                                            <div class=''>
                                                @foreach($permissions as $key => $permission)
                                                    <input type='checkbox' id='input_{{ $group->id . '_' . $key }}' name='{{ $key }}' @if($group->id == 2 ||in_array($key, $group_permissions)) checked='checked' @endif @if($group->id <= 2 || !tdt\core\auth\Auth::hasAccess('admin.group.update')) disabled='disabled' @endif/><label for='input_{{ $group->id . '_' . $key }}'>{{ $permission }}</label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach

                                    @if($group->id > 2)
                                        <input type='submit' name='btn_save_permissions' class='btn btn-cta' value='Save permissions' />
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach

        <br/>
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
                        <button type="submit" class="btn btn-cta">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id='editGroup' class="modal fade">
        <div class="modal-dialog">
            <form action='{{ URL::to('api/admin/groups/update') }}' method='post' class="form-horizontal" role="form">

                <input type="hidden" class="form-control" id="inputEditId" name='id'>

                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Rename a group</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="inputEditName" class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputEditName" name='name' placeholder="Groupname" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-cta">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@stop