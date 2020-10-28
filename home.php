<?php
    header('Access-Control-Allow-Origin: *'); 	
    header('Access-Control-Allow-Headers: *'); 
    $conn = new mysqli("localhost", "root", "", "e-forc");
    $tipe = $_GET['tipe'];
    // echo json_encode($tipe);
    if($tipe == 'index'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $role = $_GET['role'];
        $userid = $_GET['user_id'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $res = $stmt->get_result();
        $rw = $res->fetch_assoc();
        $iduser = $rw['id'];
        $sql = "SELECT * FROM jabatan WHERE nama = '".$role."'";
        $resultA = $conn->query($sql);
        $halamans = [];
        $x = 0;
        $jabatans = [];
        while ($rowA = $resultA->fetch_assoc()) {
            $sql = "SELECT * FROM halaman INNER JOIN jabatan_has_halaman ON halaman.idhalaman = jabatan_has_halaman.halaman_idhalaman WHERE jabatan_has_halaman.jabatan_idjabatan =". $rowA['idjabatan'];
            $resultB = $conn->query($sql);
            $x = 0;
            while ($rowB = $resultB->fetch_assoc()){
                // array_push($jabatans,$rowB['nama']);
                $jabatans[$x]['tabel'] = $rowB['nama'];
                $jabatans[$x]['halaman'] = $rowB['nama_halaman'];
                $x++;
            }
        }
        // echo json_encode($jabatans);
        $recent = [];
        foreach($jabatans as $j){
            if($j['tabel']!='register'&&$j['tabel']!='jabatan'){
                $sql = "SELECT * FROM jejak_".$j['tabel']." WHERE users_id = ".$iduser." ORDER BY tanggal ASC LIMIT 3";
                $resultB = $conn->query($sql);
                $x = 0;
                while ($rowB = $resultB->fetch_assoc()){
                    $tgl = strtotime($rowB['tanggal']);
                    $tanggal = date('Y-m-d', $tgl);
                    // $recent = $rowB;
                    // $recent[$x]['idgudang'] = $rowB[]
                    array_push($rowB,$j['halaman']);
                    array_push($recent,$rowB);
                }
            }
            
        }
        // echo json_encode($recent);
        $newdate = date("Y-m-d", strtotime("-12 months"));
        $nowDate = date('Y-m-d');
        $sql = "SELECT * FROM histori_barang WHERE tanggal BETWEEN '".$newdate."' AND '".$nowDate."' ORDER BY idhistori_barang DESC";
        $resultA = $conn->query($sql);
        $notas = [];
        $x = 0;
        while ($rowA = $resultA->fetch_assoc()) {
            $notas[$x]['nonota'] = $rowA['no_nota'];
            $notas[$x]['tanggal'] = $rowA['tanggal'];
            $notas[$x]['id'] = $rowA['idhistori_barang'];
            $x++;
        }
        // echo json_encode($notas);
        $sql = "SELECT * FROM barang ORDER BY idbarang DESC";
        $resultB = $conn->query($sql);
        $x = 0;
        $barangs =[];
        while ($rowB = $resultB->fetch_assoc()) {
            $barangs[$x]['nama'] = $rowB['nama'];
            $barangs[$x]['id'] = $rowB['idbarang'];
            $barangs[$x]['jumlah'] = $rowB['jumlah_barang'];
            $barangs[$x]['rop'] = $rowB['reorder_point'];
            $nota = [];
            foreach($notas as $n){
                $sql = "SELECT * FROM histori_barang_keluar WHERE histori_barang_idhistori_barang = ".$n['id']." AND barang_idbarang = ".$rowB['idbarang'];
                $resultA = $conn->query($sql);
                $y = 0;
                while ($rowA = $resultA->fetch_assoc()) {
                    $nota[$y]['jumlah'] = $rowA['jumlah'];
                    $nota[$y]['id'] = $rowA['histori_barang_idhistori_barang'];
                    $y++;
                }
            }
            $stmt = $conn->prepare("SELECT SUM(jumlah_rusak) as rusak FROM detail_nota_pengiriman WHERE barang_idbarang = ".$rowB['idbarang']);
            $stmt->execute();
            $res = $stmt->get_result();
            $rw = $res->fetch_assoc();
            $barangs[$x]['rusak'] = $rw['rusak'];
            $barangs[$x]['notas'] = $nota;
            $x++;
        }
        $kirim['notaK'] = $notas;
        $kirim['barang'] = $barangs;
        $kirim['recent'] = $recent;
        $kirim['1mnth'] = $newdate = date("Y-m-d", strtotime("-1 months"));
        $kirim['6mnth'] = $newdate = date("Y-m-d", strtotime("-6 months"));
        $kirim['12mnth'] = $newdate = date("Y-m-d", strtotime("-12 months"));
        echo json_encode($kirim);
        // echo json_encode($jabatans);
    }
    else if($tipe == 'createShow'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT gudang.nama, area.nama as namaArea,area.idarea FROM gudang INNER JOIN area ON gudang.idgudang = area.gudang_idgudang";
        $resultA = $conn->query($sql);
        $areas = [];
        $x = 0;
        while ($rowA = $resultA->fetch_assoc()) {
            $areas[$x]['show'] = $rowA['nama'] . "-" . $rowA['namaArea'];
            $areas[$x]['nama'] = $rowA['namaArea'];
            $areas[$x]['id'] = $rowA['idarea'];
            $x++;
        }
        $sql = "SELECT * FROM satuan";
        $result = $conn->query($sql);
        $satuans = [];
        $i = 0;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $satuans[$i]['id']= $row["idsatuan"];
                $satuans[$i]['nama']= $row["nama"];
                $i++;
            }
            // dd($gudangs);
            $kirim['area'] = $areas;
            $kirim['satuan'] = $satuans;
            echo json_encode($kirim);
        } else {
        echo "0 results";
        }
        
    }
    else if($tipe == 'create'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $kbarang = $_POST['barang'];
        $barang = json_decode($kbarang);
        $areas = $barang->areaP;
        $sSJ = $barang->sSJ;
        $last_id = "a";
        $stmt = $conn->prepare("INSERT INTO barang (nama,satuan_idsatuan,jumlah_barang,harga_jual) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $barang->nama,$barang->satuanA->id,$barang->jumlah,$barang->harga);
        $stmt->execute();
        $last_id = $conn->insert_id;
        foreach($areas as $area){
            // echo json_encode($area->nama);
            $sql = "INSERT INTO area_has_barang (area_idarea,barang_idbarang) VALUES ('$area->id',$last_id)";
            if ($conn->query($sql) === TRUE) {
            // echo "New record created successfully";
            } else {
            // echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
        foreach($sSJ as $s){
            // echo json_encode($area->nama);
            $sql = "INSERT INTO satuan_simpan (satuan_idsatuan,barang_idbarang,konversi,keterangan,harga_jual) VALUES (?,?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssss', $s->id,$last_id,$s->konversi,$s->tipe,$s->harga);
            $stmt->execute();
        }
        echo json_encode("200 OKE BOS");
        
    }
    else if($tipe == 'editShow'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $idbarang = $_GET['id'];
        $sql = "SELECT gudang.nama, area.nama as namaArea,area.idarea FROM gudang INNER JOIN area ON gudang.idgudang = area.gudang_idgudang";
        $resultA = $conn->query($sql);
        $areas = [];
        $x = 0;
        while ($rowA = $resultA->fetch_assoc()) {
            $areas[$x]['show'] = $rowA['nama'] . "-" . $rowA['namaArea'];
            $areas[$x]['nama'] = $rowA['namaArea'];
            $areas[$x]['id'] = $rowA['idarea'];
            $x++;
        }
        $sql = "SELECT * FROM satuan";
        $result = $conn->query($sql);
        $satuans = [];
        $i = 0;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $satuans[$i]['id']= $row["idsatuan"];
                $satuans[$i]['nama']= $row["nama"];
                $i++;
            }
            // dd($gudangs);
            $sql = "SELECT * FROM barang WHERE idbarang = $idbarang";
            $resultA = $conn->query($sql);
            $barang = [];
            $x = 0;
            while ($rowA = $resultA->fetch_assoc()) {
                $barang[$x]['id'] = $rowA['idbarang'];
                $barang[$x]['nama'] = $rowA['nama'];
                $barang[$x]['jumlah'] = $rowA['jumlah_barang'];
                $barang[$x]['harga'] = $rowA['harga_jual'];
                $stmt = $conn->prepare("SELECT * FROM satuan WHERE idsatuan = ?");
                $stmt->bind_param("s", $rowA['satuan_idsatuan']);
                $stmt->execute();
                $res = $stmt->get_result();
                $rw = $res->fetch_assoc();
                $satuanA['nama']=$rw['nama'];
                $satuanA['id']=$rw['idsatuan'];
                $barang[$x]['satuanA'] = $satuanA;
                $sql = "SELECT gudang.nama, area.nama as namaArea,area.idarea FROM gudang INNER JOIN area ON gudang.idgudang = area.gudang_idgudang INNER JOIN area_has_barang ON area.idarea = area_has_barang.area_idarea WHERE barang_idbarang = $idbarang";
                $resultB = $conn->query($sql);
                $areaP = [];
                $i =0;
                while ($rowB = $resultB->fetch_assoc()){
                    $areaP[$i]['show'] = $rowB['nama'] . "-" . $rowB['namaArea'];
                    $areaP[$i]['nama'] = $rowB['namaArea'];
                    $areaP[$i]['id'] = $rowB['idarea'];
                    $areaP[$i]['idbarang'] = $idbarang;
                    $i++;
                }
                $barang[$x]['areaP'] = $areaP;
                $sql = "SELECT satuan.nama, satuan_simpan.* FROM satuan INNER JOIN satuan_simpan ON satuan.idsatuan = satuan_simpan.satuan_idsatuan WHERE satuan_simpan.barang_idbarang = $idbarang";
                $resultB = $conn->query($sql);
                $satuanSJ = [];
                $i =0;
                while ($rowB = $resultB->fetch_assoc()){
                    $satuanSJ[$i]['tipe'] = $rowB['keterangan'];
                    $satuanSJ[$i]['nama'] = $rowB['nama'];
                    $satuanSJ[$i]['id'] = $rowB['satuan_idsatuan'];
                    $satuanSJ[$i]['idbarang'] = $idbarang;
                    $satuanSJ[$i]['konversi'] = $rowB['konversi'];
                    $satuanSJ[$i]['harga'] = $rowB['harga_jual'];
                    $i++;
                }
                $barang[$x]['sSJ'] = $satuanSJ;
                $x++;
            }
            $kirim['area'] = $areas;
            $kirim['satuan'] = $satuans;
            $kirim['barang'] = $barang;
            echo json_encode($kirim);
        } else {
        echo "0 results";
        }
        
    }
    else if($tipe == 'edit'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $idbarang = $_POST['id'];
        $kbarang = $_POST['barang'];
        $barang = json_decode($kbarang);
        
        $stmt = $conn->prepare("UPDATE barang SET nama =?,satuan_idsatuan=?,jumlah_barang=?,harga_jual=? WHERE idbarang = ".$idbarang);
        $stmt->bind_param('ssss', $barang->nama,$barang->satuanA->id,$barang->jumlah,$barang->harga);
        $stmt->execute();
        foreach($barang->areaP as $area){
            if($area->idbarang == '0'){
                $stmt = $conn->prepare("INSERT INTO area_has_barang (area_idarea,barang_idbarang) VALUES (?,?)");
                $stmt->bind_param('ss', $area->id,$idbarang);
                $stmt->execute();
            }
        }
        foreach($barang->sSJ as $sj){
            if($sj->idbarang == '0'){
                $stmt = $conn->prepare("INSERT INTO satuan_simpan (satuan_idsatuan,barang_idbarang,konversi,keterangan,harga_jual) VALUES (?,?,?,?,?)");
                $stmt->bind_param('sssss', $sj->id,$idbarang,$sj->konversi,$sj->tipe,$sj->harga);
                $stmt->execute();
            }
        }
        echo json_encode("200 sukses");
    }
    
    
    
    $conn->close();
?>