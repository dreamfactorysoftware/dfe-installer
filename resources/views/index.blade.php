<?php
/**
 * @type array $requirements
 */
?>
@extends('layouts.main')
@section('content')
    <section id="section-options">
        <h2>Installation Settings</h2>

        <form method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-6">
                    <fieldset>
                        <legend>Install User & Group</legend>
                        <p class="text-muted">This user/group is created (if it does not exist) and will own the DFE
                            directories and services. This user is given <strong>sudo</strong> rights as well. The
                            defaults are recommended.</p>

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
                        <p class="text-muted">This is the user/group that owns the web server files and processes. It
                            isn't something you create, but rather set by your web server software. The defaults should
                            be fine.</p>

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
                        <legend>DFE <strong>Administrator</strong> Credentials</legend>
                        <p class="text-muted">These credentials are used to create the initial administrator account on
                            the DreamFactory Enterprise&trade; Console</p>

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
                        <legend>MySQL <strong>Root</strong> Password</legend>
                        <p class="text-muted">This installer creates a local MySQL database. This password will be the
                            <strong>root</strong> password for that database.</p>

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
                <div class="col-md-6">
                    <fieldset>
                        <legend>GitHub Token</legend>
                        <p class="text-muted">To avoid GitHub API Rating Limit issues during installation, please create
                            a personal access token on GitHub and enter it below. Click the link below to do this:<br />
                            <small>
                                <a href="https://github.com/settings/tokens/new?scopes=repo&description={{ $token_name }}"
                                   target="_blank">https://github.com/settings/tokens/new?scopes=repo&description={{ $token_name }}</a>
                            </small>
                        </p>

                        <div class="form-group">
                            <label for="gh-token">OAuth Token</label>
                            <input required type="password" class="form-control" id="gh-token"
                                   name="gh-token"
                                   placeholder="token" value="{{ $gh_token }}">
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-6">
                    <fieldset>
                        <legend>DNS</legend>
                        <p class="text-muted">This is the zone and domain of this cluster. This will be the subdomain of
                            all instances provisioned on this cluster.</p>

                        <div class="form-group">
                            <label for="vendor-id">Subdomain/Zone</label>
                            <input required type="text" class="form-control" id="vendor-id" name="vendor-id"
                                   placeholder="zone"
                                   value="{{ $vendor_id }}">
                        </div>
                        <div class="form-group">
                            <label for="domain">Top-level Domain</label>
                            <input required type="text" class="form-control" id="domain" name="domain"
                                   placeholder="yourdomain.com"
                                   value="{{ $domain }}">
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-8 col-xs-12">
                    <fieldset>
                        <legend>Data Storage</legend>
                        <p class="text-muted">DFE uses a single directory, or mount point, to house its provisioned
                            storage. This can be a local disk or mount point. The defaults are adequate.</p>


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

                <div class="col-md-6 col-sm-8 col-xs-12">
                    <fieldset>
                        <legend>Reporting</legend>
                        <p class="text-muted">Instances provisioned by DFE send reporting data back to a data collection
                            system utilizing elasticsearch/logstash/kibana (ELK). If you have an existing <strong>elasticsearch</strong>
                            cluster that you'd prefer to use, tick the first box and enter the particulars. Otherwise an
                            entire ELK stack will be installed locally.</p>

                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input id="dc-es-exists" name="dc-es-exists" type="checkbox"
                                           value="" {{ true === $dc_es_exists ? 'checked="checked"' : null }}>Use
                                    existing ELK stack?
                                    <span class="help-block">If left unchecked, an ELK stack will be created on this system.</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dc-es-cluster">Elasticsearch Cluster</label>
                            <input required type="text" class="form-control" id="dc-es-cluster" name="dc-es-cluster"
                                   placeholder="elasticsearch"
                                   value="{{ $dc_es_cluster }}" {{ false === $dc_es_exists ? 'disabled="disabled"' : null }}>
                        </div>
                        <div class="form-group">
                            <label for="dc-host">Elasticsearch Server Host Name</label>
                            <input required type="text" class="form-control" id="dc-host" name="dc-host"
                                   placeholder="localhost"
                                   value="{{ $dc_host }}" {{ false === $dc_es_exists ? 'disabled="disabled"' : null }}>
                        </div>
                        <div class="form-group">
                            <label for="dc-port">Elasticsearch Server Port</label>
                            <input required type="text" class="form-control" id="dc-port" name="dc-port"
                                   placeholder="12202"
                                   value="{{ $dc_port }}" {{ false === $dc_es_exists ? 'disabled="disabled"' : null }}>
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-8 col-xs-12">
                    <fieldset>
                        <legend>Custom CSS</legend>
                        <div class="form-group">
                            <label for="custom-css">CSS</label>
                            <textarea rows="10" cols="60" class="form-control" id="custom-css" name="custom-css">{!! $custom_css_file !!}</textarea>
                            <p class="help-block">CSS to use with DFE web applications. Validity is <em>not</em> checked. Custom CSS is loaded <em>last</em>.
                            </p>
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-6 col-sm-8 col-xs-12">
                    <fieldset>
                        <legend>Custom Logos</legend>
                        <div class="form-group">
                            <label for="login-splash-image">Login/Splash Image</label>
                            <input type="file" class="form-control" id="login-splash-image" name="login-splash-image" value="{{ $login_splash_image }}">
                            <p class="help-block">This image will be displayed on all login and password pages. Recommended image size is 256x256 pixels.</p>
                        </div>
                        <div class="form-group">
                            <label for="navbar-image">Navigation Bar Image</label>
                            <input type="file" class="form-control" id="navbar-image" name="navbar-image" value="{{ $navbar_image }}">
                            <p class="help-block">This image will be displayed in the navigation bar of all DFE web applications. Image size expected to be
                                194x42 pixels.</p>
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

    <script>
        jQuery(function ($) {
            var $_checkbox = $('#dc-es-exists'), $_dcHost = $('#dc-host'), $_domain = $('#domain'), $_zone = $('#vendor-id');
            var _lastHost = $_dcHost.val();

            //  Set the reporting host name the same as the console if we are installing ELK
            $('#vendor-id, #domain').on('change', function () {
                if (!$_checkbox.prop('checked')) {
                    if (_lastHost == $_dcHost.val()) {
                        $_dcHost.val(_lastHost = '{{ $console_host_name }}.' + $_zone.val() + '.' + $_domain.val());
                    }
                }
            });

            //  Enable/disable ES entries if we are not installing ELK
            $_checkbox.on('change', function () {
                if (this.prop('checked')) {
                    $('#dc-es-cluster, #dc-host, #dc-port').removeAttr('disabled').removeClass('disabled').removeAttr('required');
                    $_dcHost.val('');
                } else {
                    $('#dc-es-cluster, #dc-host, #dc-port').attr('disabled', 'disabled').prop('required', true);
                }
            });
        });
    </script>
@stop
