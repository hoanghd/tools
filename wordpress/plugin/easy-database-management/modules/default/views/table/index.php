<section class="content-header">
  <h1> Manage Forms </h1>
</section>
<div class="content">
  <div class="row">
    <div class="col-xs-9">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#form-elements" aria-expanded="true">Form Elements</a></li>
          <li class=""><a data-toggle="tab" href="#database-elements" aria-expanded="false">Database Elements</a></li>
        </ul>
        <div class="tab-content">
          <div id="form-elements" class="tab-pane active table-responsive no-padding">
          	<div class="action">
                <button class="btn btn-default btn-sm" type="button"><i class="fa fa-trash-o"></i></button>
                <button class="btn btn-default btn-sm" type="button"><i class="fa fa-plus"></i></button>
            </div>
            <table class="table table-hover editable">
              <tbody>
                <tr>
                  <th><input type="checkbox"></th>
                  <th>Name</th>
                  <th>Type</th>
                  <th>Length</th>
                  <th>Decimals</th>
                  <th>Allow Null</th>
                  <th>Default</th>
                  <th>Comment</th>
                </tr>
                <tr>
                  <td><input type="checkbox"></td>
                  <td><a href="#" data-id="name" data-index="1" data-type="text" data-pk="1" data-title="Enter Name">Link ID</a></td>
                  <td><a href="#" data-id="type" data-index="1" data-type="select" data-title="Select Type">String</a></td>
                  <td>20</td>
                  <td>0</td>
                  <td><i class="fa fa-check"></i></td>
                  <td>0</td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div id="database-elements" class="tab-pane table-responsive no-padding">
            <div class="action">
            	<div class="pull-left mb10">
            		<input type="text" placeholder="Table Name" class="form-control">
                </div>
                <div class="pull-right">                
                  <button class="btn btn-default btn-sm" type="button"><i class="fa fa-refresh"></i></button>
                </div>
            </div>
            <table class="table table-hover">
              <tbody>
                <tr>
                  <th>ID</th>
                  <th>Type</th>
                  <th>Length</th>
                  <th>Decimals</th>
                  <th>Allow Null</th>
                  <th></th>
                </tr>
                <tr>
                  <td>link_id</td>
                  <td>Varchar</td>
                  <td>20</td>
                  <td>0</td>
                  <td><i class="fa fa-check"></i></td>
                  <td><i class="fa fa-key"></i></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xs-3">
      <div class="box box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Forms</h3>
          <div class="box-tools">
            <button class="btn btn-box-tool" type="button"><i class="fa fa-trash-o"></i></button>
            <button class="btn btn-box-tool" type="button"><i class="fa fa-plus"></i> </button>
          </div>
        </div>
        <div class="box-body no-padding">
          <ul class="nav nav-pills nav-stacked">
            <li class="active"><a href="#"><i class="fa fa-wpforms"></i> Sent</a></li>
            <li><a href="#"><i class="fa fa-wpforms"></i> Drafts</a></li>
            <li><a href="#"><i class="fa fa-wpforms"></i> Junk</a> </li>
            <li><a href="#"><i class="fa fa-wpforms"></i> Trash</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
