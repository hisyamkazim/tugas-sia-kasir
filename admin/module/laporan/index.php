 <?php 
	$bulan_tes =array(
		'01'=>"Januari",
		'02'=>"Februari",
		'03'=>"Maret",
		'04'=>"April",
		'05'=>"Mei",
		'06'=>"Juni",
		'07'=>"Juli",
		'08'=>"Agustus",
		'09'=>"September",
		'10'=>"Oktober",
		'11'=>"November",
		'12'=>"Desember"
	);
?>
<div class="row">
	<div class="col-md-12">
		<h4>
			<?php if(!empty($_GET['cari'])){ ?>
			Data Laporan Penjualan Bulan <?= $bulan_tes[$_POST['bln']];?> Tahun <?= $_POST['thn'];?>
			<?php }elseif(!empty($_GET['hari'])){?>
			Data Laporan Penjualan Tanggal <?= $_POST['hari'];?>
			<?php }else{?>
			Data Laporan Penjualan Tanggal <?= date('Y-m-d');?>
			<?php }?>
		</h4>
		<br />
		<div class="card">
			<div class="card-header">
				<a href="index.php?page=laporan" class="btn btn-success">
					<i class="fa fa-spinner"></i> Refresh</a>

				<?php if(!empty($_GET['cari'])){?>
					<a href="excel.php?cari=yes&bln=<?=$_POST['bln'];?>&thn=<?=$_POST['thn'];?>"
					class="btn btn-info"><i class="fa fa-download"></i>
					Excel</a>
				<?php } else if(!empty($_GET['hari'])){?>
					<a href="excel.php?hari=yes&tgl=<?=$_POST['hari'];?>"
					class="btn btn-info"><i class="fa fa-download"></i>
					Excel</a>
				<?php } else{?>
					<a href="excel.php" class="btn btn-info"><i class="fa fa-download"></i>
					Excel</a>
				<?php }?>
			</div>
			<div class="card-body p-0">
				<form method="post" action="index.php?page=laporan&hari=yes">
					<table class="table table-striped">
						<tr>
							<th>
								Pilih Tanggal
							</th>
							<th>
								Aksi
							</th>
						</tr>
						<tr>
							<td>
								<input required type="date" value="<?php 
								if(!empty($_GET['cari'])){
									echo "";
								}elseif(!empty($_GET['hari'])){
									echo $_POST['hari'];
								}else{
									echo date("Y-m-d");
								}
								?>" class="form-control" name="hari">
							</td>
							<td>
								<input type="hidden" name="periode" value="ya">
								<button class="btn btn-primary">
									<i class="fa fa-search"></i> Cari
								</button>
							</td>
						</tr>
					</table>
				</form>
				<form method="post" action="index.php?page=laporan&cari=yes">
					<table class="table table-striped">
						<tr>
							<th>
								Pilih Bulan
							</th>
							<th>
								Pilih Tahun
							</th>
							<th>
								Aksi
							</th>
						</tr>
						<tr>
							<td>
								<select name="bln" class="form-control" required>
									<option value="" selected disabled>Bulan</option>
									<?php
								$bulan=array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
								$jlh_bln=count($bulan);
								$bln1 = array('01','02','03','04','05','06','07','08','09','10','11','12');
								$no=1;
								for($c=0; $c<$jlh_bln; $c+=1){ ?>
									<option value="<?php echo $bln1[$c]; ?>" <?php echo $_POST['bln'] == $bln1[$c] ? "selected" : ""; ?>> <?php echo $bulan[$c]; ?></option>
								<?php $no++;}
							?>
								</select>
							</td>
							<td>
							<?php
								$now=date('Y');
								echo "<select name='thn' class='form-control' required>";
								echo '<option value="" selected disabled>Tahun</option>';
								for ($a=2017;$a<=$now;$a++){ ?>
									<option <?php echo $_POST['thn'] == $a ? "selected" : ""; ?> value="<?php echo $a; ?>"><?php echo $a; ?></option>
								<?php }
								echo "</select>";
							?>
							</td>
							<td>
								<input type="hidden" name="periode" value="ya">
								<button class="btn btn-primary">
									<i class="fa fa-search"></i> Cari
								</button>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
         <br />
         <br />
         <!-- view barang -->
		<div class="card">
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered w-100 table-sm" id="example1">
						<thead>
							<tr style="background:#DFF0D8;color:#333;">
								<th> No</th>
								<th> ID Barang</th>
								<th> Nama Barang</th>
								<th style="width:10%;"> Satuan</th>
								<th style="width:10%;"> Jumlah</th>
								<th style="width:10%;"> Harga Beli</th>
								<th style="width:10%;"> Harga Jual</th>
								<th style="width:10%;"> Pajak</th>
								<th> Kasir</th>
								<th> Tanggal Input</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$no=1; 
								if(!empty($_GET['cari'])){
									$periode = $_POST['bln'].'-'.$_POST['thn'];
									$no=1; 
									$jumlah = 0;
									$bayar = 0;
									$hasil = $lihat -> periode_jual($periode);
								}elseif(!empty($_GET['hari'])){
									$hari = $_POST['hari'];
									$no=1; 
									$jumlah = 0;
									$bayar = 0;
									$hasil = $lihat -> hari_jual($hari);
								}else{
									$hasil = $lihat -> hari_jual(date('Y-m-d'));
								}
							?>
							<?php 
								$bayar = 0;
								$jumlah = 0;
								$modal = 0;
								foreach($hasil as $isi){ 
									$bayar += $isi['total'] + $isi['pajak'];
									$modal += $isi['harga_beli']* $isi['jumlah'];
									$ppn += $isi['pajak'];
									$jumlah += $isi['jumlah'];
							?>
							<tr>
								<td><?php echo $no;?></td>
								<td><?php echo $isi['id_barang'];?></td>
								<td><?php echo $isi['nama_barang'];?></td>
								<td><?php echo $isi['satuan_barang'];?> </td>
								<td><?php echo $isi['jumlah'];?> </td>
								<td>Rp.<?php echo number_format($isi['harga_beli']* $isi['jumlah']);?>,-</td>
								<td>Rp.<?php echo number_format($isi['total'] + $isi['pajak']);?>,-</td>
								<td>Rp.<?php echo number_format($isi['pajak']);?>,-</td>
								<td><?php echo $isi['nm_member'];?></td>
								<td><?php echo $isi['tanggal_input'];?></td>
							</tr>
							<?php $no++; }?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="4">Total Terjual</td>
								<th><?php echo $jumlah;?></td>
								<th>Rp.<?php echo number_format($modal);?>,-</th>
								<th>Rp.<?php echo number_format($bayar);?>,-</th>
								<th>Rp.<?php echo number_format($ppn);?>,-</th>
								<th style="background:#0bb365;color:#fff;">Keuntungan</th>
								<th style="background:#0bb365;color:#fff;">
									Rp.<?php echo number_format($bayar-$modal-$ppn);?>,-</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
     </div>
 </div>