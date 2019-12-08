<?php
class RecordController extends AppController
{

	public $components = array('RequestHandler');

	public function index()
	{
		ini_set('memory_limit', '256M');
		set_time_limit(0);

		$this->setFlash('Listing Record page too slow, try to optimize it.');
		$this->set('title', __('List Record'));
	}

	/**
	 * Data source for Records
	 *
	 * @return void
	 */
	public function source()
	{
		if ($this->request->is('post')) {
			$start = $this->request->data('start');
			$length = $this->request->data('length');
			$search = $this->request->data('search');
			$columns = $this->request->data('columns');

			// Get total count of records
			$result = $this->Record->query('SELECT COUNT(*) AS total FROM records');
			$total = $result[0][0]['total'];


			// Get total count of filtered records
			$result = $this->Record->query('SELECT COUNT(*) AS total FROM records ' . self::search_sql($search, $columns));
			$filtered_total = $result[0][0]['total'];

			// Return JSON data
			$data = [];
			$data['draw'] = (int) $_POST['draw'];
			$data['recordsTotal'] = (int) $total;
			$data['recordsFiltered'] = (int) $filtered_total;
			$data['data'] = [];

			// Return JSON data
			$records = $this->Record->query('SELECT records.id, records.name FROM records 
			' . self::search_sql($search, $columns) . ' ' . self::sort_sql($this->request->data('order'), $columns) . ' ' . self::limit_sql($start, $length));


			foreach ($records as $record) {
				$data['data'][] = [
					'id' => $record['records']['id'],
					'name' => $record['records']['name']
				];
			}

			$this->set(compact('data'));
			$this->set('_serialize', 'data');
		} else {
			throw new UnauthorizedException('Page not found.');
		}
	}

	/**
	 * Build Sort SQL
	 *
	 * @param array $sort
	 * @param array $columns
	 * @return string
	 */
	private static function sort_sql($sort, $columns)
	{
		// Setup sort SQL using posted data
		$sort_array = [];
		foreach ($sort as $order) {
			$sort_array[] = $columns[$order['column']]['data'] . ' ' . $order['dir'];
		}

		return 'ORDER BY ' . implode(',', $sort_array);
	}

	/**
	 * Build Search SQL
	 *
	 * @param array $search
	 * @param array $columns
	 * @return void
	 */
	private static function search_sql($search, $columns)
	{
		// Setup search SQL using posted data
		$search_array = [];
		if ($search['value'] != '') {
			foreach ($columns as $column) {
				if ($column['searchable'] == 'true') {
					$search_array[] = $column['data'] . ' LIKE "%' . $search['value'] . '%"';
				}
			}
			$search_sql = 'WHERE ' . implode(' OR ', $search_array);
		} else {
			$search_sql = '';
		}

		return $search_sql;
	}

	/**
	 * Build Limit SQL
	 *
	 * @param int $start
	 * @param int $length
	 * @return void
	 */
	private static function limit_sql($start, $length)
	{
		return 'LIMIT ' . $start . ', ' . $length;
	}


	// 		public function update(){
	// 			ini_set('memory_limit','256M');

	// 			$records = array();
	// 			for($i=1; $i<= 1000; $i++){
	// 				$record = array(
	// 					'Record'=>array(
	// 						'name'=>"Record $i"
	// 					)			
	// 				);

	// 				for($j=1;$j<=rand(4,8);$j++){
	// 					@$record['RecordItem'][] = array(
	// 						'name'=>"Record Item $j"		
	// 					);
	// 				}

	// 				$this->Record->saveAssociated($record);
	// 			}



	// 		}
}
