<?php
require_once "../common.php";
require_once "../header.php";
require_once "../misc/channel-lookup-misc.php";

$title = "Channel Lookup";
$channel = "";
$channame = "";
$nick = NULL;
$channelObj = NULL;
do_log($_GET);
do_log($_POST);
if (isset($_GET['chan']))
{
	$channel = $_GET['chan'];
	$channelObj = $rpc->channel()->get($channel);
	if (!$channelObj && strlen($channel))
	{
		Message::Fail("Could not find channel: \"$channel\"");
	} elseif (strlen($channel)) {

		$channame = $channelObj->name;
		$title .= " for \"" . $channame . "\"";
		do_log($channelObj);
	}
}
$topicset = false;
$del_ex = false;
$del_inv = false;
$del_ban = false;
$checkboxes = [];
if (isset($_POST))
{
	if (isset($_POST['update_topic']) && isset($_POST['set_topic']))
	{
		if (isset($channelObj))
		{
			if (!isset($channelObj->topic) || strcmp($channelObj->topic,$_POST['set_topic'])) // if the set topic is different
			{
				$user = (function_exists('unreal_get_current_user') && $u = unreal_get_current_user()) ? $u->username : NULL;
				$topicset = $rpc->channel()->set_topic($channelObj->name, $_POST['set_topic'], $user);
				$channelObj->topic = $_POST['set_topic'];
			}
		}
	}
	$checkboxes = (isset($_POST['ban_checkboxes'])) ? $_POST['ban_checkboxes'] : [];
	if (isset($_POST['delete_sel_ex']))
	{
		foreach($_POST['ce_checkboxes'] as $c)
			$checkboxes[] = $c;
		$del_ex = true;
		chlkup_autoload_modal("excepts_modal");
	}
	else if (isset($_POST['delete_sel_inv']))
	{
		foreach($_POST['ci_checkboxes'] as $c)
			$checkboxes[] = $c;
		$del_inv = true;
		chlkup_autoload_modal("invites_modal");
	}
	else if (isset($_POST['delete_sel_ban']))
	{
		foreach($_POST['cb_checkboxes'] as $c)
			$checkboxes[] = $c;
		$del_ban = true;
		chlkup_autoload_modal("bans_modal");
	}

}

?>
<title><?php echo $title; ?></title>
<h4><?php echo $title; ?></h4>
<br>
<form method="get" action="details.php">
<div class="container-xxl">
	<div class="input-group">
		<input  class="form-control" id="chan" name="chan" type="text" value="<?php echo $channame; ?>">
		<div class="input-group-append">
			<button type="submit" class="btn btn-primary">Go</button>
		</div>
	</div>
</div>
</form>

<?php if (!$channelObj)
		return; ?>

<br>
<h3>
	Topic:<br></h3>
	<form method="post" action="details.php?chan=<?php echo urlencode($channelObj->name); ?>">
	<div class="input-group">
	<input maxlength="360" type="text" class="input-group form-control" name="set_topic" value="<?php echo (isset($channelObj->topic)) ? htmlspecialchars($channelObj->topic) : ""; ?>">
	<div class="input-group-append"><button type="submit" name="update_topic" value="true" class="btn btn-info">Update Topic</button></div></div>
	</form>
<?php 
if ($topicset)
	Message::Success("The topic for $channelObj->name has been updated to be: \"".htmlspecialchars($channelObj->topic)."\"");
?>
<br>
<div class="row">
	<div class="col-sm-3">
		<div class="btn btn-sm btn-danger" data-toggle="modal" data-target="#bans_modal">Bans</div>
		<div class="btn btn-sm btn-info" data-toggle="modal" data-target="#invites_modal">Invites</div>
		<div class="btn btn-sm btn-warning" data-toggle="modal" data-target="#excepts_modal">Exceptions</div>
	</div>
</div>
<br>

<!-- Modal for Channel Bans -->
<div class="modal fade" id="bans_modal" tabindex="-1" role="dialog" aria-labelledby="confirmModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="myModalLabel">Channel Bans</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<?php if ($del_ban) do_delete_chanban($channelObj, $checkboxes); ?>
			<form method="post">
			<?php generate_chanbans_table($channelObj); ?>		
			</form>
		</div>
		</div>
	</div>
</div>
<!-- Modal for Channel Invited -->
<div class="modal fade" id="invites_modal" tabindex="-1" role="dialog" aria-labelledby="confirmModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="myModalLabel">Channel Invites</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<?php if ($del_inv) do_delete_invite($channelObj, $checkboxes); ?>
			<form method="post">
			<?php generate_chaninvites_table($channelObj); ?>		
			</form>
		</div>
		</div>
	</div>
</div>

<!-- Modal for Channel Exceptions -->
<div class="modal fade" id="excepts_modal" tabindex="-1" role="dialog" aria-labelledby="confirmModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="myModalLabel">Channel Exceptions</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<?php if ($del_ex) do_delete_chanex($channelObj, $checkboxes); ?>
			<form method="post">
			<?php generate_chanexcepts_table($channelObj); ?>		
			</form>
		</div>
		</div>
	</div>
</div>



<div class="container-xxl">
	<div class="accordion" id="accordionExample">
		<div class="card">
			<div class="card-header" id="headingOne">
			<h2 class="mb-0">
				<button class="btn" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
				User List
				</button>
			</h2>
			</div>

			<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
			<div class="card-body">
				<?php generate_chan_occupants_table($channelObj); ?>
			</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header" id="headingTwo">
			<h2 class="mb-0">
				<button class="btn collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
				Collapsible Group Item #2
				</button>
			</h2>
			</div>
			<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
			<div class="card-body">
				Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
			</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header" id="headingThree">
			<h2 class="mb-0">
				<button class="btn collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
				Collapsible Group Item #3
				</button>
			</h2>
			</div>
			<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
			<div class="card-body">
				Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
			</div>
			</div>
		</div>
	</div>
</div>
<?php 
require_once("../footer.php");

