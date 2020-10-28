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
          
        $sql = "SELECT * FROM Barang ORDER BY idbarang DESC";
        $result = $conn->query($sql);
        $barangs = [];
        $i = 0;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $barangs[$i]['id']= $row["idbarang"];
                $barangs[$i]['nama']= $row["nama"];
                $barangs[$i]['harga']= $row["harga_jual"];
                $id = (int)$barangs[$i]['id'];
                $sql = "SELECT gudang.nama, area.nama as namaArea FROM gudang INNER JOIN area ON gudang.idgudang = area.gudang_idgudang INNER JOIN area_has_barang ON area.idarea=area_has_barang.area_idarea WHERE area_has_barang.barang_idbarang = $id LIMIT 3";
                $resultA = $conn->query($sql);
                $areas = [];
                $x =0;
                while($rowA = $resultA->fetch_assoc()){
                    $areas[$x] = $rowA['nama']."-".$rowA['namaArea'];
                    $x++;
                }
                $barangs[$i]['area']= $areas;
                $sql = "SELECT * FROM satuan WHERE idsatuan = ".$row['satuan_idsatuan']." LIMIT 3";
                $resultA = $conn->query($sql);
                while($rowB = $resultA->fetch_assoc()){
                    $barangs[$i]['jumlah']= number_format($row["jumlah_barang"])." ".$rowB['nama'];
                    $barangs[$i]['rop']= number_format($row["reorder_point"])." ".$rowB['nama'];
                }
                $i++;
            }
            // dd($gudangs);
            echo json_encode($barangs);
        } else {
        echo "0 results";
        }
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
        $username = $_POST['username'];
        $barang = json_decode($kbarang);
        $areas = $barang->areaP;
        $sSJ = $barang->sSJ;
        $last_id = "a";
        $stmt = $conn->prepare("INSERT INTO barang (nama,satuan_idsatuan,jumlah_barang,harga_jual,reorder_point,safety_inventory) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('ssssss', $barang->nama,$barang->satuanA->id,$barang->jumlah,$barang->harga,$barang->rop,$barang->rop);
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
        $x = "";
        foreach($sSJ as $s){
            // echo json_encode($area->nama);
            $sql = "INSERT INTO satuan_simpan (satuan_idsatuan,barang_idbarang,konversi,keterangan,harga_jual) VALUES (?,?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $tipe= strtolower($s->tipe);
            $stmt->bind_param('sssss', $s->id,$last_id,$s->konversi,$tipe,$s->harga);
            $stmt->execute();
            $x=$x.$tipe;
        }
        $stmt = $conn->prepare("SELECT id FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $rw = $res->fetch_assoc();
        $user_id = $rw['id'];
        $tanggal = date('Y-m-d');
        $act = "create";
        $stmt = $conn->prepare("INSERT INTO jejak_barang (barang_idbarang,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $last_id,$user_id,$act,$tanggal);
        $stmt->execute();
        echo json_encode("200 OKE BOS".$x);
        
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
                $barang[$x]['rop'] = $rowA['reorder_point'];
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
        $username = $_POST['username'];
        $barang = json_decode($kbarang);
        
        $stmt = $conn->prepare("UPDATE barang SET nama =?,satuan_idsatuan=?,jumlah_barang=?,harga_jual=?,reorder_point=? WHERE idbarang = ".$idbarang);
        $stmt->bind_param('sssss', $barang->nama,$barang->satuanA->id,$barang->jumlah,$barang->harga,$barang->rop);
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
        $stmt = $conn->prepare("SELECT id FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $rw = $res->fetch_assoc();
        $user_id = $rw['id'];
        $tanggal = date('Y-m-d');
        $act = "edit";
        $stmt = $conn->prepare("INSERT INTO jejak_barang (barang_idbarang,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $idbarang,$user_id,$act,$tanggal);
        $stmt->execute();
        echo json_encode("200 suksess");
    }
    
    
    
    $conn->close();
?>