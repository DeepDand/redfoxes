<!DOCTYPE html>
<html lang="en">
<head>
  <title>Marist Discussion Forums</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<?php @session_start();//echo form_open(base_url().'Discussion/discussionDetails','role="form"'); ?>
<div>
  <form name="dview" id="dview" method="post" action="<?php echo base_url().'Discussion/discussionDetails/'; ?>">
    <h2>On-going Discussions</h2>
      <div>
        <table id="tabledata"  class="table table-striped table-bordered responsive" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Discussion Title</th>
              <th>Created By</th>
              <th>Category</th>
              <th>Created on</th>
              <th class="hidden-xs hidden-sm hidden-md hidden-lg"></th>
              <th class="hidden-xs hidden-sm hidden-md hidden-lg"></th>
            </tr>
          </thead>

        </div>
        <tbody>
          <?php
            foreach ($query->result() as $result) ://$this->input->post($result->d_id, $result->d_title, $result->cwid);?>
            <div>
              <tr>
                <td><a id="anchorid" href="javascript:fetchList('<?php echo base_url().'Discussion/discussionDetails/'.$result->d_id; ?>')"><?php echo ucfirst($result->d_title); ?></a></td>
                <td><?php echo ucfirst($result->username); ?></td>
                <td><?php echo $result->category; ?></td>
                <td><?php echo $result->age; ?></td>
                <td class="hidden-xs hidden-sm hidden-md hidden-lg"><input type="hidden" id="d_id" name= "d_id" value ="<?php echo (isset($result->d_id))?$result->d_id:'';?>" required="required" /></td>
                <td class="hidden-xs hidden-sm hidden-md hidden-lg"><input type="hidden" id="getURL" name="getURL" value="<?php echo base_url().'Discussion/discussionDetails/'.$result->d_id; ?>"></input></td><!--this is to pass urls to specific discussions -->
              </tr>
            </div>
          <?php endforeach; ?>
        </tbody>
      </table>
    </form>
  </div>
<!--<?php //echo form_close(); ?>-->
<script type="text/javascript">
 $('#tabledata').DataTable({
   "order": [[3, "desc"]]
 });
  function fetchList(myURL){
    console.log(resultUrl);
    var resultUrl = myURL;//document.getElementById('getURL').value; //"<?php //echo base_url().'Discussion/discussionDetails/'; ?>"+getdid;
    $('#dlist').load(resultUrl);
    $('#dlist').css('display','block');//showing the list of discussion
    $('#disclist').css('display','none'); //hiding the button create new discussion and view on-going discussions
    //console.log(resultUrl);
  }
</script>
</body>
</html>
