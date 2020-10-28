<?php
    header('Access-Control-Allow-Origin: *'); 	
    header('Access-Control-Allow-Headers: *'); 
    $key ="mantabsekaliandaini";
    $plaintext = "message to be encrypted";
    $cipher = "aes-128-gcm";
    $conn = new mysqli("localhost", "root", "", "e-forc");
    $tipe = $_GET['tipe'];
    // echo json_encode($tipe);
    if($tipe == 'signIn'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $username = $_POST['username'];
        $pwd = $_POST['pwd'];
        $stmt = $conn->prepare("SELECT users.*, jabatan.nama as namaJabatan FROM users INNER JOIN jabatan ON users.jabatan_idjabatan = jabatan.idjabatan WHERE users.user_id = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $rw = $res->fetch_assoc();
        if (in_array($cipher, openssl_get_cipher_methods())&&$res->num_rows > 0)
        {
            $original_pwd = openssl_decrypt($rw['pwd_hp'], $cipher, $key, $options=0, $rw['iv'], $rw['tag']);
            if($original_pwd==$pwd){
                echo json_encode($rw['namaJabatan']);
            }
            else{
                echo json_encode("PWD SALAH");
            }
        }
        else{
            echo json_encode("Username tidak ditemukan");
        }
    }
    else if($tipe == 'registerNew'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $username = $_POST['username'];
        $pwd = $_POST['pwd'];
        $nama = $_POST['nama'];
        $idJ = $_POST['idj'];
        $ecrypted_pwd ="";
        $iv;$tag;
        if (in_array($cipher, openssl_get_cipher_methods()))
        {
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ecrypted_pwd = openssl_encrypt($pwd, $cipher, $key, $options=0, $iv, $tag);
        }
        $stmt = $conn->prepare("INSERT INTO users (nama,pwd_hp,user_id,jabatan_idjabatan,iv,tag) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $nama,$ecrypted_pwd,$username,$idJ,$iv,$tag);
        $stmt->execute();
        
        echo json_encode("sukses");
    }
    else if($tipe == 'registerUpdate'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $username = $_POST['username'];
        $pwd = $_POST['pwd'];
        $ecrypted_pwd ="";
        $iv;$tag;
        if (in_array($cipher, openssl_get_cipher_methods()))
        {
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ecrypted_pwd = openssl_encrypt($pwd, $cipher, $key, $options=0, $iv, $tag);
        }
        $stmt = $conn->prepare("UPDATE users SET pwd_hp=?, iv=?, tag=? WHERE user_id = ?");
        $stmt->bind_param("ssss", $ecrypted_pwd,$iv,$tag,$username);
        $stmt->execute();
        $stmt = $conn->prepare("SELECT jabatan.nama FROM users INNER JOIN jabatan ON users.jabatan_idjabatan = jabatan.idjabatan WHERE users.user_id = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $rw = $res->fetch_assoc();
        echo json_encode($rw['nama']);
        
    }
    else if($tipe == 'registerShow'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT * FROM jabatan";
        $resultA = $conn->query($sql);
        $jabatans = [];
        $x = 0;
        while ($rowA = $resultA->fetch_assoc()) {
            $jabatans[$x]['nama'] = $rowA['nama'];
            $jabatans[$x]['id'] = $rowA['idjabatan'];
            $x++;
        }
        $kirim['jabatan'] = $jabatans;
        echo json_encode($kirim);
    }
    else if($tipe == 'getRole'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT * FROM halaman";
        $resultA = $conn->query($sql);
        $halamans = [];
        $x = 0;
        while ($rowA = $resultA->fetch_assoc()) {
            $sql = "SELECT * FROM jabatan INNER JOIN jabatan_has_halaman ON jabatan.idjabatan = jabatan_has_halaman.jabatan_idjabatan WHERE jabatan_has_halaman.halaman_idhalaman =". $rowA['idhalaman'];
            $resultB = $conn->query($sql);
            $jabatans = [];
            while ($rowB = $resultB->fetch_assoc()){
                array_push($jabatans,$rowB['nama']);
            }
            $halamans[$rowA['nama']] = $jabatans;
        }
        echo json_encode($halamans);
    }
    else if($tipe == 'getPages'){
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $role = $_GET['role'];
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
                $jabatans[$x]['menu'] = $rowB['nama_halaman'];
                $jabatans[$x]['url'] = $rowB['link'];
                $x++;
            }
        }
        echo json_encode($jabatans);
    }
    
    
    
    
    $conn->close();
?>