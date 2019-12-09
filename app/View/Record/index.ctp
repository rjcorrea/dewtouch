<div class="row-fluid">
	<table class="table table-bordered" id="table_records">
		<thead>
			<tr>
				<th>ID</th>
				<th>NAME</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>
<?php $this->start('script_own') ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

<script>
	$(document).ready(function() {
		$("#table_records").DataTable({
			serverSide: true,
			ajax: {
				url: '<?php echo Router::url('/', true); ?>Record/source.json',
				type: 'POST'
			},
			order: [
				[0, "desc"]
			],
			language: {
				searchPlaceholder: "Search.."
			},
			columns: [{
					"data": 'id'
				},
				{
					"data": 'name'
				},
			]
		});
	})
</script>
<?php $this->end() ?>