<div class="content-wrapper">
    <section class="content-header">
    <h1>Pengelolaan Data <?= ucwords($this->setting->sebutan_dusun) ?></h1>
        <ol class="breadcrumb">
            <li><a href="<?= site_url('beranda') ?>"><i class="fa fa-home"></i> Beranda</a></li>
            <li><a href="<?= site_url('wilayah/clear') ?>"> Daftar <?= ucwords($this->setting->sebutan_dusun) ?></a></li>
            <li class="active">Data <?= ucwords($this->setting->sebutan_dusun) ?></li>
        </ol>
    </section>
    <section class="content" id="maincontent">
        <div class="box box-info">
            <div class="box-header with-border">
                <a href="<?= site_url('wilayah/clear') ?>" class="btn btn-social btn-flat btn-info btn-sm btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block"  title="Kembali Ke Daftar Wilayah">
                    <i class="fa fa-arrow-circle-left "></i>Kembali ke Daftar <?= ucwords($this->setting->sebutan_dusun) ?>
                </a>
            </div>
            <form id="validasi" action="<?= $form_action ?>" method="POST" class="form-horizontal">
                <div class="box-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dusun">Nama  <?= ucwords($this->setting->sebutan_dusun) ?></label>
                        <div class="col-sm-7">
                            <input id="dusun" class="form-control input-sm nama_terbatas required" maxlength="50" type="text" placeholder="Nama <?= ucwords($this->setting->sebutan_dusun) ?>" name="dusun" value="<?= $dusun['dusun'] ?>">
                        </div>
                    </div>
                    <?php if ($dusun->kepala): ?>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="kepala_lama">Kepala  <?= ucwords($this->setting->sebutan_dusun) ?> Sebelumnya</label>
                            <div class="col-sm-7">
                                <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                    <strong><?= $dusun->kepala->nama ?></strong>
                                    <br/>NIK - <?= $dusun->kepala->nik ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="id_kepala">NIK / Nama Kepala  <?= ucwords($this->setting->sebutan_dusun) ?></label>
                        <div class="col-sm-7">
                            <select class="form-control select2 select2-infinite" data-url="wilayah/apipendudukwilayah" style="width: 100%;" id="id_kepala" name="id_kepala">
                                <option selected="selected">-- Silakan Masukan NIK / Nama--</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="reset" class="btn btn-social btn-flat btn-danger btn-sm"><i class="fa fa-times"></i> Batal</button>
                    <button type="submit" class="btn btn-social btn-flat btn-info btn-sm pull-right"><i class="fa fa-check"></i> Simpan</button>
                </div>
            </form>
        </div>
    </section>
</div>
<?php $this->load->view('global/validasi_form'); ?>
