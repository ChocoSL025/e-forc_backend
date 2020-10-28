<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
$conn = new mysqli("localhost", "root", "", "e-forc");
$tipe = $_GET['tipe'];
// echo json_encode($tipe);
if ($tipe == 'index') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM nota_pembelian ORDER BY idNota_pembelian DESC";
    $result = $conn->query($sql);
    $notas = [];
    $i = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notas[$i]['id'] = $row["idNota_pembelian"];
            $notas[$i]['nonota'] = $row["no_nota"];
            $tgl = strtotime($row["tanggal"]);
            $tanggal = date('Y-m-d', $tgl);
            $notas[$i]['tgl'] = $tanggal;
            $notas[$i]['total'] = $row["total_nilai_pembelian"];
            $sql = "SELECT * FROM distributor WHERE iddistributor = " . $row['distributor_iddistributor'];
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $res = $stmt->get_result();
            $rw = $res->fetch_assoc();
            $notas[$i]['dist'] = $rw['perusahaan'];
            $i++;
        }
        // dd($gudangs);
        echo json_encode($notas);
    } else {
        echo "0 results";
    }
} else if ($tipe == 'createShow') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM distributor";
    $resultA = $conn->query($sql);
    $trans = [];
    $x = 0;
    while ($rowA = $resultA->fetch_assoc()) {
        $trans[$x]['id'] = $rowA['iddistributor'];
        $trans[$x]['nama'] = $rowA['perusahaan'];
        $x++;
    }
    $sql = "SELECT * FROM barang";
    $result = $conn->query($sql);
    $barangs = [];
    $x = 0;
    if ($result->num_rows > 0) {
        while ($rw = $result->fetch_assoc()) {
            $barangs[$x]['nama'] = $rw['nama'];
            $barangs[$x]['id'] = $rw['idbarang'];
            $barangs[$x]['jumlah'] = $rw['jumlah_barang'];
            $barangs[$x]['harga'] = $rw['harga_jual'];
            $x++;
        }
        $kirim['distris'] = $trans;
        $kirim['barangs'] = $barangs;
        echo json_encode($kirim);
    } else {
        echo "0 results";
    }
} else if ($tipe == 'create') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $knota = $_POST['nota'];
    $username = $_POST['username'];
    $nota = json_decode($knota);

    $tgl = strtotime($nota->tgl);
    $tanggal = date('Y-m-d', $tgl);
    $estimasi = strtotime($nota->estimasi);
    $tgglE = date('Y-m-d', $estimasi);
    $last_id = "a";
    $totalpembelian = 0;
    foreach ($nota->barangs as $brg) {
        $totalpembelian += (int)$brg->jBN*(int)$brg->hS;
    }
    $stmt = $conn->prepare("INSERT INTO nota_pembelian (no_nota,tanggal,distributor_iddistributor,total_nilai_pembelian,estimasi) VALUES (?,?,?,?,?)");
    $stmt->bind_param('sssss', $nota->nonota, $tanggal, $nota->distri->id, $totalpembelian,$tgglE);
    $stmt->execute();
    $last_id = $stmt->insert_id;
    $barangs = $nota->barangs;

    $now = strtotime('now');
    $days = ($estimasi - $now) / 60 / 60 / 24;
    $speriod = date('Y-m-d', strtotime('-' . $days . ' days'));
    $eperiod = date('Y-m-d');
    // $barangKeluar = DB::table('histori_barang')->whereBetween("tanggal", [$speriod, $eperiod])->orderBy('tanggal', 'desc')->get();
    $sql = "SELECT * FROM histori_barang WHERE tanggal BETWEEN ".$speriod." AND ".$eperiod." ORDER BY tanggal DESC";
    $result = $conn->query($sql);
    foreach ($barangs as $barang) {
        $stmt = $conn->prepare("INSERT INTO detail_nota_pembelian (barang_idbarang,Nota_pembelian_idNota_pembelian,harga_beli,jumlah) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $barang->databarang->id,$last_id, $barang->hS, $barang->jBN);
        $stmt->execute();
        $jumlahBarang = 0;
        $jumlahNota = 0;
        if ($result->num_rows > 0){
            while ($row = $result->fetch_assoc()){
                $stmt = $conn->prepare("SELECT jumlah FROM histori_barang_keluar WHERE barang_idbarang = ? AND histori_barang_idhistori_barang = ? LIMIT 1");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $res = $stmt->get_result();
                $rw = $res->fetch_assoc();
                if ($rw !== false) {
                    $jumlahBarang += (int)$rw['jumlah'];
                    $jumlahNota++;
                }
            }
        }
        // foreach ($barangKeluar as $val) {
        //     $jumlah = DB::table('histori_barang_keluar')->select('jumlah')->where("barang_idbarang", $value->idbarang)->where("histori_barang_idhistori_barang", $val->idhistori_barang)->first();
        //     if ($jumlah != null) {
        //         $jumlahBarang += (int)$jumlah->jumlah;
        //         $jumlahNota++;
        //     }
        // }
        $R = 0;
        if ($jumlahBarang > 0) {
            $R = $jumlahBarang / $jumlahNota;
        }
        $L = (int)$days;
        $ss = (int)$barang->databarang->jumlah - ($R * $L);
        $barang->safety_inventory = $ss;
        $stmt = $conn->prepare("UPDATE barang SET harga_jual=?,reorder_point=?,safety_inventory=? WHERE idbarang = ?");
        $stmt->bind_param('ssss', $barang->hJ,$barang->databarang->jumlah,$ss,$barang->databarang->id);
        $stmt->execute();
    }
    $stmt = $conn->prepare("SELECT id FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $rw = $res->fetch_assoc();
    $user_id = $rw['id'];
    $tanggal = date('Y-m-d');
    $act = "create";
    $stmt = $conn->prepare("INSERT INTO jejak_notapembelian (Nota_pembelian_idNota_pembelian,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $last_id, $user_id, $act, $tanggal);
    $stmt->execute();
    echo json_encode("OKEY?");
} else if ($tipe == 'editShow') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $id = $_GET['id'];
    $sql = "SELECT * FROM distributor";
    $resultA = $conn->query($sql);
    $trans = [];
    $x = 0;
    while ($rowA = $resultA->fetch_assoc()) {
        $trans[$x]['id'] = $rowA['iddistributor'];
        $trans[$x]['nama'] = $rowA['perusahaan'];
        $x++;
    }
    $sql = "SELECT * FROM barang";
    $result = $conn->query($sql);
    $barangs = [];
    $x = 0;
    if ($result->num_rows > 0) {
        $sql = "SELECT * FROM nota_pembelian WHERE idNota_pembelian = " . $id;
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->get_result();
        $rw1 = $res->fetch_assoc();
        $nota['nonota']=$rw1['no_nota'];
        $key = array_search($rw1['distributor_iddistributor'],array_column($trans,'id'));
        $nota['distri']=$trans[$key];
        $tgl = strtotime($rw1['tanggal']);
        $tanggal = date('Y-m-d', $tgl);
        $nota['tgl']=$tanggal;
        $nota['estimasi']=date('Y-m-d', strtotime($rw1['estimasi']));
        while ($rw = $result->fetch_assoc()) {
            $barangs[$x]['nama'] = $rw['nama'];
            $barangs[$x]['id'] = $rw['idbarang'];
            $barangs[$x]['jumlah'] = $rw['jumlah_barang'];
            $barangs[$x]['harga'] = $rw['harga_jual'];
            $x++;
        }
        $sql = "SELECT * FROM detail_nota_pembelian WHERE Nota_pembelian_idNota_pembelian = ".$id;
        $result = $conn->query($sql);
        $brgs =[];
        $x =0;
        $key =0;
        while ($rw = $result->fetch_assoc()) {
            $brgs[$x]['ada'] ='yes';
            $brgs[$x]['jBN'] = $rw['jumlah'];
            $brgs[$x]['hS'] = $rw['harga_beli'];
            $key = array_search($rw['barang_idbarang'],array_column($barangs,'id'));
            $brgs[$x]['databarang'] = $barangs[$key];
            array_splice($barangs,$key,1);
            $brgs[$x]['hJ'] = $barangs[$key]['jumlah'];
            $x++;
        }
        $nota['barangs']= $brgs;
        $kirim['distris'] = $trans;
        $kirim['barangs'] = $barangs;
        $kirim['notapem'] = $nota;
        echo json_encode($kirim);
    } else {
        echo "0 results";
    }
} else if ($tipe == 'edit') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $id = $_POST['id'];
    $knota = $_POST['nota'];
    $username = $_POST['username'];
    $nota = json_decode($knota);
    $tgl = strtotime($nota->tgl);
    $estimasi = strtotime($nota->estimasi);
    $tanggal = date('Y-m-d', $tgl);
    $tgglE = date('Y-m-d', $estimasi);
    $last_id = "a";
    $totalpembelian = 0;
    foreach ($nota->barangs as $brg) {
        $totalpembelian += (int)$brg->jBN*(int)$brg->hS;
    }
    $stmt = $conn->prepare("UPDATE nota_pembelian SET tanggal=?,no_nota=?,total_nilai_pembelian=?,distributor_iddistributor=?,estimasi=? WHERE idNota_pembelian =?");
    $stmt->bind_param('ssisss', $tanggal, $nota->nonota, $totalpembelian, $nota->distri->id,$tgglE,$id);
    $stmt->execute();
    $last_id = $id;
    $barangs = $nota->barangs;
    $stmt = $conn->prepare("DELETE FROM detail_nota_pembelian WHERE Nota_pembelian_idNota_pembelian=?");
    $stmt->bind_param('s',  $id);
    $stmt->execute();

    $now = strtotime('now');
    $days = ($estimasi - $now) / 60 / 60 / 24;
    $speriod = date('Y-m-d', strtotime('-' . $days . ' days'));
    $eperiod = date('Y-m-d');
    $sql = "SELECT * FROM histori_barang WHERE tanggal BETWEEN ".$speriod." AND ".$eperiod." ORDER BY tanggal DESC";
    $result = $conn->query($sql);
    foreach ($barangs as $barang) {
        // echo json_encode($area->nama);
        if($barang->ada=='yes'){
            $stmt = $conn->prepare("UPDATE detail_nota_pembelian SET harga_beli=?,jumlah=? WHERE Nota_pembelian_idNota_pembelian=? AND barang_idbarang=?");
            $stmt->bind_param('ssss',  $barang->hS, $barang->jBN, $id, $barang->databarang->id);
            $stmt->execute();
            
        }
        else{
            $stmt = $conn->prepare("INSERT INTO detail_nota_pembelian (barang_idbarang,Nota_pembelian_idNota_pembelian,harga_beli,jumlah) VALUES (?,?,?,?)");
            $stmt->bind_param('ssss', $barang->databarang->id,$id, $barang->hS, $barang->jBN);
            $stmt->execute();
        }
        $stmt = $conn->prepare("INSERT INTO detail_nota_pembelian (barang_idbarang,Nota_pembelian_idNota_pembelian,harga_beli,jumlah) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $barang->databarang->id,$last_id, $barang->hS, $barang->jBN);
        $stmt->execute();
        $jumlahBarang = 0;
        $jumlahNota = 0;
        if ($result->num_rows > 0){
            while ($row = $result->fetch_assoc()){
                $stmt = $conn->prepare("SELECT jumlah FROM histori_barang_keluar WHERE barang_idbarang = ? AND histori_barang_idhistori_barang = ? LIMIT 1");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $res = $stmt->get_result();
                $rw = $res->fetch_assoc();
                if ($rw !== false) {
                    $jumlahBarang += (int)$rw['jumlah'];
                    $jumlahNota++;
                }
            }
        }
        $R = 0;
        if ($jumlahBarang > 0) {
            $R = $jumlahBarang / $jumlahNota;
        }
        $L = (int)$days;
        $ss = (int)$barang->databarang->jumlah - ($R * $L);
        $stmt = $conn->prepare("UPDATE barang SET harga_jual=?,safety_inventory=?,reorder_point=? WHERE idbarang = ?");
        $stmt->bind_param('ssss', $barang->hJ,$ss, $barang->databarang->jumlah, $barang->databarang->id);
        $stmt->execute();
        
    }
    $stmt = $conn->prepare("SELECT id FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $rw = $res->fetch_assoc();
    $user_id = $rw['id'];
    $tanggal = date('Y-m-d');
    $act = "edit";
    $stmt = $conn->prepare("INSERT INTO jejak_notapembelian (Nota_pembelian_idNota_pembelian,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $id, $user_id, $act, $tanggal);
    $stmt->execute();
    echo json_encode("200");
}



$conn->close();
