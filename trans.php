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
          
        $sql = "SELECT * FROM transport";
        $result = $conn->query($sql);
        $distris = [];
        $i = 0;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $distris[$i]['id']= $row["idtransport"];
                $distris[$i]['namaPerusahaan']= $row["perusahaan"];
                $distris[$i]['alamat']= $row["alamat"];
                $distris[$i]['nama']= $row["kontak"];
                $distris[$i]['notlp']= $row["notlfn"];
                $i++;
            }
            // dd($gudangs);
            echo json_encode($distris);
        } else {
        echo "0 results";
        }
    }
    else if($tipe == 'create'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $username = $_POST['username'];
        $kdistri = $_POST['distri'];
        $distri = json_decode($kdistri);
        $stmt = $conn->prepare("INSERT INTO transport (perusahaan,alamat,kontak,notlfn) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $distri->namaPerusahaan,$distri->alamat,$distri->nama,$distri->notlp);
        $stmt->execute();
        $last_id = $conn->insert_id;
        $stmt = $conn->prepare("SELECT id FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $rw = $res->fetch_assoc();
        $user_id = $rw['id'];
        $tanggal = date('Y-m-d');
        $act = "create";
        $stmt = $conn->prepare("INSERT INTO jejak_transport (transport_idtransport,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $last_id,$user_id,$act,$tanggal);
        $stmt->execute();
        echo json_encode("OKE BOSs");
        
    }
    else if($tipe == 'editShow'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $iddistri = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM transport WHERE idtransport = ?");
        $stmt->bind_param("s", $iddistri);
        $stmt->execute();
        $res = $stmt->get_result();
        $rw = $res->fetch_assoc();
        $barang['namaP'] = $rw['perusahaan'];
        $barang['alamat'] = $rw['alamat'];
        $barang['nama'] = $rw['kontak'];
        $barang['notlp'] = $rw['notlfn'];
        echo json_encode($barang);
    }
    else if($tipe == 'edit'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $username = $_POST['username'];
        $kdistri = $_POST['distri'];
        $id = $_POST['id'];
        $distri = json_decode($kdistri);
        $stmt = $conn->prepare("UPDATE transport SET perusahaan =?,alamat =?,kontak =?,notlfn =? WHERE idtransport = ?");
        $stmt->bind_param('sssss', $distri->namaPerusahaan,$distri->alamat,$distri->nama,$distri->notlp,$id);
        $stmt->execute();
        $last_id = $conn->insert_id;
        $stmt = $conn->prepare("SELECT id FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $rw = $res->fetch_assoc();
        $user_id = $rw['id'];
        $tanggal = date('Y-m-d');
        $act = "edit";
        $stmt = $conn->prepare("INSERT INTO jejak_transport (transport_idtransport,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $last_id,$user_id,$act,$tanggal);
        $stmt->execute();
        echo json_encode("OKE BOS");
    }
    
    
    
    $conn->close();
?>