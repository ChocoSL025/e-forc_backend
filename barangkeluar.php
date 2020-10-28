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
    $sql = "SELECT * FROM histori_barang ORDER BY idhistori_barang DESC";
    $result = $conn->query($sql);
    $notas = [];
    $i = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notas[$i]['id'] = $row["idhistori_barang"];
            $notas[$i]['nonota'] = $row["no_nota"];
            $tgl = strtotime($row["tanggal"]);
            $tanggal = date('Y-m-d', $tgl);
            $notas[$i]['tgl'] = $tanggal;
            $sql = "SELECT gudang.nama FROM gudang INNER JOIN gudang_has_histori_barang ON gudang.idgudang = gudang_has_histori_barang.gudang_idgudang WHERE gudang_has_histori_barang.histori_barang_idhistori_barang = " . $row['idhistori_barang'];
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $res = $stmt->get_result();
            $rw = $res->fetch_assoc();
            $notas[$i]['gdg'] = $rw['nama'];
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
    $sql = "SELECT * FROM gudang";
    $result = $conn->query($sql);
    $gudangs = [];
    $x = 0;
    if ($result->num_rows > 0) {
        while ($rw = $result->fetch_assoc()) {
            $gudangs[$x]['nama'] = $rw['nama'];
            $gudangs[$x]['id'] = $rw['idgudang'];
            $sql = "SELECT DISTINCT barang.* FROM area INNER JOIN area_has_barang ON area.idarea=area_has_barang.area_idarea INNER JOIN barang ON barang.idbarang = area_has_barang.barang_idbarang WHERE area.gudang_idgudang =".$rw['idgudang'];
            $resultA = $conn->query($sql);
            $barangs = [];
            $i = 0;
            while ($rowA = $resultA->fetch_assoc()) {
                $barangs[$i]['id'] = $rowA['idbarang'];
                $barangs[$i]['nama'] = $rowA['nama'];
                $barangs[$i]['jumlah'] = $rowA['jumlah_barang'];
                $i++;
            }
            $gudangs[$x]['barangs']=$barangs;
            $x++;
        }
        $stm = $conn->prepare("SELECT idhistori_barang FROM histori_barang ORDER BY idhistori_barang DESC LIMIT 1");
        $stm->execute();
        $res = $stm->get_result();
        $rw = $res->fetch_assoc();
        $tanggal = date("y/m/d");
        $exp = $rw['idhistori_barang']+1;
        $newID = $tanggal."/".$exp;
        $kirim['newId'] = $newID;
        $kirim['gudang'] = $gudangs;
        
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
    $last_id = "a";
    $stmt = $conn->prepare("INSERT INTO histori_barang (no_nota,tanggal) VALUES (?,?)");
    $stmt->bind_param('ss', $nota->nonota, $tanggal);
    $stmt->execute();
    $last_id = $conn->insert_id;
    $stmt = $conn->prepare("INSERT INTO gudang_has_histori_barang (gudang_idgudang,histori_barang_idhistori_barang) VALUES (?,?)");
    $stmt->bind_param('ss', $nota->gudang->id, $last_id);
    $stmt->execute();
    $barangs = $nota->barangs;
    foreach ($barangs as $barang) {
        $stmt = $conn->prepare("INSERT INTO histori_barang_keluar (barang_idbarang,histori_barang_idhistori_barang,jumlah) VALUES (?,?,?)");
        $stmt->bind_param('sss', $barang->databarang->id,$last_id, $barang->jK);
        $stmt->execute();
        $stmt = $conn->prepare("UPDATE barang SET jumlah_barang =? WHERE idbarang = ?");
        $stmt->bind_param('ss', $barang->databarang->jumlah,$barang->databarang->id);
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
    $stmt = $conn->prepare("INSERT INTO jejak_histori_barang (histori_barang_idhistori_barang,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $last_id, $user_id, $act, $tanggal);
    $stmt->execute();
    echo json_encode("200 OKE BOS");
} else if ($tipe == 'editShow') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $id = $_GET['id'];
    $sql = "SELECT * FROM gudang";
    $result = $conn->query($sql);
    $gudangs = [];
    $x = 0;
    if ($result->num_rows > 0) {
        while ($rw = $result->fetch_assoc()) {
            $gudangs[$x]['nama'] = $rw['nama'];
            $gudangs[$x]['id'] = $rw['idgudang'];
            $gudangs[$x]['selected'] = true;
            
            $sql = "SELECT DISTINCT barang.* FROM area INNER JOIN area_has_barang ON area.idarea=area_has_barang.area_idarea INNER JOIN barang ON barang.idbarang = area_has_barang.barang_idbarang WHERE area.gudang_idgudang =".$rw['idgudang'];
            $resultA = $conn->query($sql);
            $barangs = [];
            $i = 0;
            while ($rowA = $resultA->fetch_assoc()) {
                $barangs[$i]['id'] = $rowA['idbarang'];
                $barangs[$i]['nama'] = $rowA['nama'];
                $barangs[$i]['jumlah'] = $rowA['jumlah_barang'];
                $i++;
            }
            $gudangs[$x]['barangs']=$barangs;
            $x++;
        }
        $sql = "SELECT * FROM histori_barang INNER JOIN gudang_has_histori_barang ON histori_barang.idhistori_barang=gudang_has_histori_barang.histori_barang_idhistori_barang  WHERE histori_barang.idhistori_barang =".$id;
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->get_result();
        $rw = $res->fetch_assoc();
        $barangK['nonota'] = $rw['no_nota'];
        $key = array_search($rw['gudang_idgudang'],array_column($gudangs,'id'));
        $gdg = $gudangs[$key];
        // array_splice($barangs, $key);
        $tgl = strtotime($rw['tanggal']);
        $tanggal = date('Y-m-d', $tgl);
        $barangK['tgl'] = $tanggal;
        $sql = "SELECT * FROM barang INNER JOIN histori_barang_keluar ON barang.idbarang=histori_barang_keluar.barang_idbarang WHERE histori_barang_keluar.histori_barang_idhistori_barang =".$id;
        $resultA = $conn->query($sql);
        $barangs = [];
        $i = 0;
        while ($rowA = $resultA->fetch_assoc()) {
            $databarang['id'] = $rowA['idbarang'];
            $databarang['nama'] = $rowA['nama'];
            $databarang['jumlah'] = $rowA['jumlah_barang'];
            $barangs[$i]['jK'] = $rowA['jumlah'];
            $barangs[$i]['databarang'] = $databarang;
            $key = array_search($rowA['idbarang'],$gdg['barangs']);
            array_splice($gdg['barangs'], $key,1);
            $i++;
        }
        // echo json_encode($gdg);
        $barangK['gudang'] = $gdg;
        $barangK['barangs'] = $barangs;
        $kirim['gudang'] = $gudangs;
        $kirim['barangK'] = $barangK;
        echo json_encode($kirim);
    } else {
        echo "0 results";
    }
} else if ($tipe == 'edit') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $knota = $_POST['nota'];
    $username = $_POST['username'];
    $id = $_POST['id'];
    $nota = json_decode($knota);
    $tgl = strtotime($nota->tgl);
    $tanggal = date('Y-m-d', $tgl);
    $last_id = "a";
    $stmt = $conn->prepare("UPDATE histori_barang SET no_nota=?,tanggal=? WHERE idhistori_barang = ?");
    $stmt->bind_param('sss', $nota->nonota, $tanggal,$id);
    $stmt->execute();
    $stmt = $conn->prepare("DELETE FROM histori_barang_keluar WHERE histori_barang_idhistori_barang = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $barangs = $nota->barangs;
    foreach ($barangs as $barang) {
        $stmt = $conn->prepare("INSERT INTO histori_barang_keluar (barang_idbarang,histori_barang_idhistori_barang,jumlah) VALUES (?,?,?)");
        $stmt->bind_param('sss', $barang->databarang->id,$id, $barang->jK);
        $stmt->execute();
        $stmt = $conn->prepare("UPDATE barang SET jumlah_barang =? WHERE idbarang = ?");
        $stmt->bind_param('ss', $barang->databarang->jumlah,$barang->databarang->id);
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
    $stmt = $conn->prepare("INSERT INTO jejak_histori_barang (histori_barang_idhistori_barang,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $id, $user_id, $act, $tanggal);
    $stmt->execute();
    echo json_encode('200 OKE SIP');
}



$conn->close();
