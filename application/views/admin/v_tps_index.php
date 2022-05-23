<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800"><?= $title; ?></h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="float-right">
                <a href="<?= base_url('admin/tps/add') ?>" class="btn btn-primary btn-icon-split btn-sm">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span class="text">Add Record</span>
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <!-- <th>No</th> -->
                            <th>Gambar</th>
                            <th>Nama TPS</th>
                            <th>Alamat</th>
                            <th>Telp</th>
                            <!-- <th>Diperbaharui</th> -->
                            <th width="13%">Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($tps)) {
                            $baseurl = base_url();
                            $no = 1;

                            foreach ($tps as $key => $value) {
                                $id = encode($value['id_tps']);
                                echo "<tr data-id='$id'>";
                                // echo "<td>$no</td>";
                                echo "<td class='text-center'><img class='img-thumbnail sampul' src='" . base_url("uploads/img/" . $value['gambar']) . "'></td>";
                                echo "<td>$value[nama_tps]</td>";
                                echo "<td>$value[alamat]</td>";
                                echo "<td>$value[telp]</td>";
                                // echo "<td>" . tgl_jam_indo($value["updated_at"], false) . "</td>";
                                echo "<td>";

                                echo "<select id='status' name='status' class='form-control change-status'>";
                                foreach (status_tps() as $k => $v) {
                                    $s = isset($value['status']) ? ($value['status'] == $k ? "selected" : "") : "";
                                    echo "<option value='$k' $s>$v</option>";
                                }

                                echo "</select>";
                                echo "</td>";
                                echo "<td>";
                                echo "<a href='" . $baseurl . "detail-tps/$id' class='btn btn-info btn-sm'><i class='fas fa-eye'></i></a>";
                                echo "<a href='" . $baseurl . "admin/tps/edit/$id' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></a>";
                                echo "<a href='javascript:void(0)' data-id='" . encode($value['id_tps']) . "' class='btn btn-danger btn-sm btn-delete'><i class='fas fa-trash'></i></a>";
                                echo "</td>";
                                echo "</tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>Belum ada data..!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <form id="form-delete" action="#">
            <?= csrf_field("csrf_delete"); ?>
        </form>
        <div class="card-footer float-right">
            <?= $pagin; ?>
        </div>
    </div>
</div>
<?= csrf_field("csrf_protection"); ?>
<style>
    .sampul {
        width: 100px;
    }

    .table>tbody>tr>* {
        vertical-align: middle;
    }
</style>
<script>
    $(document).ready(function() {

        $(".change-status").change(function(e) {
            e.preventDefault();
            let csrfName = $("#csrf_protection").attr("name");
            let csrfHash = $("#csrf_protection").val();
            let statusV = $(this).val();
            let tps_id = $(this).closest("tr").data("id");
            $.ajax({
                type: "post",
                url: "<?= base_url("admin/tps/ganti-status/"); ?>" + tps_id,
                data: {
                    [csrfName]: csrfHash,
                    'status': statusV
                },
                dataType: "json",
                success: function(response) {

                    if (response.csrf) {
                        $("#csrf_protection").val(response.csrf);
                        $("#csrf_delete").val(response.csrf);
                    }

                    if (response.success) {
                        Swal.fire("Suksess..", response.pesan, "success");
                    } else {
                        Swal.fire("Opps..", response.pesan, "warning");
                    }
                }
            });

        });
        const baseurl = "<?= base_url("admin/tps/delete"); ?>";
        $(".btn-delete").click(function(e) {
            e.preventDefault();
            let id = $(this).data("id");

            Swal.fire({
                title: "Konfirmasi Ulang",
                text: "Yakin menghapus data?",
                icon: "warning",
                showCancelButton: true,
                reverseButtons: true,
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: baseurl + '/' + id,
                        data: $("#form-delete").serialize(),
                        dataType: "json",
                        beforeSend: function() {
                            $(this).addClass("disabled");
                        },
                        complete: function() {
                            $(this).removeClass("disabled");
                        },
                        success: function(response) {

                            if (response.csrf) {
                                $("#csrf_delete").val(response.csrf);
                            }

                            if (response.error) {
                                Swal.fire("Terjadi galat..!", response.error.pesan, "warning");
                            }
                            if (response.success) {
                                Swal.fire("Sukses..!", response.success.pesan, "success").then(() => {
                                    location.reload();
                                });
                            }

                        }
                    });
                }
            });

        });
    });
</script>