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
    $sql = "SELECT * FROM pemesanan ORDER BY idpemesanan DESC";
    $result = $conn->query($sql);
    $notas = [];
    $i = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notas[$i]['id'] = $row["idpemesanan"];
            $notas[$i]['nonota'] = $row["no_nota"];
            $tgl = strtotime($row["tanggal_pembuatan"]);
            $tanggal = date('Y-m-d', $tgl);
            $notas[$i]['tglB'] = $tanggal;
            $tgl = strtotime($row["tanggal_dipesan"]);
            $tanggal = date('Y-m-d', $tgl);
            $notas[$i]['tglP'] = $tanggal;
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
    $sql = "SELECT * FROM barang";
    $result = $conn->query($sql);
    $barangs = [];
    $x = 0;
    if ($result->num_rows > 0) {
        while ($rw = $result->fetch_assoc()) {
            $C = 0;
            $R = 0;//permintaan dalam satu periode
            $S = 0;
            $Q = 0;
            // $cari = DB::table('detail_nota_pembelian')->select('*')->where("barang_idbarang", $value->idbarang)->orderBy('Nota_pembelian_idNota_pembelian', 'desc')->first();
            $stmt = $conn->prepare("SELECT * FROM detail_nota_pembelian WHERE barang_idbarang = ? ORDER BY Nota_pembelian_idNota_pembelian DESC LIMIT 1");
            $stmt->bind_param("s", $rw['idbarang']);
            $stmt->execute();
            $res = $stmt->get_result();
            $rw1 = $res->fetch_assoc();
            if($rw1 != null){
                $C = $rw1['harga_beli'];
                // $npem = NotaPembelian::get()->where('idNota_pembelian',$cari->Nota_pembelian_idNota_pembelian)->first();
                $stmt = $conn->prepare("SELECT * FROM nota_pembelian WHERE idNota_pembelian = ? LIMIT 1");
                $stmt->bind_param("s", $rw1['Nota_pembelian_idNota_pembelian']);
                $stmt->execute();
                $res = $stmt->get_result();
                $rw2 = $res->fetch_assoc();
                $tgl = strtotime($rw2['tanggal']);
                $sPeriod = date('Y-m-d',$tgl);
                $ePeriod = date('Y-m-d');
                // $npeng = NotaPengiriman::select('*')->join('detail_nota_pengiriman','nota_pengiriman.idnota_pengiriman','=','detail_nota_pengiriman.pengiriman_idpengiriman')->where('detail_nota_pengiriman.barang_idbarang',$value->idbarang)->whereBetween("tanggal", [$sPeriod, $ePeriod])->orderBy('tanggal', 'desc')->first();
                $stmt = $conn->prepare("SELECT * FROM nota_pengiriman INNER JOIN detail_nota_pengiriman ON nota_pengiriman.idnota_pengiriman = detail_nota_pengiriman.pengiriman_idnota_pengiriman WHERE detail_nota_pengiriman.barang_idbarang = ? AND nota_pengiriman.tanggal_diterima BETWEEN ? AND ? ORDER BY nota_pengiriman.tanggal_diterima DESC LIMIT 1");
                // echo json_encode($stmt);
                $stmt->bind_param("sss", $rw['idbarang'],$sPeriod,$ePeriod);
                $stmt->execute();
                $res = $stmt->get_result();
                $rw3 = $res->fetch_assoc();
                if($rw3!=null){
                    $S = $rw3['biaya_transport'];
                }
                // $bk = HistoriBarang::sum('jumlah')->join('history_barang_keluar','histori_barang_keluar.histori_barang_idhistori_barang','=','histori_barang.idhistori_barang')->where('histori_barang_keluar.barang_idbarang',$value->idbarang)->whereBetween("tanggal", [$sPeriod, $ePeriod])->orderBy('tanggal', 'desc')->first();
                $stmt = $conn->prepare("SELECT SUM(histori_barang_keluar.jumlah) as jumlah FROM histori_barang INNER JOIN histori_barang_keluar ON histori_barang_keluar.histori_barang_idhistori_barang = histori_barang.idhistori_barang WHERE histori_barang_keluar.barang_idbarang = ? AND histori_barang.tanggal BETWEEN ? AND ? ORDER BY histori_barang.tanggal DESC");
                $stmt->bind_param("sss", $rw['idbarang'],$sPeriod,$ePeriod);
                $stmt->execute();
                $res = $stmt->get_result();
                $rw4 = $res->fetch_assoc();
                if($rw4!=null){
                    $R = $rw4['jumlah'];
                }
                if($S == 0||$R == 0){
                    $Q = "Barang belum keluar";
                }
                else{
                    $up = 2*(int)$R*(int)$S;
                    $d = 0.1*(int)$C;
                    $Q = ceil(sqrt($up/$d));
                    $Q = number_format($Q);
                }
                
            }
            else if($C == 0||$S == 0||$R == 0){
                $Q = "Belum 1 periode";
            }
            $barangs[$x]['nama'] = $rw['nama'];
            $barangs[$x]['id'] = $rw['idbarang'];
            $barangs[$x]['jumlah'] = $rw['jumlah_barang'];
            $barangs[$x]['Q'] = $Q;
            $x++;
        }
        $stm = $conn->prepare("SELECT idpemesanan FROM pemesanan ORDER BY idpemesanan DESC LIMIT 1");
        $stm->execute();
        $res = $stm->get_result();
        $rw = $res->fetch_assoc();
        $tanggal = date("y/m/d");
        $exp = $rw['idpemesanan']+1;
        $newID = $tanggal."/".$exp;
        $sql = "SELECT * FROM distributor";
        $resultA = $conn->query($sql);
        $trans = [];
        $x = 0;
        while ($rowA = $resultA->fetch_assoc()) {
            $trans[$x]['id'] = $rowA['iddistributor'];
            $trans[$x]['nama'] = $rowA['perusahaan'];
            $x++;
        }
        $kirim['distris'] = $trans;
        $kirim['newId'] = $newID;
        $kirim['barangs'] = $barangs;
        echo json_encode($kirim);
    } else {
        echo "0 results";
    }
} else if ($tipe == 'create') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $knota = $_POST['pemesanan'];
    $username = $_POST['username'];
    $nota = json_decode($knota);

    $tgl = strtotime($nota->tgl);
    $tanggal = date('Y-m-d', $tgl);
    $tglnow = date('Y-m-d');
    $last_id = "a";
    $stmt = $conn->prepare("INSERT INTO pemesanan (tanggal_pembuatan,tanggal_dipesan,no_nota,distributor_iddistributor) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $tglnow, $tanggal, $nota->nonota,$nota->distri->id);
    $stmt->execute();
    $last_id = $conn->insert_id;
    $barangs = $nota->barangs;
    foreach ($barangs as $barang) {
        $stmt = $conn->prepare("INSERT INTO detail_pemesanan (barang_idbarang,pemesanan_idpemesanan,jumlah_barang) VALUES (?,?,?)");
        $stmt->bind_param('sss', $barang->databarang->id,$last_id, $barang->jP);
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
    $stmt = $conn->prepare("INSERT INTO jejak_pemesanan (pemesanan_idpemesanan,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $last_id, $user_id, $act, $tanggal);
    $stmt->execute();
    echo json_encode("200 OKE BOS");
} else if ($tipe == 'editShow') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $id = $_GET['id'];
    $sql = "SELECT * FROM barang";
    $result = $conn->query($sql);
    $barangs = [];
    $x = 0;
    if ($result->num_rows > 0) {
        $sql = "SELECT * FROM pemesanan INNER JOIN distributor ON pemesanan.distributor_iddistributor = distributor.iddistributor WHERE pemesanan.idpemesanan = " . $id;
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->get_result();
        $rw1 = $res->fetch_assoc();
        $pemesanan['nonota']=$rw1['no_nota'];
        $pemesanan['distri']['id'] = $rw1['iddistributor'];
        $pemesanan['distri']['nama'] = $rw1['perusahaan'];
        $tgl = strtotime($rw1['tanggal_dipesan']);
        $tanggal = date('Y-m-d', $tgl);
        $pemesanan['tgl']=$tanggal;
        while ($rw = $result->fetch_assoc()) {
            $C = 0;
            $R = 0;
            $S = 0;
            $Q = 0;
            // $cari = DB::table('detail_nota_pembelian')->select('*')->where("barang_idbarang", $value->idbarang)->orderBy('Nota_pembelian_idNota_pembelian', 'desc')->first();
            $stmt = $conn->prepare("SELECT * FROM detail_nota_pembelian WHERE barang_idbarang = ? ORDER BY Nota_pembelian_idNota_pembelian DESC LIMIT 1");
            $stmt->bind_param("s", $rw['idbarang']);
            $stmt->execute();
            $res = $stmt->get_result();
            $rw1 = $res->fetch_assoc();
            if($rw1 != null){
                $C = $rw1['harga_beli'];
                // $npem = NotaPembelian::get()->where('idNota_pembelian',$cari->Nota_pembelian_idNota_pembelian)->first();
                $stmt = $conn->prepare("SELECT * FROM nota_pembelian WHERE idNota_pembelian = ? LIMIT 1");
                $stmt->bind_param("s", $rw1['Nota_pembelian_idNota_pembelian']);
                $stmt->execute();
                $res = $stmt->get_result();
                $rw2 = $res->fetch_assoc();
                $tgl = strtotime($rw2['tanggal']);
                $sPeriod = date('Y-m-d',$tgl);
                $ePeriod = date('Y-m-d');
                // $npeng = NotaPengiriman::select('*')->join('detail_nota_pengiriman','nota_pengiriman.idnota_pengiriman','=','detail_nota_pengiriman.pengiriman_idpengiriman')->where('detail_nota_pengiriman.barang_idbarang',$value->idbarang)->whereBetween("tanggal", [$sPeriod, $ePeriod])->orderBy('tanggal', 'desc')->first();
                $stmt = $conn->prepare("SELECT * FROM nota_pengiriman INNER JOIN detail_nota_pengiriman ON nota_pengiriman.idnota_pengiriman = detail_nota_pengiriman.pengiriman_idnota_pengiriman WHERE detail_nota_pengiriman.barang_idbarang = ? AND nota_pengiriman.tanggal_diterima BETWEEN ? AND ? ORDER BY nota_pengiriman.tanggal_diterima DESC LIMIT 1");
                // echo json_encode($stmt);
                $stmt->bind_param("sss", $rw['idbarang'],$sPeriod,$ePeriod);
                $stmt->execute();
                $res = $stmt->get_result();
                $rw3 = $res->fetch_assoc();
                if($rw3!=null){
                    $S = $rw3['biaya_transport'];
                }
                // $bk = HistoriBarang::sum('jumlah')->join('history_barang_keluar','histori_barang_keluar.histori_barang_idhistori_barang','=','histori_barang.idhistori_barang')->where('histori_barang_keluar.barang_idbarang',$value->idbarang)->whereBetween("tanggal", [$sPeriod, $ePeriod])->orderBy('tanggal', 'desc')->first();
                $stmt = $conn->prepare("SELECT SUM(histori_barang_keluar.jumlah) as jumlah FROM histori_barang INNER JOIN histori_barang_keluar ON histori_barang_keluar.histori_barang_idhistori_barang = histori_barang.idhistori_barang WHERE histori_barang_keluar.barang_idbarang = ? AND histori_barang.tanggal BETWEEN ? AND ? ORDER BY histori_barang.tanggal DESC");
                $stmt->bind_param("sss", $rw['idbarang'],$sPeriod,$ePeriod);
                $stmt->execute();
                $res = $stmt->get_result();
                $rw4 = $res->fetch_assoc();
                if($rw4!=null){
                    $R = $rw4['jumlah'];
                }
                if($S == 0||$R == 0){
                    $Q = "Barang belum keluar";
                }
                else{
                    $up = 2*(int)$R*(int)$S;
                    $d = 0.1*(int)$C;
                    $Q = ceil(sqrt($up/$d));
                }
            }
            else if($C == 0||$S == 0||$R == 0){
                $Q = "Belum 1 periode";
            }
            $barangs[$x]['nama'] = $rw['nama'];
            $barangs[$x]['id'] = $rw['idbarang'];
            $barangs[$x]['jumlah'] = $rw['jumlah_barang'];
            $barangs[$x]['Q'] = $Q;
            $x++;
        }
        $sql = "SELECT * FROM detail_pemesanan WHERE pemesanan_idpemesanan = ".$id;
        $result = $conn->query($sql);
        $brgs =[];
        $x =0;
        $key =0;
        while ($rw = $result->fetch_assoc()) {
            $brgs[$x]['ada'] ='yes';
            $brgs[$x]['jP'] = $rw['jumlah_barang'];
            $key = array_search($rw['barang_idbarang'],array_column($barangs,'id'));
            $brgs[$x]['databarang'] = $barangs[$key];
            array_splice($barangs,$key,1);
            $x++;
        }
        $pemesanan['barangs']= $brgs;
        $sql = "SELECT * FROM distributor";
        $resultA = $conn->query($sql);
        $trans = [];
        $x = 0;
        while ($rowA = $resultA->fetch_assoc()) {
            $trans[$x]['id'] = $rowA['iddistributor'];
            $trans[$x]['nama'] = $rowA['perusahaan'];
            $x++;
        }
        $kirim['distris'] = $trans;
        $kirim['barangs'] = $barangs;
        $kirim['pemesanan'] = $pemesanan;
        echo json_encode($kirim);
    } else {
        echo "0 results";
    }
} else if ($tipe == 'edit') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $id = $_POST['id'];
    $knota = $_POST['pemesanan'];
    $username = $_POST['username'];
    $nota = json_decode($knota);
    $tgl = strtotime($nota->tgl);
    $tanggal = date('Y-m-d', $tgl);
    $stmt = $conn->prepare("UPDATE pemesanan SET tanggal_dipesan=?,distributor_iddistributor=? WHERE idpemesanan =?");
    $stmt->bind_param('sss', $tanggal, $nota->distri->id,$id);
    $stmt->execute();
    $last_id = $id;
    $barangs = $nota->barangs;
    foreach ($barangs as $barang) {
        // echo json_encode($area->nama);
        if($barang->ada=='yes'){
            $stmt = $conn->prepare("UPDATE detail_pemesanan SET jumlah_barang=? WHERE pemesanan_idpemesanan=? AND barang_idbarang=?");
            $stmt->bind_param('sss',  $barang->jP, $id, $barang->databarang->id);
            $stmt->execute();
        }
        else{
            $stmt = $conn->prepare("INSERT INTO detail_pemesanan (barang_idbarang,pemesanan_idpemesanan,jumlah_barang) VALUES (?,?,?)");
            $stmt->bind_param('sss', $barang->databarang->id,$id, $barang->jP);
            $stmt->execute();
        }
        
    }
    $stmt = $conn->prepare("SELECT id FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $rw = $res->fetch_assoc();
    $user_id = $rw['id'];
    $tanggal = date('Y-m-d');
    $act = "edit";
    $stmt = $conn->prepare("INSERT INTO jejak_pemesanan (pemesanan_idpemesanan,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $id, $user_id, $act, $tanggal);
    $stmt->execute();
    echo json_encode('200 OKE SIP');
}



$conn->close();
