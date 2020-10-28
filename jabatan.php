<?php
    header('Access-Control-Allow-Origin: *'); 	
    header('Access-Control-Allow-Headers: *'); 
    $conn = new mysqli("localhost", "root", "", "e-forc");
    $tipe = $_GET['tipe'];
    // echo json_encode($tipe);
    if($tipe == 'show'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT * FROM halaman";
        $resultA = $conn->query($sql);
        $halamans = [];
        $x = 0;
        while ($rowA = $resultA->fetch_assoc()) {
            $halamans[$x]['id'] = $rowA['idhalaman'];
            $halamans[$x]['nama'] = $rowA['nama'];
            $halamans[$x]['checked'] = false;
            $x++;
        }
        $sql = "SELECT * FROM jabatan";
        $result = $conn->query($sql);
        $jabatans = [];
        $i = 0;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $jabatans[$i]['id']= $row["idjabatan"];
                $jabatans[$i]['nama']= $row["nama"];
                $sql = "SELECT * FROM jabatan_has_halaman WHERE jabatan_idjabatan=".$row["idjabatan"];
                $resultA = $conn->query($sql);
                $halaman = [];
                $x =0;
                $halaman = $halamans;
                if($resultA->num_rows>0){
                    while($rowA = $resultA->fetch_assoc()){
                        if(in_array($rowA['halaman_idhalaman'],array_column($halaman,'id'))){
                            $key = array_search($rowA['halaman_idhalaman'],array_column($halaman,'id'));
                            $halaman[$key]['checked']=true;
                        }
                    }
                }
                $jabatans[$i]['halamans']= $halaman;
                $i++;
            }
            $kirim['jabatans']= $jabatans;
            $kirim['halamans'] = $halamans;
            echo json_encode($kirim);
        } else {
        echo "0 results";
        }
    }
    else if($tipe == 'create'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $kjabatans = $_POST['jabatans'];
        $jabatans = json_decode($kjabatans);
        
        foreach($jabatans as $j){
            $last_id = "";
            if ($j->id=="") {
                $stmt = $conn->prepare("INSERT INTO jabatan (nama) VALUES (?)");
                $stmt->bind_param('s', $j->nama);
                $stmt->execute();
                $last_id = $conn->insert_id;
                $halamans = $j->halamans;
                foreach($halamans as $h){
                    if($h->checked==true){
                        $stmt = $conn->prepare("INSERT INTO jabatan_has_halaman (jabatan_idjabatan, halaman_idhalaman) VALUES (?,?)");
                        $stmt->bind_param('ss', $last_id, $h->id);
                        $stmt->execute();
                    }
                }
            } 
            else{
                $stmt = $conn->prepare("DELETE FROM jabatan_has_halaman WHERE jabatan_idjabatan = ?");
                $stmt->bind_param('s', $j->id);
                $stmt->execute();
                $halamans = $j->halamans;
                foreach($halamans as $h){
                    if($h->checked==true){
                        $stmt = $conn->prepare("INSERT INTO jabatan_has_halaman (jabatan_idjabatan, halaman_idhalaman) VALUES (?,?)");
                        $stmt->bind_param('ss', $j->id, $h->id);
                        $stmt->execute();
                    }
                }
            }
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
        $kjabatans = $_POST['jabatans'];
        $jabatans = json_decode($kjabatans);
        
        foreach($jabatans as $j){
            $last_id = "";
            if ($j->id=="") {
                $stmt = $conn->prepare("INSERT INTO jabatan (nama) VALUES (?)");
                $stmt->bind_param('s', $j->nama);
                $stmt->execute();
                $last_id = $conn->insert_id;
                $halamans = $j->halamans;
                foreach($halamans as $h){
                    if($h->checked==true){
                        $stmt = $conn->prepare("INSERT INTO jabatan_has_halaman (jabatan_idjabatan, halaman_idhalaman) VALUES (?,?)");
                        $stmt->bind_param('ss', $last_id, $h->id);
                        $stmt->execute();
                    }
                }
            } 
            else{
                $halamans = $j->halamans;
                foreach($halamans as $h){
                    if($h->checked==true){
                        $stmt = $conn->prepare("INSERT INTO jabatan_has_halaman (jabatan_idjabatan, halaman_idhalaman) VALUES (?,?)");
                        $stmt->bind_param('ss', $j->id, $h->id);
                        $stmt->execute();
                    }
                }
            }
        }
        echo json_encode("200 OKE BOS");
    }
    
    
    
    $conn->close();
?>