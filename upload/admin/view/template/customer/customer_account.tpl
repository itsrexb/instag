<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
      <thead>
        <tr>
          <td><?php echo $column_username; ?>
          <td><?php echo $column_type; ?>
          <td><?php echo $column_date_expires; ?>
          <td><?php echo $column_date_added; ?>
          <td class="text-right"><?php echo $column_action; ?>
        </tr> 
       </thead>
      <tbody>
        <?php foreach ($accounts as $account) { ?>
        <tr>
          <td><?php echo $account['username']; ?></td>
          <td><?php echo ucwords($account['type']); ?></td>
          <td><?php echo $account['date_expires']; ?></td>
          <td><?php echo $account['date_added']; ?></td>
          <td class="text-right"><a href="<?php echo $account['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
        </tr>
        <?php } ?>
    </tbody>
  </table>
</div>
<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>