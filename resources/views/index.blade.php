<?php
/**
 * @type array $requirements
 */
?>
@extends('layouts.main')
@section('content')
    <section id="section-inspection">
        <h2>Required Components</h2>

        <div class="row">
            <div class="col-md-8 col-sm-10 col-xs-12">
                <p>The following is a list of components that are required to run DreamFactory Enterprise&trade;. Those
                    that are not installed will be noted below. These must be installed before installation begins.</p>
                <table style="width: 50%" class="table table-bordered table-condensed table-responsive">
                    <thead>
                    <tr>
                        <th>Package</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($requirements as $_name => $_info) {
                        echo '<tr><td class="' .
                                $_info['status'] .
                                '">' .
                                $_name .
                                '</td><td class="' .
                                $_info['status'] .
                                '">' .
                                ($_info['has-package'] ? 'Yes' : 'No') .
                                '</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="section-options">
        <h2>Installation Settings</h2>

        <form method="POST" action="/">
            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-6">
                    <fieldset>
                        <legend>Install User & Group</legend>
                        <div class="form-group">
                            <label for="user">Install User</label>
                            <input required type="text" class="form-control" id="user" name="user" value="{{ $user }}"
                                   placeholder="dfadmin">
                        </div>
                        <div class="form-group">
                            <label for="group">Install Group</label>
                            <input required type="text" class="form-control" id="group" name="group"
                                   value="{{ $group }}" placeholder="dfadmin">
                        </div>
                        <div class="form-group">
                            <label for="storage-group">Storage Group</label>
                            <input required type="text" class="form-control" id="storage-group" name="storage-group"
                                   value="{{ $storage_group }}"
                                   placeholder="dfadmin">
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-6">
                    <fieldset>
                        <legend>Web Server User & Group</legend>
                        <div class="form-group">
                            <label for="www-user">Web Server User</label>
                            <input required type="text" class="form-control" id="www-user" name="www-user"
                                   value="{{ $www_user }}" placeholder="www-data">
                        </div>
                        <div class="form-group">
                            <label for="www-group">Web Server Group</label>
                            <input required type="text" class="form-control" id="www-group" name="www-group"
                                   value="{{ $www_group }}" placeholder="www-data">
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <fieldset>
                        <legend>Console <strong>Root</strong> Credentials</legend>
                        <p class="text-muted">These credentials are used to create the first administrative user for the DreamFactory Enterprise&trade; Console</p>
                        <div class="form-group">
                            <label for="admin-email">Email Address</label>
                            <input required type="email" class="form-control" id="admin-email" name="admin-email"
                                   value="{{ $admin_email }}" placeholder="you@yourdomain.com">
                        </div>
                        <div class="form-group">
                            <label for="admin-pwd">Password</label>
                            <input required type="password" class="form-control" id="admin-pwd" name="admin-pwd"
                                   placeholder="secret" value="{{ $admin_pwd }}">
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-6">
                    <fieldset>
                        <legend>Data Collection</legend>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input id="dc-es-exists" name="dc-es-exists" type="checkbox" value="" {{ $dc_es_exists ? 'checked' : '' }}>Use existing ELK system?
                                    <span class="help-block">If left unchecked, an ELK stack will be created on this system.</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dc-es-cluster">Elasticsearch Cluster</label>
                            <input required type="text" class="form-control" id="dc-es-cluster" name="dc-es-cluster"
                                   placeholder="elasticsearch" value="{{ $dc_es_cluster }}">
                        </div>
                        <div class="form-group">
                            <label for="dc-host">ELK Server Host Name</label>
                            <input required type="text" class="form-control" id="dc-host" name="dc-host"
                                   placeholder="localhost" value="{{ $dc_host }}">
                        </div>
                        <div class="form-group">
                            <label for="dc-port">ELK Server Port</label>
                            <input required type="text" class="form-control" id="dc-port" name="dc-port"
                                   placeholder="12202" value="{{ $dc_port }}">
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <fieldset>
                        <legend>MySQL <strong>Root</strong> Password</legend>
                        <div class="form-group">
                            <label for="mysql-root-pwd">MySQL Root Password</label>
                            <input required type="password" class="form-control" id="mysql-root-pwd"
                                   name="mysql-root-pwd"
                                   placeholder="secret" value="{{ $mysql_root_pwd }}">
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-8 col-xs-12">
                    <fieldset>
                        <legend>DNS</legend>
                        <div class="form-group">
                            <label for="vendor-id">Sub-domain/Zone</label>
                            <input required type="text" class="form-control" id="vendor-id" name="vendor-id"
                                   placeholder="zone"
                                   value="{{ $vendor_id }}">
                        </div>
                        <div class="form-group">
                            <label for="domain">Top-level Domain</label>
                            <input required type="text" class="form-control" id="domain" name="domain"
                                   placeholder="domain.com"
                                   value="{{ $domain }}">
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-6 col-sm-8 col-xs-12">
                    <fieldset>
                        <legend>Data Storage</legend>
                        <div class="form-group">
                            <label for="mount-point">Storage Mount Point</label>
                            <input required type="text" class="form-control" id="mount-point" name="mount-point"
                                   placeholder="/data"
                                   value="{{ $mount_point }}">

                            <p class="help-block">Absolute path where instance data is to be stored</p>
                        </div>
                        <div class="form-group">
                            <label for="storage-path">Storage Path</label>
                            <input required type="text" class="form-control" id="storage-path" name="storage-path"
                                   placeholder="/storage"
                                   value="{{ $storage_path }}">

                            <p class="help-block">Relative to [<strong>Storage Mount Point</strong>]</p>
                        </div>
                        <div class="form-group">
                            <label for="log-path">Base Log Path</label>
                            <input required type="text" class="form-control" id="log-path" name="log-path"
                                   placeholder="/data/logs"
                                   value="{{ $log_path }}">

                            <p class="help-block">Absolute path where system logs are to be stored</p>
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-md btn-success">
                    <i class="fa fa-arrow-circle-right"></i> Save Configuration
                </button>
            </div>
        </form>
    </section>
@stop
