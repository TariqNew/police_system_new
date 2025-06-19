<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="../css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <title>Scanner</title>
  <style>
    .page-wrapper {
      min-height: 100vh;
      background-color: #f8f9fa;
      display: flex;
      flex-direction: column;
    }

    .header {
      text-align: center;
      padding: 20px 0;
    }

    .header img {
      height: 60px;
      width: 60px;
      object-fit: contain;
      margin-bottom: 10px;
    }

    .scan-wrapper {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .scan-box {
      background-color: #000;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
    }
  </style>
</head>

<body>
  <div class="page-wrapper">
    <div class="header">
      <img src="../img/police.png" alt="Police Logo">
      <h4 class="mb-0">CRMS | Scan for the fingerprint</h4>
    </div>

    <div class="scan-wrapper">
      <div class="scan-box">
        <div class="scan">
          <div class="fingerprint"></div>
          <h3>Scanning...</h3>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
