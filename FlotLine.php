<?php

class FlotLine
{
	protected $sets = array();
	protected $_id;
	
	function __construct($width = 1000, $height = 500)
	{
		$this->_id = md5(rand(1,999999));
		
		$this->width = $width;
		$this->height = $height;
	}
	
	public function addSet($setKey, $label, $extra = null)
	{
		$this->sets["$setKey"] = array(
			'label' => $label
			, 'data' => array()
		);
		
		if(!empty($extra))
		{
			$this->sets["$setKey"]['extra'] = $extra;
		}
	}
	
	public function addData($setKey, $k, $v)
	{
		$this->sets["$setKey"]['data'][] = array($k, $v);
	}	
	
	private function drawDiv()
	{
		return '<div id="' . $this->_id . '" style="border: 1px solid red; width:' . $this->width . 'px;height:' . $this->height .'px"></div>';
	}
	
	private function drawDataSets()
	{
		$str = 'var datasets = {';
		
		foreach($this->sets AS $key => $meta)
		{
			$str .= '"' . $key . '": {';
			$str .= '	label:"' . $meta['label'] . '",';
			$str .= '	data: [';
			$bits = array();
			foreach($meta['data'] AS $dk => $dval)
			{
				$bits[] = '[' . implode(',', $dval) . ']';
			}
			$str .= implode(',', $bits);
			$str .= ']';
			if(isset($meta['extra']))
			{
				$str .= ',' . $meta['extra'];
			}
			$str .= '},';
		}
		
		$str .= '};';

		$str .= '
			var data = [];
			$.each(datasets, function (k, v) {
				data.push(datasets[k]);
			});		
		';
		
		return $str;
	}
	
	protected function drawPlot()
	{
		return '

		 
			$.plot($("#' . $this->_id . '"), data, {
				yaxes: [{min: -100}, {position: "right"}],
				grid: { hoverable: true, clickable: true },
                series: {
                         lines: { show: true , shadowSize:0},
                         points: { show: false }
                },
				legend: {
					position: "nw"
				}
			});


		';	
	}
	
	public function draw()
	{
		echo $this->drawDiv();
		
		echo '<script type="text/javascript">';

		echo $this->drawDataSets();
		
		echo $this->drawPlot();
		
		echo $this->drawExtra();
		
		echo '</script>';
	}
	
	protected function drawExtra()
	{
		$str = "
		
		 function showTooltip(x, y, contents) {
				$('<div id=\"tooltip\">' + contents + '</div>').css( {
					position: 'absolute',
					display: 'none',
					top: y + 5,
					left: x + 5,
					border: '1px solid #fdd',
					padding: '2px',
					'background-color': '#fee',
					opacity: 0.80
				}).appendTo('body').fadeIn(200);
			}	
		";
		
		$str .= '
			$("#' . $this->_id . '").bind("plothover", function (event, pos, item) {
				if (item) {
					if (previousPoint != item.dataIndex) {
						previousPoint = item.dataIndex;
						
						$("#tooltip").remove();
						var x = item.datapoint[0].toFixed(2),
							y = item.datapoint[1].toFixed(2);
						
						showTooltip(item.pageX, item.pageY, y);
					}
				}
				else {
					$("#tooltip").remove();
					previousPoint = null;            
				}
			});
		';	
		return $str;
	}
}

?>