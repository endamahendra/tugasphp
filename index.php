<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
          <?php
          // Memulai atau melanjutkan sesi PHP
          session_start();

          // Fungsi untuk menampilkan daftar buku dengan status "Tersedia" atau "Dipinjam"
          function tampilkanDaftarBuku($daftarBuku, $status) {
              // Periksa apakah $daftarBuku diatur dan merupakan array
              if (isset($daftarBuku) && is_array($daftarBuku)) {
                  $filteredDaftarBuku = array_filter($daftarBuku, function ($buku) use ($status) {
                      return $buku['status'] == $status;
                  });

                  if (count($filteredDaftarBuku) > 0) {
                      echo "<div class='container mt-4 text-center'>";
                      echo "<h3 class='mb-4'>Daftar Buku $status di Perpustakaan:</h3>";
                      echo "<table class='table table-bordered'>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Judul</th>
                                    <th>Pengarang</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>";

                      foreach ($filteredDaftarBuku as $buku) {
                          echo "<tr>";
                          echo "<td>{$buku['id']}</td>";
                          echo "<td>{$buku['judul']}</td>";
                          echo "<td>{$buku['pengarang']}</td>";
                          echo "<td>{$buku['status']}</td>";

                          echo "<td>";

                          if ($buku['status'] == 'Tersedia') {
                              echo "<form method='post'><button class='btn btn-success' type='submit' name='pinjam' value='{$buku['id']}'>Pinjam</button></form>";
                          } else if ($buku['status'] == 'Dipinjam') {
                              echo "<form method='post'><button class='btn btn-warning' type='submit' name='kembalikan' value='{$buku['id']}'>Kembalikan</button></form>";
                          }

                          echo "</td>";
                          echo "</tr>";
                      }

                      echo "</tbody></table>";
                  }
              } else {
                  // Tangani kasus di mana $daftarBuku tidak diatur atau bukan array
                  echo "Data buku tidak valid.";
              }
          }

          // Fungsi untuk meminjam buku
          function pinjamBuku($id) {
              $_SESSION['daftarBuku'][$id - 1]['status'] = 'Dipinjam';
              $_SESSION['notif'] = "Buku '{$_SESSION['daftarBuku'][$id - 1]['judul']}' berhasil dipinjam.";
              header('Location: index.php');
              exit();
          }

          // Fungsi untuk mengembalikan buku
          function kembalikanBuku($id) {
              $_SESSION['daftarBuku'][$id - 1]['status'] = 'Tersedia';
              $_SESSION['notif'] = "Buku '{$_SESSION['daftarBuku'][$id - 1]['judul']}' telah dikembalikan.";
              header('Location: index.php');
              exit();
          }

          // Periksa apakah $_SESSION['daftarBuku'] diatur dan merupakan array
          if (isset($_SESSION['daftarBuku']) && is_array($_SESSION['daftarBuku'])) {
              // Menampilkan daftar buku Tersedia
              tampilkanDaftarBuku($_SESSION['daftarBuku'], 'Tersedia');

              // Menampilkan daftar buku Dipinjam
              tampilkanDaftarBuku($_SESSION['daftarBuku'], 'Dipinjam');

              // Logika untuk memproses peminjaman dan pengembalian buku
              if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                  if (isset($_POST['pinjam'])) {
                      $idPinjam = $_POST['pinjam'];
                      pinjamBuku($idPinjam);
                  } elseif (isset($_POST['kembalikan'])) {
                      $idKembalikan = $_POST['kembalikan'];
                      kembalikanBuku($idKembalikan);
                  }
              }

              // Menampilkan notifikasi jika ada
              if (isset($_SESSION['notif'])) {
                  echo "<div style='display:none;' id='notif'>{$_SESSION['notif']}</div>";
                  unset($_SESSION['notif']);
              }
          } else {
              // Tangani kasus di mana $_SESSION['daftarBuku'] tidak diatur atau bukan array
              echo "Data buku tidak valid.";
          }
          ?>

  <script>
    // Menampilkan notifikasi dari elemen tersembunyi
    var notifElement = document.getElementById('notif');
    if (notifElement) {
      alert(notifElement.innerHTML);
    }
  </script>
</body>
</html>
