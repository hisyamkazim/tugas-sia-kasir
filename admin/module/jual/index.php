 <!--sidebar end-->
      
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
<?php 
	$id = $_SESSION['admin']['id_member'];
	$hasil = $lihat -> member_edit($id);
?>
	<h4>Keranjang Penjualan</h4>
	<br>
	<?php if(isset($_GET['success'])){?>
	<div class="alert alert-success">
		<p>Edit Data Berhasil !</p>
	</div>
	<?php }?>
	<?php if(isset($_GET['remove'])){?>
	<div class="alert alert-danger">
		<p>Hapus Data Berhasil !</p>
	</div>
	<?php }?>
	<div class="row">
		<div class="col-sm-4">
			<div class="card card-primary mb-3">
				<div class="card-header bg-primary text-white">
					<h5><i class="fa fa-search"></i> Cari Barang</h5>
				</div>
				<div class="card-body">
					<input type="text" id="cari" class="form-control" name="cari" placeholder="Masukan : Kode / Nama Barang  [ENTER]">
				</div>
			</div>
		</div>
		<div class="col-sm-8">
			<div class="card card-primary mb-3">
				<div class="card-header bg-primary text-white">
					<h5><i class="fa fa-list"></i> Hasil Pencarian</h5>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<div id="hasil_cari"></div>
						<div id="tunggu"></div>
					</div>
				</div>
			</div>
		</div>
		

		<div class="col-sm-12">
			<div class="card card-primary">
				<div class="card-header bg-primary text-white">
					<h5><i class="fa fa-shopping-cart"></i> KASIR
					<a class="btn btn-danger float-right" 
						onclick="javascript:return confirm('Apakah anda ingin reset keranjang ?');" href="fungsi/hapus/hapus.php?penjualan=jual"><i class="fa fa-spinner"></i>
						<b>RESET KERANJANG</b></a>
					</h5>
				</div>
				<div class="card-body">
					<div id="keranjang" class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<td><b>Tanggal</b></td>
								<td><input type="text" readonly="readonly" class="form-control" value="<?php echo date("j F Y, G:i");?>" name="tgl"></td>
							</tr>
						</table>
						<table class="table table-bordered w-100" id="example1">
							<thead>
								<tr>
									<td> No</td>
									<td> Nama Barang</td>
									<td style="width:10%"> Jumlah</td>
									<td> Harga</td>
									<td> Total</td>
									<td> Kasir</td>
									<td> Aksi</td>
								</tr>
							</thead>
							<tbody>
								<?php $total_bayar=0; $no=1; $hasil_penjualan = $lihat -> penjualan();?>
								<?php foreach($hasil_penjualan  as $isi){?>
								<tr>
									<td><?php echo $no;?></td>
									<td><?php echo $isi['nama_barang'];?></td>
									<td>
										<!-- aksi ke table penjualan -->
										<form method="POST" action="fungsi/edit/edit.php?jual=jual">
												<input type="number" name="jumlah" value="<?php echo $isi['jumlah'];?>" class="form-control">
												<input type="hidden" name="id" value="<?php echo $isi['id_penjualan'];?>" class="form-control">
												<input type="hidden" name="id_barang" value="<?php echo $isi['id_barang'];?>" class="form-control">
											</td>
											<td>Rp.<?php echo number_format($isi['total']/$isi['jumlah']);?>,-</td>
											<td>Rp.<?php echo number_format($isi['total']);?>,-</td>
											<td><?php echo $isi['nm_member'];?></td>
											<td>
												<button type="submit" class="btn btn-warning"><i class="fa fa-cog"></i> Update</button>
										</form>
										<!-- aksi ke table penjualan -->
										<a href="fungsi/hapus/hapus.php?jual=jual&id=<?php echo $isi['id_penjualan'];?>&brg=<?php echo $isi['id_barang'];?>
											&jml=<?php echo $isi['jumlah']; ?>"  class="btn btn-danger"><i class="fa fa-trash"></i> Hapus
										</a>
									</td>
								</tr>
								<?php $no++; $total_bayar += $isi['total'];}?>
							</tbody>
					</table>
					<br/>
					<?php $hasil = $lihat -> jumlah(); ?>
					<div id="kasirnya">
						<table class="table table-stripped">
							<?php
							// proses bayar dan ke nota
							if(!empty($_GET['nota'] == 'yes')) {
								$total = $_POST['total_bayar'];
								$bayar = $_POST['bayar'];
								if(!empty($bayar))
								{
									$hitung = $bayar - $total;
									if($bayar >= $total)
									{
										$id_barang = $_POST['id_barang'];
										$id_member = $_POST['id_member'];
										$jumlah = $_POST['jumlah'];
										$total = $_POST['total1'];
										$pajak = $_POST['total2'];
										$tgl_input = $_POST['tgl_input'];
										$periode = $_POST['periode'];
										$jumlah_dipilih = count($id_barang);
										
										for($x=0;$x<$jumlah_dipilih;$x++){

											$d = array($id_barang[$x],$id_member[$x],$jumlah[$x],$total[$x],$pajak[$x],$tgl_input[$x],$periode[$x]);
											$sql = "INSERT INTO nota (id_barang,id_member,jumlah,total,pajak,tanggal_input,periode) VALUES(?,?,?,?,?,?,?)";
											$row = $config->prepare($sql);
											$row->execute($d);

											// ubah stok barang
											$sql_barang = "SELECT * FROM barang WHERE id_barang = ?";
											$row_barang = $config->prepare($sql_barang);
											$row_barang->execute(array($id_barang[$x]));
											$hsl = $row_barang->fetch();
											
											$stok = $hsl['stok'];
											$idb  = $hsl['id_barang'];

											$total_stok = $stok - $jumlah[$x];
											// echo $total_stok;
											$sql_stok = "UPDATE barang SET stok = ? WHERE id_barang = ?";
											$row_stok = $config->prepare($sql_stok);
											$row_stok->execute(array($total_stok, $idb));
										}
										echo '<script>alert("Belanjaan Berhasil Di Bayar !");</script>';
									}else{
										echo '<script>alert("Uang Kurang ! Rp.'.$hitung.'");</script>';
									}
								}
							}
							?>
							<!-- aksi ke table nota -->
							<form method="POST" action="index.php?page=jual&nota=yes#kasirnya">
								<?php foreach($hasil_penjualan as $isi){;?>
									<input hidden name="id_barang[]" value="<?php echo $isi['id_barang'];?>">
									<input hidden name="id_member[]" value="<?php echo $isi['id_member'];?>">
									<input hidden name="jumlah[]" value="<?php echo $isi['jumlah'];?>">
									<input hidden name="total1[]" value="<?php echo $isi['total'];?>">
									<input hidden name="total2[]" value="<?php echo $isi['pajak'];?>">
									<input hidden name="tgl_input[]" value="<?php echo $isi['tanggal_input'];?>">
									<input hidden name="periode[]" value="<?php echo date('m-Y');?>">
								<?php $no++; }?>
								<tr>
									<td>Total</td>
									<td><input type="text" class="form-control" name="total" value="<?php echo $total_bayar;?>" readonly></td>
									<td>Total Pajak (10%)</td>
									<td><input type="text" class="form-control" name="total_pajak" value="<?php echo $total_bayar*0.1;?>" readonly></td>
									<td>Total Semua</td>
									<td><input type="text" class="form-control" name="total_bayar" value="<?php echo $total_bayar*1.1;?>" readonly></td>
								</tr>
								<tr>
									<td>Bayar</td>
									<td><input type="text" class="form-control" name="bayar" value="<?php echo $bayar;?>" <?php echo $hasil_penjualan == null ? "disabled" : "";?> required></td>
									<td>
										<button class="btn btn-success" <?php echo $hasil_penjualan == null ? "disabled" : ""; ?>><i class="fa fa-shopping-cart"></i> Bayar</button>
									</td>
								</tr>
							</form>
							<!-- aksi ke table nota -->
							<tr>
								<td>Kembali</td>
								<td><input type="text" class="form-control" value="<?php echo $hitung;?>" readonly></td>
								<td></td>
								<td>
									<a href="print.php?nm_member=<?php echo $_SESSION['admin']['nm_member'];?>
									&bayar=<?php echo $bayar;?>&kembali=<?php echo $hitung;?>" target="_blank">
									<button <?php echo empty($_GET['nota'] == 'yes') ? "disabled" : ""; ?> class="btn btn-primary">
										<i class="fa fa-print"></i> Print Bukti Pembayaran
									</button></a>
								</td>
							</tr>
						</table>
						<br/>
						<br/>
					</div>
				</div>
			</div>
		</div>
	</div>
	

<script>
// AJAX call for autocomplete 
$(document).ready(function(){
	$("#cari").change(function(){
		$.ajax({
			type: "POST",
			url: "fungsi/edit/edit.php?cari_barang=yes",
			data:'keyword='+$(this).val(),
			beforeSend: function(){
				$("#hasil_cari").hide();
				$("#tunggu").html('<p style="color:green"><blink>tunggu sebentar</blink></p>');
			},
			success: function(html){
				$("#tunggu").html('');
				$("#hasil_cari").show();
				$("#hasil_cari").html(html);
			}
		});
	});
});
//To select country name
</script>