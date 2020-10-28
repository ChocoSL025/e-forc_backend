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
          
        $sql = "SELECT * FROM Gudang ORDER BY idgudang DESC";
        $result = $conn->query($sql);
        $gudangs = [];
        $i = 0;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $gudangs[$i]['id']= $row["idgudang"];
                $gudangs[$i]['nama']= $row["nama"];
                $gudangs[$i]['alamat']= $row["alamat"];
                $id = (int)$gudangs[$i]['id'];
                $sql = "SELECT * FROM Area WHERE gudang_idgudang = $id LIMIT 3";
                $resultA = $conn->query($sql);
                $areas = [];
                $x =0;
                while($row = $resultA->fetch_assoc()){
                    $areas[$x] = $row;
                    $x++;
                }
                $gudangs[$i]['area']= $areas;
                // echo $gudangs[$i]['area'];
                $i++;
            }
            // dd($gudangs);
            echo json_encode($gudangs);
        } else {
        echo "0 results";
        }
    }
    else if($tipe == 'create'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $kgudang = $_POST['gudang'];
        $username = $_POST['username'];
        $gudang = json_decode($kgudang);
        $areas = $gudang->areass;
        $last_id = "a";
        // echo json_encode($gudang->namaGudang);
        $sql = "INSERT INTO gudang (nama, alamat) VALUES ('$gudang->namaGudang','$gudang->alamat')";
        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            // echo json_encode($last_id);
        } else {
            echo json_encode($last_id);
        }
        
        foreach($areas as $area){
            // echo json_encode($area->nama);
            $sql = "INSERT INTO area (nama,gudang_idgudang) VALUES ('$area->nama',$last_id)";
            if ($conn->query($sql) === TRUE) {
            // echo "New record created successfully";
            } else {
            // echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
        $stmt = $conn->prepare("SELECT id FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $rw = $res->fetch_assoc();
        $user_id = $rw['id'];
        $tanggal = date('Y-m-d');
        $act = "tambah gudang dan area";
        $stmt = $conn->prepare("INSERT INTO jejak_gudang_area (gudang_idgudang,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $last_id,$user_id,$act,$tanggal);
        $stmt->execute();
        echo json_encode("200 OKE BOS");
        
    }
    else if($tipe == 'show'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $idgudang = $_GET['id'];
        $sql = "SELECT * FROM Gudang Where idgudang = '$idgudang'";
        $result = $conn->query($sql);
        $gudangs = [];
        $i = 0;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $gudangs[$i]['id']= $row["idgudang"];
                $gudangs[$i]['nama']= $row["nama"];
                $gudangs[$i]['alamat']= $row["alamat"];
                $id = (int)$gudangs[$i]['id'];
                $sql = "SELECT * FROM Area WHERE gudang_idgudang = $id";
                $resultA = $conn->query($sql);
                $areas = [];
                $x =0;
                while($row = $resultA->fetch_assoc()){
                    $areas[$x] = $row;
                    $x++;
                }
                $gudangs[$i]['area']= $areas;
                // echo $gudangs[$i]['area'];
                $i++;
            }
            // dd($gudangs);
            echo json_encode($gudangs);
        } else {
        echo "0 results";
        }
    }
    else if($tipe == 'edit'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $idgudang = $_POST['id'];
        $kgudang = $_POST['gudang'];
        $username = $_POST['username'];
        $gudang = json_decode($kgudang);
        $stmt = $conn->prepare("UPDATE gudang SET nama=?,alamat=? WHERE idgudang = ".$idgudang);
		$stmt->bind_param('ss', $gudang->namaGudang, $gudang->alamat);
        $stmt->execute();
        $tambah = "";
        foreach($gudang->areass as $area){
            if($area->idarea == '0'){
                $stmt = $conn->prepare("INSERT INTO area (nama,gudang_idgudang) VALUES (?,?)");
                $stmt->bind_param('ss', $area->nama,$idgudang);
                $stmt->execute();
                $tambah = " dan tambah area";
            }
            else{
                $stmt = $conn->prepare("UPDATE area SET nama=? WHERE idarea = ".$area->idarea);
                $stmt->bind_param('s', $area->nama);
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
        $act = "ubah gudang".$tambah;
        $stmt = $conn->prepare("INSERT INTO jejak_gudang_area (gudang_idgudang,users_id,keterangan,tanggal) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $idgudang,$user_id,$act,$tanggal);
        $stmt->execute();
        echo json_encode("200 sukses");
    }
    
    
    
    $conn->close();
?>