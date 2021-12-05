<style>
    .row:after,
    .row:before {
        clear: unset !important;
    }

    thead {
        background-color: #d0d0d0;
    }

    tbody {
        background-color: white;
    }
    .btn{
        width: fit-content;
    }

</style>
<div class="container">
    <div class="text-center">
        <h1 style="margin-left: 0;">
            Document Manage
        </h1>
    </div>
    
    <div style="text-align:end;padding-bottom:15px">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#AddModal">
            เพิ่มเอกสาร
        </button>
    </div>
    <table id="example" class="table table-striped">
        <thead>
            <tr>
                <th >ลำดับ <?php echo $message[0]  ?></th>
                <th hidden>id</th>
                <th>รายละเอียด</th>
                <th>ประเภทการกุ้</th>
                <th>ไฟล์ที่อยู่</th>
                <th>สถานะ</th>
                <th style="width:18%">action</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            foreach ($result as $data) { ?>

                <tr>
                    <td><?php echo $i  ?></td>
                    <td hidden><?php echo $data['id'] ?></td>
                    <td><?php echo $data['details'] ?></td>
                    <td><?php echo $data['type_loan'] ?></td>
                    <td><?php echo $data['path_data'] ?></td>
                    <td><?php echo $data['status'] ?></td>
                    <td>
                        <button class="btn" onclick="get_modal_perview(<?php echo $data['id'] ?>)"><i class="fas fa-eye"></i></button>
                        <a class="btn_edit btn btn-warning" href="edit_doc?id=<?php echo $data['id'] ?>">edit</a>
                        <button class="btn btn-danger" onclick="remove('<?php echo $data['id'] ?>','<?php echo $data['details'] ?>');">delete</button>
                    </td>
                </tr>
            <?php $i++;
            } ?>
        </tbody>

    </table>
</div>
<!-- Modal -->
<div class="modal fade" id="AddModal" tabindex="-1" role="dialog" aria-labelledby="AddModalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="display: flex; justify-content:space-between">
                <h1 class="modal-title" id="AddModalLabel">เพิ่มเอกสาร</h1>
                <button style="margin-right: -40%;" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="" style="display: flex;">
                <div style="height: 80vh;width:25vw;">

                    <!-- Form -->
                    <?php echo form_open_multipart('Loan_document/add_file_pdf'); ?>
                    <span>ชื่อไฟล์<input class="form-control" type="text" name="file_name" id="file_name"></span>
                    <br>
                    <span>รูปแบบสัญญา </span>
                    <select class="form-control" id="type_pdf" name="type_pdf" onchange="change(value);">
                        <option value="" hidden disabled selected>เลือกประเภทเอกสาร</option>
                    </select>
                    <br>
                    <span>ประเภทเอกสาร</span>
                    <select class="form-control" id="loan_type" name="loan_type" require>
                        <option value="" hidden disabled selected>เลือกประเภทเอกสาร</option>
                        <option value="11">หนังสือสัญญาเงินกู้</option>
                        <option value="12">หนังสือค้ำประกัน</option>
                        <option value="13">หนังสือรับรองเงินเดือน</option>
                    </select>
                    <br>
                    <input class="form-control" type="file" id="pdf" name="pdf" />
                    <div style="text-align: center;">
                        <button class="m-t-2 btn btn-primary" value="upload">บันทึก</button>
                    </div>
                    </form>
                    <!-- END FORM -->

                </div>
                <div id="pdfViewer" style="height: 80vh;width:50vw;overflow:auto">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- MODAL PREVIEW-->
<div class="modal fade" id="PerviewModal" aria-labelledby="PerviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="PerviewModalLabel">Modal title</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="Perview_data" style="height: 80vh;">
                <iframe src="/assets/document/petition_normal_pdf.pdf" style="width: 100%;height:100%;"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL -->
<script src="https://kit.fontawesome.com/2e9f1235d6.js" crossorigin="anonymous"></script>

<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>

