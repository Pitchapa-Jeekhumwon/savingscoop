<?php
function U2T($text)
{
	return @iconv("UTF-8", "TIS-620//IGNORE", ($text));
}
function num_format($text)
{
	if ($text != '') {
		return number_format($text, 2);
	} else {
		return '';
	}
}

$filename = $_SERVER["DOCUMENT_ROOT"] . PROJECTPATH . "/assets/document/loan_request/" . $path_data[0]['path_data'];
$pdf = new FPDI();

$pageCount_1 = $pdf->setSourceFile($filename);
for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {
	$pdf->AddPage();
	$tplIdx = $pdf->importPage($pageNo);
	$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
}
?>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<script src="https://kit.fontawesome.com/2e9f1235d6.js" crossorigin="anonymous"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

	<style>
		html {
			margin: 0px;
		}

		body { 
			color: black;
			margin: 0px;
			text-align: center;
		}

		input {
			width: 95%;
			font-size: 14px;
			height: 25px;
		}

		select {
			height: 25px;
			width: 25%;
			font-size: 14px;
		}

		select#type_pdf,
		select#loan_type {
			width: 90%;
		}

		li.label {
			width: 100%;
		}

		select {
			color: black;
		}

		input {
			color: black;
		}

		.container {
			width: 100%
		}

		.page {
			display: flex;
			justify-content: center;
		}

		.tool {
			width: 35vw;
			border: 1px solid blue;
			height: 107vh;
			overflow: auto;
		}

		.render {
			width: <?= $pdf->w * 4 ?>px;
			height: <?= $pdf->h * 4  ?>px;
		}

		#pdf_viewer {
			width: <?= $pdf->w * 3.91 ?>px !important;
			height: <?= $pdf->h * 3.87 ?>px !important;
		}

		#canvas {
			width: <?= $pdf->w *  3.91 ?>px !important;
			height: <?= $pdf->h * 3.87  ?>px !important;
			border: 1px solid blue;
			position: relative;
			top: -<?= $pdf->h * 3.89 ?>px !important;
			padding-top: 18px;
			padding-left: 27.5px !important;
		}

		ul>li.label>input {
			width: 60px;
		}

		li.active {
			background-color: blue;
			
		}
	</style>
</head>

