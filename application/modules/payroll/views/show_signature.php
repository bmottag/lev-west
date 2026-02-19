<?php

// To keep the code as clean as possible, separation of concerns and all that,
//  put the main logic for this application in a file separate from the HTML
//require_once 'lib/save-signature.php';

?><!DOCTYPE html>
<head>
  <meta charset="utf-8">
  <title>Saving a Signature &middot; Signature Pad</title>
  <link rel="stylesheet" href="http://thibot.com/pruebas_firma/signature-pad/build/jquery.signaturepad.css">
  <!--[if lt IE 9]><script src="signature-pad/build/flashcanvas.js"></script><![endif]-->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>


<div class="sigPad signed">
  <div class="sigWrapper">

    <canvas class="pad" width="198" height="55"></canvas>
  </div>
</div>




  <script src="http://thibot.com/pruebas_firma/signature-pad/build/jquery.signaturepad.min.js"></script>

  <script>
    $(document).ready(function () {
	  // Write out the complete signature from the database to Javascript
      var sig = <?php echo $task[0]['signature']; ?>;
      $('.sigPad').signaturePad({displayOnly : true}).regenerate(sig);
    });
  </script>


</body>
