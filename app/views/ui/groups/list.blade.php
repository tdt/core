@extends('layouts.admin')

@section('content')

    <div class='row header'>
        <div class="col-sm-7">
            <h3>{{ trans('admin.header_group_panel') }}</h3>
        </div>
        <div class="col-sm-5 text-right">
            @if(Tdt\Core\Auth\Auth::hasAccess('admin.group.create'))
                <a href='' class='btn btn-primary margin-left' data-toggle="modal" data-target="#addGroup"
                    data-step='1'
                    data-intro='Add a new group to the system. <br/><br/>Groups have a series of <strong>permissions</strong> to allow or deny them actions.'
                    data-position="left">
                    <i class='fa fa-plus'></i> {{ trans('admin.add_button') }}
                </a>
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
            foreach ($users as $user) {
                if ($user->inGroup($group)) {
                    $users_string .= $user->email . ', ';
                }
            }
            $users_string = rtrim($users_string, ', ');
            ?>

            <div class="panel dataset dataset-link button-row panel-default"
                        data-id='{{ $group->id }}'
                        data-name='{{ $group->name }}'>
                <div class="panel-body"
                        @if($group->id == 1)
                            data-step='2'
                            data-intro="This is the <strong>public group</strong>, you can't change permissions for this group."
                            data-position="bottom"
                        @elseif($group->id == 2)
                            data-step='3'
                            data-intro="This is the <strong>superadmin group</strong>, all the permissions are assigned to users in this group."
                            data-position="bottom"
                        @endif
                    >
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
                                @if(Tdt\Core\Auth\Auth::hasAccess('admin.group.update') && $group->id > 2)
                                    <a href='#' class='btn edit-group' title='Rename this group'><i class='fa fa-edit'></i> {{ trans('admin.rename') }}</a>
                                @endif
                                @if(Tdt\Core\Auth\Auth::hasAccess('admin.group.delete') && $group->id > 2)
                                    <a href='{{ URL::to('api/admin/groups/delete/'. $group->id) }}' class='btn delete' title="{{ trans('admin.delete_group') }}"><i class='fa fa-times icon-only'></i></a>
                                @endif
                            </div>
                        </div>
                        <hr/>
                        <div class='row edit-permissions'>
                            <div class='col-sm-4'>
                                <a class='btn'
                                    @if($group->id == 2)
                                        data-step='4'
                                        data-intro="View or edit the permissions of a group."
                                        data-position="right"
                                    @endif
                                >
                                    <i class='fa fa-lock'></i>
                                    @if($group->id > 2 && Tdt\Core\Auth\Auth::hasAccess('admin.group.update'))
                                        {{ trans('admin.edit') }}
                                    @else
                                        {{ trans('admin.view') }}
                                    @endif
                                    {{ trans('admin.permissions') }}</a>
                            </div>
                            <div class='col-sm-8'>
                                @if($group->id != 2)
                                    <strong>{{ count($group_permissions) }}</strong>
                                @else
                                    <strong>{{ trans('admin.all') }}</strong>
                                @endif
                                {{ trans('admin.permissions') }}
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
                                                    <input type='checkbox' id='input_{{ $group->id . '_' . $key }}' name='{{ $key }}' @if($group->id == 2 ||in_array($key, $group_permissions)) checked='checked' @endif @if($group->id <= 2 || !Tdt\Core\Auth\Auth::hasAccess('admin.group.update')) disabled='disabled' @endif/><label for='input_{{ $group->id . '_' . $key }}'>{{ $permission }}</label>
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
        <a href='#' class='introjs pull-right'>
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