<body>
	<div class="container">
		<div style="text-align: center; margin-top:1%">
			<h1>หน้าจัดการ วางตำแหน่งข้อความเอกสาร</h1>
		</div>
		<div class="page" >
			<section class="tool">
				<form action="save" method="post">
					<input type="hidden" id="path" name="path" value="">
					<input type="hidden" name="type_id" value="<?php echo  $path_data[0]['id'] ?>">
					<div>
						<h1>ชื่อไฟล์</h1>
						<input type="text" name="file_name" value="<?php echo $path_data[0]['details'] ?>" placeholder="กรุณาตั้งชื่อไฟล์" required>
						<div style="display: flex;">
							<div style="width:50%">
								<h3>เลือกสัญญา</h3>
								<select name="type_pdf" id="type_pdf" required>
									<option value="" disabled selected>กรุณาเลือกรูปแบบสัญญา</option>
								</select>
							</div>
							<div style="width:50%">
								<h3>เลือกประเภทเอกสาร</h3>
								<select name="loan_type" id="loan_type" required>
									<?php foreach ($path_data as $dataa) {
										if ($dataa['type_loan'] == "11") {
											echo ('<option value="11" hidden selected>หนังสือสัญญา</option>');
										}
										if ($dataa['type_loan'] == "12") {
											echo ('<option value="12" hidden selected>หนังสือค้ำประกัน</option>');
										}
										if ($dataa['type_loan'] == "13") {
											echo ('<option value="13" hidden selected>หนังสือรับรองเงินเดือน</option>');
										}
										if ($dataa['type_loan'] == "1") {
											echo ('<option value="1" hidden selected>คำขอกู้เงินกู้ฉุกเฉิน</option>');
										}
										if ($dataa['type_loan'] == "2") {
											echo ('<option value="2" hidden selected>คำขอกู้เงินกู้สามัญ</option>');
										}
										if ($dataa['type_loan'] == "3") {
											echo ('<option value="3" hidden selected>คำขอกู้เงินกู้พิเศษ</option>');
										}
										if ($dataa['type_loan'] == "5") {
											echo ('<option value="5" hidden selected>คำขอกู้เงินกู้เพื่อการเคหะฯ</option>');
										}
									} ?>
									<option value="11">หนังสือสัญญา</option>
									<option value="12">หนังสือค้ำประกัน</option>
									<option value="13">หนังสือรับรองเงินเดือน</option>
								</select>
							</div>
						</div>
					</div>
					<hr>
					<div style="display: flex; justify-content:space-between;">
						<h1>เครื่องมือตั้งค่า</h1>
						<select id="page" onchange="change_page()" style="height: fit-content; justify-content:center;align-self: center;">
						</select>
					</div>
					<ul id="label_list">
						<?php $i = 0;
						foreach ($data_details as $details) { ?>
							<li class="label" id="label_<?php echo $i; ?>" onclick="active_on('label_<?php echo $i; ?>')" value="<?php echo $details['page_no']; ?>">
								<!-- id -->
								<input type="hidden" name="id_<?php echo $i ?>" value="<?php echo $details['id'] ?>">

								<!-- page_no -->
								<input type="hidden" name="page_no_<?php echo $i ?>" value="<?php echo $details['page_no'] ?>">

								<!-- ---------ชื่อ--------- -->
								<select name="data_name_<?php echo $i; ?>" id="data_name_<?php echo $i; ?>" onchange="c_txt('label_<?php echo $i; ?>',value);">
									<option value="<?php echo $details['data_name'] ?>" selected><?php echo $details['ref'] ?></option>
								</select>

								<!-- ---------ตำแหน่ง--------- -->
								<input type="hidden" name="label_x_<?php echo $i; ?>" value="<?php echo $details['x_point'] ?>" id="label_x_<?php echo $i; ?>">
								<input type="hidden" name="label_y_<?php echo $i; ?>" value="<?php echo $details['y_point'] ?>" id="label_y_<?php echo $i; ?>">

								<!-- ---------size--------- -->
								<input type="font_size_<?php echo $i; ?>" name="font_size_<?php echo $i; ?>" id="font_size_<?php echo $i; ?>" value="<?php echo $details['fonts_size'] ?>" onkeyup="c_font_size('label_<?php echo $i; ?>',value,<?php echo $details['page_no']; ?>);">
								<!-- ---------fonts--------- -->
								<select name="fonts_<?php echo $i; ?>" id="fonts_<?php echo $i; ?>" onchange="c_font('label_<?php echo $i; ?>',value,<?php echo $details['page_no']; ?>)">
									<option value="THSarabunNew" selected>THSarabunNew</option>
								</select>

								<!--h -->
								<input type="hidden" name="h_<?php echo $i ?>" value="<?php echo $details['text_height']; ?>" placeholder="ความสูง">
								<!-- w -->
								<input type="text" name="w_<?php echo $i ?>" onkeyup="change_w('label_<?php echo $i ?>',value)" value="<?php echo $details['text_width']; ?>" placeholder="ความกว้าง">
								
								<select name="point_<?php echo $i; ?>" id="point_<?php echo $i; ?>" onchange="c_point('label_<?php echo $i; ?>',value,'<?php echo $i; ?>')">
									<option value="C" <?php echo $details['text_point']=="C"?"selected":"" ?>>กิ่งกลาง</option>
									<option value="L" <?php echo $details['text_point']=="L"?"selected":"" ?>>ชิดซ้าย</option>
									<option value="R" <?php echo $details['text_point']=="R"?"selected":"" ?>>ชิดขวา</option>
								</select>

								<a onclick="remove('label_<?php echo $i; ?>','<?php echo $details['id'] ?>');"><i class="fas fa-minus" style="color: red;"></i></a>
							</li>
						<?php
							$i++;
						}
						?>
					</ul>
					<input type="hidden" name="max_count" id="max_count">
					<div class="input_btn" style="text-align: center;">
						<a class="btn btn-success" id="add" onclick="add()">เพิ่มข้อความ</a>
						<a class="btn btn-success" id="add" onclick="add_box()">เพิ่ม Check Box</a>
					</div>
					<hr>
					<button class="btn btn-primary" type="submit" onclick="save()">บันทึกข้อมูล</button>
					<button class="btn btn-danger" type="submit" onclick="window.parent.parent.location.reload();">ยกเลิก</button>
				</form>
			</section>
			<section class="render">
				<iframe id="pdf_viewer" name="pdf_viewer" src="<?php echo "/assets/document/loan_request/" . $path_data[0]['path_data'] ?>#page=1&zoom=100&scrollbar=0&toolbar=0&navpanes=0frameborder=0"></iframe>
				<canvas id="canvas"></canvas>
			</section>
		</div>
	</div>
