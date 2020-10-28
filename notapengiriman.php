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
    $sql = "SELECT * FROM nota_pengiriman ORDER BY idnota_pengiriman DESC";
    $result = $conn->query($sql);
    $notas = [];
    $i = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notas[$i]['id'] = $row["idnota_pengiriman"];
            $notas[$i]['nonota'] = $row["no_nota"];
            $notas[$i]['tglK'] = $row["tanggal_dikirim"];
            $notas[$i]['tglT'] = $row["tanggal_diterima"];
            $notas[$i]['total'] = $row["total_biaya_transport"];
            $sql = "SELECT * FROM transport WHERE idtransport = " . $row['transport_idtransport'];
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $res = $stmt->get_result();
            $rw = $res->fetch_assoc();
            $notas[$i]['tran'] = $rw['perusahaan'];
            $i++;
        }
        // dd($gudangs);
        echo json_encode($notas);
    } else {
        echo"0 results";
    }
} else if ($tipe == 'createShow') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM transport";
    $resultA = $conn->query($sql);
    $trans = [];
    $x = 0;
    while ($rowA = $resultA->fetch_assoc()) {
        $trans[$x]['id'] = $rowA['idtransport'];
        $trans[$x]['nama'] = $rowA['perusahaan'];
        $x++;
    }
    $sql = "SELECT * FROM nota_pembelian";
    $result = $conn->query($sql);
    $notpems = [];
    $i = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $barangs = [];
            $sql = "SELECT detail_nota_pembelian.*,barang.* FROM detail_nota_pembelian INNER JOIN barang ON detail_nota_pembelian.barang_idbarang = barang.idbarang WHERE detail_nota_pembelian.nota_pembelian_idnota_pembelian = " . $row["idNota_pembelian"];
            $res = $conn->query($sql);
            $x = 0;
            while ($rw = $res->fetch_assoc()) {
                $sql = "SELECT SUM(jumlah_barang) as ttl FROM detail_nota_pengiriman WHERE barang_idbarang = " . $rw['idbarang'] . " AND Nota_pembelian_id = " . $row["idNota_pembelian"];
                $rs = $conn->query($sql);
                $jmlah = 0;
                if ($rs->num_rows > 0) {
                    while ($rwc = $rs->fetch_assoc()) {
                        // echo (int)$rwc['ttl'].'-'.$rw['idbarang'].')';
                        $jmlah = (int)$rw['jumlah'] - (int)$rwc['ttl'];
                        if ($jmlah > 0) {
                            $barangs[$x]['nama'] = $rw['nama'];
                            $barangs[$x]['id'] = $rw['idbarang'];
                            $barangs[$x]['jumlah'] = $jmlah;
                            $barangs[$x]['harga'] = $rw['harga_beli'];
                            $barangs[$x]['nonota'] = $row["no_nota"];
                            $barangs[$x]['idnota'] = $row["idNota_pembelian"];
                            $x++;
                        }
                    }
                }
                else{
                    $barangs[$x]['nama'] = $rw['nama'];
                    $barangs[$x]['id'] = $rw['idbarang'];
                    $barangs[$x]['jumlah'] = $rw['jumlah'];
                    $barangs[$x]['harga'] = $rw['harga_beli'];
                    $barangs[$x]['nonota'] = $row["no_nota"];
                    $barangs[$x]['idnota'] = $row["idNota_pembelian"];
                    $x++;
                }
            }
            if(count($barangs)>0){
                $notpems[$i]['id'] = $row["idNota_pembelian"];
                $notpems[$i]['nonota'] = $row["no_nota"];
                $notpems[$i]['barangs'] = $barangs;
                $i++;
            }
            
        }
        // dd($gudangs);
        $kirim['trans'] = $trans;
        $kirim['notpems'] = $notpems;
        echo json_encode($kirim);
    } else {
        echo "0 results";
    }
} else if ($tipe == 'create') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $knota = $_POST['nota'];
    $nota = json_decode($knota);
    $username = $_POST['username'];
    $tgl = strtotime($nota->tglK);
    $tanggalK = date('Y-m-d', $tgl);
    $tgl = strtotime($nota->tglT);
    $tanggalT = date('Y-m-d', $tgl);
    $last_id = "a";
    $totalBiayaKirim = 0;
    foreach ($nota->brgM as $brgM) {
        $totalBiayaKirim += (int)$brgM->bP;
    }
    $stmt = $conn->prepare("INSERT INTO nota_pengiriman (tanggal_dikirim,tanggal_diterima,no_nota,total_biaya_transport,transport_idtransport) VALUES (?,?,?,?,?)");
    $stmt->bind_param('sssis', $tanggalK, $tanggalT, $nota->nonota, $totalBiayaKirim, $nota->trans->id);
    $stmt->execute();
    $last_id = $conn->insert_id;
    $barangs = $nota->brgM;
    $notapembelian = [];
    foreach ($barangs as $barang) {
        // echo json_encode($area->nama);
        //tambah update barang!!!!!
        $bagus =  (int)$barang->jT - (int)$barang->jR;
        $stmt = $conn->prepare("INSERT INTO detail_nota_pengiriman (pengiriman_idnota_pengiriman,barang_idbarang,biaya_transport,jumlah_barang,Nota_pembelian_id,jumlah_bagus,jumlah_rusak) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param('sssssss', $last_id, $barang->databarang->id, $barang->bP, $barang->jT, $barang->databarang->idnota,$bagus,$barang->jR);
        $stmt->execute();
        $total = (int)$barang->databarang->jumlah+(int)$barang->jT;
        $stmt = $conn->prepare("UPDATE barang SET jumlah_barang=? WHERE idbarang = ?");
        $stmt->bind_param('ss', $total, $barang->databarang->id);
        $stmt->execute();
        if (!in_array($barang->databarang->idnota, $notapembelian)) {
            array_push($notapembelian, $barang->databarang->idnota);
        }
    }
    foreach ($notapembelian as $np) {
        // echo json_encode($area->nama);
        $sql = "INSERT INTO hubungan_nota (nota_pengiriman_idnota_pengiriman,Nota_pembelian_idNota_pembelian) VALUES (?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $last_id, $np);
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
    $stmt = $conn->prepare("INSERT INTO jejak_notapengiriman (nota_pengiriman_idnota_pengiriman,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $last_id,$user_id,$act,$tanggal);
    $stmt->execute();
    echo json_encode("200 OKE BOS");
} else if ($tipe == 'editShow') {
    $id = $_GET['id'];
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM transport";
    $resultA = $conn->query($sql);
    $trans = [];
    $x = 0;
    while ($rowA = $resultA->fetch_assoc()) {
        $trans[$x]['id'] = $rowA['idtransport'];
        $trans[$x]['nama'] = $rowA['perusahaan'];
        $x++;
    }
    $sql = "SELECT * FROM nota_pembelian";
    $result = $conn->query($sql);
    $notpems = [];
    $i = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $barangs = [];
            $sql = "SELECT detail_nota_pembelian.*,barang.* FROM detail_nota_pembelian INNER JOIN barang ON detail_nota_pembelian.barang_idbarang = barang.idbarang WHERE detail_nota_pembelian.nota_pembelian_idnota_pembelian = " . $row["idNota_pembelian"];
            $res = $conn->query($sql);
            $x = 0;
            while ($rw = $res->fetch_assoc()) {
                $sql = "SELECT SUM(jumlah_barang) as ttl FROM detail_nota_pengiriman WHERE barang_idbarang = " . $rw['idbarang'] . " AND Nota_pembelian_id = " . $row["idNota_pembelian"];
                $rs = $conn->query($sql);
                $jmlah = 0;
                if ($rs->num_rows > 0) {
                    while ($rwc = $rs->fetch_assoc()) {
                        $jmlah = (int)$rw['jumlah'] - (int)$rwc['ttl'];
                        // echo $jmlah."-";
                        if ($jmlah > 0) {
                            $barangs[$x]['nama'] = $rw['nama'];
                            $barangs[$x]['id'] = $rw['idbarang'];
                            $barangs[$x]['jumlah'] = $jmlah;
                            $barangs[$x]['jumlah_barang'] = $rw['jumlah_barang'];
                            $barangs[$x]['harga'] = $rw['harga_beli'];
                            $barangs[$x]['nonota'] = $row["no_nota"];
                            $barangs[$x]['idnota'] = $row["idNota_pembelian"];
                            $barangs[$x]['lama'] = false;
                            $x++;
                        }
                    }
                }
            }
            if(count($barangs)>0){
                $notpems[$i]['id'] = $row["idNota_pembelian"];
                $notpems[$i]['nonota'] = $row["no_nota"];
                $notpems[$i]['barangs'] = $barangs;
                $i++;
            }
        }
        // dd($gudangs);
        // echo json_encode($notpems);

    }
    $stmt = $conn->prepare("SELECT * FROM nota_pengiriman WHERE idnota_pengiriman = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $rw = $res->fetch_assoc();
    $nota['nonota'] = $rw['no_nota'];
    $nota['tglT'] = $rw['tanggal_diterima'];
    $nota['tglK'] = $rw['tanggal_dikirim'];
    $stmt = $conn->prepare("SELECT * FROM transport WHERE idtransport = ?");
    $stmt->bind_param("s", $rw['transport_idtransport']);
    $stmt->execute();
    $res = $stmt->get_result();
    $rw1 = $res->fetch_assoc();
    $tran['id'] = $rw1['idtransport'];
    $tran['nama'] = $rw1['perusahaan'];
    $nota['trans'] = $tran;
    $sql = "SELECT * FROM detail_nota_pengiriman WHERE pengiriman_idnota_pengiriman = " . $id;
    $rx = $conn->query($sql);
    $x = 0;
    $barangM =[];
    while ($rw = $rx->fetch_assoc()){
        $barangM[$x]['jT'] = $rw['jumlah_barang'];
        $barangM[$x]['jR'] = $rw['jumlah_rusak'];
        $barangM[$x]['bP'] = $rw['biaya_transport'];
        $stmt = $conn->prepare("SELECT * FROM barang WHERE idbarang = ?");
        $stmt->bind_param("s", $rw['barang_idbarang']);
        $stmt->execute();
        $res = $stmt->get_result();
        $rw1 = $res->fetch_assoc();
        $barangs = [];
        $key = '';
        for ($i=0; $i < count($notpems); $i++) { 
            if($notpems[$i]['id']==$rw['Nota_pembelian_id']){
                $key = $i;
            }
        }
        $k = '';
        if ($key!='') {
            for ($i=0; $i < count($notpems[$key]['barangs']); $i++) { 
                if($notpems[$key]['barangs'][$i]['id']==$rw1['idbarang']){
                    $k = $i;
                }
            }
        }
        $databarang = [];
        // echo $k;
        if($k!=''){
            // echo "la";
            $barangs = $notpems[$key]['barangs'][$k];
            $barangs['lama']= true;
            array_splice($notpems[$key]['barangs'],$k,1);
            if(count($notpems[$key]['barangs'])==0){
                array_splice($notpems,$key,1);
            }
        }
        else{
            $barangs['nama'] = $rw1['nama'];
            $barangs['id'] = $rw1['idbarang'];
            $key = array_search($rw['Nota_pembelian_id'],$notpems);
            $barangs['nonota'] = $notpems[$key]['nonota'];
            $barangs['idnota'] = $notpems[$key]['id'];
            $barangs['jumlah'] = 0;
            $barangs['jumlah_barang'] = $rw1['jumlah_barang'];
            $barangs['lama'] = true;
        }
        $barangM[$x]['databarang'] = $barangs;
        $x++;
    }
    $nota['brgM'] = $barangM;
    $kirim['trans'] = $trans;
    $kirim['notpems'] = $notpems;
    $kirim['notapeng'] = $nota;
    echo json_encode($kirim);
} else if ($tipe == 'edit') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $id = $_POST['id'];
    $knota = $_POST['nota'];
    $username = $_POST['username'];
    $nota = json_decode($knota);
    $tgl = strtotime($nota->tglK);
    $tanggalK = date('Y-m-d', $tgl);
    $tgl = strtotime($nota->tglT);
    $tanggalT = date('Y-m-d', $tgl);
    $last_id = "a";
    $totalBiayaKirim = 0;
    foreach ($nota->brgM as $brgM) {
        $totalBiayaKirim += (int)$brgM->bP;
    }
    $stmt = $conn->prepare("UPDATE nota_pengiriman SET tanggal_dikirim =?,tanggal_diterima=?,no_nota=?,total_biaya_transport=?,transport_idtransport=? WHERE idnota_pengiriman =?");
    $stmt->bind_param('ssssss', $tanggalK, $tanggalT, $nota->nonota, $totalBiayaKirim, $nota->trans->id,$id);
    $stmt->execute();
    
    $last_id = $id;
    $barangs = $nota->brgM;
    $notapembelian = [];
    $ada =[];
    $sql = "SELECT barang.idbarang,barang.jumlah_barang, detail_nota_pengiriman.jumlah_barang as jml FROM barang INNER JOIN detail_nota_pengiriman ON barang.idbarang = detail_nota_pengiriman.barang_idbarang WHERE detail_nota_pengiriman.pengiriman_idnota_pengiriman = " . $last_id;
    $rs = $conn->query($sql);
    $jmlah = 0;
    if ($rs->num_rows > 0) {
        while ($rwc = $rs->fetch_assoc()) {
            $jmlah = (int)$rwc['jumlah_barang'] - (int)$rwc['jml'];
            $stmt = $conn->prepare("UPDATE barang SET jumlah_barang=? WHERE idbarang=?");
            $stmt->bind_param('ss', $jmlah,$rwc['idbarang'] );
            $stmt->execute();
        }
    }
    foreach ($barangs as $barang) {
        // echo json_encode($area->nama);
        $bagus = (int)$barang->jT - (int)$barang->jR;
        if($barang->databarang->lama==true){
            $stmt = $conn->prepare("UPDATE detail_nota_pengiriman SET biaya_transport=?,jumlah_barang=?,Nota_pembelian_id=?,jumlah_bagus=?,jumlah_rusak=? WHERE pengiriman_idnota_pengiriman=? AND barang_idbarang=?");
            $stmt->bind_param('sssssss',  $barang->bP, $barang->jT, $barang->databarang->idnota,$bagus,$barang->jR, $last_id, $barang->databarang->id);
            $stmt->execute();
            if (!in_array($barang->databarang->idnota, $ada)) {
                array_push($ada, $barang->databarang->idnota);
            }
        }
        else{
            $stmt = $conn->prepare("INSERT INTO detail_nota_pengiriman (pengiriman_idnota_pengiriman,barang_idbarang,biaya_transport,jumlah_barang,Nota_pembelian_id,jumlah_bagus,jumlah_rusak) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param('sssssss', $last_id, $barang->databarang->id, $barang->bP,$barang->jT,$barang->databarang->idnota,$bagus,$barang->jR);
            $stmt->execute();
            if (!in_array($barang->databarang->idnota, $notapembelian)) {
                array_push($notapembelian, $barang->databarang->idnota);
            }
        }
        $stmt = $conn->prepare("UPDATE barang SET jumlah_barang=? WHERE idbarang = ?");
        $stmt->bind_param('ss', $barang->total, $barang->databarang->id);
        $stmt->execute();
        
    }
    foreach ($notapembelian as $np) {
        // echo json_encode($area->nama);
        if (!in_array($np, $ada)) {
            $sql = "INSERT INTO hubungan_nota (nota_pengiriman_idnota_pengiriman,Nota_pembelian_idNota_pembelian) VALUES (?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $last_id, $np);
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
    $stmt = $conn->prepare("INSERT INTO jejak_notapengiriman (nota_pengiriman_idnota_pengiriman,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $id,$user_id,$act,$tanggal);
    $stmt->execute();
    echo json_encode("200");
}



$conn->close();