<script>
    var data_loan;
    $(document).ready(function() {
        $('#example').DataTable({
            "bLengthChange": false,
            'iDisplayLength': 15,
        });
    });

    function get_modal_perview(id) {
        $('#Perview_data iframe').remove();
        $.ajax({
            type: "get",
            url: "get_iframe?id=" + id,
            dataType: 'json',
            success: function(data) {
                $('.modal-content h3').text(data[0]['details'])
                $('#Perview_data').append(
                    '<iframe src="/assets/document/loan_request/'+ data[0]['path_data'] + '#toolbar=0" style="width: 100%;height:100%;"></iframe>'
                )
                $('#PerviewModal').modal('show')
            }
        });
    }

    function remove(id, detail) {
        Swal.fire({
                title: 'คุณต้องการลบ',
                text: 'ไฟล์ชื่อ ' + detail,
                icon: 'error',
                showCancelButton: true,
                cancelButtonText: 'ยกเลิก',
                confirmButtonText: 'ใช่, ลบไฟล์',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "get",
                        url: "remove_file_loan?id=" + id,
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
                                title: 'ลบไฟล์เรียบร้อย'
                            })
                        }
                    });
                    location.reload();
                }
            })
    }

    // เรียกชื่อประเภทสัญญา
    $.ajax({
        type: "get",
        url: "get_loan",
        dataType: 'json',
        success: function(data) {
            data_loan = data;
            for (let index = 0; index < data.length; index++) {
                $('#type_pdf').append(
                    '<option value="' + data[index]['id'] + '">' + data[index]['loan_type'] + '</option>'
                )
            }

        }
    });

    function change(val) {
        $('#loan_type').text("");
        $('#loan_type').append(
            '<option value="" hidden disabled selected>เลือกประเภทเอกสาร</option><option value="' + data_loan[(val - 1)]['id'] + '">' + data_loan[(val - 1)]['loan_type'] + '</option>' +
            '<option value="11">หนังสือสัญญาเงินกู้</option><option value="12">หนังสือค้ำประกัน</option>' +
            '<option value="13">หนังสือรับรองเงินเดือน</option>'
        )

    }

    function check_submit() {

        type_pdfs = $('#type_pdf').val();
        loan_types = $('#loan_type').val();
        console.log(type_pdfs + "  " + loan_types)
        if (type_pdfs == null) {
            stop();
        }
        if (loan_types == null) {
            stop();
        }
        stop();
    }

    var pdfjsLib = window['pdfjs-dist/build/pdf'];

    $("#pdf").on("change", function(e) {
        $('#pdfViewer').text("");
        var file = e.target.files[0]
        if (file.type == "application/pdf") {
            var fileReader = new FileReader();
            fileReader.onload = function() {
                var pdfData = new Uint8Array(this.result);
                // Using DocumentInitParameters object to load binary data.
                var loadingTask = pdfjsLib.getDocument({
                    data: pdfData
                });
                loadingTask.promise.then(function(pdf) {
                    // console.log('PDF loaded');
                    num = pdf.numPages;
                    // Fetch the first page
                    var pageNumber = 2;
                    for (let pageNumber = 1; pageNumber <= num;) {
                        pdf.getPage(pageNumber).then(function(page) {
                            // console.log('Page loaded');
                            var scale = 1.5;
                            var viewport = page.getViewport({
                                scale: scale
                            });

                            // Prepare canvas using PDF page dimensions
                            var canvas = document.createElement("canvas");
                            canvas.style.display = "block";
                            canvas.style.height = "70vh";
                            canvas.style.width = "30vw";
                            var context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;

                            // Render PDF page into canvas context
                            page.render({
                                canvasContext: context,
                                viewport: viewport
                            });
                            document.getElementById('pdfViewer').appendChild(canvas);
                            if (pdf !== null && pageNumber <= num) {
                                pdf.getPage(pageNumber);
                            }

                        });
                        pageNumber++;
                    }

                }, function(reason) {
                    // PDF loading error
                    // console.error(reason);
                });
            };
            fileReader.readAsArrayBuffer(file);
        }
    });
</script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>