</body>

<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>

<script>
	for (let n_page = 1; n_page <= <?= @$pageCount_1 ?>; n_page++) {
		$("#page").append(
			"<option value=" + n_page + ">หน้าที่ " + n_page + "</option>"
		)
	}

	function remove(label, id) {
		text.forEach(function(item, key) {
			if (item.id == label) {}
		});
		if (id != '') {
			$.ajax({
				type: "post",
				url: "remove_data_details",
				data: {
					id: id,
				},
				dataType: 'json',
				success: function(data) {
					const Toast = Swal.mixin({
						toast: true,
						position: 'top-end',
						showConfirmButton: false,
						timer: 3000,
						timerProgressBar: true,
						didOpen: (toast) => {
							toast.addEventListener('mouseenter', Swal.stopTimer)
							toast.addEventListener('mouseleave', Swal.resumeTimer)
						}
					})
					Toast.fire({
						icon: 'success',
						title: data
					})
				}
			});
		}
		$("#" + label).remove();
	}

	function change_w(id, value) {
		text.forEach(function(item, key) {
			if (item.id == id) {
				text[key]['width'] = value;
			}
		});
	}

	function change_page() {
		page = $("#page").val();
		$("li.label").each(function(index) {
			if ($(this).val() != page) {
				$(this).hide();
			} else {
				$(this).show();
			}
		});
		text.forEach(function(item, key) {
			if (item.page != page) {
				text[key]['strokeStyles'] = "#fff";
				text[key]['color'] = "rgba(255, 99, 71, 0)";
			} else {
				text[key]['color'] = "blue"
				text[key]['strokeStyles'] = "#000";
			}
		});

		var elems = document.querySelectorAll("li.label");
		[].forEach.call(elems, function(el) {
			el.classList.remove("active");
		});

		$("#pdf_viewer").attr('src', '<?php echo "/assets/document/loan_request/" . $path_data[0]['path_data'] ?>#page=' + page + '&zoom=100&scrollbar=0&toolbar=0&navpanes=0')
		document.getElementById('pdf_viewer').contentDocument.location.reload(true);
	}

	function save() {
		ma = text.length
		$("#max_count").val(ma);
	}
	var exam_data = [];
	var x = 50;
	var y = 50;
	var active_id = "";
	var active_key = "";
	var text = [
		<?php $i = 0;
		foreach ($data_details as $data) { 
			$val=$data['text_point'];
			if ($val=="C") {
				$val='center';
			}else if($val=="L"){
				$val='right';
			}else if($val=="R"){
				$val='left';
			}

		?>{
			
				id: "label_<?php echo $i ?>",
				x: <?php echo $data['x_point'] = '' ? "0" : $data['x_point'] * 3.89 ?>,
				y: <?php echo $data['y_point'] = '' ? "0" : $data['y_point'] * 3.85 ?>,
				width: <?php echo $data['text_width'] != '' ? $data['text_width'] : "5" ?>,
				height: <?php echo $data['text_height'] != '' ? $data['text_height'] : "5" ?>,
				color: "blue",
				font: "<?php echo $data['fonts_size'] ?>px <?php echo $data['fonts'] ?>",
				textAlign: "<?php echo $val ?>",
				text: "<?php echo $data['detail_data'] ?>",
				is_lock: true,
				page: <?php echo $data['page_no'] ?>,
				strokeStyles: "#000"
			},
		<?php $i++;
		}
		?>
	];

	var drag = false;
	window.onload = function() {
		var canvas = document.getElementById("canvas");
		fitToContainer(canvas);
		var context = canvas.getContext('2d');

		var m = [1, 0, 0, 1, 0, 0];
		var lastMatrix = [];

		function draw() {
			get_id_page=document.getElementById("page").value;
			text.forEach(item => {
				if(item.page==get_id_page){
				var ctx = canvas.getContext("2d");
				ctx.font = item.font;
				ctx.fillStyle = item.color;
				ctx.textAlign = item.textAlign;
				
				ctx.fillText(item.text, item.x + (item.width * 1.5), item.y);
				ctx.strokeRect(item.x, (item.y) - (item.height * 2.5), item.width * 3, item.height * 3);
				ctx.strokeStyle = item.strokeStyles;
				}
			});
		}

		function matrixScale(sx, sy) {
			m[0] *= sx;
			m[1] *= sx;
			m[2] *= sy;
			m[3] *= sy;
		}

		function matrixSave() {
			lastMatrix.push(m);
		}

		function matrixRestore() {
			m = lastMatrix.pop();
		}

		function transformPoint(px, py) {
			var x = px;
			var y = py;
			px = x * m[0] + y * m[2] + m[4];
			py = x * m[1] + y * m[3] + m[5];
			return {
				x: px,
				y: py
			}
		}

		function matrixTranslate(x, y) {
			m[4] += m[0] * x + m[2] * y;
			m[5] += m[1] * x + m[3] * y;
		}

		function fitToContainer(canvas) {
			canvas.width = canvas.offsetWidth;
			canvas.height = canvas.offsetHeight;
		}

		function clear() {
			context.save();
			context.setTransform(1, 0, 0, 1, 0, 0);
			context.clearRect(0, 0, canvas.width, canvas.height);
			context.restore();
		}

		function getMousePos(canvas, e) {
			var rect = canvas.getBoundingClientRect();
			return {
				x: e.clientX - rect.left,
				y: e.clientY - rect.top
			};
		}
		var dragStart;
		var dragEnd;
		draw()
		canvas.addEventListener('mousedown', function(event) {
			var cit = transformPoint(event.pageX, event.pageY);
			dragStart = {
				x: cit.x - canvas.offsetLeft,
				y: cit.y - canvas.offsetTop
			}
			drag = true;
		})

		canvas.addEventListener('mousemove', function(event) {
			if (drag) {
				var cit = transformPoint(event.pageX, event.pageY);
				dragEnd = {
					x: cit.x - canvas.offsetLeft,
					y: cit.y - canvas.offsetTop
				}
				var pos = getMousePos(this, event); /// provide this canvas and event
				window.x = pos.x,
					window.y = pos.y;
				console.log("active_key", active_key)
				if (active_id != "") {
					text[active_key].x = pos.x
					text[active_key].y = pos.y
					document.getElementById("label_x_" + (active_key)).value = Math.round(pos.x / 3.89, 2);
					document.getElementById("label_y_" + (active_key)).value = Math.round(pos.y / 3.85, 2);
				}
				clear()
				draw()
				dragStart = dragEnd
			}
		})

		canvas.addEventListener('mouseup', function(event) {
			drag = false;
		})

		canvas.addEventListener('dblclick', function(event) {
			active_id = '';
			active_key = '';
			clear();
			draw();
			drag = false;
		});

		document.getElementById("page").addEventListener("change", function(){
			clear();
			draw();
			drag = false;
		});
	}

	function active_on(id) {
		text.forEach(function(item, key) {
			text[key].is_lock = true;
			if (item.id == id) {
				text[key].is_lock = false;
				active_id = id;
				active_key = key;
				
			}
		});
		var elems = document.querySelectorAll("li.label");
		[].forEach.call(elems, function(el) {
			el.classList.remove("active");
		});
		document.getElementById(id).setAttribute("class", "label active");
	}

	function add() {
		pages = $('#page').val();
		m = $('.label').length;
		var new_item = {
			id: "label_" + m,
			x: 500,
			y: 200,
			width: 30,
			height: 5,
			color: "red",
			font: "14px THSarabunNew",
			textAlign: "center",
			text: "text",
			is_lock: true,
			page: pages,
			strokeStyles: "#000"
		};
		var ul = document.getElementById("label_list");
		var li = document.createElement("li");
		li.setAttribute("onclick", "active_on('label_" + m + "')");
		li.setAttribute("class", "label");
		li.setAttribute("value", pages);
		li.id = 'label_' + m;
		li.className = 'label';
		li.innerHTML = '<input type="hidden" name="id_' + m + '" value="">' +
			'<input type="hidden" name="page_no_' + m + '" value="' + pages + '">' +
			'<select name="data_name_' + m + '" id="data_name_' + m + '" onchange="c_txt(' + "'" + 'label_' + m + "'" + ',value)">' +
			'<input type="hidden" name="label_x_' + m + '" id="label_x_' + m + '"> <input type="hidden" name="label_y_' + m + '" id="label_y_' + m + '">' +
			'<input type="font_size_' + m + '" value="14" name="font_size_' + m + '" id="font_size_' + m + '"onkeyup="c_font_size(' + "'" + 'label_' + m + '' + "'" + ',value,' + pages + ');">' +
			'<select name="fonts_' + m + '" id="fonts_' + m + '" onchange="c_font(' + "'" + 'label_' + m + '' + "'" + ',value,' + pages + ')">' +
			'<option value="THSarabunNew" selected>THSarabunNew</option></select><input type="hidden" name="h_' + m + '" value="5"><input type="" name="w_' + m + '" onkeyup="change_w(' + "'" + 'label_' + m + "'" + ', value)" value = "30" > ' +
			'<select name="point_' + m + '" id="point_' + m + '" onchange="c_point(' + "'" + 'label_' + m + '' + "'" + ',value,' + m + ')">' +
			'<option value="C" selected>กึ่งกลาง</option>'+
			'<option value="L">ชิดซ้าย</option>'+
			'<option value="R">ชิดขวา</option></select>'+
			'<a onclick="remove(' + "'" + 'label_' + m + '' + "'" + ',' + m + ');"><i class="fas fa-minus" style="color: red;"></i></a>';
		ul.appendChild(li);
		text.push(new_item);
		get_text(m);
	}

	function add_box() {
		pages = $('#page').val();
		m = $('.label').length;
		var new_item = {
			id: "label_" + m,
			x: 500,
			y: 200,
			width: 5,
			height: 5,
			textAlign: "center",
			text: "O",
			is_lock: true,
			page: pages,
			strokeStyles: "#000"
		};
		var ul = document.getElementById("label_list");
		var li = document.createElement("li");
		li.setAttribute("onclick", "active_on('label_" + m + "')");
		li.setAttribute("class", "label");
		li.setAttribute("value", pages);
		li.id = 'label_' + m;
		li.className = 'label';
		li.innerHTML = '<input type="hidden" name="id_' + m + '" value="">' +
			'<input type="hidden" name="page_no_' + m + '" value="' + pages + '">' +
			'<select name="data_name_' + m + '" id="data_name_' + m + '" onchange="c_txt(' + "'" + 'label_' + m + "'" + ',value)">' +
			'<input type="hidden" name="label_x_' + m + '" id="label_x_' + m + '"> <input type="hidden" name="label_y_' + m + '" id="label_y_' + m + '">' +
			'<input type="font_size_' + m + '" value="14" name="font_size_' + m + '" id="font_size_' + m + '"onkeyup="c_font_size(' + "'" + 'label_' + m + '' + "'" + ',value,' + pages + ');">' +
			'<select name="fonts_' + m + '" id="fonts_' + m + '" onchange="c_font(' + "'" + 'label_' + m + '' + "'" + ',value,' + pages + ')">' +
			'<option value="THSarabunNew" selected>THSarabunNew</option></select><input type="hidden" name="h_' + m + '" value="5"><input type="" name="w_' + m + '" onkeyup="change_w(' + "'" + 'label_' + m + "'" + ', value)" value = "30" > ' +
			'<select name="point_' + m + '" id="point_' + m + '" onchange="c_point(' + "'" + 'label_' + m + '' + "'" + ',value,' + m + ')">' +
			'<option value="C" selected>กึ่งกลาง</option>'+
			'<option value="L">ชิดซ้าย</option>'+
			'<option value="R">ชิดขวา</option></select>'+
			'<a onclick="remove(' + "'" + 'label_' + m + '' + "'" + ',' + m + ');"><i class="fas fa-minus" style="color: red;"></i></a>';
		ul.appendChild(li);
		text.push(new_item);
		get_box(m);
	}

	function get_text(m) {
		$.ajax({
			type: "get",
			url: "get_text",
			dataType: 'json',
			success: function(data) {
				$('#data_name_' + m).append(
					'<option value="" disabled hidden selected> กรุณา เลือกข้อมูล</option>'
				)
				for (let index = 0; index < data['text_pdf_type'].length; index++) {
						$('#data_name_' + m).append(
							'<option disabled style="background:lightgray;font-size:14pt">' +  data['text_pdf_type'][index]['text_type'] + '</option>'
						)
					for (let in2 = 0; in2 < data['text_pdf'].length; in2++) {
					if (data['text_pdf_type'][index]['id']==data['text_pdf'][in2]['type_id']) {
							$('#data_name_' + m).append(
								'<option value="' +data['text_pdf'][in2]['code'] + '">' + data['text_pdf'][in2]['ref'] + '</option>'
							)
						}
					}
				}
			}
		});
	}

	function get_box(m) {
		$.ajax({
			type: "get",
			url: "get_box",
			dataType: 'json',
			success: function(data) {
				$('#data_name_' + m).append(
					'<option value="" disabled hidden selected> กรุณา เลือกข้อมูล</option>'
				)
				for (let index2 = 0; index2 < data.length; index2++) {
					$('#data_name_' + m).append(
						'<option value="' + data[index2]['code'] + '">' + data[index2]['ref'] + '</option>'
					)
				}
			}
		});
	}

	// เรียกรายละเอียดข้อมูล
	$.ajax({
		type: "get",
		url: "get_text",
		dataType: 'json',
		success: function(data) {
			num = ($('li').length);
			for (let num_ber = 0; num_ber <= num; num_ber++) {
				for (let index = 0; index < data['text_pdf_type'].length; index++) {
						$('#data_name_' + num_ber).append(
							'<option disabled style="background:lightgray;font-size:14pt">' +  data['text_pdf_type'][index]['text_type'] + '</option>'
						)
					for (let in2 = 0; in2 < data['text_pdf'].length; in2++) {
					if (data['text_pdf_type'][index]['id']==data['text_pdf'][in2]['type_id']) {
							$('#data_name_' + num_ber).append(
								'<option value="' +data['text_pdf'][in2]['code'] + '">' + data['text_pdf'][in2]['ref'] + '</option>'
							)
						}
					}
				}
			}
			exam_data = data;
		}
	});

	//เรียกชื่อประเภทสัญญา
	$.ajax({
		type: "get",
		url: "get_loan",
		dataType: 'json',
		success: function(data) {
			for (let index = 0; index < data.length; index++) {
				if (data[index]['id'] == '<?php echo $path_data[0]['type_pdf_id'] ?>') {
					$('#type_pdf').append(
						'<option value="' + data[index]['id'] + '" selected>' + data[index]['loan_type'] + '</option>'
					)
				} else {
					$('#type_pdf').append(
						'<option value="' + data[index]['id'] + '">' + data[index]['loan_type'] + '</option>'
					)
				}
			}
		}
	});

	function c_txt(id, val) {
		text.forEach(function(item, key) {
			text[key].is_lock = true;
			if (item.id == id) {
				exam_data.forEach(a => {
					if (a['code'] == val) {
						text[key]['text'] = a['detail_data']; // alert (a['ref']);
					}
				});
			}
		});
	}

	function c_font_size(id, val, num) {
		text.forEach(function(item, key) {
			font = $("#fonts_" + num).val();
			text[key].is_lock = true;
			if (item.id == id) {
				text[key]['font'] = (val + "px " + font);
			}
		});
	}

	function c_font(id, font, num) {
		text.forEach(function(item, key) {
			val = $("#font_size_" + num).val();
			text[key].is_lock = true;
			if (item.id == id) {
				text[key]['font'] = (val + "px " + font);
			}
		});
	}

	function c_point(id, point, num) {
		val = $("#point_" + num).val();
		text.forEach(function(item, key) {
			if (val=="C") {
				val='center';
			}else if(val=="L"){
				val='right';
			}else if(val=="R"){
				val='left';
			}
			text[key].is_lock = true;
			if (item.id == id) {
				text[key]['textAlign'] = (val);
			}
		});
	}
</script>